<?php


namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer;

use DgoraWcas\Engines\TNTSearchMySQL\Config;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Readable\Indexer as IndexerR;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Indexer as IndexerS;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Taxonomy\Indexer as IndexerTax;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Vendor\Indexer as IndexerVendors;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Variation\Indexer as IndexerVar;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Database as DatabaseS;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Readable\Database as DatabaseR;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Taxonomy\Database as DatabaseT;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Taxonomy\Request as RequestT;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Variation\Database as DatabaseVar;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\AsyncProcess as AsyncProcessS;
use DgoraWcas\Helpers;
use DgoraWcas\Multilingual;

class Builder {

	const LAST_BUILD_OPTION_KEY = 'dgwt_wcas_indexer_last_build';
	const DETAILS_DISPLAY_KEY = 'dgwt_wcas_indexer_details_display';
	const INDEXING_PREPARE_PROCESS_EXIST_KEY = 'dgwt_wcas_indexer_prepare_process_exist';
	const INDEXER_DEBUG_TRANSIENT_KEY = 'dgwt_wcas_indexer_debug';
	const INDEXER_DEBUG_SCOPE_TRANSIENT_KEY = 'dgwt_wcas_indexer_debug_scope';
	const SEARCHABLE_SET_ITEMS_COUNT = 50;
	const READABLE_SET_ITEMS_COUNT = 25;
	const VARIATIONS_SET_ITEMS_COUNT = 25;
	const TAXONOMY_SET_ITEMS_COUNT = 100;

	public static $indexerDebugScopes = array(
		'all',
		'readable',
		'searchable',
		'taxonomy',
		'variation',
		'bg-process',
	);

	/**
	 * Structure of indexer data
	 *
	 * @return array
	 */
	private static function getIndexInfoStruct() {
		return array(
			'build_id'                        => uniqid(),
			'db'                              => 'MySQL',
			'status'                          => '',
			'start_ts'                        => time(),
			'start_searchable_ts'             => 0,
			'start_readable_ts'               => 0,
			'start_taxonomies_ts'             => 0,
			'start_variation_ts'              => 0,
			'end_ts'                          => 0,
			'end_searchable_ts'               => 0,
			'end_readable_ts'                 => 0,
			'end_taxonomies_ts'               => 0,
			'end_variation_ts'                => 0,
			'last_action_ts'                  => time(),
			'readable_processed'              => 0,
			'searchable_processed'            => 0,
			'variations_processed'            => 0,
			'terms_processed'                 => 0,
			'total_terms_for_indexing'        => 0,
			'total_variations_for_indexing'   => 0,
			'total_products_for_indexing'     => 0,
			'total_non_products_for_indexing' => 0,
			'logs'                            => array(),
			'non_critical_errors'             => array(),
			'languages'                       => array(),
			'plugin_version'                  => '',
		);
	}

	/**
	 * @return bool
	 */
	private static function createInfoStruct() {
		return update_option( self::LAST_BUILD_OPTION_KEY, self::getIndexInfoStruct() );
	}

	/**
	 * Add specific info about the last index build
	 *
	 * @return bool
	 */
	public static function addInfo( $key, $value ) {
		$added = false;

		$lastInfo = self::getLastBuildInfo();

		if ( array_key_exists( $key, $lastInfo ) ) {
			$lastInfo['last_action_ts'] = time();
			$lastInfo[ $key ]           = $value;
			$added                      = update_option( self::LAST_BUILD_OPTION_KEY, $lastInfo );
		}

		return $added;
	}

	public static function getInfo( $key ) {

		$value    = '';
		$lastInfo = self::getLastBuildInfo();

		if ( array_key_exists( $key, $lastInfo ) ) {
			$value = $lastInfo[ $key ];
		}

		return $value;
	}

	/**
	 * Is indexer debug enabled
	 *
	 * @return bool
	 */
	public static function isDebug() {
		if ( defined( 'DGWT_WCAS_INDEXER_DEBUG' ) ) {
			return (bool) DGWT_WCAS_INDEXER_DEBUG;
		}

		return (bool) get_transient( self::INDEXER_DEBUG_TRANSIENT_KEY );
	}

	/**
	 * Get indexer debug scope
	 *
	 * @return array
	 */
	public static function getDebugScopes() {
		if ( defined( 'DGWT_WCAS_INDEXER_DEBUG_SCOPE' ) ) {
			$scope = explode( ',', DGWT_WCAS_INDEXER_DEBUG_SCOPE );

			return array_map( 'trim', $scope );
		}

		$scope = get_transient( self::INDEXER_DEBUG_SCOPE_TRANSIENT_KEY );

		return is_array( $scope ) ? $scope : array( 'all' );
	}

	/**
	 * Check if scope of debug is enabled
	 *
	 * @param string $scope
	 *
	 * @return bool
	 */
	public static function isDebugScopeActive( $scope ) {
		if ( $scope === 'all' || in_array( 'all', self::getDebugScopes() ) ) {
			return true;
		}

		return in_array( $scope, self::getDebugScopes() );
	}

	/**
	 * Log indexer message
	 *
	 * @param string $message
	 * @param string $level One of the following:
	 *     'emergency': The indexer has stopped due to a fatal error.
	 *     'warning': PHP warnings.
	 *     'notice': PHP notices.
	 *     'info': Informational messages about indexer.
	 *     'debug': Debug-level messages.
	 * @param string $destination Destination. Choices: 'db', 'file' or 'both'
	 * @param string $scope Scope of log. Choices: look at self::$indexerDebugScopes
	 *
	 * @return void
	 */
	public static function log( $message, $level = 'info', $destination = 'both', $scope = 'all' ) {
		if ( defined( 'DGWT_WCAS_DISABLE_INDEXER_LOGS' ) && DGWT_WCAS_DISABLE_INDEXER_LOGS ) {
			return;
		}

		if ( $destination === 'file' || $destination === 'both' ) {
			Logger::log( $message, $level, $scope );
		}

		if ( $destination === 'db' || $destination === 'both' ) {
			$lastInfo = self::getLastBuildInfo();

			if ( ! array_key_exists( 'logs', $lastInfo ) ) {
				return;
			}

			if ( is_array( $lastInfo['logs'] ) ) {
				$lastInfo['logs'][] = array(
					'time'    => current_time( 'timestamp' ),
					'error'   => in_array( $level, array( 'emergency', 'warning', 'notice' ) ),
					'message' => $message
				);

				update_option( self::LAST_BUILD_OPTION_KEY, $lastInfo );
			}
		}
	}

	/**
	 * Get all logs
	 *
	 * @return array
	 */
	public static function getLogs() {

		$logs     = array();
		$lastInfo = self::getLastBuildInfo();

		if ( ! empty( $lastInfo['logs'] ) ) {
			$logs = $lastInfo['logs'];
		}

		return $logs;
	}

	public static function buildIndex( $async = true ) {
		self::prepareBuildIndex();

		if ( Config::isIndexerMode( 'direct' ) ) {
			$async = false;
		}

		if ( $async ) {
			DGWT_WCAS()->tntsearchMySql->asyncRebuildIndex->data( array( 'force' => true ) )->schedule_event()->dispatch();
		} else {
			self::buildIndexProcess();
		}
	}

	public static function prepareBuildIndex() {
		if ( get_transient( self::INDEXING_PREPARE_PROCESS_EXIST_KEY ) ) {
			self::log( 'Indexer already preparing building the index' );

			return;
		}

		Logger::removeLogs();

		set_transient( self::INDEXING_PREPARE_PROCESS_EXIST_KEY, true, 5 );

		self::createInfoStruct();

		self::log( sprintf( 'Indexer mode: %s', Config::getIndexerMode() ) );

		self::cancelBuildIndex( false );

		self::addInfo( 'status', 'preparing' );

		self::wipeActionScheduler();

		if ( Multilingual::isMultilingual() ) {
			self::log( sprintf( 'Multilingual: Yes, Provider: %s, Default: %s, Langs: %s', Multilingual::getProvider(), Multilingual::getDefaultLanguage(), implode( ',', Multilingual::getLanguages() ) ) );
			self::addInfo( 'languages', Multilingual::getLanguages() );
		}

		self::addInfo( 'plugin_version', DGWT_WCAS_VERSION );

		self::log( 'Indexer prepared for building the index' );
		do_action( 'dgwt/wcas/indexer/prepared' );
	}

	public static function buildIndexProcess() {
		$status = self::getInfo( 'status' );

		if ( $status === 'building' ) {
			self::log( 'Indexer already running' );

			return;
		} else if ( $status !== 'preparing' ) {
			self::log( 'Indexer is not prepared for running' );

			return;
		}

		self::addInfo( 'status', 'building' );

		self::log( 'Indexer started building the index' );
		do_action( 'dgwt/wcas/indexer/started' );

		$source = new SourceQuery( array( 'ids' => true ) );

		$productsSet = array();
		$products    = $source->getData();

		self::addInfo( 'total_products_for_indexing', count( $products ) );

		// Readable
		DatabaseR::create();
		self::addInfo( 'start_readable_ts', time() );
		self::log( '[Readable index] Building...' );

		// Variations
		if ( self::canBuildVariationsIndex() ) {
			DatabaseVar::create();
		}

		// Taxonomies
		if ( self::canBuildTaxonomyIndex() ) {
			DatabaseT::create();
		}

		// Searchable
		DatabaseS::create();
		self::addInfo( 'start_searchable_ts', time() );
		self::log( '[Searchable index] Building...' );

		$smallProductsSetCount = apply_filters( 'dgwt/wcas/indexer/readable_set_items_count', self::READABLE_SET_ITEMS_COUNT );
		$productsSetCount      = apply_filters( 'dgwt/wcas/indexer/searchable_set_items_count', self::SEARCHABLE_SET_ITEMS_COUNT );

		if ( Config::isIndexerMode( 'direct' ) ) {
			$productsSet = wp_list_pluck( $products, 'ID' );
			DGWT_WCAS()->tntsearchMySql->asynchBuildIndexR->task( $productsSet );
			DGWT_WCAS()->tntsearchMySql->asynchBuildIndexS->task( $productsSet );
		} else {
			$i = 0;
			foreach ( $products as $row ) {
				$productsSet[]      = $row['ID'];
				$smallProductsSet[] = $row['ID'];

				if ( count( $smallProductsSet ) === $smallProductsSetCount || $i + 1 === count( $products ) ) {
					DGWT_WCAS()->tntsearchMySql->asynchBuildIndexR->push_to_queue( $smallProductsSet );
					$smallProductsSet = array();
				}

				if ( count( $productsSet ) === $productsSetCount || $i + 1 === count( $products ) ) {
					DGWT_WCAS()->tntsearchMySql->asynchBuildIndexS->push_to_queue( $productsSet );
					$productsSet = array();
				}

				$i ++;
			}
		}

		// Non-product search
		$types = Helpers::getAllowedPostTypes( 'no-products' );
		if ( ! empty( $types ) ) {
			$totalNonProducts = 0;
			foreach ( $types as $type ) {

				$npSource = new PostsSourceQuery( array(
					'ids'       => true,
					'postTypes' => array( $type )
				) );
				$posts    = $npSource->getData();

				$totalNonProducts = $totalNonProducts + count( $posts );

				$nonProductsSet      = array();
				$smallNonProductsSet = array();

				if ( Config::isIndexerMode( 'direct' ) ) {
					$nonProductsSet = wp_list_pluck( $posts, 'ID' );
					DGWT_WCAS()->tntsearchMySql->asynchBuildIndexR->task( $nonProductsSet );
					DGWT_WCAS()->tntsearchMySql->asynchBuildIndexS->task( $nonProductsSet );
				} else {
					$i = 0;
					foreach ( $posts as $row ) {
						$nonProductsSet[]      = $row['ID'];
						$smallNonProductsSet[] = $row['ID'];

						if ( count( $smallNonProductsSet ) === $smallProductsSetCount || $i + 1 === count( $posts ) ) {
							DGWT_WCAS()->tntsearchMySql->asynchBuildIndexR->push_to_queue( $smallNonProductsSet );
							$smallNonProductsSet = array();
						}

						if ( count( $nonProductsSet ) === $productsSetCount || $i + 1 === count( $posts ) ) {
							DGWT_WCAS()->tntsearchMySql->asynchBuildIndexS->push_to_queue( $nonProductsSet );
							$nonProductsSet = array();
						}

						$i ++;
					}
				}
			}

			self::addInfo( 'total_non_products_for_indexing', $totalNonProducts );
		}

		if ( Config::isIndexerMode( 'direct' ) ) {
			DGWT_WCAS()->tntsearchMySql->asynchBuildIndexR->complete();
			DGWT_WCAS()->tntsearchMySql->asynchBuildIndexS->complete();
		} elseif ( Config::isIndexerMode( 'sync' ) ) {
			DGWT_WCAS()->tntsearchMySql->asynchBuildIndexR->save();
			DGWT_WCAS()->tntsearchMySql->asynchBuildIndexS->save()->maybe_dispatch();
		} else {
			DGWT_WCAS()->tntsearchMySql->asynchBuildIndexR->save()->maybe_dispatch();
			sleep( 1 );
			DGWT_WCAS()->tntsearchMySql->asynchBuildIndexS->save()->maybe_dispatch();
		}
	}

	/**
	 * Stops build index and wipes all processes and data
	 *
	 * @param bool $clearInfo clear info (start time of processes)
	 *
	 * @return void
	 */
	public static function cancelBuildIndex( $clearInfo = true ) {
		$indexerR = new IndexerR;
		$indexerS = new IndexerS;

		DGWT_WCAS()->tntsearchMySql->asynchBuildIndexR->cancel_process();
		DGWT_WCAS()->tntsearchMySql->asynchBuildIndexS->cancel_process();
		DGWT_WCAS()->tntsearchMySql->asynchBuildIndexT->cancel_process();
		DGWT_WCAS()->tntsearchMySql->asynchBuildIndexV->cancel_process();

		if ( self::searchableIndexExists() ) {
			$indexerS->wipe();
		}

		if ( self::readableIndexExists() ) {
			$indexerR->wipe();
		}

		if ( self::taxIndexExists() ) {
			$taxIndexer = new IndexerTax();
			$taxIndexer->wipe();
		}

		if ( self::vendorsIndexExists() ) {
			$vendorsIndexer = new IndexerVendors();
			$vendorsIndexer->wipe();
		}

		if ( self::variationsIndexExists() ) {
			$varIndexer = new IndexerVar();
			$varIndexer->wipe();
		}

		Helpers::removeBatchOptions__premium_only();

		if ( $clearInfo ) {
			self::addInfo( 'start_searchable_ts', '' );
			self::addInfo( 'start_readable_ts', '' );
			self::addInfo( 'start_variation_ts', '' );
			self::addInfo( 'start_taxonomies_ts', '' );
		}
	}

	public static function getReadableProgress() {
		global $wpdb;

		$percent    = 0;
		$totalItems = self::getInfo( 'total_products_for_indexing' );

		if ( ! empty( Helpers::getAllowedPostTypes( 'no-products' ) ) ) {
			$npTotalItems = self::getInfo( 'total_non_products_for_indexing' );
			if ( is_numeric( $totalItems ) && is_numeric( $npTotalItems ) && ! empty( $npTotalItems ) ) {
				$totalItems += $npTotalItems;
			}
		}

		if ( self::readableIndexExists() ) {
			$totalIndexed = $wpdb->get_var( 'SELECT COUNT(DISTINCT post_id) FROM ' . $wpdb->dgwt_wcas_index );
		}

		if (
			! empty( $totalItems )
			&& is_numeric( $totalItems )
			&& ! empty( $totalIndexed )
			&& is_numeric( $totalIndexed )
		) {
			$percent = $totalIndexed * 100 / $totalItems;
		}

		return absint( $percent );
	}

	public static function getSearchableProgress() {

		$percent    = 0;
		$totalItems = self::getInfo( 'total_products_for_indexing' );

		if ( ! empty( Helpers::getAllowedPostTypes( 'no-products' ) ) ) {
			$npTotalItems = self::getInfo( 'total_non_products_for_indexing' );
			if ( is_numeric( $totalItems ) && is_numeric( $npTotalItems ) && ! empty( $npTotalItems ) ) {
				$totalItems += $npTotalItems;
			}
		}

		$processed = self::getInfo( 'searchable_processed' );

		if (
			! empty( $totalItems )
			&& is_numeric( $totalItems )
			&& ! empty( $processed )
			&& is_numeric( $processed )
		) {
			$percent = $processed * 100 / $totalItems;
		}

		return absint( $percent );
	}

	public static function getVariationsProgress() {

		$percent    = 0;
		$totalItems = self::getInfo( 'total_variations_for_indexing' );
		$processed  = self::getInfo( 'variations_processed' );

		if (
			! empty( $totalItems )
			&& is_numeric( $totalItems )
			&& ! empty( $processed )
			&& is_numeric( $processed )
		) {
			$percent = $processed * 100 / $totalItems;
		}

		return absint( $percent );
	}

	public static function getTaxonomiesProgress() {

		$percent    = 0;
		$totalItems = self::getInfo( 'total_terms_for_indexing' );
		$processed  = self::getInfo( 'terms_processed' );

		if (
			! empty( $totalItems )
			&& is_numeric( $totalItems )
			&& ! empty( $processed )
			&& is_numeric( $processed )
		) {
			$percent = $processed * 100 / $totalItems;
		}

		return absint( $percent );
	}

	public static function getProgressBarValue() {

		if ( self::getInfo( 'status' ) === 'completed' ) {
			return 100;
		}

		$percentR = self::getReadableProgress();
		$percentS = self::getSearchableProgress();
		$percentV = self::getVariationsProgress();
		$percentT = self::getTaxonomiesProgress();

		if ( self::canBuildVariationsIndex() && self::canBuildTaxonomyIndex() ) {
			$progress = $percentR * 0.4 + $percentS * 0.4 + $percentV * 0.1 + $percentT * 0.1;
		} elseif ( self::canBuildVariationsIndex() || self::canBuildTaxonomyIndex() ) {
			$progress = $percentR * 0.4 + $percentS * 0.4 + $percentV * 0.2 + $percentT * 0.2;
		} else {
			$progress = ( $percentR + $percentS ) / 2;
		}

		$progress = apply_filters( 'dgwt/wcas/indexer/process_status/progress', $progress, $percentR, $percentS, $percentV, $percentT );

		return $progress > 100 ? 99 : $progress;
	}

	public static function renderIndexingStatus() {
		self::refreshStatus();

		$html = '<div class="js-dgwt-wcas-indexing-wrapper">';
		$html .= self::getIndexHeader();
		$html .= self::getProcessStatus();
		$html .= '</div>';

		return $html;
	}

	public static function refreshStatus() {
		global $wpdb;

		$status = self::getInfo( 'status' );

		$startTs    = self::getInfo( 'start_ts' );
		$sStartTs   = self::getInfo( 'start_searchable_ts' );
		$rStartTs   = self::getInfo( 'start_readable_ts' );
		$taxStartTs = self::getInfo( 'start_taxonomies_ts' );
		$sEndTs     = self::getInfo( 'end_searchable_ts' );
		$rEndTs     = self::getInfo( 'end_readable_ts' );
		$taxEndTs   = self::getInfo( 'end_taxonomies_ts' );

		switch ( $status ) {
			case 'cancellation':

				sleep( 2 );

				self::addInfo( 'status', 'not-exist' );
				self::log( 'Canceling completed' );

				break;
			case 'error':

				self::cancelBuildIndex();

				break;
		}

	}

	public static function getIndexHeader() {
		$text              = '';
		$statusColor       = '';
		$statusText        = '';
		$status            = self::getInfo( 'status' );
		$endTs             = self::getInfo( 'end_ts' );
		$totalProducts     = self::getInfo( 'total_products_for_indexing' );
		$totalNonProducts  = self::getInfo( 'total_non_products_for_indexing' );
		$nonCriticalErrors = self::getInfo( 'non_critical_errors' );
		$lastErrorCode     = '';
		$lastErrorMessage  = '';

		switch ( $status ) {
			case 'preparing':
				$text        = __( 'Wait... Preparing indexing in progress', 'ajax-search-for-woocommerce' );
				$statusText  = __( 'This process will continue in the background. You can leave this page!',
					'ajax-search-for-woocommerce' );
				$statusColor = '#e6a51d';
				break;
			case 'building':
				$text        = __( 'Wait... Indexing in progress', 'ajax-search-for-woocommerce' );
				$statusText  = __( 'This process will continue in the background. You can leave this page!',
					'ajax-search-for-woocommerce' );
				$statusColor = '#e6a51d';
				break;
			case 'cancellation':
				$text        = __( 'Wait... The index build process is canceling', 'ajax-search-for-woocommerce' );
				$statusText  = __( 'Canceling...', 'ajax-search-for-woocommerce' );
				$statusColor = '#7293b0';
				break;
			case 'completed':
				$lastDate = ! empty( $endTs ) ? Helpers::localDate( $endTs ) : '-';
				if ( empty( $nonCriticalErrors ) ) {
					$text = __( 'The search index was built successfully.', 'ajax-search-for-woocommerce' );
				} else {
					$text = __( 'The search index was built successfully, but some non-critical errors occurred.', 'ajax-search-for-woocommerce' );
				}
				$statusText  = __( 'Completed. Works.', 'ajax-search-for-woocommerce' );
				$statusColor = '#4caf50';
				break;
			case 'error':
				$text        = __( 'The search index could not be built.', 'ajax-search-for-woocommerce' );
				$statusText  = __( 'Errors', 'ajax-search-for-woocommerce' );
				$statusColor = '#d75f5f';
				list( $lastErrorCode, $lastErrorMessage ) = Logger::getLastEmergencyLog();
				break;
			default:
				$text        = __( 'The search index does not exist yet. Build it now.', 'ajax-search-for-woocommerce' );
				$statusText  = __( 'Not exist', 'ajax-search-for-woocommerce' );
				$statusColor = '#aaaaaa';
				break;
		}

		$actionButton = self::getIndexButton();
		$isDetails    = get_transient( self::DETAILS_DISPLAY_KEY );
		$status       = self::getInfo( 'status' );

		ob_start();
		include DGWT_WCAS_DIR . 'partials/admin/indexer-header.php';
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public static function getIndexButton() {
		$status = self::getInfo( 'status' );

		if ( in_array( $status, array( 'building', 'preparing' ) ) ) {
			$html = '<a class="button js-ajax-stop-build-index" href="#">' . __( 'Stop process',
					'ajax-search-for-woocommerce' ) . '</a>';
		} elseif ( in_array( $status, array( 'completed' ) ) ) {
			$html = '<a class="button js-ajax-build-index" href="#">' . __( 'Rebuild index',
					'ajax-search-for-woocommerce' ) . '</a>';
		} elseif ( in_array( $status, array( 'error', 'cancellation' ) ) ) {
			$html = '<a class="button js-ajax-build-index" href="#">' . __( 'Try to build the index again.',
					'ajax-search-for-woocommerce' ) . '</a>';
		} else {
			$html = '<a class="button js-ajax-build-index ajax-build-index-primary" href="#">' . __( 'Build index',
					'ajax-search-for-woocommerce' ) . '</a>';

		}

		return $html;
	}

	/**
	 * Check if readable products table exist
	 *
	 * @return bool
	 */
	public static function readableIndexExists() {
		global $wpdb;

		return Helpers::isTableExists( $wpdb->dgwt_wcas_index );
	}

	/**
	 * Check if readable taxonmies table exist
	 *
	 * @return bool
	 */
	public static function taxIndexExists() {
		global $wpdb;

		return Helpers::isTableExists( $wpdb->dgwt_wcas_tax_index );
	}

	/**
	 * Check if variations table exist
	 *
	 * @return bool
	 */
	public static function variationsIndexExists() {
		global $wpdb;

		return Helpers::isTableExists( $wpdb->dgwt_wcas_var_index );
	}

	/**
	 * Check if vendors table exist
	 *
	 * @return bool
	 */
	public static function vendorsIndexExists() {
		global $wpdb;

		return Helpers::isTableExists( $wpdb->dgwt_wcas_ven_index );
	}

	/**
	 * Check if searchable tables exist
	 *
	 * @param string $currentLang
	 *
	 * @return bool
	 */
	public static function searchableIndexExists( $currentLang = '' ) {
		global $wpdb;
		$isShortInit    = defined( 'SHORTINIT' ) && SHORTINIT;
		$wordlistExists = false;
		$doclistExists  = false;

		$currentLang = Multilingual::isLangCode( $currentLang ) ? $currentLang : '';

		$wpdb->hide_errors();

		ob_start();

		$infoExists = Helpers::isTableExists( $wpdb->dgwt_wcas_si_info );

		if ( ! empty( $currentLang ) || ( ! $isShortInit && Multilingual::isMultilingual() ) ) {
			$wordlistInstances = 0;
			$doclistInstances  = 0;

			if ( ! empty( $currentLang ) ) {
				$langs = array( $currentLang );
			} else {
				$langs = Multilingual::getLanguages();
			}

			foreach ( $langs as $lang ) {

				$lang = str_replace( '-', '_', $lang );

				$wordlistTable = $wpdb->dgwt_wcas_si_wordlist . '_' . $lang;
				$doclistTable  = $wpdb->dgwt_wcas_si_doclist . '_' . $lang;

				if ( Helpers::isTableExists( $wordlistTable ) ) {
					$wordlistInstances ++;
				}

				if ( Helpers::isTableExists( $doclistTable ) ) {
					$doclistInstances ++;
				}

			}

			if ( $wordlistInstances === count( $langs ) ) {
				$wordlistExists = true;
			}

			if ( $doclistInstances === count( $langs ) ) {
				$doclistExists = true;
			}

		} else {
			$wordlistExists = Helpers::isTableExists( $wpdb->dgwt_wcas_si_wordlist );
			$doclistExists  = Helpers::isTableExists( $wpdb->dgwt_wcas_si_doclist );
		}

		ob_end_clean();


		return $wordlistExists && $doclistExists && $infoExists;
	}

	/**
	 * Check if cache table exists
	 *
	 * @param string $lang Language
	 * @param string $postType Post type. Leave empty to check 'product' table
	 *
	 * @return bool
	 */
	public static function searchableCacheExists( $lang = '', $postType = '' ) {
		global $wpdb;

		$lang = Multilingual::isLangCode( $lang ) ? $lang : '';
		$lang = str_replace( '-', '_', $lang );

		$cacheTable = $wpdb->dgwt_wcas_si_cache;
		if ( ! empty( $postType ) ) {
			$cacheTable .= '_' . $postType;
		}
		if ( ! empty( $lang ) ) {
			$cacheTable .= '_' . $lang;
		}

		return Helpers::isTableExists( $cacheTable );
	}


	public static function getReadableTotalIndexed() {
		global $wpdb;
		$count = 0;

		if ( self::readableIndexExists() ) {
			$r = $wpdb->get_var( 'SELECT COUNT(DISTINCT post_id) FROM ' . $wpdb->dgwt_wcas_index );
			if ( ! empty( $r ) && is_numeric( $r ) ) {
				$count = absint( $r );
			}
		}


		return $count;
	}

	public static function getProcessStatus() {

		$info = array();
		foreach ( self::getIndexInfoStruct() as $key => $field ) {
			$offset = get_option( 'gmt_offset' );
			$value  = self::getInfo( $key );
			if ( strpos( $key, '_ts' ) !== false && ! empty( $value ) && ! empty( $offset ) ) {
				$info[ $key ] = $value + ( $offset * 3600 );
			} else {
				$info[ $key ] = $value;
			}
		}

		$progressPercent       = self::getProgressBarValue();
		$logs                  = self::getLogs();
		$isDetails             = get_transient( self::DETAILS_DISPLAY_KEY );
		$canBuildTaxonomyIndex = self::canBuildTaxonomyIndex();
		$canBuildVendorsIndex  = self::canBuildVendorsIndex();

		ob_start();
		include DGWT_WCAS_DIR . 'partials/admin/indexer-body.php';
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Check if can index taxonomies
	 *
	 * @return bool
	 */
	public static function canBuildTaxonomyIndex() {
		return ! empty( DGWT_WCAS()->tntsearchMySql->taxonomies->getActiveTaxonomies( 'search_direct' ) );
	}

	/**
	 * Check if can index vendors
	 *
	 * @return bool
	 */
	public static function canBuildVendorsIndex() {
		return apply_filters( 'dgwt/wcas/search/vendors', false );
	}

	/**
	 * Check if can build variations index
	 *
	 * @return bool
	 */
	public static function canBuildVariationsIndex() {
		$canBuild = false;

		if ( ! empty( $_POST['dgwt_wcas_settings'] ) ) {

			$settings = $_POST['dgwt_wcas_settings'];

			if ( ! empty( $settings['search_in_product_sku'] ) && $settings['search_in_product_sku'] === 'on' ) {
				$canBuild = true;
			}
		}

		if ( ! $canBuild ) {
			$canBuild = DGWT_WCAS()->settings->getOption( 'search_in_product_sku' ) === 'on';
		}

		return $canBuild;
	}

	/**
	 * Check if index is completed and valid
	 *
	 * @param string $lang
	 *
	 * @return bool
	 */
	public static function isIndexValid( $lang = '' ) {
		global $wpdb;
		$valid = false;

		if ( self::getInfo( 'status' ) === 'completed'
		     && self::searchableIndexExists( $lang )
		     && self::readableIndexExists()
		) {

			$info = $wpdb->get_var( "SELECT ivalue FROM $wpdb->dgwt_wcas_si_info WHERE ikey = 'stemmer'" );

			if ( ! empty( $info ) ) {
				$valid = true;
			}

		}

		return $valid;
	}

	/**
	 * Get last index build info
	 *
	 * @return array
	 */
	public static function getLastBuildInfo() {
		global $wpdb;

		$data = array();

		$opt = $wpdb->get_var( $wpdb->prepare( "SELECT SQL_NO_CACHE option_value FROM $wpdb->options WHERE option_name = %s",
			self::LAST_BUILD_OPTION_KEY ) );

		if ( ! empty( $opt ) ) {
			$opt = @unserialize( $opt );
			if ( is_array( $opt ) ) {
				$data = $opt;
			}
		}

		return $data;

	}

	/**
	 * Wipe all data of deprecated SQLite driver
	 *
	 * @return void
	 */
	public static function wipeSQLiteAfterEffects() {

		$uploadDir = wp_upload_dir();
		if ( ! empty( $uploadDir['basedir'] ) ) {


			$directory = $uploadDir['basedir'] . '/wcas-search';
			$file      = $uploadDir['basedir'] . '/wcas-search/products.index';


			if ( file_exists( $file ) && is_writable( $file ) ) {
				@unlink( $file );
			}

			if ( file_exists( $directory ) && is_writable( $directory ) ) {
				@rmdir( $directory );
			}

		}

	}

	/**
	 * Complete the search index
	 *
	 * @return void
	 */
	public static function maybeMarkAsCompleted() {

		$status = self::getInfo( 'status' );
		$sEndTs = self::getInfo( 'end_searchable_ts' );
		$rEndTs = self::getInfo( 'end_readable_ts' );
		$tEndTs = self::canBuildTaxonomyIndex() ? self::getInfo( 'end_taxonomies_ts' ) : 1;
		$vEndTs = self::canBuildVariationsIndex() ? self::getInfo( 'end_variation_ts' ) : 1;

		if ( 'building' === $status && ! empty( $sEndTs ) && ! empty( $rEndTs ) && ! empty( $vEndTs ) && ! empty( $tEndTs ) ) {
			self::addInfo( 'status', 'completed' );
			self::addInfo( 'end_ts', time() );
			self::log( 'Indexing completed' );
			do_action( 'dgwt/wcas/indexer/status/completed' );
		}

	}

	/**
	 * Remove all database tables created by this plugin
	 *
	 * @param bool $networkScope delete tables in whole network
	 *
	 * @return void
	 */
	public static function deleteIndexOptions( $networkScope = false ) {
		global $wpdb;

		$prefix = $wpdb->prefix;

		if ( is_multisite() && $networkScope ) {
			$prefix = $wpdb->base_prefix;
		}

		delete_option( self::LAST_BUILD_OPTION_KEY );
		delete_transient( self::DETAILS_DISPLAY_KEY );

		if ( is_multisite() && $networkScope ) {
			foreach ( get_sites() as $site ) {
				if ( is_numeric( $site->blog_id ) ) {

					$blogID = $site->blog_id == 1 ? '' : $site->blog_id . '_';

					$table = $prefix . $blogID . 'options';

					$wpdb->delete( $table, array( 'option_name' => self::LAST_BUILD_OPTION_KEY ) );

					$wpdb->delete( $table, array( 'option_name' => '_transient_timeout_' . self::DETAILS_DISPLAY_KEY ) );
					$wpdb->delete( $table, array( 'option_name' => '_transient_' . self::DETAILS_DISPLAY_KEY ) );

				}
			}
		}

	}

	/**
	 * Remove all database tables created by this plugin
	 *
	 * @param bool $networkScope delete tables in whole network
	 *
	 * @return void
	 */
	public static function deleteDatabaseTables( $networkScope = false ) {
		global $wpdb;

		// DB tables
		$tables = Utils::getAllPluginTables( $networkScope );

		if ( ! empty( $tables ) ) {
			foreach ( $tables as $table ) {
				$wpdb->query( "DROP TABLE IF EXISTS $table" );
			}
		}
	}

	/**
	 * Removal of planned actions that will update products in the index
	 */
	public static function wipeActionScheduler() {
		$queue = Utils::getQueue();
		if ( empty( $queue ) ) {
			return;
		}

		try {
			$queue->cancel_all( 'dgwt/wcas/tnt/background_product_update' );
		} catch ( Exception $e ) {

		}
	}

	/**
	 * Dispatch building variation index
	 */
	public static function maybeDispatchVariationAsyncProcess() {
		if ( ! self::canBuildVariationsIndex() ) {
			return;
		}

		$status = self::getInfo( 'status' );
		$sEndTs = self::getInfo( 'end_searchable_ts' );
		$rEndTs = self::getInfo( 'end_readable_ts' );

		if (
			( Config::isIndexerMode( 'async' ) && $status === 'building' && ! empty( $rEndTs ) )
			|| ( Config::isIndexerMode( 'sync' ) && $status === 'building' && ! empty( $sEndTs ) && ! empty( $rEndTs ) )
			|| ( Config::isIndexerMode( 'direct' ) && $status === 'building' )
		) {
			self::addInfo( 'start_variation_ts', time() );
			// Reset end time because this process may end several times
			self::addInfo( 'end_variation_ts', 0 );
			self::log( '[Variation index] Building...' );

			DGWT_WCAS()->tntsearchMySql->asynchBuildIndexV->maybe_dispatch();
		}
	}

	/**
	 * Dispatch building taxonomies index
	 */
	public static function maybeDispatchTaxonomyAsyncProcess() {
		if ( ! self::canBuildTaxonomyIndex() ) {
			self::maybeDispatchVariationAsyncProcess();

			return;
		}

		$status = self::getInfo( 'status' );
		$rEndTs = self::getInfo( 'end_readable_ts' );

		if (
			( Config::isIndexerMode( 'async' ) && $status === 'building' && ! empty( $rEndTs ) )
			|| ( Config::isIndexerMode( 'sync' ) && $status === 'building' && ! empty( $rEndTs ) )
			|| ( Config::isIndexerMode( 'direct' ) && $status === 'building' )
		) {
			RequestT::handle();
		}
	}

	/**
	 * Check if the indexer working too long without any action
	 *
	 * @return bool
	 */
	public static function isIndexerWorkingTooLong() {
		$status = Builder::getInfo( 'status' );

		// Return early if indexer is not working
		if ( ! in_array( $status, array( 'building', 'preparing' ) ) ) {
			return false;
		}

		$lastActionTs = absint( Builder::getInfo( 'last_action_ts' ) );

		// Return early if the indexer info hasn't been created yet
		if ( empty( $lastActionTs ) ) {
			return false;
		}

		$diff = time() - $lastActionTs;

		$maxNoActionTime = defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ? 61 * MINUTE_IN_SECONDS : 16 * MINUTE_IN_SECONDS;
		/**
		 * Filters maximum no action time of indexer.
		 *
		 * @param int $maxNoActionTime Max time in seconds. 16 min if WP-Cron is enabled or 61 min if not
		 */
		$maxNoActionTime = apply_filters( 'dgwt/wcas/indexer/max_no_action_time', $maxNoActionTime );

		return in_array( $status, array( 'building', 'preparing' ) ) && $diff >= $maxNoActionTime;
	}
}
