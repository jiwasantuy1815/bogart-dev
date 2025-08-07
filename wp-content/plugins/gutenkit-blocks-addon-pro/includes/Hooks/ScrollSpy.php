<?php

namespace GutenkitPro\Hooks;

defined('ABSPATH') || exit;

use Gutenkit\Helpers\Utils;
use WP_HTML_Tag_Processor;

class ScrollSpy
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
		add_filter("gutenkit-scrollspy-common-3rd-party-scripts", array($this, 'load_scrollspy_editor_script'), 10, 3);
		add_filter("gutenkit_save_element_markup", array($this, 'add_scrollspy_attributes_on_save'), 10, 3);
	}

	public function load_scrollspy_editor_script($scripts, $module_name, $metadata)
	{

		if ($module_name == 'scroll-spy' && is_admin()) {
			$scripts = array_merge($scripts, array('scroll-spy'));
		}

		return $scripts;
	}

	public function add_scrollspy_attributes_on_save($block_content, $parsed_block, $instance)
	{
		if (Utils::is_gkit_block($block_content, $parsed_block, 'isScrollSpyEnabled')) {
			$settings = [
				'blockClass' => $parsed_block['attrs']['blockClass'] ?? '',
				'blockID' => $parsed_block['attrs']['blockID'] ?? '',
				'isScrollSpyEnabled' => $parsed_block['attrs']['isScrollSpyEnabled'] ?? '',
				'scrollToOffset' => $parsed_block['attrs']['scrollToOffset'] ?? '',
			];

			$block_content->set_attribute('data-gkit-scrollspy', json_encode($settings));
		}

		return $block_content;
	}
}
