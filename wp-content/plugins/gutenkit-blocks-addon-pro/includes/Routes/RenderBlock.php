<?php

namespace GutenkitPro\Routes;

defined('ABSPATH') || exit;

class RenderBlock
{
    use \Gutenkit\Traits\Singleton;

    public function __construct()
    {
        add_action("rest_api_init", [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route(
            'gutenkit/v1',
            '/render-block',
            array(
                'methods'             => 'POST',
                'callback'            => [$this, 'handle_render_block_request'],
                'permission_callback' => '__return_true', // Public API, no permission check needed
            )
        );
    }

    public function get_complex_select_value($value, $isMultiple = false) {
        if ($isMultiple && is_array($value)) {
            return array_column($value, 'value');
        }
        return $value['value'] ?? '';
    }

    public function handle_render_block_request(\WP_REST_Request $request)
    {
        $search = $request->get_param('search');
        $category_name = $request->get_param('category_name');
        $post_type = $post_type = $this->get_complex_select_value($request->get_param('post_type'), true);
        $block = $request->get_param('block');
        $posts_per_page = $request->get_param('posts_per_page');
        $terms = $request->get_param('terms');
        $taxonomy = $request->get_param('taxonomy');

        // Extract taxonomy slug if available
        $taxonomy_slug = '';
        if (is_object($taxonomy) && isset($taxonomy->slug)) {
            $taxonomy_slug = $taxonomy->slug;
        } elseif (is_array($taxonomy) && isset($taxonomy['slug'])) {
            $taxonomy_slug = $taxonomy['slug'];
        }

        if (empty($post_type)) {
            $post_type = 'any'; // Search across all public post types
        }


        $query_args = [
            's'              => $search,
            'posts_per_page'  => $posts_per_page,
            'post_type'       => $post_type,
            'tax_query'       => []
        ];

        if (!empty($taxonomy_slug) && !empty($terms)) {
            $query_args['tax_query'][] = [
                'taxonomy'     => $taxonomy_slug,
                'field'       => 'slug',
                'terms'       => $terms,
                'operator'    => 'IN'
            ];
        }

        $query = new \WP_Query($query_args);

        if (!$query->have_posts()) {
            return new \WP_Error('no_posts', __('No posts found', 'gutenkit'), ['status' => 404]);
        }

        $query_content = '';
        $block = json_decode($block);
        while ($query->have_posts()) {
            $query->the_post();
            // Get an instance of the current Post Template block.
            $block_instance = $block->parsed_block;
            $block_instance = json_encode($block_instance);
            $block_instance = json_decode($block_instance, true);
            $block_instance['blockName'] = 'core/null';
            $post_id              = get_the_ID();
            
            $filter_block_context = static function ($context) use ($post_id, $post_type) {
                $context['postType'] = $post_type;
                $context['postId']   = $post_id;
                return $context;
            };

            // Use an early priority to so that other 'render_block_context' filters have access to the values.
            add_filter('render_block_context', $filter_block_context, 1);
            // Render the inner blocks of the Post Template block with `dynamic` set to `false` to prevent calling
            // `render_callback` and ensure that no wrapper markup is included.
            $block_content = (new \WP_Block($block_instance))->render(array('dynamic' => false));
            remove_filter('render_block_context', $filter_block_context, 1);
            $query_content .= sprintf(
                '<div class="gutenkit-render-block-item">%s</div>',
                $block_content,
            );
        }
        wp_reset_postdata();
        return rest_ensure_response(['content' => $query_content, 'post_count' => $query->post_count]);
    }
}
