<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.133
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base\Conditions\Conditions\EDD;

defined('ABSPATH') or die;

class TotalSpend extends EDDBase
{
    /**
     *  Returns the condtion value.
     * 
     *  @return  float
     */
    public function value()
    {
		if (!is_user_logged_in())
		{
			return;
		}

		if (!function_exists('EDD'))
		{
			return;
		}
		
		if (!$customer = edd_get_customer_by('user_id', get_current_user_id()))
		{
			return;
		}

		var_dump($customer->purchase_value);
		
		return (float) $customer->purchase_value;
    }
}