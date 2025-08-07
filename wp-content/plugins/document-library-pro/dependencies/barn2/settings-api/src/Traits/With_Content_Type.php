<?php

/**
 * @package   Barn2\barn2-settings-api
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Traits;

/**
 * Trait With_Content_Type
 * Adds content_Type to the field.
 *
 * @package Barn2\Settings_API\Traits
 */
trait With_Content_Type
{
    /**
     * The content_type of the field.
     *
     * @var array
     */
    protected $content_type = [];
    /**
     * Set the content_type of the field.
     *
     * @param array $content_type The content type of the field.
     * @return self
     */
    public function set_content_type(array $content_type) : self
    {
        $this->content_type = $content_type;
        return $this;
    }
    /**
     * Get the content_type of the field.
     *
     * @return array The content type of the field.
     */
    public function get_content_type() : array
    {
        return $this->content_type;
    }
}
