<?php

use DgoraWcas\Engines\TNTSearchMySQL\Indexer\SourceQuery;

// Exit if accessed directly
if ( ! defined( 'DGWT_WCAS_FILE' ) ) {
	exit;
}
?>
	<h3>Source query</h3>
	<form action="<?php echo admin_url( 'admin.php' ); ?>" method="get">
		<input type="hidden" name="page" value="dgwt_wcas_debug">
		<input type="hidden" name="source_query" value="1">
		<button class="button" type="submit">Get source query</button>
	</form>
<?php

if ( ! empty( $_GET['source_query'] ) ):

	$source = new SourceQuery( array( 'ids' => true ) );

	$products = $source->getData();
	$request  = $source->getRequest();

	?>
<table>
	<tr>
		<td><b>Total Products</b></td>
		<td><?php echo count($source->getData()); ?></td>
	</tr>
</table>
<h3>SQL</h3>
<pre>
	<?php echo $request; ?>
</pre>
<?php

endif;
