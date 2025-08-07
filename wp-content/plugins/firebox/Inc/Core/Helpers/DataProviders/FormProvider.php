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

namespace FireBox\Core\Helpers\DataProviders;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Interfaces\GetSelectedItems;
use FPFramework\Base\Interfaces\GetSearchItems;
use FPFramework\Base\Interfaces\GetItems;
use FPFramework\Helpers\SearchDropdownHelper;

class FormProvider implements GetSelectedItems, GetSearchItems, getItems
{
	/**
	 * Returns items based on offset and limit
	 * 
	 * @param   integer  $offset
	 * @param   integer  $limit
	 * 
	 * @return  array
	 */
	public function getItems($offset = 0, $limit = SearchDropdownHelper::SELECTION_ITEMS)
	{
		$forms = array_slice(firebox()->helper->form::getForms(), $offset, $limit);

		$parsed = [];

		foreach ($forms as $key => $value)
		{
			$parsed[] = [
				'id' => $key,
				'title' => $value
			];
		}

		return $parsed;
	}

	/**
	 * Gets items from the Selected Items
	 * 
	 * @param   array   $items
	 * 
	 * @return  array
	 */
    public function getSelectedItems($items = [])
    {
		$parsed = [];

		foreach (firebox()->helper->form::getForms() as $key => $value)
		{
			if (in_array($key, $items))
			{
				$parsed[] = [
					'id' => $key,
					'title' => $value
				];
			}
		}
		
		return $parsed;
    }

	/**
	 * Searches and returns an array of items via the name
	 * 
	 * @param   string  $name
	 * @param   array  	$no_ids  List of already added items
	 * 
	 * @return  array
	 */
    public function getSearchItems($name, $no_ids = null)
    {
		$parsed = [];

		foreach (firebox()->helper->form::getForms() as $key => $value)
		{
			if (stripos($value, trim($name)) !== false)
			{
				$parsed[] = [
					'id' => $key,
					'title' => $value
				];
			}
		}
		
		return $parsed;
	}
}