<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

$attributes = $attributes;
$block_id = $attributes['blockID'];
$block = $block;
$query_excerpt_fallback = !empty($attributes['QueryExcerptFallback']) ? $attributes['QueryExcerptFallback'] : '';
$query_excerpt_length = !empty($attributes['QueryExcerptLength']) ? $attributes['QueryExcerptLength'] : false;
$post_id = isset($block->context['postId']) ? $block->context['postId'] : '';
$excerpt = !empty($query_excerpt_length) ? substr(get_the_excerpt( $post_id ), 0, $query_excerpt_length) : get_the_excerpt( $post_id );
$content = !empty($query_excerpt_length) ? substr(wp_strip_all_tags(get_the_content( $post_id )), 0, $query_excerpt_length) : wp_strip_all_tags(get_the_content( $post_id ));
$excerpt = !empty(trim($excerpt)) ? $excerpt : (!empty($content) ? $content : null);
?>

<div <?php echo wp_kses_post(Gutenkit\Helpers\Utils::get_dynamic_block_wrapper_attributes($block)) ?>>
    <p class="gkit-query-post-excerpt">
        <?php echo esc_html(!empty($excerpt) && strlen($excerpt) <= $query_excerpt_length ? $excerpt . "..." : (!empty($excerpt) ? $excerpt : $query_excerpt_fallback)); ?>
    </p>
</div>