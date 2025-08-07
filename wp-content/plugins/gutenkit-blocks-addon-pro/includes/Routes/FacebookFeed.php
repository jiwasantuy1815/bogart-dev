<?php

namespace GutenkitPro\Routes;
use \Gutenkit\Helpers\Utils;

defined('ABSPATH') || exit;

class FacebookFeed
{
    use \GutenkitPro\Traits\Singleton;

    private $transient_expiration;

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'facebook_feed_routes']);
    }

    /**
     * Registers the REST route for the Facebook feed.
     */
    public function facebook_feed_routes()
    {
        register_rest_route(
            'gutenkit/v1',
            '/facebook-feed',
            array(
                'methods'             => 'GET',
                'callback'            => [$this, 'handle_facebook_feed_request'],
                'permission_callback' => '__return_true',
            )
        );
    }

    /**
     * Handles the Facebook feed request, retrieves data, caches, and returns the feed.
     */
    public function handle_facebook_feed_request($request)
    {
        // Check if the access token or page ID is missing
        $access_token = Utils::get_settings('facebook_feed', 'fields', 'aceess_token');
        $page_id = Utils::get_settings('facebook_feed', 'fields', 'page_id');
        $expiration_time = Utils::get_settings('facebook_feed', 'fields', 'expiration_time');

        if (empty($access_token) || empty($page_id)) {
            return new \WP_Error(
                'missing_settings',
                __('Access token or page ID is missing. Please provide the required details in the settings.', 'gutenkit'),
                ['status' => 400]
            );
        }

        $transient_key = 'gutenkit_facebook_feed';
        // Generate a hash based on the access token and page ID
        $current_hash = md5($access_token . $page_id);

		// Set the transient expiration time
		$this->transient_expiration = !empty($expiration_time) ? $expiration_time * HOUR_IN_SECONDS : 2 * 365 * 24 * HOUR_IN_SECONDS;

        // Check if the feed is cached
        $cached_feed = get_transient($transient_key);
        if ($cached_feed) {
            if (isset($cached_feed['hash']) && $cached_feed['hash'] === $current_hash) {
                return $cached_feed['data'];
            }

            // If the hash does not match, delete the transient
            delete_transient($transient_key);
        }

        // Fetch new data from Facebook API
        $response = $this->fetch_facebook_feed($access_token, $page_id);
        if (is_wp_error($response)) {
            return $response;
        }

        // Save hash and response data together
        $data_to_cache = [
            'hash' => $current_hash,
            'data' => $response,
        ];

        // Cache and return the fetched data
        set_transient(
			$transient_key,
			$data_to_cache,
			$this->transient_expiration
		);

        return $response;
    }

    /**
     * Fetches Facebook feed from the API.
     *
     * @return mixed|WP_Error
     */
    private function fetch_facebook_feed($access_token, $page_id)
    {
        if (empty($access_token) || empty($page_id)) {
            return new \WP_Error('missing_settings', __('Access token or page ID is missing.', 'gutenkit'), ['status' => 400]);
        }

        $url = sprintf(
            'https://graph.facebook.com/v20.0/%s/feed?fields=id,message,created_time,full_picture,attachments{media,url,subattachments,type},shares,reactions.summary(total_count),comments.summary(total_count),from{id,name,picture},videos{source,thumbnails,title,length}&access_token=%s',
            esc_attr($page_id),
            esc_attr($access_token)
        );

        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return new \WP_Error('api_request_failed', __('Error fetching posts from Facebook API.', 'gutenkit'), ['status' => 500]);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        // Handle specific API errors
        if (isset($data->error)) {
            return $this->handle_facebook_api_error($data->error);
        }

        // Loop through each post to fetch reactions
        foreach ($data->data as &$post) {
            if (isset($post->id)) {
                list($post_page_id, $post_id) = explode('_', $post->id); // Split the ID to get page and post IDs

                // Construct the Facebook post link
                $post->post_link = sprintf(
                    'https://www.facebook.com/%s/posts/%s',
                    esc_attr($post_page_id),
                    esc_attr($post_id)
                );
            }

            // Feed Reactions 
            if (isset($post->id)) {
                $reactions_url = sprintf(
                    'https://graph.facebook.com/v20.0/%s?fields=reactions.type(LIKE).limit(0).summary(total_count).as(like_count),reactions.type(LOVE).limit(0).summary(total_count).as(love_count),reactions.type(HAHA).limit(0).summary(total_count).as(haha_count),reactions.type(WOW).limit(0).summary(total_count).as(wow_count),reactions.type(SAD).limit(0).summary(total_count).as(sad_count),reactions.type(ANGRY).limit(0).summary(total_count).as(angry_count),reactions{id,name}&access_token=%s',
                    esc_attr($post->id),
                    esc_attr($access_token)
                );

                $reactions_response = wp_remote_get($reactions_url);
                if (!is_wp_error($reactions_response)) {
                    $reactions_body = wp_remote_retrieve_body($reactions_response);
                    $reactions_data = json_decode($reactions_body, true);

                    // Initialize arrays for storing filtered reaction types and total count
                    $filtered_reactions = [
                        'type' => [],
                        'total' => 0,
                        'name' => '',
                    ];

                    // Mapping of reaction counts to their display names
                    $reaction_map = [
                        'like_count' => 'LIKE',
                        'love_count' => 'LOVE',
                        'haha_count' => 'HAHA',
                        'wow_count' => 'WOW',
                        'sad_count' => 'SAD',
                        'angry_count' => 'ANGRY'
                    ];

                    // Check and filter reactions
                    foreach ($reaction_map as $reaction_key => $display_name) {
                        if (isset($reactions_data[$reaction_key]['summary']['total_count']) && $reactions_data[$reaction_key]['summary']['total_count'] > 0) {
                            $filtered_reactions['type'][] = $display_name;
                            $filtered_reactions['total'] += $reactions_data[$reaction_key]['summary']['total_count'];
                        }

                        if (isset($reactions_data['reactions']['data'])) {
                            foreach ($reactions_data['reactions']['data'] as $reaction) {
                                $filtered_reactions['name'] = $reaction['name'];
                            }
                        }
                    }

                    // Add the filtered reactions to the post if any types have a total_count greater than 0
                    if (!empty($filtered_reactions['type'])) {
                        $post->reactions = $filtered_reactions;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Handles errors returned by the Facebook API.
     *
     * @param object $error
     * @return WP_Error
     */
    private function handle_facebook_api_error($error)
    {
        $error_code = $error->code;
        $error_message = $error->message;

        // Check for specific text in the error message to identify invalid page_id
        if (strpos($error_message, 'Unsupported get request') !== false) {
            return new \WP_Error(
                'facebook_invalid_page_id',
                __('The provided Page ID is invalid or does not exist. Please verify the Page ID in the settings.', 'gutenkit'),
                ['status' => 400]
            );
        }

        switch ($error_code) {
            case 190:
                return new \WP_Error(
                    'facebook_token_expired',
                    __('The Facebook access token has expired Or invalid. Please update the token.', 'gutenkit'),
                    ['status' => 401]
                );

            case 102:
                return new \WP_Error(
                    'facebook_auth_failed',
                    __('Authentication failed with Facebook. Please check your credentials.', 'gutenkit'),
                    ['status' => 401]
                );

            case 803:
                return new \WP_Error(
                    'facebook_invalid_page_id',
                    __('The Page ID is invalid or the page does not exist. Please verify the Page ID in the settings.', 'gutenkit'),
                    ['status' => 400]
                );

            default:
                return new \WP_Error(
                    'facebook_api_error',
                    __('Error fetching posts from Facebook: ' . $error_message, 'gutenkit'),
                    ['status' => 500]
                );
        }
    }
}