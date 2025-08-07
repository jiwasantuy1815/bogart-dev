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

namespace FireBox\Core\API\Routes\Open;

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
	 * Register routes
	 * 
	 * @return  void
	 */
	public function register()
	{
		$this->register_route('/page/(?P<page>\d+)', WP_REST_Server::READABLE, [$this, 'get_page_data']);
		$this->register_route('/(?P<ids>(?!search$)[a-zA-Z0-9,_-]+)', WP_REST_Server::READABLE, [$this, 'get_ids_data']);
		$this->register_route('/search', WP_REST_Server::READABLE, [$this, 'get_search_data']);
	}

	public function get_permission_callback($request)
	{
        return current_user_can('manage_options');
	}

    public function get_page_data($request)
    {
        $page = (int) ($request['page'] ?? 1);
        $per_page = 15; // Number of roles per page

        $forms = \FireBox\Core\Helpers\Form\Form::getParsedForms();

        // Paginate forms
        $offset = ($page - 1) * $per_page;
        $paginated_forms = array_slice($forms, $offset, $per_page, true);

        if (empty($paginated_forms)) {
            return new \WP_Error(
                'no_more_forms',
                'No more forms available.',
                ['status' => 404]
            );
        }

        // Convert forms to value-label pairs
        $paginated_forms = array_map(function ($label, $key) {
            return ['value' => $key, 'label' => $label];
        }, $paginated_forms, array_keys($paginated_forms));

        return $paginated_forms;
    }

    public function get_ids_data($request)
    {
        $ids = array_filter(explode(',', $request['ids']));

        if (!$ids)
        {
            return [];
        }

        $forms = \FireBox\Core\Helpers\Form\Form::getParsedForms();

        $filtered_forms = array_filter($forms, function ($label, $key) use ($ids) {
            return in_array($key, $ids);
        }, ARRAY_FILTER_USE_BOTH);

        if (empty($filtered_forms)) {
            return new \WP_Error(
                'no_forms',
                'No forms found for the given IDs.',
                ['status' => 404]
            );
        }

        // Convert forms to value-label pairs
        $filtered_forms = array_map(function ($label, $key) {
            return ['value' => $key, 'label' => $label];
        }, $filtered_forms, array_keys($filtered_forms));

        return $filtered_forms;
    }

    public function get_search_data($request)
    {
        $query = sanitize_text_field($request->get_param('q'));

        $forms = \FireBox\Core\Helpers\Form\Form::getParsedForms();

        // Filter forms by query
        $filtered_forms = array_filter($forms, function ($label, $key) use ($query) {
            return stripos($key, $query) !== false || stripos($label, $query) !== false;
        }, ARRAY_FILTER_USE_BOTH);

        if (empty($filtered_forms)) {
            return new WP_Error(
                'no_forms_found',
                'No forms match your query.',
                ['status' => 404]
            );
        }

        // Convert forms to value-label pairs
        $filtered_forms = array_map(function ($label, $key) {
            return ['value' => $key, 'label' => $label];
        }, $filtered_forms, array_keys($filtered_forms));

        return $filtered_forms;
    }
}