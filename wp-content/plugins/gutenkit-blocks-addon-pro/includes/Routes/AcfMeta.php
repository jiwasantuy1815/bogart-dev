<?php

namespace GutenkitPro\Routes;

defined('ABSPATH') || exit;

class AcfMeta
{
    use \Gutenkit\Traits\Singleton;
    private $response = [];
    public function __construct()
    {
        add_action('acf/init', array($this, 'register'));
    }

    public function register()
    {
        add_action("rest_api_init", [$this, 'acf_meta_routes']);
    }

    public function acf_meta_routes() {
        // Register the route with optional group ID
        register_rest_route(
            'gutenkit/v1',
            '/acf-meta/groups(?:/(?P<id>[a-zA-Z0-9_-]+))?',
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'handle_acf_meta_request' ),
                'permission_callback' => array( $this, 'acf_meta_permissions' ),
            )
        );

        register_rest_route(
            'gutenkit/v1',
            '/acf-meta/value',
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'handle_acf_meta_image_value_request' ),
                'permission_callback' => array( $this, 'acf_meta_permissions' ),
            )
        );
    }

    public function acf_meta_permissions()
    {
        return current_user_can('edit_posts');
    }

    public function handle_acf_meta_request($request)
    {
        $group_key = $request->get_param('id');
        $search_input = $request->get_param('search');
        if (!empty($group_key)) {
            $fields = acf_get_fields($group_key);
            if ($fields) {
                $fields = array_filter($fields, function ($field) use ($search_input) {
                   if (!empty($search_input)) {
                       return stripos( $field['label'], $search_input ) !== false;
                   }
                   return true;
                });

                $this->response = array_merge($this->response, $fields);
            }
        }else{
            $acf_groups = get_posts(array(
                'post_type' => 'acf-field-group',
            ));

            if ($acf_groups) {
                foreach ($acf_groups as $acf_group) {
                    $this->response[] = [
                        'id' => $acf_group->ID,
                        'label' => $acf_group->post_title,
                        'value' => $acf_group->post_name,
                        'content' => !empty($acf_group->post_content) ? unserialize($acf_group->post_content) : '',
                    ];
                }

                $this->response = array_filter($this->response, function ($group) use ($search_input) {
                    if (!empty($search_input)) {
                        return stripos( $group['label'], $search_input ) !== false;
                    }
                    return true;
                });
            }
        }

        return wp_send_json($this->response);
    }

    public function handle_acf_meta_image_value_request($request)
    {
        $this->response = "";
        $field = $request->get_param('field');
        $post_id = $request->get_param('id');
        if (!empty($field) && !empty($post_id)) {
            $value = get_field($field, $post_id);
            if (!empty($value) && is_array($value)) {
                $this->response = $value['url'];
            }

            if (!empty($value) && is_numeric($value)) {
                $this->response = wp_get_attachment_url($value);
            }

            if (!empty($value) && is_string($value)) {
                $this->response = $value;
            }
        }

        return wp_send_json($this->response);
    }
}
