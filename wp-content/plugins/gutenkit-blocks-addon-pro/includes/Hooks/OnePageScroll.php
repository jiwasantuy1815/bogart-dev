<?php
namespace GutenkitPro\Hooks;

defined( 'ABSPATH' ) || exit;

use Gutenkit\Helpers\Utils;

class OnePageScroll {

	use \Gutenkit\Traits\Singleton;

	/**
	 * class constructor.
	 * private for singleton
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		if (!is_admin()) {
			add_action('wp_enqueue_scripts', array($this, 'load_one_page_scroll_3rd_party_frontend_script_on_demand'), 10);
			add_filter('gutenkit_save_element_markup', array($this, 'add_one_page_scroll_attributes_on_save'), 10, 3);
			add_filter('gutenkit/generated_css', array($this, 'add_one_page_navigation_dot_css'), 10);
		}
	}

	public function load_one_page_scroll_3rd_party_frontend_script_on_demand() {
		$post_id = get_the_ID();
		$is_one_page_scroll = get_post_meta( $post_id, 'enableOnePageScrollInPage', true );
		$is_support_meta = post_type_supports( get_post_type( $post_id ), 'custom-fields' );
		if( !empty( $is_one_page_scroll ) && $is_support_meta && !is_admin() ) {
			wp_enqueue_style('gutenkit-one-page-scroll-common-styles');
			wp_enqueue_script('gsap');
			wp_enqueue_script('gsap-observer');
			wp_enqueue_script('gsap-scroll-to');
			wp_enqueue_script('gutenkit-one-page-scroll-common-scripts');
			$settings = $this->get_settings($post_id);
			wp_localize_script('gutenkit-one-page-scroll-common-scripts', 'onePageScrollSettings', $settings);
		}
	}

	function get_settings($post_id) {
		$one_page_scroll_speed = get_post_meta( $post_id, 'onePageScrollSpeed', true );
		$one_page_scroll_animation = get_post_meta( $post_id, 'onePageScrollAnimation', true );
		$custom_icon = get_post_meta( $post_id, 'onePageScrollNavigationIcon', true );
		$settings = [
			"speed" => !empty( $one_page_scroll_speed ) ? $one_page_scroll_speed : 1000,
			"animation" => !empty( $one_page_scroll_animation ) ? $one_page_scroll_animation : "easeInOutQuad",
			"showDots" => get_post_meta( $post_id, 'onePageScrollShowDotNavigation', true ),
			"navigationStyle" => get_post_meta( $post_id, 'onePageScrollNavigation', true ),
			"navigationPosition" => get_post_meta( $post_id, 'onePageScrollNavigationPosition', true ),
			"customIcon" => !empty($custom_icon['src']) ? $custom_icon['src'] : '', 
		];

		return $settings;
	}

	/**
	 * Add blocks attributes on save
	 *
	 * @param string $block_content The block content.
	 * @param array $parsed_block The parsed block.
	 * @param array $instance The block instance.
	 * @return string The modified block content.
	 */
	public function add_one_page_scroll_attributes_on_save( $block_content, $parsed_block, $instance ) {	
		if( !empty( $parsed_block['attrs']['isOnePageScrollSection'] ) ) {
			$settings = [ 
				"tooltip" => isset($parsed_block['attrs']['onePageScrollTooltipText']) ? $parsed_block['attrs']['onePageScrollTooltipText'] : '',
				"tooltipColor" => !empty($parsed_block['attrs']['onPageScrollToolTipColor']) ? Utils::get_color("color", $parsed_block['attrs']['onPageScrollToolTipColor']) : "",
			];
			$block_content->add_class( 'gkit-one-page-scroll' );
			$block_content->set_attribute('data-ops-settings', wp_json_encode($settings));
		}

		return $block_content;
	}

	public function add_one_page_navigation_dot_css($css) {
		$deviceList = Utils::get_device_list();
		$devices = array_map(function($item) {
			return $item['slug'];
		}, $deviceList);

		$selectors = [
			'.gkit-one-page-scroll-dots',
			'.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-right',
			'.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-right .gkit-one-page-scroll-dot-timeline:not(:last-child)::before',
			'.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-left',
			'.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-left .gkit-one-page-scroll-dot-timeline:not(:last-child)::before',
			'.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-top',
			'.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-top .gkit-one-page-scroll-dot-timeline:not(:last-child)::before',
			'.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-bottom',
			'.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-bottom .gkit-one-page-scroll-dot-timeline:not(:last-child)::before',
			'li.gkit-one-page-scroll-dot',
			'li.gkit-one-page-scroll-dot.gkit-one-page-scroll-dot-scaleUp',
			'li.gkit-one-page-scroll-dot.gkit-one-page-scroll-dot-strokeDot',
			'li.gkit-one-page-scroll-dot.gkit-one-page-scroll-dot-strokeSimple',
			'li.gkit-one-page-scroll-dot:hover',
			'li.gkit-one-page-scroll-dot.active:hover',
			'li.gkit-one-page-scroll-dot.active',
			'.gkit-one-page-scroll-tooltip',
			'.gkit-one-page-scroll-tooltip:hover'
		];
		$frontCss = [];
		$post_id = get_the_ID();

		foreach ($devices as $device) {
			//getting the post meta value for navigation
			$one_page_scroll_horizontal_position = get_post_meta( $post_id, 'onePageScrollNavigationHorizontal', true );
			$one_page_scroll_vertical_position = get_post_meta( $post_id, 'onePageScrollNavigationVertical', true );
			$navigation_position = get_post_meta( $post_id, 'onePageScrollNavigationPosition', true );
			$navigation_vertical_default = ($navigation_position === 'top' || $navigation_position === 'bottom') ? "0%" : "50%";
			$one_page_scroll_navigation_color = get_post_meta( $post_id, 'onePageScrollNavigationColor', true );
			$one_page_scroll_navigation_color_hover = get_post_meta( $post_id, 'onePageScrollNavigationColorHover', true );
			$one_page_scroll_navigation_color_active = get_post_meta( $post_id, 'onePageScrollNavigationColorActive', true );
			$one_page_scroll_spacing = get_post_meta( $post_id, 'onePageScrollNavigationSpacing', true );
			

			$navigation_dot_width = get_post_meta($post_id, 'onePageScrollNavigationWidth', true);
			$navigation_dot_width_hover = get_post_meta($post_id, 'onePageScrollNavigationWidthHover', true);
			$navigation_dot_width_active = get_post_meta($post_id, 'onePageScrollNavigationWidthActive', true);
			$navigation_height = get_post_meta($post_id, 'onePageScrollNavigationHeight', true);
			$navigation_height_hover = get_post_meta($post_id, 'onePageScrollNavigationHeightHover', true);
			$navigation_height_active = get_post_meta($post_id, 'onePageScrollNavigationHeightActive', true);
			$navigation_dot_border = get_post_meta($post_id, 'onePageScrollNavigationBorder', true);
			$navigation_dot_border_hover = get_post_meta($post_id, 'onePageScrollNavigationBorderHover', true);
			$navigation_dot_border_active = get_post_meta($post_id, 'onePageScrollNavigationBorderActive', true);
			$navigation_dot_border_radius = get_post_meta($post_id, 'onePageScrollNavigationBorderRadius', true);
			$navigation_dot_border_radius_hover = get_post_meta($post_id, 'onePageScrollNavigationBorderRadiusHover', true);
			$navigation_dot_border_radius_active = get_post_meta($post_id, 'onePageScrollNavigationBorderRadiusActive', true);

			//getting the post meta value for tooltip
			$tooltip_color = get_post_meta($post_id, 'onePageScrollNavigationTooltipColor', true);
			$tooltip_color_hover = get_post_meta($post_id, 'onePageScrollNavigationTooltipColorHover', true);
			$tooltip_bg_color = get_post_meta($post_id, 'onePageScrollNavigationTooltipBgColor', true);
			$tooltip_bg_color_hover = get_post_meta($post_id, 'onePageScrollNavigationTooltipBgColorHover', true);
			$tooltip_padding = get_post_meta($post_id, 'onePageScrollNavigationTooltipPadding', true);
			$tooltip_padding_hover = get_post_meta($post_id, 'onePageScrollNavigationTooltipPaddingHover', true);
			$tooltip_border_radius = get_post_meta($post_id, 'onePageScrollNavigationTooltipBorderRadius', true);
			$tooltip_border_radius_hover = get_post_meta($post_id, 'onePageScrollNavigationTooltipBorderRadiusHover', true);
			$tooltip_typography = get_post_meta($post_id, 'onePageScrollNavigationTooltipTypography', true);

			/**
			 * Navigation dot control values
			 */
			$navigation_horizontal_position = !empty($one_page_scroll_horizontal_position[$device]['size']) ? Utils::get_slider_value($one_page_scroll_horizontal_position[$device]) : "0%";
			$navigation_vertical_position = !empty($one_page_scroll_vertical_position[$device]['size']) ? Utils::get_slider_value($one_page_scroll_vertical_position[$device]) : $navigation_vertical_default;
			$navigation_spacing = !empty($one_page_scroll_spacing[$device]['size']) ? Utils::get_slider_value($one_page_scroll_spacing[$device]) : "10px";

			//one page scroll navigation tooltip arrow style
			$tooltipArrowColor = (!empty($tooltip_bg_color) ? $tooltip_bg_color : ($one_page_scroll_navigation_color ?? "#ffffff"));
			$tooltipArrowColorHover = (!empty($tooltip_bg_color_hover) ? $tooltip_bg_color_hover : $tooltip_bg_color );

			$navigationDotWidth = !empty($navigation_dot_width[$device]['size']) ? $this->convertData($navigation_dot_width, $device, 'width') : [];
			$navigationDotWidthHover = !empty($navigation_dot_width_hover[$device]['size']) ? $this->convertData($navigation_dot_width_hover, $device, 'width') : [];
			$navigationDotWidthActive = !empty($navigation_dot_width_active[$device]['size']) ? $this->convertData($navigation_dot_width_active, $device, 'width') : [];
			$navigationDotHeight = !empty($navigation_height[$device]['size']) ? $this->convertData($navigation_height, $device, 'height') : [];
			$navigationDotHeightHover = !empty($navigation_height_hover[$device]['size']) ? $this->convertData($navigation_height_hover, $device, 'height') : [];
			$navigationDotHeightActive = !empty($navigation_height_active[$device]['size']) ? $this->convertData($navigation_height_active, $device, 'height') : [];

			$navigationDotBorder = [];
			$navigationDotBorderHover = [];
			$navigationDotBorderActive = [];
	
			$navigationDotBorder = (!empty($navigation_dot_border[$device])) ? Utils::get_border_value($navigation_dot_border[$device]) : [];
			$navigationDotBorderHover = (!empty($navigation_dot_border_hover[$device])) ? Utils::get_border_value($navigation_dot_border_hover[$device]) : [];
			$navigationDotBorderActive = (!empty($navigation_dot_border_active[$device])) ? Utils::get_border_value($navigation_dot_border_active[$device]) : [];

			
			$navigationDorBorderRadius = !empty($navigation_dot_border_radius[$device]) ? Utils::get_box_value($navigation_dot_border_radius[$device], 'border-radius') : ['border-radius' => '50%'];
			$navigationDorBorderRadiusHover = !empty($navigation_dot_border_radius_hover[$device]) ? Utils::get_box_value($navigation_dot_border_radius_hover[$device], 'border-radius') : ['border-radius' => '50%'];
			$navigationDorBorderRadiusActive = !empty($navigation_dot_border_radius_active[$device]) ? Utils::get_box_value($navigation_dot_border_radius_active[$device], 'border-radius') : ['border-radius' => '50%'];


			/**
			 * @description- one page scroll navigation dot tooltip control values
			 */
			$tooltipColor = !empty($tooltip_color) ? ['color' => Utils::get_color('color', $tooltip_color)] : ['color' => '#ffffff'];
			$tooltipColorHover = !empty($tooltip_color_hover) ? ['color' => Utils::get_color('color', $tooltip_color_hover)] : [];
			$tooltipBgColor = !empty($tooltip_bg_color) ? ['background-color' => Utils::get_color('color', $tooltip_bg_color)] : ['background-color' => '#00ff0d'];
			$tooltipBgColorHover = !empty($tooltip_bg_color_hover) ? ['background-color' => Utils::get_color('color', $tooltip_bg_color_hover)] : [];
			$tooltipPadding = !empty($tooltip_padding[$device]) ? Utils::get_box_value($tooltip_padding[$device], 'padding') : ['padding' => '4px 8px'];
			$tooltipPaddingHover = !empty($tooltip_padding_hover[$device]) ? Utils::get_box_value($tooltip_padding_hover[$device], 'padding') : [];
			$tooltipBorderRadius = !empty($tooltip_border_radius[$device]) ? Utils::get_box_value($tooltip_border_radius[$device], 'border-radius') : ['border-radius' => '4px'];
			$tooltipBorderRadiusHover = !empty($tooltip_border_radius_hover[$device]) ? Utils::get_box_value($tooltip_border_radius_hover[$device], 'border-radius') : [];
			$tooltipTextTypography = !empty($tooltip_typography) ? Utils::get_typography_value($tooltip_typography, $device) : ['font-size' => '12px'];

			$frontCss[strtolower($device)] = [];

			foreach ($selectors as $selector) {
				if ($selector === '.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-right') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						['top' => $navigation_vertical_position],
						['right' => $navigation_horizontal_position],
					);
				} 
				
				else if ($selector === '.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-right .gkit-one-page-scroll-dot-timeline:not(:last-child)::before') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						['height' => "calc($navigation_spacing + 1)"],
					);
				} 

				else if ($selector === '.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-left .gkit-one-page-scroll-dot-timeline:not(:last-child)::before') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						['height' => "calc($navigation_spacing + 1)"],
					);
				} 
				else if ($selector === '.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-top .gkit-one-page-scroll-dot-timeline:not(:last-child)::before') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						['height' => "calc($navigation_spacing + 1)"],
					);
				} 
				else if ($selector === '.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-bottom .gkit-one-page-scroll-dot-timeline:not(:last-child)::before') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						['height' => "calc($navigation_spacing + 1)"],
					);
				} 
				else if ($selector === '.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-left') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						['top' => $navigation_vertical_position],
						['left' => $navigation_horizontal_position], 
					);
				} else if ($selector === '.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-top') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						['top' => $navigation_vertical_position],
						['left' => $navigation_horizontal_position], 
					);
				} else if ($selector === '.gkit-one-page-scroll-dots.gkit-one-page-scroll-dots-bottom') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						['bottom' => $navigation_vertical_position],
						['left' => $navigation_horizontal_position], 
					);
				} 
				
				else if ($selector === '.gkit-one-page-scroll-dots') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						['gap' => $navigation_spacing],
						['--navigation-color' => Utils::get_color("color", $one_page_scroll_navigation_color)],
						['--navigation-color-active' => Utils::get_color("color", $one_page_scroll_navigation_color_active)],
						['--navigation-color-hover' => Utils::get_color("color", $one_page_scroll_navigation_color_hover)],
						['--navigation-tooltip-arrow-color' => Utils::get_color("color", $tooltipArrowColor)],
						['--navigation-tooltip-arrow-color-hover' => Utils::get_color("color", $tooltipArrowColorHover)],
					);
				} else if ($selector === 'li.gkit-one-page-scroll-dot:hover') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						$navigationDotWidthHover,
						$navigationDotHeightHover,
						$navigationDotBorderHover,
						$navigationDorBorderRadiusHover
					);
				} else if ($selector === 'li.gkit-one-page-scroll-dot.active:hover') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						$navigationDotWidthHover,
						$navigationDotHeightHover,
						$navigationDotBorderHover,
						$navigationDorBorderRadiusHover
					);
				} else if ($selector === 'li.gkit-one-page-scroll-dot.active') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						$navigationDotWidthActive,
						$navigationDotHeightActive,
						$navigationDotBorderActive,
						$navigationDorBorderRadiusActive,
					);
				} else if ($selector === '.gkit-one-page-scroll-tooltip') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						$tooltipColor,
						$tooltipBgColor,
						$tooltipPadding,
						$tooltipBorderRadius,
						$tooltipTextTypography
					);
				} else if ($selector === '.gkit-one-page-scroll-tooltip:hover') {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						$tooltipPaddingHover,
						$tooltipColorHover,
						$tooltipBgColorHover,
						$tooltipBorderRadiusHover,
					);
				} else {
					$frontCss[strtolower($device)][] = array_merge(
						["selector" => $selector],
						$navigationDotWidth,
						$navigationDotHeight,
						$navigationDotBorder,
						$navigationDorBorderRadius
					);
				}
			}
		}

		$parsed_css = Utils::parse_css($frontCss);
		$final_style = '';
		foreach ($deviceList as $device) {
			foreach ($parsed_css as $key => $block) {
				if ( !empty($block) && trim($block) !== '' ) {
					$direction = isset($device['direction']) ? $device['direction'] : 'max';
					$width = isset($device['value']) ? $device['value'] : '';
					$device_key = isset($device['slug']) ? strtolower($device['slug']) : '';
					if (isset($device['value']) && $device['value'] == 'base' && $key == 'desktop') {
						$final_style .= $block;
					} elseif (!empty($direction) && !empty($width) && $device_key == $key) {
						$final_style .= '@media (' . $direction . '-width: ' . $width . 'px) {' . trim($block) . '}';
					}
				}
			}
		}
		$css .= $final_style;

		return $css;
	}

	public function convertData($arr, $device, $prop) {
		$newData = [];
		if (isset($arr[$device])) {
			$newData = array(
				$prop => $arr[$device]["size"] . $arr[$device]["unit"],
			);
		}

		return $newData;
	}
}
