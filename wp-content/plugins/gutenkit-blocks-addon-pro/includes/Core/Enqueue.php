<?php
namespace GutenkitPro\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue registrar.
 *
 * @since 1.0.0
 * @access public
 */
class Enqueue {

	use \Gutenkit\Traits\Singleton;

	/**
	 * class constructor.
	 * private for singleton
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'enqueue_block_assets', array( $this, 'blocks_scripts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'blocks_editor_scripts' ), 5 );
	}

	/**
	 * Enqueues necessary scripts and localizes data for the admin area.
	 *
	 * @param string $screen The current screen.
	 * @return void
	 * @since 1.0.0
	 */
	public function admin_scripts() {
		wp_localize_script(
			'wp-block-editor',
			'gutenkitPro',
			array(
				'plugin_url'    => GUTENKIT_PRO_PLUGIN_URL,
				'admin_url'    => admin_url(),
			)
		);
	}
	
	/**
	 * Enqueues the necessary scripts and styles for the blocks.
	 *
	 * Registers and enqueues various scripts and styles required for the blocks.
	 * This function is called to enqueue the scripts and styles when needed.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function blocks_scripts() {
		// Gsap js files
		wp_register_script(
			'gsap',
			GUTENKIT_PRO_PLUGIN_URL . 'assets/js/gsap.js',
			array(), GUTENKIT_PRO_PLUGIN_VERSION,
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);
		wp_register_script(
			'gsap-scroll-trigger',
			GUTENKIT_PRO_PLUGIN_URL . 'assets/js/gsap-scroll-trigger.js',
			array(),
			GUTENKIT_PRO_PLUGIN_VERSION,
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);
		wp_register_script(
			'gsap-observer',
			GUTENKIT_PRO_PLUGIN_URL . 'assets/js/gsap-observer.js',
			array(),
			GUTENKIT_PRO_PLUGIN_VERSION,
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);
		wp_register_script(
			'gsap-scroll-to',
			GUTENKIT_PRO_PLUGIN_URL . 'assets/js/gsap-scroll-to.js',
			array(),
			GUTENKIT_PRO_PLUGIN_VERSION,
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);

		// Register lottie js
		wp_register_script(
			'lottie-animation',
			GUTENKIT_PRO_PLUGIN_URL . 'assets/js/lottie.min.js',
			array(),
			GUTENKIT_PRO_PLUGIN_VERSION,
			true
		);

		wp_register_script(
			'floating-ui-core', GUTENKIT_PRO_PLUGIN_URL . 'assets/js/floating-ui-core.js',
			array(),
			GUTENKIT_PRO_PLUGIN_VERSION,
			true
		);
		wp_register_script(
			'floating-ui-dom',
			GUTENKIT_PRO_PLUGIN_URL . 'assets/js/floating-ui-dom.js',
			array(),
			GUTENKIT_PRO_PLUGIN_VERSION,
			true
		);

		// Chart js file
		wp_register_script(
			'chart',
			GUTENKIT_PRO_PLUGIN_URL . 'assets/js/chart.js',
			array(),
			GUTENKIT_PRO_PLUGIN_VERSION, true
		);

		// Particle js file
		wp_register_script('particle', GUTENKIT_PRO_PLUGIN_URL . 'assets/js/particle.js', array(), GUTENKIT_PRO_PLUGIN_VERSION, true);

		// Google map module
		if( ! method_exists( 'Gutenkit\Helpers\Utils', 'get_settings' ) ) {
			return;
		}

		$gmap_api_key = \Gutenkit\Helpers\Utils::get_settings('google_map', 'fields', 'api_key');
		if($gmap_api_key) {
			wp_register_script(
				'gutenkit-google-map-api', 'https://maps.googleapis.com/maps/api/js?key=' . $gmap_api_key . '&callback=initMap&loading=async',
				array(),
				GUTENKIT_PRO_PLUGIN_VERSION,
				array('strategy'  => 'async', 'in_footer' => false)
			);
		}

		// One Page Scroll module editor scripts
		$is_support_meta = post_type_supports(get_post_type(), 'custom-fields');
		$active_modules = (new \Gutenkit\Config\Modules())->get_active_modules();
		if(!empty($active_modules['one-page-scroll']) && $is_support_meta && is_admin()) {
			wp_enqueue_script('gutenkit-one-page-scroll-editor-scripts');
		}
	}

	/**
	 * enqueue block editor assets
	 * loads styles and scripts for block editor
	 * 
	 * @return void
	 * @since 1.0.0
	 */
	public function blocks_editor_scripts() {
		$global_asset_file = GUTENKIT_PRO_PLUGIN_DIR . 'build/gutenkit/global.asset.php';
		if ( file_exists( $global_asset_file ) ) {
			$global_asset = include_once $global_asset_file;
			if ( isset( $global_asset['version'] ) ) {
				wp_enqueue_script(
					'gutenkit-pro-blocks-editor-global',
					GUTENKIT_PRO_PLUGIN_URL . 'build/gutenkit/global.js',
					$global_asset['dependencies'],
					$global_asset['version'],
					false
				);

				wp_enqueue_style(
					'gutenkit-pro-blocks-editor-global',
					GUTENKIT_PRO_PLUGIN_URL . 'build/gutenkit/global.css',
					array(),
					$global_asset['version']
				);
			}
		}
	}
}
