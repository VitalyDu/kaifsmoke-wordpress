<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer;

use DgoraWcas\Helpers;
use DgoraWcas\Multilingual;

class SourceQuery {

	private $args = array();

	private $data = array();

	private $select = '';
	private $join = '';
	private $where = '';

	private $request;

	private $visibilityTermIds;

	public function __construct( $args = array() ) {
		$this->visibilityTermIds = wc_get_product_visibility_term_ids();

		$this->setArgs( $args );

		$this->setData();
	}

	private function setArgs( $args ) {

		$defaults = array(
			'ids'      => false, // return only IDS
			'trigrams' => false,
			'package'  => array()
		);

		$this->args = wp_parse_args( $args, $defaults );
	}

	/**
	 * Get data directly from MySQL to save memory
	 * @return void
	 */
	private function setData() {
		global $wpdb;

		$this->select .= 'posts.ID, posts.post_title';

		// Visibility settings
		$this->excludeFromSearch();

		// Exclude out of stock products
		if ( DGWT_WCAS()->settings->getOption( 'exclude_out_of_stock' ) === 'on' ) {
			$this->excludeOutOfStock();
		};

		// Exclude/include products matched by filters
		if ( ! empty( DGWT_WCAS()->settings->getOption( 'filter_products_rules' ) ) ) {
			$this->excludeOrIncludeMatchedByFilters();
		};

		// Set range of products set
		if ( ! empty( $this->args['package'] ) ) {
			$this->narrowDownToTheSet();
		}

		// Add product full description
		if ( DGWT_WCAS()->settings->getOption( 'search_in_product_content' ) === 'on' ) {
			$this->addDescription();
		}

		// Add product short description
		if ( DGWT_WCAS()->settings->getOption( 'search_in_product_excerpt' ) === 'on' ) {
			$this->addShortDescription();
		}

		if ( Helpers::canSearchInVariableProducts() ) {
			$this->addVariableProductsDescription();
		}

		// Add SKUs to the index
		if ( DGWT_WCAS()->settings->getOption( 'search_in_product_sku' ) === 'on' ) {
			$this->addSku();
			$this->addSkuForVariations();
		}

		// Add attributes to the index
		if ( DGWT_WCAS()->settings->getOption( 'search_in_product_attributes' ) === 'on' ) {
			$this->addAttributes();
		}

		// Add Custom fields to the index
		if ( ! empty( DGWT_WCAS()->settings->getOption( 'search_in_custom_fields' ) ) ) {
			$this->addCustomFields();
		}

		// Search in taxonomies
		$activeTaxonomies = DGWT_WCAS()->tntsearchMySql->taxonomies->getActiveTaxonomies( 'search_related_products' );
		foreach ( $activeTaxonomies as $taxonomy ) {
			$this->addTerms( $taxonomy );
		}

		if ( Multilingual::isMultilingual() ) {
			$this->addLang();
		}


		$this->where .= " AND (post_type = 'product') ";
		$this->where .= " AND post_status = 'publish' ";

		$onlyIDs = $this->onlyIDs();

		if ( $onlyIDs ) {
			$this->select = 'posts.ID';
		}

		do_action( 'dgwt/wcas/tnt/source_query/before_request', $this, $onlyIDs );

		$this->select = apply_filters( 'dgwt/wcas/tnt/source_query/select', $this->select, $this, $onlyIDs );
		$this->join   = apply_filters( 'dgwt/wcas/tnt/source_query/join', $this->join, $this, $onlyIDs );
		$this->where  = apply_filters( 'dgwt/wcas/tnt/source_query/where', $this->where, $this, $onlyIDs );

		$groupConcatMaxLen = apply_filters( 'dgwt/wcas/tnt/source_query/group_concat_max_len', 100000 );

		if ( ! empty( $groupConcatMaxLen ) ) {
			$groupConcatMaxLen = absint( $groupConcatMaxLen );
			$wpdb->query( 'SET SESSION group_concat_max_len = ' . $groupConcatMaxLen . ';' );
		}

		$this->request = "SELECT $this->select
                FROM $wpdb->posts posts
                $this->join
                WHERE  1=1
                $this->where
               ";

		$rows = $wpdb->get_results( apply_filters( 'dgwt/wcas/tnt/source_query/request', $this->request, $this, $onlyIDs ), ARRAY_A );

		if ( ! empty( $rows ) && ! is_wp_error( $rows ) ) {
			$this->data = $rows;
		}

		$this->data = apply_filters( 'dgwt/wcas/tnt/source_query/data', $this->data, $this, $onlyIDs );
	}

	/**
	 * Not index products excluded from search via WooCommerce product settings
	 *
	 * @return void
	 */
	private function excludeFromSearch() {
		global $wpdb;

		$this->where .= $wpdb->prepare( " AND posts.ID NOT IN (
                                                   SELECT object_id
                                                   FROM $wpdb->term_relationships
                                                   WHERE term_taxonomy_id IN (%d)
				                                )",
			$this->visibilityTermIds['exclude-from-search']
		);

	}

	/**
	 * Not index products with the stock status "outofstock"
	 *
	 * @return void
	 */
	private function excludeOutOfStock() {

		global $wpdb;

		$this->where .= $wpdb->prepare( " AND ( posts.ID NOT IN (
                                                   SELECT object_id
                                                   FROM $wpdb->term_relationships
                                                   WHERE term_taxonomy_id IN (%d)
				                                ))",
			$this->visibilityTermIds['outofstock']
		);

	}

	/**
	 * Do not index or index only products with a given category, tag or attribute
	 *
	 * @return void
	 */
	private function excludeOrIncludeMatchedByFilters() {
		global $wpdb;

		$rules = Helpers::getFilterProductsRules__premium_only();

		if ( empty( $rules ) ) {
			return;
		}

		$filterMode              = DGWT_WCAS()->settings->getOption( 'filter_products_mode', 'exclude' );
		$filteredTermTaxonomyIds = array();
		$langs                   = array();

		if ( Multilingual::isMultilingual() ) {
			// Others languages than default
			$langs = array_values( array_diff( Multilingual::getLanguages(), array( Multilingual::getDefaultLanguage() ) ) );
		}

		foreach ( $rules as $group => $values ) {
			$matchedTerms = Helpers::getFilterGroupTerms__premium_only( $group, $values );
			if ( ! empty( $matchedTerms ) ) {
				$filteredTermTaxonomyIds = array_merge( $filteredTermTaxonomyIds, wp_list_pluck( $matchedTerms, 'term_taxonomy_id' ) );

				// Get all related term's ids from all languages (except default)
				if ( ! empty( $langs ) ) {
					$taxonomy = Helpers::getTaxonomyFromFilterGroup__premium_only( $group );
					foreach ( $langs as $lang ) {
						foreach ( $matchedTerms as $term ) {
							$termTranslated = Multilingual::getTerm( $term->term_id, $taxonomy, $lang );
							if ( ! empty( $termTranslated ) ) {
								$filteredTermTaxonomyIds[] = $termTranslated->term_taxonomy_id;
							}
						}
					}
				}
			}
		}

		if ( empty( $filteredTermTaxonomyIds ) ) {
			return;
		}

		$placeholders = array_fill( 0, count( $filteredTermTaxonomyIds ), '%d' );
		$format       = implode( ', ', $placeholders );

		$this->where .= $wpdb->prepare( " AND ( posts.ID " . ( $filterMode === 'exclude' ? 'NOT' : '' ) . " IN (
                                                   SELECT object_id
                                                   FROM $wpdb->term_relationships
                                                   WHERE term_taxonomy_id IN ($format)
				                                ))",
			$filteredTermTaxonomyIds
		);
	}

	/**
	 * Narrow down to the specific products set
	 *
	 * @return void
	 */
	private function narrowDownToTheSet() {

		global $wpdb;

		$package = $this->args['package'];

		$placeholders = array_fill( 0, count( $package ), '%d' );
		$format       = implode( ', ', $placeholders );

		$this->where .= $wpdb->prepare( " AND posts.ID IN ($format)", $package );
	}


	/**
	 * Add product description to the index
	 *
	 * @return void
	 */
	private function addDescription() {
		$this->select .= ", posts.post_content";
	}

	/**
	 * Add product excerpt to the index
	 *
	 * @return void
	 */
	private function addShortDescription() {
		$this->select .= ", posts.post_excerpt";
	}

	/**
	 * Add product SKU to the index
	 *
	 * @return void
	 */
	private function addSku() {
		global $wpdb;

		$this->select .= ", (SELECT meta_value FROM $wpdb->postmeta WHERE post_id = posts.ID AND meta_key='_sku' LIMIT 1) AS sku";
	}

	/**
	 * Add variable products SKU to the index
	 *
	 * @return void
	 */
	private function addSkuForVariations() {
		global $wpdb;

		$excludeOutOfStockSql = '';
		if ( DGWT_WCAS()->settings->getOption( 'exclude_out_of_stock' ) === 'on' ) {
			$excludeOutOfStockSql = "AND psv.ID NOT IN (
							            SELECT post_id FROM $wpdb->posts AS psv2
			                            JOIN $wpdb->postmeta AS pmsv2 ON psv2.ID = pmsv2.post_id
			                            WHERE psv2.post_type = 'product_variation'
			                            AND psv2.post_parent = posts.ID
			                            AND pmsv2.meta_key = '_stock_status'
			                            AND pmsv2.meta_value = 'outofstock'
			                         )";
		}

		$this->select .= ", (SELECT GROUP_CONCAT( pmsv.meta_value SEPARATOR ' | ')
                             FROM $wpdb->posts AS psv
                             JOIN $wpdb->postmeta AS pmsv ON psv.ID = pmsv.post_id
                             WHERE psv.post_type = 'product_variation'
                             AND psv.post_parent = posts.ID
                             AND pmsv.meta_key='_sku'
                             AND pmsv.meta_value != ''
                             $excludeOutOfStockSql
                             ) AS sku_variations";
	}

	/**
	 * Add variable product description to the search scope
	 *
	 * @return void
	 */
	private function addVariableProductsDescription() {

		global $wpdb;

		$excludeOutOfStockSql = '';
		if ( DGWT_WCAS()->settings->getOption( 'exclude_out_of_stock' ) === 'on' ) {
			$excludeOutOfStockSql = "AND pvd.ID NOT IN (
							            SELECT post_id FROM $wpdb->posts AS pvd2
			                            JOIN $wpdb->postmeta AS pmvd2 ON pvd2.ID = pmvd2.post_id
			                            WHERE pvd2.post_type = 'product_variation'
			                            AND pvd2.post_parent = posts.ID
			                            AND pmvd2.meta_key = '_stock_status'
			                            AND pmvd2.meta_value = 'outofstock'
			                         )";
		}

		$this->select .= ", (SELECT GROUP_CONCAT(pmvd.meta_value SEPARATOR ' | ')
                             FROM $wpdb->posts AS pvd
                             JOIN $wpdb->postmeta AS pmvd ON pvd.ID = pmvd.post_id
                             WHERE pvd.post_type = 'product_variation'
                             AND pvd.post_parent = posts.ID
                             AND pmvd.meta_key='_variation_description'
                             AND pmvd.meta_value != ''
                             $excludeOutOfStockSql
                             ) AS variations_description";
	}

	/**
	 * Add attributes to the index
	 *
	 * @return void
	 */
	private function addAttributes() {
		global $wpdb;
		$taxonomies = Helpers::getAttributesTaxonomies();

		if ( ! empty( $taxonomies ) ) {

			$placeholders = array_fill( 0, count( $taxonomies ), '%s' );
			$format       = implode( ', ', $placeholders );

			$this->select .= $wpdb->prepare( ", (SELECT GROUP_CONCAT( t.name SEPARATOR ' | ')
                             FROM $wpdb->terms AS t
                             INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
                             INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
                             WHERE tt.taxonomy IN ($format)
                             AND tr.object_id = posts.ID
                             ) AS attributes",
				$taxonomies
			);
		}
	}

	/**
	 * Add custom fields
	 *
	 * @return void
	 */
	private function addCustomFields() {
		global $wpdb;

		$keys = explode( ',', DGWT_WCAS()->settings->getOption( 'search_in_custom_fields' ) );

		if ( ! empty( $keys ) ) {

			$i = 1;
			foreach ( $keys as $keyRaw ) {

				$key = sanitize_key( $keyRaw );

				$colName      = 'custom_field_' . $i;
				$query = $wpdb->prepare(
					", (SELECT meta_value FROM $wpdb->postmeta WHERE post_id = posts.ID AND meta_key=%s LIMIT 1) AS $colName",
					$key
				);

				$this->select .= apply_filters( 'dgwt/wcas/tnt/source_query/select/custom_field', $query, $keyRaw, $colName);

				$i ++;
			}

		}

	}

	/**
	 * Add terms
	 *
	 * @param $taxonomy
	 *
	 * @return void
	 */
	public function addTerms( $taxonomy ) {
		global $wpdb;

		if ( ! empty( $taxonomy ) && taxonomy_exists( $taxonomy ) ) {

			$taxonomy = sanitize_key( $taxonomy );
			$label    = 'tax_' . $taxonomy;

			$groupConcat = "GROUP_CONCAT( t.name SEPARATOR ' | ')";

			if ( apply_filters( 'dgwt/wcas/tnt/source_query/term_description', false ) ) {
				$groupConcat = "GROUP_CONCAT( DISTINCT CONCAT(t.name, ' | ', tt.description) ORDER BY t.name SEPARATOR ' | ' )";
			}

			$this->select .= $wpdb->prepare( ", (SELECT $groupConcat
                             FROM $wpdb->terms AS t
                             INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
                             INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
                             WHERE tt.taxonomy = %s
                             AND tr.object_id = posts.ID
                             ) AS '$label'",
				$taxonomy );

		}
	}

	/**
	 * Add language
	 *
	 * @return void
	 */
	public function addLang() {

		global $wpdb;
		$langs = Multilingual::getLanguages();

		if ( Multilingual::isWPML() ) {


			$placeholders = array_fill( 0, count( $langs ), '%s' );
			$format       = implode( ', ', $placeholders );

			$tranlationsTable = $wpdb->prefix . 'icl_translations';

			$this->select .= $wpdb->prepare( ", (SELECT language_code
                                 FROM $tranlationsTable
                                 WHERE element_type = 'post_product'
                                 AND element_id = posts.ID
                                 AND language_code IN ($format) LIMIT 1) AS lang",
				$langs );
		}

		if ( Multilingual::isPolylang() ) {

			$this->select .= ", (SELECT slug
                                 FROM $wpdb->terms
                                 WHERE term_id = (
                                     SELECT t.term_id
                                     FROM $wpdb->terms AS t
                                     INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
                                     INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
                                     WHERE tt.taxonomy = 'language'
                                     AND tr.object_id = posts.ID
                                     LIMIT 1
                                 )) AS lang";

		}

	}

	/**
	 * Get products data
	 *
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * Get SQL
	 *
	 * @return array
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * Check if query has return only ids insetad full data
	 *
	 * @return bool
	 */
	private function onlyIDs() {
		return (bool) $this->args['ids'];
	}

}
