<?php

/**
 * @package   Barn2\barn2-settings-api
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Traits;

/**
 * Trait With_Supported_Taxonomies
 * Adds supported taxonomies to the field.
 *
 * @package Barn2\Settings_API\Traits
 */
trait With_Supported_Taxonomies
{
    /**
     * The taxonomies.
     *
     * @var array
     */
    protected $taxonomies = [];
    /**
     * Setup taxonomies.
     *
     * Array format:
     *
     * [
     *    [
     *      'label' => 'Column label',
     *      'value' => 'column_key'
     *    ],
     * ]
     *
     * @param array $taxonomies The taxonomies to add.
     * @return self
     */
    public function set_taxonomies(array $taxonomies) : self
    {
        $this->taxonomies = $taxonomies;
        return $this;
    }
    /**
     * Get the taxonomies.
     *
     * @return array
     */
    public function get_taxonomies() : array
    {
        return $this->taxonomies;
    }
}
