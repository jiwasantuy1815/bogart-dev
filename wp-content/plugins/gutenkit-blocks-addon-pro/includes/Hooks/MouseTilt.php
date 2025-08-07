<?php
namespace GutenkitPro\Hooks;

defined( 'ABSPATH' ) || exit;

use Gutenkit\Helpers\Utils;

class MouseTilt {

	use \Gutenkit\Traits\Singleton;

	/**
	 * class constructor.
	 * private for singleton
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( "gutenkit-mouse-tilt-common-3rd-party-scripts", array( $this, 'load_mouse_tilt_editor_script' ), 10, 3 );
		if (!is_admin()) {
			add_filter("render_block_data", array($this, 'load_mouse_tilt_3rd_party_frontend_script_on_demand'), 10, 3);
			add_filter("gutenkit_save_element_markup", array($this, 'add_mouse_tilt_attributes_on_save'), 10, 3);
		}
	}

	public function load_mouse_tilt_editor_script( $scripts, $module_name, $metadata ) {
		if($module_name == 'mouse-tilt' && is_admin()) {
			$scripts = array_merge($scripts, array('vanilla-tilt'));
		}
		return $scripts;
	}

	public function load_mouse_tilt_3rd_party_frontend_script_on_demand( $parsed_block, $source_block, $parent_block ) {
		if( Utils::is_gkit_block('gkit', $parsed_block, 'mouseTiltEffects') ) {
			wp_enqueue_script('vanilla-tilt');
		}

		return $parsed_block;
	}

	public function add_mouse_tilt_attributes_on_save( $block_content, $parsed_block, $instance ) {
		if ( Utils::is_gkit_block($block_content, $parsed_block, 'mouseTiltEffects') ) {
			$settings = [
				'mouseEffectsTiltDirection' => $parsed_block['attrs']['mouseEffectsTiltDirection'] ?? false,
				'mouseTiltEffectsGyroscope' => $parsed_block['attrs']['mouseTiltEffectsGyroscope'] ?? false,
				'mouseTiltEffectsGlare' => $parsed_block['attrs']['mouseTiltEffectsGlare'] ?? false,
				'mouseEffectsTiltGlareSize' => $parsed_block['attrs']['mouseEffectsTiltGlareSize']['size'] ?? ['size' => 1, ],
				'mouseEffectsTiltScale' => $parsed_block['attrs']['mouseEffectsTiltScale']['size'] ?? ['size' => 1, ],
				'mouseEffectsTiltSpeed' => $parsed_block['attrs']['mouseEffectsTiltSpeed'] ?? ['size' => 500, ],
				'mouseEffectsTiltRotation' => $parsed_block['attrs']['mouseEffectsTiltRotation'] ?? ['size' => 20, ],
				'mouseEffectsTiltStartX' => $parsed_block['attrs']['mouseEffectsTiltStartX'] ?? ['size' => 0, ],
				'mouseEffectsTiltStartY' => $parsed_block['attrs']['mouseEffectsTiltStartY'] ?? ['size' => 0, ],
				'mouseEffectsTiltPerspective' => $parsed_block['attrs']['mouseEffectsTiltPerspective'] ?? ['size' => 1000, ],
				'tiltGrayscope' => $parsed_block['attrs']['tiltGrayscope'] ?? [],
			];
			$block_content->set_attribute('data-mouse-tilt-effects', wp_json_encode($settings));
			$block_content->add_class('gkit-mouse-tilt-effects');
		}

		return $block_content;
	}
}
