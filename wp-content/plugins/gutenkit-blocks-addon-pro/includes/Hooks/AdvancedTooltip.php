<?php
namespace GutenkitPro\Hooks;

defined( 'ABSPATH' ) || exit;

use Gutenkit\Helpers\Utils;

class AdvancedTooltip {

	use \Gutenkit\Traits\Singleton;

	/**
	 * class constructor.
	 * private for singleton
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( "gutenkit-advanced-tooltip-common-3rd-party-scripts", array( $this, 'load_advanced_tooltip_editor_script' ), 10, 3 );
		if(!is_admin()) {
			add_filter( "render_block_data", array( $this, 'load_advanced_tooltip_3rd_party_frontend_script_on_demand' ), 10, 3 );
			add_filter( "gutenkit_save_element_markup", array( $this, 'add_advanced_tooltip_attributes_on_save' ), 10, 3 );
		}
	}

	public function load_advanced_tooltip_editor_script( $scripts, $module_name, $metadata ) {
		if($module_name == 'advanced-tooltip' && is_admin()) {
			$scripts = array_merge($scripts, array('floating-ui-core', 'floating-ui-dom'));
		}

		return $scripts;
	}

	public function load_advanced_tooltip_3rd_party_frontend_script_on_demand( $parsed_block, $source_block, $parent_block ) {
		if( Utils::is_gkit_block('gkit', $parsed_block, 'enableTooltip') ) {
			wp_enqueue_script('floating-ui-core');
			wp_enqueue_script('floating-ui-dom');

			// Add tooltip styles
			wp_enqueue_style('gutenkit-tooltip', GUTENKIT_PRO_PLUGIN_URL . 'build/modules/advanced-tooltip/editor.css', array(), GUTENKIT_PRO_PLUGIN_VERSION);
		}

		return $parsed_block;
	}

	public function add_advanced_tooltip_attributes_on_save($block_content, $parsed_block, $instance) {
		if ( Utils::is_gkit_block($block_content, $parsed_block, 'enableTooltip') ) {
			$settings = [
				'clientId' => $parsed_block['attrs']['blockClass'] ?? '',
				'enableTooltip' => $parsed_block['attrs']['enableTooltip'] ?? false,
				'tooltipPosition' => $parsed_block['attrs']['tooltipPosition'] ?? 'top',
				'tooltipTrigger' => $parsed_block['attrs']['tooltipTrigger'] ?? 'mouseenter',
				'tooltipContent' => !empty($parsed_block['attrs']['tooltipContent']) ? $parsed_block['attrs']['tooltipContent'] : 'Gkit Tooltip',
				'tooltipPlacement' => $parsed_block['attrs']['tooltipPlacement'] ?? '',
				'tooltipOffset' => $parsed_block['attrs']['tooltipOffset'] ?? 10,
				'enableTooltipArrow' => $parsed_block['attrs']['enableTooltipArrow'] ?? true,
				'tooltipAnimation' => $parsed_block['attrs']['tooltipAnimation'] ?? 'fade',
				'tooltipDelay' => $parsed_block['attrs']['tooltipDelay'] ?? 300
			];
			$block_content->set_attribute('data-tooltip', json_encode($settings));
		}

		return $block_content;
	}
}
