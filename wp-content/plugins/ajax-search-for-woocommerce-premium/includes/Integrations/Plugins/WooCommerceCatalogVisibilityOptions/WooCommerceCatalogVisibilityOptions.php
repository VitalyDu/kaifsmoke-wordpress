<?php
/**
 * @dgwt_wcas_premium_only
 */
namespace DgoraWcas\Integrations\Plugins\WooCommerceCatalogVisibilityOptions;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Integration with WooCommerce Catalog Visibility Options
 *
 * Plugin URL: https://woocommerce.com/products/catalog-visibility-options/
 * Author: Lucas Stark
 */
class WooCommerceCatalogVisibilityOptions {
	public function init() {
		if ( ! dgoraAsfwFs()->is_premium() ) {
			return;
		}

		if ( ! defined( 'WC_CATALOG_VISIBILITY_OPTIONS_VERSION' ) ) {
			return;
		}
		if ( version_compare( WC_CATALOG_VISIBILITY_OPTIONS_VERSION, '2.8.5' ) < 0 ) {
			return;
		}

		add_action( 'init', array( $this, 'storeInSessionDisallowedProducts' ), 20 );
	}

	/**
	 * Store disallowed product ids in session
	 */
	public function storeInSessionDisallowedProducts() {
		if ( class_exists( 'WC_Catalog_Restrictions_Query' ) ) {
			$wc_catalog_restrictions_query = \WC_Catalog_Restrictions_Query::instance();
			$disallowed_products           = $wc_catalog_restrictions_query->get_disallowed_products();
			if ( ! session_id() ) {
				session_start();
			}
			$_SESSION['dgwt-wcas-wccvo-disallowed-products'] = $disallowed_products;
		}
	}
}
