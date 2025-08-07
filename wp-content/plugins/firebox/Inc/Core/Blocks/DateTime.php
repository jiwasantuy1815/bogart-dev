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

namespace FireBox\Core\Blocks;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class DateTime extends \FireBox\Core\Blocks\Block
{
	/**
	 * Block identifier.
	 * 
	 * @var  string
	 */
	protected $name = 'datetime';

	public function render_callback($attributes, $content)
	{
		$blockPayload = [
			'blockName' => $this->name,
			'attrs' => $attributes
		];

		$payload = [
			'id' => $attributes['uniqueId'],
			'dateSelectionMode' => isset($attributes['dateSelectionMode']) ? $attributes['dateSelectionMode'] : 'single',
			'dateFormat' => isset($attributes['dateFormat']) ? $attributes['dateFormat'] : 'Y-m-d H:i',
			'firstDayOfWeek' => isset($attributes['firstDayOfWeek']) ? $attributes['firstDayOfWeek'] : 1,
			'minDate' => isset($attributes['minDate']) ? $attributes['minDate'] : '',
			'maxDate' => isset($attributes['maxDate']) ? $attributes['maxDate'] : '',
			'enableTime' => isset($attributes['enableTime']) ? $attributes['enableTime'] : true,
			'time24hr' => isset($attributes['time24hr']) ? $attributes['time24hr'] : false,
			'minuteStep' => isset($attributes['minuteStep']) ? $attributes['minuteStep'] : 5,
			'inline' => isset($attributes['inline']) ? $attributes['inline'] : false,
			'disableMobileNativePicker' => isset($attributes['disableMobileNativePicker']) ? $attributes['disableMobileNativePicker'] : false,
			'name' => isset($attributes['fieldName']) ? $attributes['fieldName'] : \FireBox\Core\Helpers\Form\Field::getFieldName($blockPayload),
			'label' => isset($attributes['fieldLabel']) ? $attributes['fieldLabel'] : \FireBox\Core\Helpers\Form\Field::getFieldLabel($blockPayload),
			'hideLabel' => isset($attributes['hideLabel']) ? $attributes['hideLabel'] : false,
			'requiredFieldIndication' => isset($attributes['fieldLabelRequiredFieldIndication']) ? $attributes['fieldLabelRequiredFieldIndication'] : true,
			'required' => isset($attributes['required']) ? $attributes['required'] : true,
			'description' => isset($attributes['helpText']) ? $attributes['helpText'] : '',
			'value' => isset($attributes['defaultValue']) ? $attributes['defaultValue'] : '',
			'width' => isset($attributes['width']) ? $attributes['width'] : '',
			'placeholder' => isset($attributes['placeholder']) ? $attributes['placeholder'] : '',
			'css_class' => isset($attributes['cssClass']) ? [$attributes['cssClass']] : [],
			'input_css_class' => isset($attributes['inputCssClass']) && !empty($attributes['inputCssClass']) ? [$attributes['inputCssClass']] : [],
		];

		// Replace Smart Tags
		$payload = \FPFramework\Base\SmartTags\SmartTags::getInstance()->replace($payload);
		
		$field = new \FireBox\Core\Form\Fields\Fields\DateTime($payload);

		// $content contains CSS variables for the field
		return $content . $field->render();
	}
}