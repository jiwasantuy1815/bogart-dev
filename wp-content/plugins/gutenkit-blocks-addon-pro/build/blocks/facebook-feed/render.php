<?php if (!defined('ABSPATH')) exit;

// Set attributes
$attributes = isset($attributes) ? $attributes : [];
$content = isset($content) ? $content : '';
$block = isset($block) ? $block : [];
$block_id = isset($attributes['blockID']) ? $attributes['blockID'] : '';
$block_class = isset($attributes['blockClass']) ? $attributes['blockClass'] : '';
$number_of_show_post = isset($attributes['numberOfShowPost']) ? intval($attributes['numberOfShowPost']) : 10;
$image_position = isset($attributes['imagePosition']) ? $attributes['imagePosition'] : 'center';
$show_reaction = isset($attributes['showReaction']) ? boolval($attributes['showReaction']) : false;
$show_comments = isset($attributes['showComments']) ? boolval($attributes['showComments']) : false;
$show_share_button = isset($attributes['showShareButton']) ? boolval($attributes['showShareButton']) : false;
$swiper_settings = !empty($attributes['swiperSettings']) ? json_encode($attributes['swiperSettings']) : [];
$loading = true;

$wrapper_extra_props = [];
$wrapper_extra_props['class'] = "gutenkit-facebook-feed";
if (!empty($attributes['facebookGridStyle'])) {
    $wrapper_extra_props['class'] .= ' ' . "gutenkit-facebook-feed" . $attributes['facebookGridStyle'];
}

// Call the API endpoint
$api_url = get_rest_url(null, 'gutenkit/v1/facebook-feed');
$response = wp_remote_get($api_url);

// Check for errors in the API response
if (is_wp_error($response)) {
    $error_message = $response->get_error_message();
}

// Retrieve the body of the response
$body = wp_remote_retrieve_body($response);
$data = json_decode($body);
$loading = false; // Set loading state after API call

// Check if API request failed
if (isset($data->data->status)) {
    $error_message = $data->message;
}

// Format text content
if (!function_exists('format_text')) {
    function format_text($text)
    {
        if (empty($text)) { return ''; }

        // Replace URLs
        $url_pattern = '/(https?:\/\/[^\s]+)/';
        $text = preg_replace($url_pattern, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>', $text);

        // Replace hashtags
        $hashtag_pattern = '/#(\w+)/';
        $text = preg_replace($hashtag_pattern, '<a href="https://www.facebook.com/hashtag/$1" target="_blank" rel="noopener noreferrer" style="white-space: nowrap;">#$1</a>', $text);

        return $text;
    }
}


// Get share URLs
if (!function_exists('getFacebookShareUrl')) {
    function getFacebookShareUrl($pageId, $postId)
    {
        return 'https://www.facebook.com/sharer/sharer.php?u=https://www.facebook.com/' . urlencode($pageId) . '/posts/' . urlencode($postId) . '&display=popup&ref=plugin&src=post';
    }
}

if (!function_exists('getTwitterShareUrl')) {
    function getTwitterShareUrl($postUrl)
    {
        return 'https://twitter.com/intent/tweet?url=' . urlencode($postUrl);
    }
}

if (!function_exists('getPinterestShareUrl')) {
    function getPinterestShareUrl($postUrl)
    {
        return 'https://www.pinterest.com/pin/create/button/?url=' . urlencode($postUrl);
    }
}


// Render comment component
if (!function_exists('gkit_facebook_comments')) {
    function gkit_facebook_comments($attributes, $item)
    {
        ob_start(); // Start output buffering
?>
        <div class="gkit-facebook-feed-comments">
            <a href="<?php echo esc_url($item->post_link); ?>" target="_blank" rel="nofollow">
                <span class="gkit-facebook-feed-comments-icon">
                    <?php if (!empty($attributes['commentIcon']['src'])) :
                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['commentIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                    endif; ?>
                </span>
                <span>Comments</span>
            </a>
        </div>
    <?php
        return ob_get_clean(); // Return the buffered content
    }
}

// Render share component
if (!function_exists('gkit_facebook_feed_share_menu')) {
    function gkit_facebook_feed_share_menu($item, $postId, $attributes)
    {
        // Generate the post URL for sharing
        $postUrl = 'https://www.facebook.com/' . esc_attr($item->from->id) . '/posts/' . esc_attr($postId);
        ob_start(); // Start output buffering to return HTML markup
    ?>
        <div class="gkit-facebook-feed-share">
            <div class="gkit-facebook-feed-share-button">
                <span>
                    <?php if (!empty($attributes['shareButtonIcon']['src'])) :
                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['shareButtonIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                    endif; ?>
                </span>
                <span> Share </span>
            </div>
            <div class="gkit-facebook-feed-share-menu">
                <a href="<?php echo esc_url(getFacebookShareUrl($item->from->id, $postId)); ?>" title="Facebook" target="_blank" class="gkit-facebook-feed-share-item gkit-facebook-icon">
                    <?php if (!empty($attributes['facebookBtnIcon']['src'])) :
                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['facebookBtnIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                    endif; ?>
                    <span class="gkit-facebook-feed-share-option-name"> Share on Facebook </span>
                </a>
                <a href="<?php echo esc_url(getTwitterShareUrl($postUrl)); ?>" title="Twitter" target="_blank" class="gkit-facebook-feed-share-item gkit-twitter-icon">
                    <?php if (!empty($attributes['twitterBtnIcon']['src'])) :
                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['twitterBtnIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                    endif; ?>
                    <span class="gkit-facebook-feed-share-option-name"> Share on Twitter </span>
                </a>
                <a href="<?php echo esc_url(getPinterestShareUrl($postUrl)); ?>" title="Pinterest" target="_blank" class="gkit-facebook-feed-share-item gkit-pinterest-icon">
                    <?php if (!empty($attributes['pinterestBtnIcon']['src'])) :
                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['pinterestBtnIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                    endif; ?>
                    <span class="gkit-facebook-feed-share-option-name"> Share on Pinterest </span>
                </a>
            </div>
        </div>
    <?php
        return ob_get_clean(); // Return the buffered output
    }
}

// Render reaction component
if (!function_exists('gkit_facebook_feed_reactions')) {
    function gkit_facebook_feed_reactions($attributes, $item)
    {
        ob_start(); // Start output buffering
    ?>
        <div class="gkit-facebook-feed-reactions">
            <a href="<?php echo esc_url($item->post_link); ?>" target="_blank" rel="nofollow">
                <span class="gkit-facebook-feed-reactions-icon">
                    <?php if (!empty($attributes['likeIcon']['src'])) :
                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['likeIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                    endif; ?>
                </span>
                <span>Like</span>
            </a>
        </div>
    <?php
        return ob_get_clean(); // Return the buffered output
    }
}

// Render count component
if (!function_exists('gkit_facebook_feed_count')) {
    function gkit_facebook_feed_count($attributes, $item)
    {
        ob_start(); // Start output buffering
    ?>
        <?php if (!empty($attributes['showComments']) || !empty($attributes['showReaction'])): ?>
            <div class="gkit-facebook-feed-count">
                <?php if (!empty($attributes['showReaction'])): ?>
                    <div class="gkit-facebook-feed-reaction">
                        <span class="gkit-facebook-feed-reactions-icon">
                            <?php
                            // Display up to 3 reaction icons
                            if (!empty($item->reactions->type)) {
                                $reactions = array_slice($item->reactions->type, 0, 3);
                                foreach ($reactions as $index => $like) {
                                    echo '<span class="gkit-facebook-feed-reactions-icon" data-settings="' . esc_attr(json_encode($like)) . '"></span>';
                                }
                            }
                            ?>
                        </span>
                        <span class="gkit-facebook-feed-reactions-count"><?php echo isset($item->reactions->total)  && esc_html($item->reactions->total); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($attributes['showComments']) && !empty($item->comments->summary->total_count) && $item->comments->summary->total_count > 0): ?>
                    <div class="gkit-facebook-feed-comment">
                        <span class="gkit-facebook-feed-comments-count">
                            <?php echo esc_html($item->comments->summary->total_count); ?>
                        </span>
                        <span class="gkit-facebook-feed-comments-text">Comments</span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

<?php
        return ob_get_clean(); // Return the buffered content
    }
}
?>


<div
    <?php echo wp_kses_post(Gutenkit\Helpers\Utils::get_dynamic_block_wrapper_attributes($block, $wrapper_extra_props)) ?>
    data-settings="<?php echo esc_attr(json_encode($attributes['imagePosition'])); ?>">
    <?php if ($loading): ?>
        <div class="spinner"></div>
    <?php elseif (isset($error_message)): ?>
        <div class="gutenkit-facebook-feed-error"><?php echo esc_html($error_message); ?></div>
    <?php else: ?>
        <?php if (!empty($data->data)): ?>
            <?php
            // Slice the data array to show only the specified number of posts
            $numberOfShowPost = isset($attributes['numberOfShowPost']) ? (int) $attributes['numberOfShowPost'] : count($data->data);
            $itemsToShow = array_slice($data->data, 0, $numberOfShowPost);
            ?>
            <?php foreach ($itemsToShow as $item): ?>
                <?php
                $postId = isset($item->id) ? explode('_', $item->id)[1] : ''; // Extract postId
                $message = isset($item->message) ? $item->message : '';
                $hasLongMessage = !empty(format_text($message)) && str_word_count(format_text($message)) > 40; // Check message length
                
                ?>
                <div class="gkit-facebook-feed-item" key="<?php echo esc_attr($item->id); ?>">

                    <div
                        class="single-card-item gkit-facebook-feed-item-<?php echo esc_attr($attributes['imagePosition'] ); ?> <?php echo esc_attr($attributes['imagePosition'] === 'background' ? $attributes['imagePositionBackgroundStyle'] : ''); ?>"
                        style="<?php echo $attributes['imagePosition'] === 'background' ? 'background-image: url(' . esc_url($item->full_picture) . '); background-size: cover;' : ''; ?>">
                        <div class="gkit-facebook-feed-item-inner">

                            <!-- Header section -->
                            <?php if (!empty($attributes['showAuthor'])): ?>
                                <div class="gkit-facebook-feed-header">
                                    <div class="gkit-facebook-feed-author">
                                        <?php if (in_array($attributes['authorSettings'], ['picture', 'both'])): ?>
                                            <div class="gkit-facebook-feed-author-picture">
                                                <a href="https://www.facebook.com/<?php echo esc_attr($item->from->id); ?>" target="_blank" rel="nofollow" class="<?php echo esc_attr($attributes['authorStyle']); ?> <?php echo !empty($attributes['showAuthorImageOutline']) ? 'gkit-author-image-outline' : ''; ?> gkit-author-image">
                                                    <img src="<?php echo esc_url($item->from->picture->data->url); ?>" alt="<?php echo esc_attr($item->from->name); ?>" />
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (in_array($attributes['authorSettings'], ['name', 'both'])): ?>
                                            <div class="gkit-facebook-feed-author-info">
                                                <div class="gkit-facebook-feed-author-name">
                                                    <a href="https://www.facebook.com/<?php echo esc_attr($item->from->id); ?>" target="_blank" rel="nofollow">
                                                        <?php echo esc_html($item->from->name); ?>
                                                    </a>
                                                </div>
                                                <?php if (!empty($attributes['showPostDate'])): ?>
                                                    <div class="gkit-facebook-feed-date">
                                                        <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($item->created_time))); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($attributes['showHeaderMenu']) && !empty($attributes['headerMenuStyle'] !== 'none')): ?>
                                        <div class="gkit-facebook-feed-header-menu">
                                            <span class="gkit-facebook-feed-header-menu__icon <?php echo esc_attr($attributes['headerMenuStyle'] === 'arrow' ? 'rotate-on-hover' : ''); ?>">
                                             <?php
                                                if ($attributes['headerMenuStyle'] === 'arrow') {
                                                    if (!empty($attributes['downArrowIcon']['src'])) {
                                                        echo wp_kses(
                                                            Gutenkit\Helpers\Utils::add_class_to_svg($attributes['downArrowIcon']['src']),
                                                            Gutenkit\Helpers\Utils::svg_allowed_html()
                                                        );
                                                    }
                                                } elseif (!empty($attributes['threeDotIcons']['src'])) {
                                                    echo wp_kses(
                                                        Gutenkit\Helpers\Utils::add_class_to_svg($attributes['threeDotIcons']['src']),
                                                        Gutenkit\Helpers\Utils::svg_allowed_html()
                                                    );
                                                }
                                             ?>
                                            </span>
                                            <div class="gkit-facebook-feed-header-menu__content">
                                                <?php if (!empty($item->attachments->data)): ?>
                                                    <?php foreach ($item->attachments->data as $index => $attachment): ?>
                                                        <a href="<?php echo esc_url($attachment->url); ?>" target="_blank" rel="nofollow">View on Facebook</a>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Content section -->
                            <div class="gkit-facebook-feed-content">
                                <?php if (!empty($item->message)): ?>
                                    <div class="gkit-facebook-feed-content-text">
                                        <span class="gkit-facebook-feed-text">
                                            <?php echo wp_kses_post(format_text($item->message)); ?>
                                        </span>
                                        <?php if ($hasLongMessage && $attributes['imagePosition'] !== 'background'): ?>
                                            <span class="gkit-facebook-feed-text-see-more"> See More </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (($attributes['imagePosition'] !== 'center') &&
                                    ($attributes['showReaction'] || $attributes['showComments'] || $attributes['showShareButton'])
                                ): ?>
                                    <div class="gkit-facebook-feed-footer-wrapper">
                                        <?php echo gkit_facebook_feed_count($attributes, $item); ?>
                                        <hr class="gkit-facebook-feed-footer-divider" />
                                        <div class="gkit-facebook-feed-footer">
                                            <?php if ($show_reaction) : ?>
                                                <?php echo gkit_facebook_feed_reactions($attributes, $item); ?>
                                            <?php endif; ?>
                                            <?php if ($show_comments) : ?>
                                                <?php echo gkit_facebook_comments($attributes, $item); ?>
                                            <?php endif; ?>
                                            <?php if ($show_share_button) : ?>
                                                <?php echo gkit_facebook_feed_share_menu($item, $postId, $attributes); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($attributes['imagePosition'] !== 'background'): ?>
                            <div class="gkit-facebook-feed-media-footer-wrapper">
                                <?php if (!empty($item->attachments->data)): ?>
                                    <?php foreach ($item->attachments->data as $index => $attachment): ?>
                                        <?php if (!empty($attachment->subattachments->data)): ?>
                                            <div class="gkit-facebook-feed-content-picture picture-slider swiper" key="<?php echo esc_attr($index); ?>" data-settings="<?php echo esc_attr(json_encode($attributes['swiperSettings'])); ?>">
                                                <div class="gkit-facebook-feed-content-picture-slider swiper-wrapper">
                                                    <?php foreach ($attachment->subattachments->data as $subIndex => $subAttachment): ?>
                                                        <a href="<?php echo esc_url($subAttachment->url); ?>" target="_blank" rel="nofollow" class="swiper-slide gkit-facebook-feed-content-picture-link" key="<?php echo esc_attr($subIndex); ?>">
                                                            <div>
                                                                <?php if (strpos($subAttachment->type, 'video') !== false): ?>
                                                                    <video src="<?php echo esc_url($subAttachment->media->source); ?>" controls data-fancybox></video>
                                                                <?php else: ?>
                                                                    <img src="<?php echo esc_url($subAttachment->media->image->src); ?>" alt="<?php echo esc_attr('sub-attachment-' . $postId); ?>" data-fancybox />
                                                                <?php endif; ?>
                                                            </div>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                                <span class="gkit-facebook-feed-nav-button gkit-prev swiper-button-prev">
                                                    <?php if (!empty($attributes['leftArrowIcon']['src'])) :
                                                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['leftArrowIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                                                    endif; ?>
                                                </span>
                                                <span class="gkit-facebook-feed-nav-button gkit-next swiper-button-next">
                                                    <?php if (!empty($attributes['rightArrowIcon']['src'])) :
                                                        echo wp_kses(Gutenkit\Helpers\Utils::add_class_to_svg($attributes['rightArrowIcon']['src']), Gutenkit\Helpers\Utils::svg_allowed_html());
                                                    endif; ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <div class="gkit-facebook-feed-content-picture" key="<?php echo esc_attr($index); ?>">
                                                <a href="<?php echo esc_url($attachment->url); ?>" target="_blank" rel="nofollow" class="gkit-facebook-feed-content-picture-link">
                                                    <div>
                                                        <?php if (strpos($attachment->type, 'video') !== false): ?>
                                                            <video src="<?php echo esc_url($attachment->media->source); ?>" controls data-fancybox></video>
                                                        <?php else: ?>
                                                            <img src="<?php echo esc_url($attachment->media->image->src); ?>" alt="<?php echo esc_attr('attachment-' . $postId); ?>" data-fancybox />
                                                        <?php endif; ?>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- Footer -->
                                <?php if ($attributes['imagePosition'] === 'center' && ($show_reaction || $show_comments || $show_share_button)): ?>
                                    <div class="gkit-facebook-feed-footer-wrapper">
                                        <?php echo gkit_facebook_feed_count($attributes, $item); ?>
                                        <hr class="gkit-facebook-feed-footer-divider" />
                                        <div class="gkit-facebook-feed-footer">
                                            <?php if ($show_reaction) : ?>
                                                <?php echo gkit_facebook_feed_reactions($attributes, $item); ?>
                                            <?php endif; ?>
                                            <?php if ($show_comments) : ?>
                                                <?php echo gkit_facebook_comments($attributes, $item); ?>
                                            <?php endif; ?>
                                            <?php if ($show_share_button) : ?>
                                                <?php echo gkit_facebook_feed_share_menu($item, $postId, $attributes); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>