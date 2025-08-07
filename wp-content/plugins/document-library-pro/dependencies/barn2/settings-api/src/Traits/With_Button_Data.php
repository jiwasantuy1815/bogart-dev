<?php

/**
 * @package   Barn2\barn2-settings-api
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Traits;

/**
 * Trait With_Button_Data
 * Adds button data to the button field.
 *
 * @package Barn2\Settings_API\Traits
 */
trait With_Button_Data
{
    /**
     * The options.
     *
     * @var array
     */
    protected $button_data = [];
    /**
     * Setup button data.
     *
     * Array format:
     *
     * [
     *  'text' => 'Button text',
     *  'url'  => 'Button URL',
     *  'new_tab' => true
     * ]
     *
     * @param array $button_data The button data to add.
     * @return self
     */
    public function set_button_data(array $button_data) : self
    {
        $this->button_data = $button_data;
        return $this;
    }
    /**
     * Get the button data.
     *
     * @return array
     */
    public function get_button_data() : array
    {
        return $this->button_data;
    }
}
