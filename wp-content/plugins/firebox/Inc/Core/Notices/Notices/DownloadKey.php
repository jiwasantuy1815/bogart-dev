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

class DownloadKey extends Notice
{
	protected $notice_payload = [
		'type' => 'error',
		'class' => 'download-key',
		'dismissible' => false,
		'download_key' => null,
		'state' => null
	];

	public function __construct($payload = [])
	{
		parent::__construct($payload);
	
		$this->payload['download_key'] = trim(\FireBox\Core\Helpers\Settings::findSettingsOption('license_key'));
		$this->payload['state'] = get_option('firebox_license_status', '');
	}

	/**
	 * Notice title.
	 * 
	 * @return  string
	 */
	protected function getTitle()
	{
		$text = !empty($this->payload['download_key']) || ($this->payload['state'] === 'invalid_key') ? firebox()->_('FB_IS_INVALID') : firebox()->_('FB_IS_MISSING');
		return sprintf(firebox()->_('FB_DOWNLOAD_KEY_TEXT'), $text);
	}

	/**
	 * Notice description.
	 * 
	 * @return  string
	 */
	protected function getDescription()
	{
		$url = 'https://www.fireplugins.com/docs/start/activate/#find_license_key';
		$url = \FPFramework\Base\Functions::getUTMURL($url, '', 'notice', 'download-key');
		
		$text = !empty($this->payload['download_key']) || ($this->payload['state'] === 'invalid_key') ? firebox()->_('FB_A_VALID') : firebox()->_('FB_YOUR');
		return sprintf(firebox()->_('FB_DOWNLOAD_KEY_MISSING_DESC'), $text, esc_url($url));
	}
	
	/**
	 * Notice actions.
	 * 
	 * @return  string
	 */
	protected function getActions()
	{
		return '<div class="actions-inner">
					<input type="text" class="firebox-notice-download-key" value="' . esc_attr($this->getDownloadKey()) . '" placeholder="' . esc_attr(firebox()->_('FB_ENTER_YOUR_DOWNLOAD_KEY')) . '" />
					<a href="#" class="firebox-notice-download-key-btn firebox-notice-btn">' . esc_attr(fpframework()->_('FPF_APPLY')) . '
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="15" height="15" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
							<circle cx="50" cy="50" fill="none" stroke="currentColor" stroke-width="8" r="38" stroke-dasharray="179.0707812546182 61.690260418206066">
								<animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform>
							</circle>
						</svg>
					</a>
				</div>
				<div class="actions-message"></div>';
	}

	/**
	 * Notice icon.
	 * 
	 * @return  string
	 */
	protected function getIcon()
	{
		return '<mask id="mask0_616_255" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="40" height="40"><rect width="40" height="40" fill="#D9D9D9"/></mask><g mask="url(#mask0_616_255)"><path d="M18.75 27.9166H21.25V18.3333H18.75V27.9166ZM20 15.4808C20.3814 15.4808 20.7011 15.3518 20.9592 15.0937C21.2172 14.8357 21.3463 14.5159 21.3463 14.1345C21.3463 13.7532 21.2172 13.4334 20.9592 13.1754C20.7011 12.9176 20.3814 12.7887 20 12.7887C19.6186 12.7887 19.2989 12.9176 19.0409 13.1754C18.7828 13.4334 18.6538 13.7532 18.6538 14.1345C18.6538 14.5159 18.7828 14.8357 19.0409 15.0937C19.2989 15.3518 19.6186 15.4808 20 15.4808ZM20.0029 35.8333C17.8129 35.8333 15.7545 35.4177 13.8275 34.5866C11.9006 33.7555 10.2245 32.6276 8.79919 31.2029C7.37391 29.7782 6.24544 28.1027 5.41377 26.1766C4.58238 24.2505 4.16669 22.1926 4.16669 20.0029C4.16669 17.8129 4.58224 15.7544 5.41335 13.8275C6.24446 11.9005 7.37238 10.2244 8.7971 8.79913C10.2218 7.37385 11.8972 6.24538 13.8234 5.41371C15.7495 4.58232 17.8074 4.16663 19.9971 4.16663C22.1871 4.16663 24.2456 4.58218 26.1725 5.41329C28.0995 6.2444 29.7756 7.37232 31.2009 8.79704C32.6261 10.2218 33.7546 11.8972 34.5863 13.8233C35.4177 15.7494 35.8334 17.8073 35.8334 19.997C35.8334 22.187 35.4178 24.2455 34.5867 26.1725C33.7556 28.0994 32.6277 29.7755 31.2029 31.2008C29.7782 32.6261 28.1028 33.7545 26.1767 34.5862C24.2506 35.4176 22.1927 35.8333 20.0029 35.8333ZM20 33.3333C23.7222 33.3333 26.875 32.0416 29.4584 29.4583C32.0417 26.875 33.3334 23.7222 33.3334 20C33.3334 16.2777 32.0417 13.125 29.4584 10.5416C26.875 7.95829 23.7222 6.66663 20 6.66663C16.2778 6.66663 13.125 7.95829 10.5417 10.5416C7.95835 13.125 6.66669 16.2777 6.66669 20C6.66669 23.7222 7.95835 26.875 10.5417 29.4583C13.125 32.0416 16.2778 33.3333 20 33.3333Z" fill="currentColor" /></g>';
	}
	
	/**
	 * Whether the notice can run.
	 * 
	 * @return  string
	 */
	protected function canRun()
	{
		// Ensure customer is using the Pro version
		if (FBOX_LICENSE_TYPE !== 'pro')
		{
			return false;
		}

		if (!isset($this->payload['license_data']['license_type']))
		{
			return true;
		}

		if ($this->payload['license_data']['license_type'] !== 'pro')
		{
			return true;
		}

		if (!empty($this->getDownloadKey()))
		{
			return false;
		}

		return true;
	}

	private function getDownloadKey()
	{
		return isset($this->payload['download_key']) ? $this->payload['download_key'] : '';
	}
}