<?php


namespace DgoraWcas\Engines\TNTSearchMySQL\Debug;


use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Readable\Indexer as IndexerR;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Indexer as IndexerS;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Tokenizer;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\SourceQuery;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Support\Collection;

class Product {

	private $productID;
	public $product;
	private $indexerR;
	private $indexerS;

	public function __construct( $productID ) {

		$productID = absint( $productID );

		$this->product   = new \DgoraWcas\Product( $productID );
		$this->productID = $productID;
		$this->indexerR  = new IndexerR();
		$this->indexerS  = new IndexerS();

	}

	/**
	 * Get data that are saved in a readable index
	 *
	 * @return array
	 */
	public function getReadableIndexData() {

		return $this->indexerR->getSingle( $this->productID );

	}

	/**
	 * Get searchable index terms that belong to product
	 *
	 * @return array
	 */
	public function getSearchableIndexData() {

		$terms = array();
		foreach ( $this->indexerS->getWordList( $this->productID ) as $term ) {
			$terms[] = $term['term'];
		}

		return $terms;
	}

	/**
	 * Get data before saving in searchable index database using "updater" method
	 *
	 * @return array
	 * @throws \DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Exceptions\IndexNotFoundException
	 */
	public function getDataForIndexingByUpdater() {

		$terms = array();

		$document = $this->indexerS->prepareProduct( $this->productID );

		if ( ! empty( $document ) ) {


			$this->indexerS->tnt->selectIndex();
			$indexer = $this->indexerS->tnt->getIndex();

			$indexer->setTokenizer( new Tokenizer );

			$row = new Collection( $document );

			$stems = $row->map( function ( $columnContent, $columnName ) use ( $indexer, $row ) {
				return $indexer->stemText( $columnContent );
			} );

			if ( ! empty( $stems ) ) {

				foreach ( $stems as $key => $group ) {

					if ( $key === 'id' ) {
						continue;
					}

					foreach ( $group as $term ) {
						$terms[] = $term;
					}

				}

				$terms = array_unique( $terms );
				sort( $terms, SORT_STRING );
			}

		}

		return $terms;
	}

	/**
	 *  Get data before saving in searchable index database using "source" method (raw SQL)
	 *
	 * @return array
	 */
	public function getDataForIndexingBySource() {
		$terms = array();

		$source = new SourceQuery( array(
			'package' => array( $this->productID )
		) );

		$data = $source->getData();

		if ( ! empty( $data[0] ) ) {

			$this->indexerS->tnt->selectIndex();
			$indexer = $this->indexerS->tnt->getIndex();
			$indexer->setTokenizer( new Tokenizer );
			$indexer->loadConfig( [
				'scope' => array(
					'attributes' => DGWT_WCAS()->settings->getOption( 'search_in_product_attributes' ) === 'on' ? true : false,
				),
			] );

			$row = apply_filters( 'dgwt/wcas/indexer/items_row', $data[0] );

			if ( ! empty( $row['lang'] ) ) {
				unset( $row['lang'] );
			}

			if ( ! empty( $row['post_type'] ) ) {
				unset( $row['post_type'] );
			}

			if ( ! empty( $indexer->config['scope']['attributes'] ) ) {
				$row = $indexer->applyCustomAttributes( $row );
			}

			unset( $row['ID'] );

			$row = new Collection( $row );

			$stems = $row->map( function ( $columnContent, $columnName ) use ( $indexer, $row ) {
				return $indexer->stemText( $columnContent );
			} );

			if ( ! empty( $stems ) ) {

				foreach ( $stems as $key => $group ) {

					if ( $key === 'ID' ) {
						continue;
					}

					foreach ( $group as $term ) {
						$terms[] = $term;
					}

				}

				$terms = array_unique( $terms );
				sort( $terms, SORT_STRING );
			}

		}

		return $terms;
	}

	/**
	 * @param array $origin
	 * @param array $toCompare
	 *
	 */
	public function diffIndexMethod() {

		$wordlistUpdater = $this->getDataForIndexingByUpdater();
		$wordlistSQL     = $this->getDataForIndexingBySource();

		$data = array(
			'only_updater'      => array(),
			'only_source_query' => array(),
			'common'            => array_intersect( $wordlistUpdater, $wordlistSQL )
		);

		$diff1 = array_diff( $wordlistUpdater, $wordlistSQL );
		$diff2 = array_diff( $wordlistSQL, $wordlistUpdater );
		$diff  = array_merge( $diff1, $diff2 );

		if ( ! empty( $diff ) ) {
			foreach ( $diff as $term ) {

				if ( in_array( $term, $wordlistUpdater ) ) {
					$data['only_updater'][] = $term;
				} else {
					$data['only_source_query'][] = $term;
				}
			}
		}

		return $data;

	}

}
