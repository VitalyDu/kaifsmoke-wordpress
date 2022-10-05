<?php
/*
Plugin Name: Управление ограничениями для пользователя
Description: Плагин позволяет управлять заблюриванием изображений товаров, а также показывает POPUP о возрасте
Version: 0.1
Author: Сергей Ильин
License: GNU
*/


define('ANBLOG_MASSR_DIR', plugin_dir_path(__FILE__)); //полный путь к корню папки плагина (от сервера)
define('ANBLOG_MASSR_URL', plugin_dir_url(__FILE__)); //путь к корню папки плагина (лучше его использовать)

if (defined('WP_INSTALL_PLUGIN')){
  global $wpdb;

}

add_action('admin_menu', 'anblog_bloknot_menu' ); 

function anblog_bloknot_menu() {
add_menu_page('Плагин позволяет управлять заблюриванием изображений товаров, а также показывает POPUP о возрасте', 'Управление ограничениями для пользователя', 'manage_options', 'age-management-isb/admin-settings.php', '', 'dashicons-edit' );
if ( function_exists ( 'add_menu_page' ) ) {

} }



?>