<?php

/**
 * @package   Barn2\barn2-settings-api
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Fields;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Traits\With_Content_Type;
/**
 * Post_Select field class.
 * Represents a custom post select field with a view page link.
 */
class Post_Select extends Field
{
    /** {@inheritDoc} */
    protected $type = 'post_select';
    use With_Content_Type;
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $field = parent::jsonSerialize();
        $field['contentType'] = $this->get_content_type();
        return $field;
    }
}
