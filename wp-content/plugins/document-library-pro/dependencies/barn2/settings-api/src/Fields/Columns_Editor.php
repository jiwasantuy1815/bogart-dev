<?php

/**
 * @package   Barn2\barn2-settings-api
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Fields;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Traits\With_Content_Type;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Traits\With_Supported_Columns;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Traits\With_Supported_Taxonomies;
/**
 * Columns_Editor field class.
 */
class Columns_Editor extends Field
{
    use With_Content_Type;
    use With_Supported_Columns;
    use With_Supported_Taxonomies;
    /**
     * The type of field.
     *
     * @var string
     */
    protected $type = 'columns_editor';
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $field = parent::jsonSerialize();
        $field['contentType'] = $this->get_content_type();
        $field['columns'] = $this->get_columns();
        $field['taxonomies'] = $this->get_taxonomies();
        return $field;
    }
}
