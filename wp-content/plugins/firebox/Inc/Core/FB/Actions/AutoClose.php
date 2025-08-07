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

class AutoClose extends Actions
{
    public function __construct()
    {
        add_filter('firebox/box/before_render', [$this, 'onFireBoxBeforeRender'], 1);
    }

    /**
     * The BeforeRender event fires before the box's layout is ready.
     *
     * @param  object $box           The box's settings object
     *
     * @return void
     */
    public function onFireBoxBeforeRender($box)
    {
        if (!isset($box->params))
        {
            return $box;
        }

        if (!$auto_close = $box->params->get('box_auto_close'))
        {
            return $box;
        }

        if (!$delay = $box->params->get('box_auto_close_seconds'))
        {
            return $box;
        }

        if ($delay <= 0)
        {
            return $box;
        }

        $this->actions[] = [
            'box' => $box->ID,
            'delay' => $box->params->get('box_auto_close_seconds'),
            'do' => 'closebox',
            'when' => 'afterOpen'
        ];

        return $box;
    }
}