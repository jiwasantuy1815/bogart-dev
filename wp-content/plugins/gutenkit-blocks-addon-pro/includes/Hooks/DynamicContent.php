<?php

namespace GutenkitPro\Hooks;

use WP_HTML_Tag_Processor;

defined('ABSPATH') || exit;

class DynamicContent
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
		// Hook into the 'pre_render_block' filter
		$active_modules = \Gutenkit\Config\Modules::get_active_modules_list();
		if (!empty($active_modules['dynamic-content']) && !is_admin()) {
			add_filter('render_block', array($this, 'add_dynamic_content_on_save'), 10, 2);
			add_filter('render_block_data', array($this, 'dynamic_background'), 10, 2);
			add_filter('render_block', [$this, 'dynamic_background_style'], 99999999, 2);
		}
	}

	public function add_dynamic_content_on_save($block_content, $parsed_block)
	{
		// Exclude the blocks from the dynamic content
		if (isset($parsed_block['blockName']) && strstr($parsed_block['blockName'], 'gutenkit')) {
			// Detect encoding of the input content
			$encoding = mb_detect_encoding($block_content, 'UTF-8, ISO-8859-1, ISO-8859-15', true);
			
			// Convert the content to UTF-8
			if ($encoding != 'UTF-8') {
				$block_content = mb_convert_encoding($block_content, 'UTF-8', 'auto');
			}

			libxml_use_internal_errors(true); // suppress errors

			// Load the HTML content into DOMDocument
			$dom = new \DOMDocument();
			@$dom->loadHTML('<?xml encoding="UTF-8">' . $block_content); // Corrected encoding
			libxml_clear_errors(); // Clear any libxml errors that have been suppressed

			$xpath = new \DOMXPath($dom);

			$block_content = $this->dynamic_content_text($xpath, $dom);
			$block_content = $this->dynamic_content_image($xpath, $dom);
			$block_content = $this->dynamic_content_url($xpath, $dom);
		}
		return $block_content;
	}

	public function dynamic_content_text($xpath, $dom)
	{
		$this->process_rich_text($xpath, $dom);
		$this->process_input_text($xpath, $dom);
		return $this->get_body_content($dom);
	}

	private function process_rich_text($xpath, $dom)
	{
		// Query all gdc tags
		$gdcTags = $xpath->query("//gdc");

		foreach ($gdcTags as $gdcTag) {
			$closestWrapper = $xpath->query("ancestor::*[contains(@class, 'gutenkit-block')]", $gdcTag);
			if ($closestWrapper->length === 1) {
				$closestWrapperItem = $closestWrapper->item(0);
				$post_id = $closestWrapperItem->getAttribute('data-post-id');
				$this->process_dynamic_text($gdcTag, $dom, $post_id);
			} elseif ($closestWrapper->length === 0 && $gdcTags->length > 0) {
				$this->process_dynamic_text($gdcTag, $dom, get_the_ID()); // Call without post_id
			}
		}
	}
	private function process_input_text($xpath, $dom)
	{
		// Query all gdc tags
		$gdcTags = $xpath->query("//*[contains(text(), 'symbolText')]");
		foreach ($gdcTags as $gdcTag) {
			$closestWrapper = $xpath->query("ancestor::*[contains(@class, 'gutenkit-block')]", $gdcTag);
			if ($closestWrapper->length === 1) {
				$closestWrapperItem = $closestWrapper->item(0);
				$post_id = $closestWrapperItem->getAttribute('data-post-id');
				$this->process_dynamic_text($gdcTag, $dom, $post_id, 'json');
			} elseif ($closestWrapper->length === 0 && $gdcTags->length > 0) {
				$this->process_dynamic_text($gdcTag, $dom, get_the_ID(), 'json'); // Call without post_id
			}
		}
	}

	// Extracted method to handle the selectedPath logic
	private function process_dynamic_text($gdcTag, $dom, $post_id = null, $sourceType = 'attributes')
	{
		// Get dynamic data from either JSON or attributes
		$dynamicData = $this->get_dynamic_data($gdcTag, $sourceType);

		// Extract dynamic content based on selected path
		$selectedPath = $dynamicData['selectedPath'];
		$fallback = $dynamicData['fallback'];

		switch ($selectedPath) {
			case '/postcustomfield':
				if ($dynamicData['postCustomField'] && $post_id) {
					$postCustomFieldValue = get_post_meta($post_id, $dynamicData['postCustomField'], true) ?: '';
					$this->appendHtmlToTag($dom, $gdcTag, $postCustomFieldValue ?: $fallback);
				}
				break;
			case '/postdate':
				$dateFormat = $dynamicData['dateFormat'] === 'custom'
					? $dynamicData['customDateFormat']
					: $dynamicData['dateFormat'];
				$postDateType = $gdcTag->getAttribute('postdatetype') ?: 'published';
				$postDate = $postDateType === 'published'
					? get_the_date($dateFormat, $post_id)
					: get_the_modified_date($dateFormat, $post_id);
				$this->appendHtmlToTag($dom, $gdcTag, $postDate ?: $fallback);
				break;
			case '/postexcerpt':
				$excerptLength = $gdcTag->getAttribute('excerptlength') ?: 0;
				$excerpt = !empty($excerptLength)
					? substr(get_the_excerpt($post_id), 0, $excerptLength)
					: get_the_excerpt($post_id);
				$this->appendHtmlToTag($dom, $gdcTag, $excerpt ?: $fallback);
				break;
			case '/postid':
				$this->appendHtmlToTag($dom, $gdcTag, $post_id ?: $fallback);
				break;
			case '/posttag':
				$postTags = get_the_tags($post_id);
				$tagIndex = $gdcTag->getAttribute('tagindex') ?: 0;
				$tag = isset($postTags[$tagIndex]) ? $postTags[$tagIndex]->name : '';
				$this->appendHtmlToTag($dom, $gdcTag, $tag ?: $fallback);
				break;
			case '/posttime':
				$timeFormat = $dynamicData['timeFormat'] === 'custom'
					? $dynamicData['customTimeFormat']
					: $dynamicData['timeFormat'];
				$postTimeType = $gdcTag->getAttribute('posttimetype') ?: 'published';
				$postTime = $postTimeType === 'published'
					? get_the_time($timeFormat, $post_id)
					: get_the_modified_time($timeFormat, $post_id);
				$this->appendHtmlToTag($dom, $gdcTag, $postTime ?: $fallback);
				break;
			case '/posttitle':
				$this->appendHtmlToTag($dom, $gdcTag, get_the_title($post_id) ?: $fallback);
				break;
			case '/postcategory':
				$postCategories = get_the_category($post_id);
				$catIndex = $gdcTag->getAttribute('categoryindex') ?: 0;
				$cat = isset($postCategories[$catIndex]) ? $postCategories[$catIndex]->name : '';
				$this->appendHtmlToTag($dom, $gdcTag, $cat ?: $fallback);
				break;
			case '/postcomments':
				$commentsLength = get_comments_number($post_id);
				if ($commentsLength == 0) {
					$comments = $gdcTag->getAttribute('nocomment') ?: '';
				} elseif ($commentsLength == 1) {
					$comments = $gdcTag->getAttribute('singlecomment') ?: '';
				} elseif ($commentsLength > 1) {
					$comments = str_replace('{number}', $commentsLength, $gdcTag->getAttribute('multicomments') ?: '');
				}
				$this->appendHtmlToTag($dom, $gdcTag, $comments ?: $fallback);
				break;
			case '/archivedescription':
				$this->appendHtmlToTag($dom, $gdcTag, get_the_archive_description() ?: $fallback);
				break;
			case '/archivetitle':
				$this->appendHtmlToTag($dom, $gdcTag, get_the_archive_title() ?: $fallback);
				break;
			case '/sitetagline':
				$this->appendHtmlToTag($dom, $gdcTag, get_bloginfo('description') ?: $fallback);
				break;
			case '/sitetitle':
				$this->appendHtmlToTag($dom, $gdcTag, get_bloginfo('name') ?: $fallback);
				break;
			case '/currentdatetime':
				$currentDateFormat = $dynamicData['dateFormat'] === 'custom'
					? $dynamicData['customDateFormat']
					: $dynamicData['dateFormat'];
				$currentTimeFormat = $dynamicData['timeFormat'] === 'custom'
					? $dynamicData['customTimeFormat']
					: $dynamicData['timeFormat'];
				$this->appendHtmlToTag($dom, $gdcTag, get_the_date($currentDateFormat) . ' ' . get_the_time($currentTimeFormat) ?: $fallback);
				break;
			case '/authordisplayname':
				$this->appendHtmlToTag($dom, $gdcTag, get_the_author_meta('display_name', get_post_field('post_author', $post_id)) ?: $fallback);
				break;
			case '/authorinfo':
				$authorMetaKey = $gdcTag->getAttribute('authorinfo');
				$authorInfo = get_the_author_meta($authorMetaKey, get_post_field('post_author', $post_id));
				$this->appendHtmlToTag($dom, $gdcTag, $authorInfo ?: $fallback);
				break;
			case '/currentusername':
				$this->appendHtmlToTag($dom, $gdcTag, wp_get_current_user()->user_login ?: $fallback);
				break;
			case '/currentuserinfo':
				$userInfo = wp_get_current_user();
				$metaKey = $gdcTag->getAttribute('currentuserinfo');
				$this->appendHtmlToTag($dom, $gdcTag, $userInfo->data->$metaKey ?: $fallback);
				break;
			case '/acf':
				if (function_exists('get_field')) {
					$acfFieldValue = get_field($dynamicData['acfField'], $post_id);
					$this->appendHtmlToTag($dom, $gdcTag, $acfFieldValue ?: $fallback);
				} else {
					$this->appendHtmlToTag($dom, $gdcTag, $fallback);
				}
				break;
			default:
				$this->appendHtmlToTag($dom, $gdcTag, $fallback);
				break;
		}
	}

	// Helper function to get dynamic data from attributes or textContent JSON
	private function get_dynamic_data($gdcTag, $sourceType = 'attributes')
	{
		if ($sourceType === 'json') {
			// Extract data from the JSON string in the textContent
			$textContent = $gdcTag->textContent;
			$dynamicData = json_decode($textContent, true);

			return [
				'selectedPath'    => $dynamicData['selectedpath'] ?? '',
				'fallback'        => $dynamicData['fallback'] ?? '',
				'postCustomField' => $dynamicData['postcustomfield'] ?? $dynamicData['postcustomfieldkey'] ?? '',
				'dateFormat'      => $dynamicData['dateformat'] ?? 'F j, Y',
				'customDateFormat' => $dynamicData['customdateformat'] ?? '',
				'timeFormat'      => $dynamicData['timeformat'] ?? 'g:i A',
				'customTimeFormat' => $dynamicData['customtimeformat'] ?? '',
				'acfField'        => $dynamicData['acffield'] ?? '',
			];
		} else {
			// Extract data from the element's attributes (old version)
			return [
				'selectedPath'    => $gdcTag->getAttribute('selectedpath'),
				'fallback'        => $gdcTag->getAttribute('fallback') ?: '',
				'postCustomField' => $gdcTag->getAttribute('postcustomfield') ?: $gdcTag->getAttribute('postcustomfieldkey'),
				'dateFormat'      => !empty($gdcTag->getAttribute('dateformat')) ? $gdcTag->getAttribute('dateformat') : 'F j, Y',
				'customDateFormat' => $gdcTag->getAttribute('customdateformat'),
				'timeFormat'      => !empty($gdcTag->getAttribute('timeformat')) ? $gdcTag->getAttribute('timeformat') : 'g:i A',
				'customTimeFormat' => $gdcTag->getAttribute('customtimeformat'),
				'acfField'        => $gdcTag->getAttribute('acffield'),
			];
		}
	}



	public function dynamic_content_image($xpath, $dom)
	{
		// Query for all <img> elements with the class 'is-dynamic-image'
		$dynamic_images = $xpath->query("//img[contains(@class, 'is-dynamic-image')]");

		foreach ($dynamic_images as $dynamic_image) {
			// Find the closest ancestor element with the class 'gutenkit-block'
			$closestWrapper = $xpath->query("ancestor::*[contains(@class, 'gutenkit-block')]", $dynamic_image);

			if ($closestWrapper->length === 1) {
				$closestWrapperItem = $closestWrapper->item(0);
				$post_id = $closestWrapperItem->getAttribute('data-post-id');
				$this->process_dynamic_image($dynamic_image, $post_id);
			} else if ($closestWrapper->length === 0 && $dynamic_images->length > 0) {
				$post_id = get_the_ID();
				$this->process_dynamic_image($dynamic_image, $post_id);
			}
		}

		// Get the body element's content
		return $this->get_body_content($dom);
	}

	public function process_dynamic_image($dynamic_image, $post_id)
	{
		// Get the value of the 'data-dynamic-content' attribute
		$dynamic_value = json_decode($dynamic_image->getAttribute('data-dynamic-content'), true);

		if (!empty($dynamic_value['isDynamicContent'])) {
			// Handle placeholder or fallback image
			$placeholder_image = method_exists('Gutenkit\Helpers\Utils', 'get_placeholder_image') ? \Gutenkit\Helpers\Utils::get_placeholder_image() : '';
			$fallback_image = !empty($dynamic_value['fallbackimage']) ? json_decode($dynamic_value['fallbackimage'], true)['url'] : $placeholder_image;
			$alt = '';
			$src = '';
			// Check the content type and process accordingly
			switch ($dynamic_value['dynamicContentType']) {
				case 'featuredimage':
					$image_id = get_post_thumbnail_id($post_id);
					$image = !empty($image_id) ? wp_get_attachment_image_src($image_id, 'full') : null;
					$src = $image[0] ?? $fallback_image;
					$alt = get_the_title($image_id) ?: '';
					break;

				case 'sitelogo':
					$logo_id = get_theme_mod('custom_logo');
					$image = !empty($logo_id) ? wp_get_attachment_image_src($logo_id, 'full') : null;
					$src = $image[0] ?? $fallback_image;
					$alt = get_the_title($logo_id) ?: '';
					break;

				case 'authoravatar':
					$author_id = get_post_field('post_author', $post_id);
					$src = get_avatar_url($author_id) ?: $fallback_image;
					$alt = get_the_author_meta('display_name', $author_id) ?: '';
					break;

				case 'useravatar':
					$user_id = get_current_user_id();
					$src = get_avatar_url($user_id) ?: $fallback_image;
					$alt = get_the_author_meta('display_name', $user_id) ?: '';
					break;

				case 'acfimage':
					if (function_exists('get_field')) {
						$acf_field_value = get_field($dynamic_value['acffield'], $post_id);
						if (is_array($acf_field_value)) {
							$src = $acf_field_value['url'];
							$alt = $acf_field_value['alt'];
						} elseif (is_integer($acf_field_value)) {
							$image = wp_get_attachment_image_src($acf_field_value, 'full');
							$src = $image[0];
							$alt = get_the_title($acf_field_value);
						} else {
							$src = $acf_field_value ?: $fallback_image;
						}
					} else {
						$src = $fallback_image;
						$alt = '';
					}
					break;

				default:
					$src = $fallback_image;
					$alt = '';
					break;
			}

			// Set the attributes for the image element
			$dynamic_image->setAttribute('src', $src);
			$dynamic_image->setAttribute('alt', $alt);
		}
	}


	public function dynamic_content_url($xpath, $dom)
	{
		// Query for all <a> elements with the 'data-dynamic-content-url' attribute
		$anchors = $xpath->query("//a[@data-dynamic-content-url]");

		foreach ($anchors as $anchor) {
			// Find the closest ancestor element with the class 'gutenkit-block'
			$closestWrapper = $xpath->query("ancestor::*[contains(@class, 'gutenkit-block')]", $anchor);

			if ($closestWrapper->length === 1) {
				$closestWrapperItem = $closestWrapper->item(0);
				$post_id = $closestWrapperItem->getAttribute('data-post-id');

				// Get and decode the 'data-dynamic-content-url' JSON
				$data_json = $anchor->getAttribute('data-dynamic-content-url');
				$data = !empty($data_json) ? json_decode($data_json, true) : [];

				if (!empty($data['isDynamicContent']) && !empty($data['dynamicContentType'])) {
					$content_type = $data['dynamicContentType'];
					$fallback_url = !empty($data['fallbackUrl']['url']) ? esc_url($data['fallbackUrl']['url']) : '';

					// Handle content type and set the URL
					$url = $this->get_dynamic_url($content_type, $data, $post_id, $fallback_url);
					if ($url) {
						$anchor->setAttribute('href', esc_url($url));
					}
				}
			} elseif ($closestWrapper->length === 0 && $anchors->length > 0) {
				$post_id = get_the_ID();
				// Get and decode the 'data-dynamic-content-url' JSON
				$data_json = $anchor->getAttribute('data-dynamic-content-url');
				$data = !empty($data_json) ? json_decode($data_json, true) : [];

				if (!empty($data['isDynamicContent']) && !empty($data['dynamicContentType'])) {
					$content_type = $data['dynamicContentType'];
					$fallback_url = !empty($data['fallbackUrl']['url']) ? esc_url($data['fallbackUrl']['url']) : '';

					// Handle content type and set the URL
					$url = $this->get_dynamic_url($content_type, $data, $post_id, $fallback_url);
					if ($url) {
						$anchor->setAttribute('href', esc_url($url));
					}
				}
			}
		}

		// Get and return the body element's content
		return $this->get_body_content($dom);
	}

	private function get_dynamic_url($content_type, $data, $post_id, $fallback_url)
	{
		switch ($content_type) {
			case 'post_custom_field_url':
				$field_name = $data['postcustomfield'] ?? '';
				$field_value = get_post_meta($post_id, $field_name, true);
				return $field_value ?: $fallback_url;

			case 'post_url':
				return get_permalink($post_id) ?: $fallback_url;

			case 'site_url':
				return get_site_url() ?: $fallback_url;

			case 'author_url':
				$author_id = get_post_field('post_author', $post_id);
				$author_link_type = $data['authorLinkType'] ?? '';
				if ($author_link_type === 'archive') {
					return get_author_posts_url($author_id) ?: $fallback_url;
				} elseif ($author_link_type === 'url') {
					return get_the_author_meta('url') ?: $fallback_url;
				}
				return $fallback_url;

			case 'comments_url':
				return get_comments_link($post_id) ?: $fallback_url;

			case 'archive_url':
				return get_post_type_archive_link($post_id) ?: $fallback_url;

			case 'acf_url':
				if (function_exists('get_field')) {
					$acf_field_value = get_field($data['acffield'] ?? '', $post_id);
					return $acf_field_value ?: $fallback_url;
				}
				return $fallback_url;

			case 'popup':
				return '#gutenkit-popup-' . ($data['popupID'] ?? '');

			default:
				return $fallback_url;
		}
	}

	public function dynamic_background($parsed_block, $source_block)
	{
		// Check if the block is a Gutenkit block and dynamic content module is active
		if (
			isset($parsed_block['blockName']) &&
			str_contains($parsed_block['blockName'], 'gutenkit')
		) {
			$parsed_block = $this->process_dynamic_background($parsed_block);
		}
		return $parsed_block;
	}

	public function process_dynamic_background($parsed_block)
	{
		$backgrounds = $parsed_block['attrs']['backgroundTracker'] ?? [];
		$placeholder_image = method_exists('Gutenkit\Helpers\Utils', 'get_placeholder_image') ? \Gutenkit\Helpers\Utils::get_placeholder_image() : '';

		if (empty($this->has_dynamic_background($parsed_block))) {
			return $parsed_block; // Early return if no backgrounds
		}

		foreach ($backgrounds as $background) {
			$fallback_image = $background['fallbackImage']['url'] ?? $placeholder_image;
			$content_type = $background['dynamicContentType'];
			$block_css = $parsed_block['attrs']['blocksCSS'] ?? [];
			$common_css = $parsed_block['attrs']['commonStyle'] ?? [];
			$post_id = get_the_ID();
			$image_url = '';

			// Fetch the image URL based on content type
			switch ($content_type) {
				case 'featuredimage':
					$image_url = $this->get_image_url_from_id(get_post_thumbnail_id());
					break;
				case 'sitelogo':
					$image_url = $this->get_image_url_from_id(get_theme_mod('custom_logo'));
					break;
				case 'authoravatar':
					$author_id = get_the_author_meta('ID');
					$image_url = get_avatar_url($author_id);
					break;
				case 'useravatar':
					$user_id = get_current_user_id();
					$image_url = get_avatar_url($user_id);
					break;
				case 'acfimage':
					$image_url = $this->get_acf_image_url($background['acffield'] ?? '', $post_id, $fallback_image);
					break;
				default:
					$image_url = $fallback_image;
					break;
			}

			// If a valid image URL is found, replace the placeholder in block CSS
			if (!empty($image_url)) {
				$block_class = $parsed_block['attrs']['blockClass'] ?? '';
				$unique_class = 'gutenkit-' . $post_id . '.' . $block_class;

				// Replace the block class and content type URL placeholder
				foreach ($block_css as $key => $css) {
					if (!empty($css)) {
						$block_css[$key] = $this->replace_css_content($css, $block_class, $unique_class, $content_type, $image_url);
					}
				}

				foreach ($common_css as $key => $css) {
					if (!empty($css)) {
						$common_css[$key] = $this->replace_css_content($css, $block_class, $unique_class, $content_type, $image_url);
					}
				}
			}
		}

		// Update the block's CSS with the dynamic images
		if (isset($parsed_block['attrs']['blocksCSS']) && !empty($block_css)) {
			$parsed_block['attrs']['blocksCSS'] = $block_css;
		}

		if (isset($parsed_block['attrs']['commonStyle'])) {
			$parsed_block['attrs']['commonStyle'] = $common_css;
		}

		return $parsed_block;
	}

	public function dynamic_background_style($block_content, $block)
	{
		// Check if the block is a Gutenkit block and dynamic content module is active
		if (
			isset($block['blockName']) &&
			str_contains($block['blockName'], 'gutenkit') &&
			$this->has_dynamic_background($block)
		) {
			$block_content = new WP_HTML_Tag_Processor($block_content);
			$block_content->next_tag();
			$block_content->add_class('gutenkit-' . get_the_ID());
			$block_content = $block_content->get_updated_html();
			$blocks_css = $this->merge_block_css($block);
			$css_content = $this->generate_responsive_css($blocks_css);
			if (!empty($css_content)) {
				$block_content = '<style type="text/css">' . $css_content . '</style>' . $block_content;
			}
		}

		return $block_content;
	}

	/**
	 * Helper function to replace the CSS content with unique class and image URL
	 */
	private function replace_css_content($css, $block_class, $unique_class, $content_type, $image_url)
	{
		// Check if CSS is empty
		if (empty($css)) {
			return $css; // Return the original (empty) CSS
		}

		// Replace the block class and content type URL placeholder
		if (str_contains($css, "url($content_type)")) {
			$css = str_replace(["url($content_type)", "$block_class"], ["url($image_url)", "$unique_class"], $css);
		}

		return $css;
	}

	private function has_dynamic_background($parsed_block)
	{
		$has_dynamic_background = false;
		if(isset($parsed_block['attrs']['backgroundTracker'])){
			foreach ($parsed_block['attrs']['backgroundTracker'] as $background) {
				if (empty($background['isDynamicContent']) || empty($background['dynamicContentType'])) {
					continue;
				}
	
				$has_dynamic_background = true;
				break; // Stop loop once a match is found
			}
		}

		return $has_dynamic_background;
	}

	private function merge_block_css($block)
	{
		$blocks_css = [];

		// Merge blocksCSS and commonStyle into blocks_css
		$sources = ['blocksCSS', 'commonStyle'];
		foreach ($sources as $source) {
			if (isset($block['attrs'][$source])) {
				foreach ($block['attrs'][$source] as $device => $css) {
					if (!isset($blocks_css[$device])) {
						$blocks_css[$device] = '';
					}
					$blocks_css[$device] .= $css;
				}
			}
		}

		return $blocks_css;
	}

	private function generate_responsive_css($blocks_css)
	{
		$device_list = \Gutenkit\Helpers\Utils::get_device_list();
		$css_content = '';
		$is_custom_styles_added = false;

		// Generate CSS for each device
		foreach ($device_list as $device) {
			$direction = $device['direction'] ?? 'max';
			$width = $device['value'] ?? '';
			$device_key = strtolower($device['slug'] ?? '');

			foreach ($blocks_css as $key => $block) {
				if (!empty($block) && trim($block) !== '') {
					// Base styles for desktop
					if ($device['value'] === 'base' && $key === 'desktop') {
						$css_content .= $block;
					}
					// Responsive media queries
					elseif (!empty($direction) && !empty($width) && $device_key === $key) {
						$css_content .= '@media (' . $direction . '-width: ' . $width . 'px) {' . trim($block) . '}';
					}

					// Custom styles added only once
					if ($key === 'customStyles' && !$is_custom_styles_added) {
						$is_custom_styles_added = true;
						$css_content .= $block;
					}
				}
			}
		}

		return $css_content;
	}


	// Helper function to get the image URL by attachment ID
	private function get_image_url_from_id($image_id)
	{
		if (!empty($image_id)) {
			$image = wp_get_attachment_image_src($image_id, 'full');
			return $image[0] ?? '';
		}
		return '';
	}

	// Helper function to get ACF image URL
	private function get_acf_image_url($acf_field, $post_id, $fallback_image)
	{
		if (function_exists('get_field') && !empty($acf_field)) {
			$acf_field_value = get_field($acf_field, $post_id);
			if (!empty($acf_field_value)) {
				if (is_array($acf_field_value)) {
					return $acf_field_value['url'];
				} elseif (is_integer($acf_field_value)) {
					return $this->get_image_url_from_id($acf_field_value);
				}
			}
		}
		return $fallback_image;
	}


	private function get_body_content($dom)
	{
		$body = $dom->getElementsByTagName('body')->item(0);
		$innerHTML = '';

		if (!empty($body->childNodes)) {
			foreach ($body->childNodes as $child) {
				$innerHTML .= $dom->saveHTML($child);
			}
		}

		return trim($innerHTML);
	}

	public function convertToTimeString($input)
	{
		// Check if the input is a string, number, or boolean
		if (is_string($input) || is_numeric($input) || is_bool($input)) {
			return strval($input); // Convert to string representation
		}

		// Check if the input is an array or an object
		if (is_array($input) || is_object($input)) {
			// Use var_export to convert arrays and objects to their string representations
			return var_export($input, true);
		}

		// If none of the above conditions are met, return the input as is
		// This could happen if the input is null or another unexpected type
		return $input;
	}

	public function appendHtmlToTag($dom, $gdcTag, $htmlContent)
	{
		if (!empty($htmlContent)) {
			$htmlContent = $this->convertToTimeString($htmlContent);

			if ($this->is_html($htmlContent)) {
				// Escape ampersands that are not already part of a valid entity
				$htmlContent = $this->fixSelfClosingTags($htmlContent);
				$htmlContent = preg_replace('/&(?!#?[a-z0-9]+;)/i', '&amp;', $htmlContent);

				$frag = $dom->createDocumentFragment();
				@$frag->appendXML($htmlContent);

				if ($frag->hasChildNodes()) {
					$gdcTag->nodeValue = '';
					$gdcTag->appendChild($frag);
				}
			} else {
				$gdcTag->nodeValue = htmlspecialchars($htmlContent, ENT_XML1 | ENT_COMPAT, 'UTF-8');
			}
		} else {
			$gdcTag->nodeValue = "";
		}
	}

	public function is_html($string) {
		// Strip all HTML tags using WordPress function
		$stripped_string = wp_strip_all_tags($string);

		// If the original string and stripped string are different, it's HTML
		return $string !== $stripped_string;
	}

	private function fixSelfClosingTags($html)
	{
		// List of self-closing tags in HTML
		$selfClosingTags = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'];

		foreach ($selfClosingTags as $tag) {
			// Regex to match improper or incomplete self-closing tags
			$pattern = '/<(' . $tag . ')([^>]*)>/i';

			// Callback to validate and correct the tags
			$html = preg_replace_callback($pattern, function ($matches) {
				$tag = $matches[1]; // The tag name
				$attributes = $matches[2]; // Any attributes inside the tag

				// Check if the tag is already properly self-closing
				if (preg_match('/\/\s*>$/', $matches[0])) {
					return $matches[0]; // Already correct, return as-is
				}

				// Otherwise, fix the tag to be properly self-closing
				return "<{$tag}{$attributes} />";
			}, $html);
		}
		return $html;
	}
	
}
