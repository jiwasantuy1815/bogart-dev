<?php

/**
 * Image Label Field Class.
 *
 * @package   Barn2\barn2-settings-api
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Fields;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Traits\With_Callable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Traits\With_Options;
/**
 * Radio field class.
 * Represents a radio field.
 */
class Image_Label extends Field
{
    /**
     * {@inheritDoc}
     *
     * @var string
     */
    protected $type = 'image_label';
    use With_Options;
    use With_Callable;
    /** {@inheritDoc} */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $field = parent::jsonSerialize();
        $field['options'] = $this->get_options();
        $field['hasCallable'] = $this->has_callable();
        return $field;
    }
}
