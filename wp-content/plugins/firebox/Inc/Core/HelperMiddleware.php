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

namespace FireBox\Core;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class HelperMiddleware
{
    public $factory;
    public $wpdb;

    
    public $viewedanotherbox;
    public $form;
    

    public function __construct($factory = null)
    {
        if (empty($factory))
        {
            $factory = new \FPFramework\Base\Factory();
        }

        $this->factory = $factory;
        $this->wpdb = $this->factory->getDbo();

        
        $this->viewedanotherbox = new \FireBox\Core\Helpers\ViewedAnotherBoxHelper();
        $this->form = new \FireBox\Core\Helpers\Form();
        
    }
}