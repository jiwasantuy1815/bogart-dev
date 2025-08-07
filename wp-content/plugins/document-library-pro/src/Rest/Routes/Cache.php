<?php

namespace Barn2\Plugin\Document_Library_Pro\Rest\Routes;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Rest\Base_Route;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Rest\Route;
use Barn2\Plugin\Document_Library_Pro\Util\Util;
use WP_REST_Response;
use WP_REST_Server;

/**
 * REST controller for the uploading files from the frontend.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Cache extends Base_Route implements Route {

	protected $rest_base = 'clear-cache';

	/**
	 * Register the REST routes.
	 */
	public function register_routes() {

		// Clear cache.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'clear_cache' ],
					'permission_callback' => [ $this, 'permission_callback' ],
				],
			]
		);
	}

	/**
	 * Clear the cache.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response|WP_Error
	 */
	public function clear_cache( $request ) {
		Util::delete_table_transients();

		return new WP_REST_Response(
			[
				'success' => true,
				'message' => __( 'Cache cleared successfully.', 'document-library-pro' ),
			],
			200
		);
	}

	/**
	 * Permission callback.
	 *
	 * @return bool
	 */
	public function permission_callback() {
		return current_user_can( 'manage_options' );
	}
}
