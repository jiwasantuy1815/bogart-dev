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

namespace FireBox\Core\FB\Actions;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Libs\Registry;

class BoxActions extends Actions
{
    use ActionsTrait;
    
    public function __construct()
    {
        // renders the Actions per box
        add_filter('firebox/box/before_render', [$this, 'onFireBoxBeforeRender']);
    }

    /**
     * Append the box actions
     * 
     * @param   object  $box
     * 
     * @return  void
     */
    public function onFireBoxBeforeRender($box)
    {
        if (!isset($box->params))
        {
            return $box;
        }
        
        if (!$actions = $box->params->get('actions'))
        {
            return $box;
        }

        foreach ($actions as $action)
        {
            $this->actions[] = (array) $action;
        }

        return $box;
    }
}