<?php
$block = $block;
$attributes = $attributes;
$block_class = !empty($attributes['blockClass']) ? $attributes['blockClass'] : '';
$content = $content;
$query_data = !empty($block->context['query']) ? $block->context['query'] : [];
$inherit = !empty($query_data['inherit']) ? true : false;
$posts_query = !empty($query_data['posts']) ? json_encode($query_data['posts']) : [];
$mid_size = !empty($attributes['truncatePaginationNumbers']) && $attributes['PaginationNumberAmountBothSides'] ? $attributes['PaginationNumberAmountBothSides']['size'] : false;
$pagination_type = !empty($attributes['paginationType']['value']) ? $attributes['paginationType']['value'] : 'number';
$next_prev_type = !empty($attributes['paginationNextPrevType']['value']) ? $attributes['paginationNextPrevType']['value'] : 'text';
$next_text = !empty($attributes['paginationNextText']) ? $attributes['paginationNextText'] : '';
$prev_text = !empty($attributes['paginationPreviousText']) ? $attributes['paginationPreviousText'] : '';
$prev_icon = !empty($attributes['prevIcon']['src']) ? $attributes['prevIcon']['src'] : '';
$next_icon = !empty($attributes['nextIcon']['src']) ? $attributes['nextIcon']['src'] : '';

// Get the custom 'paginationPage' value
$paginationPage = isset($query_data['posts']['paginationPage']) ? (int) $query_data['posts']['paginationPage'] : 1;

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

$pagination_content = '';

// Ensure $query is an instance of WP_Query and has max_num_pages property
if ($query instanceof WP_Query && isset($query->max_num_pages)) {
	$big = 999999999;

	// Check if 'paged' exists, otherwise fallback to 'paginationPage'
	$paged = (get_query_var('paged')) ? get_query_var('paged') : (isset($_GET['gkit-query-page']) ? $_GET['gkit-query-page'] : 1);

	// If 'paginationPage' is set and 'paged' is still on page 1, override the initial paged value
	if ($paginationPage > 1 && $paged == 1) {
		$paged = $paginationPage;
	}

	$args = array(
		'current' => max(1, $paged), // Use the adjusted $paged value
		'total' => $query->max_num_pages,
		'show_all' => isset($attributes['truncatePaginationNumbers']) ? !$attributes['truncatePaginationNumbers'] : false,
		'mid_size' => $mid_size ? $mid_size : 2,
	);

	// Check if this is a single post.
	if (is_single()) {
		$args['base'] = str_replace($big, '%#%', esc_url(add_query_arg('gkit-query-page', $big)));
		$args['format'] = '?gkit-query-page=%#%';
	} else {
		$args['base'] = str_replace($big, '%#%', esc_url(get_pagenum_link($big)));
		$args['format'] = '?paged=%#%'; 
	}

	if (!function_exists('prev_next_items')) {
		function prev_next_items($type = 'text', $prev_text = 'Previous', $next_text = 'Next', $prev_icon = '', $next_icon = '')
		{
			$prev_next_args = [];
			switch ($type) {
				case 'text':
					$prev_next_args['prev_text'] = $prev_text;
					$prev_next_args['next_text'] = $next_text;
					break;
				case 'icon':
					$prev_next_args['prev_text'] = !empty($prev_icon) ? sprintf('<div class="gkit-icons">%s</div>', wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($prev_icon), Gutenkit\Helpers\Utils::svg_allowed_html())) : '';
					$prev_next_args['next_text'] = !empty($next_icon) ? sprintf('<div class="gkit-icons">%s</div>', wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($next_icon), Gutenkit\Helpers\Utils::svg_allowed_html())) : '';
					break;
				case 'icon-text':
					$prev_next_args['prev_text'] = !empty($prev_icon) ? sprintf('<div class="gkit-icons">%s</div>%s', wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($prev_icon), Gutenkit\Helpers\Utils::svg_allowed_html()), $prev_text) : '';
					$prev_next_args['next_text'] = !empty($next_icon) ? sprintf('%s<div class="gkit-icons">%s</div>', $next_text, wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($next_icon), Gutenkit\Helpers\Utils::svg_allowed_html())) : '';
					break;
			}
			return $prev_next_args;
		}
	}

	switch ($pagination_type) {
		case 'number':
			$args['prev_next'] = false;
			break;
		case 'previous-next':
			$args['prev_next'] = true;
			$args = array_merge($args, prev_next_items($next_prev_type, $prev_text, $next_text, $prev_icon, $next_icon));
			break;
		case 'number-previous-next':
			$args['prev_next'] = true;
			$args = array_merge($args, prev_next_items($next_prev_type, $prev_text, $next_text, $prev_icon, $next_icon));
			break;
		default:
			$args['prev_next'] = false;
			break;
	}

	$pagination_content = paginate_links($args) ?: '';
	if ($pagination_type === 'previous-next') {
		$filter_link_attributes_next = static function () {
			return "class='next page-numbers'";
		};
		$filter_link_attributes_prev = static function () {
			return "class='prev page-numbers'";
		};
		add_filter( 'next_posts_link_attributes', $filter_link_attributes_next );
		add_filter( 'previous_posts_link_attributes', $filter_link_attributes_prev );
		$pagination_content = get_previous_posts_link( $args['prev_text'], $args['total'] ) . get_next_posts_link( $args['next_text'], $args['total'] );
		remove_filter( 'next_posts_link_attributes', $filter_link_attributes_next );
		remove_filter( 'previous_posts_link_attributes', $filter_link_attributes_prev );
	}
}

wp_reset_postdata();
?>

<div <?php echo wp_kses_post(Gutenkit\Helpers\Utils::get_dynamic_block_wrapper_attributes($block)) ?>>
	<?php echo wp_kses($pagination_content, Gutenkit\Helpers\Utils::post_kses_extend_allowed_html()); ?>
</div>
