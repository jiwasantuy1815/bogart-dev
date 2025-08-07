<?php

namespace GutenkitPro\Routes;

defined('ABSPATH') || exit;

class QueryBuilder
{
	use \GutenkitPro\Traits\Singleton;

	public function __construct()
	{
		add_action('rest_api_init', array($this, 'query_builder_route'));
	}

	public function query_builder_route()
	{
		register_rest_route(
			'gutenkit/v1',
			'/query-builder/(?P<type>[a-zA-Z0-9]+)/',
			array(
				'methods'             => 'GET',
				'callback'            => array($this, 'gutenkit_query_builder'),
				'permission_callback' => array($this, 'gutenkit_query_builder_permission'),
			)
		);
	}

	function gutenkit_query_builder($request)
	{
		$params = $request->get_params();
		$data = array();
		$type = $params['type'];

		// Check if the 'type' parameter is provided
		if (!$type) {
			return new \WP_REST_Response(array('error' => 'Type parameter is required'), 400);
		}

		// Perform different queries based on the type
		switch ($type) {
			case 'posts':
				$data = \GutenkitPro\Core\BuildQuery::instance()->posts_query($params, true);
				break;
			case 'taxQuery':
				$data = \GutenkitPro\Core\BuildQuery::instance()->tax_query($params);
				break;
			default:
				// Default query if no type is specified
				$data = get_posts(array(
					'post_type' => 'post',
					'posts_per_page' => 10,
				));
				break;
		}

		// Return the data as a JSON response
		return new \WP_REST_Response($data, 200);
	}


	public function gutenkit_query_builder_permission()
	{
		return current_user_can( 'manage_options' );
	}
}
