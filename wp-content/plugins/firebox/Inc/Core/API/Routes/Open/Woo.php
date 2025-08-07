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

class Woo extends EndpointController
{
	/**
	 * Endpoint name
	 * 
	 * @return  string
	 */
	public function get_name()
	{
		return 'woo';
	}
	
	/**
	 * Register routes
	 * 
	 * @return  void
	 */
	public function register()
	{
        // downloads
		$this->register_route('/downloads/page/(?P<page>\d+)', WP_REST_Server::READABLE, [$this, 'get_downloads_page_data']);
		$this->register_route('/downloads/(?P<ids>(?!search$)[a-zA-Z0-9,_-]+)', WP_REST_Server::READABLE, [$this, 'get_downloads_ids_data']);
		$this->register_route('/downloads/search', WP_REST_Server::READABLE, [$this, 'get_downloads_search_data']);

        // category
		$this->register_route('/category/page/(?P<page>\d+)', WP_REST_Server::READABLE, [$this, 'get_category_page_data']);
		$this->register_route('/category/(?P<ids>(?!search$)[a-zA-Z0-9,_-]+)', WP_REST_Server::READABLE, [$this, 'get_category_ids_data']);
		$this->register_route('/category/search', WP_REST_Server::READABLE, [$this, 'get_category_search_data']);
	}

	public function get_permission_callback($request)
	{
        return current_user_can('manage_options');
	}

    // Downloads
    public function get_downloads_page_data($request)
    {
		global $wp_roles;

        $page = (int) ($request['page'] ?? 1);
        $per_page = 15;

        // Paginate roles
        $offset = ($page - 1) * $per_page;

        $items = fpframework()->helper->woocommerce->getItems($offset, $per_page);

        $items = array_map(function ($item) {
            return ['value' => $item['id'], 'label' => $item['title']];
        }, $items);

        return $items;
    }

    public function get_downloads_ids_data($request)
    {
        global $wp_roles;
        
        $ids = array_filter(explode(',', $request['ids']));

        if (!$ids)
        {
            return [];
        }

        $items = fpframework()->helper->woocommerce->getSelectedItems($ids);

        $items = array_map(function ($item) {
            return ['value' => $item['id'], 'label' => $item['title']];
        }, $items);

        return $items;
    }

    public function get_downloads_search_data($request)
    {
        global $wp_roles;

        $query = sanitize_text_field($request->get_param('q'));

        $items = fpframework()->helper->woocommerce->getSearchItems($query);

        $items = array_map(function ($item) {
            return ['value' => $item['id'], 'label' => $item['title']];
        }, $items);

        return $items;
    }

    // Category
    public function get_category_page_data($request)
    {
		global $wp_roles;

        $page = (int) ($request['page'] ?? 1);
        $per_page = 15;

        // Paginate roles
        $offset = ($page - 1) * $per_page;

        $items = fpframework()->helper->woocommercecategory->getItems($offset, $per_page);

        $items = array_map(function ($item) {
            return ['value' => $item['id'], 'label' => $item['title']];
        }, $items);

        return $items;
    }

    public function get_category_ids_data($request)
    {
        global $wp_roles;
        
        $ids = array_filter(explode(',', $request['ids']));

        if (!$ids)
        {
            return [];
        }

        $items = fpframework()->helper->woocommercecategory->getSelectedItems($ids);

        $items = array_map(function ($item) {
            return ['value' => $item['id'], 'label' => $item['title']];
        }, $items);

        return $items;
    }

    public function get_category_search_data($request)
    {
        global $wp_roles;

        $query = sanitize_text_field($request->get_param('q'));

        $items = fpframework()->helper->woocommercecategory->getSearchItems($query);

        $items = array_map(function ($item) {
            return ['value' => $item['id'], 'label' => $item['title']];
        }, $items);

        return $items;
    }
}