<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Widgets\Document_Search;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Premium_Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Includes_Files;

defined( 'ABSPATH' ) || exit;

/**
 * The main plugin class. Responsible for setting up to core plugin services.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin extends Premium_Plugin {

	const NAME    = 'Document Library Pro';
	const ITEM_ID = 194365;

	/**
	 * Constructs and initalizes the main plugin class.
	 *
	 * @param string $file The root plugin __FILE__
	 * @param string $version The current plugin version
	 */
	public function __construct( $file = null, $version = '1.0' ) {
		parent::__construct(
			[
				'id'                 => self::ITEM_ID,
				'name'               => self::NAME,
				'version'            => $version,
				'file'               => $file,
				'settings_path'      => 'admin.php?page=document_library_pro',
				'documentation_path' => 'kb-categories/document-library-pro-kb/',
			]
		);

		$this->add_service( 'plugin_setup', new Admin\Plugin_Setup( $this->get_file(), $this ), true );
	}

	/**
	 * Registers the plugin with WordPress.
	 */
	public function register() {
		parent::register();

		register_activation_hook( $this->get_file(), [ 'Barn2\\Plugin\\Document_Library_Pro\\Install', 'install' ] );

		add_action( 'plugins_loaded', [ $this, 'check_for_db_updates' ] );
		add_action( 'init', [ $this, 'handle_2_0_0_migrate_settings' ], 17 ); // run after post type registration and flush
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
	}

	/**
	 * Check for database updates.
	 */
	public function check_for_db_updates() {
		$this->check_updates();
	}

	/**
	 * Handle v2 settings migration.
	 *
	 * We need to handle this outside of the update functions because it runs too early.
	 */
	public function handle_2_0_0_migrate_settings() {
		if ( get_option( 'dlp_should_migrate_settings_2_0' ) ) {
			Settings_Migration::update_2_0_0_migrate_settings();
			update_option( 'dlp_should_migrate_settings_2_0', false );
		}
	}

	/**
	 * Retrieve the plugin services.
	 */
	public function add_services() {
		// Admin
		$this->add_service( 'admin', new Admin\Admin( $this ) );
		$this->add_service( 'settings_controller', new Admin\Settings_Controller( $this ) );
		$this->add_service( 'wizard', new Admin\Wizard\Setup_Wizard( $this ) );

		// Base
		$this->add_service( 'ptp_integration', new Integration\Posts_Table_Pro() );
		$this->add_service( 'post_type', new Post_Type( $this ) );
		$this->add_service( 'taxonomies', new Taxonomies( $this ) );
		$this->add_service( 'settings_compatibility', new Settings_Compatibility() );
		$this->add_service( 'rest_controller', new Rest\Rest_Controller() );

		// Frontend
		$this->add_service( 'rest_fields', new Rest_Fields() );
		$this->add_service( 'shortcode', new Shortcode() );
		$this->add_service( 'document_expiry', new Document_Expiry_Handler() );
		$this->add_service( 'document_expirator', new Document_Expirator() );
		$this->add_service( 'frontend_scripts', new Frontend_Scripts( $this ) );
		$this->add_service( 'ajax_handler', new Ajax_Handler() );
		$this->add_service( 'single_content', new Single_Content() );
		$this->add_service( 'comments', new Comments( $this ) );
		$this->add_service( 'preview_modal', new Preview_Modal() );
		$this->add_service( 'search_handler', new Search_Handler() );
		$this->add_service( 'shortcode/doc_search', new Shortcodes\Document_Search() );
		$this->add_service( 'rest_api', new Submissions\Rest_Api() );
		$this->add_service( 'submission_form', new Submissions\Frontend_Form() );
		$this->add_service( 'shortcode/submission_form', new Shortcodes\Frontend_Form() );

		// PTP Integration
		$this->add_service( 'ptp/frontend_scripts', new Posts_Table_Pro\Frontend_Scripts( $this ) );
		$this->add_service( 'ptp/ajax_handler', new Posts_Table_Pro\Ajax_Handler() );
		$this->add_service( 'ptp/theme_integration', new Posts_Table_Pro\Integration\Theme_Integration() );

		// 3rd Party Integration
		$this->add_service( 'integration/wp_term_order', new Integration\WP_Term_Order() );
		$this->add_service( 'integration/custom_taxonomy_order', new Integration\Custom_Taxonomy_Order() );
		$this->add_service( 'integration/facetwp', new Integration\FacetWP() );
		$this->add_service( 'integration/searchwp', new Integration\SearchWP() );

		// Includes
		$this->add_service( 'includes', new Includes_Files( $this ) );
	}

	/**
	 * Register Widgets
	 */
	public function register_widgets() {
		if ( ! $this->get_license()->is_valid() ) {
			return;
		}

		register_widget( Document_Search::class );
	}

	/**
	 * Check if plugin has updates.
	 */
	private function check_updates() {
		$code_version = $this->data['version'];
		$code_version = preg_replace( '/-.*$/', '', $code_version ); // Remove any postfix strings (e.g. -beta1, -rc1)
		$db_version   = get_option( 'dlp_db_version' );
		$db_version   = preg_replace( '/-.*$/', '', $db_version ); // Remove any postfix strings (e.g. -beta1, -rc1)

		if ( version_compare( $code_version, $db_version, '>' ) ) {
			$version_updates = Update_Functions::$updates;

			foreach ( (array) $version_updates as $version => $update_functions ) {
				$db_version = get_option( 'dlp_db_version' );

				// if we are going from version 1 db to v2 code then set a marker for the settings migration
				if ( version_compare( $db_version, '2.0.0', '<' ) && version_compare( $code_version, '2.0.0', '>=' ) ) {
					update_option( 'dlp_should_migrate_settings_2_0', true );
				}

				if ( version_compare( $db_version, $version, '<' ) && version_compare( $code_version, $version, '>=' ) ) {

					foreach ( $update_functions as $function ) {
						if ( is_callable( [ new Update_Functions(), $function ] ) ) {
							Update_Functions::$function();
						}
					}

					update_option( 'dlp_db_version', $version );
					$db_version = $version;
				}
			}

			if ( version_compare( $code_version, $db_version, '>' ) ) {
				update_option( 'dlp_db_version', $code_version );
			}
		}
	}
}
