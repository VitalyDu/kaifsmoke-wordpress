<?php


use DgoraWcas\Engines\TNTSearchMySQL\SearchQuery\AjaxQuery;
use DgoraWcas\Engines\TNTSearchMySQL\Debug\Debugger;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Tokenizer;

// Exit if accessed directly
if ( ! defined( 'DGWT_WCAS_FILE' ) ) {
	exit;
}

$productID = ! empty( $_GET['product_id'] ) ? $_GET['product_id'] : '';

if ( ! empty( $productID ) ) {
	$p = new \DgoraWcas\Engines\TNTSearchMySQL\Debug\Product( $productID );

	$readableIndexData = $p->getReadableIndexData();
	$wordlist          = $p->getSearchableIndexData();
	$wordlistUpdater   = $p->getDataForIndexingByUpdater();
	$wordlistSQL       = $p->getDataForIndexingBySource();
	$diff              = $p->diffIndexMethod();
}

?>


<h3>Product debug</h3>
<form action="<?php echo admin_url( 'admin.php' ); ?>" method="get">
	<input type="hidden" name="page" value="dgwt_wcas_debug">
	<input type="text" class="regular-text" id="dgwt-wcas-debug-product" name="product_id"
		   value="<?php echo esc_html( $productID ); ?>" placeholder="Product ID">
	<button class="button" type="submit">Debug</button>
</form>

<?php if ( ! empty( $productID ) && ! $p->product->isValid() ): ?>
	<p>Wrong product ID</p>
<?php endif; ?>

<?php if ( ! empty( $productID ) && $p->product->isValid() ): ?>

	<table class="wc_status_table widefat" cellspacing="0">
		<thead>
		<tr>
			<th colspan="2" data-export-label="Searchable Index"><h3>General</h3></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td><b>Can index: </b></td>
			<td><?php echo $p->product->canIndex__premium_only() ? 'yes' : 'no'; ?></td>
		</tr>
		</tbody>
	</table>

	<table class="wc_status_table widefat" cellspacing="0">
		<thead>
		<tr>
			<th colspan="2" data-export-label="Searchable Index"><h3>Readable Index (stored in the database)</h3></th>
		</tr>
		</thead>
		<tbody>

		<?php

		foreach ( $readableIndexData as $key => $data ): ?>
			<tr>
				<td><b><?php echo $key; ?>: </b></td>
				<td><?php echo $data; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<table class="wc_status_table widefat" cellspacing="0">
		<thead>
		<tr>
			<th colspan="2" data-export-label="Searchable Index"><h3>Searchable Index (stored in the database)</h3></th>
		</tr>
		</thead>
		<tbody>

		<tr>
			<td><b>Total terms:</b></td>
			<td><?php echo count( $wordlist ); ?></td>
		</tr>


		<tr>
			<td><b>Wordlist: </b></td>
			<td class="dgwt-wcas-table-wordlist">
				<p>
					<?php foreach ( $wordlist as $term ): ?>
						<?php echo $term . '<br />'; ?>
					<?php endforeach; ?>
				</p>
			</td>
		</tr>
		</tbody>
	</table>

	<table class="wc_status_table widefat" cellspacing="0">
		<thead>
		<tr>
			<th colspan="2" data-export-label="Searchable Index"><h3>Diff indexing methods (Updater.php VS SourceQuery.php)</h3>
				<small>Compare wordlists generated via <code>Updater.php</code> and via <code>SourceQuery.php</code> method.</small>
			</th>
		</tr>
		</thead>
		<tbody>

		<tr>
			<td><b>Diff</b></td>
			<td><?php echo empty( $diff['only_updater']) && empty( $diff['only_source_query']) ? 'Both methods generated <b>the same wordlist</b>' : 'The wordlists are <b>different</b>'; ?></td>
		</tr>

		<?php if ( ! empty( $diff['only_updater'] ) ): ?>
			<tr>
				<td><b>Only in Updater.php: </b></td>
				<td class="dgwt-wcas-table-wordlist">
					<p>
						<?php foreach ( $diff['only_updater'] as $term ): ?>
							<?php echo $term . '<br />'; ?>
						<?php endforeach; ?>
					</p>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( ! empty( $diff['only_source_query'] ) ): ?>
			<tr>
				<td><b>Only in SourceQuery.php: </b></td>
				<td class="dgwt-wcas-table-wordlist">
					<p>
						<?php foreach ( $diff['only_source_query'] as $term ): ?>
							<?php echo $term . '<br />'; ?>
						<?php endforeach; ?>
					</p>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( !empty( $diff['only_updater']) || !empty( $diff['only_source_query']) ): ?>
			<tr>
				<td><b>Common: </b></td>
				<td class="dgwt-wcas-table-wordlist">
					<p>
						<?php foreach ( $diff['common'] as $term ): ?>
							<?php echo $term . '<br />'; ?>
						<?php endforeach; ?>
					</p>
				</td>
			</tr>
		<?php endif; ?>


		</tbody>
	</table>


<?php endif; ?>
