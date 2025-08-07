<?php
namespace Barn2\Plugin\Document_Library_Pro;

defined( 'ABSPATH' ) || exit;

use Barn2\Plugin\Document_Library_Pro\Util\Options;

/**
 * Settings migration functions.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Settings_Migration {

	/**
	 * Migrate settings from v1 to v2
	 */
	public static function update_2_0_0_migrate_settings() {
		$new_settings = get_option( Options::SETTINGS_KEY, [] );

		// handle a fresh v2 install
		self::handle_fresh_v2_install();

		// SHORTCODE DEFAULTS
		$old_shortcode_option = get_option( Options::SHORTCODE_OPTION_KEY, [] );

		// link_style structure change
		if ( isset( $old_shortcode_option['link_style'] ) ) {
			$new_link_style_settings = Options::migrate_link_style_settings( $old_shortcode_option['link_style'] );
			$old_shortcode_option    = array_merge( $old_shortcode_option, $new_link_style_settings );
		}

		// preview_style structure change
		if ( isset( $old_shortcode_option['preview_style'] ) ) {
			$new_preview_style_settings = Options::migrate_preview_style_settings( $old_shortcode_option['preview_style'] );
			$old_shortcode_option       = array_merge( $old_shortcode_option, $new_preview_style_settings );
		}

		// accessing_documents structure change
		if ( isset( $old_shortcode_option['accessing_documents'] ) ) {
			$old_shortcode_option['multi_downloads'] = Options::migrate_accessing_document_settings( $old_shortcode_option['accessing_documents'] );
			unset( $old_shortcode_option['accessing_documents'] );
		}

		// remove category id folders_orderby, default to name
		if ( isset( $old_shortcode_option['folders_orderby'] ) && $old_shortcode_option['folders_orderby'] === 'id' ) {
			$old_shortcode_option['folders_orderby'] === 'name';
		}

		// migrate columns
		if ( isset( $old_shortcode_option['columns'] ) ) {
			$old_shortcode_option['columns'] = Options::migrate_columns( $old_shortcode_option['columns'], $old_shortcode_option['links'], $old_shortcode_option['link_destination'] );
		}

		// migrate links to new grid_links
		if ( isset( $old_shortcode_option['links'] ) ) {
			$links_array = array_map( 'trim', explode( ',', $old_shortcode_option['links'] ) );

			// handle title
			if ( in_array( 'title', $links_array, true ) ) {
				$old_shortcode_option['grid_document_title_link'] = isset( $old_shortcode_option['link_destination'] ) && $old_shortcode_option['link_destination'] === 'direct' ? 'download_file' : 'single_document';
			}

			// filename
			if ( in_array( 'filename', $links_array, true ) ) {
				$old_shortcode_option['grid_filename_link'] = 'download_file';
			}

			// remove unsupported links
			$links_array = array_filter( $links_array, function( $link ) {
				return ! in_array( $link, [ 'title', 'filename', 'terms', 'doc_tags' ], true );
			} );

			$old_shortcode_option['grid_links'] = implode( ',', $links_array );
			unset( $old_shortcode_option['links'] );
		}

		// migrate link_destination
		if ( isset( $old_shortcode_option['link_destination'] ) ) {
			$old_shortcode_option['link_destination'] = $old_shortcode_option['link_destination'] === 'direct' ? 'download_file' : 'single_document';
		}

		// Remove unused options
		$removed_options = [
			'document_link',
			'folders_order',
			'accessing_documents',
			'reset_button',
			'links',
		];

		foreach ( $removed_options as $option ) {
			if ( isset( $old_shortcode_option[ $option ] ) ) {
				unset( $old_shortcode_option[ $option ] );
			}
		}

		if ( isset( $old_shortcode_option['grid_content'] ) && is_array( $old_shortcode_option['grid_content'] ) ) {
			$old_shortcode_option['grid_content'] = Options::migrate_multicheckbox_settings( $old_shortcode_option['grid_content'] );
		}

		if ( ! empty( $old_shortcode_option ) && is_array( $old_shortcode_option ) ) {
			$new_settings = array_merge( $new_settings, $old_shortcode_option );
		}

		// MISC
		$old_misc_option = get_option( Options::MISC_OPTION_KEY, [] );

		if ( ! empty( $old_misc_option ) && is_array( $old_misc_option ) ) {
			$new_settings = array_merge( $new_settings, $old_misc_option );
		}

		// DOCUMENT FIELDS
		$old_document_fields_option = get_option( Options::DOCUMENT_FIELDS_OPTION_KEY, [] );

		// we want to seperate these keys and the rest will go in the 'document_fields' key
		$seperate_df_keys = [
			'fronted_email_admin',
			'fronted_moderation',
			'version_control',
			'version_control_mode',
		];

		if ( ! empty( $old_document_fields_option ) && is_array( $old_document_fields_option ) ) {
			foreach ( $old_document_fields_option as $key => $value ) {
				if ( in_array( $key, $seperate_df_keys, true ) ) {
					$new_settings[ $key ] = $value;
					unset( $old_document_fields_option[ $key ] );
				}
			}

			$new_settings['document_fields'] = Options::migrate_multicheckbox_settings( $old_document_fields_option );
		}

		// DOCUMENT FIELDS DISPLAY
		$old_document_fields_display_option = get_option( Options::SINGLE_DOCUMENT_DISPLAY_OPTION_KEY, [] );

		if ( ! empty( $old_document_fields_display_option ) && is_array( $old_document_fields_display_option ) ) {
			$new_settings['single_document_fields'] = Options::migrate_multicheckbox_settings( $old_document_fields_display_option );
		}

		// DOCUMENT SLUG
		$document_slug = get_option( Options::DOCUMENT_SLUG_OPTION_KEY, '' );

		if ( ! empty( $document_slug ) ) {
			$new_settings['document_slug'] = $document_slug;
		}

		// DOCUMENT PAGE
		$document_page = get_option( Options::DOCUMENT_PAGE_OPTION_KEY, '' );

		if ( ! empty( $document_page ) ) {
			$new_settings['document_page'] = $document_page;
		}

		// SEARCH PAGE
		$search_page = get_option( Options::SEARCH_PAGE_OPTION_KEY, '' );

		if ( ! empty( $search_page ) ) {
			$new_settings['search_page'] = $search_page;
		}

		// FOLDER ICONS SVG
		$folder_icon_svg_closed = get_option( Options::FOLDER_CLOSE_SVG_OPTION_KEY, '' );

		if ( ! empty( $folder_icon_svg_closed ) ) {
			$new_settings['folder_icon_svg_closed'] = $folder_icon_svg_closed;
		}

		$folder_icon_svg_open = get_option( Options::FOLDER_OPEN_SVG_OPTION_KEY, '' );

		if ( ! empty( $folder_icon_svg_open ) ) {
			$new_settings['folder_icon_svg_open'] = $folder_icon_svg_open;
		}

		// Convert all true values to '1' and false to '0' for checkboxes
		foreach ( $new_settings as $key => $value ) {
			if ( true === $value ) {
				$new_settings[ $key ] = '1';
			} elseif ( false === $value ) {
				$new_settings[ $key ] = '0';
			}
		}

		update_option( Options::SETTINGS_KEY, $new_settings );
	}

	/**
	 * Handle a fresh v2 install.
	 *
	 * This creates the legacy options keys for PTP core compatibility.
	 *
	 * @return bool
	 */
	private static function handle_fresh_v2_install() {
		// we need to recreate these for the PTP core compatibility layer
		$legacy_keys = [
			Options::DOCUMENT_FIELDS_OPTION_KEY => false,
			Options::DOCUMENT_PAGE_OPTION_KEY => false,
			Options::SEARCH_PAGE_OPTION_KEY => false,
			Options::SHORTCODE_OPTION_KEY => false,
			Options::MISC_OPTION_KEY => false,
			Options::SINGLE_DOCUMENT_DISPLAY_OPTION_KEY => false,
			Options::FOLDER_CLOSE_SVG_OPTION_KEY => false,
			Options::FOLDER_OPEN_SVG_OPTION_KEY => false,
		];

		// check if legacy option exists
		foreach ( $legacy_keys as $key => $status ) {
			if ( get_option( $key ) ) {
				$legacy_keys[ $key ] = true;
			}
		}

		// if new options does not exist, then create any legacy options that don't exist
		foreach ( $legacy_keys as $key => $status ) {
			if ( ! $status ) {
				update_option( $key, [] );
			}
		}

		return true;
	}
}
