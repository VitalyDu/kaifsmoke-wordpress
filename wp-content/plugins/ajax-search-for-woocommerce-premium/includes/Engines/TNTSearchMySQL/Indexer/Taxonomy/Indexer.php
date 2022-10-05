<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer\Taxonomy;

use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Builder;
use DgoraWcas\Helpers;
use DgoraWcas\Multilingual;
use DgoraWcas\Term;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Indexer {

	private $taxonomy = '';

	public function __construct() {

	}

	/**
	 * Set taxonomy
	 *
	 * @param $taxonomy
	 *
	 * @return bool
	 */
	public function setTaxonomy( $taxonomy ) {
		$success = false;
		if ( taxonomy_exists( $taxonomy ) ) {
			$this->taxonomy = $taxonomy;
			$success        = true;
		}

		return $success;
	}

	/**
	 * Insert term to the index
	 *
	 * @param int $termID
	 * @param string $taxonomy
	 *
	 * @return bool true on success
	 */
	public function index( $termID, $taxonomy ) {

		global $wpdb;
		$success = false;

		$termLang = Multilingual::getTermLang( $termID, $taxonomy );

		if ( Multilingual::isMultilingual() ) {
			$term = Multilingual::getTerm( $termID, $taxonomy, $termLang );
			// Switch language to compatibility with other plugins.
			// Our plugin don't need this switch, but some plugins use the active language as the term language
			if ( Multilingual::getCurrentLanguage() !== $termLang ) {
				Multilingual::switchLanguage( $termLang );
			}
		} else {
			$term = get_term( $termID, $taxonomy );
		}

		$data = array();

		$termObj              = new Term( $term );
		$taxonomiesWithImages = apply_filters( 'dgwt/wcas/taxonomies_with_images', array() );

		if ( is_object( $term ) && ! is_wp_error( $term ) ) {

			$data = array(
				'term_id'        => $termID,
				'term_name'      => $term->name,
				'term_link'      => get_term_link( $term, $taxonomy ),
				'image'          => in_array( $taxonomy, $taxonomiesWithImages ) ? $termObj->getThumbnailSrc() : '',
				'breadcrumbs'    => '',
				'total_products' => $term->count,
				'taxonomy'       => $taxonomy,
				'lang'           => $termLang
			);

			if ( $term->taxonomy === 'product_cat' ) {
				$breadcrumbs = Helpers::getTermBreadcrumbs( $termID, 'product_cat', array(), $termLang, array( $termID ) );

				// Fix: Remove last separator
				if ( ! empty( $breadcrumbs ) ) {
					$breadcrumbs = mb_substr( $breadcrumbs, 0, - 3 );
				}
				$data['breadcrumbs'] = $breadcrumbs;
			}

			$rows = $wpdb->insert(
				$wpdb->dgwt_wcas_tax_index,
				$data,
				array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
				)
			);

			if ( is_numeric( $rows ) ) {
				$success = true;
			}

		}

		do_action( 'dgwt/wcas/taxonomy_index/after_insert', $data, $termID, $taxonomy, $success );

		return $success;

	}

	/**
	 * Update term
	 *
	 * @param int $termID
	 * @param string $taxonomy
	 *
	 * @return void
	 */
	public function update( $termID, $taxonomy ) {
		$this->delete( $termID, $taxonomy );
		$this->index( $termID, $taxonomy );
	}

	/**
	 * Remove term from the index
	 *
	 * @param int $termID
	 * @param string $taxonomy
	 *
	 * @return bool true on success
	 */
	public function delete( $termID, $taxonomy ) {
		global $wpdb;
		$success = false;

		$wpdb->delete(
			$wpdb->dgwt_wcas_tax_index,
			array( 'term_id' => $termID ),
			array( '%d' )
		);

		return $success;

	}

	/**
	 * Wipe index
	 *
	 * @return bool
	 */
	public function wipe() {
		Database::remove();
		Builder::log( '[Taxonomy index] Cleared' );

		return true;
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
