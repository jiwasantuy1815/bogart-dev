<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util;

/**
 * Functions for handling the Posts Table Pro plugin options.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Options {

	const SHORTCODE_OPTION_KEY = 'dlp_shortcode_defaults';
	const MISC_OPTION_KEY      = 'dlp_misc_settings';
	const SEARCH_PAGE_KEY      = 'dlp_search_page';

	public static function get_shortcode_options( array $defaults = [] ) {
		return self::table_settings_to_args( self::get_option( self::SHORTCODE_OPTION_KEY, $defaults ), $defaults );
	}

	public static function get_misc_options() {
		$defaults = [
			'cache_expiry' => 6,
			'design'       => 'default'
		];

		return self::get_option( self::MISC_OPTION_KEY, $defaults );
	}

	public static function get_search_page_option() {
		$search_page = (int) get_option( self::SEARCH_PAGE_KEY, false ) ?? false;

		if ( $search_page && in_array( get_post_status( $search_page ), [ false, 'trash' ], true ) ) {
			$search_page = false;
		}

		return $search_page;
	}

	/**
	 * Retrieves the design template settings by merging defaults with saved options.
	 *
	 * @return array The merged template settings.
	 */
	public static function get_design_template_settings() {
		$defaults = Defaults::get_design_defaults_for_templates();
		$options  = self::get_setting_table_styling();
		$defaults = self::merge_design_defaults_with_options( $defaults, $options );

		return $defaults;
	}

	public static function get_setting_table_styling() {
		$options = self::get_misc_options();
		unset($options['cache_expiry']);

		return $options;
	}

	/**
	 * Merges the default design settings with saved options.
	 *
	 * @param array $defaults The default design settings.
	 * @param array $options  The saved design options from the settings.
	 *
	 * @return array The merged design settings, with saved options overriding defaults.
	 */
	public static function merge_design_defaults_with_options( $defaults, $options ) {
		$theme             = ! empty( $options['design'] ) ? $options['design'] : 'default';
		$formatted_options = [];

		unset( $options['design'] );
		foreach ( $options as $key => $option ) {
			if ( ! empty( $option['color'] ) ) {
				$formatted_options[ $key ]['color'] = $option['color'];
			} else {
				$formatted_options[ $key ] = $option;
			}
			if ( ! empty( $option['size'] ) ) {
				$formatted_options[ $key ]['size'] = $option['size'];
			}
		}

		$defaults[ $theme ] = $formatted_options;

		return $defaults;
	}

	public static function get_cache_expiration_length() {
		$options = self::get_misc_options();

		return filter_var(
			$options['cache_expiry'],
			FILTER_VALIDATE_INT,
			[
				'options' => [
					'default'   => 6,
					'min_range' => 1
				]
			]
		);
	}

	private static function get_option( $option, $default ) {
		$value = get_option( $option, $default );

		if ( empty( $value ) || ( is_array( $default ) && ! is_array( $value ) ) ) {
			$value = $default;
		}

		if ( is_array( $value ) && is_array( $default ) ) {
			$value = array_merge( $default, $value );
		}

		return $value;
	}

	private static function table_settings_to_args( array $options, array $defaults = [] ) {
		if ( empty( $options ) ) {
			return $defaults;
		}

		$options = array_merge( $defaults, $options );

		// Check free text options are not empty.
		foreach ( [ 'columns', 'image_size', 'links' ] as $arg ) {
			if ( empty( $options[ $arg ] ) && ! empty( $defaults[ $arg ] ) ) {
				$options[ $arg ] = $defaults[ $arg ];
			}
		}

		// Sanitize custom filters option.
		if ( 'custom' === $options['filters'] ) {
			$options['filters'] = ! empty( $options['filters_custom'] ) ? $options['filters_custom'] : ( $defaults['filters'] ?? '' );
		}

		unset( $options['filters_custom'] );

		// Sanitize sort by option.
		if ( 'custom' === $options['sort_by'] ) {
			if ( ! empty( $options['sort_by_custom'] ) ) {
				$options['sort_by'] = $options['sort_by_custom'];
			} else if ( isset( $defaults['sort_by'] ) ) {
				$options['sort_by'] = $defaults['sort_by'];
			} else {
				$options['sort_by'] = '';
			}
		}

		unset( $options['sort_by_custom'] );

		// Convert 'true' or 'false' strings to booleans.
		$options = array_map( [ Util::class, 'maybe_parse_bool' ], $options );

		return $options;
	}

}
