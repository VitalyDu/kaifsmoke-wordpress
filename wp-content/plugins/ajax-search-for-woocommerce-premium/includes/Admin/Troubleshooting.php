<?php

namespace DgoraWcas\Admin;

use DgoraWcas\Admin\Promo\Upgrade;
use DgoraWcas\Helpers;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Builder;
use DgoraWcas\Multilingual;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Troubleshooting {

	const SECTION_ID = 'dgwt_wcas_troubleshooting';
	const TRANSIENT_RESULTS_KEY = 'dgwt_wcas_troubleshooting_async_results';
	const TRANSIENT_TEST_KEY = 'dgwt_wcas_test_transients';
	const TRANSIENT_TEST_KEY2 = 'dgwt_wcas_test_transients2';
	const ASYNC_TEST_NONCE = 'troubleshooting-async-test';
	const RESET_ASYNC_TESTS_NONCE = 'troubleshooting-reset-async-tests';
	const FIX_OUTOFSTOCK_NONCE = 'troubleshooting-fix-outofstock';
	const DISMIS_ELEMENTOR_TEMPLATE_NONCE = 'troubleshooting-dismiss-elementor-template';

	public function __construct() {
		if ( ! $this->checkRequirements() ) {
			return;
		}

		add_filter( 'dgwt/wcas/settings', array( $this, 'addSettingsTab' ) );
		add_filter( 'dgwt/wcas/settings/sections', array( $this, 'addSettingsSection' ) );
		add_filter( 'dgwt/wcas/scripts/admin/localize', array( $this, 'localizeSettings' ) );

		add_action( DGWT_WCAS_SETTINGS_KEY . '-form_bottom_' . self::SECTION_ID, array( $this, 'tabContent' ) );
		add_action( 'wp_ajax_dgwt_wcas_troubleshooting_test', array( $this, 'asyncTest' ) );
		add_action( 'wp_ajax_dgwt_wcas_troubleshooting_reset_async_tests', array( $this, 'resetAsyncTests' ) );
		add_action( 'wp_ajax_dgwt_wcas_troubleshooting_fix_outofstock', array( $this, 'fixOutOfStock' ) );
		add_action( 'wp_ajax_dgwt_wcas_troubleshooting_dismiss_elementor_template', array(
			$this,
			'dismissElementorTemplate'
		) );
		if ( dgoraAsfwFs()->is__premium_only() ) {
			add_action( 'wp_ajax_dgwt_wcas_troubleshooting_fix_outofstock', array(
				$this,
				'fixOutOfStock__premium_only'
			) );
		}
	}

	/**
	 * Add "Troubleshooting" tab on Settings page
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function addSettingsTab( $settings ) {
		$settings[ self::SECTION_ID ] = apply_filters( 'dgwt/wcas/settings/section=troubleshooting', array(
			10 => array(
				'name'  => 'troubleshooting_head',
				'label' => __( 'Troubleshooting', 'ajax-search-for-woocommerce' ),
				'type'  => 'head',
				'class' => 'dgwt-wcas-sgs-header'
			),
		) );

		return $settings;
	}

	/**
	 * Content of "Troubleshooting" tab on Settings page
	 *
	 * @param array $sections
	 *
	 * @return array
	 */
	public function addSettingsSection( $sections ) {
		$sections[35] = array(
			'id'    => self::SECTION_ID,
			'title' => __( 'Troubleshooting', 'ajax-search-for-woocommerce' ) . '<span class="js-dgwt-wcas-troubleshooting-count dgwt-wcas-tab-mark"></span>'
		);

		return $sections;
	}

	/**
	 * AJAX callback for running async test
	 */
	public function asyncTest() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_die( - 1, 403 );
		}

		check_ajax_referer( self::ASYNC_TEST_NONCE );

		$test = isset( $_POST['test'] ) ? wc_clean( wp_unslash( $_POST['test'] ) ) : '';

		if ( ! $this->isTestExists( $test ) ) {
			wp_send_json_error();
		}

		$testFunction = sprintf(
			'getTest%s',
			$test
		);

		if ( method_exists( $this, $testFunction ) && is_callable( array( $this, $testFunction ) ) ) {
			$data = $this->performTest( array(
				$this,
				$testFunction
			) );
			wp_send_json_success( $data );
		}

		wp_send_json_error();
	}

	/**
	 * Reset stored results of async tests
	 */
	public function resetAsyncTests() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_die( - 1, 403 );
		}

		check_ajax_referer( self::RESET_ASYNC_TESTS_NONCE );

		delete_transient( self::TRANSIENT_RESULTS_KEY );

		wp_send_json_success();
	}

	/**
	 * Fix "Out of stock" relationships
	 */
	public function fixOutOfStock__premium_only() {
		global $wpdb;

		if ( ! current_user_can( 'administrator' ) ) {
			wp_die( - 1, 403 );
		}

		check_ajax_referer( self::FIX_OUTOFSTOCK_NONCE );

		delete_transient( self::TRANSIENT_RESULTS_KEY );

		$wpdb->hide_errors();
		$termID = wc_get_product_visibility_term_ids()['outofstock'];
		// STEP 1 | Wipe all current relationships related to outofstock
		$wpdb->delete( $wpdb->term_relationships,
			array(
				'term_taxonomy_id' => $termID
			),
			array(
				'%d'
			)
		);
		// STEP 2 | Get all products with stock status "outofstock"
		$ids = wc_get_products(
			array(
				'status'       => array( 'publish' ),
				'limit'        => - 1,
				'stock_status' => 'outofstock',
				'return'       => 'ids'
			)
		);
		// STEP 3 | Hide these products by adding correct term relationships
		if ( ! empty( $ids ) && is_array( $ids ) ) {
			$wpdb->query( 'START TRANSACTION' );
			for ( $i = 0; $i < count( $ids ); $i ++ ) {
				$wpdb->insert( $wpdb->term_relationships,
					array( 'object_id' => $ids[ $i ], 'term_taxonomy_id' => $termID, 'term_order' => 0 ),
					array( '%d', '%d', '%d' )
				);
				if ( $i % 100 == 0 ) {
					$wpdb->query( 'COMMIT' );
					$wpdb->query( 'START TRANSACTION' );
				}
			}
			$wpdb->query( 'COMMIT' );
		}

		wp_send_json_success();
	}

	/**
	 * Dismiss Elementor template error
	 */
	public function dismissElementorTemplate() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_die( - 1, 403 );
		}

		check_ajax_referer( self::DISMIS_ELEMENTOR_TEMPLATE_NONCE );

		update_option( 'dgwt_wcas_dismiss_elementor_template', '1' );

		wp_send_json_success();
	}

	/**
	 * Pass "troubleshooting" data to JavaScript on Settings page
	 *
	 * @param array $localize
	 *
	 * @return array
	 */
	public function localizeSettings( $localize ) {
		$localize['troubleshooting'] = array(
			'nonce' => array(
				'troubleshooting_async_test'                 => wp_create_nonce( self::ASYNC_TEST_NONCE ),
				'troubleshooting_reset_async_tests'          => wp_create_nonce( self::RESET_ASYNC_TESTS_NONCE ),
				'troubleshooting_fix_outofstock'             => wp_create_nonce( self::FIX_OUTOFSTOCK_NONCE ),
				'troubleshooting_dismiss_elementor_template' => wp_create_nonce( self::DISMIS_ELEMENTOR_TEMPLATE_NONCE ),
			),
			'tests' => array(
				'direct'        => array(),
				'async'         => array(),
				'issues'        => array(
					'good'        => 0,
					'recommended' => 0,
					'critical'    => 0,
				),
				'results_async' => array(),
			)
		);

		set_transient( self::TRANSIENT_TEST_KEY, '1', HOUR_IN_SECONDS );
		set_transient( self::TRANSIENT_TEST_KEY2, '1', 1 );

		$asyncTestsResults = get_transient( self::TRANSIENT_RESULTS_KEY );
		if ( ! empty( $asyncTestsResults ) && is_array( $asyncTestsResults ) ) {
			$localize['troubleshooting']['tests']['results_async'] = array_values( $asyncTestsResults );
			foreach ( $asyncTestsResults as $result ) {
				$localize['troubleshooting']['tests']['issues'][ $result['status'] ] ++;
			}
		}

		$tests = Troubleshooting::getTests();

		if ( ! empty( $tests['direct'] ) && is_array( $tests['direct'] ) ) {
			foreach ( $tests['direct'] as $test ) {
				if ( is_string( $test['test'] ) ) {
					$testFunction = sprintf(
						'getTest%s',
						$test['test']
					);

					if ( method_exists( $this, $testFunction ) && is_callable( array( $this, $testFunction ) ) ) {
						$localize['troubleshooting']['tests']['direct'][] = $this->performTest( array(
							$this,
							$testFunction
						) );
						continue;
					}
				}

				if ( is_callable( $test['test'] ) ) {
					$localize['troubleshooting']['tests']['direct'][] = $this->performTest( $test['test'] );
				}
			}
		}

		if ( ! empty( $localize['troubleshooting']['tests']['direct'] ) && is_array( $localize['troubleshooting']['tests']['direct'] ) ) {
			foreach ( $localize['troubleshooting']['tests']['direct'] as $result ) {
				$localize['troubleshooting']['tests']['issues'][ $result['status'] ] ++;
			}
		}

		if ( ! empty( $tests['async'] ) && is_array( $tests['async'] ) ) {
			foreach ( $tests['async'] as $test ) {
				if ( is_string( $test['test'] ) ) {
					$localize['troubleshooting']['tests']['async'][] = array(
						'test'      => $test['test'],
						'completed' => isset( $asyncTestsResults[ $test['test'] ] ),
					);
				}
			}
		}

		return $localize;
	}

	/**
	 * Load content for "Troubleshooting" tab on Settings page
	 */
	public function tabContent() {
		require DGWT_WCAS_DIR . 'partials/admin/troubleshooting.php';
	}

	/**
	 * Test for incompatible plugins
	 *
	 * @return array The test result.
	 */
	public function getTestIncompatiblePlugins() {
		$result = array(
			'label'       => __( 'You are using one or more incompatible plugins', 'ajax-search-for-woocommerce' ),
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'IncompatiblePlugins',
		);

		$errors = array();

		// GTranslate
		if ( class_exists( 'GTranslate' ) ) {
			$errors[] = sprintf( __( 'You use the %s plugin. The %s does not support this plugin.', 'ajax-search-for-woocommerce' ), 'GTranslate', DGWT_WCAS_NAME );
		}
		// WooCommerce Product Sort and Display
		if ( defined( 'WC_PSAD_VERSION' ) ) {
			$errors[] = sprintf( __( 'You use the %s plugin. The %s does not support this plugin.', 'ajax-search-for-woocommerce' ), 'WooCommerce Product Sort and Display', DGWT_WCAS_NAME );
		}

		if ( dgoraAsfwFs()->is__premium_only() ) {
			if ( defined( 'YITH_WCAS_PREMIUM' ) && defined( 'YITH_WCAS_VERSION' ) ) {
				$errors[] = sprintf( __( 'You use the %s plugin, which may cause errors in the search results returned by our plugin.', 'ajax-search-for-woocommerce' ), 'YITH WooCommerce Ajax Search Premium' );
			} elseif ( defined( 'YITH_WCAS_VERSION' ) ) {
				$errors[] = sprintf( __( 'You use the %s plugin, which may cause errors in the search results returned by our plugin.', 'ajax-search-for-woocommerce' ), 'YITH WooCommerce Ajax Search' );
			}
		}

		if ( ! empty( $errors ) ) {
			$result['description'] = join( '<br>', $errors );
			$result['status']      = 'critical';
		}

		return $result;
	}

	/**
	 * Test for incompatible plugins
	 *
	 * @return array The test result.
	 */
	public function getTestTranslatePress() {
		$result = array(
			'label'       => __( 'You are using TranslatePress with Free version of our plugin', 'ajax-search-for-woocommerce' ),
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'TranslatePress',
		);

		if ( ! defined( 'TRP_PLUGIN_VERSION' ) && ! class_exists( 'TRP_Translate_Press' ) ) {
			return $result;
		}

		$result['description'] = sprintf( __( 'Due to the way the TranslatePress - Multilingual plugin works, we can only provide support for it in the <a href="%s" target="_blank">Pro version</a>.', 'ajax-search-for-woocommerce' ), Upgrade::getUpgradeUrl() );
		$result['status']      = 'critical';

		return $result;
	}

	/**
	 * Test if loopbacks work as expected
	 *
	 * @return array The test result.
	 */
	public function getTestLoopbackRequests() {
		$result = array(
			'label'       => __( 'Your site can perform loopback requests', 'ajax-search-for-woocommerce' ),
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'LoopbackRequests',
		);

		$cookies = array();
		$timeout = 10;
		$headers = array(
			'Cache-Control' => 'no-cache',
		);
		/** This filter is documented in wp-includes/class-wp-http-streams.php */
		$sslverify = apply_filters( 'https_local_ssl_verify', false );

		$authorization = Helpers::getBasicAuthHeader();
		if ( $authorization ) {
			$headers['Authorization'] = $authorization;
		}

		$url = home_url();

		$r = wp_remote_get( $url, compact( 'cookies', 'headers', 'timeout', 'sslverify' ) );

		$markAsCritical = is_wp_error( $r ) || wp_remote_retrieve_response_code( $r ) !== 200;

		// Exclude timeout error
		if (
			is_wp_error( $r )
			&& $r->get_error_code() === 'http_request_failed'
			&& strpos( strtolower( $r->get_error_message() ), 'curl error 28:' ) !== false
		) {
			$markAsCritical = false;
		}

		// Skipping notice error if the search index is correct and the search endpoint responds
		if ( dgoraAsfwFs()->is__premium_only() ) {
			$indexCompletenessResult = $this->getTestIndexCompleteness__premium_only();
			$pingEndpointResult      = $this->getTestPingEndpoint__premium_only();
			if ( $indexCompletenessResult['status'] === 'good' && $pingEndpointResult['status'] === 'good' ) {
				$markAsCritical = false;
			}
		}

		if ( $markAsCritical ) {
			$result['status'] = 'critical';
			$linkToDocs       = 'https://fibosearch.com/documentation/troubleshooting/the-search-index-could-not-be-built/';
			$linkToWpHealth   = admin_url( 'site-health.php' );

			$result['label'] = __( 'Your site could not complete a loopback request', 'ajax-search-for-woocommerce' );


			if ( ! dgoraAsfwFs()->is_premium() ) {
				$result['description'] = __( 'This issue may affect the search results page and e.g. display all products every time', 'ajax-search-for-woocommerce' );
			}

			if ( dgoraAsfwFs()->is__premium_only() ) {
				$result['description'] = __( 'This issue may affect the building of the search index. Indexer may stuck at 0%.', 'ajax-search-for-woocommerce' );
				$result['description'] .= '<p>' . __( 'The Indexer uses the WordPress function <code>wp_remote_post()</code> to build the index in background. Sometimes the server can block this kind of request and responses with HTTP 401 Unauthorized or 403 Forbidden errors.',
						'ajax-search-for-woocommerce' ) . '</p>';
			}


			$result['description'] .= '<h3 class="dgwt-wcas-font-thin">' . __( 'Solutions:', 'ajax-search-for-woocommerce' ) . '</h3>';

			$result['description'] .= '<h4>' . __( "Your server can't send an HTTP request to itself", 'ajax-search-for-woocommerce' ) . '</h4>';

			$result['description'] .= '<p>' . sprintf( __( 'Go to <a href="%s" target="_blank">Tools -> Site Health</a> in your WordPress. You should see issues related to REST API or Loopback request. Expand descriptions of these errors and follow the instructions. Probably you will need to contact your hosting provider to solve it.',
					'ajax-search-for-woocommerce' ), $linkToWpHealth ) . '</p>';

			$result['description'] .= '<p>' . __( 'Is your website publicly available only for whitelisted IPs? <b>Add your server IP to the whitelist</b>. That’s all. This is a common mistake when access is blocked by a <code>.htaccess</code> file. Developers add a list of allowed IPs, but they forget to add the IP of the server to allow make HTTP requests to itself.',
					'ajax-search-for-woocommerce' ) . '</p>';

			if ( dgoraAsfwFs()->is__premium_only() ) {
				$result['description'] .= '<hr />';
				$result['description'] .= '<p>' . sprintf( __( 'Read more about indexer issues on <a target="_blank" href="%s">our documentation</a> and read the section “Your server can’t send an HTTP request to itself”.', 'ajax-search-for-woocommerce' ), $linkToDocs ) . '</p>';
			}
		}

		$this->storeResult( $result );

		return $result;
	}

	/**
	 * Test for required PHP extensions
	 *
	 * @return array The test result.
	 */
	public function getTestPHPExtensions() {
		$result = array(
			'label'       => __( 'One or more required PHP extensions are missing on your server', 'ajax-search-for-woocommerce' ),
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'PHPExtensions',
		);

		$errors = array();

		if ( ! extension_loaded( 'mbstring' ) ) {
			$errors[] = sprintf( __( 'Required PHP extension: %s', 'ajax-search-for-woocommerce' ), 'mbstring' );
		}
		if ( ! extension_loaded( 'pdo_mysql' ) ) {
			$errors[] = sprintf( __( 'Required PHP extension: %s', 'ajax-search-for-woocommerce' ), 'pdo_mysql' );
		}
		if ( ! empty( $errors ) ) {
			$result['description'] = join( '<br>', $errors );
			$result['status']      = 'critical';
		}

		return $result;
	}

	/**
	 * Tests for WordPress version and outputs it.
	 *
	 * @return array The test result.
	 */
	public function getTestWordPressVersion() {
		$result = array(
			'label'       => __( 'WordPress version', 'ajax-search-for-woocommerce' ),
			'status'      => '',
			'description' => '',
			'actions'     => '',
			'test'        => 'WordPressVersion',
		);

		$coreCurrentVersion = get_bloginfo( 'version' );
		if ( version_compare( $coreCurrentVersion, '5.2.0' ) >= 0 ) {
			$result['description'] = __( 'Great! Our plugin works great with this version of WordPress.', 'ajax-search-for-woocommerce' );
			$result['status']      = 'good';
		} else {
			$result['description'] = __( 'Install the latest version of WordPress for our plugin to work as best it can!', 'ajax-search-for-woocommerce' );
			$result['status']      = 'critical';
		}

		return $result;
	}

	/**
	 * Tests for required "Add to cart" behaviour in WooCommerce settings
	 * If the search Details Panel is enabled, WooCommerce "Add to cart" behaviour should be enabled.
	 *
	 * @return array The test result.
	 */
	public function getTestAjaxAddToCart() {

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'AjaxAddToCart',
		);

		if (
			'on' === DGWT_WCAS()->settings->getOption( 'show_details_box' )
			&& (
				'yes' !== get_option( 'woocommerce_enable_ajax_add_to_cart' )
				|| 'yes' === get_option( 'woocommerce_cart_redirect_after_add' )
			)
		) {
			$redirectLabel = __( 'Redirect to the cart page after successful addition', 'woocommerce' );
			$ajaxAtcLabel  = __( 'Enable AJAX add to cart buttons on archives', 'woocommerce' );
			$settingsUrl   = admin_url( 'admin.php?page=wc-settings&tab=products' );

			$result['label']       = __( 'Incorrect "Add to cart" behaviour in WooCommerce settings', 'ajax-search-for-woocommerce' );
			$result['description'] = '<p><b>' . __( 'Solution', 'ajax-search-for-woocommerce' ) . '</b></p>';
			$result['description'] .= '<p>' . sprintf( __( 'Go to <code>WooCommerce -> Settings -> <a href="%s" target="_blank">Products (tab)</a></code> and check option <code>%s</code> and uncheck option <code>%s</code>.', 'ajax-search-for-woocommerce' ), $settingsUrl, $ajaxAtcLabel, $redirectLabel ) . '</p>';
			$result['description'] .= __( 'Your settings should looks like the picture below:', 'ajax-search-for-woocommerce' );
			$result['description'] .= '<p><img style="max-width: 720px" src="' . DGWT_WCAS_URL . 'assets/img/admin-troubleshooting-atc.png" /></p>';
			$result['status']      = 'critical';
		}

		return $result;
	}

	/**
	 * Tests if "Searching by Text" extension from WOOF - WooCommerce Products Filter is enabled.
	 * It's incompatible with our plugin and should be disabled.
	 *
	 * @return array The test result.
	 */
	public function getTestWoofSearchTextExtension() {

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'WoofSearchTextExtension',
		);
		if ( ! defined( 'WOOF_VERSION' ) || ! isset( $GLOBALS['WOOF'] ) ) {
			return $result;
		}
		if ( ! method_exists( 'WOOF_EXT', 'is_ext_activated' ) ) {
			return $result;
		}

		$extDirs = $GLOBALS['WOOF']->get_ext_directories();
		if ( empty( $extDirs['default'] ) ) {
			return $result;
		}

		$extPaths = array_filter( $extDirs['default'], function ( $path ) {
			return ( strpos( $path, 'ext/by_text' ) !== false );
		} );
		if ( empty( $extPaths ) ) {
			return $result;
		}

		$extPath = array_shift( $extPaths );

		if ( \WOOF_EXT::is_ext_activated( $extPath ) ) {
			$settingsUrl = admin_url( 'admin.php?page=wc-settings&tab=woof' );

			$result['label']       = __( 'Incompatible "Searching by Text" extension from WOOF - WooCommerce Products Filter plugin is active', 'ajax-search-for-woocommerce' );
			$result['description'] = '<p><b>' . __( 'Solution', 'ajax-search-for-woocommerce' ) . '</b></p>';
			$result['description'] .= '<p>' . sprintf( __( 'Go to <code>WooCommerce -> Settings -> <a href="%s" target="_blank">Products Filter (tab)</a> -> Extensions (tab)</code>, uncheck <code>Searching by Text</code> extension and save changes.', 'ajax-search-for-woocommerce' ), $settingsUrl ) . '</p>';
			$result['description'] .= __( 'Extensions should looks like the picture below:', 'ajax-search-for-woocommerce' );
			$result['description'] .= '<p><img style="max-width: 720px" src="' . DGWT_WCAS_URL . 'assets/img/admin-troubleshooting-woof.png" /></p>';
			$result['status']      = 'critical';
		}

		return $result;
	}

	/**
	 * Test if Elementor has defined correct template for search results
	 *
	 * @return array The test result.
	 */
	public function getTestElementorSearchResultsTemplate() {
		global $wp_query;

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'ElementorSearchTemplate',
		);

		if ( get_option( 'dgwt_wcas_dismiss_elementor_template' ) === '1' ) {
			return $result;
		}
		if ( ! defined( 'ELEMENTOR_VERSION' ) || ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			return $result;
		}
		if ( version_compare( ELEMENTOR_VERSION, '2.9.0' ) < 0 || version_compare( ELEMENTOR_PRO_VERSION, '2.10.0' ) < 0 ) {
			return $result;
		}

		$conditionsManager = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' )->get_conditions_manager();

		// Prepare $wp_query so that the conditions for checking if there is a search page are true.
		$wp_query->is_search            = true;
		$wp_query->is_post_type_archive = true;
		set_query_var( 'post_type', 'product' );

		$documents = $conditionsManager->get_documents_for_location( 'archive' );

		// Reset $wp_query
		$wp_query->is_search            = false;
		$wp_query->is_post_type_archive = false;
		set_query_var( 'post_type', '' );

		// Stop checking - a template from a theme or WooCommerce will be used
		if ( empty( $documents ) ) {
			return $result;
		}

		/**
		 * @var \ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document $document
		 */
		$document = current( $documents );

		if ( ! $this->doesElementorElementsContainsWidget( $document->get_elements_data(), 'wc-archive-products' ) ) {
			$linkToDocs    = 'https://fibosearch.com/documentation/troubleshooting/the-search-results-page-created-in-elementor-doesnt-display-products/';
			$dismissButton = get_submit_button( __( 'Dismiss', 'ajax-search-for-woocommerce' ), 'secondary', 'dgwt-wcas-dismiss-elementor-template', false );

			$templateLink = '<a target="_blank" href="' . admin_url( 'post.php?post=' . $document->get_post()->ID . '&action=elementor' ) . '">' . $document->get_post()->post_title . '</a>';

			$result['label']       = __( 'There is no correct template in Elementor Theme Builder for the WooCommerce search results page.', 'ajax-search-for-woocommerce' );
			$result['description'] = '<p>' . sprintf( __( 'You are using Elementor and we noticed that the template used in the search results page titled <strong>%s</strong> does not include the <strong>Archive Products</strong> widget.', 'ajax-search-for-woocommerce' ), $templateLink ) . '</p>';
			$result['description'] .= '<p><b>' . __( 'Solution', 'ajax-search-for-woocommerce' ) . '</b></p>';
			$result['description'] .= '<p>' . sprintf( __( 'Add <strong>Archive Products</strong> widget to the template <strong>%s</strong> or create a new template dedicated to the WooCommerce search results page. Learn how to do it in <a href="%s" target="_blank">our documentation</a>.', 'ajax-search-for-woocommerce' ), $templateLink, $linkToDocs ) . '</p>';
			$result['description'] .= '<br/><hr/><br/>';
			$result['description'] .= '<p>' . sprintf( __( 'If you think the search results page is displaying your products correctly, you can ignore and dismiss this message: %s', 'ajax-search-for-woocommerce' ), $dismissButton ) . '</p>';
			$result['status']      = 'critical';

			return $result;
		}

		return $result;
	}


	/**
	 * [iThemes Security] Check option Disabled PHP in Plugins. It should be unchecked.
	 * Otherwise AJAX endpoint will be blocked by .htaccess
	 *
	 * @return array The test result.
	 */
	public function getTestIthemesSecurityPhpInPlugins__premium_only() {

		$pass = true;

		if ( class_exists( 'ITSEC_Modules' ) ) {

			$input = \ITSEC_Modules::get_settings( 'system-tweaks' );

			if ( ! empty( $input['plugins_php'] ) ) {
				$pass = false;
			}
		}

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'SecurityPhpInPlugins',
		);

		if ( ! $pass ) {

			$tweaksUrl = admin_url( 'admin.php?page=itsec&module=system-tweaks&module_type=recommended' );

			$result['label']       = __( 'iThemes Security plugin blocks AJAX requests', 'ajax-search-for-woocommerce' );
			$result['description'] = '<p><b>' . __( 'Solution', 'ajax-search-for-woocommerce' ) . '</b></p>';
			$result['description'] .= '<p>' . sprintf( __( 'Go to <code>Security -> Settings -> <a href="%s" target="_blank">System Tweaks</a></code> and uncheck <code>Disable PHP in Plugins</code> option.', 'ajax-search-for-woocommerce' ), $tweaksUrl ) . '</p>';
			$result['description'] .= '<p><img style="max-width: 720px" src="' . DGWT_WCAS_URL . 'assets/img/admin-troubleshooting-ithemes.png" /></p>';
			$result['status']      = 'critical';
		}

		return $result;
	}

	/**
	 * Test if WordPress loads in SHORTINIT as expected
	 *
	 * @return array The test result.
	 */
	public function getTestWordPressLoad__premium_only() {
		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'WordPressLoad__premium_only',
		);

		$wpLoadExists = false;
		$wpLoad       = '../../wp-load.php';
		$maxDepth     = 8;

		while ( $maxDepth > 0 ) {
			if ( file_exists( __DIR__ . DIRECTORY_SEPARATOR . $wpLoad ) ) {
				$wpLoadExists = true;
				break;
			} else {

				$alternativePaths = array(
					'wp', // Support for Bedrock by Roots - https://roots.io/bedrock
					'.wordpress', // Support for Flywheel hosting - https://getflywheel.com
					'cms' // Support for Themosis Framefork - https://framework.themosis.com
				);

				foreach ( $alternativePaths as $alternativePath ) {

					$bedrockAbsPath = str_replace( 'wp-load.php', $alternativePath . '/wp-load.php', $wpLoad );

					if ( file_exists( __DIR__ . DIRECTORY_SEPARATOR . $bedrockAbsPath ) ) {
						$wpLoadExists = true;
						break;
					}

				}

			}

			$wpLoad = '../' . $wpLoad;
			$maxDepth --;
		}

		// Support for Bitnami WordPress With NGINX And SSL - https://docs.bitnami.com/aws/apps/wordpress-pro/
		if ( ! $wpLoadExists ) {
			$wpLoad = '/opt/bitnami/wordpress/wp-load.php';
			if ( file_exists( $wpLoad ) ) {
				$wpLoadExists = true;
			}
		}

		if ( ! $wpLoadExists ) {
			$result['label']       = __( 'Custom location of wp-load.php file', 'ajax-search-for-woocommerce' );
			$result['description'] = sprintf( __( 'Could not load <code>wp-load.php</code> from the locations it normally is. To solve this issue, contact the <a target="_blank" href="%s">technical support</a>.', 'ajax-search-for-woocommerce' ), dgoraAsfwFs()->contact_url() );
			$result['status']      = 'critical';
		}

		return $result;
	}

	/**
	 * Test if scheduled events are delayed
	 *
	 * @return array The test result.
	 */
	public function getTestScheduledEvents__premium_only() {
		$result = array(
			'label'       => __( 'Issue with WP-Cron', 'ajax-search-for-woocommerce' ),
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'ScheduledEvents__premium_only',
		);

		// Break early if the index is properly built or is being built
		if ( Builder::getInfo( 'status' ) !== 'error' ) {
			return $result;
		}

		if ( $this->hasWpCronMissedEvents() ) {
			$tasks = '<ol><li>wcas_build_readable_index_cron</li><li>wcas_build_searchable_index_cron</li>';
			if ( Builder::canBuildTaxonomyIndex() ) {
				$tasks .= '<li>wcas_build_taxonomy_index_cron</li>';
			}
			if ( Builder::canBuildVariationsIndex() ) {
				$tasks .= '<li>wcas_build_variation_index_cron</li>';
			}
			$tasks                 .= '</ol>';
			$result['status']      = 'critical';
			$result['description'] = '<p><b>' . __( 'Solution', 'ajax-search-for-woocommerce' ) . '</b></p>';
			$result['description'] .= '<p>' . __( 'Install the <a target="_blank" href="https://wordpress.org/plugins/advanced-cron-manager/">Advanced Cron Manager</a> plugin to verify if WP-Cron works correctly. You should see some of the actions related to the Indexer:', 'ajax-search-for-woocommerce' ) . '</p>';
			$result['description'] .= $tasks;
			$result['description'] .= '<p>' . __( 'You can run these actions manually via Advanced Cron Manager. Then the Indexer should run. If the index stuck again, run these actions manually one more time until the finished index.', 'ajax-search-for-woocommerce' ) . '</p>';
			$result['description'] .= '<p>' . __( 'A real solution is to find the reason why the WP-Cron doesn’t work and fix it.', 'ajax-search-for-woocommerce' ) . '</p>';
		}

		return $result;
	}

	/**
	 * Test if the search index structure is completely
	 *
	 * @return array The test result.
	 */
	public function getTestIndexCompleteness__premium_only() {

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'IndexCompleteness__premium_only',
		);

		// Break early if there is problem with DB connection
		$pdoTestResult = $this->getTestPDOConnection__premium_only();
		if ( $pdoTestResult['status'] !== 'good' ) {
			return $result;
		}
		// Break early if the index isn't completed
		if ( 'completed' !== Builder::getInfo( 'status' ) ) {
			return $result;
		}

		if ( ! Builder::isIndexValid() ) {

			// Prevent trigger error when no products in DB
			$source   = new \DgoraWcas\Engines\TNTSearchMySQL\Indexer\SourceQuery( array( 'ids' => true ) );
			$products = $source->getData();
			if ( empty( $products ) ) {
				return $result;
			}

			$rebuildLabel = __( 'Rebuild index', 'ajax-search-for-woocommerce' );

			$result['status'] = 'critical';

			$result['label'] = __( "The search index structure isn't completely", 'ajax-search-for-woocommerce' );

			$result['description'] = '<p>' . sprintf( __( 'Go to the Indexer tab and click the button <i>%s</i>.', 'ajax-search-for-woocommerce' ), $rebuildLabel ) . '</p>';
		}

		return $result;
	}

	/**
	 * Test if PDO can connect to DB
	 *
	 * @return array The test result.
	 */
	public function getTestPDOConnection__premium_only() {

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'PDOConnection__premium_only',
		);

		// Break early if pdo_mysql extension is no loaded
		$phpExtensionsResult = $this->getTestPHPExtensions();
		if ( $phpExtensionsResult['status'] !== 'good' && strpos( $phpExtensionsResult['description'], 'pdo_mysql' ) !== false ) {
			return $result;
		}

		$dbConfig = \DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Database::getConfig();
		$pdo      = new \DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Connectors\MySqlConnector();
		try {
			$pdo->connect( $dbConfig );
		} catch ( \PDOException $exception ) {
			$result['status']      = 'critical';
			$result['label']       = __( 'Error establishing a database connection', 'ajax-search-for-woocommerce' );
			$result['description'] .= '<p>' . sprintf( __( 'An error occurred while trying to connect to the database using a PDO_MYSQL driver: <code>%s</code>.', 'ajax-search-for-woocommerce' ), $exception->getMessage() ) . '</p>';
			$result['description'] .= '<p>' . __( 'FiboSearch uses a PDO_MYSQL driver in the search engine. A proper database connection is required.', 'ajax-search-for-woocommerce' ) . '</p>';
			/**
			 * "localhost" will cause the MySQL client to try a UNIX socket in a standard directory.
			 * If that doesn't exist or is somewhere else, you won't be able to connect. 127.0.0.1 always uses a TCP connection.
			 */
			if ( $dbConfig['host'] === 'localhost' ) {
				$result['description'] .= '<p><b>' . __( 'Solution', 'ajax-search-for-woocommerce' ) . '</b></p>';
				$result['description'] .= '<p>' . __( 'Edit <code>wp-config.php</code> file, find the <code>DB_HOST</code> constant, and change its value from <code>localhost</code> to <code>127.0.0.1</code>.', 'ajax-search-for-woocommerce' ) . '</p>';
			}
		}

		return $result;
	}

	/**
	 * Test if Search module from Jetpack is active
	 *
	 * @return array The test result.
	 */
	public function getTestJetpackSearchModule__premium_only() {

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'JetpackSearchModule__premium_only',
		);

		if ( ! class_exists( '\Jetpack' ) ) {
			return $result;
		}
		if ( ! method_exists( '\Jetpack', 'is_module_active' ) ) {
			return $result;
		}

		if ( \Jetpack::is_module_active( 'search' ) ) {
			$jetpackSettingsUrl    = admin_url( 'admin.php?page=jetpack#/performance' );
			$result['status']      = 'critical';
			$result['label']       = sprintf( __( 'The Jetpack Search module is incompatible with the %s plugin.', 'ajax-search-for-woocommerce' ), DGWT_WCAS_NAME );
			$result['description'] = '<p>' . sprintf( __( 'Go to the <code>Jetpack settings page -> <a href="%s" target="_blank">Performance tab</a> -> disable the Search module</code>', 'ajax-search-for-woocommerce' ), $jetpackSettingsUrl ) . '</p>';
		}

		return $result;
	}

	/**
	 * Test if WooCommerce Multilingual is active
	 *
	 * @return array The test result.
	 */
	public function getTestWooCommerceMultilingual__premium_only() {
		/**
		 * @var \woocommerce_wpml
		 */
		global $woocommerce_wpml;

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'WooCommerceMultilingual__premium_only',
		);

		if ( ! Multilingual::isMultilingual() || ! Multilingual::isWPML() ) {
			return $result;
		}

		$woocommerceWpmlNotReady = isset( $woocommerce_wpml->dependencies_are_ok ) ? $woocommerce_wpml->dependencies_are_ok : true;

		if ( ! defined( 'WCML_VERSION' ) ) {
			$woocommerceWpmlUrl    = 'https://wordpress.org/plugins/woocommerce-multilingual/';
			$result['status']      = 'critical';
			$result['label']       = __( 'Missing plugin: WooCommerce Multilingual', 'ajax-search-for-woocommerce' );
			$result['description'] = '<p>' . sprintf( __( 'You use the WPML Multilingual CMS plugin and to correctly search for products in multiple languages, you must also install the <a href="%s" target="_blank">WooCommerce Multilingual</a> plugin.', 'ajax-search-for-woocommerce' ), $woocommerceWpmlUrl ) . '</p>';
		} elseif ( ! $woocommerceWpmlNotReady ) {
			$woocommerceWpmlAdminUrl = admin_url( 'admin.php?page=wpml-wcml' );
			$result['status']        = 'critical';
			$result['label']         = __( 'WooCommerce Multilingual plugin is enabled but not effective', 'ajax-search-for-woocommerce' );
			$result['description']   = '<p>' . sprintf( __( 'You use the WPML Multilingual CMS and WooCommerce Multilingual plugins, but the latter requires action to be fully functional. Check <a href="%s" target="_blank">WooCommerce Multilingual status</a>.', 'ajax-search-for-woocommerce' ),
					$woocommerceWpmlAdminUrl ) . '</p>';
		}

		return $result;
	}

	/**
	 * Test if WPML is active but with disabled translations for products
	 *
	 * @return array The test result.
	 */
	public function getTestWpmlDisabledTranslations__premium_only() {
		/**
		 * @var \woocommerce_wpml
		 */
		global $woocommerce_wpml;

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'WpmlDisabledTranslations__premium_only',
		);

		if ( ! Multilingual::isMultilingual() || ! Multilingual::isWPML() ) {
			return $result;
		}

		// Break early if "Post Types Translation" is not "Not translatable"
		if ( (int) apply_filters( 'wpml_sub_setting', false, 'custom_posts_sync_option', 'product' ) !== 0 ) {
			return $result;
		}

		$rebuildLabel = __( 'Rebuild index', 'ajax-search-for-woocommerce' );

		$result['status']      = 'critical';
		$result['label']       = __( 'Incompatible WPML Multilingual CMS plugin setting', 'ajax-search-for-woocommerce' );
		$result['description'] = '<p>' . __( 'You are using WPML Multilingual CMS, but you have product translations disabled, so for the search engine to function properly, you must disable its multi-language support feature.', 'ajax-search-for-woocommerce' ) . '</p>';
		$result['description'] .= '<p><b>' . __( 'Solution', 'ajax-search-for-woocommerce' ) . '</b></p>';
		$result['description'] .= '<ol><li>' . sprintf( __( 'Add a constant <code>%s</code> to your <code>wp-config.php</code> file.', 'ajax-search-for-woocommerce' ), "define('DGWT_WCAS_DISABLE_MULTILINGUAL', true);" ) . '</li>';
		$result['description'] .= '<li>' . sprintf( __( 'Go to the Indexer tab and click the button <i>%s</i>.', 'ajax-search-for-woocommerce' ), $rebuildLabel ) . '</li></ol>';

		return $result;
	}

	/**
	 * Test for non-critical errors while building the index
	 *
	 * @return array The test result.
	 */
	public function getTestNonCriticalIndexerErrors__premium_only() {
		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'NonCriticalIndexerErrors__premium_only',
		);

		// Break early if the index isn't completed
		if ( Builder::getInfo( 'status' ) !== 'completed' ) {
			return $result;
		}

		$errors = Builder::getInfo( 'non_critical_errors' );
		if ( empty( $errors ) ) {
			return $result;
		}

		$result['status']      = 'critical';
		$result['label']       = __( 'The search index was built, but some significant errors occurred during this process. There is a risk that some products may not be available during the search.', 'ajax-search-for-woocommerce' );
		$result['description'] .= '<p>' . sprintf( __( 'If the following errors are related to your theme or plugins, try to fix them yourself or contact your developers. Otherwise, please <a href="%s" target="_blank">create a support ticket</a>.', 'ajax-search-for-woocommerce' ), dgoraAsfwFs()->contact_url() ) . '</p>';
		foreach ( $errors as $errorType => $error ) {
			$result['description'] .= '<p class="dgwt-wcas-error-log">' . $error . '</p>';
		}

		return $result;
	}

	/**
	 * Test for slugs if site is multilingual
	 *
	 * @return array The test result.
	 */
	public function getTestMultilingualSlugs__premium_only() {
		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'MultilingualSlugs__premium_only',
		);

		if ( defined( 'DGWT_WCAS_DISABLE_MULTILINGUAL' ) && DGWT_WCAS_DISABLE_MULTILINGUAL ) {
			return $result;
		}
		if ( Multilingual::getProvider() === 'not set' ) {
			return $result;
		}

		$langs = Multilingual::getLanguages( true );
		if ( count( $langs ) === 1 ) {
			return $result;
		}

		$invalidCodes = array();
		foreach ( $langs as $lang ) {
			if ( ! Multilingual::isLangCode( $lang ) ) {
				$invalidCodes[] = $lang;
			}
		}

		if ( empty( $invalidCodes ) ) {
			return $result;
		}

		$rebuildLabel = __( 'Rebuild index', 'ajax-search-for-woocommerce' );

		$result['status']      = 'critical';
		$result['label']       = __( 'Incompatible multilingual plugin setting', 'ajax-search-for-woocommerce' );
		$result['description'] = '<p>' . __( 'You are using the multilingual plugin, but one or more of the language codes has wrong format. This needs to be corrected or the index will not be able to build properly.', 'ajax-search-for-woocommerce' ) . '</p>';
		$result['description'] .= '<p>' . sprintf( _n( 'Invalid language code: <code>%s</code>', 'Invalid language codes: <code>%s</code>', count( $invalidCodes ), 'ajax-search-for-woocommerce' ), join( ', ', $invalidCodes ) ) . '</p>';
		$result['description'] .= '<p><b>' . __( 'Solution', 'ajax-search-for-woocommerce' ) . '</b></p>';
		$result['description'] .= '<ol><li>' . __( 'Go to the multilingual plugin settings page and find the section where you can edit the available languages.', 'ajax-search-for-woocommerce' ) . '</li>';
		$result['description'] .= '<li>' . __( 'Change the above-mentioned language codes to the correct format: <code>xx</code>, <code>xxx</code>, <code>xx-xx</code> or <code>xx-xxxx</code>.', 'ajax-search-for-woocommerce' ) . '</li>';
		$result['description'] .= '<li>' . sprintf( __( 'Go to the Indexer tab and click the button <i>%s</i>.', 'ajax-search-for-woocommerce' ), $rebuildLabel ) . '</li></ol>';

		return $result;
	}

	/**
	 * Test if the index was built by the current plugin version
	 *
	 * @return array The test result.
	 */
	public function getTestOldIndex__premium_only() {
		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'OldIndex__premium_only',
		);

		// Break early if the index isn't completed
		if ( Builder::getInfo( 'status' ) !== 'completed' ) {
			return $result;
		}

		$version = Builder::getInfo( 'plugin_version' );
		if ( $version === DGWT_WCAS_VERSION ) {
			return $result;
		}

		$rebuildLabel = __( 'Rebuild index', 'ajax-search-for-woocommerce' );

		$result['status']      = 'critical';
		$result['label']       = __( "The index was built by the previous plugin version", 'ajax-search-for-woocommerce' );
		$result['description'] = '<p>' . sprintf( __( 'Go to the Indexer tab and click the button <i>%s</i>.', 'ajax-search-for-woocommerce' ), $rebuildLabel ) . '</p>';

		return $result;
	}

	/**
	 * Test if the MySQL server has support to InnoDB engine
	 *
	 * @return array The test result.
	 */
	public function getTestInnoDbSupport__premium_only() {
		global $wpdb;

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'InnoDbSupport__premium_only',
		);

		$hasInnoDbSupport = $wpdb->get_var( "SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE = 'InnoDB'" );
		if ( empty( $hasInnoDbSupport ) || is_wp_error( $hasInnoDbSupport ) ) {
			return $result;
		}

		if ( strtolower( $hasInnoDbSupport ) === 'no' ) {
			$result['status']      = 'critical';
			$result['label']       = __( "The MySQL server has the InnoDB engine turned off", 'ajax-search-for-woocommerce' );
			$result['description'] = '<p>' . __( 'The InnoDB engine within the MySQL server must be enabled for our plugin to work properly. Contact your hosting provider and ask for enabling InnoDB engine in your MySQL server.', 'ajax-search-for-woocommerce' ) . '</p>';
		}

		return $result;
	}

	/**
	 * Test "Out of stock" relationships
	 *
	 * @return array The test result.
	 */
	public function getTestOutOfStockRelationships__premium_only() {
		global $wpdb;

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'OutOfStockRelationships__premium_only',
		);

		// Break early if "Exclude “out of stock” products" option is disabled
		if ( DGWT_WCAS()->settings->getOption( 'exclude_out_of_stock' ) !== 'on' ) {
			return $result;
		}

		$outOfStockProductIds1 = wc_get_products(
			array(
				'status'       => array( 'publish' ),
				'limit'        => - 1,
				'stock_status' => 'outofstock',
				'return'       => 'ids'
			)
		);

		if ( empty( $outOfStockProductIds1 ) ) {
			return $result;
		}

		$visibilityTermIds = wc_get_product_visibility_term_ids();
		if ( empty( $visibilityTermIds['outofstock'] ) ) {
			return $result;
		}

		$outOfStockProductIds2 = $wpdb->get_col( $wpdb->prepare( "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $visibilityTermIds['outofstock'] ) );
		if ( ! empty( $outOfStockProductIds2 ) ) {
			$outOfStockProductIds2 = array_map( 'absint', $outOfStockProductIds2 );
		}

		$totalProducts = Helpers::getTotalProducts();

		if ( $totalProducts > 0 && is_array( $outOfStockProductIds1 ) && is_array( $outOfStockProductIds2 ) ) {
			$diff        = array_merge( array_diff( $outOfStockProductIds1, $outOfStockProductIds2 ), array_diff( $outOfStockProductIds2, $outOfStockProductIds1 ) );
			$diffPercent = ( count( $diff ) * 100 ) / $totalProducts;

			if ( $diffPercent > 2 ) {
				$fixItButton           = get_submit_button( __( 'Fix “Out of stock“ relationships', 'ajax-search-for-woocommerce' ), 'secondary', 'dgwt-wcas-fix-out-of-stock-relationships', false );
				$result['status']      = 'critical';
				$result['label']       = __( 'There is a problem with the visibility of products with “Out of stock“ status', 'ajax-search-for-woocommerce' );
				$result['description'] .= '<p>' . __( 'We\'ve detected that some products with a status “Out of Stock“ may have something wrong with relationships in the database. It affects visibility of products in the search engine. This error is often the result of migrating from a different WordPress.', 'ajax-search-for-woocommerce' ) . '</p>';
				$result['description'] .= '<p>' . sprintf( __( 'Total “out of stock“ products calculated by <code>wc_get_products()</code> function: <b>%d</b>', 'ajax-search-for-woocommerce' ), count( $outOfStockProductIds1 ) ) . '</p>';
				$result['description'] .= '<p>' . sprintf( __( 'Total “out of stock“ products calculated by SQL query on <code>%s</code>: <b>%d</b>', 'ajax-search-for-woocommerce' ), $wpdb->term_relationships, count( $outOfStockProductIds2 ) ) . '</p>';
				$result['description'] .= '<p>' . sprintf( __( 'You can fix it by clicking on this button: %s', 'ajax-search-for-woocommerce' ), $fixItButton ) . '<span class="dgwt-wcas-ajax-loader"></span></p>';
			}
		}

		return $result;
	}

	/**
	 * Test if transients are working properly
	 *
	 * @return array The test result.
	 */
	public function getTestTransients__premium_only() {
		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'Transients__premium_only',
		);

		// Break early if the index is properly built or is being built
		if ( Builder::getInfo( 'status' ) !== 'error' ) {
			return $result;
		}

		sleep( 1 );
		// Long-life transient
		$r1 = get_transient( self::TRANSIENT_TEST_KEY );
		// Short-life transient
		$r2 = get_transient( self::TRANSIENT_TEST_KEY2 );

		if ( $r1 === '1' && $r2 === false ) {
			return $result;
		}

		$result['status'] = 'critical';
		$result['label']  = __( 'Problem with WordPress Transients API', 'ajax-search-for-woocommerce' );
		$errorType        = '';
		if ( $r1 !== '1' ) {
			$errorType = __( 'the transient value was not returned', 'ajax-search-for-woocommerce' );
		} else if ( $r2 !== false ) {
			$errorType = __( 'the transient value was cached and existed too long', 'ajax-search-for-woocommerce' );
		}
		$result['description'] .= '<p>' . sprintf( __( 'Error type: <strong>%s</strong>', 'ajax-search-for-woocommerce' ), $errorType ) . '</p>';
		$result['description'] .= '<p>' . __( 'If you have an object caching plugin, turn it off or check its settings. Ideally, the transients should not be cached in any way and stored directly in the database.', 'ajax-search-for-woocommerce' ) . '</p>';

		return $result;
	}

	/**
	 * Test if the search endpoint is blocked
	 *
	 * This test run few other tests in sequence
	 *
	 * @return array The test result.
	 */
	public function getTestBlockedSearchEndpoint__premium_only() {

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'BlockedSearchEndpoint__premium_only',
		);

		$loopbackResult = $this->getResult( 'LoopbackRequests' );
		// Mute error and break if no loopback test result
		if ( empty( $loopbackResult ) || ! isset( $loopbackResult['status'] ) ) {
			$this->storeResult( $result );

			return $result;
		}

		// Mute error and break if there is loopback error
		if ( $loopbackResult['status'] !== 'good' ) {
			$this->storeResult( $result );

			return $result;
		}

		$testResult = $this->getTestPingEndpoint__premium_only();
		// Break early if search endpoint can response
		if ( $testResult['status'] === 'good' ) {
			$this->storeResult( $result );

			return $result;
		} else {
			$result['label']       = $testResult['label'];
			$result['description'] = $testResult['description'];
			$result['status']      = $testResult['status'];
		}

		// We have a problem accessing the search endpoint and are looking for the cause

		$testResult = $this->getTestIthemesSecurityPhpInPlugins__premium_only();
		if ( $testResult['status'] === 'critical' ) {
			$result['label']       = $testResult['label'];
			$result['description'] = $testResult['description'];
			$result['status']      = $testResult['status'];
			$this->storeResult( $result );

			return $result;
		}

		$testResult = $this->getTestDefenderSecurity__premium_only();
		if ( $testResult['status'] === 'critical' ) {
			$result['label']       = $testResult['label'];
			$result['description'] = $testResult['description'];
			$result['status']      = $testResult['status'];
			$this->storeResult( $result );

			return $result;
		}

		$testResult = $this->getTestSucuriSecurity__premium_only();
		if ( $testResult['status'] === 'critical' ) {
			$result['label']       = $testResult['label'];
			$result['description'] = $testResult['description'];
			$result['status']      = $testResult['status'];
			$this->storeResult( $result );

			return $result;
		}

		$testResult = $this->getTestNginx__premium_only();
		if ( $testResult['status'] === 'critical' ) {
			$result['label']       = $testResult['label'];
			$result['description'] = $testResult['description'];
			$result['status']      = $testResult['status'];
			$this->storeResult( $result );

			return $result;
		}

		$testResult = $this->getTestHtaccessRulesBlockingPhp__premium_only();
		if ( $testResult['status'] === 'critical' ) {
			$result['label']       = $testResult['label'];
			$result['description'] = $testResult['description'];
			$result['status']      = $testResult['status'];
			$this->storeResult( $result );

			return $result;
		}

		$this->storeResult( $result );

		return $result;
	}

	/**
	 * Test if the search endpoint response to ping
	 *
	 * @return array The test result.
	 * @see getTestBlockedSearchEndpoint__premium_only
	 */
	public function getTestPingEndpoint__premium_only() {

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'PingEndpoint__premium_only',
		);

		$cookies       = array();
		$timeout       = 10;
		$headers       = array(
			'Cache-Control' => 'no-cache',
		);
		$authorization = Helpers::getBasicAuthHeader();
		if ( $authorization ) {
			$headers['Authorization'] = $authorization;
		}
		/** This filter is documented in wp-includes/class-wp-http-streams.php */
		$sslverify = apply_filters( 'https_local_ssl_verify', false );

		$url = DGWT_WCAS_URL . 'includes/Engines/TNTSearchMySQL/Endpoints/search.php?dgwt_wcas_ping=1';

		$r = wp_remote_get( $url, compact( 'cookies', 'headers', 'timeout', 'sslverify' ) );

		$rBody    = wp_remote_retrieve_body( $r );
		$rCode    = wp_remote_retrieve_response_code( $r );
		$rMessage = wp_remote_retrieve_response_message( $r );

		if ( $rBody === 'pong' ) {
			return $result;
		}

		$result['label']       = __( "The server returns an incorrect response for the search engine's AJAX calls", 'ajax-search-for-woocommerce' );
		$result['description'] .= '<p>' . sprintf( __( 'Server response with message <code>%s</code> and status code <code>%s</code>.', 'ajax-search-for-woocommerce' ), esc_html( $rMessage ), $rCode ) . '</p>';
		$result['description'] .= '<p><b>' . __( "What's wrong?", 'ajax-search-for-woocommerce' ) . '</b></p>';
		$result['description'] .= '<p>' . __( "The search uses a dedicated URL to makes queries. In your case, this URL is blocked for some reason. Let visit it directly in your browser:", 'ajax-search-for-woocommerce' ) . '</p>';
		$result['description'] .= '<p><a target="_blank" href="' . $url . '">' . $url . '</a></p>';
		$result['description'] .= '<p>' . __( 'You should see <code>pong</code> word as a response. Probably you see something else.', 'ajax-search-for-woocommerce' ) . '</p>';
		$result['description'] .= '<p><b>' . __( 'Solutions', 'ajax-search-for-woocommerce' ) . '</b></p>';
		$result['description'] .= '<ol><li>' . __( 'Think about what can block the execution of PHP scripts inside <code>wp-content</code> or <code>wp-content/plugins</code> directory. Maybe you use some security plugins or you have custom code that may block it.', 'ajax-search-for-woocommerce' ) . '</li>';
		$result['description'] .= '<li>' . __( 'Maybe your server blocks it by Apache module <code>mod_security</code>. Contact your hosting provider and ask what can block the URL you see above.', 'ajax-search-for-woocommerce' ) . '</li></ol>';
		$result['description'] .= $this->getDebugData();
		$result['status']      = 'critical';

		return $result;
	}

	/**
	 * Test if the search endpoint return valid response for phrase
	 *
	 * @return array The test result.
	 * @see getTestBlockedSearchEndpoint__premium_only
	 */
	public function getTestQuerySearchResults__premium_only() {

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'QuerySearchResults__premium_only',
		);

		$indexCompletenessResult = $this->getTestIndexCompleteness__premium_only();
		$pingEndpointResult      = $this->getTestPingEndpoint__premium_only();
		// Skip if related tests fails
		if ( $indexCompletenessResult['status'] !== 'good' || $pingEndpointResult['status'] !== 'good' ) {
			$this->storeResult( $result );

			return $result;
		}

		$cookies       = array();
		$timeout       = 10;
		$headers       = array(
			'Cache-Control' => 'no-cache',
		);
		$authorization = Helpers::getBasicAuthHeader();
		if ( $authorization ) {
			$headers['Authorization'] = $authorization;
		}
		/** This filter is documented in wp-includes/class-wp-http-streams.php */
		$sslverify = apply_filters( 'https_local_ssl_verify', false );

		$url = DGWT_WCAS_URL . 'includes/Engines/TNTSearchMySQL/Endpoints/search.php?s=test';

		if ( Multilingual::isMultilingual() ) {
			$url .= '&l=' . Multilingual::getDefaultLanguage();
		}

		$r = wp_remote_get( $url, compact( 'cookies', 'headers', 'timeout', 'sslverify' ) );

		$rBody    = wp_remote_retrieve_body( $r );
		$rCode    = wp_remote_retrieve_response_code( $r );
		$rMessage = wp_remote_retrieve_response_message( $r );

		// Test if response is in JSON format
		if ( $rCode === 200 && is_string( $rBody ) && is_array( json_decode( $rBody, true ) ) && ( json_last_error() === JSON_ERROR_NONE ) ) {
			$this->storeResult( $result );

			return $result;
		}

		// Skip when the server did not return a correct response
		if ( empty( $rCode ) ) {
			$this->storeResult( $result );

			return $result;
		}

		$result['label']       = __( "A search engine's AJAX call did not return valid results", 'ajax-search-for-woocommerce' );
		$result['description'] .= '<p>' . sprintf( __( 'Server response with message <code>%s</code> and status code <code>%s</code>.', 'ajax-search-for-woocommerce' ), esc_html( $rMessage ), $rCode ) . '</p>';
		$result['description'] .= '<p><b>' . __( "Response body", 'ajax-search-for-woocommerce' ) . '</b></p>';
		$result['description'] .= '<pre>' . substr( wp_strip_all_tags( $rBody ), 0, 2000 ) . '</pre>';
		$result['description'] .= $this->getDebugData();
		$result['status']      = 'critical';

		$this->storeResult( $result );

		return $result;
	}

	/**
	 * Test if Defender Security is active and blocking PHP execution in wp-content directory
	 *
	 * @return array The test result.
	 * @see getTestBlockedSearchEndpoint__premium_only
	 */
	public function getTestDefenderSecurity__premium_only() {

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'DefenderSecurity__premium_only',
		);

		$htaccessPath = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . '.htaccess';
		if ( class_exists( 'WP_Defender_Free' ) && is_file( $htaccessPath ) ) {
			$htaccessContent = file_get_contents( $htaccessPath );
			if ( strpos( $htaccessContent, '## WP Defender - Protect PHP Executed ##' ) !== false ) {
				$defenderSettingsUrl   = admin_url( 'admin.php?page=wdf-hardener&view=resolved' );
				$endpointPath          = 'search.php';
				$result['status']      = 'critical';
				$result['label']       = __( 'Defender plugin by WPMU DEV blocks AJAX calls of the live search', 'ajax-search-for-woocommerce' );
				$result['description'] = '<p><b>' . __( 'Solution', 'ajax-search-for-woocommerce' ) . '</b></p>';
				$result['description'] .= '<p>' . sprintf( __( 'Go to <code>Defender -> Security Tweaks -> <a href="%s" target="_blank">Resolved</a> -> Prevent PHP execution</code> and add following file path as exceptions: <code>%s</code>', 'ajax-search-for-woocommerce' ), $defenderSettingsUrl,
						$endpointPath ) . '</p>';
			}
		}

		return $result;
	}

	/**
	 * Test if there are .htaccess rules that block .php scripts
	 *
	 * @return array The test result.
	 * @see getTestBlockedSearchEndpoint__premium_only
	 */
	public function getTestHtaccessRulesBlockingPhp__premium_only() {

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'HtaccessRulesBlockingPhp__premium_only',
		);

		$htaccessPaths = array(
			WP_CONTENT_DIR . DIRECTORY_SEPARATOR . '.htaccess',
			WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . '.htaccess'
		);

		$htaccessFiles = array();
		foreach ( $htaccessPaths as $htaccessPath ) {
			if ( is_file( $htaccessPath ) ) {
				$htaccessContent = file_get_contents( $htaccessPath );
				preg_match_all( '/deny/mi', $htaccessContent, $matches );
				if (
					isset( $matches[0] ) &&
					! empty( $matches[0] )
				) {
					$htaccessFiles[] = $htaccessPath;
				}
			}
		}

		if ( ! empty( $htaccessFiles ) ) {
			$result['status']      = 'critical';
			$result['label']       = __( 'The .htaccess file(s) may blocking AJAX calls of the live search', 'ajax-search-for-woocommerce' );
			$result['description'] = '<p><b>' . __( 'Solution', 'ajax-search-for-woocommerce' ) . '</b></p>';
			$result['description'] .= '<p>' . sprintf( __( 'We recommend contact your hosting provider and ask to check <code>%s</code> file(s) on your server.', 'ajax-search-for-woocommerce' ), join( ', ', $htaccessFiles ) ) . '</p>';
		}

		return $result;
	}

	/**
	 * The test if the server software is NGINX
	 *
	 * @return array The test result.
	 * @see getTestBlockedSearchEndpoint__premium_only
	 */
	public function getTestNginx__premium_only() {

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'Nginx__premium_only',
		);

		if ( strpos( $_SERVER['SERVER_SOFTWARE'], 'nginx' ) !== false ) {
			$endpointUrl           = '<code>' . DGWT_WCAS_URL . 'includes/Engines/TNTSearchMySQL/Endpoints/search.php' . '</code>';
			$result['status']      = 'critical';
			$result['label']       = __( 'NGINX configuration may blocks search requests', 'ajax-search-for-woocommerce' );
			$result['description'] .= '<p>' . sprintf( __( 'Some NGINX configuration may block executing PHP files included directly in the plugins directory. There is no one solution. It depends on your NGINX configuration. We recommend contact your hosting provider and ask to allow to execute the following file: %s',
					'ajax-search-for-woocommerce' ), $endpointUrl ) . '</p>';
			$result['description'] .= '<p>' . __( 'Here are few samples NGINX config which helps other users:', 'ajax-search-for-woocommerce' ) . '</p>';
			$result['description'] .= '<ol><li>' . __( 'Adding extra rules to <code>/usr/local/nginx/conf/wpsecure_${vhostname}.conf</code>', 'ajax-search-for-woocommerce' ) . '</li></ol>';
			$nginxRule             = <<< EOT
# Whitelist Exception for FiboSearch endpoint
location ~ ^/wp-content/plugins/ajax-search-for-woocommerce-premium/includes/Engines/TNTSearchMySQL/Endpoints/ {
  include /usr/local/nginx/conf/php.conf;
}
EOT;
			$result['description'] .= '<pre>' . esc_html( $nginxRule ) . '</pre>';
		}

		return $result;
	}

	/**
	 * Test if Sucuri Security is active and blocking PHP execution in wp-content directory
	 *
	 * @return array The test result.
	 * @see getTestBlockedSearchEndpoint__premium_only
	 */
	public function getTestSucuriSecurity__premium_only() {

		$result = array(
			'label'       => '',
			'status'      => 'good',
			'description' => '',
			'actions'     => '',
			'test'        => 'SucuriSecurity__premium_only',
		);

		$cookies = array();
		$timeout = 10;
		$headers = array(
			'Cache-Control' => 'no-cache',
		);
		/** This filter is documented in wp-includes/class-wp-http-streams.php */
		$sslverify = apply_filters( 'https_local_ssl_verify', false );

		$url = DGWT_WCAS_URL . 'includes/Engines/TNTSearchMySQL/Endpoints/search.php';

		$r = wp_remote_get( $url, compact( 'cookies', 'headers', 'timeout', 'sslverify' ) );

		$rBody = wp_remote_retrieve_body( $r );

		if ( strpos( $rBody, 'Sucuri Website Firewall' ) !== false || strpos( $rBody, 'GoDaddy Website Firewall' ) !== false ) {
			$result['status']      = 'critical';
			$result['label']       = __( 'Sucuri Security firewall may block AJAX calls of the live search', 'ajax-search-for-woocommerce' );
			$result['description'] = '<p><b>' . __( 'Solution', 'ajax-search-for-woocommerce' ) . '</b></p>';
			$result['description'] .= '<ol><li>' . __( 'You need to log in to your <a href="https://login.sucuri.net" target="_blank">Sucuri panel</a>.', 'ajax-search-for-woocommerce' ) . '</li>';
			$result['description'] .= '<li>' . __( 'Go to the settings', 'ajax-search-for-woocommerce' ) . '</li>';
			$result['description'] .= '<li>' . __( 'Find section <code>Access Control -> whitelist URL</code>', 'ajax-search-for-woocommerce' ) . '</li>';
			$result['description'] .= '<li>' . __( 'Add the following URL to the white list:', 'ajax-search-for-woocommerce' ) . '</li></ol>';
			$result['description'] .= '<pre>' . esc_html( $url ) . '</pre>';
		}

		return $result;
	}

	/**
	 * Return a set of tests
	 *
	 * @return array The list of tests to run.
	 */
	public static function getTests() {

		$tests = array(
			'direct' => array(
				array(
					'label' => __( 'WordPress version', 'ajax-search-for-woocommerce' ),
					'test'  => 'WordPressVersion',
				),
				array(
					'label' => __( 'PHP extensions', 'ajax-search-for-woocommerce' ),
					'test'  => 'PHPExtensions',
				),
				array(
					'label' => __( 'Incompatible plugins', 'ajax-search-for-woocommerce' ),
					'test'  => 'IncompatiblePlugins',
				),
				array(
					'label' => __( 'Incorrect "Add to cart" behaviour in WooCommerce settings', 'ajax-search-for-woocommerce' ),
					'test'  => 'AjaxAddToCart',
				),
				array(
					'label' => __( 'Incompatible "Searching by Text" extension in WOOF - WooCommerce Products Filter', 'ajax-search-for-woocommerce' ),
					'test'  => 'WoofSearchTextExtension',
				),
				array(
					'label' => __( 'Elementor search results template', 'ajax-search-for-woocommerce' ),
					'test'  => 'ElementorSearchResultsTemplate',
				),
			),
			'async'  => array(
				array(
					'label' => __( 'Loopback request', 'ajax-search-for-woocommerce' ),
					'test'  => 'LoopbackRequests',
				)
			),
		);

		if ( ! dgoraAsfwFs()->is_premium() ) {
			// List of tests only for free plugin version
			$tests['direct'][] = array(
				'label' => __( 'TranslatePress', 'ajax-search-for-woocommerce' ),
				'test'  => 'TranslatePress',
			);
		}

		if ( dgoraAsfwFs()->is__premium_only() ) {
			// List of tests only for premium plugin version
			$tests['direct'][] = array(
				'label' => __( 'WordPress loading problem', 'ajax-search-for-woocommerce' ),
				'test'  => 'WordPressLoad__premium_only',
			);

			$tests['direct'][] = array(
				'label' => __( 'Issue with WP-Cron', 'ajax-search-for-woocommerce' ),
				'test'  => 'ScheduledEvents__premium_only',
			);

			$tests['direct'][] = array(
				'label' => __( 'Index completeness test', 'ajax-search-for-woocommerce' ),
				'test'  => 'IndexCompleteness__premium_only',
			);

			$tests['direct'][] = array(
				'label' => __( 'PDO connection test', 'ajax-search-for-woocommerce' ),
				'test'  => 'PDOConnection__premium_only',
			);

			$tests['direct'][] = array(
				'label' => __( 'Jetpack search module', 'ajax-search-for-woocommerce' ),
				'test'  => 'JetpackSearchModule__premium_only',
			);

			$tests['direct'][] = array(
				'label' => __( 'WooCommerce Multilingual', 'ajax-search-for-woocommerce' ),
				'test'  => 'WooCommerceMultilingual__premium_only',
			);

			$tests['direct'][] = array(
				'label' => __( 'WPML with disabled translations for products', 'ajax-search-for-woocommerce' ),
				'test'  => 'WpmlDisabledTranslations__premium_only',
			);

			$tests['direct'][] = array(
				'label' => __( 'Non Critical Indexer Errors', 'ajax-search-for-woocommerce' ),
				'test'  => 'NonCriticalIndexerErrors__premium_only',
			);

			$tests['direct'][] = array(
				'label' => __( 'Multilingual slugs', 'ajax-search-for-woocommerce' ),
				'test'  => 'MultilingualSlugs__premium_only',
			);

			$tests['direct'][] = array(
				'label' => __( 'Old index', 'ajax-search-for-woocommerce' ),
				'test'  => 'OldIndex__premium_only',
			);

			$tests['direct'][] = array(
				'label' => __( 'InnoDB support', 'ajax-search-for-woocommerce' ),
				'test'  => 'InnoDbSupport__premium_only',
			);

			$tests['async'][] = array(
				'label' => __( 'Blocked search endpoint test', 'ajax-search-for-woocommerce' ),
				'test'  => 'BlockedSearchEndpoint__premium_only',
			);

			$tests['async'][] = array(
				'label' => __( 'Valid search results test', 'ajax-search-for-woocommerce' ),
				'test'  => 'QuerySearchResults__premium_only',
			);

			$tests['async'][] = array(
				'label' => __( '"Out of stock" relationships', 'ajax-search-for-woocommerce' ),
				'test'  => 'OutOfStockRelationships__premium_only',
			);

			$tests['async'][] = array(
				'label' => __( 'Transients test', 'ajax-search-for-woocommerce' ),
				'test'  => 'Transients__premium_only',
			);
		}

		$tests = apply_filters( 'dgwt/wcas/troubleshooting/tests', $tests );

		return $tests;
	}

	/**
	 * Check if WP-Cron has missed events
	 *
	 * @return bool
	 */
	public static function hasWpCronMissedEvents() {
		if ( ! self::checkRequirements() ) {
			return false;
		}

		if ( ! class_exists( 'WP_Site_Health' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
		}

		$siteHealth = \WP_Site_Health::get_instance();
		$data       = $siteHealth->get_test_scheduled_events();

		if ( $data['status'] === 'critical' || $data['status'] === 'recommended' && $siteHealth->has_missed_cron() ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if Elementor elements contains specific widget type
	 *
	 * @param $elements
	 * @param $widget
	 *
	 * @return bool
	 */
	private function doesElementorElementsContainsWidget( $elements, $widget ) {
		$result = false;

		if ( ! is_array( $elements ) || empty( $elements ) || empty( $widget ) ) {
			return false;
		}

		if ( isset( $elements['widgetType'] ) && $elements['widgetType'] === 'wc-archive-products' ) {
			$result = true;
		}

		// Plain array of elements
		if ( ! isset( $elements['elements'] ) ) {
			foreach ( $elements as $element ) {
				$result = $result || $this->doesElementorElementsContainsWidget( $element, $widget );
			}
		} // Assoc array - single element
		else if ( isset( $elements['elements'] ) && is_array( $elements['elements'] ) && ! empty( $elements['elements'] ) ) {
			$result = $result || $this->doesElementorElementsContainsWidget( $elements['elements'], $widget );
		}

		return $result;
	}

	/**
	 * Check requirements
	 *
	 * We need WordPress 5.4 from which the Site Health module is available.
	 *
	 * @return bool
	 */
	private static function checkRequirements() {
		global $wp_version;

		return version_compare( $wp_version, '5.4.0' ) >= 0;
	}

	/**
	 * Run test directly
	 *
	 * @param $callback
	 *
	 * @return mixed|void
	 */
	private function performTest( $callback ) {
		return apply_filters( 'dgwt/wcas/troubleshooting/test-result', call_user_func( $callback ) );
	}

	/**
	 * Check if test exists
	 *
	 * @param $test
	 *
	 * @return bool
	 */
	private function isTestExists( $test, $type = 'async' ) {
		if ( empty( $test ) ) {
			return false;
		}
		$tests = self::getTests();

		foreach ( $tests[ $type ] as $value ) {
			if ( $value['test'] === $test ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get table with server environment
	 *
	 * @return string
	 */
	private function getDebugData() {
		if ( ! class_exists( 'WP_Debug_Data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
		}

		$result = '';
		$info   = \WP_Debug_Data::debug_data();

		if ( isset( $info['wp-server']['fields'] ) ) {
			ob_start();
			?>
			<p><b><?php _e( 'Server environment', 'ajax-search-for-woocommerce' ); ?></b></p>
			<table class="widefat striped" role="presentation">
				<tbody>
				<?php
				foreach ( $info['wp-server']['fields'] as $field_name => $field ) {
					if ( is_array( $field['value'] ) ) {
						$values = '<ul>';
						foreach ( $field['value'] as $name => $value ) {
							$values .= sprintf( '<li>%s: %s</li>', esc_html( $name ), esc_html( $value ) );
						}
						$values .= '</ul>';
					} else {
						$values = esc_html( $field['value'] );
					}
					printf( '<tr><td>%s</td><td>%s</td></tr>', esc_html( $field['label'] ), $values );
				}
				?>
				</tbody>
			</table>
			<?php
			$result = ob_get_clean();
		}

		return $result;
	}

	/**
	 * Get result of async test
	 *
	 * @param string $test Test name
	 *
	 * @return array
	 */
	private function getResult( $test ) {
		$asyncTestsResults = get_transient( self::TRANSIENT_RESULTS_KEY );
		if ( isset( $asyncTestsResults[ $test ] ) ) {
			return $asyncTestsResults[ $test ];
		}

		return array();
	}

	/**
	 * Storing result of async test
	 *
	 * Direct tests do not need to be saved.
	 *
	 * @param $result
	 */
	private function storeResult( $result ) {
		$asyncTestsResults = get_transient( self::TRANSIENT_RESULTS_KEY );
		if ( ! is_array( $asyncTestsResults ) ) {
			$asyncTestsResults = array();
		}
		$asyncTestsResults[ $result['test'] ] = $result;
		set_transient( self::TRANSIENT_RESULTS_KEY, $asyncTestsResults, 15 * 60 );
	}
}
