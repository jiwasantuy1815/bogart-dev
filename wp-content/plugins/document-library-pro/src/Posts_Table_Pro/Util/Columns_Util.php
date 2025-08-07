<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util;

/**
 * Column utility functions.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Columns_Util {

	/**
	 * Column default headings and priorities.
	 *
	 * @var array
	 */
	private static $column_defaults = null;

	public static function check_blank_heading( $heading ) {
		return 'blank' === $heading ? '' : $heading;
	}

	public static function column_defaults() {

		if ( empty( self::$column_defaults ) ) {
			// Priority values are used to determine visibility at small screen sizes (1 = highest priority on mobile).
			self::$column_defaults = apply_filters(
				'document_library_pro_column_defaults',
				[
					'id'            => [
						'heading'  => __( 'ID', 'document-library-pro' ),
						'priority' => 3,
					],
					'title'         => [
						'heading'  => __( 'Title', 'document-library-pro' ),
						'priority' => 1,
					],
					'content'       => [
						'heading'  => __( 'Content', 'document-library-pro' ),
						'priority' => 8,
					],
					'excerpt'       => [
						'heading'  => __( 'Summary', 'document-library-pro' ),
						'priority' => 4,
					],
					'date'          => [
						'heading'  => __( 'Date', 'document-library-pro' ),
						'priority' => 5,
					],
					'date_modified' => [
						'heading'  => __( 'Last Modified', 'document-library-pro' ),
						'priority' => 5,
					],
					'author'        => [
						'heading'  => __( 'Author', 'document-library-pro' ),
						'priority' => 9,
					],
					'categories'    => [
						'heading'  => __( 'Categories', 'document-library-pro' ),
						'priority' => 6,
					],
					'tags'          => [
						'heading'  => __( 'Tags', 'document-library-pro' ),
						'priority' => 10,
					],
					'status'        => [
						'heading'  => __( 'Status', 'document-library-pro' ),
						'priority' => 11,
					],
					'image'         => [
						'heading'  => __( 'Image', 'document-library-pro' ),
						'priority' => 2,
					],
					'button'        => [
						'heading'  => '',
						'priority' => 7,
					],
				]
			);
		}

		return self::$column_defaults;
	}

	public static function columns_array_to_string( array $columns ) {
		$combine_column = function ( $column, $heading ) {
			return '' === $heading ? $column : $column . ':' . $heading;
		};
		return implode( ',', array_map( $combine_column, array_keys( $columns ), array_values( $columns ) ) );
	}

	public static function get_column_class( $column ) {
		$column_class_suffix = self::unprefix_column( $column );

		// Certain classes are reserved for use by DataTables Responsive, so we need to strip these to prevent conflicts.
		$column_class_suffix = trim( str_replace( [ 'mobile', 'tablet', 'desktop' ], '', $column_class_suffix ), '_- ' );

		return $column_class_suffix ? Util::sanitize_class_name( 'col-' . $column_class_suffix ) : '';
	}

	public static function get_column_data_source( $column ) {
		// '.' not allowed in data source
		return str_replace( '.', '', $column );
	}

	public static function get_column_name( $column ) {
		// ':' not allowed in column name as not compatible with DataTables API.
		return str_replace( ':', '_', $column );
	}

	/**
	 * Get the taxonomy slug for a column name.
	 *
	 * @param string $column The name of the column.
	 * @return string|false The taxonomy slug, or false if the column is not a taxonomy column.
	 */
	public static function get_column_taxonomy( $column ) {
		if ( self::is_hidden_filter( $column ) ) {
			$column = self::get_hidden_filter( $column );
		}

		$tax = $column;

		if ( 'categories' === $column ) {
			$tax = 'category';
		} elseif ( 'tags' === $column ) {
			$tax = 'post_tag';
		} elseif ( self::is_custom_taxonomy( $column ) ) {
			$tax = self::get_custom_taxonomy( $column );
		}

		if ( taxonomy_exists( $tax ) ) {
			return $tax;
		}

		return false;
	}

	public static function is_custom_field( $column ) {
		return $column && 'cf:' === substr( $column, 0, 3 );
	}

	public static function get_custom_field( $column ) {
		if ( self::is_custom_field( $column ) ) {
			return substr( $column, 3 );
		}
		return false;
	}

	public static function is_custom_taxonomy( $column ) {
		return $column && 'tax:' === substr( $column, 0, 4 );
	}

	public static function get_custom_taxonomy( $column ) {
		if ( self::is_custom_taxonomy( $column ) ) {
			return substr( $column, 4 );
		}
		return false;
	}

	public static function is_hidden_filter( $column ) {
		return $column && 'hf:' === substr( $column, 0, 3 );
	}

	public static function get_hidden_filter( $column ) {
		if ( self::is_hidden_filter( $column ) ) {
			return substr( $column, 3 );
		}
		return false;
	}

	/**
	 * Parse the supplied columns into an array, whose keys are the column names, and values are the column headings (if specified).
	 *
	 * Invalid taxonomies are removed, but non-standard columns are kept as they could be custom columns. Custom field keys are not validated.
	 *
	 * E.g. parse_columns( 'title,author:User,content,tax:region:Area,cf:my_field' );
	 *
	 * Returns:
	 *
	 * [ 'title' => '', 'author' => 'User', 'content' => '', 'tax:region' => 'Area', 'cf:my_field' => '' ];
	 *
	 * @param string|string[] $columns The columns to parse as a string or array of strings.
	 * @return array The parsed columns.
	 */
	public static function parse_columns( $columns ) {
		if ( ! is_array( $columns ) ) {
			$columns = str_replace( '\,', '{comma}', str_replace( '\;', '{semicolon}', $columns ) );
			$columns = Util::string_list_to_array( $columns );
		}

		$combined    = '';
		$is_combined = false;

		$parsed = [];

		foreach ( $columns as $column ) {
			$prefix = sanitize_key( strtok( $column, ':' ) );
			$col    = false;
			$label  = null;

			if ( $column[0] === '(' && strpos( $column, ')' ) !== false ) {
				$col_label = explode( ')', $column );
				$col       = substr( $col_label[0], 1 );
				$label     = isset( $col_label[1] ) && is_string( $col_label[1] ) && strlen( $col_label[1] ) > 0 && $col_label[1][0] === ':' ? substr( $col_label[1], 1 ) : '';
			} elseif ( $column[0] === '(' ) {
				$combined   .= substr( $column, 1 );
				$is_combined = true;
			} elseif ( $is_combined ) {
				$combined .= ',' . $column;
				if ( strpos( $column, ')' ) !== false && $is_combined ) {
					$col_label   = explode( ')', $combined );
					$col         = $col_label[0];
					$label       = $col_label[1][0] === ':' ? substr( $col_label[1], 1 ) : '';
					$is_combined = false;
					$combined    = '';
				}
			} elseif ( in_array( $prefix, [ 'cf', 'tax' ], true ) ) {
				// Custom field or taxonomy.
				$suffix = trim( strtok( ':' ) );

				if ( ! $suffix ) {
					continue; // no custom field or taxonomy specified
				} elseif ( 'tax' === $prefix && ! taxonomy_exists( $suffix ) ) {
					continue; // invalid taxonomy
				}

				$col = $prefix . ':' . $suffix;
			} else {
				// Standard or custom column.
				$col = $prefix;
			}

			// Only add column if valid and not added already.
			if ( $col && ! array_key_exists( $col, $parsed ) && ! $is_combined ) {
				// $parsed[ $col ] = self::sanitize_heading( strtok( '' ) );
				$parsed[ $col ] = self::sanitize_heading( $label ?? strtok( '' ) );
			}
		}

		return $parsed;
	}

	/**
	 * Parse the supplied filters into an array, whose keys are the filter names, and values are the filter headings (if specified).
	 *
	 * Invalid filter columns and taxonomies are removed. When $filters = true, the filters are based on the table contents and this
	 * is specified by passing the columns in the $table_columns arg.
	 *
	 * E.g. parse_filters( 'categories:Category,invalid,tags,tax:region:Area' );
	 *
	 * Returns:
	 *
	 * [ 'categories' => 'Category', 'tags' => '', 'tax:region' => 'Area' ];
	 *
	 * @param bool|string|string[] $filters       The filters to parse as a string or array of strings.
	 * @param string[]             $table_columns The columns to base the filters on when $filters = true.
	 * @return array The parsed filters, or an empty array if the filters are invalid.
	 */
	public static function parse_filters( $filters, $table_columns = [] ) {
		$parsed         = [];
		$filter_columns = Util::maybe_parse_bool( $filters );

		if ( true === $filter_columns ) {
			$filter_columns = $table_columns;
		} elseif ( empty( $filter_columns ) ) {
			$filter_columns = [];
		}

		if ( ! is_array( $filter_columns ) ) {
			$filter_columns = Util::string_list_to_array( $filter_columns );
		}

		foreach ( $filter_columns as $filter ) {
			$f                  = false;
			$prefix             = strtok( $filter, ':' );
			$filterable_columns = apply_filters( 'document_library_pro_filterable_columns', [ 'categories', 'tags' ] );

			if ( in_array( $prefix, $filterable_columns, true ) ) {
				// Category or tags filter.
				$f = $prefix;
			} elseif ( 'tax' === $prefix ) {
				// Custom taxonomy filter.
				$tax = trim( strtok( ':' ) );

				if ( taxonomy_exists( $tax ) ) {
					$f = 'tax:' . $tax;
				}
			}

			if ( $f && ! array_key_exists( $f, $parsed ) ) {
				$parsed[ $f ] = self::sanitize_heading( strtok( '' ) );
			}
		}

		return $parsed;
	}

	public static function remove_non_applicable_columns( $columns, $post_type ) {
		if ( empty( $post_type ) ) {
			return $columns;
		}

		// Convert any 'attachment:mime_type' combinations to just 'attachment'.
		if ( is_string( $post_type ) && 0 === strpos( $post_type, 'attachment' ) ) {
			$post_type = 'attachment';
		}

		if ( is_array( $post_type ) || ( is_string( $post_type ) && ! in_array( $post_type, [ 'attachment', 'any' ], true ) ) ) {
			// Get taxonomies for all post types.
			$taxonomies = get_object_taxonomies( $post_type );

			foreach ( $columns as $column => $heading ) {
				if ( ( 'categories' === $column || 'tax:category' === $column ) && ! in_array( 'category', $taxonomies, true ) ) {
					unset( $columns[ $column ] );
				} elseif ( ( 'tags' === $column || 'tax:post_tag' === $column ) && ! in_array( 'post_tag', $taxonomies, true ) ) {
					unset( $columns[ $column ] );
				} elseif ( $tax = self::get_custom_taxonomy( $column ) ) {
					if ( ! in_array( $tax, $taxonomies, true ) ) {
						unset( $columns[ $column ] );
					}
				}
			}
		}

		return $columns;
	}

	public static function sanitize_heading( $heading ) {
		return esc_html( $heading );
	}

	public static function unprefix_column( $column ) {
		if ( false !== ( $str = strstr( $column, ':' ) ) ) {
			$column = substr( $str, 1 );
		}
		return $column;
	}

	/**
	 * Get column type for a specific provided column
	 *
	 * @param array $columnns
	 * @param string $type
	 * @return string|void
	 */
	public static function get_column_type( $columnns, $type ) {
		foreach ( $columnns as $column ) {
			$column = explode( '::', $column );
			if ( isset( $column[0] ) && $column[0] === $type && isset( $column[1] ) ) {
				return $column[1];
			}
		}

		return;
	}

	/**
	 * Checks if a specific custom field is being used to retrieve specific posts for custom fields.
	 *
	 * Determines whether the current custom field matches the field name in the filter data.
	 * This is used to verify if posts are being filtered by a specific custom field.
	 *
	 * @param string $cf_field      The custom field name to check.
	 * @param string $cf_filter_data The filter data string to compare against.
	 * @return bool Returns true if the custom field matches the filter, false if no filter data.
	 */
	public static function is_retreiving_specific_cf_field_posts( $cf_field, $cf_filter_data ) {
		if ( ! $cf_filter_data ) {
			return false;
		}

		$cf_field_name = explode( ':', $cf_filter_data );

		if ( isset( $cf_field_name[0] ) && $cf_field === $cf_field_name[0] ) {
			return true;
		}

		return false;
	}

	// DEPRECATED

	public static function is_filter_column( $column ) {
		_deprecated_function( __METHOD__, '2.5.1', 'is_hidden_filter' );
		return self::is_hidden_filter( $column );
	}

	public static function get_filter_column( $column ) {
		_deprecated_function( __METHOD__, '2.5.1', 'get_hidden_filter' );
		return self::get_hidden_filter( $column );
	}

	/**
	 * Retrieves the combined column name if it is a combined column.
	 *
	 * A combined column is identified by the presence of either a comma (',')
	 * or a semicolon (';') in the column name.
	 *
	 * @param string $column The column name to check.
	 * @return string|false The combined column name if it is a combined column, false otherwise.
	 */
	public static function get_combined_column( $column ) {
		if ( self::is_combined_column( $column ) ) {
			return $column;
		}
		return false;
	}

	/**
	 * Checks if the given column name is a combined column.
	 *
	 * A combined column is identified by the presence of either a comma (',')
	 * or a semicolon (';') in the column name.
	 *
	 * @param string $column The column name to check.
	 * @return bool True if the column is a combined column, false otherwise.
	 */
	public static function is_combined_column( $column ) {
		return strpos( $column, ',' ) !== false || strpos( $column, ';' ) !== false;
	}
}
