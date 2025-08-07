<?php

namespace GutenkitPro\Hooks;

defined('ABSPATH') || exit;

use Gutenkit\Helpers\Utils;

class SmoothScroll
{

	use \Gutenkit\Traits\Singleton;

	/**
	 * class constructor.
	 * private for singleton
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct()
	{
		add_action('enqueue_block_assets', array($this, 'load_smooth_scroll_scripts'), 10);
	}

	public function load_smooth_scroll_scripts()
	{
		$enable_smooth_scroll = get_option('enable_smooth_scroll', false);
		$duration = get_option('smooth_scroll_duration', 1.2);
		$lerp = get_option('smooth_scroll_lerp', 0.1);
		$prevent_type = get_option('smooth_scroll_prevent_type', 'exclude');
		$exclude = get_option('smooth_scroll_exclude', []);
		$include = get_option('smooth_scroll_include', []);
		$currentPostId = get_the_ID();

		if($prevent_type === 'exclude') {
			foreach ($exclude as $item) {
				if ($item['value'] == $currentPostId) {
					$enable_smooth_scroll = false;
					break; // Exit the loop as soon as a match is found
				}
			}
		}

		if($prevent_type === 'include') {
			$enable_smooth_scroll = false;
			$include = array_map(function($item) { return $item['value']; }, $include);
			if(in_array($currentPostId, $include)) {
				$enable_smooth_scroll = true;
			}
		}

		$assets_path = GUTENKIT_PRO_PLUGIN_DIR . 'build/modules/smooth-scroll/common.asset.php';
		if (file_exists($assets_path) && $enable_smooth_scroll) {
			$assets = include_once $assets_path;
			if (!is_admin()) {
				if (isset($assets['version']) && isset($assets['dependencies'])) {
					wp_enqueue_style('gutenkit-smooth-scroll-common-styles');
					wp_enqueue_script('gsap');
					wp_enqueue_script('gsap-scroll-trigger');
					wp_enqueue_script('lenis');
					wp_enqueue_script('gutenkit-smooth-scroll-common-scripts', GUTENKIT_PRO_PLUGIN_URL . 'build/modules/smooth-scroll/common.js', $assets['dependencies'], $assets['version'], true);
					wp_localize_script('gutenkit-smooth-scroll-common-scripts', 'gutenkitSmoothScroll', ['duration' => $duration, 'lerp' => $lerp]);
				}
			}
		}

		if (is_admin()) {
			wp_enqueue_script('gutenkit-smooth-scroll-editor-scripts');
			wp_enqueue_style('gutenkit-smooth-scroll-editor-styles');
		}
	}
}
