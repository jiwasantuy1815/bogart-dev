<?php
namespace GutenkitPro\Hooks;

defined( 'ABSPATH' ) || exit;

use Gutenkit\Helpers\Utils;

class Sticky {

	use \Gutenkit\Traits\Singleton;

	/**
	 * class constructor.
	 * private for singleton
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		if(!is_admin()) {
			add_filter( "render_block_data", array( $this, 'load_sticky_3rd_party_frontend_script_on_demand' ), 10, 3 );
			add_filter( "gutenkit_save_element_markup", array( $this, 'add_sticky_attributes_on_save' ), 10, 3 );
		}
	}

	public function load_sticky_3rd_party_frontend_script_on_demand( $parsed_block, $source_block, $parent_block ) {
		if( Utils::is_gkit_block('gkit', $parsed_block, 'stickyPosition') && $parsed_block['attrs']['stickyPosition'] !== 'none') {
			// Add sticky styles
			wp_enqueue_style('gutenkit-sticky', GUTENKIT_PRO_PLUGIN_URL . 'build/modules/sticky/editor.css', array(), GUTENKIT_PRO_PLUGIN_VERSION);
		}

		return $parsed_block;
	}

	public function add_sticky_attributes_on_save($block_content, $parsed_block, $instance) {
		if ( Utils::is_gkit_block($block_content, $parsed_block, 'stickyPosition') && $parsed_block['attrs']['stickyPosition'] !== 'none') {
			$settings = [
				'stickyPosition' => $parsed_block['attrs']['stickyPosition'] ?? 'none',
				'stickyUntil' => $parsed_block['attrs']['stickyUntil'] ?? '',
				'isStickyContainer' => $parsed_block['attrs']['isStickyContainer'] ?? false,
				'scrollEffect' => $parsed_block['attrs']['scrollEffect'] ?? 'fade',
				'stickyDesktopDisable' => $parsed_block['attrs']['stickyDesktopDisable'] ?? false,
				'stickyTabletDisable' => $parsed_block['attrs']['stickyTabletDisable'] ?? false,
				'stickyMobileDisable' => $parsed_block['attrs']['stickyMobileDisable'] ?? false,
				'stickyTabletLandscapeDisable' => $parsed_block['attrs']['stickyTabletLandscapeDisable'] ?? false,
				'stickyMobileLanscapeDisable' => $parsed_block['attrs']['stickyMobileLanscapeDisable'] ?? false,
				'stickyLaptopDisable' => $parsed_block['attrs']['stickyLaptopDisable'] ?? false,
				'stickyWideScreenDisable' => $parsed_block['attrs']['stickyWideScreenDisable'] ?? false,
				'stickYOffsetDesktop' => $parsed_block['attrs']['stickYOffsetDesktop'] ?? ['size' => 0, 'unit' => 'px'],
				'stickYOffsetTablet' => $parsed_block['attrs']['stickYOffsetTablet'] ?? ['size' => 0, 'unit' => 'px'],
				'stickYOffsetMobile' => $parsed_block['attrs']['stickYOffsetMobile'] ?? ['size' => 0, 'unit' => 'px'],
			];
			$block_content->set_attribute('data-sticky', json_encode($settings));
		}

		
		return $block_content;
	}
}
