<?php


use DgoraWcas\Engines\TNTSearchMySQL\SearchQuery\AjaxQuery;
use DgoraWcas\Engines\TNTSearchMySQL\Debug\Debugger;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Tokenizer;

// Exit if accessed directly
if ( ! defined( 'DGWT_WCAS_FILE' ) ) {
	exit;
}

Debugger::wipeLogs( 'product-search-flow' );
Debugger::wipeLogs( 'search-resutls' );


$searchPhrase = ! empty( $_GET['s'] ) ? $_GET['s'] : '';
$lang         = ! empty( $_GET['lang'] ) ? $_GET['lang'] : '';

$toTokenize   = ! empty( $_GET['dgwt-wcas-to-tokenize'] ) ? $_GET['dgwt-wcas-to-tokenize'] : '';
$tokenizerCtx = ! empty( $_GET['dgwt-wcas-debug-tokenizer-ctx'] ) ? $_GET['dgwt-wcas-debug-tokenizer-ctx'] : 'indexer';

?>


<h3>Search flow</h3>
<form action="<?php echo admin_url( 'admin.php' ); ?>" method="get">
	<input type="hidden" name="page" value="dgwt_wcas_debug">
	<label for="dgwt-wcas-debug-search"></label>
	<input type="text" class="regular-text" id="dgwt-wcas-debug-search" name="s"
		   value="<?php echo esc_html( $searchPhrase ); ?>" placeholder="search phrase">
	<input type="text" class="small-text" id="dgwt-wcas-debug-search-lang" name="lang"
		   value="<?php echo esc_html( $lang ); ?>" placeholder="lang">
	<button class="button" type="submit">Search</button>
</form>

<hr/>
<h3>Tokenizer</h3>
<form action="<?php echo admin_url( 'admin.php' ); ?>" method="get">
	<input type="hidden" name="page" value="dgwt_wcas_debug">
	<label for="dgwt-wcas-debug-tokenizer"></label>
	<input type="text" class="regular-text" id="dgwt-wcas-debug-tokenizer" name="dgwt-wcas-to-tokenize"
		   value="<?php echo esc_html( $toTokenize ); ?>" placeholder="To tokenize"">
	<select name="dgwt-wcas-debug-tokenizer-ctx">
		<option <?php echo $tokenizerCtx === 'search' ? 'selected="selected"' : ''; ?>>search</option>
		<option <?php echo $tokenizerCtx === 'indexer' ? 'selected="selected"' : ''; ?>>indexer</option>
	</select>
	<button class="button" type="submit">Tokenize</button>
</form>

<?php if ( ! empty( $searchPhrase ) ) {

	define( 'DGWT_SEARCH_START', microtime( true ) );
	$query = new AjaxQuery( true );
	$query->setPhrase( $searchPhrase );

	if ( ! empty( $_GET['lang'] ) ) {
		$query->setLang( $_GET['lang'] );
	}

	$query->searchProducts();
	$query->searchPosts();
	$query->searchTaxonomy();
	Debugger::logSearchResults( $query );


	Debugger::printLogs( 'Search flow', 'product-search-flow' );
	Debugger::printLogs( 'Search resutls', 'search-resutls' );

}
?>

<?php if ( ! empty( $toTokenize ) ) {

	$tokenizer = new Tokenizer();
	$tokenizer->setContext( $tokenizerCtx );

	Debugger::log( '<b>Phrase:</b> <pre>' . var_export( $toTokenize, true ) . '</pre>', 'tokenizer' );
	Debugger::log( '<b>Context:</b> <pre>' . var_export( $tokenizerCtx, true ) . '</pre>', 'tokenizer' );
	Debugger::log( '<b>Split by:</b> <pre>' . var_export( $tokenizer->getSpecialChars(), true ) . '</pre>', 'tokenizer' );
	Debugger::log( '<b>Tokens:</b> <pre>' . var_export( $tokenizer->tokenize( $toTokenize ), true ) . '</pre>', 'tokenizer' );

	Debugger::printLogs( 'Tokenizer', 'tokenizer' );

}

Debugger::wipeLogs( 'product-search-flow' );
Debugger::wipeLogs( 'search-resutls' );
Debugger::wipeLogs( 'tokenizer' );
?>
