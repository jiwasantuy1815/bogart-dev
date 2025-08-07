<?php
namespace GutenkitPro\Hooks;

defined( 'ABSPATH' ) || exit;

use Gutenkit\Helpers\Utils;

class VideoScroller {

	use \Gutenkit\Traits\Singleton;

	/**
	 * class constructor.
	 * private for singleton
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( "render_block_data", array( $this, 'load_frontend_scripts' ), 10, 3 );
		add_action('enqueue_block_editor_assets', array( $this, 'load_editor_scripts' ));
		add_filter( "gutenkit_save_element_markup", array( $this, 'add_video_scroller_attributes_on_save' ), 10, 3 );
	}

	public function load_frontend_scripts( $parsed_block, $source_block, $parent_block ) {
		if ( Utils::is_gkit_block('gkit', $parsed_block, 'enableVideoScroller') && !is_admin() ) {
			wp_enqueue_script( 'scrolly-video' );
			wp_enqueue_script( 'gutenkit-video-scroller-common-scripts' );
		}
        return $parsed_block;
	}

	public function load_editor_scripts() {
		wp_enqueue_script( 'gutenkit-video-scroller-editor-scripts' );
	}

	public function add_video_scroller_attributes_on_save($block_content, $parsed_block, $instance) {
		if ( Utils::is_gkit_block($block_content, $parsed_block, 'enableVideoScroller') && !is_admin() ) {
			$settings = [
				'transitionSpeed' => isset($parsed_block['attrs']['videoScrollerTransitionSpeed']) ? $parsed_block['attrs']['videoScrollerTransitionSpeed'] : 8,
				'frameThreshold' => isset($parsed_block['attrs']['videoScrollerFrameThreshold']) ? $parsed_block['attrs']['videoScrollerFrameThreshold'] : 0.1,
			];
			$block_content->add_class('gutenkit-video-scroller');
			$block_content->set_attribute('data-video-scroller', json_encode($settings));
		}

		return $block_content;
	}
}
