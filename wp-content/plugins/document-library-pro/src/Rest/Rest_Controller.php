<?php
namespace Barn2\Plugin\Document_Library_Pro\Rest;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Rest\Rest_Server;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Rest\Base_Server;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Rest\Route;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;

/**
 * Main controller which registers the REST routes for the plugin.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Rest_Controller extends Base_Server implements Registerable, Standard_Service, Rest_Server {

	const NAMESPACE = 'document-library-pro/v1';

	/**
	 * The list of REST route objects handled by this server.
	 *
	 * @var Route[]
	 */
	private $routes = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->routes = [
			new Routes\Cache( self::NAMESPACE ),
		];
	}

	/**
	 * Retrieve the namespace.
	 *
	 * @return string
	 */
	public function get_namespace() {
		return self::NAMESPACE;
	}

	/**
	 * Retrieve the routes.
	 *
	 * @return Route[]
	 */
	public function get_routes() {
		return $this->routes;
	}
}
