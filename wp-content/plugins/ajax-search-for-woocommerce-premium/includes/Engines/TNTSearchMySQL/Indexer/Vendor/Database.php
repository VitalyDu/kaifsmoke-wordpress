<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer\Vendor;

use DgoraWcas\Engines\TNTSearchMySQL\Config;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Database {

	const DB_VERSION = 1;
	const DB_VERSION_OPTION = 'dgwt_wcas_ven_index_db_version';

	/**
	 * Add table names to the $wpdb object
	 * @return null
	 */
	public static function registerTables() {
		global $wpdb;

		$wpdb->dgwt_wcas_ven_index = $wpdb->prefix . Config::VENDORS_INDEX;
		$wpdb->tables[]            = Config::VENDORS_INDEX;

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
	private static function install( $fromTheScratch = false ) {
		global $wpdb;

		$wpdb->hide_errors();
		$freshInstall = empty( get_option( self::DB_VERSION_OPTION ) ) || $fromTheScratch ? true : false;

		$upFile = ABSPATH . 'wp-admin/includes/upgrade.php';

		if ( file_exists( $upFile ) ) {

			require_once( $upFile );

			$collate = $collate = Utils::getCollate( 'vendor/main' );

			$table = "CREATE TABLE $wpdb->dgwt_wcas_ven_index (
				vendor_id         BIGINT(20) UNSIGNED NOT NULL,
		        shop_name         VARCHAR(100) NOT NULL,
				shop_city         VARCHAR(100) NOT NULL,
				shop_description  TEXT NOT NULL,
				shop_url          TEXT NOT NULL,
				shop_image        TEXT NOT NULL,
				PRIMARY KEY (vendor_id)
			) ENGINE=InnoDB $collate;";


			dbDelta( $table );

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

		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dgwt_wcas_ven_index" );

	}

}
