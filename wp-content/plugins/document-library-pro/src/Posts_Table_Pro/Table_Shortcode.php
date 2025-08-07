<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Columns_Util;

/**
 * This class handles our posts table shortcode.
 *
 * Example usage:
 *   [posts_table
 *       post_type="band"
 *       columns="title,content,tax:country,tax:genre,cf:_price,cf:stock"
 *       tag="cool",
 *       term="country:uk,artist:beatles"]
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Shortcode implements Standard_Service, Registerable, Conditional {

	const SHORTCODE = 'posts_table';

	/**
	 * Checks if the service is required.
	 *
	 * @return bool True if the service is required, false otherwise.
	 */
	public function is_required() {
		return Util::is_front_end();
	}

	/**
	 * Registers the posts table shortcode.
	 */
	public function register() {
		// Register posts table shortcode
		add_shortcode( self::SHORTCODE, [ self::class, 'do_shortcode' ] );

		// Back-compat with free version of plugin
		add_shortcode( 'posts_data_table', [ self::class, 'do_shortcode' ] );

		// Change default shortcode attributes.
		add_filter( 'shortcode_atts_posts_table', [ $this, 'change_default_shortcode_atts' ], 10, 4 );
	}

	/**
	 * Handles our posts data table shortcode.
	 *
	 * @param array  $atts    The attributes passed in to the shortcode
	 * @param string $content The content passed to the shortcode (not used)
	 * @return string The shortcode output
	 */
	public static function do_shortcode( $atts, $content = '' ) {
		if ( ! self::can_do_shortocde() ) {
			return '';
		}

		// Fill-in missing attributes, and ensure back compat for old attribute names.
		$r = shortcode_atts( Table_Args::get_site_defaults(), self::back_compat_args( (array) $atts ), self::SHORTCODE );

		// Return the table as HTML
		return apply_filters( 'document_library_pro_shortcode_output', dlp_get_posts_table( $r ) );
	}

	/**
	 * Determines if the shortcode can be executed.
	 *
	 * @return bool True if the shortcode can be executed, false otherwise.
	 */
	private static function can_do_shortocde() {
		// Don't run in the search results page.
		if ( is_search() && in_the_loop() && ! apply_filters( 'document_library_pro_run_in_search', false ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Converts old shortcode argument names to their new equivalents for backwards compatibility.
	 *
	 * @param array $args The shortcode arguments to convert
	 * @return array The converted arguments with old names replaced by new ones
	 */
	private static function back_compat_args( array $args ) {
		$compat = [
			'post_status' => 'status',
		];

		foreach ( $compat as $old => $new ) {
			if ( isset( $args[ $old ] ) ) {
				$args[ $new ] = $args[ $old ];
				unset( $args[ $old ] );
			}
		}

		$has_date_columns_argument = isset( $args['date_columns'] );

		if ( $has_date_columns_argument ) {
			if ( isset( $args['column_type'] ) && is_array( $args['column_type'] ) ) {
				// Convert date_columns string to array
				$date_columns = array_map( 'trim', explode( ',', $args['date_columns'] ) );

				$args['column_type'] = array_map(
					function ( $column_type ) use ( $date_columns ) {
						// Get the base column name by removing '::date' if it exists
						$base_column = substr( $column_type, -6 ) === '::date' ?
							substr( $column_type, 0, strlen( $column_type ) - 6 ) :
							$column_type;

						// Check if this column is in date_columns
						if ( in_array( $base_column, $date_columns, true ) && substr( $column_type, -6 ) === '::date' ) {
							return 'date';
						}
						return $column_type;
					},
					$args['column_type']
				);
			} elseif ( ! isset( $args['column_type'] ) ) {
				$columns = Columns_Util::parse_columns( $args['columns'] );

				// Convert date_columns string to array if it exists
				$date_columns = isset( $args['date_columns'] ) ? array_map( 'trim', explode( ',', $args['date_columns'] ) ) : [];

				$column_types = [];

				foreach ( $columns as $column_key => $column_title ) {
					// Set type to 'date' if column is in date_columns, otherwise 'auto'
					$type = in_array( $column_key, $date_columns, true ) ? 'date' : 'auto';

					// Use double colons (::) if the column key already contains a colon
					$separator      = strpos( $column_key, ':' ) !== false ? '::' : ':';
					$column_types[] = $column_key . $separator . $type;
				}

				if ( ! empty( $column_types ) ) {
					$args['column_type'] = implode( ',', $column_types );
				}
			}
		}

		return $args;
	}

	/**
	 * Change the default shortcode attributes.
	 *
	 * @param array  $out       The output array of shortcode attributes.
	 * @param array  $pairs     The supported attributes and their defaults.
	 * @param array  $atts      The user defined shortcode attributes.
	 * @param string $shortcode The shortcode name.
	 * @return array The output array of shortcode attributes.
	 */
	public function change_default_shortcode_atts( $out, $pairs, $atts, $shortcode ) {
		// Sets the default WooCommerce orders status to any.
		if ( isset( $atts['post_type'] ) && $atts['post_type'] === 'shop_order' && class_exists( 'WooCommerce' ) && ! isset( $atts['status'] ) ) {
			$out['status'] = 'any';
		}

		return $out;
	}
}
