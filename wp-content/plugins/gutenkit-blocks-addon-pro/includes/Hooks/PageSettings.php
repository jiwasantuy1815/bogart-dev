<?php
namespace GutenkitPro\Hooks;

defined( 'ABSPATH' ) || exit;

class PageSettings {

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
			add_filter( 'gutenkit/generated_css', array( $this, 'render_page_settings_custom_css' ), 10 );
			add_filter( 'gutenkit/collected_css', array( $this, 'collect_page_settings_custom_css' ), 10 );
		}
	}

	public function render_page_settings_custom_css($css) {
		$post_id = get_the_ID();
		$meta_css = get_post_meta( $post_id, 'pageSettingsCustomCss', true );
		$executable_css = $this->replace_selector_with_class($meta_css, 'selector', 'html body.gutenkit.gutenkit-frontend');
		return $executable_css . $css;
	}

	public function collect_page_settings_custom_css($block) {
		if(!empty($block['attrs']['blockClass']) && !empty($block['attrs']['gutenkitBlockCustomCSS'])) {
			$block = $this->add_gutenkit_block_custom_css($block, 'gutenkitBlockCustomCSS', ".gutenkit-block.".$block['attrs']['blockClass']);
		}
		return $block;
	}

	public function add_gutenkit_block_custom_css($block, $module_key, $class) {
		if (!isset($block['blockName']) || !strstr($block['blockName'], 'gutenkit')) {
			return $block;
		}
		if (empty($block['attrs'][$module_key]) || !isset($block['attrs']['blockClass'])) {
			return $block;
		}

		$css = $this->replace_selector_with_class($block['attrs'][$module_key], 'selector', $class);
		if (empty($block['attrs']['commonStyle']['customStyles'])) {
			$block['attrs']['commonStyle']['customStyles'] = "";
		}
		
		$block['attrs']['commonStyle']['customStyles'] .= $css;
		$block['attrs'][$module_key] = null;
		
		return $block;
	}

	public function replace_selector_with_class($css, $selector, $class) {
		if(empty($css) || empty($selector) || empty($class)) return $css;

		return str_replace($selector, $class, $css);
	}
}
