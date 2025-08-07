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

namespace FireBox\Core\Notices\Notices;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Functions;

class Expired extends Notice
{
	protected $notice_payload = [
		'type' => 'error',
		'class' => 'expired',
		'expired_at' => '',
		'plan' => ''
	];

	public function __construct($payload = [])
	{
		parent::__construct($payload);
		
		$this->payload['tooltip'] = firebox()->_('FB_NOTICE_EXPIRED_TOOLTIP');
		$this->payload['expired_at'] = isset($payload['expired_at']) ? $payload['expired_at'] : false;
		$this->payload['plan'] = isset($payload['plan']) ? $payload['plan'] : false;
	}

	/**
	 * Notice title.
	 * 
	 * @return  string
	 */
	protected function getTitle()
	{
		return firebox()->_('FB_FIREBOX_EXPIRED');
	}

	/**
	 * Notice description.
	 * 
	 * @return  string
	 */
	protected function getDescription()
	{
		return sprintf(firebox()->_('FB_FIREBOX_EXPIRED_DESC'), $this->payload['plan'], Functions::applySiteTimezoneToUTCDate($this->payload['expired_at'], 'd M o'));
	}
	
	/**
	 * Notice actions.
	 * 
	 * @return  string
	 */
	protected function getActions()
	{
		$url = 'https://www.fireplugins.com/account/subscriptions/';
		
		return '<a href="' . esc_url(Functions::getUTMURL($url, '', 'notice', 'expired')) . '" target="_blank" class="firebox-notice-btn">' . esc_html(sprintf(firebox()->_('FB_REACTIVATE_X_PERCENT_OFF'), 20)) . '</a>';
	}

	/**
	 * Notice icon.
	 * 
	 * @return  string
	 */
	protected function getIcon()
	{
		return '<mask id="mask0_616_225" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="40" height="40"><rect width="40" height="40" fill="#D9D9D9"/></mask><g mask="url(#mask0_616_225)"><path d="M20 35.6667C19.6089 35.6667 19.2307 35.5914 18.8654 35.4409C18.5001 35.29 18.1668 35.0725 17.8654 34.7884L5.21165 22.1346C4.92748 21.8332 4.70998 21.4999 4.55915 21.1346C4.40859 20.7694 4.33331 20.3912 4.33331 20C4.33331 19.6089 4.40859 19.2264 4.55915 18.8525C4.70998 18.4787 4.92748 18.1496 5.21165 17.8655L17.8654 5.21171C18.1668 4.91032 18.5001 4.68865 18.8654 4.54671C19.2307 4.40448 19.6089 4.33337 20 4.33337C20.3911 4.33337 20.7736 4.40448 21.1475 4.54671C21.5214 4.68865 21.8504 4.91032 22.1346 5.21171L34.7883 17.8655C35.0897 18.1496 35.3114 18.4787 35.4533 18.8525C35.5955 19.2264 35.6666 19.6089 35.6666 20C35.6666 20.3912 35.5955 20.7694 35.4533 21.1346C35.3114 21.4999 35.0897 21.8332 34.7883 22.1346L22.1346 34.7884C21.8504 35.0725 21.5214 35.29 21.1475 35.4409C20.7736 35.5914 20.3911 35.6667 20 35.6667ZM20.3687 33.0063L33.0062 20.3688C33.0918 20.2832 33.1346 20.1603 33.1346 20C33.1346 19.8398 33.0918 19.7168 33.0062 19.6313L20.3687 6.99379C20.2832 6.90823 20.1603 6.86546 20 6.86546C19.8397 6.86546 19.7168 6.90823 19.6312 6.99379L6.99373 19.6313C6.90817 19.7168 6.8654 19.8398 6.8654 20C6.8654 20.1603 6.90817 20.2832 6.99373 20.3688L19.6312 33.0063C19.7168 33.0919 19.8397 33.1346 20 33.1346C20.1603 33.1346 20.2832 33.0919 20.3687 33.0063ZM18.75 21.8109H21.25V12.4521H18.75V21.8109ZM20 26.17C20.3761 26.17 20.6944 26.0396 20.955 25.7788C21.2158 25.5182 21.3462 25.1999 21.3462 24.8238C21.3462 24.4477 21.2158 24.1294 20.955 23.8688C20.6944 23.608 20.3761 23.4775 20 23.4775C19.6239 23.4775 19.3055 23.608 19.045 23.8688C18.7841 24.1294 18.6537 24.4477 18.6537 24.8238C18.6537 25.1999 18.7841 25.5182 19.045 25.7788C19.3055 26.0396 19.6239 26.17 20 26.17Z" fill="currentColor"/></g>';
	}

	/**
	 * Whether the notice can run.
	 * 
	 * @return  string
	 */
	protected function canRun()
	{
		// If cookie exists, it's already hidden
		if ($this->factory->getCookie('fboxNoticeHideExpiredNotice') === 'true')
		{
			return false;
		}

		// The date the extension expired.
		if (!$this->payload['expired_at'])
		{
			return false;
		}

		return true;
	}
}