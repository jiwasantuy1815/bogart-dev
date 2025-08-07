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

class Roles extends EndpointController
{
	/**
	 * Endpoint name
	 * 
	 * @return  string
	 */
	public function get_name()
	{
		return 'roles';
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
		global $wp_roles;

        $page = (int) ($request['page'] ?? 1);
        $per_page = 15;

        // Get all roles
        $roles = $wp_roles->roles;

        // Paginate roles
        $offset = ($page - 1) * $per_page;
        $paginated_roles = array_slice($roles, $offset, $per_page, true);

        if (empty($paginated_roles))
        {
            return new \WP_Error(
                'no_more_roles',
                'No more roles available.',
                ['status' => 404]
            );
        }

        // Convert roles to value-label pairs
        $paginated_roles = array_map(function ($role, $key) {
            return ['value' => $key, 'label' => $role['name']];
        }, $paginated_roles, array_keys($paginated_roles));

        // Include guest role for the first page
        if ($page === 1)
        {
            array_unshift($paginated_roles, ['value' => 'guest', 'label' => 'Guest']);
        }

        return $paginated_roles;
    }

    public function get_ids_data($request)
    {
        global $wp_roles;
        
        $ids = array_filter(explode(',', $request['ids']));

        if (!$ids)
        {
            return [];
        }

        $roles = $wp_roles->roles;
        
        $filtered_roles = array_filter($roles, function ($key) use ($ids) {
            return in_array($key, $ids);
        }, ARRAY_FILTER_USE_KEY);

        // Include guest role if 'guest' is in the IDs
        if (in_array('guest', $ids))
        {
            $filtered_roles['guest'] = ['name' => 'Guest'];
        }
        
        if (empty($filtered_roles))
        {
            return new \WP_Error(
                'no_roles',
                'No roles found for the given IDs.',
                ['status' => 404]
            );
        }

        // Convert roles to value-label pairs
        $filtered_roles = array_map(function ($role, $key) {
            return ['value' => $key, 'label' => $role['name']];
        }, $filtered_roles, array_keys($filtered_roles));

        return $filtered_roles;
    }

    public function get_search_data($request)
    {
        global $wp_roles;

        $query = sanitize_text_field($request->get_param('q'));
        $roles = $wp_roles->roles;

        // Filter roles by query
        $filtered_roles = array_filter($roles, function ($role, $key) use ($query) {
            return stripos($key, $query) !== false || stripos($role['name'], $query) !== false;
        }, ARRAY_FILTER_USE_BOTH);

        // Include guest role if it matches the query
        if (stripos('guest', $query) !== false || stripos('Guest', $query) !== false)
        {
            $filtered_roles['guest'] = ['name' => 'Guest'];
        }

        if (empty($filtered_roles))
        {
            return new \WP_Error(
                'no_roles_found',
                'No roles match your query.',
                ['status' => 404]
            );
        }

        // Convert roles to value-label pairs
        $filtered_roles = array_map(function ($role, $key) {
            return ['value' => $key, 'label' => $role['name']];
        }, $filtered_roles, array_keys($filtered_roles));

        return $filtered_roles;
    }
}