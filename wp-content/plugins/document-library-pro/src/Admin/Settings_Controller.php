<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin;

use Barn2\Plugin\Document_Library_Pro\Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Settings_Manager;
use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Util\SVG_Icon;
use WP_Error;

/**
 * Handles registration of the settings manager
 * that powers the settings page.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Settings_Controller implements Standard_Service {

	/**
	 * The settings manager.
	 *
	 * @var Settings_Manager
	 */
	protected $manager;

	/**
	 * The plugin.
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * The default options.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * The settings tabs.
	 *
	 * @var array
	 */
	protected $settings_tabs;

	/**
	 * Constructor
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin        = $plugin;
		$this->settings_tabs = $this->get_settings_tabs();
		$this->register();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register(): void {
		add_action( 'init', [ $this, 'configure_manager' ] );
		add_filter( 'barn2_settings_api_document-library-pro_save_settings', [ $this, 'sanitize_settings' ], 10, 4 );
	}

	/**
	 * Retrieves the settings tab classes.
	 *
	 * @return array
	 */
	private function get_settings_tabs() {
		$settings_tabs = [
			Settings_Tab\General::TAB_ID         => new Settings_Tab\General(),
			Settings_Tab\Display::TAB_ID         => new Settings_Tab\Display(),
			Settings_Tab\Search::TAB_ID          => new Settings_Tab\Search(),
			Settings_Tab\Design::TAB_ID          => new Settings_Tab\Design(),
			Settings_Tab\Single_Document::TAB_ID => new Settings_Tab\Single_Document(),
			Settings_Tab\Advanced::TAB_ID        => new Settings_Tab\Advanced(),
		];

		return $settings_tabs;
	}

	/**
	 * Get the settings manager.
	 *
	 * @return Settings_Manager
	 */
	public function get_manager(): Settings_Manager {
		return $this->manager;
	}

	/**
	 * Configure the settings manager.
	 */
	public function configure_manager(): void {
		$this->defaults = Options::get_defaults();

		$settings_manager = new Settings_Manager( $this->plugin );
		$settings_manager->set_library_path( $this->plugin->get_dir_path( 'dependencies/barn2/settings-api' ) );
		$settings_manager->set_library_url( $this->plugin->get_dir_url( 'dependencies/barn2/settings-api' ) );
		$settings_manager->add_tabs( $this->get_settings_config() );

		$this->manager = $settings_manager;

		$this->manager->boot();

		$this->manager->set_validation( function ( $values ) {
			$errors = new WP_Error();

			if ( empty( $values['grid_content'] ) ) {
				$errors->add( 'grid_content', __( 'You need to select at least one option for grid content.', 'document-library-pro' ) );

				return $errors;
			}

			return $values;
		} );
	}

	/**
	 * Get the settings config.
	 *
	 * @return array
	 */
	private function get_settings_config(): array {
		return [
			$this->settings_tabs[ Settings_Tab\General::TAB_ID ]->get_tab(),
			$this->settings_tabs[ Settings_Tab\Display::TAB_ID ]->get_tab(),
			$this->settings_tabs[ Settings_Tab\Search::TAB_ID ]->get_tab(),
			$this->settings_tabs[ Settings_Tab\Design::TAB_ID ]->get_tab(),
			$this->settings_tabs[ Settings_Tab\Single_Document::TAB_ID ]->get_tab(),
			$this->settings_tabs[ Settings_Tab\Advanced::TAB_ID ]->get_tab(),
		];
	}

	/**
	 * Sanitize the settings.
	 *
	 * @param array $settings
	 * @param \WP_REST_Request $request
	 * @param Settings_Manager $settings_manager
	 * @param string $option_name
	 * @return array
	 */
	public function sanitize_settings( $settings, $request, $settings_manager, $option_name ) {
		$previous_settings = get_option( $option_name );

		// check if document page has changed
		if ( isset( $previous_settings['document_page'] ) && $previous_settings['document_page'] !== $settings['document_page'] ) {
			$settings['document_page'] = $this->sanitize_document_page( $settings['document_page'] );
		}

		// check if search page has changed
		if ( isset( $previous_settings['search_page'] ) && $previous_settings['search_page'] !== $settings['search_page'] ) {
			$settings['search_page'] = $this->sanitize_search_page( $settings['search_page'] );
		}

		// check if docuument slug has changed
		if ( isset( $previous_settings['document_slug'] ) && $previous_settings['document_slug'] !== $settings['document_slug'] ) {
			$settings['document_slug'] = $this->sanitize_document_slug( $settings['document_slug'] );
		}

		// SVG sanitization
		$pre_sanitized_settings = $request->get_param('settings'); // we need to check for folder svg pre-sanitized since it's excluded by sanitize_text_field

		if ( isset( $pre_sanitized_settings['folder_icon_svg_open'] ) && ! empty( $pre_sanitized_settings['folder_icon_svg_open'] ) && $previous_settings['folder_icon_svg_open'] !== $pre_sanitized_settings['folder_icon_svg_open'] ) {
			$settings['folder_icon_svg_open'] = $this->sanitize_svg( $pre_sanitized_settings['folder_icon_svg_open'] );
		}

		if ( isset( $pre_sanitized_settings['folder_icon_svg_closed'] ) && ! empty( $pre_sanitized_settings['folder_icon_svg_closed'] ) && $previous_settings['folder_icon_svg_closed'] !== $pre_sanitized_settings['folder_icon_svg_closed'] ) {
			$settings['folder_icon_svg_closed'] = $this->sanitize_svg( $pre_sanitized_settings['folder_icon_svg_closed'] );
		}

		return $settings;
	}

	/**
	 * Sanitize the Document Page setting.
	 *
	 * @param string $page_setting
	 * @return string
	 */
	public function sanitize_document_page( $page_setting ) {
		if ( ! is_numeric( $page_setting ) ) {
			return;
		}

		$page = get_post( absint( $page_setting ) );

		$update_page = [ 'ID' => $page->ID ];

		// Add the doc library shortcode if we don't have it
		if ( $page && 'publish' === $page->post_status && ! stripos( $page->post_content, '[doc_library' ) ) {
			$update_page['post_content'] = $page->post_content . '<!-- wp:shortcode -->[doc_library]<!-- /wp:shortcode -->';
		}

		// We always update post when changing pages to clear any cache
		wp_update_post( $update_page );

		return $page_setting;
	}

	/**
	 * Sanitize the Search Page setting.
	 *
	 * @param string $page_setting
	 * @return string
	 */
	public function sanitize_search_page( $page_setting ) {
		if ( ! is_numeric( $page_setting ) ) {
			return '';
		}

		$page = get_post( absint( $page_setting ) );

		$update_page = [ 'ID' => $page->ID ];

		// Update post when changing pages to clear any cache
		wp_update_post( $update_page );

		return $page_setting;
	}

	/**
	 * Sanitize the Document Slug setting.
	 *
	 * @param string $slug_setting
	 * @return string
	 */
	public function sanitize_document_slug( $slug_setting ) {
		if ( ! is_string( $slug_setting ) ) {
			return 'document';
		}

		$slug_setting = sanitize_key( $slug_setting );

		update_option( 'dlp_should_flush_rewrite_rules', true );

		return $slug_setting;
	}

	/**
	 * Sanitize the SVG setting.
	 *
	 * @param string $svg_setting
	 * @return string
	 */
	public function sanitize_svg( $svg_setting ) {
		return SVG_Icon::sanitize_svg( $svg_setting );
	}
}
