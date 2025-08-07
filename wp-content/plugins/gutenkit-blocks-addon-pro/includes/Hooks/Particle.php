<?php

namespace GutenkitPro\Hooks;

defined('ABSPATH') || exit;

use Gutenkit\Helpers\Utils;
use WP_HTML_Tag_Processor;

class Particle
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
		add_filter("gutenkit-particle-common-3rd-party-scripts", array($this, 'load_particle_editor_script'), 10, 3);
		add_filter("render_block_data", array($this, 'load_particle_3rd_party_frontend_script_on_demand'), 10, 3);
		add_filter("gutenkit_save_element_markup", array($this, 'add_particle_attributes_on_save'), 10, 3);
	}

	public function load_particle_editor_script($scripts, $module_name, $metadata)
	{

		if ($module_name == 'particle' && is_admin()) {
			$scripts = array_merge($scripts, array('particle'));
		}

		return $scripts;
	}

	public function load_particle_3rd_party_frontend_script_on_demand($parsed_block, $source_block, $parent_block)
	{
		if (Utils::is_gkit_block('gkit', $parsed_block, 'enableParticleEffect')) {
			wp_enqueue_script('particle');
		}

		return $parsed_block;
	}

	public function add_particle_attributes_on_save($block_content, $parsed_block, $instance)
	{
		if (Utils::is_gkit_block($block_content, $parsed_block, 'enableParticleEffect')) {
			$settings = [
				'blockClass' => $parsed_block['attrs']['blockClass'] ?? '',
				'blockID' => $parsed_block['attrs']['blockID'] ?? '',
				'particleEffectFormat' => $parsed_block['attrs']['particleEffectFormat'] ?? 'preset',
				'particlePresetStyle' => $parsed_block['attrs']['particlePresetStyle'] ?? 'default',
				'particleJsonCode' => isset($parsed_block['attrs']['particleEffectFormat']) && $parsed_block['attrs']['particleEffectFormat'] == 'json'
					? $parsed_block['attrs']['particleJsonCode']
					: '',

				'particleJsonFile' => isset($parsed_block['attrs']['particleEffectFormat']) && $parsed_block['attrs']['particleEffectFormat'] == 'file'
					? ['url' => $parsed_block['attrs']['particleJsonFile']['url']]
					: ['']

			];

			$block_content->set_attribute('data-gkit-particle', json_encode($settings));
			//add a class to the block
			$block_content->add_class('gkit-particle-effects-' . $parsed_block['attrs']['blockClass']);
		}

		return $block_content;
	}
}
