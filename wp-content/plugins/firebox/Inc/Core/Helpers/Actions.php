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

class Actions
{
	/**
	 * Runs the actions.
	 * 
	 * @return  void
	 */
	public static function run()
	{
		$actions = [];
		
		
		// Initialize Box Actions
		$actions = [
			// Initialize Auto Close
			new \FireBox\Core\FB\Actions\AutoClose(),

			// Initialize Box Actions
			new \FireBox\Core\FB\Actions\BoxActions()
		];
		

		// Initialize Sounds
		$actions[] = new \FireBox\Core\FB\Actions\Sounds();
			
		// Make actions filterable
		$actions = apply_filters('firebox/actions/box/edit', $actions);
	
		// Run actions
		new \FireBox\Core\FB\Actions\ActionsBase($actions);
	}
}