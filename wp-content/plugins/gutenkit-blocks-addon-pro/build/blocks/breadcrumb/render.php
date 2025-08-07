<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
$attributes                = $attributes;
$content                   = $content;
$block                     = $block;
$block_id                  = $attributes['blockID'];
$align_class               = isset($attributes['align']) ? 'align' . $attributes['align'] : '';
$trail                     = $attributes['gkitIsBreadcrumbShowTrail'];
$max_len                   = isset($attributes['gkitBreadcrumbLabelWordMaxLength']) ? intval($attributes['gkitBreadcrumbLabelWordMaxLength']) : 100;
$post_id                   = get_the_ID();
$show_separator_icon       = isset($attributes['gkitIsShowBreadcrumbSeparateIcon']) ? $attributes['gkitIsShowBreadcrumbSeparateIcon'] : false;
$separator_icon            = isset($attributes['gkitBreadcrumbSeparateIcon']['src']) ? $attributes['gkitBreadcrumbSeparateIcon']['src'] : '';
$home_icon                 = isset($attributes['gkitBreadcrumbHomeIcon']['src']) ? $attributes['gkitBreadcrumbHomeIcon']['src'] : '';
$home_label                = !empty($attributes['gkitBreadcrumbHomeLabel']) ? $attributes['gkitBreadcrumbHomeLabel'] : 'Home';
$error_label               = !empty($attributes['gkitBreadcrumb404Label']) ? $attributes['gkitBreadcrumb404Label'] : '404 Not Found';
$search_label_prefix       = !empty($attributes['gkitBreadcrumbSearchPageLabelPrefix']) ? $attributes['gkitBreadcrumbSearchPageLabelPrefix'] : '';
$search_label              = !empty($attributes['gkitBreadcrumbSearchPageLabel']) ? $attributes['gkitBreadcrumbSearchPageLabel'] : ' "'. get_search_query(true) . '"';
$author_label_prefix       = !empty($attributes['gkitBreadcrumbAuthorPageLabelPrefix']) ? $attributes['gkitBreadcrumbAuthorPageLabelPrefix'] : '';
$author_label              = !empty($attributes['gkitBreadcrumbAuthorPageLabel']) ? $attributes['gkitBreadcrumbAuthorPageLabel'] : get_the_author();
$year_label_prefix         = !empty($attributes['gkitBreadcrumbArchiveYearLabelPrefix']) ? $attributes['gkitBreadcrumbArchiveYearLabelPrefix'] : '';
$year_label                = !empty($attributes['gkitBreadcrumbArchiveYearLabel']) ? $attributes['gkitBreadcrumbArchiveYearLabel'] : get_the_time('Y', $post_id);
$month_label_prefix        = !empty($attributes['gkitBreadcrumbArchiveMonthLabelPrefix']) ? $attributes['gkitBreadcrumbArchiveMonthLabelPrefix'] : '';
$month_label               = !empty($attributes['gkitBreadcrumbArchiveMonthLabel']) ? $attributes['gkitBreadcrumbArchiveMonthLabel'] : get_the_time('F, Y', $post_id);
$day_label_prefix          = !empty($attributes['gkitBreadcrumbArchiveDayLabelPrefix']) ? $attributes['gkitBreadcrumbArchiveDayLabelPrefix'] : '';
$day_label                 = !empty($attributes['gkitBreadcrumbArchiveDayLabel']) ? $attributes['gkitBreadcrumbArchiveDayLabel'] : get_the_time('F j, Y', $post_id);
$category_label_prefix     = !empty($attributes['gkitBreadcrumbArchiveCategoryLabelPrefix']) ? $attributes['gkitBreadcrumbArchiveCategoryLabelPrefix'] : '';
$category_label            = !empty($attributes['gkitBreadcrumbArchiveCategoryLabel']) ? $attributes['gkitBreadcrumbArchiveCategoryLabel'] : "";
$tag_label_prefix          = !empty($attributes['gkitBreadcrumbArchiveTagLabelPrefix']) ? $attributes['gkitBreadcrumbArchiveTagLabelPrefix'] : '';
$tag_label                 = !empty($attributes['gkitBreadcrumbArchiveTagLabel']) ? $attributes['gkitBreadcrumbArchiveTagLabel'] : single_tag_title('', false);
$blog_label                = !empty($attributes['gkitBreadcrumbBlogPageLabel']) ? $attributes['gkitBreadcrumbBlogPageLabel'] : "Blogs";

$markup = '';
$separator = $show_separator_icon && !empty($separator_icon) ? '<li class="gutenkit-breadcrumb-separator">' . Gutenkit\Helpers\Utils::add_class_to_svg($separator_icon) . '</li>' : '';

if(!function_exists('gutenkit_breadcrumb_item_markup')){
    function gutenkit_breadcrumb_item_markup($value, $prefix = '', $suffix = '', $is_current = true, $url = '')
    {
        if(!$is_current){
            return sprintf(
                '<li class="gutenkit-breadcrumb-item"><a href="%1$s"><span class="gutenkit-breadcrumb-label ">%2$s %3$s %4$s</span></a></li>',
                $url,
                $prefix,
                $value,
                $suffix
            );
        }

        return sprintf(
            '<li class="gutenkit-breadcrumb-item %1$s"><span class="gutenkit-breadcrumb-label">%2$s %3$s %4$s</span></li>',
            $is_current ? 'is-current' : '',
            $prefix,
            $value,
            $suffix
        );
    }

}

if (!function_exists("gutenkit_render_parent_posts")) {
    function gutenkit_render_parent_posts($post_id, $max_len, $separator)
    {
        $page_items = [];
        $page_items[] = sprintf(
            '%1$s <li class="gutenkit-breadcrumb-item is-current"><span class="gutenkit-breadcrumb-label">%2$s</span></li>',
            $separator,
            (is_home() && get_option('page_for_posts')) ? mb_substr(get_the_title(get_option('page_for_posts')), 0, $max_len) : mb_substr(get_the_title(), 0, $max_len)

        );
        $post = get_post($post_id);
        while ($post->post_parent) {
            $page_items[] = $separator . '<li class="gutenkit-breadcrumb-item"><a href="' . get_permalink($post->post_parent) . '" title="' . get_the_title($post->post_parent) . '"><span class="gutenkit-breadcrumb-label">' . get_the_title($post->post_parent) . '</span></a></li>';
            $post = get_post($post->post_parent);
        }
        $page_items = array_reverse($page_items);
        $page_list = implode('', $page_items);
        return $page_list;
    }
}

if (!function_exists("gutenkit_render_parent_category")) {
    function gutenkit_render_parent_category($category, $max_len, $separator, $parents = [])
    {
        if (!empty($category->parent)) {
            $parent_category = get_category($category->parent);

            if ($parent_category) {
                $parents[] = $separator . '<li class="gutenkit-breadcrumb-item"><a href="' . esc_url(get_term_link($parent_category)) . '"><span class="gutenkit-breadcrumb-label">' . esc_html($parent_category->name) . '</span></a></li>';
                $parents = gutenkit_render_parent_category($parent_category, $max_len, $separator, $parents);
            }
        }

        return array_reverse($parents);
    }
}

$condition = [];
is_home() && $condition[] = 'home';
is_front_page() && $condition[] = 'frontpage';
if (empty($condition)) {
    if (is_single() || is_category()) {

        $category = is_category() ? [get_category(get_query_var('cat'))] : get_the_category();
        if (!empty($category)) {
            if ( $trail ){
                $markup .= join('', gutenkit_render_parent_category($category[0], $max_len, $separator));
            }
            if(is_category()){
                $markup .= $separator . gutenkit_breadcrumb_item_markup($category_label ? $category_label : $category[0]->name, $category_label_prefix);
            }else{
                $markup .= $separator . gutenkit_breadcrumb_item_markup($category[0]->cat_name, '', '', false, get_category_link($category[0]->term_id));
            }
        } else {

            $p_type    = get_post_type($post_id);
            $post_type = get_post_type_object($p_type);

            if (!empty($post_type->labels->singular_name) && !in_array($post_type->name, ['post', 'page'])) {

                $markup .= $separator . gutenkit_breadcrumb_item_markup($post_type->labels->singular_name, '', '', false, get_post_type_archive_link($p_type));
            }
        }

        if (is_single()) {
            $markup .= gutenkit_render_parent_posts($post_id, $max_len, $separator);
        }
    } elseif (is_page()) {
        $markup .= gutenkit_render_parent_posts($post_id, $max_len, $separator);
    } elseif (is_category()) {
        $markup .= gutenkit_render_parent_posts($post_id, $max_len, $separator);
    }
} elseif (in_array('home', $condition) && !in_array('frontpage', $condition)) {
    $page_for_posts = get_option('page_for_posts');
    $markup .= $separator . gutenkit_breadcrumb_item_markup(esc_html__($blog_label, 'gutenkit-blocks-addon-pro'));
}elseif (is_front_page()) {
    $markup = "";
}

if (is_tag()) {
    $markup .= $separator . gutenkit_breadcrumb_item_markup($tag_label, $tag_label_prefix);
} elseif (is_day()) {
    $markup .= $separator . gutenkit_breadcrumb_item_markup($day_label, $day_label_prefix);
} elseif (is_month()) {
    $markup .= $separator . gutenkit_breadcrumb_item_markup($month_label, $month_label_prefix);
} elseif (is_year()) {
    $markup .= $separator . gutenkit_breadcrumb_item_markup($year_label, $year_label_prefix);
} elseif (is_author()) {
    $markup .= $separator . gutenkit_breadcrumb_item_markup($author_label, $author_label_prefix);
} elseif (is_search()) {
    $markup .= $separator . gutenkit_breadcrumb_item_markup($search_label, $search_label_prefix);
} elseif (is_404()) {
    $markup .= $separator . gutenkit_breadcrumb_item_markup($error_label);
}
?>

<div id="block-<?php echo esc_attr($block_id); ?>" role="document" aria-label="Block: Breadcrumb" data-block="<?php echo esc_attr($block_id); ?>" data-type="gutenkit-pro/breadcrumb" data-title="Breadcrumb" class="wp-block-gutenkit-pro-breadcrumb <?php echo esc_attr($align_class); ?>">
    <ul class="gutenkit-breadcrumb">
        <li class="gutenkit-breadcrumb-item is-home">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <?php if (!empty($home_icon)) : ?>
                    <span class="gutenkit-breadcrumb-home-icon">
                        <?php echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($home_icon), Gutenkit\Helpers\Utils::svg_allowed_html()); ?>
                    </span>
                <?php endif; ?>
                <span class="gutenkit-breadcrumb-label"><?php esc_html_e($home_label, 'gutenkit-blocks-addon-pro') ?></span>
            </a>
        </li>
        <?php echo wp_kses($markup, Gutenkit\Helpers\Utils::post_kses_extend_allowed_html()); ?>
    </ul>
</div>
