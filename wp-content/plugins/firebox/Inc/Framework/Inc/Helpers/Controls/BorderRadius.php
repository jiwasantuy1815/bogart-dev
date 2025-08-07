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

namespace FPFramework\Helpers\Controls;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class BorderRadius extends Spacing
{
    /**
     * Border Radius Spacing Control Positions.
     * 
     * @var  array
     */
    protected static $spacing_positions = ['top_left', 'top_right', 'bottom_right', 'bottom_left'];
}