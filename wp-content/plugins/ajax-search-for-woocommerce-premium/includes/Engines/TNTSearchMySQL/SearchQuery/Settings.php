<?php


namespace DgoraWcas\Engines\TNTSearchMySQL\SearchQuery;


class Settings {

	public static $settingsStorage = null;

	public static function getSettings() {

		$settings = array();

		if ( ! empty( self::$settingsStorage ) ) {
			return self::$settingsStorage;
		}

		if ( defined( 'SHORTINIT' ) && SHORTINIT ) {
			global $wpdb;

			$record = $wpdb->get_var(
				"SELECT option_value
                   FROM $wpdb->options
                   WHERE option_name = 'dgwt_wcas_settings'
                   LIMIT 1"
			);

			$s = @unserialize( $record );
			if ( $record === 'b:0;' || $s !== false ) {
				$settings = $s;
			}
		} else {
			$s = get_option( 'dgwt_wcas_settings' );
			if ( ! empty( $s ) ) {
				$settings = $s;
			}

		}

		self::$settingsStorage = $settings;

		return $settings;
	}

}
