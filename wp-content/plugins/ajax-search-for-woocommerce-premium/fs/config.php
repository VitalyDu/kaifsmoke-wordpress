<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
class dgoraAsfwFsNull {
    public function is_premium() {
        return true;
    }
    public function is__premium_only() {
        return true;
    }
    public function contact_url() {
        return '';
    }
    public function get_account_url() {
        return '';
    }
}
// Create a helper function for easy SDK access.
function dgoraAsfwFs()
{
    global  $dgoraAsfwFs ;
    
    if ( !isset( $dgoraAsfwFs ) ) {
        // Include Freemius SDK.
        require_once dirname( __FILE__ ) . '/lib/start.php';
        $dgoraAsfwFs = new dgoraAsfwFsNull();
    }
    
    return $dgoraAsfwFs;
}

// Init Freemius.
dgoraAsfwFs();
// Signal that SDK was initiated.
do_action( 'dgoraAsfwFs_loaded' );
add_filter( 'plugin_icon', function () {
    return dirname( dirname( __FILE__ ) ) . '/assets/img/logo-128.png';
} );
// Uninstall
if ( dgoraAsfwFs()->is__premium_only() ) {
    add_action( 'after_uninstall', function () {
        global  $wpdb ;
        /* ----------------------
         * WIPE DATABASE TABLES
         * --------------------- */
        $pluginTables = array();
        $tables = $wpdb->get_results( "SHOW TABLES" );
        if ( !empty($tables) && is_array( $tables ) ) {
            foreach ( $tables as $table ) {
                if ( !empty($table) && is_object( $table ) ) {
                    foreach ( $table as $tableName ) {
                        if ( !empty($tableName) && is_string( $tableName ) && strpos( $tableName, 'dgwt_wcas_' ) !== false ) {
                            $pluginTables[] = $tableName;
                        }
                    }
                }
            }
        }
        foreach ( $pluginTables as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
        }
        /* ----------------------
         * WIPE SETTINGS
         * --------------------- */
        delete_option( 'dgwt_wcas_indexer_last_build' );
        delete_transient( 'dgwt_wcas_indexer_details_display' );
        if ( is_multisite() ) {
            foreach ( get_sites() as $site ) {
                
                if ( is_numeric( $site->blog_id ) && $site->blog_id > 1 ) {
                    $table = $wpdb->prefix . $site->blog_id . '_' . 'options';
                    $wpdb->delete( $table, array(
                        'option_name' => 'dgwt_wcas_indexer_last_build',
                    ) );
                    $wpdb->delete( $table, array(
                        'option_name' => '_transient_timeout_dgwt_wcas_indexer_details_display',
                    ) );
                    $wpdb->delete( $table, array(
                        'option_name' => '_transient_dgwt_wcas_indexer_details_display',
                    ) );
                }
            
            }
        }
        /* ----------------------
         * WIPE FILES (DEPRECATED)
         * --------------------- */
        $upload_dir = wp_upload_dir();
        
        if ( !empty($upload_dir['basedir']) ) {
            $path = $upload_dir['basedir'] . '/wcas-search/';
            
            if ( file_exists( $path ) ) {
                $index = $path . 'products.index';
                if ( file_exists( $index ) ) {
                    unlink( $index );
                }
                rmdir( $path );
            }
        
        }
    
    } );
}