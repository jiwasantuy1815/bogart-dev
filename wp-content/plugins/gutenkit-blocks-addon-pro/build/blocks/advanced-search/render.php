<?php
if (!defined('ABSPATH')) exit;

// Set attributes
$attributes = $attributes;
$content = $content;
$block = $block;
$block_id = $attributes['blockID'];
$searchBoxStyle = $attributes['searchBoxStyle'];
$showSearchBar = $attributes['showSearchBar'];
$showOverlay = ($searchBoxStyle === "popup-style" && $attributes['showOverlay']) ? "show" : "";
$taxonomy = $attributes['selectTexonomies'];
$placeHolder = $attributes['gkitSearchPlaceHolder'];

$options = (object) array(
    'paginationPage' => $attributes['showMoreItem'],
    'postType' => isset($attributes['searchQuery']) ? $attributes['searchQuery'] : '',
    'postsPerPage' => $attributes['gkitResultPerPage'] * $attributes['showMoreItem'],
    'taxonomy' => $attributes['selectTexonomies'],
    'terms' => $attributes['selectedCategoryItem'],
    'showSearchResult' => $attributes['gkitEnableSearchResult'],
);


if (isset($taxonomy) && !empty($taxonomy)) {
    $terms = get_terms(array('taxonomy' => $taxonomy['slug'], 'hide_empty' => false));
    $all_taxonomies = (object) array(
        'term_id' => 0,
        'name' => 'All ' . $taxonomy['name'],
        'slug' => '',
        'term_group' => 0,
        'term_taxonomy_id' => 0,
        'taxonomy' => '',
        'description' => '',
        'parent' => 0,
        'count' => 1,
        'filter' => 'raw'
    );
    array_unshift($terms, $all_taxonomies);
};

wp_localize_script('gutenkit-pro-advanced-search-view-script', 'advancedSearchData', ['block' => json_encode($block), 'url' => rest_url('gutenkit/v1/render-block'), 'nonce' => wp_create_nonce('wp_rest')]);
?>

<!-- Markup for popup-style and group-style -->
<div <?php echo wp_kses_post(Gutenkit\Helpers\Utils::get_dynamic_block_wrapper_attributes($block)) ?> data-settings="<?php echo esc_attr(json_encode($options)); ?>">
    <?php if (in_array($searchBoxStyle, ['popup-style', 'group-style'])): ?>
        <?php if ($searchBoxStyle === 'popup-style'): ?>
            <div class="gkit-initial-overlay-container">
                <div class="gkit-searchbar-overlay"></div>
                <div class="gkit-initial-overlay-style">
                    <span class="gkit-search-icon">
                        <?php if (!empty($attributes['gkitSearchIcon']['src'])) :
                            echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['gkitSearchIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                        endif; ?>
                    </span>
                    <input type="text" name="s" class="gkit-overlay-search-field" placeholder='<?php echo esc_attr($placeHolder); ?>' />
                </div>
            </div>
        <?php endif; ?>
        <div class="gkit-search-container-wrapper">
            <div class="gkit-search-container <?php echo esc_attr($searchBoxStyle . ' ' . $showOverlay); ?>">
                <form role="search" method="get" class="gkit-search-group" action="<?php echo esc_url(home_url('/')); ?>" target="_blank">
                    <?php if ($attributes['gkitShowSearchCategory']): ?>
                        <!-- SearchCategory Component -->
                        <div class="gkit-select-category-menu">
                            <div class="gkit-select-category-btn">
                                <span class="gkit-select-category-content"><?php echo esc_html('All ' . $taxonomy['name']); ?></span>
                                <span class="gkit-select-category-icon">
                                    <?php if (!empty($attributes['gkitCategoryDropdownIcon']['src'])) :
                                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['gkitCategoryDropdownIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                                    endif; ?>
                                </span>
                            </div>
                            <div class='gkit-search-category-options-wrapper'>
                                <ul class="gkit-search-category-options">
                                    <?php foreach ($terms as $term) : ?>
                                        <li class="gkit-search-category-option" data-settings="<?php echo esc_attr(json_encode($term->slug)); ?>">
                                            <span>
                                                <?php if (isset($term->name) && !empty($term->name)) : echo esc_html($term->name);
                                                endif; ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="gkit-search-box">
                        <div class="gkit-search-input" tab-index="0">
                            <span class="gkit-search-icon">
                                <?php if (isset($attributes['gkitShowInputIcon']) && $attributes['gkitShowInputIcon'] == true) :
                                    if (!empty($attributes['gkitInputIcon']['src'])) :
                                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['gkitInputIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                                    endif;
                                endif; ?>
                            </span>
                            <input type="text" name="s" class="gkit_search-field" placeholder='<?php echo esc_attr($placeHolder); ?>' />
                            <button type="submit" class="gkit-clear-search-button">
                                <span class="gkit-search-icon">
                                    <?php if (!empty($attributes['gkitCloseIcon']['src'])) :
                                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['gkitCloseIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                                    endif; ?>
                                </span>
                            </button>
                            <span class="gkit-clear-search-spinner"></span> <!-- Spinner placeholder -->
                        </div>
                    </div>
                    <?php if ($attributes['gkitShowSearchButton']): ?>
                        <!-- SearchButton Component -->
                        <button type="submit" aria-label="search-button" class="gkit_search-button">
                            <span class="gkit-search-icon">
                                <?php if (isset($attributes['gkitBtnAppearance']) && $attributes['gkitBtnAppearance'] !== "text-only") :
                                    if (!empty($attributes['gkitSearchIcon']['src'])) :
                                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['gkitSearchIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                                    endif;
                                endif; ?>
                            </span>
                            <span class="gkit-search-button-text">
                                <?php if (isset($attributes['gkitBtnAppearance']) && $attributes['gkitBtnAppearance'] !== "icon-only") :
                                    echo esc_html($attributes['gkitSearchBtnText']);
                                endif; ?>
                            </span>
                        </button>
                    <?php endif; ?>
                </form>
            </div>
            <!-- SearchResult Component -->
            <div class="gkit-search-result-dropdown <?php echo esc_attr($searchBoxStyle); ?>">
                <span class="gkit-search-result-header"></span>
                <div class="gkit-search-dropdown">
                    <?php echo $content; ?>
                </div>
                <div class="gkit-search-btn-wrapper">
                    <button class="gkit-more-search-result-btn">
                        <?php echo esc_html($attributes['gkitLoadMoreButton']); ?>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <!-- Markup for toggle-style -->
    <?php if ($searchBoxStyle === 'toggle-style'): ?>
        <div class="gkit-hidden-search-container <?php echo esc_attr($searchBoxStyle); ?>">
            <button class="gkit-clicked-button">
                <span class="gkit-toggle-icon gkit-show-search-icon">
                    <?php if (!empty($attributes['gkitClickBtnSearchIcon']['src'])) :
                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['gkitClickBtnSearchIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                    endif; ?>
                </span>
                <span class="gkit-toggle-icon gkit-hide-search-icon">
                    <?php if (!empty($attributes['gkitClickBtnCloseIcon']['src'])) :
                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['gkitClickBtnCloseIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                    endif; ?>
                </span>
            </button>

            <!-- SearchContainer Component -->
            <div class="gkit-search-container-wrapper">
                <div class="gkit-search-container <?php echo esc_attr($searchBoxStyle); ?>">
                    <form role="search" method="get" class="gkit-search-group" action="<?php echo esc_url(home_url('/')); ?>" target="_blank">
                        <div class="gkit-search-box">
                            <!-- SearchInput Component -->
                            <div class="gkit-search-box">
                                <div class="gkit-search-input" tab-index="0">
                                    <span class="gkit-search-icon">
                                        <?php if (isset($attributes['gkitShowInputIcon']) && $attributes['gkitShowInputIcon'] == true) :
                                            if (!empty($attributes['gkitInputIcon']['src'])) :
                                                echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['gkitInputIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                                            endif;
                                        endif; ?>
                                    </span>
                                    <input type="text" name="s" class="gkit_search-field" placeholder='<?php echo esc_attr($placeHolder); ?>' />
                                    <button type="submit" class="gkit-clear-search-button">
                                        <span class="gkit-search-icon">
                                            <?php if (!empty($attributes['gkitCloseIcon']['src'])) :
                                                echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['gkitCloseIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                                            endif; ?>
                                        </span>
                                    </button>
                                    <span class="gkit-clear-search-spinner"></span> <!-- Spinner placeholder -->
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- SearchResult Component -->
                <div class="gkit-search-result-dropdown <?php echo esc_attr($searchBoxStyle); ?>">
                    <span class="gkit-search-result-header"></span>
                    <div class="gkit-search-dropdown">
                        <?php echo $content; ?>
                    </div>
                    <div class="gkit-search-btn-wrapper">
                        <button class="gkit-more-search-result-btn">
                            <?php echo esc_html($attributes['gkitLoadMoreButton']); ?>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>