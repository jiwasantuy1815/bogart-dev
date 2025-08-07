<?php

namespace Gutenkit\Routes;

defined('ABSPATH') || exit;

class MailChimp
{
	use \Gutenkit\Traits\Singleton;

	public function __construct()
	{
		add_action('rest_api_init', array($this, 'gutenkit_mailchimp_get_list_id'));
		add_action('rest_api_init', array($this, 'gutenkit_mailchimp_post_data'));
		add_action('rest_api_init', array($this, 'gutenkit_mailchimp_get_interest_data'));
		// add_action('rest_api_init', array($this, 'gutenkit_mailchimp_form_field_list'));
	}

	public function gutenkit_mailchimp_get_list_id()
	{
		register_rest_route(
			'gutenkit/v1',
			'/mailchimp/get/lists',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'gutenkit_mailchimp_callback'),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function gutenkit_mailchimp_post_data()
	{
		register_rest_route(
			'gutenkit/v1',
			'/mailchimp/post/form',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'gutenkit_mailchimp_post_callback'),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function gutenkit_mailchimp_get_interest_data(){
		register_rest_route(
			'gutenkit/v1',
			'/mailchimp/get/interests',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'gutenkit_mailchimp_get_interests_callback'),
				'permission_callback' => '__return_true',
				'args' => array(
					'list_id' => array(
						'required' => true,
						'type' => 'string',
						'description' => __('The Mailchimp list ID', 'gutenkit-blocks-addon'),
					),
				),
			)
		);
	}

	public function gutenkit_mailchimp_get_interests_callback($param){
		// error_log(print_r($param['list_id'], true));

		// Retrieve form list id
		$list_id = $param['list_id'];
		if(empty($list_id)) {
			return;
		}
		
		$api_key = get_option('gutenkit_settings_list');
		// error_log(print_r($api_key, true));
		
		$api_value = !empty($api_key) ? $api_key['mailchimp']['fields']['api_key']['value'] : '';
		$server_parts = explode('-', $api_value);
		// error_log(print_r($server_parts, true));

		if(!isset($server_parts[1])) {
			return;
		}

		$body = $param->get_body();
		// error_log(print_r($body, true));
		
		$request = json_decode($body, true);
		// error_log(print_r($request, true));
		
		$server_prefix = $server_parts[1];
		//construct the API URL
		$url = 'https://' . $server_prefix . '.api.mailchimp.com/3.0/lists/'.$list_id.'/interest-categories';
		$response = wp_remote_get($url, [
			'headers' => [
				'Authorization' => 'apikey ' . $api_value,
				'Content-Type' => 'application/json; charset=utf-8',
			],
		]);
		$resBody = json_decode($response['body'], true);
		$categories = isset($resBody['categories']) ? $resBody['categories'] : [];
		// error_log(print_r($categories, true));
		if (!empty($categories)) {
			foreach ($categories as $category) {
				// error_log(print_r($category, true));

				$categoryId = $category['id'];

				//call the API to get the interests list of this category
				$interestUrl = 'https://' . $server_prefix . '.api.mailchimp.com/3.0/lists/'.$list_id.'/interest-categories/'.$categoryId.'/interests';
				$response = wp_remote_get($interestUrl, [
					'headers' => [
						'Authorization' => 'apikey ' . $api_value,
						'Content-Type' => 'application/json; charset=utf-8',
					],
				]);
				// error_log(print_r($response, true));
				$interestResBody = json_decode($response['body'], true);
				$interests = isset($interestResBody['interests']) ? $interestResBody['interests'] : [];
				// error_log(print_r($interests, true));
				


				// Build trimmed category info with interests
				$results[] = [
					'list_id'       => $category['list_id'],
					'id'            => $categoryId,
					'title'         => $category['title'],
					'display_order' => $category['display_order'],
					'type'          => $category['type'],
					'interests'     => !empty($interests) ? array_map(function ($i) {
						return [
							'id'    => $i['id'],
							'name'  => $i['name'],
							'subscriber_count' => $i['subscriber_count']
						];
					}, $interests) : []
				];
				
				
				
				
			}
		}

		return $results;
		
		
	}


	public function gutenkit_mailchimp_callback()
	{
		$options = [['value' => '', 'label' => __('Select a Form', 'gutenkit-blocks-addon')]];
		$api_key = get_option('gutenkit_settings_list');
		$api_value = !empty($api_key) ? $api_key['mailchimp']['fields']['api_key']['value'] : '';
		$server_parts = explode('-', $api_value);

		if (!isset($server_parts[1])) {
			return $options;
		}
		$server_prefix = $server_parts[1];
		$url = 'https://' . $server_prefix . '.api.mailchimp.com/3.0/lists';
		$response = wp_remote_get($url, [
			'headers' => [
				'Authorization' => 'apikey ' . $api_value,
				'Content-Type' => 'application/json; charset=utf-8',
			],
		]);


		if (is_array($response) && !is_wp_error($response)) {
			$body = json_decode($response['body'], true);
			
			$listed = isset($body['lists']) ? $body['lists'] : [];

			$list_id = $listed[0]['id'];
			$form_fields = [];
			if (isset($list_id)) {
				$form_fields = $this->gutenkit_mailchimp_get_form_fields($list_id, $server_prefix, $api_value);
			}

			if (is_array($listed) && sizeof($listed) > 0) {
				foreach ($listed as $v) {
					$options[] = [
						'value' => $v['id'],
						'label' => $v['name']
					];
				}
			}
		}


		return [
			"options" => $options,
			"form_fields" => $form_fields
		];
	}


	public function gutenkit_mailchimp_post_callback($param)
	{
		// error_log(print_r($param, true));
		$body = $param->get_body();
		$request = json_decode($body, true);

		$return = ['success' => [], 'error' => []];

		// Retrieve API key from options
		$api_key = get_option('gutenkit_settings_list');
		$token = !empty($api_key) ? $api_key['mailchimp']['fields']['api_key']['value'] : '';

		$formData = [
			'status_if_new' => 'subscribed',
			'merge_fields' => [],
			'status' => 'subscribed'
		];
		// error_log(print_r($request, true));
		

		//prepare the data array
		foreach ($request as $key => $value) {
			// error_log(print_r($key, true));
			

			if ($key == 'EMAIL') {
				$formData['email_address'] = !empty($request['EMAIL']) ? sanitize_email($request['EMAIL']) : '';
			} else {
				$gkit_mailchimp_key_1 = explode("-", $key)[0];

				if ($gkit_mailchimp_key_1 == 'gkit_mailchimp_address') {
					$addressMainKey = explode("-", $key)[1];
					$addressInfoKey = explode("-", $key)[2];
					if (!empty($addressMainKey)) {
						$formData['merge_fields'][$addressMainKey][$addressInfoKey] = !empty($request[$key]) ? sanitize_text_field($request[$key]) : '';
					}
				} else if($gkit_mailchimp_key_1 == 'gkit_mailchimp_date' || $gkit_mailchimp_key_1 == 'gkit_mailchimp_birthday') {
					$gkit_mailchimp_date_key = explode("-", $key)[1];

					if(!empty($request[$key])){
						$date_parts = explode('-', sanitize_text_field($request[$key])); // Split by '-'
						$formatted_date = "";
						// Ensure the input has 3 parts (year, month, day) and reformat to MM/DD/YYYY
						if ($gkit_mailchimp_key_1 == 'gkit_mailchimp_date' && count($date_parts) === 3 ) {
							$formatted_date = $date_parts[1] . '/' . $date_parts[2] . '/' . $date_parts[0]; // MM/DD/YYYY
						} else if ($gkit_mailchimp_key_1 == 'gkit_mailchimp_birthday' && count($date_parts) >= 2) {
							// Format as DD/MM
							$formatted_date = $date_parts[2] . '/' . $date_parts[1]; 
						} else {
							// Handle incorrect input format (optional: set to empty or some default value)
							$formatted_date = '';
						}
						$formData['merge_fields'][$gkit_mailchimp_date_key] = $formatted_date;
					}

				} else if ($gkit_mailchimp_key_1 == 'gkit_mailchimp_phone') {
					$gkit_mailchimp_phone_key = explode("-", $key)[1];

					$formData['merge_fields'][$gkit_mailchimp_phone_key] = !empty($request[$key]) ? preg_replace('/\D+/', '', $request[$key]) : '';
				} else {
					if ($key != 'list_id') {
						$formData['merge_fields'][$key] = !empty($request[$key]) ? sanitize_text_field($request[$key]) : '';
					}
				}

				$interestResult = [];
				if (strpos(strtolower($key), 'interest') !== false) {
					// error_log(print_r($value, true));
					$parts = explode('-', $value, 2);
					if (count($parts) === 2) {
						$formData['interests'][$parts[0]] = filter_var($parts[1], FILTER_VALIDATE_BOOLEAN) ;
					}
				}
			}
			
		}
		error_log(print_r($formData, true));
		

		// Validate API key and server prefix
		if (empty($token)) {
			$return['error'] = esc_html__('Please set API Key in Gutenkit -> Settings -> API Integration -> MailChimp and Create Campaign.', 'gutenkit-blocks-addon');
			return $return;
		}

		$server_parts = explode('-', $token);
		if (!isset($server_parts[1])) {
			$return['error'] = esc_html__('Invalid API key format.', 'gutenkit-blocks-addon');
			return $return;
		}

		// Prepare subscription status based on double opt-in setting
		$subscription_status = !empty($request['double_opt_in']) && $request['double_opt_in'] === 'yes' ? 'pending' : 'subscribed';
		$data['status'] = $subscription_status;

		// Construct the API URL
		$server_prefix = $server_parts[1];
		$list_id = isset($request['list_id']) ? $request['list_id'] : '';
		$url = 'https://' . $server_prefix . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/';

		// Make the API request
		$response = wp_remote_post($url, [
			'method'    => 'POST',
			'timeout'   => 45,
			'headers'   => [
				'Authorization' => 'apikey ' . $token,
				'Content-Type'  => 'application/json; charset=utf-8',
			],
			'body'      => wp_json_encode($formData),
		]);
		

		// Handle the response
		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			/* translators: %s: error message */
			$return['error'] = sprintf(esc_html__('Something went wrong: %s', 'gutenkit-blocks-addon'), $error_message);
		} else {
			$response_body = wp_remote_retrieve_body($response);
			$response_data = json_decode($response_body, true);

			// Check if there are errors returned from Mailchimp
			if (isset($response_data['status']) && !in_array($response_data['status'], ['subscribed', 'pending'])) {
				$return['error'] = $response_data;

			} else {
				$return['status'] = $response_data['status'];
				if ($response_data['status'] === 'pending') {
					$return['success'] = esc_html__('Please check your email to confirm your subscription.', 'gutenkit-blocks-addon');
				} else if ($response_data['status'] === 'subscribed') {
					$return['success'] = esc_html__('Successfully subscribed to the mailing list.', 'gutenkit-blocks-addon');
				}
			}
		}
		return $return;
	}


	public function gutenkit_mailchimp_get_form_fields($id, $server_prefix, $api_value)
	{

		$url = 'https://' . $server_prefix . '.api.mailchimp.com/3.0/lists/' . $id . '/merge-fields';
		$response = wp_remote_get($url, [
			'headers' => [
				'Authorization' => 'apikey ' . $api_value,
				'Content-Type' => 'application/json; charset=utf-8',
			],
		]);
		$fieldListBody = isset($response['body']) ? json_decode($response['body'], true) : [];
		$fieldListItems = isset($fieldListBody['merge_fields']) ? $fieldListBody['merge_fields'] : [];
		// error_log(print_r($fieldListBody, true));
		




		$tagsAndNames = [['value' => '', 'label' => __('Select Field Name ID', 'gutenkit-blocks-addon')], ['value' => 'EMAIL', 'label' => __('EMAIL', 'gutenkit-blocks-addon')]];

		foreach ($fieldListItems as $key => $item) {
			if($item['type'] == 'dropdown' || $item['type'] == 'radio'){
				$tagsAndNames[] = [
					"value" => $item['tag'],
					"label" => $item['name'],
					"type" => $item['type'],
					"options" => $item['options']['choices'],
				];
			} else {
				$tagsAndNames[] = [
					"value" => $item['tag'],
					"label" => $item['tag'],
				];
			}
		}

		return $tagsAndNames;
	}

	// https://mailchimp.com/help/all-the-merge-tags-cheat-sheet/
}
