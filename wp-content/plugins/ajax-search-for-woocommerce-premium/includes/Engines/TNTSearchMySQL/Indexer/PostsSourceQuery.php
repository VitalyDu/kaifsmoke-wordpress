<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer;

use DgoraWcas\Helpers;
use DgoraWcas\Multilingual;

class PostsSourceQuery {

	private $args = array();

	private $data = array();
	private $postTypes = array( 'post' );

	private $select = '';
	private $join = '';
	private $where = '';

	private $request;

	public function __construct( $args = array() ) {
		$this->setArgs( $args );
		$this->setPostTypes();

		$this->setData();
	}

	private function setArgs( $args ) {

		$defaults = array(
			'postTypes' => array( 'post' ),
			'ids'       => false, // return only IDS
			'trigrams'  => false,
			'package'   => array()
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

		// Set range of products set
		if ( ! empty( $this->args['package'] ) ) {
			$this->narrowDownToTheSet();
		}

		// Add post full description
		if ( apply_filters( 'dgwt/wcas/tnt/post_source_query/description', false ) ) {
			$this->addDescription();
		}

		$this->addPostTypes();

		if ( Multilingual::isMultilingual() ) {
			$this->addLang();
		}

		$postTypes = $this->getPostTypes();

		$placeholders = array_fill( 0, count( $postTypes ), '%s' );
		$format       = implode( ', ', $placeholders );

		$this->where .= $wpdb->prepare( " AND post_type IN ($format)", $postTypes );
		$this->where .= " AND post_status = 'publish' ";

		$onlyIDs = $this->onlyIDs();

		if ( $onlyIDs ) {
			$this->select = 'posts.ID';
		}

		$this->select = apply_filters( 'dgwt/wcas/tnt/post_source_query/select', $this->select, $this, $onlyIDs );
		$this->join   = apply_filters( 'dgwt/wcas/tnt/post_source_query/join', $this->join, $this, $onlyIDs );
		$this->where  = apply_filters( 'dgwt/wcas/tnt/post_source_query/where', $this->where, $this, $onlyIDs );

		$this->request = "SELECT $this->select
                FROM $wpdb->posts posts
                $this->join
                WHERE  1=1
                $this->where
               ";
		$rows          = $wpdb->get_results( apply_filters( 'dgwt/wcas/tnt/post_source_query/request', $this->request, $this, $onlyIDs ), ARRAY_A );

		if ( ! empty( $rows ) && ! is_wp_error( $rows ) ) {
			$this->data = $rows;
		}

		$this->data = apply_filters( 'dgwt/wcas/tnt/post_source_query/data', $this->data, $this, $onlyIDs );
	}

	/**
	 * Narrow down to the specific posts set
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
	 * Add post type info
	 *
	 * @return string
	 */
	private function addPostTypes() {
		global $wpdb;

		$this->select .= ", (SELECT post_type
                             FROM $wpdb->posts
                             WHERE posts.ID = ID) AS post_type";
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


			$langsPlaceholders = array_fill( 0, count( $langs ), '%s' );
			$langsFormat       = implode( ', ', $langsPlaceholders );

			$tranlationsTable = $wpdb->prefix . 'icl_translations';

			$postTypes       = $this->getPostTypes();
			$wpmlObjectTypes = array();
			foreach ( $postTypes as $postType ) {
				$wpmlObjectTypes[] = 'post_' . $postType;
			}

			$postTypesPlaceholders = array_fill( 0, count( $wpmlObjectTypes ), '%s' );
			$postTypesFormat       = implode( ', ', $postTypesPlaceholders );

			$replace = array_merge( $wpmlObjectTypes, $langs );

			$this->select .= $wpdb->prepare( ", (SELECT language_code
                                 FROM $tranlationsTable
                                 WHERE element_type IN ($postTypesFormat)
                                 AND element_id = posts.ID
                                 AND language_code IN ($langsFormat) LIMIT 1) AS lang",
				$replace );
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

	/**
	 * Set post types
	 *
	 * @return void
	 */
	private function setPostTypes() {

		$pt     = $this->args['postTypes'];
		$output = array();

		if ( ! empty( $pt ) && is_array( $pt ) ) {
			foreach ( $pt as $postType ) {
				$output[] = sanitize_key( $postType );
			}
		}

		if ( empty( $output ) ) {
			$output = array( 'post' );
		}

		$this->postTypes = $output;
	}

	/**
	 * Get post types
	 *
	 * @return array
	 */
	private function getPostTypes() {
		return $this->postTypes;
	}

}
