<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer\Variation;

use DgoraWcas\Engines\TNTSearchMySQL\Config;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Database {

	const DB_VERSION = 1;
	const DB_VERSION_OPTION = 'dgwt_wcas_var_index_db_version';

	/**
	 * Add table names to the $wpdb object
	 * @return null
	 */
	public static function registerTables() {
		global $wpdb;

		$wpdb->dgwt_wcas_var_index = $wpdb->prefix . Config::VARIATIONS_INDEX;
		$wpdb->tables[]            = Config::VARIATIONS_INDEX;

	}

	/**
	 * Check DB version and install DB if necessary
	 * @return null
	 */
	public static function maybeInstall() {

		$db_version = get_option( self::DB_VERSION_OPTION );

		if ( absint( $db_version ) !== self::DB_VERSION ) {
			self::install();
		}

	}

	/**
	 * Install DB table
	 *
	 * @param bool $fromTheScratch
	 *
	 * @return null
	 */
	private static function install() {
		global $wpdb;

		$wpdb->hide_errors();

		$upFile = ABSPATH . 'wp-admin/includes/upgrade.php';

		if ( file_exists( $upFile ) ) {

			require_once( $upFile );

			$collate = $collate = Utils::getCollate( 'variations/main' );

			/**
			 * We use 'id' column because 'variation_id' because 'variation_id' is not always unique.
			 * This happens, for example, with the TranslatePress plugin, when records of different
			 * languages have the same 'variation_id'.
			 */
			$table = "CREATE TABLE $wpdb->dgwt_wcas_var_index (
				id              BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				variation_id    BIGINT(20) UNSIGNED NOT NULL,
				product_id      BIGINT(20) UNSIGNED NOT NULL,
				sku             VARCHAR(100) NOT NULL,
				title           TEXT NOT NULL,
				description     TEXT NOT NULL,
				image           TEXT NOT NULL,
				url				TEXT NOT NULL,
				html_price      TEXT NOT NULL,
				lang            VARCHAR(7) NOT NULL,
				PRIMARY KEY    (id)
			) ENGINE=InnoDB $collate;";

			dbDelta( $table );

			$wpdb->query( "CREATE INDEX main_variation_id ON $wpdb->dgwt_wcas_var_index(variation_id);" );
			$wpdb->query( "CREATE INDEX main_product_id ON $wpdb->dgwt_wcas_var_index(product_id);" );
			$wpdb->query( "CREATE INDEX main_sku ON $wpdb->dgwt_wcas_var_index(sku);" );
			$wpdb->query( "CREATE INDEX main_lang ON $wpdb->dgwt_wcas_var_index(lang);" );

			update_option( self::DB_VERSION_OPTION, self::DB_VERSION );
		}
	}

	/**
	 * Create database structure from the scratch
	 *
	 * @return void
	 */
	public static function create() {
		self::install( true );
	}

	/**
	 * Remove DB table
	 *
	 * @return void
	 */
	public static function remove() {
		global $wpdb;

		$wpdb->hide_errors();

		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dgwt_wcas_var_index" );

	}

}
