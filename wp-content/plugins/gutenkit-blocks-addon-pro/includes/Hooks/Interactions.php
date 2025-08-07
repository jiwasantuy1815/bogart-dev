<?php
namespace GutenkitPro\Hooks;

defined( 'ABSPATH' ) || exit;

use Gutenkit\Helpers\Utils;

class Interactions {

	use \Gutenkit\Traits\Singleton;

	/**
	 * class constructor.
	 * private for singleton
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action('enqueue_block_editor_assets', array($this, 'load_interactions_editor_script'));
		if (!is_admin()) {
			add_filter('render_block_data', array($this, 'load_interactions_frontend_script_on_demand'), 10, 3);
			add_filter('gutenkit_save_element_markup', array($this, 'add_interactions_attributes_on_save'), 10, 3);
		}
	}

    public function load_interactions_editor_script() {
        wp_enqueue_script( 'gutenkit-interactions-editor-scripts' );
        wp_enqueue_style( 'gutenkit-interactions-editor-styles' );
    }

	public function validate_interactions( $interactions ) {
		$valid_interactions = [];
		foreach($interactions as $key => $interaction){
			if(empty($interaction['triggerSettings']['trigger']['value'])){
				continue;
			}

			if(empty($interaction['actionSettings']['action']['value'])){
				continue;
			}

			$valid_interactions[] = $interaction;
		}

		return $interactions;
	}

	public function add_interactions_attributes_on_save( $block_content, $parsed_block, $instance ) {
		if ( Utils::is_gkit_block($block_content, $parsed_block, 'interactions') ) {
			$interactions = !empty($parsed_block['attrs']['interactions']) && is_array($parsed_block['attrs']['interactions']) ? $this->validate_interactions($parsed_block['attrs']['interactions']): [];
			if(!empty($interactions)) {
				$block_content->set_attribute('data-interactions', json_encode($interactions));
				$block_content->add_class('has-interactions');
			}
		}
		return $block_content;
	}

	public function load_interactions_frontend_script_on_demand( $parsed_block, $source_block, $parent_block ) {
		if( Utils::is_gkit_block('gkit', $parsed_block, 'interactions') ) {
			$settings = json_encode($parsed_block['attrs']['interactions']);

			if(str_contains($settings, 'show') || str_contains($settings, 'hide') || str_contains($settings, 'toggle') || str_contains($settings, 'scrollTo') || 
			str_contains($settings, 'scaleX') || str_contains($settings, 'scaleY') || str_contains($settings, 'rotate') || str_contains($settings, 'translateX') || 
			str_contains($settings, 'translateY') || str_contains($settings, 'skewX') || str_contains($settings, 'skewY') || str_contains($settings, 'opacity')){
				wp_enqueue_script('gsap');
			}

			if(str_contains($settings, 'startAnimation')){
				wp_enqueue_style('animate');
			}

			if(str_contains($settings, 'scrollTo')){
				wp_enqueue_script('gsap-scroll-to');
			}


			wp_enqueue_script('gutenkit-interactions-common-scripts');
		}

		return $parsed_block;
	}


}
