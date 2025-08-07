<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
$attributes = $attributes;
$block_id = $attributes['blockID'];
$block = $block;
$align_class = isset($attributes['align']) ? 'align' . $attributes['align'] : '';
$TagName = !empty($attributes['gkitQueryPostTitleTag']) ? $attributes['gkitQueryPostTitleTag'] : '';
$link_condition = !empty($attributes['gkitQueryPostLink']) ? $attributes['gkitQueryPostLink'] : '';
$title_before = !empty($attributes['gkitQueryPostTitleBefore']) ? $attributes['gkitQueryPostTitleBefore'] : '';
$title_after = !empty($attributes['gkitQueryPostTitleAfter']) ? $attributes['gkitQueryPostTitleAfter'] : '';
$fallback = !empty($attributes['gkitQueryPostTitleFallback']) ? $attributes['gkitQueryPostTitleFallback'] : '';
$query_length = !empty($attributes['QueryPostTitleLength']) ? $attributes['QueryPostTitleLength'] : 0;
$post_id = isset($block->context['postId']) ? $block->context['postId'] : '';

$target = '';
$rel = '';
if ($link_condition === "custom") {
    $link = !empty($attributes['gkitQueryCustomPostTitleLink']['url']) ? $attributes['gkitQueryCustomPostTitleLink']['url'] : '';
    $target = !empty($attributes['gkitQueryCustomPostTitleLink']['newTab']) ? 'target="_blank"'  : '';
    $rel = !empty($attributes['gkitQueryCustomPostTitleLink']['noFollow']) ? 'rel="nofollow"'  : '';
} else if ($link_condition === "post") {
    $link = get_the_permalink($post_id);
}

$link_attribute = !empty($link) ? 'href="' . esc_url($link) . '"' : '';
$WrapperTag = !empty($link) ? 'a' : 'div';
$full_title = get_the_title($post_id);
$title = (!empty($query_length) && strlen($full_title) > $query_length) 
    ? substr($full_title, 0, $query_length) . "..." 
    : $full_title;
$post_title = !empty(trim($title)) ? sprintf('%s%s%s', $title_before, $title, $title_after) : null;
?>

<<?php echo esc_attr($WrapperTag) . ' ' . $link_attribute . ' ' . esc_attr($target) . ' ' . esc_attr($rel) . ' ' . wp_kses_post(Gutenkit\Helpers\Utils::get_dynamic_block_wrapper_attributes($block)); ?>>
    <<?php echo esc_attr($TagName); ?> class="gkit-query-post-title <?php echo esc_attr($align_class); ?>">
        <?php echo esc_html(!empty($post_title) ? $post_title : $fallback); ?>
    </<?php echo esc_attr($TagName); ?>>
</<?php echo esc_attr($WrapperTag); ?>>
