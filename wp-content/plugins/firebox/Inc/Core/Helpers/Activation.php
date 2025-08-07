<?php
/**
 * @package         FireBox
 * @version         3.0.0 Pro
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Activation
{
	/**
	 * Creates library directories.
	 * 
	 * @return  void
	 */
	public static function createLibraryDirectories()
	{
		// Create /wp-content/uploads/firebox and secure it
		\FPFramework\Helpers\Directory::createDirs(\FPFramework\Helpers\WPHelper::getPluginUploadsDirectory('firebox'));

		// Create /wp-content/uploads/firebox/templates and secure it
		\FPFramework\Helpers\Directory::createDirs(\FPFramework\Helpers\WPHelper::getPluginUploadsDirectory('firebox', 'templates'));
	}
}