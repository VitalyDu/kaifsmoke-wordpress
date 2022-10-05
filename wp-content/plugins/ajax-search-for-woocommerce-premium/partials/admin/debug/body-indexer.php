<?php

use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Builder;

// Exit if accessed directly
if ( ! defined( 'DGWT_WCAS_FILE' ) ) {
	exit;
}
?>


<h2>Maintenance</h2>
<form action="<?php echo admin_url( 'admin.php' ); ?>" method="get">
	<input type="hidden" name="page" value="dgwt_wcas_debug">
	<input type="submit" name="dgwt-wcas-debug-delete-db-tables" class="button" value="Delete DB tables">
	<input type="submit" name="dgwt-wcas-debug-delete-indexer-options" class="button"
		   value="Delete Indexer options">

	<?php if ( is_multisite() ): ?>
		<br/><br/>
		<input type="submit" name="dgwt-wcas-debug-delete-db-tables-ms" class="button"
			   value="Delete DB tables (Network)"></button>
		<input type="submit" name="dgwt-wcas-debug-delete-indexer-options-ms" class="button"
			   value="Delete Indexer options (Network)"></button>
	<?php endif; ?>
</form>
<?php

if ( ! empty( $_GET['dgwt-wcas-debug-delete-db-tables'] ) ) {
	Builder::deleteDatabaseTables();
	echo 'tables deleted';
}

if ( ! empty( $_GET['dgwt-wcas-debug-delete-indexer-options'] ) ) {
	Builder::deleteIndexOptions();
	echo 'settings deleted';
}

if ( ! empty( $_GET['dgwt-wcas-debug-delete-db-tables-ms'] ) ) {
	Builder::deleteDatabaseTables( true );
	echo 'tables deleted (ms)';
}

if ( ! empty( $_GET['dgwt-wcas-debug-delete-indexer-options-ms'] ) ) {
	Builder::deleteIndexOptions( true );
	echo 'settings deleted (ms)';
}

?>

<h2>Extended indexer debug logs</h2>
<?php
if ( ! empty( $_GET['dgwt-wcas-debug-enable-indexer-debug'] ) ) {
	set_transient( Builder::INDEXER_DEBUG_TRANSIENT_KEY, true, 12 * HOUR_IN_SECONDS );
	set_transient( Builder::INDEXER_DEBUG_SCOPE_TRANSIENT_KEY, array( 'all' ), 12 * HOUR_IN_SECONDS );
	?>
	<div class="dgwt-wcas-notice notice notice-success">
		<p>Indexer debug is now enabled with scope: all</p>
	</div>
	<?php
}
if ( ! empty( $_GET['dgwt-wcas-debug-disable-indexer-debug'] ) ) {
	delete_transient( Builder::INDEXER_DEBUG_TRANSIENT_KEY );
	?>
	<div class="dgwt-wcas-notice notice notice-success">
		<p>Indexer debug is now disabled</p>
	</div>
	<?php
}
if ( ! empty( $_GET['dgwt-wcas-debug-save-indexer-debug-scope'] ) && ! empty( $_GET['dgwt-wcas-debug-indexer-debug-scope'] ) ) {
	set_transient( Builder::INDEXER_DEBUG_TRANSIENT_KEY, true, 12 * HOUR_IN_SECONDS );
	set_transient( Builder::INDEXER_DEBUG_SCOPE_TRANSIENT_KEY, $_GET['dgwt-wcas-debug-indexer-debug-scope'], 12 * HOUR_IN_SECONDS );
	?>
	<div class="dgwt-wcas-notice notice notice-success">
		<p>Indexer debug scope saved</p>
	</div>
	<?php
}
?>
<form action="<?php echo admin_url( 'admin.php' ); ?>" method="get">
	<input type="hidden" name="page" value="dgwt_wcas_debug">

	<strong>
		Debug state: <?php echo Builder::isDebug() ? 'enabled' : 'disabled'; ?>
		<?php echo defined( 'DGWT_WCAS_INDEXER_DEBUG' ) ? ( '(via DGWT_WCAS_INDEXER_DEBUG)' ) : ''; ?>
	</strong>
	<br/>
	<br/>
	<strong>
		Scope
		<?php echo defined( 'DGWT_WCAS_INDEXER_DEBUG_SCOPE' ) ? ( '(via DGWT_WCAS_INDEXER_DEBUG_SCOPE)' ) : ''; ?>
	</strong>
	<br/>
	<?php foreach ( Builder::$indexerDebugScopes as $scope ) {
		if ( $scope === 'all' ) {
			continue;
		}
		?>
		<label for="indexer-debug-scope-<?php echo $scope ?>">
			<input id="indexer-debug-scope-<?php echo $scope ?>" type="checkbox"
				   name="dgwt-wcas-debug-indexer-debug-scope[]"
				   value="<?php echo $scope ?>" <?php checked( Builder::isDebugScopeActive( $scope ) ) ?>
				<?php disabled( defined( 'DGWT_WCAS_INDEXER_DEBUG_SCOPE' ) ) ?>>
			<?php echo $scope; ?>
		</label>
		<br/>
	<?php } ?>
	<br/>
	<input type="submit" name="dgwt-wcas-debug-enable-indexer-debug" class="button"
		   value="Enable debug with scope: all" <?php disabled( defined( 'DGWT_WCAS_INDEXER_DEBUG' ) ) ?>>
	<input type="submit" name="dgwt-wcas-debug-save-indexer-debug-scope" class="button"
		   value="Enable debug with selected scope" <?php disabled( defined( 'DGWT_WCAS_INDEXER_DEBUG_SCOPE' ) ) ?>>
	<input type="submit" name="dgwt-wcas-debug-disable-indexer-debug" class="button"
		   value="Disable debug" <?php disabled( defined( 'DGWT_WCAS_INDEXER_DEBUG' ) ) ?>>
</form>

