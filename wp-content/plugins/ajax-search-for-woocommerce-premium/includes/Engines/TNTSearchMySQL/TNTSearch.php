<?php

//https://github.com/trilbymedia/grav-plugin-tntsearch/blob/develop/classes/GravTNTSearch.php

namespace DgoraWcas\Engines\TNTSearchMySQL;

use DgoraWcas\Admin\Troubleshooting;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\AsyncRebuildIndex;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\BackgroundProductUpdater;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Scheduler;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Taxonomies;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Updater;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Builder;

use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Readable\AsyncProcess as AsyncProcessR;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\AsyncProcess as AsyncProcessS;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Taxonomy\AsyncProcess as AsyncProcessT;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Variation\AsyncProcess as AsyncProcessV;
use DgoraWcas\Helpers;
use DgoraWcas\Product;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TNTSearch {

	/**
	 * Background processes for the readable index
	 *
	 * @var \DgoraWcas\Engines\TNTSearchMySQL\Indexer\Readable\AsyncProcess
	 *
	 */
	public $asynchBuildIndexR;

	/**
	 * Background processes for the searchable index
	 *
	 * @var \DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\AsyncProcess
	 *
	 */
	public $asynchBuildIndexS;

	/**
	 * Background processes for the variation index
	 *
	 * @var \DgoraWcas\Engines\TNTSearchMySQL\Indexer\Variation\AsyncProcess
	 *
	 */
	public $asynchBuildIndexV;

	/**
	 * Background processes for the taxonomies index
	 *
	 * @var \DgoraWcas\Engines\TNTSearchMySQL\Indexer\Taxonomy\AsyncProcess
	 *
	 */
	public $asynchBuildIndexT;

	/**
	 * Async rebuild whole search index
	 *
	 * @var \DgoraWcas\Engines\TNTSearchMySQL\Indexer\AsyncRebuildIndex
	 *
	 */
	public $asyncRebuildIndex;

	/**
	 * Taxonomies
	 *
	 * @var \DgoraWcas\Engines\TNTSearchMySQL\Indexer\Taxonomies
	 */
	public $taxonomies;

	/**
	 * TNTSearch constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * TNTSearch init
	 *
	 * @return void
	 */
	private function init() {

		$this->asynchBuildIndexR = new AsyncProcessR();
		$this->asynchBuildIndexS = new AsyncProcessS();
		$this->asynchBuildIndexT = new AsyncProcessT();
		$this->asynchBuildIndexV = new AsyncProcessV();

		$this->asyncRebuildIndex = new AsyncRebuildIndex();

		$this->taxonomies = new Taxonomies();

		add_action( 'init', function () {

			if ( DGWT_WCAS()->engine === 'tntsearchMySql' && apply_filters( 'dgwt/wcas/override_search_results_page', true ) ) {
				$this->overrideSearchPage();
			}

			$this->initScheduler();
			$this->initUpdater();
			$this->initBackgroundProductUpdater();
			$this->taxonomies->init();
		} );

		add_action( 'wp_ajax_dgwt_wcas_build_index', array( $this, 'ajaxBuildIndex' ) );
		add_action( 'wp_ajax_dgwt_wcas_stop_build_index', array( $this, 'ajaxStopBuildIndex' ) );
		add_action( 'wp_ajax_dgwt_wcas_build_index_heartbeat', array( $this, 'ajaxBuildIndexHeartbeat' ) );
		add_action( 'wp_ajax_dgwt_wcas_index_details_toggle', array( $this, 'ajaxBuildIndexDetailsToggle' ) );

		add_action( 'update_option_' . DGWT_WCAS_SETTINGS_KEY, array( $this, 'buildIndexOnchangeSettings' ), 10, 2 );

		add_action( 'admin_init', array( $this, 'maintenanceOnInit' ) );

		$this->wpBgProcessingBasicAuthBypass();

		// Load dynamic prices
		add_action( 'wc_ajax_' . DGWT_WCAS_GET_PRICES_ACTION, array( $this, 'getDynamicPrices' ) );
	}


	/**
	 * Load background product updater
	 *
	 * @return void
	 */
	private function initBackgroundProductUpdater() {
		$productUpdater = new BackgroundProductUpdater();
		$productUpdater->init();
	}

	/**
	 * Load scheduler
	 *
	 * @return void
	 */
	private function initScheduler() {
		$scheduler = new Scheduler();
		$scheduler->init();
	}

	/**
	 * Load updater
	 *
	 * Listens for changes in posts and taxonomies terms and update index
	 *
	 * @return void
	 */
	private function initUpdater() {
		$updater = new Updater;
		$updater->init();
	}

	/**
	 * Load search page logic
	 *
	 * The class interferes with WordPress search resutls page
	 *
	 * @return void
	 */
	private function overrideSearchPage() {
		$sp = new SearchPage();
		$sp->init();
	}

	/**
	 * Build index
	 *
	 * Admin ajax callback for action "dgwt_wcas_build_index"
	 *
	 * @return void
	 */
	public function ajaxBuildIndex() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_die( - 1, 403 );
		}

		check_ajax_referer( 'dgwt_wcas_build_index' );

		Builder::buildIndex();

		wp_send_json_success( array(
			'html' => Builder::renderIndexingStatus()
		) );
	}

	/**
	 * Start build an index after first vising a settings page
	 */
	public function maintenanceOnInit() {

		if ( ! Helpers::isSettingsPage() ) {
			return;
		}

		$status = Builder::getInfo( 'status' );

		// Build index on start
		if ( empty( $status ) || $status === 'not-exist' ) {
			Builder::buildIndex();
		}
	}

	/**
	 * Stop building index
	 *
	 * Admin ajax callback for action "dgwt_wcas_stop_build_index"
	 *
	 * @return void
	 */
	public function ajaxStopBuildIndex() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_die( - 1, 403 );
		}

		check_ajax_referer( 'dgwt_wcas_stop_build_index' );

		Builder::addInfo( 'status', 'cancellation' );
		Builder::log( 'Stop building the index. Starting the cancellation process.' );

		Builder::cancelBuildIndex();

		sleep( 1 );

		wp_send_json_success( array(
			'html' => Builder::renderIndexingStatus()
		) );
	}

	/**
	 * Refresh index status
	 *
	 * Admin ajax callback for action "dgwt_wcas_build_index_heartbeat"
	 *
	 * @return void
	 */
	public function ajaxBuildIndexHeartbeat() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_die( - 1, 403 );
		}

		check_ajax_referer( 'dgwt_wcas_build_index_heartbeat' );

		$status       = Builder::getInfo( 'status' );
		$loop         = false;
		$lastActionTs = absint( Builder::getInfo( 'last_action_ts' ) );

		$diff = time() - $lastActionTs;

		if ( empty( $lastActionTs ) ) {
			$diff = 0;
		}

		if (
			$diff >= MINUTE_IN_SECONDS && $diff <= ( MINUTE_IN_SECONDS + 1 )
			|| $diff >= ( 3 * MINUTE_IN_SECONDS ) && $diff <= ( 3 * MINUTE_IN_SECONDS + 1 )
			|| $diff >= ( 5 * MINUTE_IN_SECONDS ) && $diff <= ( 5 * MINUTE_IN_SECONDS + 1 )
		) {
			Builder::log( sprintf( '[Indexer] %d minute(s) with no action', floor( $diff / 60 ) ), 'debug', 'file' );
		}

		if ( Builder::isIndexerWorkingTooLong() ) {
			if ( Troubleshooting::hasWpCronMissedEvents() ) {
				Builder::log( sprintf( '[Indexer] [Error code: 001] The index build was stuck for %d minutes.', floor( $diff / 60 ) ), 'emergency', 'both' );
			} else {
				Builder::log( sprintf( '[Indexer] [Error code: 002] The index build was stuck for %d minutes.', floor( $diff / 60 ) ), 'emergency', 'both' );
			}

			Builder::addInfo( 'status', 'error' );
			Builder::log( 'Stop building the index. Starting the cancellation process.' );
			Builder::cancelBuildIndex();

			$loop = true;
		}

		if ( in_array( $status, array( 'preparing', 'building', 'cancellation' ) ) ) {
			$loop = true;
		}

		$refreshOnce = '';
		if ( $status === 'completed' && ! $loop && ! empty( Builder::getInfo( 'non_critical_errors' ) ) ) {
			$refreshOnce = Builder::getInfo( 'build_id' );
		}

		wp_send_json_success( array(
			'html'         => Builder::renderIndexingStatus(),
			'loop'         => $loop,
			'refresh_once' => $refreshOnce,
		) );
	}


	/**
	 * Show/hide indexer logs
	 *
	 * Admin ajax callback for action "dgwt_wcas_index_details_toggle"
	 *
	 * @return void
	 */
	public function ajaxBuildIndexDetailsToggle() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_die( - 1, 403 );
		}

		delete_transient( Builder::DETAILS_DISPLAY_KEY );

		if ( ! empty( $_REQUEST['display'] ) && $_REQUEST['display'] == 'true' ) {
			set_transient( Builder::DETAILS_DISPLAY_KEY, 1, 3600 );
		} else {
			set_transient( Builder::DETAILS_DISPLAY_KEY, 0, 3600 );
		}
	}


	/**
	 * Rebuild index after changing some options
	 *
	 * @return void
	 */
	public function buildIndexOnchangeSettings( $oldSettings, $newSettings ) {

		if ( DGWT_WCAS()->engine !== 'tntsearchMySql' ) {
			return;
		};

		$listenKeys = array(
			'search_in_product_content',
			'search_in_product_excerpt',
			'search_in_product_sku',
			'search_in_product_attributes',
			'search_in_custom_fields',
			'exclude_out_of_stock',
			'filter_products_mode',
			'filter_products_rules',
			'show_matching_pages',
			'show_matching_posts',
			'search_synonyms',
		);

		$taxonomiesSlugs = DGWT_WCAS()->tntsearchMySql->taxonomies->getTaxonomiesSlugs();
		foreach ( $taxonomiesSlugs as $slug ) {
			$listenKeys[] = 'search_in_product_tax_' . $slug;
			$listenKeys[] = 'show_product_tax_' . $slug;
		}

		foreach ( $listenKeys as $key ) {
			if (
				(
					// Values are different
					is_array( $newSettings ) &&
					is_array( $oldSettings ) &&
					array_key_exists( $key, $newSettings ) &&
					array_key_exists( $key, $oldSettings ) &&
					$newSettings[ $key ] != $oldSettings[ $key ]
				) ||
				(
					// The key does not exist yet
					is_array( $newSettings ) &&
					is_array( $oldSettings ) &&
					array_key_exists( $key, $newSettings ) &&
					! array_key_exists( $key, $oldSettings )
				)
			) {
				Builder::buildIndex();
				break;
			}
		}

	}

	/**
	 * Bypass for WP Background Processing when BasicAuth is enabled
	 *
	 * @return void
	 */
	public function wpBgProcessingBasicAuthBypass() {

		$authorization = Helpers::getBasicAuthHeader();
		if ( $authorization ) {

			add_filter( 'http_request_args', function ( $r, $url ) {

				if ( strpos( $url, 'wp-cron.php' ) !== false
				     || strpos( $url, 'admin-ajax.php' ) !== false ) {

					$r['headers']['Authorization'] = Helpers::getBasicAuthHeader();

				}

				return $r;
			}, 10, 2 );
		}

	}

	/**
	 * Get prices for products
	 * AJAX callback
	 *
	 * @return void
	 */
	public function getDynamicPrices() {

		if ( ! defined( 'DGWT_WCAS_AJAX' ) ) {
			define( 'DGWT_WCAS_AJAX', true );
		}

		$prices = array();

		if ( ! empty( $_POST['items'] ) && array( $_POST['items'] ) ) {
			foreach ( $_POST['items'] as $postID ) {
				if ( ! empty( $postID ) && is_numeric( $postID ) ) {

					$postID = absint( $postID );

					$product = new Product( $postID );

					if ( $product->isCorrect() ) {
						$prices[] = (object) array(
							'id'    => $postID,
							'price' => $product->getPriceHTML()
						);
					}

				}
			}
		}

		wp_send_json_success( $prices );
	}

}
