<?php

namespace GutenkitPro\Config;

defined( 'ABSPATH' ) || exit;

/**
 * Register modules class
 *
 * @since 0.1.0
 * @return void
 */

class Modules {

	use \GutenkitPro\Traits\Singleton;

	/**
	 * Modules constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Set pro modules list
		add_filter( 'gutenkit/modules/list', array( $this, 'set_pro_modules_list' ) );

		// Load module assets
		foreach ( $this->get_modules_list() as $module => $module_data ) {
			add_filter( "gutenkit_pro_{$module}_assets", array( $this, 'load_module_assets' ) );
		}
	}

	/**
	 * Sets the list of pro modules.
	 *
	 * @param array $modules The list of modules.
	 * @return array The updated list of modules.
	 * @since 1.0.0
	 */
	public function set_pro_modules_list($modules) {
		$modules_list = array_merge($modules, $this->get_modules_list());
		return $modules_list;
	}

	/**
	 * Retrieves the list of modules.
	 *
	 * @return array The list of modules.
	 * @since 1.0.0
	 */
	public function get_modules_list() {
		$modules = [
			'scrolling-effects' => array(
				'slug'          => 'scrolling-effects',
				'title'         => 'Scrolling Effects (Parallax)',
				'package'       => 'pro',
				'auto_enqueue'  => true,
				'attributes'    => array( 'new' ),
				'status'        => 'active',
			),
			'glass-morphism' => array(
				'slug'			=> 'glass-morphism',
				'title'			=> 'Glass Morphism',
				'package'		=> 'pro',
				'auto_enqueue'	=> true,
				'attributes'	=> array( 'new' ),
				'status'		=> 'active',
			),
			'one-page-scroll' => array(
				'slug'			=> 'one-page-scroll',
				'title'			=> 'One Page Scroll',
				'package'		=> 'pro',
				'auto_enqueue'	=> false,
				'attributes'	=> array( 'new' ),
				'status'		=> 'active',
			),
			'css-transform' => array(
				'slug'			=> 'css-transform',
				'title'			=> 'CSS Transform',
				'package'		=> 'pro',
				'auto_enqueue'	=> true,
				'attributes'	=> array( 'new' ),
				'status'		=> 'active',
			),
			'advanced-tooltip' => array(
				'slug'			=> 'advanced-tooltip',
				'title'			=> 'Advanced Tooltip',
				'package'		=> 'pro',
				'auto_enqueue'	=> true,
				'attributes'	=> array( 'new' ),
				'status'		=> 'active',
			),
			'dynamic-content' => array(
				'slug'			=> 'dynamic-content',
				'title'			=> 'Dynamic Content',
				'package'		=> 'pro',
				'auto_enqueue'	=> true,
				'attributes'	=> array( 'new' ),
				'status'		=> 'inactive',
				'badge'			=> ['new', 'beta'],
			),
			'sticky' => array(
				'slug'			=> 'sticky',
				'title'			=> 'Sticky',
				'package'		=> 'pro',
				'auto_enqueue'	=> true,
				'attributes'	=> array( 'new' ),
				'status'		=> 'active',
			),
			'mouse-tilt' => array(
				'slug'			=> 'mouse-tilt',
				'title'			=> 'Mouse Tilt',
				'package'		=> 'pro',
				'auto_enqueue'	=> true,
				'attributes'	=> array( 'new' ),
				'status'		=> 'active',
			),
			'mouse-track' => array(
				'slug'			=> 'mouse-track',
				'title'			=> 'Mouse Track',
				'package'		=> 'pro',
				'auto_enqueue'	=> true,
				'attributes'	=> array( 'new' ),
				'status'		=> 'active',
			),
			'masking' => array(
				'slug'			=> 'masking',
				'title'			=> 'Masking',
				'package'		=> 'pro',
				'auto_enqueue'	=> true,
				'attributes'	=> array( 'new' ),
				'status'		=> 'active',
				'badge'			=> ['new'],
			),
			'custom-css' => array(
				'slug'			=> 'custom-css',
				'title'			=> 'Custom CSS',
				'package'		=> 'pro',
				'auto_enqueue'	=> true,
				'attributes'	=> array( 'new' ),
				'status'		=> 'active',
				'badge'			=> ['new'],
			),
			'display-conditions' => array(
				'slug'			=> 'display-conditions',
				'title'			=> 'Display Conditions',
				'package'		=> 'pro',
				'auto_enqueue'	=> true,
				'attributes'	=> array('new'),
				'status'		=> 'active',
				'badge'			=> ['new'],
			),
			'smooth-scroll' => array(
				'slug'			=> 'smooth-scroll',
				'title'			=> 'Smooth Scroll',
				'package'		=> 'pro',
				'auto_enqueue'	=> false,
				'attributes'	=> array( 'new' ),
				'status'		=> 'active',
				'badge'			=> ['new'],
			),
			'interactions' => array(
				'slug'			=> 'interactions',
				'title'			=> 'Interactions',
				'package'		=> 'pro',
				'auto_enqueue'	=> false,
				'attributes'	=> array( 'new' ),
				'status'		=> 'inactive',
				'badge'			=> ['new'],
			),
			'particle' => array(
				'slug'			=> 'particle',
				'title'			=> 'Particle',
				'package'		=> 'pro',
				'auto_enqueue'	=> true,
				'attributes'	=> array( 'new' ),
				'status'		=> 'inactive',
				'badge'			=> ['new'],
			),
			'scroll-spy' => array(
				'slug'			=> 'scroll-spy',
				'title'			=> 'Scroll Spy',
				'package'		=> 'pro',
				'auto_enqueue'	=> true,
				'attributes'	=> array( 'new' ),
				'status'		=> 'inactive',
				'badge'			=> ['new'],
			),
			'video-scroller' => array(
				'slug'			=> 'video-scroller',
				'title'			=> 'Video Scroller',
				'package'		=> 'pro',
				'auto_enqueue'	=> false,
				'attributes'	=> array( 'new' ),
				'status'		=> 'inactive',
				'badge'			=> ['new'],
			),
		];

		return apply_filters( 'gutenkit/pro/modules/list', $modules );
	}

	/**
	 * Load module assets
	 *
	 * @param string $module The module name.
	 * @return array The module assets.
	 * @since 1.0.0
	 */
	public function load_module_assets( $module ) {
		return [
			'url' => GUTENKIT_PRO_PLUGIN_URL . 'build/modules/' . $module . '/',
			'path' => GUTENKIT_PRO_PLUGIN_DIR . 'build/modules/' . $module . '/',
		];
	}
}
