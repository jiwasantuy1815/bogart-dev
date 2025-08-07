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

namespace FireBox\Core\API;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Manager
{
	/**
	 * Root Namespace of the API calls
	 * 
	 * @var  string
	 */
	const ROOT_NAMESPACE = 'firebox';

	/**
	 * REST base name.
	 * 
	 * With empty REST base:
	 * /{ROOT_NAMESPACE}/v{VERSION}/{ENDPOINT}
	 * 
	 * With valid REST base:
	 * /{ROOT_NAMESPACE}/v{VERSION}/{REST_BASE}/{ENDPOINT}
	 * 
	 * @var  string
	 */
	const REST_BASE = '';

	/**
	 * Version of API
	 * 
	 * @var  string
	 */
	const VERSION = '1';
}