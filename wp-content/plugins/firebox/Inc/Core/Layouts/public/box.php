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

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}
$box = $this->data->get('box', []);
$box = new \FPFramework\Libs\Registry($box);

$close_button = (int) $box->get('params.data.closebutton.show', '');
?>
<div data-id="<?php echo esc_attr($box->get('ID')); ?>" 
	class="fb-inst fb-hide <?php echo esc_attr(implode(' ', (array) $box->get('classes'))); ?>"
	data-options='<?php echo wp_json_encode($box->get('settings'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
	data-type='<?php echo esc_attr($box->get('params.data.mode')); ?>'>

	<?php if ($close_button == 2) { firebox()->renderer->public->render('closebutton', ['box' => $this->data->get('box', [])]); } ?>

	<div class="fb-dialog <?php echo esc_attr(implode(' ', (array) $box->get('dialog_classes', []))); ?>" style="<?php echo esc_attr($box->get('style')); ?>" role="dialog" aria-modal="true" id="dialog<?php echo esc_attr($box->get('ID')); ?>" aria-label="dialog<?php echo esc_attr($box->get('ID')); ?>">
		
		<?php if ($close_button == 1) { firebox()->renderer->public->render('closebutton', ['box' => $this->data->get('box', [])]); } ?>

		<div class="fb-container">
			<div class="fb-content is-layout-constrained">
				<?php echo $box->get('post_content'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
</div>
<?php

if ($box->get('params.data.triggermethod') === 'floatingbutton' || $box->get('params.data.floating_button_show_on_close', '0'))
{
	firebox()->renderer->public->render('floatingbutton', $this->data->get('box', []));
}
