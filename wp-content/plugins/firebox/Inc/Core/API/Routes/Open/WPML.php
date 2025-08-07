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

class WPML extends EndpointController
{
	/**
	 * Endpoint name
	 * 
	 * @return  string
	 */
	public function get_name()
	{
		return 'wpml';
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
        $per_page = 15; // Number of roles per page

        // Paginate roles
        $offset = ($page - 1) * $per_page;

        $items = fpframework()->helper->language->getItems($offset, $per_page);

        $items = array_map(function ($item) {
            return ['value' => $item['id'], 'label' => $item['title']];
        }, $items);

        return $items;
    }

    public function get_ids_data($request)
    {
        global $wp_roles;
        
        $ids = array_filter(explode(',', $request['ids']));

        if (!$ids)
        {
            return [];
        }

        $items = fpframework()->helper->language->getSelectedItems($ids);

        $items = array_map(function ($item) {
            return ['value' => $item['id'], 'label' => $item['title']];
        }, $items);

        return $items;
    }

    public function get_search_data($request)
    {
        global $wp_roles;

        $query = sanitize_text_field($request->get_param('q'));

        $items = fpframework()->helper->language->getSearchItems($query);

        $items = array_map(function ($item) {
            return ['value' => $item['id'], 'label' => $item['title']];
        }, $items);

        return $items;
    }
}