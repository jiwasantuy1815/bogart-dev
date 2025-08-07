<?php
if (!defined('ABSPATH')) exit;

$block = $block;
$attributes = $attributes;
$content = $content;
$block_class = !empty($attributes['blockClass']) ? $attributes['blockClass'] : '';
$query_data = !empty($block->context['query']) ? $block->context['query'] : [];
$inherit = !empty($query_data['inherit']) ? true : false;
$posts_query = !empty($query_data['posts']) ? json_encode($query_data['posts']) : [];
$is_slider_enable = isset($attributes['gkitQueryBuilderLayoutType']) && $attributes['gkitQueryBuilderLayoutType'] == 'slider' ? true : false;
$swiper_settings = !empty($attributes['swiperSettings']) ? json_encode($attributes['swiperSettings']) : [];
$is_swiper_dots_enable = isset($attributes['enableDots']) ? $attributes['enableDots'] : false;
$is_swiper_arrows_enable = isset($attributes['enableArrows']) ? $attributes['enableArrows'] : false;
$left_arrow_icon = !empty($attributes['leftArrowIcon']) ? $attributes['leftArrowIcon'] : [];
$right_arrow_icon = !empty($attributes['rightArrowIcon']) ? $attributes['rightArrowIcon'] : [];
$wrapper_props = [];

if ($is_slider_enable) {
	$wrapper_props['class'] = "swiper {$block_class}";
	$wrapper_props['data-settings'] = $swiper_settings;
}
$query_content = '';


if ($inherit) {
	global $wp_query;
	
	if (in_the_loop()) {
		$query = clone $wp_query;
		$query->rewind_posts();
	} else {
		$query = $wp_query;
	}
} else {
	$query = \GutenkitPro\Core\BuildQuery::instance()->posts_query(array('query' => $posts_query));
}

if ($query->have_posts()) {
	while ($query->have_posts()) {
		$query->the_post();
		// Get an instance of the current Post Template block.
		$block_instance = $block->parsed_block;

		$block_instance['blockName'] = 'core/null';

		$post_id              = get_the_ID();
		$post_type            = get_post_type();

		$filter_block_context = static function ($context) use ($post_id, $post_type) {
			$context['postType'] = $post_type;
			$context['postId']   = $post_id;
			return $context;
		};

		// Use an early priority to so that other 'render_block_context' filters have access to the values.
		add_filter('render_block_context', $filter_block_context, 1);
		// Render the inner blocks of the Post Template block with `dynamic` set to `false` to prevent calling
		// `render_callback` and ensure that no wrapper markup is included.
		$block_content = (new WP_Block($block_instance))->render(array('dynamic' => false));
		remove_filter('render_block_context', $filter_block_context, 1);
		$post_classes = implode( ' ', get_post_class( 'gutenkit-query-template-item' ) );
		$query_content .= sprintf('%s<div class="%s">%s</div>%s', $is_slider_enable ? '<div class="swiper-slide">' : '', $post_classes, $block_content, $is_slider_enable ? '</div>' : '');
	}

	wp_reset_postdata();

	$swiper_pagination = $is_swiper_dots_enable ? '<div class="swiper-pagination"></div>' : '';
	$swiper_navigation = $is_swiper_arrows_enable ? '<div class="swiper-button swiper-button-prev">' . wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($left_arrow_icon['src']), Gutenkit\Helpers\Utils::post_kses_extend_allowed_html()) . '</div><div class="swiper-button swiper-button-next">' . wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($right_arrow_icon['src']), Gutenkit\Helpers\Utils::post_kses_extend_allowed_html()) . '</div>' : '';

	if ($is_slider_enable) {
		printf(
			'<div %1s>%2s%3s%4s%5s%6s</div>',
			wp_kses_post(Gutenkit\Helpers\Utils::get_dynamic_block_wrapper_attributes($block, $wrapper_props)),
			'<div class="swiper-wrapper">',
			$query_content,
			'</div>',
			$swiper_pagination,
			$swiper_navigation
		);
	} else {
		printf(
			'<div %1s>%2s</div>',
			wp_kses_post(Gutenkit\Helpers\Utils::get_dynamic_block_wrapper_attributes($block)),
			$query_content,
		);
	}
}
