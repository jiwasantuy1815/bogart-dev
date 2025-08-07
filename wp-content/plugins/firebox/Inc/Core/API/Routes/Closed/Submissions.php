<?php
/**
 * @package         FireBox
 * @version         3.0.0
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\API\Routes\Closed;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use \FireBox\Core\API\EndpointController;
use \FireBox\Core\Helpers\Form\Form;
use WP_REST_Server;

class Submissions extends EndpointController
{
	/**
	 * Endpoint name
	 * 
	 * @return  string
	 */
	public function get_name()
	{
		return 'submissions';
	}
	
	/**
	 * Get API namespace
	 * 
	 * @return  string
	 */
	public function get_namespace()
	{
		return parent::get_namespace() . '(?:\/(?P<api_key>\w+))?';
	}
	
	/**
	 * Register routes
	 * 
	 * @return  void
	 */
	public function register()
	{
		$this->register_route('/get?(?:\/(?P<form_id>.+))?', WP_REST_Server::READABLE, [$this, 'get_data']);
	}

	public function get_permission_callback($request)
	{
		$request_api_key = trim($request->get_param('api_key'));
		if (empty($request_api_key))
		{
			return false;
		}

		return $request_api_key === \FireBox\Core\Helpers\Settings::findSettingsOption('api_key');
	}

	public function get_data($request)
	{
		$form_id = $request->get_param('form_id');
		if (empty($form_id))
		{
			$error = new \WP_Error('missing_form_id', 'Missing Form ID.');
			wp_send_json_error($error);
		}

		if (!$form = Form::getFormByID($form_id))
		{
			$error = new \WP_Error('invalid_form_id', 'Invalid Form ID.');
			wp_send_json_error($error);
		}

		// Get form fields
		$form_fields = [];
		foreach ($form['fields'] as $field)
		{
			$form_fields[$field->getOptionValue('id')] = $field->getOptionValue('name');
		}

		if (!$submissions = Form::getSubmissions($form_id))
		{
			$error = new \WP_Error('no_submissions', 'No submissions found.');
			wp_send_json_error($error);
		}

		$data = [];

		foreach ($submissions as $submission)
		{
			$payload = [
				'id' => $submission->id,
				'state' => $submission->state,
				'created' => $submission->created_at,
				'form_id' => $submission->form_id,
				'user_id' => $submission->user_id,
				'visitor_id' => $submission->visitor_id
			];
			
			// Add fields
			if (count($submission->meta))
			{
				foreach ($submission->meta as $meta_item)
				{
					// Skip submission field value for a field that no longer exists in the form
					if (!isset($form_fields[$meta_item->meta_key]))
					{
						continue;
					}

					$payload['field_' . $form_fields[$meta_item->meta_key]] = $meta_item->meta_value;
				}
			}
			
			$data[] = $payload;
		}

		return $data;
	}
}