<?php

namespace GutenkitPro\Traits;

/**
 * Trait for making plugin helper functions
 * 
 * @package GutenkitPro\Traits
 */

trait PluginHelper{

	public static $instance = null;
	private static $key     = 'gutenkit_options';

    public static function is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || self::is_plugin_active_for_network( $plugin );
	}

    /**
     * Check for network plugin active
     */
	public static function is_plugin_active_for_network( $plugin ) {
		if ( ! is_multisite() ) {
			return false;
		}
	
		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[ $plugin ] ) ) {
			return true;
		}
	
		return false;
	}


	public static function get_option( $key, $default = '' ) {
		$data_all = get_option( self::$key );
		return ( isset( $data_all[ $key ] ) && $data_all[ $key ] != '' ) ? $data_all[ $key ] : $default;
	}

	public static function save_option( $key, $value = '' ) {
		$data_all         = get_option( self::$key );
		$data_all[ $key ] = $value;
		update_option( 'gutenkit_options', $data_all );
	}

	public static function get_settings( $key, $default = '' ) {
		$data_all = self::get_option( 'settings', array() );
		return ( isset( $data_all[ $key ] ) && $data_all[ $key ] != '' ) ? $data_all[ $key ] : $default;
	}

	public function save_settings( $new_data = '' ) {
		$data_old = self::get_option( 'settings', array() );
		$data     = array_merge( $data_old, $new_data );
		self::save_option( 'settings', $data );
	}


}

