<?php
/**
 * Plugin Name: LeadBack
 * Author: leadbackru
 * Author URI: leadback.ru
 * Plugin URI: https://leadback.ru/
 * Description: This plugin makes a simple widget for callback and live chat on your website. Official LeadBack plugin.
 * Version: 1.1
 *
 * Text Domain: leadback
 * Domain Path: /languages/
 */


// Регистрируем и добавляем меню настроек плагина
function leadback_menu() {
	add_menu_page(
		'leadback',
		'LeadBack',
		'administrator',
		'leadback/options-page.php',
		'',
		'dashicons-phone'
	);
	add_action( 'admin_init', 'leadback_register_settings' );
}

add_action('admin_menu', 'leadback_menu');


// Добавляем допалнительную ссылку настроек на страницу всех плагинов
function leadback_settings_link($links) {
  $settings_link = '<a href="admin.php?page=leadback/options-page.php">' . __('Settings', 'leadback') . '</a>';
  array_unshift($links, $settings_link);
  return $links;
}

$plugin = plugin_basename(__FILE__);

add_filter("plugin_action_links_$plugin", 'leadback_settings_link' );


// Регистрируем настройки
function leadback_register_settings() {
    register_setting( 'leadback-settings-group', 'leadback_widget_code' );
}


function leadback_footer_code() {

    echo get_option('leadback_widget_code');
}

//localize
function leadback_localize_load() {
    load_plugin_textdomain( 'leadback', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action('plugins_loaded', 'leadback_localize_load');

add_action("wp_footer", "leadback_footer_code");