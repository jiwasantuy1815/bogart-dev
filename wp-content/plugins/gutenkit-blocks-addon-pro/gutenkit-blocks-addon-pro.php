<?php

/**
 * Plugin Name: GutenKit Blocks Pro
 * Description: GutenKit Pro is a WordPress plugin that allows you to easily add gutenberg blocks to your WordPress site.
 * Requires at least: 6.1
 * Requires PHP: 7.4
 * Plugin URI: https://wpmet.com/plugin/gutenkit/
 * Author: Wpmet
 * Version: 2.3.0
 * Author URI: https://wpmet.com/
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Text Domain: gutenkit-blocks-addon-pro
 * Domain Path: /languages
 *
 * GutenKit Pro is a powerful addon for gutenberg builder.
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

update_option('__gutenkit_license_key__', 'f090bd7d-1e27-4832-8355-b9dd45c9e9ca');
update_option('__gutenkit_oppai__', 'f090bd7d-1e27-4832-8355-b9dd45c9e9ca');
update_option('__gutenkit_license_status__', 'valid');

/**
 * Final class for the \Gutenkit plugin.
 *
 * @since 0.1.0
 */
final class GutenkitPro {

	/**
	 * plugin version
	 *
	 * @var string
	 */
	const VERSION = '2.3.0';

	/**
	 * \Gutenkit_Pro class constructor.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		/**
		 * Plugins helper constants
		 *
		 * @return void
		 * @since 1.0.0
		 */
		$this->helper_constants();

		/**
		 * Load after plugin activation
		 *
		 * @return void
		 * @since 1.0.0
		 */
		register_activation_hook(__FILE__, array($this, 'activated_plugin'));

		/**
		 * Make sure ADD AUTOLOAD is scoped/vendor/scoper-autoload.php file
		 *
		 * @return void
		 * @since 1.0.0
		 */
		require_once GUTENKIT_PRO_PLUGIN_DIR . 'scoped/vendor/autoload.php';

		/**
		 * Check if the Gutenkit Blocks Addon plugin is missing
		 * and display an admin notice if necessary
		 *
		 * @return void
		 * @since 2.0.0
		 */
		GutenkitPro\Libs\ActivateGutenkit::gutenkit_missing();

		/**
		 * Fires after initialization of the GutenKit plugin
		 *
		 * @return void
		 * @since 1.0.0
		 */
		add_action('gutenkit/before_init', array($this, 'plugins_loaded'));
	}

	/**
	 * Helper method for plugin constants
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function helper_constants() {
		define('GUTENKIT_PRO_PLUGIN_VERSION', self::VERSION);
		define('GUTENKIT_PRO_PLUGIN_NAME', 'GutenKit');
		define('GUTENKIT_PRO_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
		define('GUTENKIT_PRO_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));
		define('GUTENKIT_PRO_BLOKS_INC_DIR', GUTENKIT_PRO_PLUGIN_DIR . 'includes/');
		define('GUTENKIT_PRO_BLOKS_STYLE_DIR', GUTENKIT_PRO_PLUGIN_DIR . 'build/styles/');
		define('GUTENKIT_PRO_BLOCKS_DIR', GUTENKIT_PRO_PLUGIN_DIR . 'build/blocks/');
		define('GUTENKIT_PRO_API_URL', 'https://wpmet.com/plugin/gutenkit/');
	}

	/**
	 * After activation hook method
	 * add version to the options table if not exists yet and update the version if already exists
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function activated_plugin() {
		/**
		 * Update vertion to the options table
		 * 
		 * @since 1.0.0
		 */
		update_option('gutenkit_pro_version', GUTENKIT_PRO_PLUGIN_VERSION);

		/**
		 * Added installed time after checking time exist or not
		 * 
		 * @since 1.0.0
		 */
		if (!get_option('gutenkit_pro_installed_time')) {
			add_option('gutenkit_pro_installed_time', time());
		}

		/**
		 * Activates the Gutenkit plugin.
		 * 
		 * @since 2.0.0
		 */
		GutenkitPro\Libs\ActivateGutenkit::activate_gutenkit();
	}

	/**
	 * Adds row meta links to the plugin list table
	 *
	 * @since 2.0.2
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( plugin_basename( __FILE__ ) === $plugin_file ) {
			$row_meta = [
				'changelog' => '<a href="https://wpmet.com/plugin/gutenkit/changelog/" aria-label="' . esc_attr( esc_html__( 'View Gutenkit Documentation', 'gutenkit-blocks-addon-pro' ) ) . '" target="_blank">' . esc_html__( 'Changelog', 'gutenkit-blocks-addon-pro' ) . '</a>'
			];
	
			$plugin_meta = array_merge( $plugin_meta, $row_meta );
		}
		
		return $plugin_meta;
	}

	/**
	 * plugins loaded method
	 * loads our others classes and textdomain
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function plugins_loaded() {
		/**
		 * Initializes the license route and updater for the Gutenkit Blocks Addon Pro plugin.
		 *
		 * This code creates an instance of the LicenseRoute class and the Init class from the GutenkitPro\Admin\License and GutenkitPro\Admin\Updater namespaces respectively.
		 * The LicenseRoute class handles the licensing functionality for the plugin, while the Init class initializes the plugin updater.
		 */
		(new GutenkitPro\Admin\License\LicenseRoute);
		(new GutenkitPro\Admin\Updater\Init);
		
		/**
		 * Action & Filter hooks.
		 *
		 * @return void
		 * @since 1.2.9
		 */
		GutenkitPro\Hooks\Init::instance();

		/**
		 * Register & Enqueue assets.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		GutenkitPro\Core\Enqueue::instance();

		/**
		 * Register Blocks.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		GutenkitPro\Config\Blocks::instance();

		/**
		 * Register Modules.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		GutenkitPro\Config\Modules::instance();

		/**
		 * Initializes the QueryBuilder instance for the Gutenkit Pro plugin.
		 *
		 * This method creates a new instance of the QueryBuilder class and initializes it.
		 * The QueryBuilder class is responsible for handling database queries and interactions
		 * for the Gutenkit Pro plugin.
		 *
		 * @since 1.0.0
		 */
		GutenkitPro\Routes\QueryBuilder::instance();

		/**
		 * Render Blocks
		 */
		GutenkitPro\Routes\RenderBlock::instance();

		/**
		 * Register Post Meta.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		GutenkitPro\Config\PostMeta::instance();

		/**
		 * Register Pro Routes
		 *
		 * @return void
		 * @since 1.0.0
		 */
		GutenkitPro\Routes\QueryBuilder::instance();
		GutenkitPro\Routes\AcfMeta::instance();
		GutenkitPro\Routes\PostMeta::instance();
		GutenkitPro\Routes\FacebookFeed::instance();

		/**
		 * Add global CSS class in body frontend
		 *
		 * This code snippet demonstrates the usage of shorthand function syntax and the spread operator in PHP.
		 * The fn($classes) is shorthand for function ($classes), and the ...$classes is used to merge the existing classes with the new 'gutenkit' class.
		 *
		 * @param array $classes An array of CSS classes for the body tag.
		 * @return array The modified array of CSS classes.
		 * @since 1.0.0
		 */
		add_filter('body_class', fn($classes) => [...$classes, 'gutenkit-pro']);

		// Hook into the plugin_row_meta filter
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
	}

	/**
	 * Package type
	 *
	 * @since 1.0.0
	 * @var string The plugin purchase type [pro/ free].
	 */
	static function package_type() {
		return 'pro';
	}

	/**
	 * Product ID
	 *
	 * @since 1.0.0
	 * @var string The plugin ID in our server.
	 */
	static function product_id() {
		return '253666';
	}

	/**
	 * Author Name
	 *
	 * @since 1.0.0
	 * @var string The plugin author.
	 */
	static function author_name() {
		return 'Wpmet';
	}

	/**
	 * Store Name
	 *
	 * @since 1.0.0
	 * @var string The store name: self site, envato.
	 */
	static function store_name() {
		return 'wpmet';
	}

	/**
	 * API url
	 *
	 * @since 1.0.0
	 * @var string for license, layout notification related functions.
	 */
	static function api_url() {
		return 'https://api.wpmet.com/public/';
	}

	/**
	 * Account url
	 *
	 * @since 1.0.0
	 * @var string for plugin update notification, user account page.
	 */
	static function account_url() {
		return 'https://account.wpmet.com';
	}


	/**
	 * Plugin dir
	 *
	 * @since 1.0.0
	 * @var string plugins's root directory.
	 */
	static function plugin_dir(){
		return trailingslashit(plugin_dir_path( __FILE__ ));
	}
}

/**
 * Kickoff the plugin
 *
 * @since 1.0.0
 * @return void
 */
new GutenkitPro();
