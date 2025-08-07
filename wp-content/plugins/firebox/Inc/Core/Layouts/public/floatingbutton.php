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

$box = $this->data;

$cssClasses = [
	'fb-' . $box->get('ID'),
	'fb-floating-button',
	'fb-' . $box->get('params.data.floating_button_position')
];

if ($box->get('params.data.triggermethod') !== 'floatingbutton')
{
	$cssClasses[] = 'fb-hide';
}

if ($box->get('params.data.triggermethod') === 'floatingbutton')
{
	// Always show the button when the trigger is the floatingbutton itself
	$state = true;
	
	// but, if the popup is configured to remain hidden on close, do not show the floating button again.
	if ($box->get('params.data.assign_cookietype') !== 'never')
	{
		$state = false;
	}

	$box->set('params.data.floating_button_show_on_close', $state);
}

$js = '
	FireBox.onReady(() => {
		const popup = FireBox.getInstance(' . $box->get('ID') . ');
		const button = document.querySelector(".fb-floating-button.fb-' . $box->get('ID') . '");
		const showOnClose = '. ($box->get('params.data.floating_button_show_on_close') ? 'true' : 'false') .';

		popup.on("close", () => {
			if (showOnClose) {
				button.classList.remove("fb-hide");
			}
		}).on("open", () => {
			button.classList.add("fb-hide");
		});
	})
';
wp_add_inline_script('firebox-main', html_entity_decode(stripslashes($js)));

$allowed_html = wp_kses_allowed_html();
$allowed_html['img'] = [
	'src' => true,
	'width' => true,
	'height' => true,
	'alt' => true,
	'title' => true,
	'style' => true,
	'class' => true,
];
?>
<div class="<?php echo esc_attr(implode(' ' , $cssClasses)); ?>">
	<div data-fbox="<?php echo esc_attr($box->get('ID')); ?>" data-fbox-delay="0"><?php echo wp_kses($box->get('params.data.floatingbutton_message.text', ''), $allowed_html); ?></div>
</div>