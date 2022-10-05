<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer\Readable;

use DgoraWcas\Engines\TNTSearchMySQL\Config;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Database {

	const DB_VERSION = 1;
	const DB_VERSION_OPTION = 'dgwt_wcas_index_db_version';

	/**
	 * Add table names to the $wpdb object
	 * @return null
	 */
	public static function registerTables() {
		global $wpdb;

		$wpdb->dgwt_wcas_index = $wpdb->prefix . Config::READABLE_INDEX;
		$wpdb->tables[]        = Config::READABLE_INDEX;

	}

	/**
	 * Check DB version and install DB if necessary
	 * @return null
	 */
	public static function maybeInstall() {

		$dbVersion = get_option( self::DB_VERSION_OPTION );

		if ( absint( $dbVersion ) !== self::DB_VERSION ) {
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

			$collate = Utils::getCollate( 'readable/main' );

			/**
			 * We use 'id' column because 'post_id' because 'post_id' is not always unique.
			 * This happens, for example, with the TranslatePress plugin, when records of different
			 * languages have the same 'post_id'.
			 */
			$table = "CREATE TABLE $wpdb->dgwt_wcas_index (
				id         		BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				post_id         BIGINT(20) UNSIGNED NOT NULL,
				created_date    DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
				name            TEXT NOT NULL,
				description     TEXT NOT NULL,
				sku             TEXT NOT NULL,
				sku_variations  TEXT NOT NULL,
				attributes      LONGTEXT NOT NULL,
				meta            LONGTEXT NOT NULL,
				image           TEXT NOT NULL,
				url				TEXT NOT NULL,
				html_price      TEXT NOT NULL,
				price           DECIMAL(10,2) NOT NULL,
				average_rating  DECIMAL(3,2) NOT NULL,
                review_count    SMALLINT(5) NOT NULL DEFAULT '0',
                total_sales     SMALLINT(5) NOT NULL DEFAULT '0',
                lang            VARCHAR(7) NOT NULL,
				PRIMARY KEY     (id)
			) ENGINE=InnoDB $collate;";

			dbDelta( $table );

			if ( $freshInstall ) {
				$wpdb->query( "CREATE INDEX main_post_id ON $wpdb->dgwt_wcas_index(post_id);" );
			}

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

		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dgwt_wcas_index" );

	}

}
