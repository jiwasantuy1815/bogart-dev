<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Util\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer to translate the v2 settings into the v1 option calls.
 *
 * We do this because the PTP core internally use the individual settings keys across the codebase.
 * The new settings-api use a single option.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Settings_Compatibility implements Registerable, Standard_Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		// check db is on v2 or higher
		$db_version = get_option( 'dlp_db_version' );
		if ( version_compare( $db_version, '2.0.0', '<' ) || get_option( 'dlp_should_migrate_settings_2_0' ) ) {
			return;
		}

		add_filter( 'option_' . Options::SHORTCODE_OPTION_KEY, [ $this, 'handle_shortcode_option' ] );
		add_filter( 'option_' . Options::MISC_OPTION_KEY, [ $this, 'handle_misc_options' ] );
		add_filter( 'option_' . Options::DOCUMENT_FIELDS_OPTION_KEY, [ $this, 'handle_document_fields' ] );
		add_filter( 'option_' . Options::DOCUMENT_SLUG_OPTION_KEY, [ $this, 'handle_document_slug' ] );
		add_filter( 'option_' . Options::DOCUMENT_PAGE_OPTION_KEY, [ $this, 'handle_document_page' ] );
		add_filter( 'option_' . Options::SEARCH_PAGE_OPTION_KEY, [ $this, 'handle_search_page' ] );
		add_filter( 'option_' . Options::SINGLE_DOCUMENT_DISPLAY_OPTION_KEY, [ $this, 'handle_single_document_display' ] );
		add_filter( 'option_' . Options::FOLDER_CLOSE_SVG_OPTION_KEY, [ $this, 'handle_folder_close_svg' ] );
		add_filter( 'option_' . Options::FOLDER_OPEN_SVG_OPTION_KEY, [ $this, 'handle_folder_open_svg' ] );
	}

	/**
	 * Handle the shortcodes option.
	 *
	 * @param mixed $option
	 * @return array
	 */
	public function handle_shortcode_option( $option ) {
		$shortcode_option_keys = [
			// general
			'layout',
			'document_link',
			'link_style',
			'link_text',
			'link_destination',
			'link_target',
			'links',
			'preview',
			'preview_style',
			'preview_text',
			'folders',
			'folders_order_by',
			'folders_order',
			'folder_status',
			'folder_status_custom',
			'folder_icon_custom',
			'folder_icon_color',
			'default_table_template',
			'folder_icon_subcolor',
			'lightbox',
			'shortcodes',
			'excerpt_length',
			'content_length',
			'rows_per_page',
			'paging_type',
			'pagination',
			'totals',
			'sort_by',
			'sort_by_custom',
			'sort_order',
			'version_control',
			'version_control_mode',
			// document tables
			'columns',
			'image_size',
			'accessing_documents',
			'multi_downloads',
			'multi_download_button',
			'multi_download_text',
			'lazy_load',
			'post_limit',
			'cache',
			'cache_expiry',
			'filters',
			'filters_custom', // Saved to 'filters'
			'page_length',
			'search_box',
			'reset_button',
			'responsive_display',
			// grid
			'grid_content',
			'grid_columns',
			// new settings
			'link_icon',
			'preview_icon',
			'priorities',
			'widths',
			'column_breakpoints',
			'grid_links',
			'grid_document_title_link',
			'grid_filename_link',
			'table_document_title_link',
			'table_filename_link',
			'search_on_click',
			'new_tab_links',
		];

		$settings = get_option( Options::SETTINGS_KEY, [] );

		// grid_content
		if ( isset( $settings['grid_content'] ) && is_array( $settings['grid_content'] ) ) {
			$settings['grid_content'] = Options::sanitize_grid_content( implode( ',', $settings['grid_content'] ) );
		}

		// parse the new columns editor field
		if ( isset( $settings['columns'] ) && is_array( $settings['columns'] ) ) {
			$settings['links']                     = Options::parse_links_from_v2_columns( $settings['columns'] );
			$settings['widths']                    = Options::parse_widths_from_v2_columns( $settings['columns'] );
			$settings['priorities']                = Options::parse_priorities_from_v2_columns( $settings['columns'] );
			$settings['column_breakpoints']        = Options::parse_column_breakpoints_from_v2_columns( $settings['columns'] );
			$settings['table_filename_link']       = Options::parse_column_link_destination_from_v2_columns( 'filename', $settings['columns'] );
			$settings['table_document_title_link'] = Options::parse_column_link_destination_from_v2_columns( 'title', $settings['columns'] );
			$settings['search_on_click']           = Options::parse_search_on_click_from_v2_columns( $settings['columns'] );
			// make sure this is last!
			$settings['columns'] = Options::parse_columns_from_v2_columns( $settings['columns'] );
		}

		// accessing documents
		$settings = array_intersect_key( $settings, array_flip( $shortcode_option_keys ) );

		return $settings;
	}

	/**
	 * Handle the misc options.
	 *
	 * @param array $option
	 */
	public function handle_misc_options( $option ) {
		$misc_option_keys = [
			// table
			'table_design',
			'external_border',
			'header_border',
			'border_horizontal_cell',
			'border_vertical_cell',
			'border_bottom',
			'header_text',
			'body_text',
			'hyperlink_font',
			'button_font',
			'disabled_button_font',
			'quantity_font',
			'dropdown_border',
			'text_border',
			'header_bg',
			'body_bg',
			'button_bg',
			'body_bg_alt',
			'button_bg_hover',
			'button_disabled_bg',
			'button_quantity_bg',
			'dropdown_background',
			'dropdown_font',
			'text_background',
			'text_font',
			'table_corner_style',
			'table_spacing',
			'cell_backgrounds',
			// grid
			'default_grid_template',
			'grid_design',
			'grid_image_bg',
			'grid_category_bg',
			'grid_body_text',
			'grid_card_border',
			'grid_dropdown_border',
			'grid_hyperlink_font',
			'grid_button_font',
			'grid_button_border',
			'grid_corner_style',
			'grid_button_background',
			'grid_button_background_hover',
			'grid_card_background',

			'folder_design',
			'folder_icon_color',
			'folder_icon_subcolor',
		];

		$settings = get_option( Options::SETTINGS_KEY, [] );

		return array_intersect_key( $settings, array_flip( $misc_option_keys ) );
	}

	/**
	 * Handle the document slug option.
	 *
	 * @param array $option
	 */
	public function handle_document_slug( $option ) {
		$settings = get_option( Options::SETTINGS_KEY, [] );

		return $settings['document_slug'];
	}

	/**
	 * Handle the document page option.
	 *
	 * @param array $option
	 */
	public function handle_document_page( $option ) {
		// retrieve the document page option from our new option
		$settings = get_option( Options::SETTINGS_KEY, [] );

		return isset( $settings['document_page'] ) ? $settings['document_page'] : '';
	}

	/**
	 * Handle the search page option.
	 *
	 * @param array $option
	 */
	public function handle_search_page( $option ) {
		// retrieve the search page option from our new option
		$settings = get_option( Options::SETTINGS_KEY, [] );

		return isset( $settings['search_page'] ) ? $settings['search_page'] : '';
	}

	/**
	 * Handle the single document display option.
	 *
	 * @param array $option
	 */
	public function handle_single_document_display( $option ) {
		$settings         = get_option( Options::SETTINGS_KEY, [] );
		$document_display = isset( $settings['single_document_fields'] ) ? $settings['single_document_fields'] : [];

		$structure = [
			'thumbnail'      => '0',
			'comments'       => '0',
			'doc_categories' => '0',
			'doc_tags'       => '0',
			'doc_author'     => '0',
			'file_type'      => '0',
			'file_size'      => '0',
			'filename'       => '0',
			'custom-fields'  => '0',
			'download_count' => '0',
			'excerpt'        => '0',
		];

		foreach ( $document_display as $key ) {
			$structure[ $key ] = '1';
		}

		return $structure;
	}

	/**
	 * Handle the document fields display option.
	 */
	public function handle_document_fields() {
		// retrieve the document fields display option from our new option
		$settings        = get_option( Options::SETTINGS_KEY, [] );
		$document_fields = isset( $settings['document_fields'] ) ? $settings['document_fields'] : [];

		$structure = [
			'editor'        => '0',
			'excerpt'       => '0',
			'thumbnail'     => '0',
			'comments'      => '0',
			'author'        => '0',
			'custom-fields' => '0',
		];

		foreach ( $document_fields as $key ) {
			$structure[ $key ] = '1';
		}

		// add back version control
		if ( isset( $settings['version_control'] ) ) {
			$structure['version_control'] = $settings['version_control'];
		}

		if ( isset( $settings['version_control_mode'] ) ) {
			$structure['version_control_mode'] = $settings['version_control_mode'];
		}

		// add back front end submission settings
		if ( isset( $settings['fronted_email_admin'] ) ) {
			$structure['fronted_email_admin'] = $settings['fronted_email_admin'];
		}

		if ( isset( $settings['fronted_moderation'] ) ) {
			$structure['fronted_moderation'] = $settings['fronted_moderation'];
		}

		return $structure;
	}

	/**
	 * Handle the folder open SVG option.
	 *
	 * @param array $option
	 */
	public function handle_folder_open_svg( $option ) {
		// retrieve the folder open SVG option from our new option
		$settings = get_option( Options::SETTINGS_KEY, [] );

		return isset( $settings['folder_icon_svg_open'] ) && $settings['folder_design'] !== 'default' ? $settings['folder_icon_svg_open'] : '';
	}

	/**
	 * Handle the folder close SVG option.
	 *
	 * @param array $option
	 */
	public function handle_folder_close_svg( $option ) {
		// retrieve the folder close SVG option from our new option
		$settings = get_option( Options::SETTINGS_KEY, [] );

		return isset( $settings['folder_icon_svg_closed'] ) && $settings['folder_design'] !== 'default' ? $settings['folder_icon_svg_closed'] : '';
	}
}
