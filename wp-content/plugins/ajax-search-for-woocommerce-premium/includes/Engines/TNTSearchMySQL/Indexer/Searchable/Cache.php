<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable;

use DgoraWcas\Helpers;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\TNTSearch;
use \PDO;

class Cache {
	/**
	 * @var TNTSearch
	 */
	private $tnt;
	private $enabled;

	/**
	 * Cache constructor
	 *
	 * @param $tnt
	 */
	public function __construct( $tnt ) {
		$this->tnt = $tnt;

		$this->setStatus();
	}

	/**
	 * Set cache status
	 */
	private function setStatus() {
		$enabled = true;

		if ( defined( 'DGWT_WCAS_SEARCH_CACHE' ) ) {
			$enabled = (bool) DGWT_WCAS_SEARCH_CACHE;
		}

		$enabled = apply_filters( 'dgwt/wcas/tnt/search_cache', $enabled );

		$this->enabled = Helpers::doesDbSupportJson__premium_only() && $enabled;
	}

	/**
	 * Get cache status
	 *
	 * @return bool
	 */
	public function isEnabled() {
		return (bool) $this->enabled;
	}

	/**
	 * Set value into cache
	 *
	 * @param string $key
	 * @param string $value JSON
	 *
	 * @return bool
	 */
	public function set( $key, $value ) {
		if ( ! $this->enabled ) {
			return true;
		}

		$cacheTable = $this->tnt->getTableName( 'cache' );

		$query = "INSERT INTO $cacheTable (`cache_key`, `cache_value`) VALUES (:key, :value) ON DUPLICATE KEY UPDATE `cache_key` = VALUES(`cache_key`), `cache_value` = VALUES(`cache_value`)";

		$stmt = $this->tnt->index->prepare( $query );
		$stmt->bindValue( ':key', $key );
		$stmt->bindValue( ':value', $value );

		return $stmt->execute();
	}

	/**
	 * Get value from cache
	 *
	 * @param $key
	 *
	 * @return bool|mixed
	 */
	public function get( $key ) {
		if ( ! $this->enabled ) {
			return false;
		}

		$cacheTable = $this->tnt->getTableName( 'cache' );

		$stmt = $this->tnt->index->prepare( "SELECT * FROM $cacheTable WHERE cache_key = :key LIMIT 1" );
		$stmt->bindValue( ':key', $key );
		$stmt->execute();

		$result = $stmt->fetch( PDO::FETCH_ASSOC );
		if ( ! empty( $result['cache_value'] ) ) {
			$value = json_decode( $result['cache_value'] );

			return json_last_error() === JSON_ERROR_NONE ? $value : false;
		}

		return false;
	}

	/**
	 * Delete cache entry by part of it's value
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	public function deleteByValue( $value ) {
		if ( ! $this->enabled ) {
			return true;
		}

		$cacheTable = $this->tnt->getTableName( 'cache' );

		$stmt = $this->tnt->index->prepare( "DELETE FROM $cacheTable WHERE JSON_CONTAINS(`cache_value`, :value) = 1" );
		$stmt->bindValue( ':value', $value );

		return $stmt->execute();
	}
}
