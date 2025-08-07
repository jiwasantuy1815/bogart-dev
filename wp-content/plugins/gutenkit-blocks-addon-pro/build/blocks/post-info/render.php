<?php
if (!defined('ABSPATH')) exit;

$block = $block;
$attributes = $attributes;
$post_id = !empty($block->context['postId']) ? $block->context['postId'] : false;
$post_type = !empty($block->context['postType']) ? $block->context['postType'] : false;
$post_info_items = isset($attributes['gkitPostInfoItems']) ? $attributes['gkitPostInfoItems'] : [];
$post = get_post($post_id);
$wrapper_extra_props = [];
if (!empty($attributes['showDivider'])) {
    $wrapper_extra_props['class'] = 'is-show-divider';
}

if ($post) {
    $author_id = $post->post_author;
    $author_name = get_the_author_meta('display_name', $author_id);
    $author_url = get_author_posts_url($author_id);
    $comments_link = get_comments_link($post_id);
    $comment_count = $post->comment_count;
    $post_day_link = get_day_link(get_post_time('Y', false, $post), get_post_time('m', false, $post), get_post_time('d', false, $post));
    $tag_list = get_the_tags($post_id);
    $category_list = get_the_category($post_id);

    $default_author_icon = [
        'src' => '<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 14 16\"><defs><path id=\"a\" d=\"M0 0h14v16H0z\"/></defs><g class=\"st0\"><path d=\"M7 8.8c2.3 0 4.2-1.9 4.2-4.2S9.3.3 7 .3 2.8 2.2 2.8 4.5 4.7 8.8 7 8.8zm0-7c1.5 0 2.7 1.2 2.7 2.7S8.5 7.2 7 7.2 4.3 6 4.3 4.5 5.5 1.8 7 1.8zm0 7.5C3.3 9.3.2 11.8.2 14.9c0 .4.3.8.8.8s.8-.3.8-.8c0-2.3 2.4-4.1 5.2-4.1 2.9 0 5.2 1.9 5.2 4.1 0 .4.3.8.8.8s.8-.3.8-.8c0-3-3.1-5.6-6.8-5.6z\" /></g></svg>'
    ];

    $default_date_icon = [
        'src' => '<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 14 16\"><defs><path d=\"M0 0h14v16H0z\"/></defs><g><path d=\"M11.7 1.6h-1.2V1c0-.4-.3-.8-.8-.8s-.8.4-.8.8v.6H5.1V1c0-.4-.3-.8-.8-.8s-.7.4-.7.8v.6H2.3C1.2 1.6.2 2.5.2 3.7V13c0 1.1.9 2.1 2.1 2.1h9.3c1.1 0 2.1-.9 2.1-2.1V3.7c.1-1.2-.9-2.1-2-2.1zM2.3 3.1h1.2v.6c0 .4.3.8.8.8s.8-.3.8-.8v-.6h3.8v.6c0 .4.3.8.8.8s.8-.3.8-.8v-.6h1.2c.3 0 .6.3.6.6v1.9H1.8V3.7c0-.4.2-.6.5-.6zm9.4 10.5H2.3c-.3 0-.6-.3-.6-.6V7.1h10.5V13c0 .3-.2.6-.5.6z\"/></g></svg>'
    ];
    $default_time_icon = [
        'src' => '<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 16 16\"><defs><path d=\"M0 0h16v16H0z\"/></defs><g><path d=\"M8 .2C3.7.2.2 3.7.2 8s3.5 7.8 7.8 7.8 7.8-3.5 7.8-7.8S12.3.2 8 .2zm0 14c-3.4 0-6.2-2.8-6.2-6.2 0-3.4 2.8-6.2 6.2-6.2 3.4 0 6.2 2.8 6.2 6.2 0 3.4-2.8 6.2-6.2 6.2zm3.1-5.5L8.8 7.5V3.8c0-.4-.3-.8-.8-.8s-.8.4-.8.8V8c0 .3.2.5.4.7l2.8 1.4c.1.1.2.1.3.1.3 0 .5-.2.7-.4.3-.4.1-.9-.3-1.1z\"/></g></svg>'
    ];

    $default_comment_icon = [
        'src' => '<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 16 16\"><defs><path d=\"M0 0h16v16H0z\"/></defs><g><path d=\"M15.8 6.8c0-3.6-3.1-6.5-6.9-6.5-3.7 0-7 2.9-7 6.7-1 .8-1.6 2-1.6 3.3 0 1.2.6 2.4 1.5 3.2l-.1 1c-.1.5.2.9.5 1.1.2.1.4.2.6.2s.4-.1.6-.2l1.7-1c1.2-.1 2.4-.5 3.2-1.6l.4.2 2.8 1.6c.2.1.5.2.7.2.3 0 .5-.1.8-.2.5-.3.7-.8.7-1.4l-.2-1.8c1.4-1.1 2.3-2.9 2.3-4.8zM4.9 13.1c-.1 0-.3 0-.4.1l-1.3.8.1-.7c0-.3-.1-.6-.3-.7-.8-.5-1.2-1.4-1.2-2.3 0-.9.5-1.8 1.3-2.3.5-.3 1.1-.5 1.8-.5C6.2 7.5 8 8.6 8 10.1c0 .3 0 .9-.1 1.2-.1.3-.3.6-.5.8-.7.6-1.6 1-2.5 1zm7.2-2.3c-.2.2-.3.4-.3.7l.3 2.2L9.2 12l-.1-.1c.1-.2.1-.3.2-.5s.2-.6.2-1c0-2.4-2.1-4.3-4.6-4.3-.6 0-.9 0-1.5.2.3-2.5 2.7-4.6 5.5-4.6 3 0 5.4 2.3 5.4 5-.1 1.7-.8 3.1-2.2 4.1z\"/></g></svg>'
    ];

    $default_term_icon = [
        'src' => '<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 16 16\"><defs><path d=\"M0 0h16v16H0z\"/></defs><g><path d=\"M3.8.2C1.8.2.3 1.8.3 3.7s1.6 3.6 3.5 3.6 3.6-1.6 3.6-3.6S5.8.2 3.8.2zm0 5.7c-1.1 0-2-.9-2-2.1s.9-2 2-2 2.1.9 2.1 2-1 2.1-2.1 2.1zm8.4 1.5c2 0 3.5-1.6 3.5-3.6S14.1.3 12.2.3 8.7 1.8 8.7 3.8s1.5 3.6 3.5 3.6zm0-5.6c1.1 0 2 .9 2 2s-.9 2.1-2 2.1-2-.9-2-2.1.9-2 2-2zM3.8 8.7c-2 0-3.5 1.6-3.5 3.5s1.6 3.5 3.5 3.5 3.6-1.6 3.6-3.5-1.6-3.5-3.6-3.5zm0 5.5c-1.1 0-2-.9-2-2s.9-2 2-2 2.1.9 2.1 2-1 2-2.1 2zm8.4-5.5c-2 0-3.5 1.6-3.5 3.5s1.6 3.5 3.5 3.5 3.5-1.6 3.5-3.5-1.5-3.5-3.5-3.5zm0 5.5c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z\"/></g></svg>'
    ];

    $default_custom_icon = [
        'src' => '<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 14 14\"><path d=\"M13.1 4.7c-.2-.2-.7-.1-.8.2-.9 1.4-2.3 1.6-2.7 1.6-2.8.2-3.9 1.9-4.2 2.5-.1.3 0 .6.2.7.3.1.6 0 .7-.3.4-.6 1-1.1 1.6-1.4l.8-.3v3c-.1.7-.4 1.2-.9 1.5-.5.4-1.3.5-2.2.5-1.1 0-1.9-.3-2.6-1-.6-.7-1-1.8-1-3.2V5.3c.1-1.3.4-2.2 1-2.8.6-.7 1.5-1.1 2.5-1.1.8 0 1.5.2 2.1.6.5.4.8.7.9 1 .1.3.2.8.7.8 1.5 0 .6-1.8-.4-2.7-1-.9-1.9-1-3.1-1-1.5 0-2.6.4-3.6 1.4C1.3 2.5.8 3.7.7 5.2v3.3c0 1.6.5 3 1.4 4 .9 1 2.1 1.4 3.6 1.4 1.2 0 2.3-.2 3.1-.8.8-.5 1.2-1.3 1.3-2.2V7.5l.8-.2c.7-.3 1.4-.7 2.1-1.6.2-.3.4-.7.1-1z\"/></svg>'
    ];
    if (!function_exists('getMetaDataMarkup')) {
        function getMetaDataMarkup($info_type, $meta_icon, $meta_text, $meta_link, $is_link = false, $before = '')
        {
            $wrapper = sprintf('<div class="gutenkit-post-info__metadata gutenkit-post-info__metadata-%s">', esc_attr($info_type));
            $wrapper_end = '</div>';
            $wrapper_link = sprintf('<a href="%s" class="gutenkit-post-info__metadata gutenkit-post-info__metadata-%s">', $meta_link, esc_attr($info_type));
            $wrapper_link_end = '</a>';
            $icon = sprintf('<span class="%s-icon author-icon-avatar post-info-meta-icon">%s</span>', esc_attr($info_type), $meta_icon);
            $text = sprintf('<span class="%s-name post-info-meta-name">%s%s</span>', $info_type, $before, $meta_text);
            $markup = sprintf('%s%s%s%s', $is_link ? $wrapper_link : $wrapper, $icon, $text, $is_link ? $wrapper_link_end : $wrapper_end);
            return $markup;
        }
    }

    if (!function_exists('get_icon')) {
        function get_icon($icon, $default_icon)
        {
            if (isset($icon["gkitPostInfoIconStyle"]) && $icon["gkitPostInfoIconStyle"] === 'Default') {
                return Gutenkit\Helpers\Utils::add_class_to_svg($default_icon["src"]);
            } else if (isset($icon["gkitPostInfoIconStyle"]) && $icon["gkitPostInfoIconStyle"] === 'Custom' && !empty($icon['gkitPostInfoIcon']['src'])) {
                return Gutenkit\Helpers\Utils::add_class_to_svg($icon['gkitPostInfoIcon']['src']);
            } else {
                return '';
            }
        }
    }
    $inner_content = '';
    foreach ($post_info_items as $item) :
        $settings = !empty($item['gkitPostInfoSettings']) ? $item['gkitPostInfoSettings'] : [];
        $icon = !empty($item['gkitPostInfoIconSettings']) ? $item['gkitPostInfoIconSettings'] : '';
        $date_format = !empty($settings['gkitPostInfoDateFormat']) && $settings['gkitPostInfoDateFormat'] != 'default' ? $settings['gkitPostInfoDateFormat'] : 'F j, Y';
        $time_format = !empty($settings['gkitPostInfoTimeFormat']) && $settings['gkitPostInfoTimeFormat'] != 'default' ? $settings['gkitPostInfoTimeFormat'] : 'g:i a';
        $post_date = get_the_date($date_format, $post);
        $post_time = get_the_time($time_format, $post);
        $term_meta = !empty($settings['gkitPostInfoTerms']) ? $settings['gkitPostInfoTerms'] : '';
        $term_count = !empty($settings['gkitPostInfoTermsCount']['size']) ? $settings['gkitPostInfoTermsCount']['size'] : 1;
        $is_link = !empty($settings['gkitPostInfoLink']) ? $settings['gkitPostInfoLink'] : false;
        $info_type = !empty($settings['gkitPostInfoType']) ? strtolower($settings['gkitPostInfoType']) : 'date';
        $before_text = !empty($item['gkitPostInfoBeforeText']) ? $item['gkitPostInfoBeforeText'] : '';
        $hasTerms = ($term_meta === "categories" && !empty($category_list) && count($category_list) > 0) || ($term_meta === "tags" && !empty($tag_list) && count($tag_list) > 0);
        if (isset($settings['gkitPostInfoType'])) {
            switch ($settings['gkitPostInfoType']) {
                case 'Author':
                    $author_icon = "";
                    if (isset($settings['gkitPostInfoShowAvatar']) && $settings['gkitPostInfoShowAvatar'] === true) {
                        $author_icon = get_avatar($author_id, !empty($settings['avatarSize']['size']) ? $settings['avatarSize']['size'] : 16);
                    } else {
                        $author_icon = get_icon($icon, $default_author_icon);
                    }
                    $inner_content .= getMetaDataMarkup($info_type, $author_icon, $author_name, $author_url, $is_link, $before_text);
                    break;
                case 'Date':
                    $date_icon = get_icon($icon, $default_date_icon);
                    $inner_content .= getMetaDataMarkup($info_type, $date_icon, $post_date, $post_day_link, $is_link, $before_text);
                    break;
                case 'Time':
                    $time_icon = get_icon($icon, $default_time_icon);
                    $inner_content .= getMetaDataMarkup($info_type, $time_icon, $post_time, $post_day_link, false, $before_text);
                    break;
                case 'Comments':
                    $comments_icon = get_icon($icon, $default_comment_icon);
                    $inner_content .= getMetaDataMarkup($info_type, $comments_icon, $comment_count, $comments_link, $is_link, $before_text);
                    break;
                case 'Terms':
                    if ($term_meta) {
                        $terms = [];
                        $term_meta = $term_meta === 'categories' ? 'category' : $term_meta;
                        $term_icon = get_icon($icon, $default_term_icon);
                        $term_list = get_the_terms($post_id, $term_meta);
                        $wrapper = sprintf('<div class="gutenkit-post-info__metadata gutenkit-post-info__metadata-%s">', esc_attr($info_type));
                        $wrapper_end = '</div>';
                        $icon = sprintf('<span class="%s-icon post-info-meta-icon">%s</span>', esc_attr($info_type), $term_icon);
                        if (!empty($term_list)) {
                            foreach ($term_list as $key => $term) {
                                if ($term) {
                                    $term_link = get_term_link($term);
                                    $term_name = $term->name;

                                    if ($term_count === $key) {
                                        break;
                                    }

                                    $terms[] = $is_link ? sprintf('<a href="%s" class="gutenkit-post-info__metadata-%s">%s</a>', $term_link, esc_attr($info_type), $term_name) : sprintf('<span class="gutenkit-post-info__metadata-%s">%s</span>', esc_attr($info_type), $term_name);
                                }
                            }
                        }

                        if (!empty($terms)) {
                            $inner_content .= $wrapper . $icon . $before_text . join(',', $terms) . $wrapper_end;
                        }
                    }
                    break;
                case 'Custom':
                    $custom_icon = get_icon($icon, $default_custom_icon);
                    $custom_text = !empty($settings['gkitPostInfoCustomText']) ? $settings['gkitPostInfoCustomText'] : '';
                    $custom_link = !empty($settings['gkitPostInfoCustomURL']['url']) ? $settings['gkitPostInfoCustomURL']['url'] : '';
                    $inner_content .= getMetaDataMarkup($info_type, $custom_icon, $custom_text, $custom_link, $is_link, $before_text);
                    break;
            }
        }
    endforeach;
?>

    <?php if (!empty($inner_content)) : ?>
        <div <?php echo wp_kses_post(Gutenkit\Helpers\Utils::get_dynamic_block_wrapper_attributes($block, $wrapper_extra_props)) ?>>
            <?php echo wp_kses($inner_content, Gutenkit\Helpers\Utils::post_kses_extend_allowed_html()); ?>
        </div>
    <?php endif; ?>
<?php
}
?>