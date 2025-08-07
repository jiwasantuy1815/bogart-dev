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

namespace FireBox\Core\Notices;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use \FireBox\Core\Helpers\Settings;
use \FireBox\Core\Helpers\Plugin;

class Notices
{
	/**
	 * The payload.
	 * 
	 * @var  array
	 */
	private $payload;
	
	/**
	 * The notices to exclude.
	 * 
	 * @var  array
	 */
	private $exclude = [];

	/**
	 * Define how old (in days) the file that holds all extensions data needs to be set as expired,
	 * so we can fetch new data.
	 * 
	 * @var  int
	 */
	private $extensions_data_file_days_old = 1;

	
	/**
     * Download Key.
     *
     * @var  String
     */
    protected $download_key = null;
	

	/**
	 * The license data for the given download key.
	 * 
	 * @var  array
	 */
	protected $license_data = [];
	
	/**
     * Notices Instance.
     *
     * @var  Notices
     */
    private static $instance;
	
	public function __construct($payload = [])
	{
		$this->payload = $payload;

		$this->exclude = isset($this->payload['exclude']) ? $this->payload['exclude'] : [];

		
		$this->download_key = Settings::findSettingsOption('license_key');
		
	}

    /**
     * Returns class instance
	 * 
	 * @param   array   $payload
     *
     * @return  object
     */
    public static function getInstance($payload = [])
    {
        if (is_null(self::$instance))
        {
            self::$instance = new self($payload);
        }

        return self::$instance;
    }

	/**
	 * Show all available notices.
	 * 
	 * @return  void
	 */
	public function show()
	{
		// Show only for Super Users
		if (!$this->isSuperUser())
		{
			return;
		}

		$this->loadAssets();

		$payload = [
			'exclude' => $this->exclude
		];
		
		echo firebox()->renderer->admin->render('notices/tmpl', $payload); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function loadAssets()
	{
		wp_register_style(
			'firebox-notices',
			FBOX_MEDIA_ADMIN_URL . 'css/notices.css',
			[],
			FBOX_VERSION,
			false
		);
		wp_enqueue_style('firebox-notices');

		if (apply_filters('firebox/load_notices', false))
		{
			// load notices js
			wp_register_script(
				'firebox-notices',
				FBOX_MEDIA_ADMIN_URL . 'js/notices.js',
				[],
				FBOX_VERSION,
				true
			);
			wp_enqueue_script('firebox-notices');
		}

		
		// Localize the script with new data
		wp_localize_script('firebox-notices', 'firebox_notices_object', [
			'DOWNLOAD_KEY_ENTERED_INVALID' => firebox()->_('FB_DOWNLOAD_KEY_ENTERED_INVALID')
		]);
		
	}

	/**
	 * Check if the current user is a Super User.
	 * 
	 * @return  bool
	 */
	private function isSuperUser()
	{
		return current_user_can('manage_options');
	}

	/**
	 * Returns the base notices.
	 * 
	 * @param   array  $notices
	 * 
	 * @return  void
	 */
	private function getBaseNotices()
	{
		$base_notices = [
			'UsageTracking',
			'UpgradeToPro',
			'Outdated',
			
			'DownloadKey',
			'Geolocation'
			
		];

		// Exclude notices we should not display
		if (count($this->exclude))
		{
			foreach ($base_notices as $key => $notice)
			{
				if (!in_array($notice, $this->exclude))
				{
					continue;
				}

				unset($base_notices[$key]);
			}
		}

		// Allow to filter which base notices to display
		$base_notices = apply_filters('firebox/base_notices', $base_notices);

		if (!$base_notices)
		{
			return [];
		}
		
		$notices = [];

		// Initialize notices
		foreach ($base_notices as $key => $notice)
		{
			$class = '\FireBox\Core\Notices\Notices\\' . $notice;

			// Skip empty notice
			if (!$html = (new $class($this->payload))->render())
			{
				continue;
			}
			
			$notices[strtolower($notice)] = $html;
		}

		return $notices;
	}

	
	/**
	 * Returns which license-related notices to show.
	 * 
	 * Notices:
	 * - Extension expires in date
	 * - Extension expired at date
	 * 
	 * @return  array
	 */
	private function getLicensesBasedNoticesToShow()
	{
		if (!isset($this->payload['license_data']))
		{
			return true;
		}

		if (!$this->payload['license_data'])
		{
			return true;
		}

		$extension_data = $this->payload['license_data'];

		$notices = [];

		// Active subscription and we have a expiration date
		if (!$extension_data['error'] && array_key_exists('expires_in', $extension_data) && $extension_data['expires_in'])
		{
			$notices[] = (new Notices\Expiring(array_merge($this->payload, [
				'expires_in' => $extension_data['expires_in'],
				'status' => isset($extension_data['status']) ? $extension_data['status'] : false,
				'plan' => $extension_data['plan']
			])))->render();
		}

		// We should not have an active subscription and the "expired_at" date must be set.
		if ($extension_data['error'] && array_key_exists('expired_at', $extension_data) && $extension_data['expired_at'])
		{
			$notices[] = (new Notices\Expired(array_merge($this->payload, [
				'expired_at' => $extension_data['expired_at'],
				'plan' => $extension_data['plan']
			])))->render();
		}

		if (!$notices)
		{
			return;
		}

		return implode('', $notices);
	}
	

	/**
	 * Returns the based notices:
	 * 
	 * Notices:
	 * - Base notices
	 * 	 - Outdated
	 * 	 - Download Key
	 * 	 - Geolocation
	 * 	 - Upgrade To Pro
	 * - Update notice
	 * - Extension expires in date
	 * - Extension expired at date
	 * - Rate (If none of the license-related notices appear)
	 * 
	 * @return  string
	 */
	public function getNotices()
	{
		// Check and Update the local licenses data
		$this->checkAndUpdateExtensionsData();

		$notices = $this->getBaseNotices();
		
		// Show Update Notice
		if (!isset($notices['outdated']) && $update_html = (new Notices\Update($this->payload))->render())
		{
			$notices['update'] = $update_html;
		}

		
		if ($license_notices = $this->getLicensesBasedNoticesToShow())
		{
			$notices['license'] = $license_notices;
		}
		else {
		
			if ($rate_html = (new Notices\Rate($this->payload))->render())
			{
				$notices['rate'] = $rate_html;
			}
		
		}
		

		return $notices;
	}

	/**
	 * Checks whether the current extensions data has expired and updates the data file.
	 * 
	 * Also checks and sets the installation date of the extension.
	 * 
	 * @return  bool
	 */
	public function checkAndUpdateExtensionsData()
	{
		
		// Sets licenses information
		$this->license_data = \FireBox\Core\Helpers\License::getRemoteLicenseData($this->download_key);

		// Add the license data to the payload as well
		$this->payload['license_data'] = $this->license_data;
		

		// Set installation date
		Plugin::setInstallationDate(gmdate('Y-m-d H:i:s'));
	}
}