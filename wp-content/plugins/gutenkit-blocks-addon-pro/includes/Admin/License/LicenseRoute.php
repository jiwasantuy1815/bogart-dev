<?php

namespace GutenkitPro\Admin\License;

use WP_REST_Response;

class LicenseRoute {

	
	use \GutenkitPro\Traits\Auth;
	public function __construct(){
		
		add_action(	'rest_api_init', function () {
			
			register_rest_route( 'gutenkit/v1', 'license/active',
				array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            =>  array( $this, 'activate_license'),
				'permission_callback' => '__return_true',
				),
			);
	
			register_rest_route('gutenkit/v1', 'license/deactive',
				array(
					'methods'             => \WP_REST_Server::ALLMETHODS,
					'callback'            => array( $this, 'deactive_license' ),
					'permission_callback' => '__return_true',
				),
			);

			register_rest_route('gutenkit/v1', 'license/get',
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_license' ),
					'permission_callback' => '__return_true',
				),
			);
			register_rest_route('gutenkit/v1', 'license/status',
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_license_status' ),
					'permission_callback' => '__return_true',
				),
			);
		});
	}

	public function deactive_license($resquest) {
		self::validate($resquest);

		$res = Helper::instance()->deactivate();

		return [
			'success' => 'ok',
			'msg'     => esc_html__('Successfully deactivated', 'gutenkit-blocks-addon-pro'),
		];
	}

	
	public function get_license($request) {

		self::validate($request);

		// Successfully activated
		$response = new WP_REST_Response([
			'success' => true,
			'data'    =>  Helper::instance()->get_license(),
			'message' => __('Successfully activated', 'gutenkit-blocks-addon-pro')
		], 200); // OK
		return $response;
	}
	public function get_license_status($request) {

		self::validate($request);

		// Successfully activated
		$response = new WP_REST_Response([
			'success' => true,
			'status'    =>  Helper::instance()->status()
		], 200); // OK
		return $response;
	}

	public function activate_license($request) {
		self::validate($request);

		$data = json_decode($request->get_body(), true);

		if (empty($data['license_key'])) {
			// Invalid license key
			$response = new WP_REST_Response([
				'success' => false,
				'message' => __('License key is empty', 'gutenkit-blocks-addon-pro')
			], 400); // Bad Request
			return $response;
		}

		$activationResult = Helper::instance()->activate($data['license_key']);
	
		if (!empty($activationResult->is_activated)) {
			// Successfully activated
			$response = new WP_REST_Response([
				'success' => true,
				'data'    => $activationResult,
				'message' => __('Successfully activated', 'gutenkit-blocks-addon-pro')
			], 200); // OK
			return $response;
		}

		if (!empty($activationResult->error)) {
			// Error during activation
			$response = new WP_REST_Response([
				'success' => false,
				'message' => $activationResult->message
			], 400); // Bad Request
			return $response;
		}
	
		// Unsupported pro version
		$response = new WP_REST_Response([
			'success' => false,
			'message' => esc_html__('Unsupported pro version', 'gutenkit-blocks-addon-pro')
		], 400); // Bad Request
		return $response;
	}

}
