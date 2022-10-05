<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable;

use DgoraWcas\Engines\TNTSearchMySQL\Config;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Builder;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Utils;
use DgoraWcas\Multilingual;
use DgoraWcas\Helpers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Database {

	const DB_VERSION = 5;
	const DB_VERSION_OPTION = 'dgwt_wcas_inv_index_db_version';

	/**
	 * Add tables names to the $wpdb object
	 * @return null
	 */
	public static function registerTables() {
		global $wpdb;

		$wpdb->dgwt_wcas_si_wordlist = $wpdb->prefix . Config::SEARCHABLE_INDEX_WORDLIST;
		$wpdb->tables[]              = Config::SEARCHABLE_INDEX_WORDLIST;

		$wpdb->dgwt_wcas_si_doclist = $wpdb->prefix . Config::SEARCHABLE_INDEX_DOCLIST;
		$wpdb->tables[]             = Config::SEARCHABLE_INDEX_DOCLIST;

		$wpdb->dgwt_wcas_si_info = $wpdb->prefix . Config::SEARCHABLE_INDEX_INFO;
		$wpdb->tables[]          = Config::SEARCHABLE_INDEX_INFO;

		$wpdb->dgwt_wcas_si_cache = $wpdb->prefix . Config::SEARCHABLE_INDEX_CACHE;
		$wpdb->tables[]           = Config::SEARCHABLE_INDEX_CACHE;
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
	 * @return void
	 */
	private static function install( $fromTheScratch = false ) {
		global $wpdb;
		$tables       = array();
		$freshInstall = empty( get_option( self::DB_VERSION_OPTION ) ) || $fromTheScratch ? true : false;

		$wpdb->hide_errors();

		$upFile = ABSPATH . 'wp-admin/includes/upgrade.php';

		if ( file_exists( $upFile ) ) {

			require_once( $upFile );

			$suffixes = self::getTablesSuffixes();

			foreach ( $suffixes as $suffix ) {
				$tables[] = self::wordListTableStruct( $suffix );
				$tables[] = self::docListTableStruct( $suffix );
				if ( Helpers::doesDbSupportJson__premium_only() ) {
					$tables[] = self::cacheTableStruct( $suffix );
				}
			}

			// Info
			$collate  = Utils::getCollate( 'searchable/info' );
			$tables[] = "CREATE TABLE $wpdb->dgwt_wcas_si_info (
				id            MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
                ikey          VARCHAR(255) NOT NULL,
				ivalue        VARCHAR(255) NOT NULL,
				PRIMARY KEY   (id)
			) ENGINE=InnoDB $collate;";

			dbDelta( $tables );

			sleep( 1 );

			if ( $freshInstall ) {

				$wpdb->query( "INSERT INTO $wpdb->dgwt_wcas_si_info ( ikey, ivalue) VALUES ( 'total_documents', 0)" );

				// MySQL Index
				foreach ( $suffixes as $suffix ) {
					$doclistMLTable = self::getDoclistTableName( $suffix );

					$wpdb->query( "CREATE INDEX main_term_id_index ON $doclistMLTable(term_id);" );
					$wpdb->query( "CREATE INDEX main_doc_id_index ON $doclistMLTable(doc_id);" );

					if ( Helpers::doesDbSupportJson__premium_only() ) {
						$cacheMLtable = self::getCacheTableName( $suffix );
						$wpdb->query( "CREATE INDEX main_cache_key_index ON $cacheMLtable(cache_key);" );
					}
				}

			}

			update_option( self::DB_VERSION_OPTION, self::DB_VERSION );
		}
	}

	/**
	 * Get all tables variations suffixes
	 *
	 * @return array
	 */
	public static function getTablesSuffixes() {
		$suffixes        = array();
		$langs           = Multilingual::getLanguages();
		$noProductsTypes = Helpers::getAllowedPostTypes( 'no-products' );

		if ( Multilingual::isMultilingual() ) {

			foreach ( $langs as $lang ) {

				$lang = str_replace( '-', '_', $lang );

				$suffixes[] = $lang;

				// Non-products indices
				if ( ! empty( $noProductsTypes ) ) {
					foreach ( $noProductsTypes as $noProductsType ) {

						$suffixes[] = $noProductsType . '_' . $lang;
					}
				}

			}

		} else {

			// Regular table - non suffix
			$suffixes[] = '';

			// Non-products indices
			if ( ! empty( $noProductsTypes ) ) {
				foreach ( $noProductsTypes as $noProductsType ) {

					$suffixes[] = $noProductsType;
				}
			}

		}

		return $suffixes;
	}

	/**
	 * Get real tables belong to the searchable index
	 *
	 * @return array
	 */
	public static function getSearchableIndexTables() {
		$searchableTables = array();

		$tables = Utils::getAllPluginTables();

		if ( ! empty( $tables ) ) {
			foreach ( $tables as $table ) {

				if (
					strpos( $table, 'dgwt_wcas_invindex_doclist' ) !== false
					|| strpos( $table, 'dgwt_wcas_invindex_wordlist' ) !== false
					|| strpos( $table, 'dgwt_wcas_invindex_cache' ) !== false
				) {
					$searchableTables[] = $table;
				}

			}
		}

		return $searchableTables;
	}

	/**
	 * Get WordList table name
	 *
	 * @param string $suffix
	 *
	 * @return string
	 */
	public static function getWordlistTableName( $suffix = '' ) {
		global $wpdb;

		return empty( $suffix ) ? $wpdb->dgwt_wcas_si_wordlist : $wpdb->dgwt_wcas_si_wordlist . '_' . $suffix;
	}

	/**
	 * Get Doclist table name
	 *
	 * @param string $suffix
	 *
	 * @return string
	 */
	public static function getDoclistTableName( $suffix = '' ) {
		global $wpdb;

		return empty( $suffix ) ? $wpdb->dgwt_wcas_si_doclist : $wpdb->dgwt_wcas_si_doclist . '_' . $suffix;
	}

	/**
	 *  DB structure for Wordlist table
	 *
	 * @param string $tableName
	 *
	 * @return string
	 */
	public static function wordListTableStruct( $suffix ) {
		global $wpdb;

		$collateContext = $suffix;
		$tableName      = $wpdb->dgwt_wcas_si_wordlist;

		if ( ! empty( $suffix ) ) {
			$tableName      = $tableName . '_' . sanitize_key( $suffix );
			$collateContext = '/' . $suffix;
		}

		$collate = Utils::getCollate( 'searchable/wordlist' . $collateContext );

		$sql     = "CREATE TABLE $tableName (
				id           MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
				term         VARCHAR(127) NOT NULL UNIQUE,
				num_hits     MEDIUMINT NOT NULL DEFAULT 1,
				num_docs     MEDIUMINT NOT NULL DEFAULT 1,
				PRIMARY KEY  (id)
			    ) ENGINE=InnoDB $collate;";

		return $sql;
	}

	/**
	 *  DB structure for Doclist table
	 *
	 * @param string $tableName
	 *
	 * @return string
	 */
	public static function docListTableStruct( $suffix ) {
		global $wpdb;

		$tableName = $wpdb->dgwt_wcas_si_doclist;

		if ( ! empty( $suffix ) ) {
			$tableName = $tableName . '_' . sanitize_key( $suffix );
		}

		$sql = "CREATE TABLE $tableName (
				id           MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
                term_id      MEDIUMINT UNSIGNED NOT NULL,
				doc_id       BIGINT NOT NULL,
				hit_count    MEDIUMINT NOT NULL DEFAULT 1,
				PRIMARY KEY  (id)
			    ) ENGINE=InnoDB COLLATE ascii_bin";

		return $sql;
	}

	/**
	 * Get Cache table name
	 *
	 * @param string $suffix
	 *
	 * @return string
	 */
	public static function getCacheTableName( $suffix = '' ) {
		global $wpdb;

		return empty( $suffix ) ? $wpdb->dgwt_wcas_si_cache : $wpdb->dgwt_wcas_si_cache . '_' . $suffix;
	}

	/**
	 *  DB structure for Cache table
	 *
	 * @param string $tableName
	 *
	 * @return string
	 */
	public static function cacheTableStruct( $suffix ) {
		global $wpdb;

		$tableName = $wpdb->dgwt_wcas_si_cache;

		if ( ! empty( $suffix ) ) {
			$tableName = $tableName . '_' . sanitize_key( $suffix );
		}

		$collate = Utils::getCollate( 'searchable/cache' );

		$sql = "CREATE TABLE $tableName (
				cache_id     MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
                cache_key    VARCHAR(255) NOT NULL UNIQUE,
				cache_value  JSON NOT NULL,
				PRIMARY KEY  (cache_id)
			    ) ENGINE=InnoDB $collate;";

		return $sql;
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
	 * Remove searchable index
	 *
	 * @return void
	 */
	public static function remove() {
		global $wpdb;

		$wpdb->hide_errors();

		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dgwt_wcas_si_info" );

		foreach ( self::getSearchableIndexTables() as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS $table" );
		}

	}

	/**
	 * MySQL Config for the connector
	 *
	 * @return array
	 */
	public static function getConfig() {
		global $wpdb;

		$dbHost = empty( DB_HOST ) ? '127.0.0.1' : DB_HOST;

		$hostInfo = $wpdb->parse_db_host( $dbHost );
		list( $host, $port, $socket, $is_ipv6 ) = $hostInfo;

		$config = array(
			'database'  => DB_NAME,
			'username'  => DB_USER,
			'password'  => DB_PASSWORD,
			'host'      => $host,
			'charset'   => $wpdb->charset,
			'collation' => '',
			'options'   => array(
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			)
		);

		if ( $wpdb->has_cap( 'collation' ) && ! empty( $wpdb->collate ) ) {
			$config['collation'] = $wpdb->collate;
		}

		if ( ! empty( $port ) ) {
			$config['port'] = $port;
		}

		if ( ! empty( $socket ) ) {
			$config['unix_socket'] = $socket;
		}


		// SSL
		if (
			defined( 'MYSQL_CLIENT_FLAGS' )
			&& defined( 'MYSQLI_CLIENT_SSL' )
			&& MYSQL_CLIENT_FLAGS === MYSQLI_CLIENT_SSL
		) {

			$sslKey     = defined( 'DGWT_WCAS_MYSQL_SSL_KEY' ) ? DGWT_WCAS_MYSQL_SSL_KEY : '';
			$verifyCert = defined( 'DGWT_WCAS_MYSQL_ERIFY_SERVER_CERT' ) ? boolval( DGWT_WCAS_MYSQL_ERIFY_SERVER_CERT ) : false;

			$config['options'][ \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT ] = $verifyCert;
			$config['options'][ \PDO::MYSQL_ATTR_SSL_KEY ]                = $sslKey;

			if ( defined( 'DGWT_WCAS_MYSQL_SSL_CERT' ) ) {
				$config['options'][ \PDO::MYSQL_ATTR_SSL_CERT ] = DGWT_WCAS_MYSQL_SSL_CERT;
			}

			if ( defined( 'DGWT_WCAS_MYSQL_SSL_CA' ) ) {
				$config['options'][ \PDO::MYSQL_ATTR_SSL_CA ] = DGWT_WCAS_MYSQL_SSL_CA;
			}

		}

		return apply_filters( 'dgwt/wcas/tnt/db/config', $config );
	}

}
