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

class Rating extends \FireBox\Core\Blocks\Block
{
	/**
	 * Block identifier.
	 * 
	 * @var  string
	 */
	protected $name = 'rating';

	public function render_callback($atts, $content)
	{
		$payload = [
			'id' => $atts['uniqueId'],
			'name' => isset($atts['fieldName']) ? $atts['fieldName'] : \FireBox\Core\Helpers\Form\Field::getFieldName($blockPayload),
			'label' => isset($atts['fieldLabel']) ? $atts['fieldLabel'] : \FireBox\Core\Helpers\Form\Field::getFieldLabel($blockPayload),
			'hideLabel' => isset($atts['hideLabel']) ? $atts['hideLabel'] : false,
			'requiredFieldIndication' => isset($atts['fieldLabelRequiredFieldIndication']) ? $atts['fieldLabelRequiredFieldIndication'] : true,
			'required' => isset($atts['required']) ? $atts['required'] : true,
			'description' => isset($atts['helpText']) ? $atts['helpText'] : '',
			'value' => $atts['defaultValue'],
			'width' => isset($atts['width']) ? $atts['width'] : '',
			'placeholder' => isset($atts['placeholder']) ? $atts['placeholder'] : '',
			'cssClass' => isset($atts['cssClass']) ? [$atts['cssClass']] : [],
			'inputCssClass' => isset($atts['inputCssClass']) && !empty($atts['inputCssClass']) ? [$atts['inputCssClass']] : [],
			'icon' => isset($atts['icon']) ? $atts['icon'] : 'star',
			'size' => isset($atts['size']) ? $atts['size'] : 24,
			'maxRating' => isset($atts['maxRating']) ? $atts['maxRating'] : 5,
			'halfRatings' => isset($atts['halfRatings']) ? $atts['halfRatings'] : false,
			'selectedColor' => isset($atts['selectedColor']) ? $atts['selectedColor'] : '#f6cc01',
			'unselectedColor' => isset($atts['unselectedColor']) ? $atts['unselectedColor'] : '#bdbdbd'
		];

		$field = new \FireBox\Core\Form\Fields\Fields\Rating($payload);

		return $field->render();
	}

	/**
	 * Registers assets on back-end only.
	 * 
	 * @return  void
	 */
	public function assets()
	{
		wp_register_style(
			'fb-block-rating',
			FPF_MEDIA_URL . 'public/css/widgets/rating.css',
			[],
			FBOX_VERSION
		);
		wp_register_script(
			'fb-block-rating',
			FPF_MEDIA_URL . 'public/js/widgets/rating.js',
			[],
			FBOX_VERSION
		);
		wp_enqueue_style('fb-block-rating');
		wp_enqueue_script('fb-block-rating');
	}
}