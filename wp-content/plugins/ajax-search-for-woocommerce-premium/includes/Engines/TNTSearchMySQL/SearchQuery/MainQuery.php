<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\SearchQuery;

use DgoraWcas\Multilingual;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\TNTSearch;
use DgoraWcas\Helpers;
use DgoraWcas\Engines\TNTSearchMySQL\Config;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Tokenizer;

abstract class MainQuery {
	private $s = '';
	/**
	 * @var TNTSearch
	 */
	private $tnt;
	private $searchableLimit = PHP_INT_MAX;
	protected $searchStart = 0;
	protected $taxQuery;
	protected $tntTime = 0;
	protected $settings = array();
	protected $slots;
	private $foundProductsIds = array();
	protected $foundProducts = array();
	protected $foundTax = array();
	protected $foundVendors = array();
	protected $foundPosts = array();
	protected $lang = '';
	public $debug = false;

	/**
	 * MainQuery constructor.
	 *
	 * @param bool $debug
	 */
	public function __construct( $debug = false ) {
		$this->debug = $debug;

		$this->setSettings();
		$this->loadFilters();

		$this->slots = $this->getOption( 'suggestions_limit', 'int', 7 );

		$this->initTNT();

		$this->taxQuery = new TaxQuery();
	}

	/**
	 * Include filters directly from themes and child themes
	 *
	 * @return void
	 */
	private function loadFilters() {
		$theme = Config::getCurrentThemePath();
		$files = array(
			'ajax-search-for-woocommerce.php',
			'ajax-search-filters.php',
			'asfw-filters.php'
		);

		if ( file_exists( $theme ) ) {
			foreach ( $files as $file ) {
				if ( file_exists( $theme . $file ) ) {
					require_once $theme . $file;

					break;
				}
			}
		}

		// Internal plugins integrations
		foreach ( Config::getInternalFilterClasses() as $class ) {
			if ( class_exists( $class ) ) {
				$obj = new $class;
				$obj->init();
			}
		}

	}

	/**
	 * Set searched phrase
	 *
	 * @param string $phrase
	 *
	 * @return void
	 */
	public function setPhrase( $phrase ) {
		$charLimit = apply_filters( 'dgwt/wcas/search/input_chars_limit', 200 );

		if ( mb_strlen( $phrase ) > $charLimit ) {
			// Limit the number of characters
			$phrase = mb_substr( $phrase, 0, $charLimit );

			// Trim last word if needed
			if ( mb_substr( $phrase, - 1 ) !== ' ' ) {
				$phrase = mb_substr( $phrase, 0, mb_strrpos( $phrase, ' ' ) );
			}
		}

		$phrase  = apply_filters( 'dgwt/wcas/phrase/initial', $phrase );
		$phrase  = $this->replacePhrase( $phrase );
		$phrase  = $this->removeInPhrase( $phrase );
		$this->s = apply_filters( 'dgwt/wcas/phrase/final', $phrase );
	}

	/**
	 * Set language
	 *
	 * @param string $lang
	 *
	 * @return void
	 */
	public function setLang( $lang ) {
		if ( Multilingual::isLangCode( $lang ) ) {
			$this->lang = $lang;
			$this->tnt->setLang( $lang );
		}
	}

	/**
	 * Load settings
	 *
	 * @return void
	 */
	protected function setSettings() {
		$this->settings = Settings::getSettings();
	}

	/**
	 * Get searched phrase
	 *
	 * @param string mode - searchable or readable
	 *
	 * @return string
	 */
	public function getPhrase( $mode = 'readable' ) {
		$phrase = trim( $this->s );
		$phrase = str_replace( '  ', ' ', $phrase );

		return apply_filters( 'dgwt/wcas/search_phrase', $phrase );
	}

	/**
	 * Get language code
	 *
	 * @return string
	 */
	public function getLang() {
		return $this->lang;
	}

	/**
	 * Get option from the plugin settings
	 *
	 * @param $key
	 * @param string $type
	 * @param string $default
	 *
	 * @return bool|int|string
	 */
	protected function getOption( $key, $type = 'string', $default = '' ) {

		if ( isset( $this->settings[ $key ] ) ) {

			$value = filter_var( $this->settings[ $key ], FILTER_SANITIZE_STRING );
		} else {
			$value = $default;
		}

		switch ( $type ) {
			case 'int':
				$value = intval( $value );
				break;
			case 'bool':
				$value = boolval( $value );
				break;
		}

		return $value;
	}

	private function initTNT() {

		$this->tnt = new TNTSearch();

		$fuzzines = $this->getFuzzinessSettings();

		$config = apply_filters( 'dgwt/wcas/tnt/query/config', array(
			'debug'                  => $this->debug,
			'wordlistByKeywordLimit' => apply_filters( 'dgwt/wcas/tnt/wordlist_by_keyword_limit', 5000 ),
			'maxDocs'                => 50000,
			'asYouType'              => true,
			'fuzziness'              => $fuzzines['fuzziness'],
			'fuzzy'                  => $fuzzines['fuzzy'],
		) );

		if ( defined( 'DGWT_WCAS_TNT_WORDLIST_LIMIT' ) ) {
			$config['wordlistByKeywordLimit'] = absint( DGWT_WCAS_TNT_WORDLIST_LIMIT );
		}

		if ( defined( 'DGWT_WCAS_TNT_DOCS_LIMIT' ) ) {
			$config['maxDocs'] = absint( DGWT_WCAS_TNT_DOCS_LIMIT );
		}

		$this->setConfig( $config );

		$this->tnt->selectIndex();

		$this->tnt->setTokenizer( new Tokenizer );

	}

	/**
	 * Get fuzziness options
	 *
	 * @return array
	 */
	private function getFuzzinessSettings() {
		$fuzziness = array(
			'fuzziness' => true,
			'fuzzy'     => array(
				'fuzzy_prefix_length'  => 2,
				'fuzzy_max_expansions' => 200,
				'fuzzy_distance'       => 2
			)
		);

		$option = $this->getOption( 'fuzziness_enabled', 'string', 'normal' );

		switch ( $option ) {
			case 'soft':
				$fuzziness['fuzzy']['fuzzy_prefix_length']  = 2;
				$fuzziness['fuzzy']['fuzzy_max_expansions'] = 50;
				$fuzziness['fuzzy']['fuzzy_distance']       = 1;
				break;
			case 'normal':
				break;
			case 'hard':
				$fuzziness['fuzzy']['fuzzy_prefix_length']  = 2;
				$fuzziness['fuzzy']['fuzzy_max_expansions'] = 400;
				$fuzziness['fuzzy']['fuzzy_distance']       = 3;
				break;
			default:
				$fuzziness['fuzziness'] = false;
				$fuzziness['fuzzy']     = array();
				break;
		}

		return $fuzziness;
	}

	/**
	 * Set config fot TNT Search
	 *
	 * @return void
	 */
	public function setConfig( $config = array() ) {

		if ( isset( $config['asYouType'] ) ) {
			$this->tnt->asYouType = boolval( $config['asYouType'] );
			unset( $config['asYouType'] );
		}

		$fuzziness = null;
		if ( isset( $config['fuzziness'] ) ) {
			$fuzziness            = boolval( $config['fuzziness'] );
			$this->tnt->fuzziness = $fuzziness;
			unset( $config['fuzziness'] );
		}

		if ( isset( $config['fuzzy'] ) && is_array( $config['fuzzy'] ) ) {
			if ( isset( $fuzziness ) && is_bool( $fuzziness ) ) {
				$this->tnt->fuzzy = $fuzziness;
			}
		}

		if ( isset( $config['fuzzy'] ) && is_array( $config['fuzzy'] ) ) {
			$this->tnt->fuzzy = true;

			if ( isset( $config['fuzzy']['fuzzy_prefix_length'] ) ) {
				$this->tnt->fuzzy_prefix_length = intval( $config['fuzzy']['fuzzy_prefix_length'] );
				unset( $config['fuzzy']['fuzzy_prefix_length'] );
			}

			if ( isset( $config['fuzzy']['fuzzy_max_expansions'] ) ) {
				$this->tnt->fuzzy_max_expansions = intval( $config['fuzzy']['fuzzy_max_expansions'] );
				unset( $config['fuzzy']['fuzzy_max_expansions'] );
			}

			if ( isset( $config['fuzzy']['fuzzy_distance'] ) ) {
				$this->tnt->fuzzy_distance = intval( $config['fuzzy']['fuzzy_distance'] );
				unset( $config['fuzzy']['fuzzy_distance'] );
			}
		}


		if ( isset( $config['fuzzy'] ) ) {
			unset( $config['fuzzy'] );
		}

		$this->tnt->loadConfig( $config );
	}

	/**
	 * Search products
	 *
	 * @param int $limit
	 *
	 * @return bool true if something was found
	 */
	public function searchProducts( $limit = null ) {

		$found = false;

		$max    = isset( $limit ) ? $limit : $this->searchableLimit;
		$phrase = $this->getPhrase( 'searchable' );
		$res    = $this->tnt->searchFibo( $phrase, $max );

		if ( ! empty( $res['ids'] ) && is_array( $res['ids'] ) ) {
			$this->foundProductsIds = apply_filters( 'dgwt/wcas/tnt/search_results/ids', $res['ids'], $phrase );

			$this->tntTime = $this->tntTime + (float) ( str_replace( ' ms', '', $res['execution_time'] ) );
			$found         = true;
		}

		return $found;
	}

	/**
	 * Search no-products items (taxonomies)
	 *
	 * @return bool true if something was found
	 */
	public function searchTaxonomy() {

		$found = false;

		if ( $this->taxQuery->isEnabled() ) {

			if ( ! empty( $this->lang ) ) {
				$this->taxQuery->setLang( $this->lang );
			}

			$this->foundTax = $this->taxQuery->search( $this->getPhrase() );

			if ( ! empty( $this->foundTax ) ) {
				$found = true;
			}

		}

		return $found;
	}

	/**
	 * Search no-products items (vendors)
	 *
	 * @return bool true if something was found
	 */
	public function searchVendors() {

		$found = false;

		$vendorQuery = new VendorQuery();
		$vendorQuery->init();

		if ( $vendorQuery->isEnabled() ) {

			$this->foundVendors = $vendorQuery->search( $this->getPhrase() );

			if ( ! empty( $this->foundVendors ) ) {
				$found = true;
			}

		}

		return $found;
	}

	/**
	 * Search in Posts
	 *
	 * @return bool true if something was found
	 */
	public function searchPosts() {

		$found = false;

		$postTypes = $this->getExtraPostTypes();
		if ( ! empty( $postTypes ) ) {

			foreach ( $postTypes as $postType ) {

				$max    = isset( $limit ) ? $limit : $this->searchableLimit;
				$phrase = $this->getPhrase( 'searchable' );
				$this->tnt->setPostType( $postType );
				$res = $this->tnt->searchFibo( $phrase, $max );

				if ( ! empty( $res['ids'] ) && is_array( $res['ids'] ) ) {
					$ids = apply_filters( 'dgwt/wcas/tnt/search_results' . $postType . '/ids', $res['ids'], $phrase );

					$cp = new CustomPost( $ids, $postType, $this->getPhrase() );
					if ( ! empty( $this->getLang() ) ) {
						$cp->setLang( $this->getLang() );
					}
					$this->foundPosts[ $postType ] = $cp->getResutls();

					$this->tntTime = $this->tntTime + (float) ( str_replace( ' ms', '', $res['execution_time'] ) );
					$found         = true;
				}

			}

		}

		return $found;
	}

	/**
	 * Check if current query has relevant IDs
	 *
	 * @return  bool
	 */
	public function hasResults() {

		return ! empty( $this->foundProductsIds ) || ! empty( $this->foundTax ) || ! empty( $this->foundPosts ) || ! empty( $this->foundVendors );
	}

	/**
	 *
	 * Set found posts
	 * @return void
	 */
	private function setFoundProducts() {

		global $wpdb;

		if ( ! empty( $this->foundProductsIds ) ) {

			$placeholders = array_fill( 0, count( $this->foundProductsIds ), '%d' );
			$format       = implode( ', ', $placeholders );

			$sql = $wpdb->prepare( "
                SELECT *
                FROM " . $wpdb->prefix . Config::READABLE_INDEX . "
                WHERE post_id IN ($format)
                AND name != ''
                ",
				$this->foundProductsIds
			);

			$r = $wpdb->get_results( $sql );

			if ( ! empty( $r ) && is_array( $r ) && ! empty( $r[0] ) && ! empty( $r[0]->post_id ) ) {
				foreach ( $r as $index => $value ) {
					$r[ $index ]->meta = maybe_unserialize( $value->meta );
				}
				$this->foundProducts = apply_filters( 'dgwt/wcas/tnt/search_results/products', $r, $this->getPhrase(), $this->getLang() );
			}

		}

	}

	/**
	 * Get products from IDs
	 *
	 * @param string $orderBy
	 * @param string $order
	 *
	 * @return array
	 */
	public function getProducts( $orderBy = 'relevance', $order = '' ) {

		if ( empty( $this->foundProducts ) ) {
			$this->setFoundProducts();
		}

		if ( ! empty( $this->foundProducts ) ) {
			$this->sortResults( $orderBy, $order );
		}

		return $this->foundProducts;
	}

	/**
	 * Sort products
	 *
	 * @param string $orderBy
	 * @param string $order
	 *
	 * @param string $orderBy
	 */
	private function sortResults( $orderBy, $order = '' ) {
		// Something wrong with the query vars? Try to read order from the URL
		if ( empty( $orderBy ) || ! is_string( $orderBy ) ) {

			if ( ! empty( $_GET['orderby'] ) ) {
				$orderBy = sanitize_title( $_GET['orderby'] );

				if ( strpos( $orderBy, '-asc' ) !== false ) {
					$order = 'asc';
				}
				if ( strpos( $orderBy, '-desc' ) !== false ) {
					$order = 'desc';
				}
			} else {
				return;
			}

		}

		$orderBy = str_replace( array( '-asc', '-desc' ), '', $orderBy );

		if ( in_array( $order, array( 'asc', 'desc' ) ) ) {
			if ( $orderBy === 'date' ) {
				$orderBy = 'date-' . $order;
			}
			if ( $orderBy === 'price' ) {
				$orderBy = 'price-' . $order;
			}
		}

		$orderBy = apply_filters( 'dgwt/wcas/tnt/sort_products/order_by', $orderBy );

		switch ( $orderBy ) {
			case 'relevance':
				$this->orderByWeight();
				break;
			case 'date ID':
			case 'date-desc':

				usort( $this->foundProducts, function ( $a, $b ) {
					$a = strtotime( $a->created_date );
					$b = strtotime( $b->created_date );
					if ( $a == $b ) {
						return 0;
					}

					return ( $a < $b ) ? 1 : - 1;
				} );

				break;
			case 'price-asc':
				usort( $this->foundProducts, function ( $a, $b ) {
					if ( $a->price == $b->price ) {
						return 0;
					}

					return ( $a->price < $b->price ) ? - 1 : 1;
				} );

				break;

			case 'price-desc':
				usort( $this->foundProducts, function ( $a, $b ) {
					if ( $a->price == $b->price ) {
						return 0;
					}

					return ( $a->price < $b->price ) ? 1 : - 1;
				} );

				break;

			case 'rating':
				usort( $this->foundProducts, function ( $a, $b ) {
					if ( $a->average_rating == $b->average_rating ) {
						return 0;
					}

					return ( $a->average_rating < $b->average_rating ) ? 1 : - 1;
				} );

				break;

			case 'popularity':
			case 'popularity-desc':

				usort( $this->foundProducts, function ( $a, $b ) {
					if ( $a->total_sales == $b->total_sales ) {
						return 0;
					}

					return ( $a->total_sales < $b->total_sales ) ? 1 : - 1;
				} );

				break;

		}

		$this->foundProducts = apply_filters( 'dgwt/wcas/tnt/sort_products', $this->foundProducts, $orderBy );
	}

	/**
	 * Order found products by weights
	 *
	 * @return void
	 */
	private function orderByWeight() {
		$i = 0;

		foreach ( $this->foundProducts as $product ) {

			$score = 0;

			$score += Helpers::calcScore( $this->getPhrase(), $product->name );

			// SKU
			if ( $this->searchIn( 'sku' ) ) {

				$score += Helpers::calcScore( $this->getPhrase(), $product->sku, array(
					'check_similarity' => false
				) );
				$score += Helpers::calcScore( $this->getPhrase(), $product->sku_variations, array(
					'check_similarity' => false,
					'check_position'   => false,
					'score_containing' => 80
				) );
			}

			// Attributes
			if ( $this->searchIn( 'attributes' ) ) {
				$score += Helpers::calcScore( $this->getPhrase(), $product->attributes, array(
					'check_similarity' => false,
					'check_position'   => false,
					'score_containing' => 80
				) );
			}

			$this->foundProducts[ $i ]->score = apply_filters('dgwt/wcas/tnt/product/score', (float) $score, $product->post_id, $product, $this );

			$i ++;
		}

		usort( $this->foundProducts, array( 'DgoraWcas\Helpers', 'cmpSimilarity' ) );

	}

	/**
	 * Count total results
	 *
	 * @return int
	 */
	public function getTotalFound() {
		return count( $this->foundProductsIds );
	}

	/**
	 * Get extra post types to search
	 *
	 * @return array
	 */
	public function getExtraPostTypes() {
		$postTypes = array();

		if ( array_key_exists( 'show_matching_posts', $this->settings )
		     && $this->settings['show_matching_posts'] === 'on' ) {

			$postTypes[] = 'post';
		}

		if ( array_key_exists( 'show_matching_pages', $this->settings )
		     && $this->settings['show_matching_pages'] === 'on' ) {

			$postTypes[] = 'page';
		}


		return apply_filters( 'dgwt/wcas/tnt/search_post_types', $postTypes );
	}

	/**
	 * Check the search scope
	 *
	 * @param $scope
	 *
	 * @return bool
	 */
	public function searchIn( $scope ) {
		$inScope = false;

		switch ( $scope ) {
			case 'content':
			case 'description':
				if ( array_key_exists( 'search_in_product_content', $this->settings ) ) {
					$inScope = $this->settings['search_in_product_content'] === 'on' ? true : false;
				}
				break;
			case 'excerpt':
				if ( array_key_exists( 'search_in_product_excerpt', $this->settings ) ) {
					$inScope = $this->settings['search_in_product_excerpt'] === 'on' ? true : false;
				}
				break;
			case 'sku':

				if ( array_key_exists( 'search_in_product_sku', $this->settings ) ) {
					$inScope = $this->settings['search_in_product_sku'] === 'on' ? true : false;
				}
				break;
			case 'attributes':
				if ( array_key_exists( 'search_in_product_attributes', $this->settings ) ) {
					$inScope = $this->settings['search_in_product_attributes'] === 'on' ? true : false;
				}
				break;
		}

		return $inScope;
	}

	/**
	 * Replace phrase to another
	 *
	 * @param string $phrase
	 *
	 * @return string
	 */
	private function replacePhrase( $phrase ) {
		$phrase     = trim( mb_strtolower( $phrase ) );
		$to_replace = apply_filters( 'dgwt/wcas/phrase/replace', array() );
		if ( empty( $to_replace ) || ! is_array( $to_replace ) ) {
			return $phrase;
		}
		if ( isset( $to_replace[ $phrase ] ) ) {
			return $to_replace[ $phrase ];
		}

		return $phrase;
	}

	/**
	 * Remove words in phrase
	 *
	 * @param string $phrase
	 *
	 * @return string
	 */
	private function removeInPhrase( $phrase ) {
		$phrase    = trim( mb_strtolower( $phrase ) );
		$to_remove = apply_filters( 'dgwt/wcas/phrase/remove', array() );
		if ( empty( $to_remove ) || ! is_array( $to_remove ) ) {
			return $phrase;
		}
		foreach ( $to_remove as $word ) {
			$phrase = str_replace( $word, '', $phrase );
		}
		$phrase = trim( $phrase );

		return $phrase;
	}

	/**
	 * Wrapper for tokenizer
	 *
	 * @param string $phrase
	 *
	 * @return array
	 */
	public function breakIntoTokens( $phrase ) {

		return $this->tnt->breakIntoTokens( $phrase );
	}
}
