<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer;

use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Tokenizer;

class Utils {

	/**
	 * Create ngrams from string
	 *
	 * @param string $phrase
	 *
	 * @return string
	 */
	public static function applyNgrams( $phrase ) {

		$ngrams = '';

		$words = self::breakIntoTokens( $phrase );

		if ( ! empty( $words ) && is_array( $words ) ) {
			foreach ( $words as $word ) {
				$ngrams .= self::buildTrigrams( $word ) . ' ';
			}
		}

		return rtrim( $ngrams );
	}

	/**
	 * Build trigrams
	 *
	 * @param string $keyword
	 *
	 * @return string
	 */
	public static function buildTrigrams( $keyword ) {

		$t        = "__" . $keyword . "__";
		$trigrams = "";
		for ( $i = 0; $i < strlen( $t ) - 2; $i ++ ) {
			$trigrams .= mb_substr( $t, $i, 3 ) . " ";
		}

		return trim( $trigrams );
	}

	/**
	 * Break into tokens
	 *
	 * @param $text
	 *
	 * @return mixed
	 */
	public static function breakIntoTokens( $text ) {
		//@TODO support for other tokenizers
		$tokenizer = new Tokenizer();

		// TODO Add stopwords
		$stopwords = array();

		return $tokenizer->tokenize( $text, $stopwords );
	}

	/**
	 * Clear content from HTML tags, comments, scripts and shortcodes
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public static function clearContent( $content ) {
		// Strip all tags with separating its text, eg. "<h1>Foo</h1>bar" >> "Foo bar" (not "Foobar")
		$content = str_replace( '  ', ' ', wp_strip_all_tags( str_replace( '<', ' <', $content ) ) );

		// If we have shortcodes, remove all except allowed by `dgwt/wcas/indexer/allowed_shortcodes` filter
		if ( strpos( $content, '[' ) !== false ) {
			add_filter( 'strip_shortcodes_tagnames', array( __CLASS__, 'stripShortcodesTagnames' ), 10, 2 );
			$content = strip_shortcodes( $content );
			remove_filter( 'strip_shortcodes_tagnames', array( __CLASS__, 'stripShortcodesTagnames' ) );

			$content = do_shortcode( $content );
			$content = str_replace( '  ', ' ', wp_strip_all_tags( str_replace( '<', ' <', $content ) ) );
		}

		return trim( $content );
	}

	/**
	 * Filter shortcodes that will be stripped from content
	 *
	 * @param string[] $tags_to_remove
	 * @param string $content
	 *
	 * @return string[]
	 */
	public static function stripShortcodesTagnames( $tags_to_remove, $content ) {
		$allowedShortcodes = apply_filters( 'dgwt/wcas/indexer/allowed_shortcodes', array() );

		if ( is_array( $allowedShortcodes ) ) {
			$tags_to_remove = array_diff( $tags_to_remove, $allowedShortcodes );
		}

		return $tags_to_remove;
	}

	/**
	 * Get default collate
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public static function getCollate( $context = '' ) {
		global $wpdb;

		$sql     = '';
		$collate = '';
		$charset = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$charset = $wpdb->charset;
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate = $wpdb->collate;

			}
		}

		$charset = apply_filters( 'dgwt/wcas/db/charset', $charset, $context );
		$collate = apply_filters( 'dgwt/wcas/db/collation', $collate, $context );


		if ( ! empty( $charset ) ) {
			$sql .= " DEFAULT CHARACTER SET " . $charset;
		}

		if ( ! empty( $collate ) ) {
			$sql .= " COLLATE " . $collate;
		}

		return apply_filters( 'dgwt/wcas/db/collation/sql', $sql, $context );
	}

	/**
	 * Get WooCommerce queue object WC_Queue
	 *
	 * @return null|\WC_Queue_Interface
	 */
	public static function getQueue() {
		$queue = null;
		$wcObj = WC();
		if ( method_exists( $wcObj, 'queue' ) ) {
			$wcQueue = $wcObj->queue();
			if ( is_object( $wcQueue ) && method_exists( $wcQueue, 'schedule_recurring' ) ) {
				$queue = $wcQueue;
			}
		}

		return $queue;
	}


	/**
	 * Get all DB tables belong to the plugin
	 *
	 * @param bool $networkScope delete tables in whole network
	 *
	 * @return array
	 */
	public static function getAllPluginTables( $networkScope = false ) {
		global $wpdb;

		$pluginTables = array();

		$tables = $wpdb->get_results( "SHOW TABLES" );

		if ( ! empty( $tables ) && is_array( $tables ) ) {
			foreach ( $tables as $table ) {
				if ( ! empty( $table ) && is_object( $table ) ) {
					foreach ( $table as $tableName ) {

						if ( ! empty( $tableName ) && is_string( $tableName ) && strpos( $tableName, 'dgwt_wcas_' ) !== false ) {
							$pluginTables[] = $tableName;
						}
					}
				}
			}
		}


		if ( ! ( is_multisite() && $networkScope ) ) {
			$blogScopeTables = array();

			foreach ( $pluginTables as $name ) {
				$prefix = $wpdb->get_blog_prefix();

				if ( ! empty( $prefix ) && strpos( $name, $prefix ) !== false ) {
					$blogScopeTables[] = $name;
				}

			}

			$pluginTables = $blogScopeTables;

		}

		return $pluginTables;
	}
}
