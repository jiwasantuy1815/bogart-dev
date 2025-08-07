<?php

/**
 * @package   Barn2\barn2-settings-api
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Fields;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Traits\With_Min_Max;
/**
 * Color_Size field class.
 */
class Color_Size extends Field
{
    use With_Min_Max;
    /** {@inheritDoc} */
    protected $type = 'color_size';
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $field = parent::jsonSerialize();
        $field['min'] = $this->get_min();
        $field['max'] = $this->get_max();
        return $field;
    }
}
