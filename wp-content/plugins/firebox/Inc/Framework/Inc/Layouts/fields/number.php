<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.133
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}
?>
<input type="number"<?php echo wp_kses_data($this->data->get('required_attribute', '') . $this->data->get('extra_atts', '') . $this->data->get('number_atts')); ?> id="fpf-control-input-item_<?php echo esc_attr($this->data->get('name')); ?>" class="fpf-field-item fpf-control-input-item text<?php echo esc_attr($this->data->get('input_class')); ?>" placeholder="<?php echo esc_attr($this->data->get('placeholder', '')); ?>" value="<?php echo esc_attr($this->data->get('value')); ?>" name="<?php echo esc_attr($this->data->get('name')); ?>" />
<?php if (!empty($this->data->get('addon'))): ?>
<span class="fpf-input-addon"><?php echo esc_html(fpframework()->_($this->data->get('addon'))); ?></span>
<?php endif; ?>