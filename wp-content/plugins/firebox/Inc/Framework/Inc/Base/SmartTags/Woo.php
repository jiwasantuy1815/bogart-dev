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

namespace FPFramework\Base\SmartTags;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Woo extends SmartTag
{
    /**
     * WooCommerce Condition used to fetch WooCommerce-related data.
     * 
     * @return  object
     */
    private $woo_condition;
    
    /**
     * Returns the Smart Tag value.
     * 
     * @return  string
     */
    public function fetchValue($key)
	{
		if (!$key)
		{
			return '';
		}

		if (!is_string($key))
		{
			return '';
		}

        $key = str_replace('.', '', $key);
        $method = 'get' . $key;

        if (!method_exists($this, $method))
        {
            return;
        }

        $this->woo_condition = $this->fetchWooCondition();

        return $this->$method();
    }

    private function fetchWooCondition()
    {
        $payload = [
            'params' => [
                'exclude_shipping_cost' => $this->parsedOptions->get('excludeshipping', 'false') === 'true',
                'exclude_tax' => $this->parsedOptions->get('excludetax', 'false') === 'true'
            ]
        ];
        
        return new \FPFramework\Base\Conditions\Conditions\WooCommerce\CartValue($payload);
    }

    /**
     * Returns the total cart items count.
     * 
     * @return  int
     */
    public function getCartCount()
    {
        $min = (int) $this->parsedOptions->get('min', 0);

        $cart_count = count($this->woo_condition->getCartProducts());

        if (!$min)
        {
            return $cart_count;
        }
        
        return $min > $cart_count ? abs($min - $cart_count) : 0;
    }

    /**
     * Returns the total cart total.
     * 
     * @return  int
     */
    public function getCartTotal()
    {
        $return = $total = (float) $this->woo_condition->getCartTotal();

        $min = (float) $this->parsedOptions->get('min', 0);
        if ($min)
        {
            $return = $min > $total ? (float) abs($min - $total) : 0;
        }

        $filter = $this->parsedOptions->get('filter', '');
        if ($filter === 'percentage')
        {
            $return = (($min - $total) / $min) * 100;
            $return = $return > 0 ? round($return, 2) : 0;
        }

        return number_format($return, 2);
    }

    /**
     * Returns the total cart subtotal.
     * 
     * @return  int
     */
    public function getCartSubtotal()
    {
        $return = $subtotal = (float) $this->woo_condition->getCartSubtotal();
        
        // Apply minimum value
        $min = (float) $this->parsedOptions->get('min', 0);
        if ($min)
        {
            $return = $min > $subtotal ? (float) abs($min - $subtotal) : 0;
        }

        $filter = $this->parsedOptions->get('filter', '');
        if ($filter === 'percentage')
        {
            $return = (($min - $subtotal) / $min) * 100;
            $return = $return > 0 ? round($return, 2) : 0;
        }

        return number_format($return, 2);
    }

    /**
     * Returns a product's stock.
     * 
     * @return  int
     */
    public function getStock()
    {
        if (!$product = (int) $this->parsedOptions->get('product', 0))
        {
            return;
        }

        if (!$stock = $this->fetchWooCondition()->getProductStock($product))
        {
            return;
        }

        return $stock;
    }
}