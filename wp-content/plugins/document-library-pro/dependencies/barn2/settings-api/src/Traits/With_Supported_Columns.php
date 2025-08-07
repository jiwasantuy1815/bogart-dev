<?php

/**
 * @package   Barn2\barn2-settings-api
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Traits;

/**
 * Trait With_Supported_Columns
 * Adds supported columns to the field.
 *
 * @package Barn2\Settings_API\Traits
 */
trait With_Supported_Columns
{
    /**
     * The columns.
     *
     * @var array
     */
    protected $columns = [];
    /**
     * Setup columns.
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
     * @param array $columns The columns to add.
     * @return self
     */
    public function set_columns(array $columns) : self
    {
        $this->columns = $columns;
        return $this;
    }
    /**
     * Get the columns.
     *
     * @return array
     */
    public function get_columns() : array
    {
        return $this->columns;
    }
}
