<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Indexer;

use DgoraWcas\Engines\TNTSearchMySQL\Indexer\PostsSourceQuery;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\SourceQuery;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\SynonymsHandler;
use Exception;
use PDO;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Connectors\FileSystemConnector;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Connectors\MySqlConnector;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Connectors\PostgresConnector;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Connectors\SQLiteConnector;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Connectors\SqlServerConnector;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\FileReaders\TextFileReader;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Stemmer\CroatianStemmer;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Stemmer\NoStemmer;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Support\Collection;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Support\Tokenizer;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Support\TokenizerInterface;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Database;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Builder;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Utils;
use DgoraWcas\Multilingual;
use DgoraWcas\Product;
use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\TNTSearch;

class TNTIndexer
{
	/**
	 * @var \PDO
	 */
    protected $index              = null;
	/**
	 * @var \PDO
	 */
    protected $dbh                = null;
    protected $primaryKey         = null;
    protected $excludePrimaryKey  = true;
    public $stemmer               = null;
    public $tokenizer             = null;
	/**
	 * @var TNTSearch
	 */
    public $tnt                   = null;
    public $synonymsHandler       = null;
    public $stopWords             = [];
    public $filereader            = null;
    public $config                = [];
    protected $query              = "";
    protected $wordlist           = [];
    protected $inMemoryTerms      = [];
    protected $decodeHTMLEntities = false;
    public $disableOutput         = false;
    public $steps                 = 1000;
    public $indexName             = "";
    public $statementsPrepared    = false;
    public $statementsLang        = "";
    public $statementsPostType    = "";
    public $bufforLang            = "";
    public $bufforPostType        = "";
    public $isMultilingual        = false;
    public $itemsSet              = [];

    public function __construct()
    {
        $this->stemmer    = new NoStemmer;
        $this->tokenizer  = new Tokenizer;
        $this->tokenizer->setContext('indexer');
        $this->filereader = new TextFileReader;
        $this->synonymsHandler = new SynonymsHandler;
    }

    /**
     * @param TokenizerInterface $tokenizer
     */
    public function setTokenizer(TokenizerInterface $tokenizer)
    {
        $this->tokenizer = $tokenizer;
        $this->tokenizer->setContext('indexer');
    }

	/**
	 * @param TNTSearch $tnt
	 */
	public function setTnt(TNTSearch $tnt)
	{
		$this->tnt = $tnt;
	}

    public function setStopWords(array $stopWords)
    {
        $this->stopWords = $stopWords;
    }

    /**
     * @param array $config
     */
    public function loadConfig(array $config)
    {
        $this->config            = $config;
        if (!isset($this->config['driver'])) {
            $this->config['driver'] = "";
        }

    }

    public function getStemmer()
    {
        return $this->stemmer;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        if (isset($this->primaryKey)) {
            return $this->primaryKey;
        }
        return 'id';
    }

    /**
     * @param string $primaryKey
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    public function excludePrimaryKey()
    {
        $this->excludePrimaryKey = true;
    }

    public function includePrimaryKey()
    {
        $this->excludePrimaryKey = false;
    }

    public function setStemmer($stemmer)
    {
        global $wpdb;

        $this->stemmer = $stemmer;
        $class         = addslashes(get_class($stemmer));

        $query = "SELECT * FROM $wpdb->dgwt_wcas_si_info WHERE ikey = 'stemmer'";
        $stemmer  = $this->index->query($query);
        $stemmerData = $stemmer->fetch(PDO::FETCH_ASSOC);
	    $stemmerVal = is_array($stemmerData) && array_key_exists('ivalue', $stemmerData) ? $stemmerData['ivalue'] : '';

        if(empty($stemmerVal)) {
            $this->index->exec("INSERT INTO $wpdb->dgwt_wcas_si_info ( ikey, ivalue) values ( 'stemmer', '$class')");
        }else{

            // Backward compatibility
            if($stemmerVal === 'TeamTNT\TNTSearch\Stemmer\NoStemmer' || $stemmerVal === 'TeamTNT\TNTSearchASFW\Stemmer\NoStemmer'){
                $fixedClass = 'DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Stemmer\NoStemmer';
                $this->index->exec("UPDATE $wpdb->dgwt_wcas_si_info SET ivalue='$fixedClass' WHERE ivalue='$stemmerVal' ");
            }
        }
    }

    public function setCroatianStemmer()
    {
        $this->setStemmer(new CroatianStemmer);
    }

    /**
     * @param string $language  - one of: arabic, croatian, german, italian, porter, russian, ukrainian
     */
    public function setLanguage($language = 'porter')
    {
        $class = 'DgoraWcas\\Engines\\TNTSearchMySQL\\Libs\\TNTSearch\\Stemmer\\'.ucfirst(strtolower($language)).'Stemmer';
        $this->setStemmer(new $class);
    }

    /**
     * @param PDO $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    public function setFileReader($filereader)
    {
        $this->filereader = $filereader;
    }

	/**
	 * @param array $itemsSet
	 */
	public function setItemsSet( $itemsSet ) {
		$this->itemsSet = $itemsSet;
	}

	public function fetchItemsSetData() {
		$args = array(
			'trigrams' => true
		);

		$postType = ! empty( $this->itemsSet[0] ) ? get_post_type( $this->itemsSet[0] ) : 'product';

		if ( ! empty( $this->itemsSet ) ) {
			$args['package'] = $this->itemsSet;
		}

		if ( $postType === 'product' ) {
			$source = new SourceQuery( $args );
		} else {
			$args['postTypes'] = array( $postType );
			$source            = new PostsSourceQuery( $args );
		}

		return $source->getData();
	}

    public function prepareStatementsForIndex()
    {
        if (
            !$this->statementsPrepared
            || $this->statementsLang !== $this->bufforLang
            || $this->statementsPostType !== $this->bufforPostType
         ) {
            $wordlistTable = $this->getTableName('wordlist');

            $this->insertWordlistStmt = $this->index->prepare("INSERT INTO $wordlistTable (term, num_hits, num_docs) VALUES (:keyword, :hits, :docs)");
            $this->selectWordlistStmt = $this->index->prepare("SELECT * FROM $wordlistTable WHERE term = :keyword LIMIT 1");
            $this->updateWordlistStmt = $this->index->prepare("UPDATE $wordlistTable SET num_docs = num_docs + :docs, num_hits = num_hits + :hits WHERE term = :keyword");
            $this->statementsPrepared = true;
            $this->statementsLang = $this->bufforLang;
            $this->statementsPostType = $this->bufforPostType;
        }
    }

    /**
     * @param string $indexName
     *
     * @return TNTIndexer
     */
    public function createIndex($deprecated = '')
    {
        $this->indexName = $deprecated;

        $pdo         = new MySqlConnector();
        $this->index = $pdo->connect(Database::getConfig());

        return $this;
    }

    public function indexBeginTransaction()
    {
        $this->index->beginTransaction();
    }

    public function indexEndTransaction()
    {
	    if ( $this->index->inTransaction() ) {
		    $this->index->commit();
	    }
    }

    /**
     * @param array $config
     *
     * @return FileSystemConnector|MySqlConnector|PostgresConnector|SQLiteConnector|SqlServerConnector
     * @throws Exception
     */
    public function createConnector(array $config)
    {
        if (!isset($config['driver'])) {
            throw new Exception('A driver must be specified.');
        }

        switch ($config['driver']) {
            case 'mysql':
                return new MySqlConnector;
            case 'pgsql':
                return new PostgresConnector;
            case 'sqlite':
                return new SQLiteConnector;
            case 'sqlsrv':
                return new SqlServerConnector;
            case 'filesystem':
                return new FileSystemConnector;
        }
        throw new Exception("Unsupported driver [{$config['driver']}]");
    }

    /**
     * @param PDO $dbh
     */
    public function setDatabaseHandle(PDO $dbh)
    {
        $this->dbh = $dbh;
        if ($this->dbh->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') {
            $this->dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        }
    }

    public function query($query)
    {
        $this->query = $query;
    }

    public function run()
    {
        $this->isMultilingual = Multilingual::isMultilingual();

//	    Builder::log( "[Searchable index] Memory usage: " . memory_get_usage() );
        $productProcessed = Builder::getInfo('searchable_processed');

        $time = microtime(true);

        $counter = !empty($productProcessed) && is_numeric($productProcessed) ? intval($productProcessed) : 0;

	    $productsSetCount = apply_filters( 'dgwt/wcas/indexer/searchable_set_items_count', Builder::SEARCHABLE_SET_ITEMS_COUNT );
	    $longTimeCounter  = 0;
	    $longTimeLimit    = 20;
	    $longTimeWarning  = false;

	    $this->index->beginTransaction();
	    foreach ( $this->fetchItemsSetData() as $row ) {
		    $counter ++;
		    $longTimeCounter ++;

		    $row = apply_filters('dgwt/wcas/indexer/items_row', $row);

            if(!empty($row['lang'])){
                $this->bufforLang = $row['lang'];
                unset($row['lang']);
            }

            if(!empty($row['post_type'])){
                $this->bufforPostType = $row['post_type'];
                unset($row['post_type']);
            }

            if($this->isMultilingual && empty($this->bufforLang)) {
                continue;
            }

            // Set custom attributes values
		    if ( ! empty( $this->config['scope']['attributes'] ) ) {
			    $row = $this->applyCustomAttributes($row);
		    }

            $this->processDocument(new Collection($row));

            $this->bufforLang = '';
            $this->bufforPostType = '';

		    if ( $longTimeWarning === false && microtime( true ) - $time > $longTimeLimit ) {
			    $longTimeWarning = true;
			    Builder::log( "[Searchable index] Timeout warning: " . round( ( $longTimeCounter / $productsSetCount ) * 100 ) . "% of the objects in the set were indexed in " . number_format( microtime( true ) - $time, 4, '.', '' ) . " seconds", 'debug', 'file', 'searchable' );
		    }
        }

        if (Builder::getInfo('status') !== 'building') {
	        if ( $this->index->inTransaction() ) {
		        $this->index->rollBack();
	        }
	        Builder::log( "[Searchable index] Process killed" );
            exit();
        }

        $ntime = number_format(microtime(true) - $time, 4, '.', '') . ' s';
	    Builder::log( "[Searchable index] Processed $counter objects in $ntime" );

	    if ( $this->index->inTransaction() ) {
		    $this->index->commit();
	    }


        $this->updateInfoTable('total_documents', $counter);
        Builder::addInfo('searchable_processed', $counter);

        $this->info("Total rows $counter");
    }

	/**
	 * Apply custom attributes
	 *
	 * @param array $row
	 *
	 * @return array
	 */
	public function applyCustomAttributes( $row ) {

		$customAttributesValues = Product::getCustomAttributes( (int) $row['ID'] );
		if ( ! empty( $customAttributesValues ) ) {
			$sep = ' | ';
			if ( ! isset( $row['attributes'] ) ) {
				$row['attributes'] = '';
				$sep               = '';
			}

			$row['attributes'] .= $sep . implode( ' | ', $customAttributesValues );
		}

		return $row;
	}

    public function processDocument($row)
    {
        $documentId = $row->get($this->getPrimaryKey());

        if ($this->excludePrimaryKey) {
            $row->forget($this->getPrimaryKey());
        }

        $stems = $row->map(function ($columnContent, $columnName) use ($row) {
            return $this->stemText($columnContent);
        });

        $this->saveToIndex($stems, $documentId);
    }

	/**
	 * Get all keywords related to document ID
	 *
	 * @param $keyword
	 *
	 * @return array
	 */
	public function getWordlistByDocumentId( $documentId, $lang = '', $postType = '' ) {

		$this->setBuffor($lang, $postType);

		$documentId       = intval( $documentId );
		$doclistTable = $this->getTableName( 'doclist' );
		$wordlistTable    = $this->getTableName( 'wordlist' );

		$termsSql = "SELECT wordlist.term
                     FROM $doclistTable doclist
                     INNER JOIN $wordlistTable wordlist ON doclist.term_id = wordlist.id
                     WHERE doclist.doc_id = :doc_id
                     ORDER BY wordlist.term ASC";

		$stmtWTerms = $this->index->prepare( $termsSql );
		$stmtWTerms->bindValue( ':doc_id', $documentId );


		$stmtWTerms->execute();
		$wordlist = $stmtWTerms->fetchAll( PDO::FETCH_ASSOC );

		return $wordlist;
	}

    public function insert($document, $lang = '', $postType = '')
    {
        $this->setBuffor($lang, $postType);

        $this->processDocument(new Collection($document), $lang);
        $total = $this->totalDocumentsInCollection() + 1;
        $this->updateInfoTable('total_documents', $total);
    }

    public function update($id, $document, $lang = '', $postType = '')
    {
        $this->setBuffor($lang, $postType);

        $this->delete($id);
        $this->insert($document, $lang, $postType);
    }

    public function delete($documentId, $lang = '', $postType = '')
    {
        $this->setBuffor($lang, $postType);

        $wordlistTable = $this->getTableName('wordlist');
        $doclistTable  = $this->getTableName('doclist');

        $rows = $this->prepareAndExecuteStatement("SELECT * FROM $doclistTable WHERE doc_id = :documentId;", [
            ['key' => ':documentId', 'value' => $documentId]
        ])->fetchAll(PDO::FETCH_ASSOC);

        $updateStmt = $this->index->prepare("UPDATE $wordlistTable SET num_docs = num_docs - 1, num_hits = num_hits - :hits WHERE id = :term_id");

        foreach ($rows as $document) {
            $updateStmt->bindParam(":hits", $document['hit_count']);
            $updateStmt->bindParam(":term_id", $document['term_id']);
            $updateStmt->execute();
        }

        $this->prepareAndExecuteStatement("DELETE FROM $doclistTable WHERE doc_id = :documentId;", [
            ['key' => ':documentId', 'value' => $documentId]
        ]);

        $res = $this->prepareAndExecuteStatement("DELETE FROM $wordlistTable WHERE num_hits = 0");

        $affected = $res->rowCount();

        if ($affected) {
            $total = $this->totalDocumentsInCollection() - 1;
            $this->updateInfoTable('total_documents', $total);
        }

	    if ( ! is_null( $this->tnt ) ) {
		    $this->tnt->cache->deleteByValue( $documentId );
	    }
    }

    public function updateInfoTable($key, $value)
    {
        global $wpdb;

        $this->index->exec("UPDATE $wpdb->dgwt_wcas_si_info SET ivalue = $value WHERE ikey = '$key'");
    }

    public function stemText($text)
    {
        $stemmer = $this->getStemmer();
        $text    = $this->clearText($text);
        $text    = $this->synonymsHandler->applySynonyms($text);
        $words   = $this->breakIntoTokens($text);
        $stems   = [];
        foreach ($words as $word) {
            if($word !== ''){
                $stems[] = $stemmer->stem($word);
            }
        }

        return $stems;
    }

    /**
     * Clear text rom HTML, comments etc.
     *
     * @param string $text
     *
     * @return string
     */
    public function clearText($text)
    {
        return Utils::clearContent($text);
    }

    public function breakIntoTokens($text)
    {
        if ($this->decodeHTMLEntities) {
            $text = html_entity_decode($text);
        }

        return $this->tokenizer->tokenize($text, $this->stopWords);
    }

    public function decodeHtmlEntities($value = true)
    {
        $this->decodeHTMLEntities = $value;
    }

    public function saveToIndex($stems, $docId)
    {

        $this->prepareStatementsForIndex();
        $terms = $this->saveWordlist($stems);
        $this->saveDoclist($terms, $docId);
        //$this->saveHitList($stems, $docId, $terms); weights
    }

    /**
     * @param $stems
     *
     * @return array
     */
    public function saveWordlist($stems)
    {
        $terms = [];
        $stems->map(function ($column, $key) use (&$terms) {
            foreach ($column as $term) {
                if (array_key_exists($term, $terms)) {
                    $terms[$term]['hits']++;
                    $terms[$term]['docs'] = 1;
                } else {
                    $terms[$term] = [
                        'hits' => 1,
                        'docs' => 1,
                        'id'   => 0
                    ];
                }
            }
        });

        foreach ($terms as $key => $term) {

            try {
                $this->insertWordlistStmt->bindParam(":keyword", $key);
                $this->insertWordlistStmt->bindParam(":hits", $term['hits']);
                $this->insertWordlistStmt->bindParam(":docs", $term['docs']);
                $this->insertWordlistStmt->execute();

                $terms[$key]['id'] = $this->index->lastInsertId();

                $this->addTermToMemory($key, $terms[$key]['id']);

            } catch (\Exception $e) {

                if ($e->getCode() == 23000) {
                    $this->updateWordlistStmt->bindValue(':docs', $term['docs']);
                    $this->updateWordlistStmt->bindValue(':hits', $term['hits']);
                    $this->updateWordlistStmt->bindValue(':keyword', $key);
                    $this->updateWordlistStmt->execute();

                    $termIdFromMemory = $this->getTermFromMemory($key);

                    if (empty($termIdFromMemory)) {
                        $this->selectWordlistStmt->bindValue(':keyword', $key);
                        $this->selectWordlistStmt->execute();
                        $res = $this->selectWordlistStmt->fetch(PDO::FETCH_ASSOC);
                        $terms[$key]['id'] = $res['id'];

                    } else {
                        $terms[$key]['id'] = $termIdFromMemory;
                    }

                } else {
                    echo "Error while saving wordlist: ".$e->getMessage()."\n";
                }

                // Statements must be refreshed, because in this state they have error attached to them.
                $this->statementsPrepared = false;
                $this->prepareStatementsForIndex();

            }
        }
        return $terms;
    }

    public function saveDoclist($terms, $docId)
    {
        $doclistTable = $this->getTableName('doclist');

        $insert = "INSERT INTO $doclistTable (term_id, doc_id, hit_count) VALUES (:id, :doc, :hits)";
        $stmt   = $this->index->prepare($insert);

        foreach ($terms as $key => $term) {
            $stmt->bindValue(':id', $term['id']);
            $stmt->bindValue(':doc', $docId);
            $stmt->bindValue(':hits', $term['hits']);
            try {
                $stmt->execute();
            } catch (\Exception $e) {
                //we have a duplicate
	            Builder::log( "[Searchable index] DB: Duplicate " . serialize( $term ) . ' | ' . $e->getMessage() );
            }
        }
    }

    public function saveHitList($stems, $docId, $termsList)
    {
        return;
        global $wpdb;

        $fieldCounter = 0;
        $fields       = [];

        $insert = "INSERT INTO $wpdb->dgwt_wcas_si_hitlist (term_id, doc_id, field_id, position, hit_count)
                   VALUES (:term_id, :doc_id, :field_id, :position, :hit_count)";
        $stmt = $this->index->prepare($insert);

        foreach ($stems as $field => $terms) {
            $fields[$fieldCounter] = $field;
            $positionCounter       = 0;
            $termCounts            = array_count_values($terms);
            foreach ($terms as $term) {
                if (isset($termsList[$term])) {
                    $stmt->bindValue(':term_id', $termsList[$term]['id']);
                    $stmt->bindValue(':doc_id', $docId);
                    $stmt->bindValue(':field_id', $fieldCounter);
                    $stmt->bindValue(':position', $positionCounter);
                    $stmt->bindValue(':hit_count', $termCounts[$term]);
                    $stmt->execute();
                }
                $positionCounter++;
            }
            $fieldCounter++;
        }
    }

    /**
     * Add term to memory
     *
     * @param string $key
     * @param int $termID
     *
     * @return void
     */
    public function addTermToMemory($key, $termID)
    {
        if ($this->isMultilingual && !empty($this->bufforLang)) {
            $this->inMemoryTerms[$this->bufforLang][$key] = $termID;
        } else {
            $this->inMemoryTerms[$key] = $termID;
        }
    }

    /**
     * Get term from memory
     *
     * @param string $key
     *
     * @return int
     */
    public function getTermFromMemory($key)
    {

        $termID = 0;

        if (!empty($key) && is_string($key)) {

            if ($this->isMultilingual && !empty($this->bufforLang)) {

                if (array_key_exists($this->bufforLang, $this->inMemoryTerms)
                    && array_key_exists($key, $this->inMemoryTerms[$this->bufforLang])
                ) {
                    $termID = $this->inMemoryTerms[$this->bufforLang][$key];
                }
            } else {

                if (array_key_exists($key, $this->inMemoryTerms)) {
                    $termID = $this->inMemoryTerms[$key];
                }

            }

        }

        return $termID;
    }

    /**
     * @return int
     */
    public function totalDocumentsInCollection()
    {
        global $wpdb;

        $query = "SELECT * FROM $wpdb->dgwt_wcas_si_info WHERE ikey = 'total_documents'";
        $docs  = $this->index->query($query);

        return $docs->fetch(PDO::FETCH_ASSOC)['ivalue'];
    }

    /**
     * Get doclist table name
     * @param string $type
     *
     * @return string
     */
    public function getTableName($type){

        global $wpdb;

        $name = '';
        $suffix = $this->getTableSuffix();

        switch ($type){
            case 'wordlist':
                $name = $wpdb->dgwt_wcas_si_wordlist . $suffix;
                break;
            case 'doclist':
                $name = $wpdb->dgwt_wcas_si_doclist . $suffix;
                break;
            case 'info':
                $name = $wpdb->dgwt_wcas_si_info;
                break;
	        case 'cache':
		        $name = $wpdb->dgwt_wcas_si_cache . $suffix;
		        break;
        }

        return $name;
    }

    /**
     * Get table suffix
     *
     * @return string
     */
    public function getTableSuffix()
    {
        $suffix = '';

        if (! empty($this->bufforPostType) && $this->bufforPostType !== 'product') {
            $suffix .= '_' . $this->bufforPostType;
            //@TODO DB tables don't support "-" in post type name. Better change "-" to "_" than add single quote to all SQL queries
        }

        if (! empty($this->bufforLang)) {
            $suffix .= '_' . str_replace( '-', '_', $this->bufforLang );
        }

        return $suffix;
    }

    /**
     * Set languages and post type buffor
     *
     * @param string $lang
     * @param string $postType
     *
     * @return void
     */
    public function setBuffor($lang = '', $postType = ''){
        $this->bufforLang     = ! empty($lang) ? $lang : $this->bufforLang;
        $this->bufforPostType = ! empty($postType) && $postType !== 'product' ? $postType : $this->bufforPostType;
    }

    /**
     * @param $keyword
     *
     * @return string
     */
    public function buildTrigrams($keyword)
    {
        $t        = "__".$keyword."__";
        $trigrams = "";
        for ($i = 0; $i < strlen($t) - 2; $i++) {
            $trigrams .= mb_substr($t, $i, 3)." ";
        }

        return trim($trigrams);
    }

    public function prepareAndExecuteStatement($query, $params = [])
    {
        $statemnt = $this->index->prepare($query);
        foreach ($params as $param) {
            $statemnt->bindParam($param['key'], $param['value']);
        }
        $statemnt->execute();
        return $statemnt;
    }

    public function info($text)
    {
        if (!$this->disableOutput) {
            echo $text.PHP_EOL;
        }
    }

}
