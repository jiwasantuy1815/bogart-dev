<?php
namespace GutenkitPro\Hooks;

defined( 'ABSPATH' ) || exit;

use Gutenkit\Helpers\Utils;

class CollectedCss {

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
			add_filter("gutenkit/collected_css", function($block) {
				$block = $this->add_gutenkit_module_css($block, 'glassMorphismStyle');
				$block = $this->add_gutenkit_module_css($block, 'stickyStyles');
				$block = $this->add_gutenkit_module_css($block, 'OnPageScrollStyle');
				$block = $this->add_gutenkit_module_css($block, 'cssTransformModuleStyle');
				$block = $this->add_gutenkit_module_css($block, 'tooltipStyle');
				$block = $this->add_gutenkit_module_css($block, 'particleEffectStyle');
				
				// TODO: needs to check if module is enabled.
				if (!empty($block['attrs']['enableMasking'])) {
					$block = $this->add_gutenkit_module_css($block, 'maskingStyle');
				}
	
				return $block;
			}, 10);
		}
	}

	/**
	 * Adds module CSS to the commonStyle attribute of a block.
	 *
	 * @param array $block The block to modify.
	 * @param string $module_key The key of the module CSS in the block's attributes.
	 * @return array The modified block.
	 */
	public function add_gutenkit_module_css($block, $module_key) {
		if (!isset($block['blockName']) || !strstr($block['blockName'], 'gutenkit')) {
			return $block;
		}

		$attributes = $block['attrs'];
		$module_css = $attributes[$module_key] ?? [];
		$common_css = $attributes['commonStyle'] ?? [];
		$device_list = Utils::get_device_list() ?? [];
		
		foreach ($device_list as $device) {
			$device = strtolower($device['slug']);
			if (!isset($common_css[$device])) {
				$common_css[$device] = '';
			}

			if (!empty($module_css[$device])) {
				$common_css[$device] .= $module_css[$device];
			}
		}
		
		$block['attrs']['commonStyle'] = $common_css;
		$block['attrs'][$module_key] = null;
		
		return $block;
	}
}
