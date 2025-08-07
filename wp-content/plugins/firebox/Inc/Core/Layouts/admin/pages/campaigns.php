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

if (!current_user_can('read_fireboxes'))
{
	esc_html_e('Sorry, you are not allowed to access this page.', 'firebox');
	return;
}

$campaigns = $this->data->get('campaigns');
$table = new \FireBox\Core\FB\CampaignsList();

// Process bulk actions and show notices
$table->process_bulk_action();
do_action('fpframework/admin/notices');
?>
<h1 class="mb-3 text-default text-[32px] dark:text-white flex gap-1 items-center fp-admin-page-title"><?php echo esc_html(firebox()->_('FB_CAMPAIGNS')); ?></h1>

<form action="" method="get">
	<?php
	$table->views();
	$table->prepare_items();
	$table->search_box(firebox()->_('FB_SEARCH_CAMPAIGNS'), 'firebox-campaign' );
	$table->display();
	?>
	<input type="hidden" name="page" value="firebox-campaigns" />
</form>