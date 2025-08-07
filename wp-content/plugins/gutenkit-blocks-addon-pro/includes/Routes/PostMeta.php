<?php

namespace GutenkitPro\Routes;

defined('ABSPATH') || exit;

class PostMeta
{
    use \Gutenkit\Traits\Singleton;
    private $response = [];
    public function __construct()
    {
        add_action("rest_api_init", [$this, 'post_meta_routes']);
    }

    public function post_meta_routes()
    {
        // Register the route with optional group ID
        register_rest_route(
            'gutenkit/v1',
            '/post-meta',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'handle_post_meta_request'),
                'permission_callback' => array($this, 'post_meta_permissions'),
            )
        );
    }

    public function post_meta_permissions()
    {
        return current_user_can('edit_posts');
    }

    public function handle_post_meta_request()
    {
        $meta_keys = get_registered_meta_keys('post');
        if (!empty($meta_keys)) {
            return wp_send_json($meta_keys);
        }else{
            return wp_send_json([]);
        }
    }
}
