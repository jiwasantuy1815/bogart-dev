<?php
/**
 * Server-side rendering of the `featured-image` block.
*/

if (!isset($block->context['postId'])) {
	return '';
}
$post_ID = $block->context['postId'];

$is_link        = isset($attributes['isLink']) && $attributes['isLink'];
$link_data 		= '';
$size_slug      = isset($attributes['sizeSlug']) ? $attributes['sizeSlug'] : 'post-thumbnail';
$attr           = array();
$overlay_markup = $attributes['isOverlay'] ? '<span class="image-overlay" aria-hidden="true"></span>' : '';
$caption 		= $attributes['captionSource'] === "attachment" ? get_the_post_thumbnail_caption($post_ID) : ($attributes['captionSource'] === "custom" ? $attributes['customCaption'] : '');
$wrapper_class  = 'gkit-featured-image-wrapper';

if ($is_link) {
	if (get_the_title($post_ID)) {
		$attr['alt'] = trim(strip_tags(get_the_title($post_ID)));
	} else {
		// Translators: %d is the post ID.
		$attr['alt'] = sprintf(__('Untitled post %d', 'gutenkit-blocks-addon-pro'), $post_ID);
	}
}

$featured_image = get_the_post_thumbnail($post_ID, $size_slug, $attr);
$featured_image_url = get_the_post_thumbnail_url($post_ID, $size_slug);

// Get the first image from the post.
if ($attributes['useFirstImageFromPost'] && !$featured_image) {
	$content_post = get_post($post_ID);
	$content      = $content_post->post_content;
	$processor    = new WP_HTML_Tag_Processor($content);

	//Transfer the image tag from the post into a new text snippet.
	if ($processor->next_tag('img')) {
		$tag_html = new WP_HTML_Tag_Processor('<img>');
		$tag_html->next_tag();
		foreach ($processor->get_attribute_names_with_prefix('') as $name) {
			$tag_html->set_attribute($name, $processor->get_attribute($name));
		}
		$featured_image = $tag_html->get_updated_html();
	}
}

if (!$featured_image) {
	return '';
}

if(!function_exists("add_permalink")) {
	function add_permalink($id, $newTab, $noFollow) {
		$attr = array();
		$attr['url'] = get_the_permalink($id);
		$attr['newTab'] = $newTab;
		$attr['noFollow'] = $noFollow;
		return $attr;
	}
}

switch ($attributes['linkType']) {
	case 'none':
		$featured_image = "<div class=$wrapper_class>$featured_image $overlay_markup</div>";
		break;
	case 'file':
		if($attributes['isLightbox']) {
			$featured_image = "<a class=$wrapper_class href=$featured_image_url data-fancybox data-caption=$caption>$featured_image $overlay_markup</a>";
		} else {
			$featured_image = "<a class=$wrapper_class href=$featured_image_url>$featured_image $overlay_markup</a>";
		}
		break;
	case 'post':
		$link_data = add_permalink($post_ID, $attributes['postNewTab'], $attributes['postNoFollow']);
		
		$link_attributes = Gutenkit\Helpers\Utils::get_link_attributes($link_data);

		$featured_image = "<a class=$wrapper_class $link_attributes>$featured_image $overlay_markup</a>";
		break;
	case 'custom':
		$link_attributes = Gutenkit\Helpers\Utils::get_link_attributes($attributes['customURL']);

		$featured_image = "<a class=$wrapper_class $link_attributes>$featured_image $overlay_markup</a>";
		break;
	default:
		break;

}

?>
<figure <?php echo wp_kses_post(Gutenkit\Helpers\Utils::get_dynamic_block_wrapper_attributes($block)) ?>>
	<?php echo $featured_image; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
	<?php if (!empty($caption)) : ?>
		<figcaption class="gkit-featured-image-caption"><?php echo esc_attr($caption); ?></figcaption>
	<?php endif; ?>
</figure>
