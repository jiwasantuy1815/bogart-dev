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

namespace FireBox\Core;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Previewer
{
	/**
	 * FireBox data.
	 *
	 * @var  array
	 */
	public $box_data;

	protected $embed_html = '';

    /**
     * Inits Previewer
     * 
     * @return  void
     */
    public function init()
    {
        if (!$this->is_preview_page())
        {
			return false;
		}

		$this->hooks();
		return true;
    }

	/**
	 * Check if current page request meets requirements for firebox preview page.
	 *
	 * @return  boolean
	 */
    public function is_preview_page()
    {
		global $post;
		$post_type = isset($_GET['post_type']) ? sanitize_key($_GET['post_type']) : false; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id = isset($_GET['p']) ? \absint($_GET['p']) : false; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if (!$post_type && !$post_id && $post)
		{
			$post_type = $post->post_type;
			$post_id = $post->ID;
		}
		
		// Only proceed if we have a item to preview
        if (empty($post_type))
        {
			return false;
		}

        if ($post_type != 'firebox')
        {
			return false;
		}

        if (empty($post_id))
        {
			return false;
		}

        if (empty($_GET['preview'])) //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
			return false;
		}

        if (!sanitize_key(wp_unslash($_GET['preview']))) //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
			return false;
		}

		// Check for logged in user.
        if (!\is_user_logged_in())
        {
			return false;
		}

        if (!\current_user_can('manage_options'))
        {
			return false;
		}

		if (!$this->box_data = firebox()->box->get($post_id))
		{
			return false;
		}

		return true;
	}

	/**
	 * Hooks
	 *
     * @return  void
	 */
	public function hooks()
	{
		if ($this->box_data->params->get('mode') !== 'embed')
		{
			// Set to trigger on Page Load
			$this->box_data->params->set('triggermethod', 'pageload');
		}

		// Remove Impressions
		$this->box_data->params->set('assign_impressions_param_type', 'always');
		// Remove Assignments
		$this->box_data->params->remove('assignments');
		// Remove Rules
		$this->box_data->params->remove('rules');
		// Enable Test Mode to prevent cookies
		$this->box_data->params->set('testmode', true);
		// Disable Statistics to prevent impressions from being tracked into the database
		$this->box_data->params->set('stats', 0);

		\FireBox\Core\Helpers\Actions::run();

		if ($this->box_data->params->get('mode') !== 'embed')
		{
			firebox()->box->setBox($this->box_data)->render();
		}
		else
		{
			global $post;
			$this->embed_html = \FireBox\Core\Helpers\Embed::renderCampaign($post->ID, ['draft']);
		}

		\add_filter('the_title', [$this, 'the_title'], 100, 1);

		\add_filter('the_content', [$this, 'the_content'], 999);

		\add_filter('get_the_excerpt', [$this, 'the_content'], 999);
	}

	public function the_content()
	{
		if (!isset($this->box_data->ID))
		{
			return '';
		}

		if (!current_user_can('manage_options'))
		{
			return '';
		}

		$links = [];

		$links[] = [
			'url'  => esc_url(
				add_query_arg(
					[
						'post'	 => absint($this->box_data->ID),
						'action' => 'edit',
					],
					admin_url('post.php')
				)
			),
			'text' => esc_html(firebox()->_('FB_EDIT_CAMPAIGN')),
		];

		$links[] = [
			'url'  => esc_url(
				add_query_arg(
					[
						'post_type' => 'firebox',
					],
					admin_url('edit.php')
				)
			),
			'text' => esc_html(firebox()->_('FB_VIEW_CAMPAIGNS')),
		];

		$content  = '<div style="padding: 15px; background: #ededed;">';
		$content .= '<p>';
		$content .= esc_html(firebox()->_('FB_FIREBOX_PREVIEW_DESC'));
        if (!empty($links))
        {
			$content .= '<br>';
            foreach ($links as $key => $link)
            {
				$content .= '<a href="' . esc_url($link['url']) . '">' . esc_html($link['text']) . '</a>';
				$l        = array_keys($links);
                if (end($l) !== $key)
                {
					$content .= ' <span style="display:inline-block;margin:0 6px;opacity: 0.5">|</span> ';
				}
			}
		}
		$content .= '</p>';
		$content .= '</div>';

		// Add embed campaign to preview page
		if ($this->box_data->params->get('mode') === 'embed')
		{
			$content .= '<div style="margin: 30px auto; display: flex; justify-content: center;">' . $this->embed_html . '</div>';
		}
		
		return $content;
	}

	/**
	 * Customize firebox popup preview page title.
	 *
	 * @param   string  $title
	 *
	 * @return  string
	 */
    public function the_title($title)
    {
        if (in_the_loop())
        {
			$title = sprintf(
				esc_html('%s'),
				! empty($this->box_data->post_title) ? sanitize_text_field($this->box_data->post_title) . ' ' . firebox()->_('FB_PREVIEW') : esc_html(firebox()->_('FB_FIREBOX_CAMPAIGN_PREVIEW'))
			);
		}
		
		return $title;
	}
}