<?php
namespace GutenkitPro\Hooks;

defined( 'ABSPATH' ) || exit;

use Gutenkit\Helpers\Utils;

class MouseTrack {

	use \Gutenkit\Traits\Singleton;

	/**
	 * class constructor.
	 * private for singleton
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( "gutenkit-mouse-track-common-3rd-party-scripts", array( $this, 'load_mouse_track_editor_script' ), 10, 3 );
		if(!is_admin()) {
			add_filter( "render_block_data", array( $this, 'load_mouse_track_3rd_party_frontend_script_on_demand' ), 10, 3 );
			add_filter( "gutenkit_save_element_markup", array( $this, 'add_mouse_track_attributes_on_save' ), 10, 3 );
		}
	}

	public function load_mouse_track_editor_script( $scripts, $module_name, $metadata ) {
		if($module_name == 'mouse-track' && is_admin()) {
			$scripts = array_merge($scripts, array('gsap'));
		}
		return $scripts;
	}

	public function load_mouse_track_3rd_party_frontend_script_on_demand( $parsed_block, $source_block, $parent_block ) {
		if( Utils::is_gkit_block('gkit', $parsed_block, 'mouseTrackEffects') ) {
			wp_enqueue_script('gsap');
		}

		return $parsed_block;
	}

	public function add_mouse_track_attributes_on_save( $block_content, $parsed_block, $instance ) {
		if ( Utils::is_gkit_block($block_content, $parsed_block, 'mouseTrackEffects') ) {
			$settings = [
				'mouseEffectsTrackDirection' => $parsed_block['attrs']['mouseEffectsTrackDirection'] ?? 'direct',
				'mouseEffectsTrackSpeed' => $parsed_block['attrs']['mouseEffectsTrackSpeed'] ?? 0.5
			];
			$block_content->set_attribute('data-mouse-track-effects', wp_json_encode($settings));
			$block_content->add_class('gkit-mouse-track-effects');
		}

		return $block_content;
	}
}
