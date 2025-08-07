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
use WP_REST_Server;

class Forms extends EndpointController
{
	/**
	 * Endpoint name
	 * 
	 * @return  string
	 */
	public function get_name()
	{
		return 'forms';
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
		$this->register_route('/get', WP_REST_Server::READABLE, [$this, 'get_data']);
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

	public function get_data()
	{
		// Get forms
		if (!$forms = \FireBox\Core\Helpers\Form\Form::getForms())
		{
			return [];
		}

		$data = [];

		foreach ($forms as $form)
		{
			$data[] = [
				'id' => $form['id'],
				'name' => $form['name'],
				'created' => $form['created_at'],
				'state' => $form['state']
			];
		}

		return $data;
	}
}