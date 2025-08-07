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

use \FPFramework\Base\Functions;

class Geolocation extends Notice
{
	protected $notice_payload = [
		'type' => 'default',
		'class' => 'geolocation'
	];

	/**
	 * Notice title.
	 * 
	 * @return  string
	 */
	protected function getTitle()
	{
		return fpframework()->_('FPF_GEOIP_MAINTENANCE');
	}

	/**
	 * Notice description.
	 * 
	 * @return  string
	 */
	protected function getDescription()
	{
		$geolocationDocsUrl = 'https://www.fireplugins.com/docs/advanced-features/geolocation/';
		$url = Functions::getUTMURL($geolocationDocsUrl, '', 'notice', 'geolocation');
		
		return sprintf(firebox()->_('FB_NOTICE_GEO_MAINTENANCE_DESC'), esc_url($url));
	}
	
	/**
	 * Notice actions.
	 * 
	 * @return  string
	 */
	protected function getActions()
	{
		return '<a href="' . admin_url('admin.php?page=firebox-settings#geolocation') . '" class="firebox-notice-btn">' . esc_html(firebox()->_('FB_UPDATE_DATABASE')) . '</a>';
	}

	/**
	 * Notice icon.
	 * 
	 * @return  string
	 */
	protected function getIcon()
	{
		return '<mask id="mask0_616_279" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="40" height="40"><rect width="40" height="40" fill="#D9D9D9"/></mask><g mask="url(#mask0_616_279)"><path d="M15.3846 21.3783H18.4616V17.1154H21.5383V21.3783H24.6154V14.0383L20 10.9616L15.3846 14.0383V21.3783ZM20 32.5225C23.2605 29.6036 25.7557 26.8038 27.4854 24.1233C29.2151 21.4427 30.08 19.095 30.08 17.08C30.08 14.0416 29.1147 11.5438 27.1841 9.58663C25.2536 7.6294 22.8589 6.65079 20 6.65079C17.1411 6.65079 14.7464 7.6294 12.8158 9.58663C10.8853 11.5438 9.91998 14.0416 9.91998 17.08C9.91998 19.095 10.7848 21.4427 12.5146 24.1233C14.2443 26.8038 16.7394 29.6036 20 32.5225ZM20 35.8491C15.8055 32.2147 12.6603 28.8323 10.5641 25.702C8.46804 22.5715 7.41998 19.6975 7.41998 17.08C7.41998 13.2338 8.66401 10.1201 11.1521 7.73871C13.6404 5.35732 16.5897 4.16663 20 4.16663C23.4103 4.16663 26.3596 5.35732 28.8479 7.73871C31.336 10.1201 32.58 13.2338 32.58 17.08C32.58 19.6975 31.5319 22.5715 29.4358 25.702C27.3397 28.8323 24.1944 32.2147 20 35.8491Z" fill="currentColor" /></g>';
	}

	/**
	 * Whether the notice can run.
	 * 
	 * @return  string
	 */
	protected function canRun()
	{
		// If cookie exists, its been hidden
		if ($this->factory->getCookie('fboxNoticeHideGeolocationNotice') === 'true')
		{
			return false;
		}

		// Abort if no key is set
		if (!\FPFramework\Helpers\Geolocation::getLicenseKey())
		{
			return;
		}

		// Abort if no campaign is using the Geolocation conditions
		if (!\FireBox\Core\Helpers\Geolocation::campaignsUsingGeolocation())
		{
			return false;
		}

		// Abort if the database is not outdated
		if (!\FPFramework\Helpers\Geolocation::geoNeedsUpdate(90))
		{
			return false;
		}

		return true;
	}
}