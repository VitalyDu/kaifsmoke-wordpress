<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch;

use PDO;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Exceptions\IndexNotFoundException;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Indexer\TNTIndexer;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Stemmer\PorterStemmer;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Support\Collection;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Support\Expression;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Support\Highlighter;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Support\Tokenizer;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Support\TokenizerInterface;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Connectors\MySqlConnector;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Database;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Cache;
use DgoraWcas\Engines\TNTSearchMySQL\Debug\Debugger;
use DgoraWcas\Multilingual;

class TNTSearch
{
    public $config;
    public $asYouType = false;
    public $tokenizer = null;
	/**
	 * @var PDO
	 */
    public $index = null;
    public $stemmer = null;
    public $fuzziness = false;
    public $fuzzy_prefix_length = 2;
    public $fuzzy_max_expansions = 50;
    public $fuzzy_distance = 2;
    public $debug = false;
	/**
	 * @var Cache
	 */
	public $cache;
    protected $lang = '';
    protected $postType = '';
    protected $dbh = null;

    /**
     * @param array $config
     *
     * @see https://github.com/teamtnt/tntsearch#examples
     */
    public function loadConfig(array $config)
    {
        if(!empty($config['debug'])){
            $this->debug = true;
        }

        $this->config            = $config;
    }

    public function __construct()
    {
	    $this->tokenizer = new Tokenizer;
	    $this->cache     = new Cache( $this );
    }

    /**
     * @param PDO $dbh
     */
    public function setDatabaseHandle(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

	/**
	 * Get all limitations
	 *
	 * @return int
	 */
	private function getLimits( $target ) {

		$limits = array(
			'wordlist' => isset( $this->config['wordlistByKeywordLimit'] ) ? absint( $this->config['wordlistByKeywordLimit'] ) : 5000,
			'doclist'  => isset( $this->config['maxDocs'] ) ? absint( $this->config['maxDocs'] ) : 20000
		);

		return array_key_exists( $target, $limits ) ? $limits[ $target ] : 0;
	}

    /**
     * Set lang
     *
     * @param string $lang
     */
    public function setLang($lang)
    {
        if (
            ! empty($lang)
            && is_string($lang)
            && Multilingual::isLangCode($lang)
        ) {
            $this->lang = $lang;
        }
    }

    /**
     * Set post type
     *
     * @param string $postType
     */
    public function setPostType($postType)
    {
        if (
            ! empty($postType)
            && is_string($postType)
            && preg_match('/^[a-z_\-]{1,50}$/', $postType)
        ) {
            $this->postType = $postType;
        }
    }

    /**
     * @param TokenizerInterface $tokenizer
     */
    public function setTokenizer(TokenizerInterface $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * @param string $indexName
     * @param boolean $disableOutput
     *
     * @return TNTIndexer
     */
    public function createIndex($deprecated = '', $disableOutput = false)
    {
        $indexer = new TNTIndexer;
        $indexer->loadConfig($this->config);
        $indexer->disableOutput = $disableOutput;

        if ($this->dbh) {
            $indexer->setDatabaseHandle($this->dbh);
        }

        return $indexer->createIndex();
    }

    /**
     * @throws IndexNotFoundException
     */
    public function selectIndex()
    {
        $pdo         = new MySqlConnector();
        $this->index = $pdo->connect(Database::getConfig());

        $this->setStemmer();
    }

    /**
     * @param string $phrase
     * @param int $numOfResults
     *
     * @return array
     */
    public function search($phrase, $numOfResults = 100)
    {
        $startTimer = microtime(true);
        $keywords   = $this->breakIntoTokens($phrase);
        $keywords   = new Collection($keywords);

        $keywords = $keywords->map(function ($keyword) {
            return $this->stemmer->stem($keyword);
        });

        $tfWeight  = 1;
        $dlWeight  = 0.5;
        $docScores = [];
        $count     = $this->totalDocumentsInCollection();

        foreach ($keywords as $index => $term) {
            $isLastKeyword = ($keywords->count() - 1) == $index;
            $df            = $this->totalMatchingDocuments($term, $isLastKeyword);
            $idf           = log($count / max(1, $df));
            foreach ($this->getAllDocumentsForKeyword($term, $isLastKeyword) as $document) {
                $docID             = $document['doc_id'];
                $tf                = $document['hit_count'];
                $num               = ($tfWeight + 1) * $tf;
                $denom             = $tfWeight
                                     * ((1 - $dlWeight) + $dlWeight)
                                     + $tf;
                $score             = $idf * ($num / $denom);
                $docScores[$docID] = isset($docScores[$docID]) ?
                    $docScores[$docID] + $score : $score;
            }
        }

        arsort($docScores);

        $docs = new Collection($docScores);

        $totalHits = $docs->count();
        $docs      = $docs->map(function ($doc, $key) {
            return $key;
        })->take($numOfResults);
        $stopTimer = microtime(true);

        return [
            'ids'            => array_keys($docs->toArray()),
            'hits'           => $totalHits,
            'execution_time' => round($stopTimer - $startTimer, 7) * 1000 . " ms"
        ];
    }

    /**
     * @param string $phrase
     * @param int $numOfResults
     *
     * @return array
     */
    public function searchBoolean($phrase, $numOfResults = 100)
    {

        $keywords    = $this->breakIntoTokens($phrase);
        $lastKeyword = end($keywords);

        $stack      = [];
        $startTimer = microtime(true);

        $expression = new Expression;

        $postfix = !empty($keywords) ? $expression->toPostfix($this->prepareExpression($keywords)) : array();

        if($this->debug){
            Debugger::log('<b>Phrase:</b> ' . var_export($phrase, true), 'product-search-flow');
            Debugger::log('<b>Keywords:</b> <pre>' . var_export($keywords, true) . '</pre>', 'product-search-flow');
            Debugger::log('<b>Tokens:</b> <pre>' . var_export($postfix, true) . '</pre>', 'product-search-flow');
        }

        foreach ($postfix as $token) {
            if ($token == '&') {

                $right  = array_pop($stack);
                $left = array_pop($stack);

                if($this->debug){
                    $debugOutput = 'INTERSECT START <br />';
                }

                if (is_string($right)) {
                    $rightWord = $right;
                    $isLastKeyword = $right == $lastKeyword;
                    $right         = $this->getAllDocumentsForKeyword($this->stemmer->stem($right), $isLastKeyword)
                        ->pluck('doc_id');

                    if($this->debug){
                        $debugOutput .= 'INTERSECT right keyword: ' . $rightWord . ' | total: ' . count($right) . ' | ids: ' . implode(',', $right) . '<br />';
                    }
                }

                if (is_string($left)) {
                    $leftWord = $left;
                    $isLastKeyword = $left == $lastKeyword;
                    $left          = $this->getAllDocumentsForKeyword($this->stemmer->stem($left), $isLastKeyword)
                                          ->pluck('doc_id');

                    if($this->debug){
                        $debugOutput .= 'INTERSECT left keyword: ' . $leftWord . ' | total: ' . count($left) . ' | ids: ' . implode(',', $left) . '<br />';
                    }
                }

                if (is_null($right)) {
                    $right = [];
                }

                if (is_null($left)) {
                    $left = [];
                }

                $stack[] = array_values(array_intersect($right, $left));

                if($this->debug){

                    if(is_array($stack) && isset($stack[0])){

                        $debugOutput .= 'INTERSECT ' . ' common: ' . implode(',', array_unique($stack[0])) . '<br />';
                    }

                    $debugOutput .= 'INTERSECT END<br /><br />';
                    Debugger::log($debugOutput, 'product-search-flow');
                }

            } elseif ($token == '|') {
                $right  = array_pop($stack);
                $left = array_pop($stack);

                if($this->debug){
                    $debugOutput = 'MERGE START <br />';
                }

                if (is_string($right)) {
                    $rightWord = $right;
                    $isLastKeyword = $right == $lastKeyword;
                    $right         = $this->getAllDocumentsForKeyword($this->stemmer->stem($right), $isLastKeyword)
                                          ->pluck('doc_id');

                    if($this->debug){
                        $debugOutput .= 'MERGE right keyword: ' . $rightWord . ' | total: ' . count($right) . ' | ids: ' . implode(',', $right) . '<br />';
                    }
                }

                if (is_string($left)) {
                    $leftWord = $left;
                    $isLastKeyword = $left == $lastKeyword;
                    $left          = $this->getAllDocumentsForKeyword($this->stemmer->stem($left), $isLastKeyword)
                        ->pluck('doc_id');

                    if($this->debug){
                        $debugOutput .= 'MERGE left keyword: ' . $leftWord . ' | total: ' . count($left) . ' | ids: ' . implode(',', $left) . '<br />';
                    }
                }

                if (is_null($right)) {
                    $right = [];
                }

                if (is_null($left)) {
                    $left = [];
                }

                $stack[] = array_unique(array_merge($right, $left));

                if($this->debug){

                    if(is_array($stack) && isset($stack[0])){

                        $debugOutput .= 'MERGE ' . ' sum: ' . implode(',', $stack[0]) . '<br />';
                    }

                    $debugOutput .= 'MERGE END<br /><br />';
                    Debugger::log($debugOutput, 'product-search-flow');
                }

            } elseif ($token == '~') {
                $left = array_pop($stack);
                if (is_string($left)) {
                    $left = $this->getAllDocumentsForWhereKeywordNot($this->stemmer->stem($left), true)
                                 ->pluck('doc_id');
                }
                if (is_null($left)) {
                    $left = [];
                }
                $stack[] = $left;
            } else {
                $stack[] = $token;
            }
        }
        if (count($stack)) {
            $docs = new Collection($stack[0]);
        } else {
            $docs = new Collection;
        }

        $docs = $docs->take($numOfResults);

        $stopTimer = microtime(true);


        return [
            'ids'            => $docs->toArray(),
            'hits'           => $docs->count(),
            'execution_time' => round($stopTimer - $startTimer, 7) * 1000 . " ms"
        ];
    }

	/**
	 * Search documents
	 *
	 * Assumptions:
	 * - at the beginning, we sort the keywords by length (from longest)
	 * - when searching for documents for the next keywords, we take into account the previous results
	 * - we stop searching when there are no more results for any of the keywords
	 *
	 * @param string $phrase
	 * @param int $numOfResults
	 *
	 * @return array
	 */
	public function searchFibo( $phrase, $numOfResults = 100 ) {
		$startTimer = microtime( true );
		$keywords   = $this->breakIntoTokens( $phrase );
		$keywords   = new Collection( $keywords );

		if ( $this->debug ) {
			Debugger::log( '<b>Phrase:</b> ' . var_export( $phrase, true ) . '<br /><br />', 'product-search-flow' );
			Debugger::log( '<b>Keywords after tokenization:</b> <pre>' . var_export( $keywords->toArray(), true ) . '</pre>', 'product-search-flow' );
		}

		$keywords = $keywords->map( function ( $keyword ) {
			return $this->stemmer->stem( $keyword );
		} );

		$last = $keywords->last();

		if ( $this->debug ) {
			Debugger::log( '<b>Keywords after stemmer and before sorting:</b> <pre>' . var_export( $keywords->toArray(), true ) . '</pre>', 'product-search-flow' );
			Debugger::log( '<b>Last keyword:</b> ' . var_export( $last, true ) . '<br /><br />', 'product-search-flow' );
		}

		$keywords->sortWith( 'usort', 'DgoraWcas\Helpers::sortFromLongest' );

		if ( $this->debug ) {
			Debugger::log( '<b>Keywords after sorting:</b> <pre>' . var_export( $keywords->toArray(), true ) . '</pre>', 'product-search-flow' );
		}

		$documentsIds = null;

		foreach ( $keywords as $index => $term ) {
			$isLastKeyword = $term === $last;
			// Break if there are no results for previous keywords
			if ( is_array( $documentsIds ) && empty( $documentsIds ) ) {
				break;
			}
			$result = $this->getAllDocumentIdsForKeyword( $term, $isLastKeyword, $documentsIds );
			if ( $this->debug ) {
				$keywordLike = $this->getKeywordLikeFormat($term, $isLastKeyword);
				if ( count( $result ) < 5000 ) {
					Debugger::log( 'Partial results for: <b>' . $term . '</b> | SQL LIKE statement ' . $keywordLike . ' | isLastKeyword: ' . ( $isLastKeyword ? 'true' : 'false' ) . ' | total: ' . count( $result ) . ' | ids: ' . implode( ',', $result ) . '<br /><br />', 'product-search-flow' );
				} else {
					Debugger::log( 'Partial results for: <b>' . $term . '</b> | SQL LIKE statement ' . $keywordLike . ' | isLastKeyword: ' . ( $isLastKeyword ? 'true' : 'false' ) . ' | total: ' . count( $result ) . '<br /><br />', 'product-search-flow' );
				}
			}
			if ( $index === 0 ) {
				$documentsIds = $result;
			} else {
				$documentsIds = array_intersect( $documentsIds, $result );
			}
		}

		// Allow following code to run properly when there is no results.
		$documentsIds = is_null( $documentsIds ) ? array() : $documentsIds;

		$docs = new Collection( $documentsIds );

		$totalHits = $docs->count();
		$docs      = $docs->take( $numOfResults );
		$stopTimer = microtime( true );

		return [
			'ids'            => array_values( $docs->toArray() ),
			'hits'           => $totalHits,
			'execution_time' => round( $stopTimer - $startTimer, 7 ) * 1000 . " ms"
		];
	}

	/**
	 * Search documents with sorting by BM25 algorithm
	 *
	 * @param string $phrase
	 * @param int $numOfResults
	 *
	 * @return array
	 */
	public function searchFiboBM25( $phrase, $numOfResults = 100 ) {
		$startTimer = microtime( true );
		$keywords   = $this->breakIntoTokens( $phrase );
		$keywords   = new Collection( $keywords );

		if ( $this->debug ) {
			Debugger::log( '<b>Phrase:</b> ' . var_export( $phrase, true ), 'product-search-flow' );
			Debugger::log( '<b>Keywords before tokenization:</b> <pre>' . var_export( $keywords->toArray(), true ) . '</pre>', 'product-search-flow' );
		}

		$keywords = $keywords->map( function ( $keyword ) {
			return $this->stemmer->stem( $keyword );
		} );

		if ( $this->debug ) {
			Debugger::log( '<b>Keywords after stemmer and before sorting:</b> <pre>' . var_export( $keywords->toArray(), true ) . '</pre>', 'product-search-flow' );
		}

		$last = $keywords->last();
		$keywords->sortWith( 'usort', 'DgoraWcas\Helpers::sortFromLongest' );

		if ( $this->debug ) {
			Debugger::log( '<b>Keywords after sorting:</b> <pre>' . var_export( $keywords->toArray(), true ) . '</pre>', 'product-search-flow' );
		}

		$tfWeight     = 1;
		$dlWeight     = 0.5;
		$docScores    = [];
		$count        = $this->totalDocumentsInCollection();
		$documentsIds = null;

		foreach ( $keywords as $index => $term ) {
			$isLastKeyword = $term === $last;
			// Break if there are no results for previous keywords
			if ( is_array( $documentsIds ) && empty( $documentsIds ) ) {
				break;
			}
			$df        = $this->totalMatchingDocuments( $term, $isLastKeyword );
			$idf       = log( $count / max( 1, $df ) );
			$documents = $this->getAllDocumentsForKeyword( $term, $isLastKeyword, $documentsIds );
			if ( $this->debug ) {
				if ($documents->count() < 5000) {
					Debugger::log( 'Partial results for: ' . $term . ' | total: ' . count( $documents->pluck( 'doc_id' ) ) . ' | ids: ' . implode( ',', $documents->pluck( 'doc_id' ) ) . '<br /><br />', 'product-search-flow' );
				} else {
					Debugger::log( 'Partial results for: ' . $term . ' | total: ' . count( $documents->pluck( 'doc_id' ) ) . '<br /><br />', 'product-search-flow' );
				}
			}

			foreach ( $documents as $document ) {
				$docID               = $document['doc_id'];
				$tf                  = $document['hit_count'];
				$num                 = ( $tfWeight + 1 ) * $tf;
				$denom               = $tfWeight
				                       * ( ( 1 - $dlWeight ) + $dlWeight )
				                       + $tf;
				$score               = $idf * ( $num / $denom );
				$docScores[ $docID ] = isset( $docScores[ $docID ] ) ?
					$docScores[ $docID ] + $score : $score;
			}

			$resultIds = $documents->pluck( 'doc_id' );
			$resultIds = array_unique( $resultIds );
			$resultArr = array();
			foreach ( $resultIds as $id ) {
				$resultArr[ $id ] = $id;
			}
			$docScores    = array_intersect_key( $docScores, $resultArr );
			$documentsIds = array_keys( $docScores );
		}

		arsort( $docScores );

		$docs = new Collection( $docScores );

		$totalHits = $docs->count();
		$docs      = $docs->map( function ( $doc, $key ) {
			return $key;
		} )->take( $numOfResults );
		$stopTimer = microtime( true );

		return [
			'ids'            => array_keys( $docs->toArray() ),
			'hits'           => $totalHits,
			'execution_time' => round( $stopTimer - $startTimer, 7 ) * 1000 . " ms"
		];
	}

    /**
     * Prepare search expression for postfix
     *
     * @param array $keywords
     * @return string
     */
    public function prepareExpression($keywords){

        if(count($keywords) < 2 || !empty($chars)) {
            return '|' . implode(' ', $keywords);
        }

        $chars = method_exists($this->tokenizer, 'getSpecialChars') ? $this->tokenizer->getSpecialChars() : array();

        $pieces = array();

        foreach ($keywords as $keyword){
            $hasSpecialChar = false;
            foreach ($chars as $char){
                if(strpos($keyword, $char) !== false){
                    $hasSpecialChar = true;
                    break;
                }
            }

            if(!$hasSpecialChar){
	            $pieces[] = $keyword;
            }
        }

        $exp = '|';
        if(!empty($pieces)){
            $exp .= implode(' ', $pieces);
        }

        return $exp;
    }

    /**
     * @param      $keyword
     * @param bool $isLastKeyword
     * @param array $docsIn
     *
     * @return Collection
     */
    public function getAllDocumentsForKeyword($keyword, $isLastKeyword = false, $docsIn = null)
    {
	    $words = $this->getWordlistByKeyword($keyword, $isLastKeyword);

        if ( ! isset($words[0])) {
            return new Collection([]);
        }

	    return $this->getAllDocumentsForWordlist($words, $docsIn);
    }

	/**
	 * @param      $keyword
	 * @param bool $isLastKeyword
	 * @param array $docsIn
	 *
	 * @return array
	 */
	public function getAllDocumentIdsForKeyword( $keyword, $isLastKeyword = false, $docsIn = null ) {
		$keywordLike = $this->getKeywordLikeFormat( $keyword, $isLastKeyword );

		$result = $this->cache->get( $keywordLike );
		if ( $result !== false ) {
			if ( is_array( $docsIn ) && ! empty( $docsIn ) ) {
				$result = array_intersect( $docsIn, $result );
			}

			return $result;
		}

		$startTime = microtime( true );
		$words     = $this->getWordlistByKeyword( $keyword, $isLastKeyword );

		if ( ! isset( $words[0] ) ) {
			return array();
		}

		$result = $this->getAllDocumentsForWordlist( $words, $docsIn )->pluck( 'doc_id' );
		$result = array_values( array_unique( $result ) );

		$stopTime = microtime( true );

		// Re-run self for slow search, to allow cache result
		if ( ! empty( $docsIn ) && $stopTime - $startTime > 0.5 && $this->cache->isEnabled() ) {
			$this->getAllDocumentIdsForKeyword( $keyword, $isLastKeyword );
		}

		if ( ! empty( $result ) && empty( $docsIn ) && ( $stopTime - $startTime > 0.5 ) ) {
			$this->cache->set( $keywordLike, json_encode( array_map( 'absint', $result ) ) );
		}

		return $result;
	}

    /**
     * @param      $keyword
     *
     * @return Collection
     */
    public function getAllDocumentsForWhereKeywordNot($keyword)
    {
    	$limit = $this->getLimits('doclist');
        $doclistTable = $this->getTableName('doclist');

        $word = $this->getWordlistByKeyword($keyword);
        if ( ! isset($word[0])) {
            return new Collection([]);
        }
        $query = "SELECT * FROM $doclistTable WHERE doc_id NOT IN (SELECT doc_id FROM $doclistTable WHERE term_id = :id) GROUP BY doc_id ORDER BY hit_count DESC LIMIT {$limit}";

        if ( empty( $limit ) ) {
		    $query = "SELECT * FROM $doclistTable WHERE doc_id NOT IN (SELECT doc_id FROM $doclistTable WHERE term_id = :id) GROUP BY doc_id ORDER BY hit_count DESC";
	    }
        $stmtDoc = $this->index->prepare($query);

        $stmtDoc->bindValue(':id', $word[0]['id']);
        $stmtDoc->execute();

        return new Collection($stmtDoc->fetchAll(PDO::FETCH_ASSOC));
    }

	/**
	 * Get format of keyword LIKE statement depending on context
	 *
	 * @param $keyword
	 * @param $isLastKeyword
	 *
	 * @return string
	 */
	public function getKeywordLikeFormat( $keyword, $isLastKeyword ) {
		$keywordLike = mb_strtolower( $keyword );
		if ( $this->asYouType ) {
			$keywordLike = "%" . $keyword . "%";
			if ( strlen( $keyword ) <= 1 && ! $isLastKeyword ) {
				$keywordLike = $keyword;
			} elseif ( strlen( $keyword ) <= 1 && $isLastKeyword ) {
				$keywordLike = $keyword . "%";
			}
		}

		return apply_filters( 'dgwt/wcas/tnt/keyword_like_format', $keywordLike, $keyword, $isLastKeyword, $this->asYouType);
	}

    /**
     * @param      $keyword
     * @param bool $isLastWord
     *
     * @return int
     */
    public function totalMatchingDocuments($keyword, $isLastWord = false)
    {
        $occurance = $this->getWordlistByKeyword($keyword, $isLastWord);
        if (isset($occurance[0])) {
            return $occurance[0]['num_docs'];
        }

        return 0;
    }

    /**
     * @param      $keyword
     * @param bool $isLastWord
     *
     * @return array
     */
    public function getWordlistByKeyword($keyword, $isLastWord = false)
    {

        $keyword = mb_strtolower($keyword);
        $keywordLike = $this->getKeywordLikeFormat($keyword, $isLastWord);
        $limit = $this->getLimits('wordlist');

        $wordlistTable = $this->getTableName('wordlist');

        $searchWordlist = "SELECT * FROM $wordlistTable WHERE term like :keyword LIMIT 1";
        $stmtWord       = $this->index->prepare($searchWordlist);

        if ($this->asYouType) {

            $searchWordlist = "SELECT * FROM $wordlistTable WHERE term like :keyword ORDER BY length(term) ASC, num_hits DESC LIMIT $limit";
            $stmtWord       = $this->index->prepare($searchWordlist);
            $stmtWord->bindValue(':keyword', $keywordLike);

        } else {
            $stmtWord->bindValue(':keyword', $keywordLike);
        }

        $stmtWord->execute();
        $res = $stmtWord->fetchAll(PDO::FETCH_ASSOC);

        if ($this->fuzziness && ! isset($res[0])) {
            return $this->fuzzySearch($keyword);
        }

        return $res;
    }

    /**
     * @param $keyword
     *
     * @return array
     */
    public function fuzzySearch($keyword)
    {

        $wordlistTable = $this->getTableName('wordlist');

        $prefix         = substr($keyword, 0, $this->fuzzy_prefix_length);
        $searchWordlist = "SELECT * FROM $wordlistTable WHERE term like :keyword ORDER BY num_hits DESC LIMIT {$this->fuzzy_max_expansions}";
        $stmtWord       = $this->index->prepare($searchWordlist);
        $stmtWord->bindValue(':keyword', mb_strtolower($prefix) . "%");
        $stmtWord->execute();
        $matches = $stmtWord->fetchAll(PDO::FETCH_ASSOC);

        $resultSet = [];
        foreach ($matches as $match) {
            $distance = levenshtein($match['term'], $keyword);
            if ($distance <= $this->fuzzy_distance) {
                $match['distance'] = $distance;
                $resultSet[]       = $match;
            }
        }

        // Sort the data by distance, and than by num_hits
        $distance = [];
        $hits     = [];
        foreach ($resultSet as $key => $row) {
            $distance[$key] = $row['distance'];
            $hits[$key]     = $row['num_hits'];
        }
        array_multisort($distance, SORT_ASC, $hits, SORT_DESC, $resultSet);

        return $resultSet;
    }

    public function totalDocumentsInCollection()
    {
        return $this->getValueFromInfoTable('total_documents');
    }

    public function getStemmer()
    {
        return $this->stemmer;
    }

    public function setStemmer()
    {
        $stemmer = $this->getValueFromInfoTable('stemmer');
        if ($stemmer) {

            // Backward compatibility
            if ($stemmer === 'TeamTNT\TNTSearch\Stemmer\NoStemmer' || $stemmer === 'TeamTNT\TNTSearchASFW\Stemmer\NoStemmer') {
                $stemmer = 'DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Stemmer\NoStemmer';
            }

            $this->stemmer = new $stemmer;
        } else {
            $this->stemmer = isset($this->config['stemmer']) ? new $this->config['stemmer'] : new PorterStemmer;
        }
    }

    public function getValueFromInfoTable($value)
    {
        $infoTable = $this->getTableName('info');

        $query = "SELECT * FROM $infoTable WHERE ikey = '$value'";
        $docs  = $this->index->query($query);

        return $docs->fetch(PDO::FETCH_ASSOC)['ivalue'];
    }

    public function filesystemMapIdsToPaths($docs)
    {
        $query = "SELECT * FROM filemap WHERE id in (" . $docs->implode(', ') . ");";
        $res   = $this->index->query($query)->fetchAll(PDO::FETCH_ASSOC);

        return $docs->map(function ($key) use ($res) {
            $index = array_search($key, array_column($res, 'id'));

            return $res[$index];
        });
    }

    public function info($str)
    {
        echo $str . "\n";
    }

    public function breakIntoTokens($text)
    {
        return $this->tokenizer->tokenize($text);
    }

    /**
     * @param        $text
     * @param        $needle
     * @param string $tag
     * @param array $options
     *
     * @return string
     */
    public function highlight($text, $needle, $tag = 'em', $options = [])
    {
        $hl = new Highlighter;

        return $hl->highlight($text, $needle, $tag, $options);
    }

    public function snippet($words, $fulltext, $rellength = 300, $prevcount = 50, $indicator = '...')
    {
        $hl = new Highlighter;

        return $hl->extractRelevant($words, $fulltext, $rellength, $prevcount, $indicator);
    }

    /**
     * @return TNTIndexer
     */
    public function getIndex()
    {
        $indexer           = new TNTIndexer;
        $indexer->inMemory = false;
        $indexer->setIndex($this->index);
        $indexer->setStemmer($this->stemmer);

        return $indexer;
    }

    /**
     * @param $words
     * @param null|array $docsIn
     *
     * @return Collection
     */
    private function getAllDocumentsForWordlist($words, $docsIn = null)
    {

    	$limit = $this->getLimits('doclist');
        $doclistTable = $this->getTableName('doclist');

        $binding_params = implode(',', array_fill(0, count($words), '?'));
	    if ( is_array( $docsIn ) && ! empty( $docsIn ) ) {
		    $in    = join( ",", $docsIn );
		    $query = "SELECT * FROM $doclistTable WHERE term_id in ($binding_params) AND doc_id IN($in) ORDER BY CASE term_id";
	    } else {
		    $query = "SELECT * FROM $doclistTable WHERE term_id in ($binding_params) ORDER BY CASE term_id";
	    }
        $order_counter  = 1;

        foreach ($words as $word) {
            $query .= " WHEN " . $word['id'] . " THEN " . $order_counter++;
        }

        $query .= " END";

	    if ( ! empty( $limit ) ) {
		    $query .= " LIMIT {$limit}";
	    }

        $stmtDoc = $this->index->prepare($query);

        $ids = null;
        foreach ($words as $word) {
            $ids[] = $word['id'];
        }

        $stmtDoc->execute($ids);

        return new Collection($stmtDoc->fetchAll(PDO::FETCH_ASSOC));
    }

	/**
	 * Get MySQL table name
	 *
	 * @param string $type Table type
	 *
	 * @return string Table name
	 */
    public function getTableName($type)
    {
        global $wpdb;
        $tableName = '';

        $suffix = $this->getTableSuffix();

        switch ($type) {
            case 'wordlist':
                $tableName = $wpdb->dgwt_wcas_si_wordlist . $suffix;
                break;
            case 'doclist':
                $tableName = $wpdb->dgwt_wcas_si_doclist . $suffix;
                break;
            case 'info':
                $tableName = $wpdb->dgwt_wcas_si_info;
                break;
	        case 'cache':
		        $tableName = $wpdb->dgwt_wcas_si_cache . $suffix;
		        break;
        }

        return $tableName;
    }

    /**
     * Get table suffix
     *
     * @return string
     */
    public function getTableSuffix()
    {
        $suffix = '';

        if ( ! empty($this->postType) && $this->postType !== 'product') {
            $suffix .= '_' . $this->postType;
	        //@TODO DB tables don't support "-" in post type name. Better change "-" to "_" than add single quote to all SQL queries
        }

        if ( ! empty($this->lang)) {
            $suffix .= '_' . str_replace( '-', '_', $this->lang );
        }

        return $suffix;
    }
}
