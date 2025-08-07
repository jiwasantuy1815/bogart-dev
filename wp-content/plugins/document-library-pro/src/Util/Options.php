<?php
namespace Barn2\Plugin\Document_Library_Pro\Util;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Options as PTP_Options;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Columns_Util as PTP_Columns_Util;
use Barn2\Plugin\Document_Library_Pro\Taxonomies;

defined( 'ABSPATH' ) || exit;

/**
 * Settings Options Utilities
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Options {

	// new settings key
	const SETTINGS_KEY = 'document-library-pro_settings';

	const DOCUMENT_FIELDS_OPTION_KEY         = 'dlp_document_fields';
	const DOCUMENT_SLUG_OPTION_KEY           = 'dlp_document_slug';
	const DOCUMENT_PAGE_OPTION_KEY           = 'dlp_document_page';
	const SEARCH_PAGE_OPTION_KEY             = 'dlp_search_page';
	const SHORTCODE_OPTION_KEY               = 'dlp_shortcode_defaults';
	const MISC_OPTION_KEY                    = 'dlp_misc_settings';
	const SINGLE_DOCUMENT_DISPLAY_OPTION_KEY = 'dlp_document_fields_display';
	const FOLDER_CLOSE_SVG_OPTION_KEY        = 'dlp_folder_icon_svg_closed';
	const FOLDER_OPEN_SVG_OPTION_KEY         = 'dlp_folder_icon_svg_open';

	const GENERAL_OPTION_GROUP = 'document_library_pro_general';
	const TABLE_OPTION_GROUP   = 'document_library_pro_table';
	const GRID_OPTION_GROUP    = 'document_library_pro_grid';
	const SINGLE_OPTION_GROUP  = 'document_library_pro_single_document';

	/**
	 * Update the shortcode options.
	 *
	 * @param array $values
	 * @return bool
	 */
	public static function update_shortcode_option( $values = [] ) {
		return self::update_settings( $values );
	}

	/**
	 * Retrieve the user shortcode options.
	 *
	 * @return array
	 */
	public static function get_user_shortcode_options() {
		return self::get_shortcode_options( array_merge( Table_Args::get_table_defaults(), self::get_dlp_specific_default_args(), self::get_v2_default_args() ) );
	}

	/**
	 * Retrieve the shortcode options.
	 * We use the PTP function to maintain consistency across grid and table code.
	 *
	 * @param array $defaults
	 * @return array
	 */
	public static function get_shortcode_options( array $defaults = [] ) {
		$settings     = self::get_option( self::SHORTCODE_OPTION_KEY, $defaults );
		$new_settings = self::get_settings();

		// TODO: here is the bug relating to some new settings not being in the correct format
		// the compatibility layer won't always kick in due to priority.
		if ( ! empty( $new_settings ) ) {
			$settings = array_merge( $settings, $new_settings );
		}

		return self::sanitize_shortcode_options( $settings, $defaults );
	}

	/**
	 * Retrieve the new settings key and format the options for table args.
	 *
	 * We need to preserve the existing table args for backwards compatibility with the shortcodes.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = self::get_option( self::SETTINGS_KEY, [] );

		return $settings;
	}

	/**
	 * Update the settings.
	 *
	 * @param array $values
	 * @return bool
	 */
	public static function update_settings( $values = [] ) {
		if ( ! is_array( $values ) || empty( $values ) ) {
			return false;
		}

		$options = self::get_settings();

		$allowed_keys = array_keys( array_merge( Table_Args::get_table_defaults(), self::get_dlp_specific_default_args(), self::get_v2_default_args(), [ 'filters_custom' => '' ] ) );

		foreach ( $values as $key => $value ) {
			if ( ! in_array( $key, $allowed_keys, true ) ) {
				unset( $values[ $key ] );
			}
		}

		update_option( self::SETTINGS_KEY, array_merge( $options, $values ) );
	}

	/**
	 * Sanitize the shortcode options.
	 *
	 * @param array $options
	 * @param array $defaults
	 * @return array
	 */
	private static function sanitize_shortcode_options( array $options, array $defaults = [] ) {
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
		if ( isset( $options['filters'] ) && 'custom' === $options['filters'] ) {
			$options['filters'] = ! empty( $options['filters_custom'] ) ? $options['filters_custom'] : $defaults['filters'];
		}

		unset( $options['filters_custom'] );

		// Sanitize sort by option.
		if ( isset( $options['sort_by'] ) && 'custom' === $options['sort_by'] ) {
			$options['sort_by'] = ! empty( $options['sort_by_custom'] ) ? $options['sort_by_custom'] : ( $defaults['sort_by'] ?? '' );
		}

		unset( $options['sort_by_custom'] );

		// Convert 'true' or 'false' strings to booleans.
		$options = array_map( [ self::class, 'maybe_parse_bool' ], $options );

		// Adjust grid column if converted above.
		if ( isset( $options['grid_columns'] ) && $options['grid_columns'] === true ) {
			$options['grid_columns'] = '1';
		}

		if ( isset( $options['grid_content'] ) ) {
			$options['grid_content'] = self::sanitize_grid_content( $options['grid_content'] );
		}

		return $options;
	}

	/**
	 * Get additional options.
	 * We use the PTP function to maintain consistency across grid and table code.
	 *
	 * @return mixed
	 */
	public static function get_additional_options() {
		return PTP_Options::get_misc_options();
	}

	/**
	 * Retrive the Document post type fields.
	 *
	 * @return array
	 */
	public static function get_document_fields() {
		$document_fields_structure = [
			'editor'        => '1',
			'excerpt'       => '1',
			'thumbnail'     => '1',
			'comments'      => '0',
			'author'        => '1',
			'custom-fields' => '0',
		];

		$settings = self::get_settings();
		$fields   = isset( $settings['document_fields'] ) ? $settings['document_fields'] : [];

		if ( empty( $fields ) ) {
			$fields = $document_fields_structure;

			$fields = array_keys(
				array_filter(
					$fields,
					function ( $field ) {
						return $field === '1';
					}
				)
			);
		}

		return $fields;
	}

	/**
	 * Retrieve the single document display option.
	 *
	 * @return array
	 */
	public static function get_document_display_fields() {
		$document_fields_display_structure = self::get_document_display_default_structure();

		$settings = self::get_settings();
		$fields   = isset( $settings['single_document_fields'] ) ? $settings['single_document_fields'] : [];

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			$fields = $document_fields_display_structure;

			$fields = array_keys(
				array_filter(
					$fields,
					function ( $field ) {
						return $field === '1';
					}
				)
			);

		}

		return $fields;
	}

	/**
	 * Get the document display default structure.
	 *
	 * @return array
	 */
	public static function get_document_display_default_structure() {
		$structure = [
			'thumbnail'      => '1',
			'comments'       => '0',
			'doc_categories' => '1',
			'doc_tags'       => '1',
			'doc_author'     => '1',
			'file_type'      => '1',
			'file_size'      => '1',
			'filename'       => '0',
			'custom-fields'  => '0',
			'download_count' => '0',
		];

		// maybe add excerpt option to the beginning of the array
		if ( self::uses_excerpt_option() ) {
			$structure = [ 'excerpt' => '1' ] + $structure;
		}

		return $structure;
	}

	/**
	 * Get the document display option labels
	 */
	public static function get_document_display_option_labels() {
		$labels = [
			'thumbnail'      => __( 'Featured image', 'document-library-pro' ),
			'comments'       => __( 'Comments', 'document-library-pro' ),
			'doc_categories' => __( 'Categories', 'document-library-pro' ),
			'doc_tags'       => __( 'Tags', 'document-library-pro' ),
			'doc_author'     => __( 'Authors', 'document-library-pro' ),
			'file_size'      => __( 'File size', 'document-library-pro' ),
			'file_type'      => __( 'File type', 'document-library-pro' ),
			'filename'       => __( 'Filename', 'document-library-pro' ),
			'custom-fields'  => __( 'Custom fields', 'document-library-pro' ),
			'download_count' => __( 'Download count', 'document-library-pro' ),
		];

		// maybe add excerpt option to the beginning of the array
		if ( self::uses_excerpt_option() ) {
			$labels = [ 'excerpt' => __( 'Excerpt', 'document-library-pro' ) ] + $labels;
		}

		return $labels;
	}

	/**
	 * Determine if the excerpt option is enabled and in use.
	 *
	 * - New installs and existing installs with the excerpt option disabled will return false.
	 * - Used to remove the excerpt option from the single document display settings in a backwards compatible way.
	 *
	 * @return bool
	 */
	public static function uses_excerpt_option() {
		/**
		 * Allow the use of on an excerpt option on the single document page.
		 *
		 * @param bool $enable Whether to disable the excerpt option.
		 */
		if ( apply_filters( 'document_library_pro_enable_single_document_excerpt', false ) ) {
			return true;
		}

		$display_options = get_option( self::SINGLE_DOCUMENT_DISPLAY_OPTION_KEY, [] );

		return isset( $display_options['excerpt'] ) && $display_options['excerpt'] === '1';
	}

	/**
	 * Retrieve the document slug
	 *
	 * @return array
	 */
	public static function get_document_slug() {
		$settings = self::get_settings();

		return $settings['document_slug'] ?? 'document';
	}

	/**
	 * Sanitizes grid content data to the correct array format.
	 *
	 * @param mixed $fields
	 * @return array
	 */
	public static function sanitize_grid_content( $fields ) {
		if ( ! is_array( $fields ) ) {
			$fields = self::string_list_to_multicheckbox_array( $fields );
		}

		if ( is_null( $fields ) ) {
			$fields = [];
		}

		$grid_content_structure = [
			'image'          => '0',
			'title'          => '0',
			'filename'       => '0',
			'file_size'      => '0',
			'file_type'      => '0',
			'download_count' => '0',
			'doc_categories' => '0',
			'doc_author'     => '0',
			'excerpt'        => '0',
			'custom_fields'  => '0',
			'link'           => '0',
		];

		$fields = array_merge( $grid_content_structure, $fields );

		$fields = array_map(
			function ( $value ) {
				return (bool) $value;
			},
			$fields
		);

		return $fields;
	}

	/**
	 * Convert a string list to a multicheckbox array.
	 *
	 * @param mixed $string_list
	 * @return null|array
	 */
	public static function string_list_to_multicheckbox_array( $string_list ) {
		if ( ! is_string( $string_list ) ) {
			return null;
		}

		$key_array   = array_filter( array_map( 'trim', explode( ',', $string_list ) ) );
		$value_array = array_pad( [], count( $key_array ), '1' );

		$multicheckbox_array = array_combine( $key_array, $value_array );

		return $multicheckbox_array;
	}

	/**
	 * Normalize user arguments provided to shortcode.
	 *
	 * @param array $args
	 * @return array
	 */
	public static function normalize_user_arguments( $args ) {
		$settings = self::get_settings();

		// bools
		if ( isset( $args['document_link'] ) ) {
			if ( $args['document_link'] === 'true' ) {
				$args['document_link'] = true;
			}

			if ( $args['document_link'] === 'false' ) {
				$args['document_link'] = false;
			}
		}

		if ( isset( $args['folders'] ) ) {
			if ( $args['folders'] === 'true' ) {
				$args['folders'] = true;
			}

			if ( $args['folders'] === 'false' ) {
				$args['folders'] = false;
			}
		}

		if ( isset( $args['reset_button'] ) ) {
			if ( $args['reset_button'] === 'true' ) {
				$args['reset_button'] = true;
			}

			if ( $args['reset_button'] === 'false' ) {
				$args['reset_button'] = false;
			}
		}

		// link target
		if ( isset( $args['link_target'] ) ) {
			if ( $args['link_target'] === 'blank' ) {
				$args['link_target'] = true;
			}

			if ( $args['link_target'] === 'self' ) {
				$args['link_target'] = false;
			}
		}

		// link_style attribute option deprecation: file_type_icon --> icon
		if ( isset( $args['link_style'] ) && $args['link_style'] === 'file_type_icon' ) {
			$args['link_style'] = 'icon';
		}

		// alternative attributes
		if ( isset( $args['clickable_columns'] ) ) {
			$args['links'] = $args['clickable_columns'];
			unset( $args['clickable_columns'] );
		}

		if ( isset( $args['no_docs_message'] ) ) {
			$args['no_posts_message'] = $args['no_docs_message'];
			unset( $args['no_docs_message'] );
		}

		if ( isset( $args['no_docs_filtered_message'] ) ) {
			$args['no_posts_filtered_message'] = $args['no_docs_filtered_message'];
			unset( $args['no_docs_filtered_message'] );
		}

		if ( isset( $args['docs_per_page'] ) ) {
			$args['rows_per_page'] = $args['docs_per_page'];
			unset( $args['docs_per_page'] );
		}

		if ( isset( $args['doc_limit'] ) ) {
			$args['post_limit'] = $args['doc_limit'];
			unset( $args['doc_limit'] );
		}

		// handle shared attributes
		if ( isset( $args['layout'] ) && in_array( $args['layout'], [ 'table', 'grid' ], true ) ) {
			$args['layout'] = $args['layout'];
		} else {
			$args['layout'] = Table_Args::get_site_defaults()['layout'];
		}

		if ( isset( $args['content'] ) ) {
			if ( $args['layout'] === 'grid' ) {
				$args['grid_content'] = $args['content'];
				unset( $args['content'] );
			} elseif ( $args['layout'] === 'table' ) {
				$args['columns'] = $args['content'];
				unset( $args['content'] );
			}
		}

		if ( isset( $args['clickable_fields'] ) ) {
			if ( $args['layout'] === 'grid' ) {
				$args['grid_links'] = $args['clickable_fields'];
			} elseif ( $args['layout'] === 'table' ) {
				$args['links'] = $args['clickable_fields'];
			}
			unset( $args['clickable_fields'] );
		}

		if ( isset( $args['folder_status'] ) && 'open' !== $args['folder_status'] && 'closed' !== $args['folder_status'] ) {
			$args['folder_status_custom'] = $args['folder_status'];
			$args['folder_status']        = 'custom';
		}

		if ( isset( $args['link_destination'] ) ) {
			if ( $args['link_destination'] === 'direct' ) {
				$args['link_destination'] = 'download_file';
			}

			if ( in_array( $args['link_destination'], [ 'single', 'post' ], true ) ) {
				$args['link_destination'] = 'single_document';
			}
		}

		// hande pre 2.0.0 link styles
		if ( isset( $args['link_style'] ) && in_array( $args['link_style'], [ 'button_icon', 'button_icon_text', 'icon_only', 'icon', 'text' ], true ) ) {
			switch ( $args['link_style'] ) {
				case 'button_icon':
					$args['link_style'] = 'button';
					$args['link_icon']  = true;
					$args['link_text']  = '';
					break;
				case 'button_icon_text':
					$args['link_style'] = 'button';
					$args['link_icon']  = true;
					break;
				case 'icon_only':
					$args['link_style'] = 'link';
					$args['link_icon']  = true;
					$args['link_text']  = '';
					break;
				case 'icon':
					$args['link_style'] = 'file_icon';
					break;
				case 'text':
					$args['link_style'] = 'link';
					$args['link_icon']  = false;
					break;
				default:
					$args['link_style'] = 'button';
					break;
			}
		}

		// handle pre 2.0.0 preview styles
		if ( isset( $args['preview_style'] ) && in_array( $args['preview_style'], [ 'button_icon', 'button_icon_text', 'icon_only', 'text' ], true ) ) {
			switch ( $args['preview_style'] ) {
				case 'button_icon':
					$args['preview_style'] = 'button';
					$args['preview_icon']  = true;
					$args['preview_text']  = '';
					break;
				case 'button_icon_text':
					$args['preview_style'] = 'button';
					$args['preview_icon']  = true;
					break;
				case 'icon_only':
					$args['preview_style'] = 'link';
					$args['preview_icon']  = true;
					$args['preview_text']  = '';
					break;
				case 'text':
					$args['preview_style'] = 'link';
					$args['preview_icon']  = false;
					break;
				default:
					$args['preview_style'] = 'button';
					break;
			}
		}

		// handle pre 2.0.0 accessing_documents
		if ( isset( $args['accessing_documents'] ) && in_array( $args['accessing_documents'], [ 'checkbox', 'both' ], true ) ) {
			$args['multi_downloads'] = true;
		} elseif ( isset( $args['accessing_documents'] ) && ! in_array( $args['accessing_documents'], [ 'checkbox', 'both' ], true ) ) {
			$args['multi_downloads'] = false;
		}

		return $args;
	}

	/**
	 * Returns the hardcoded default arguments for DLP
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array_merge( Table_Args::get_table_defaults(), self::get_dlp_specific_default_args(), self::get_v2_default_args() );
	}

	/**
	 * Retrieves the default args specific to DLP (as opposed to the PTP defaults)
	 *
	 * @return string[]
	 */
	public static function get_dlp_specific_default_args() {
		$dlp_args = [
			'multi_download_button'  => 'above',
			'multi_download_text'    => __( 'Download Selected Documents', 'document-library-pro' ),
			'accessing_documents'    => 'link',
			'preview'                => false,
			'preview_style'          => 'button',
			'preview_text'           => '',
			'document_link'          => true,
			'link_style'             => 'button',
			'link_text'              => __( 'Download', 'document-library-pro' ),
			'link_destination'       => 'download_file',
			'link_target'            => false,
			'folders'                => false,
			'folders_order_by'       => 'name',
			'folder_status'          => 'closed',
			'folder_status_custom'   => '',
			'folder_icon_custom'     => false,
			'folder_icon_color'      => '#f6b900',
			'folder_icon_subcolor'   => '#333',
			'folder_icon_svg_closed' => '',
			'folder_icon_svg_open'   => '',
			'layout'                 => 'table',
			'grid_content'           => [
				'image'          => '1',
				'title'          => '1',
				'filename'       => '0',
				'file_type'      => '0',
				'file_size'      => '0',
				'doc_categories' => '0',
				'doc_author'     => '0',
				'download_count' => '0',
				'excerpt'        => '1',
				'custom_fields'  => '0',
				'link'           => '1',
			],
			'grid_columns'           => 'autosize',
			'doc_tag'                => '',
			'doc_category'           => '',
			'doc_author'             => '',
			'exclude_doc_category'   => '',
			'columns'                => 'title,excerpt,doc_categories,link',
			'links'                  => 'title,doc_categories,doc_tags,terms,doc_author',
			'version_control'        => false,
			'version_control_mode'   => 'keep',
			'cache_expiry'           => 6,
		];

		return $dlp_args;
	}

	/**
	 * Get the new args for the v2 changes
	 *
	 * @return array
	 */
	public static function get_v2_default_args() {
		return [
			'document_slug'             => 'document',
			'document_fields'           => [ 'editor', 'excerpt', 'thumbnail', 'author' ],
			'grid_links'                => 'image,doc_categories',
			'link_icon'                 => true,
			'preview_icon'              => true,
			'multi_downloads'           => false,
			'grid_document_title_link'  => 'single_document',
			'grid_filename_link'        => 'download_file',
			'table_document_title_link' => 'single_document',
			'table_filename_link'       => 'download_file',
			'search_on_click'           => true,
			'new_tab_links'             => false,
			'fronted_email_admin'       => false,
			'fronted_moderation'        => false,
			'cache'                     => true,
			'single_document_fields'    => self::get_single_document_display_defaults(),
			'accent_neutralise'         => false,
			'diacritics_sort'           => false,
			'columns_editor'            => [
				[
					'name'     => 'Title',
					'slug'     => 'title',
					'settings' =>
					[
						'input'              => '',
						'visibility'         => 'true',
						'column_type'        => 'text',
						'widths'             => '%',
						'priorities'         => '',
						'column_breakpoints' => 'default',
						'links'              => 'true',
						'link_destination'   => 'single_document',
					],
				],
				[
					'name'     => 'Excerpt',
					'slug'     => 'excerpt',
					'settings' =>
					[
						'input'              => '',
						'visibility'         => 'true',
						'column_type'        => 'text',
						'widths'             => '',
						'priorities'         => '',
						'column_breakpoints' => 'default',
					],
				],
				[
					'name'     => 'Categories',
					'slug'     => 'doc_categories',
					'settings' =>
					[
						'input'              => '',
						'visibility'         => 'true',
						'column_type'        => 'text',
						'widths'             => '',
						'priorities'         => '',
						'column_breakpoints' => 'default',
					],
				],
				[
					'name'     => 'Link',
					'slug'     => 'link',
					'settings' =>
					[
						'input'              => '',
						'visibility'         => 'true',
						'column_type'        => 'text',
						'widths'             => '%',
						'priorities'         => '',
						'column_breakpoints' => 'default',
					],
				],
			],
		];
	}

	/**
	 * Get the document display default structure.
	 *
	 * @return array
	 */
	public static function get_single_document_display_defaults() {
		$enabled_fields = [
			'thumbnail',
			'doc_categories',
			'doc_tags',
			'doc_author',
			'file_type',
			'file_size',
		];

		if ( self::uses_excerpt_option() ) {
			array_unshift( $enabled_fields, 'excerpt' );
		}

		return $enabled_fields;
	}

	/**
	 * Retrieve an option.
	 *
	 * @param string $option
	 * @param mixed $default
	 * @return mixed
	 */
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

	/**
	 * Return the version control mode (keep or delete) or false if version control is disabled
	 *
	 * @return bool|string Either 'keep', 'delete' or false
	 */
	public static function get_version_control_mode() {
		$settings = self::get_settings();

		$is_vc_active = $settings && isset( $settings['version_control'] ) ? (bool) $settings['version_control'] : false;

		if ( $is_vc_active ) {
			return $settings['version_control_mode'];
		}

		return false;
	}

	/**
	 * Determine whether the version control is enabled and the replacing file strategy is set to `keep`
	 *
	 * @return bool
	 */
	public static function is_version_history_active() {
		return 'keep' === self::get_version_control_mode();
	}

	/**
	 * Retrieve the search page option.
	 *
	 * @return int|false
	 */
	public static function get_search_page_option() {
		$search_page = (int) get_option( self::SEARCH_PAGE_OPTION_KEY, false ) ?? false;

		if ( $search_page && in_array( get_post_status( $search_page ), [ false, 'trash' ], true ) ) {
			$search_page = false;
		}

		return $search_page;
	}

	/**
	 * Determine if admin notifications for frontend submissions are active.
	 *
	 * @return boolean
	 */
	public static function is_submission_admin_email_active() {
		$options = self::get_option( self::DOCUMENT_FIELDS_OPTION_KEY, [] );
		return isset( $options['fronted_email_admin'] ) && $options['fronted_email_admin'] === '1';
	}

	/**
	 * Determine if frontend submissions are moderated.
	 *
	 * @return boolean
	 */
	public static function is_submission_moderated() {
		$options = self::get_option( self::DOCUMENT_FIELDS_OPTION_KEY, [] );
		return isset( $options['fronted_moderation'] ) && $options['fronted_moderation'] === '1';
	}

	/**
	 * Determine if the database has been successfully migrated to 2.0
	 *
	 * @return boolean
	 */
	public static function has_db_migrated_to_2_0() {
		return get_option( 'dlp_has_migrated_to_2_0', false );
	}

	/**
	 * Retrieve the supported columns.
	 *
	 * @return array
	 */
	public static function get_supported_columns() {
		$default_columns = [
			'id'             => __( 'ID', 'document-library-pro' ),
			'title'          => __( 'Title', 'document-library-pro' ),
			'content'        => __( 'Content', 'document-library-pro' ),
			'excerpt'        => __( 'Summary', 'document-library-pro' ),
			'image'          => __( 'Image', 'document-library-pro' ),
			'filename'       => __( 'Filename', 'document-library-pro' ),
			'file_size'      => __( 'File Size', 'document-library-pro' ),
			'file_type'      => __( 'File Type', 'document-library-pro' ),
			'doc_categories' => __( 'Categories', 'document-library-pro' ),
			'doc_tags'       => __( 'Tags', 'document-library-pro' ),
			'doc_author'     => __( 'Author', 'document-library-pro' ),
			'cf'             => __( 'Custom Fields', 'document-library-pro' ),
			'link'           => __( 'Link', 'document-library-pro' ),
			'download_count' => __( 'Downloads', 'document-library-pro' ),
			'status'         => __( 'Status', 'document-library-pro' ),
			'date'           => __( 'Date', 'document-library-pro' ),
			'date_modified'  => __( 'Last modified date', 'document-library-pro' ),
		];

		// retrieve all valid registered custom taxonomies for dlp_document post type
		$valid_custom_taxonomies = get_object_taxonomies( 'dlp_document', 'objects' );

		// remove our built-in taxonomies
		$valid_custom_taxonomies = array_filter(
			$valid_custom_taxonomies,
			function ( $taxonomy ) {
				// check slug and skip if built in or not public
				return ! in_array( $taxonomy->name, [ Taxonomies::CATEGORY_SLUG, Taxonomies::TAG_SLUG, Taxonomies::AUTHOR_SLUG, Taxonomies::FILE_TYPE_SLUG ], true ) || ! $taxonomy->public;
			}
		);

		foreach ( $valid_custom_taxonomies as $taxonomy ) {
			$default_columns[ sprintf( 'tax:%s', $taxonomy->name ) ] = $taxonomy->label;
		}

		return $default_columns;
	}

	/**
	 * Retrieve the supported taxonomies.
	 *
	 * @return array
	 */
	public static function get_supported_taxonomies() {
		$supported_taxonomies = [
			'file_type'      => __( 'File Type', 'document-library-pro' ),
			'doc_categories' => __( 'Categories', 'document-library-pro' ),
			'doc_tags'       => __( 'Tags', 'document-library-pro' ),
			'doc_author'     => __( 'Author', 'document-library-pro' ),
		];

		// retrieve all valid registered custom taxonomies for dlp_document post type
		$valid_custom_taxonomies = get_object_taxonomies( 'dlp_document', 'objects' );

		// remove our built-in taxonomies
		$valid_custom_taxonomies = array_filter(
			$valid_custom_taxonomies,
			function ( $taxonomy ) {
				// check slug and skip if built in or not public
				return ! in_array( $taxonomy->name, [ Taxonomies::CATEGORY_SLUG, Taxonomies::TAG_SLUG, Taxonomies::AUTHOR_SLUG, Taxonomies::FILE_TYPE_SLUG ], true ) || ! $taxonomy->public;
			}
		);

		foreach ( $valid_custom_taxonomies as $taxonomy ) {
			$supported_taxonomies[ sprintf( 'tax:%s', $taxonomy->name ) ] = $taxonomy->label;
		}

		return $supported_taxonomies;
	}

	/**
	 * Convert to checkbox value to boolean.
	 *
	 * @param string|bool $maybe_bool
	 * @return bool|string
	 */
	public static function maybe_parse_bool( $maybe_bool ) {
		if ( is_bool( $maybe_bool ) ) {
			return $maybe_bool;
		} elseif ( 'true' === $maybe_bool || '1' === $maybe_bool ) {
			return true;
		} elseif ( 'false' === $maybe_bool || '' === $maybe_bool || '0' === $maybe_bool ) {
			return false;
		} else {
			return $maybe_bool;
		}
	}

	/**
	 * Convert to checkbox value to boolean.
	 *
	 * @param string|bool $value
	 * @return string
	 */
	public static function convert_checkbox_value_to_boolean( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_string( $value ) ) {
			return $value === 'true' || $value === '1' ? true : false;
		}

		return false;
	}

	/**
	 * Convert to checkbox value
	 *
	 * Convert true / false, 'true' / 'false', 1 / 0 to '1' / '0'.
	 *
	 * @param string|bool $value
	 * @return string
	 */
	public static function convert_to_checkbox_value( $value ) {
		if ( is_bool( $value ) ) {
			return $value ? '1' : '0';
		}

		if ( is_string( $value ) ) {
			return $value === 'true' || $value === '1' ? '1' : '0';
		}

		return '0';
	}

	/**
	 * Migrate columns and links to the new structure.
	 *
	 * @param string $columns Colunms from the v1 settings
	 * @param string $links Clickable fields from the v1 settings
	 * @param string $link_destination Link destination from the v1 settings
	 * @return array
	 */
	public static function migrate_columns( $columns, $links, $link_destination = 'single_document' ) {
		$columns           = array_map( 'trim', explode( ',', $columns ) );
		$links             = array_map( 'trim', explode( ',', $links ) );
		$supported_columns = self::get_supported_columns();
		$new_columns       = [];

		if ( empty( $columns ) ) {
			return [];
		}

		$i = 0;
		foreach ( $columns as $index => $column ) {
			if ( ! isset( $supported_columns[ $column ] ) && ! PTP_Columns_Util::is_custom_field( $column ) ) {
				++$i;
				continue;
			}

			$name = PTP_Columns_Util::is_custom_field( $column ) ? str_ireplace( 'cf:', '', $column ) : $supported_columns[ $column ];
			$slug = PTP_Columns_Util::is_custom_field( $column ) ? 'cf' : $column;

			$new_columns[] = [
				'name'     => PTP_Columns_Util::is_custom_field( $column ) ? ucwords( str_ireplace( '_', ' ', str_ireplace( 'cf:', '', $column ) ) ) : $name,
				'slug'     => $slug,
				'settings' => [
					'input'      => PTP_Columns_Util::is_custom_field( $column ) ? $name : $supported_columns[ $column ],
					'visibility' => 'true',
				],
			];

			if ( in_array( $column, [ 'id', 'image', 'title', 'author', 'doc_categories', 'doc_tags', 'doc_author', 'file_type' ], true ) || PTP_Columns_Util::is_custom_taxonomy( $column ) ) {
				if ( in_array( $column, $links, true ) || in_array( 'all', $links, true ) || ( PTP_Columns_Util::is_custom_taxonomy( $column ) && in_array( 'terms', $links, true ) ) ) {
					$new_columns[ $index - $i ]['settings']['links'] = 'true';
				} else {
					$new_columns[ $index - $i ]['settings']['links'] = 'false';
				}
			}

			if ( in_array( $column, [ 'categories', 'tags' ], true ) || PTP_Columns_Util::is_custom_taxonomy( $column ) ) {
				$new_columns[ $index - $i ]['settings']['search_on_click'] = 'true';
			}

			if ( $column === 'title' ) {
				$new_columns[ $index - $i ]['settings']['link_destination'] = $link_destination === 'direct' ? 'download_file' : 'single_document';
			}
		}

		return $new_columns;
	}

	/**
	 * Generate the link style settings.
	 *
	 * @param string $link_style
	 * @return array
	 */
	public static function migrate_link_style_settings( $link_style ) {
		switch ( $link_style ) {
			case 'button':
				$settings = [
					'link_style' => 'button',
					'link_icon'  => false,
				];
				break;
			case 'button_icon_text':
				$settings = [
					'link_style' => 'button',
					'link_icon'  => true,
				];
				break;
			case 'button_icon':
			case 'icon_only':
				$settings = [
					'link_style' => 'button',
					'link_icon'  => true,
					'link_text'  => '',
				];
				break;
			case 'icon':
				$settings = [
					'link_style' => 'file_icon',
					'link_icon'  => false,
				];
				break;
			case 'text':
				$settings = [
					'link_style' => 'link',
					'link_icon'  => false,
				];
				break;
			default:
				$settings = [
					'link_style'  => 'button',
					'button_icon' => true,
				];
				break;
		}

		return $settings;
	}

	/**
	 * Generate the preview link style settings.
	 *
	 * @param string $link_style
	 * @return array
	 */
	public static function migrate_preview_style_settings( $link_style ) {
		switch ( $link_style ) {
			case 'button':
				$settings = [
					'preview_style' => 'button',
					'preview_icon'  => false,
				];
				break;
			case 'button_icon_text':
				$settings = [
					'preview_style' => 'button',
					'preview_icon'  => true,
				];
				break;
			case 'button_icon':
			case 'icon_only':
				$settings = [
					'preview_style' => 'button',
					'preview_icon'  => true,
					'preview_text'  => '',
				];
				break;
			case 'text':
				$settings = [
					'preview_style' => 'link',
					'preview_icon'  => false,
				];
				break;
			default:
				$settings = [
					'preview_style' => 'button',
					'preview_icon'  => true,
				];
				break;
		}

		return $settings;
	}

	/**
	 * Generate the accessing document settings.
	 *
	 * @param string $accessing_document
	 * @return bool
	 */
	public static function migrate_accessing_document_settings( $accessing_document ) {
		if ( in_array( $accessing_document, [ 'checkbox', 'both' ], true ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Generate the multicheckbox settings.
	 *
	 * @param array $setting
	 * @return array
	 */
	public static function migrate_multicheckbox_settings( $setting ) {
		$new_setting = [];

		if ( empty( $setting ) || ! is_array( $setting ) ) {
			return $new_setting;
		}

		// convert to an array of the true-y values
		foreach ( $setting as $key => $value ) {
			if ( in_array( $value, [ true, 'true', '1' ], true ) ) {
				$new_setting[] = $key;
			}
		}

		return $new_setting;
	}

	/**
	 * Get the list of columns.
	 *
	 * @param array $selected_columns
	 * @return string
	 */
	public static function parse_columns_from_v2_columns( array $selected_columns = [] ) {
		$columns = [];

		foreach ( $selected_columns as $column ) {
			$is_custom_field = isset( $column['slug'] ) && $column['slug'] === 'cf';
			$input           = isset( $column['settings']['input'] ) && $column['settings']['input'] !== '' ? $column['settings']['input'] : __( 'Custom field', 'document-library-pro' );
			$name            = isset( $column['settings']['visibility'] ) && $column['settings']['visibility'] === 'false' ? 'blank' : $column['name'];

			if ( $is_custom_field ) {
				$columns[] = "{$column['slug']}:{$input}:{$name}";
			} else {
				$columns[] = "{$column['slug']}:{$name}";
			}
		}

		return implode( ',', $columns );
	}

	/**
	 * Get the list of columns that have links.
	 *
	 * @param array $columns
	 * @return string
	 */
	public static function parse_links_from_v2_columns( array $columns = [] ) {
		foreach ( $columns as $column ) {
			if ( isset( $column['settings']['links'] ) && in_array( $column['settings']['links'], [ 'true', '1' ], true ) ) {
				$links[] = $column['slug'];
			}
		}

		return empty( $links ) ? 'none' : implode( ',', $links );
	}

	/**
	 * Get the list of columns that have search on click.
	 *
	 * @param array $columns
	 * @return string
	 */
	public static function parse_search_on_click_from_v2_columns( array $columns = [] ) {
		$search_on_click = [];

		foreach ( $columns as $column ) {
			if ( isset( $column['settings']['search_on_click'] ) && $column['settings']['search_on_click'] === 'true' ) {
				$search_on_click[] = $column['slug'];
			}
		}

		if ( empty( $search_on_click ) ) {
			return 'false';
		}

		return implode( ',', $search_on_click );
	}

	/**
	 * Get the list of columns width.
	 *
	 * @param array $columns
	 * @return string
	 */
	public static function parse_widths_from_v2_columns( array $columns = [] ) {
		$widths = [];

		foreach ( $columns as $column ) {
			if ( isset( $column['settings']['widths'] ) && $column['settings']['widths'] > 0 ) {
				$widths[] = $column['settings']['widths'];
			} else {
				$widths[] = 'auto';
			}
		}

		if ( empty( $widths ) ) {
			return '';
		}

		return implode( ',', $widths );
	}

	/**
	 * Get the list of column priorities.
	 *
	 * @param array $columns
	 * @return string
	 */
	public static function parse_priorities_from_v2_columns( array $columns = [] ) {
		$priorities = [];

		foreach ( $columns as $column ) {
			if ( isset( $column['settings']['priorities'] ) && $column['settings']['priorities'] > 0 ) {
				$priorities[] = $column['settings']['priorities'];
			} else {
				$priorities[] = '';
			}
		}

		if ( empty( $priorities ) ) {
			return '';
		}

		return implode( ',', $priorities );
	}

	/**
	 * Get the list of column breakpoints.
	 *
	 * @param array $columns
	 * @return string
	 */
	public static function parse_column_breakpoints_from_v2_columns( array $columns = [] ) {
		$column_breakpoints = [];

		foreach ( $columns as $column ) {
			if ( isset( $column['settings']['column_breakpoints'] ) && $column['settings']['column_breakpoints'] !== '' ) {
				$column_breakpoints[] = $column['settings']['column_breakpoints'];
			} else {
				$column_breakpoints[] = 'default';
			}
		}

		if ( empty( $column_breakpoints ) ) {
			return '';
		}

		return implode( ',', $column_breakpoints );
	}

	/**
	 * Get the list of column breakpoints.
	 *
	 * @param string $slug
	 * @param array $columns
	 * @return string
	 */
	public static function parse_column_link_destination_from_v2_columns( string $slug, array $columns = [] ) {
		// Find the column with specified slug.
		$target_column = array_filter(
			$columns,
			function ( $column ) use ( $slug ) {
				return $slug === $column['slug'];
			}
		);

		// If no matching column found, return 'none'.
		if ( empty( $target_column ) ) {
			return 'none';
		}

		// Get the first (and should be only) matching column.
		$target_column = reset( $target_column );

		// Check if links setting exists and is true.
		if ( isset( $target_column['settings']['links'] ) && 'true' === $target_column['settings']['links'] ) {
			// Return link_destination if it exists, otherwise 'none'.
			return isset( $target_column['settings']['link_destination'] )
				? $target_column['settings']['link_destination']
				: 'none';
		}

		return 'none';
	}
}
