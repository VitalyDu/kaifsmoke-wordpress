<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer\Taxonomy;

use DgoraWcas\Engines\TNTSearchMySQL\Config;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Database {

	const DB_VERSION = 1;
	const DB_VERSION_OPTION = 'dgwt_wcas_tax_index_db_version';

	/**
	 * Add table names to the $wpdb object
	 * @return null
	 */
	public static function registerTables() {
		global $wpdb;

		$wpdb->dgwt_wcas_tax_index = $wpdb->prefix . Config::READABLE_TAX_INDEX;
		$wpdb->tables[]            = Config::READABLE_TAX_INDEX;

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
	 * @return void
	 */
	private static function install( $fromTheScratch = false ) {
		global $wpdb;

		$wpdb->hide_errors();
		$freshInstall = empty( get_option( self::DB_VERSION_OPTION ) ) || $fromTheScratch ? true : false;

		$upFile = ABSPATH . 'wp-admin/includes/upgrade.php';

		if ( file_exists( $upFile ) ) {

			require_once( $upFile );

			$collate = $collate = Utils::getCollate( 'taxonomy/main' );

			/**
			 * We use 'id' column because 'term_id' because 'term_id' is not always unique.
			 * This happens, for example, with the TranslatePress plugin, when records of different
			 * languages have the same 'term_id'.
			 */
			$table = "CREATE TABLE $wpdb->dgwt_wcas_tax_index (
				id              BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				term_id         BIGINT(20) UNSIGNED NOT NULL,
				term_name       TEXT NOT NULL,
				term_link       TEXT NOT NULL,
				image           TEXT NOT NULL,
				breadcrumbs     TEXT NOT NULL,
				total_products  INT NOT NULL,
				taxonomy        VARCHAR(50) NOT NULL,
				lang            VARCHAR(7) NOT NULL,
				PRIMARY KEY    (id)
			) ENGINE=InnoDB $collate;";


			dbDelta( $table );

			if ( $freshInstall ) {
				$wpdb->query( "CREATE INDEX main_term_id ON $wpdb->dgwt_wcas_tax_index(term_id);" );
				$wpdb->query( "CREATE INDEX main_taxonomy ON $wpdb->dgwt_wcas_tax_index(taxonomy);" );
				$wpdb->query( "CREATE INDEX main_lang ON $wpdb->dgwt_wcas_tax_index(lang);" );
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

		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dgwt_wcas_tax_index" );

	}

}
