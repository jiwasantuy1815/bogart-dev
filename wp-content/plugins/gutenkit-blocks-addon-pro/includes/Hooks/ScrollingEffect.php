<?php
namespace GutenkitPro\Hooks;

defined( 'ABSPATH' ) || exit;

use Gutenkit\Helpers\Utils;

class ScrollingEffect {

	use \Gutenkit\Traits\Singleton;

	/**
	 * class constructor.
	 * private for singleton
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( "gutenkit-scrolling-effects-common-3rd-party-scripts", array( $this, 'load_scrolling_effect_editor_script' ), 10, 3 );
		if(!is_admin()) {
			add_filter( "render_block_data", array( $this, 'load_scrolling_effect_3rd_party_frontend_script_on_demand' ), 10, 3 );
			add_filter( "gutenkit_save_element_markup", array( $this, 'add_scrolling_effect_attributes_on_save' ), 10, 3 );
		}
	}

	public function load_scrolling_effect_editor_script( $scripts, $module_name, $metadata ) {
		if($module_name == 'scrolling-effects' && is_admin()) {
			$scripts = array_merge($scripts, array('gsap', 'gsap-scroll-trigger'));
		}

		return $scripts;
	}

	/**
	 * enqueue blocks frontend assets
	 * loads styles and scripts for blocks on frontend
	 * 
	 * @return void
	 * @since 1.0.0
	 */
	public function load_scrolling_effect_3rd_party_frontend_script_on_demand( $parsed_block, $source_block, $parent_block ) {
		if( Utils::is_gkit_block('gkit', $parsed_block, 'isImageScrollingEffect') || Utils::is_gkit_block('gkit', $parsed_block, 'isScrollingEffect') ) {
			wp_enqueue_script('gsap');
			wp_enqueue_script('gsap-scroll-trigger');
		}

		return $parsed_block;
	}

	public function add_scrolling_effect_attributes_on_save( $block_content, $parsed_block, $instance ) {
		// Image Scrolling effects
		if ( Utils::is_gkit_block($block_content, $parsed_block, 'isImageScrollingEffect') ) {
			$settings = [
				'isImageScrollingEffect' => $parsed_block['attrs']['isImageScrollingEffect'] ?? false,
				'verticalImageScroll' => $parsed_block['attrs']['verticalImageScroll'] ?? [],
				'horizontalImageScroll' => $parsed_block['attrs']['horizontalImageScroll'] ?? [],
				'transperencyImageScroll' => $parsed_block['attrs']['transperencyImageScroll'] ?? [],
				'blurImageScroll' => $parsed_block['attrs']['blurImageScroll'] ?? [],
				'scaleImageScroll' => $parsed_block['attrs']['scaleImageScroll'] ?? [],
				'imageScrollDesktopDisable' => $parsed_block['attrs']['imageScrollDesktopDisable'] ?? false,
				'imageScrollTabletDisable' => $parsed_block['attrs']['imageScrollTabletDisable'] ?? false,
				'imageScrollMobileDisable' => $parsed_block['attrs']['imageScrollMobileDisable'] ?? false,
				'imageScrollTabletLandscapeDisable' => $parsed_block['attrs']['imageScrollTabletLandscapeDisable'] ?? false,
				'imageScrollMobileLandscapeDisable' => $parsed_block['attrs']['imageScrollMobileLandscapeDisable'] ?? false,
				'imageScrollLaptopDisable' => $parsed_block['attrs']['imageScrollLaptopDisable'] ?? false,
				'imageScrollWideScreenDisable' => $parsed_block['attrs']['imageScrollWideScreenDisable'] ?? false,
			];
			$block_content->set_attribute('data-image-scroll', wp_json_encode($settings));
		}

		// Advanced Scrolling effects
		if ( Utils::is_gkit_block($block_content, $parsed_block, 'isScrollingEffect') ) {
			$settings = [
				'isScrollingEffect' => $parsed_block['attrs']['isScrollingEffect'] ?? false,
				'verticalScroll' => $parsed_block['attrs']['verticalScroll'] ?? [],
				'horizontalScroll' => $parsed_block['attrs']['horizontalScroll'] ?? [],
				'transperencyScroll' => $parsed_block['attrs']['transperencyScroll'] ?? [],
				'blurScroll' => $parsed_block['attrs']['blurScroll'] ?? [],
				'scaleScroll' => $parsed_block['attrs']['scaleScroll'] ?? [],
				'rotateScroll' => $parsed_block['attrs']['rotateScroll'] ?? [],
				'scrollDesktopDisable' => $parsed_block['attrs']['scrollDesktopDisable'] ?? false,
				'scrollTabletDisable' => $parsed_block['attrs']['scrollTabletDisable'] ?? false,
				'scrollMobileDisable' => $parsed_block['attrs']['scrollMobileDisable'] ?? false,
				'scrollTabletLandscapeDisable' => $parsed_block['attrs']['scrollTabletLandscapeDisable'] ?? false,
				'scrollMobileLandscapeDisable' => $parsed_block['attrs']['scrollMobileLandscapeDisable'] ?? false,
				'scrollLaptopDisable' => $parsed_block['attrs']['scrollLaptopDisable'] ?? false,
				'scrollWideScreenDisable' => $parsed_block['attrs']['scrollWideScreenDisable'] ?? false,
			];

			$block_content->set_attribute('data-scroll', wp_json_encode($settings));
			$block_content->add_class('gkit-scrolling-effect');
		}

		return $block_content;
	}
}
