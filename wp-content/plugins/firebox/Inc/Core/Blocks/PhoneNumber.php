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

class PhoneNumber extends \FireBox\Core\Blocks\Block
{
	/**
	 * Block identifier.
	 * 
	 * @var  string
	 */
	protected $name = 'phonenumber';

	public function render_callback($atts, $content)
	{
        $code = '';
        
        $defaultCountryOption = isset($atts['defaultCountryOption']) ? $atts['defaultCountryOption'] : 'detect';

        switch ($defaultCountryOption)
        {
            case 'detect':
                $geo = new \FPFramework\Libs\Vendors\GeoIP\GeoIP();
                $code = $geo->getCountryCode();
                break;

            default:
                $code = isset($atts['defaultCountry']) && !empty($atts['defaultCountry']) ? $atts['defaultCountry'] : $code;
                break;
        }

		$payload = [
			'id' => $atts['uniqueId'],
			'name' => isset($atts['fieldName']) ? $atts['fieldName'] : \FireBox\Core\Helpers\Form\Field::getFieldName($blockPayload),
			'label' => isset($atts['fieldLabel']) ? $atts['fieldLabel'] : \FireBox\Core\Helpers\Form\Field::getFieldLabel($blockPayload),
			'hideLabel' => isset($atts['hideLabel']) ? $atts['hideLabel'] : false,
			'requiredFieldIndication' => isset($atts['fieldLabelRequiredFieldIndication']) ? $atts['fieldLabelRequiredFieldIndication'] : true,
			'required' => isset($atts['required']) ? $atts['required'] : true,
			'description' => isset($atts['helpText']) ? $atts['helpText'] : '',
			'value' => [
                'code'  => $code ? strtoupper($code) : '',
                'value' => isset($atts['defaultValue']) ? $atts['defaultValue'] : ''
            ],
			'width' => isset($atts['width']) ? $atts['width'] : '',
			'placeholder' => isset($atts['placeholder']) ? $atts['placeholder'] : '',
			'cssClass' => isset($atts['cssClass']) ? [$atts['cssClass']] : [],
			'inputCssClass' => isset($atts['inputCssClass']) && !empty($atts['inputCssClass']) ? [$atts['inputCssClass']] : [],
			'browserautocomplete' => isset($atts['disableBrowserAutocomplete']) ? $atts['disableBrowserAutocomplete'] : false
		];

		$field = new \FireBox\Core\Form\Fields\Fields\PhoneNumber($payload);

		return $field->render();
	}

	/**
	 * Registers assets on back-end only.
	 * 
	 * @return  void
	 */
	public function assets()
	{
		\FPFramework\Base\Widgets\PhoneNumber::register_assets();

		wp_enqueue_style('fpframework-widget');
		wp_enqueue_style('fpframework-phonenumber-widget');
	}
}