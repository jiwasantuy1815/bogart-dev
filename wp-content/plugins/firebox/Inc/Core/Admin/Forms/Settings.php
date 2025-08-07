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

namespace FireBox\Core\Admin\Forms;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Settings
{
	/**
	 * All Settings for the FireBox Global Settings Page
	 * 
	 * @return  array
	 */
	public static function getSettings()
	{
		$settings = [
			'id' => 'FireBoxSettings',
			'tabs_menu_sticky' => true,
			'mobile_menu' => true,
			'data' => [
				'general' => self::getGeneralSettings(),
				'advanced' => self::getAdvancedSettings(),
				'geolocation' => self::getGeolocationSettings(),
				'captcha' => self::getCaptchaSettings(),
				'data' => self::getDataSettings(),
				'license_key' => self::getLicenseKeySettings(),
			]
		];

		return apply_filters('firebox/forms/settings/edit', $settings);
	}

	/**
	 * Holds the General Settings
	 * 
	 * @retun  array
	 */
	private static function getGeneralSettings()
	{
		return [
			'title' => 'FPF_GENERAL',
			'content' => [
				// Media
				'media' => [
					'title' => [
						'title' => 'FPF_MEDIA',
						'description' => firebox()->_('FB_SETTINGS_MEDIA_DESC')
					],
					'fields' => [
						[
							'name' => 'loadCSS',
							'type' => 'FPToggle',
							'label' => firebox()->_('FB_SETTINGS_LOAD_CSS'),
							'description' => firebox()->_('FB_SETTINGS_LOAD_CSS_DESC'),
							'checked' => true
						]
					]
				],
				// Other
				'other' => [
					'title' => [
						'title' => 'FPF_OTHER',
						'description' => firebox()->_('FB_SETTINGS_OTHER_DESC')
					],
					'fields' => [
						[
							'name' => 'show_admin_bar_menu_item',
							'type' => 'FPToggle',
							'label' => firebox()->_('FB_SETTINGS_SHOW_ADMIN_BAR_MENU_ITEM'),
							'description' => firebox()->_('FB_SETTINGS_SHOW_ADMIN_BAR_MENU_ITEM_DESC'),
							'checked' => true
						],
						[
							'name' => 'showcopyright',
							'type' => 'FPToggle',
							'label' => firebox()->_('FB_SETTINGS_SHOW_COPYRIGHT'),
							'description' => firebox()->_('FB_SETTINGS_SHOW_COPYRIGHT_DESC'),
							'checked' => true
						],
						[
							'name' => 'debug',
							'type' => 'FPToggle',
							'label' => firebox()->_('FB_SETTINGS_DEBUG'),
							'description' => firebox()->_('FB_SETTINGS_DEBUG_DESC'),
						]
					]
				]
			]
		];
	}

	/**
	 * Holds the Advanced settings
	 * 
	 * @return  array
	 */
	private static function getAdvancedSettings()
	{
		return [
			'title' => 'FPF_ADVANCED',
			'content' => [
				// Analytics
				'analytics' => [
					'title' => [
						'title' => firebox()->_('FB_SETTINGS_ANALYTICS'),
						'description' => firebox()->_('FB_SETTINGS_ANALYTICS_DESC')
					],
					'fields' => [
						[
							'name' => 'statsdays',
							'type' => 'Dropdown',
							'label' => firebox()->_('FB_SETTINGS_STATS_PERIOD'),
							'description' => firebox()->_('FB_SETTINGS_STATS_PERIOD_DESC'),
							'showon' => '[stats]:1',
							'default' => 730,
							'choices' => [
								365 => fpframework()->_('FPF_1_YEAR'),
								365 * 2 => fpframework()->_('FPF_2_YEARS'),
								365 * 5 => fpframework()->_('FPF_5_YEARS'),
								365 * 99 => fpframework()->_('FPF_KEEP_FOREVER'),
							]
						]
					]
				],
				// PHP Scripts
				'phpscripts' => [
					'title' => [
						'title' => firebox()->_('FB_PHPSCRIPTS'),
						'description' => firebox()->_('FB_PHPSCRIPTS_SETTINGS_DESC')
					],
					'fields' => [
						
						[
							'name' => 'enable_phpscripts',
							'type' => 'FPToggle',
							'label' => firebox()->_('FB_ENABLE_PHPSCRIPTS'),
							'description' => firebox()->_('FB_ENABLE_PHPSCRIPTS_DESC')
						],
						
						
					]
				],
				// Google Analytics
				'google_analytics' => [
					'title' => [
						'title' => firebox()->_('FB_SETTINGS_GAT'),
						'description' => firebox()->_('FB_SETTINGS_GAT_DESC')
					],
					'fields' => [
						
						[
							'type' => 'Label',
							'description' => firebox()->_('FB_GOOGLE_ANALYTICS_INTEGRATION_LABEL_DESC')
						],
						
						
					]
				],
				// API Key
				'api' => [
					'title' => [
						'title' => firebox()->_('FB_JSON_API'),
						'description' => firebox()->_('FB_JSON_API_DESC')
					],
					'fields' => [
						
						[
							'name' => 'enable_json_api',
							'type' => 'FPToggle',
							'label' => firebox()->_('FB_ENABLE_JSON_API'),
							'description' => firebox()->_('FB_ENABLE_JSON_API_DESC')
						],
						[
							'name' => 'api_key',
							'type' => 'Text',
							'label' => firebox()->_('FB_API_PASSWORD'),
							'description' => firebox()->_('FB_API_PASSWORD_DESC'),
							'showon' => '[enable_json_api]:1',
							'input_class' => ['large']
						],
						
						
					]
				]
			]
		];
	}

	/**
	 * Holds the Geolocation settings
	 * 
	 * @return  array
	 */
	private static function getGeolocationSettings()
	{
		return [
			'title' => 'FPF_GEOLOCATION',
			'content' => [
				// Database
				'database' => [
					'title' => [
						'title' => 'FPF_GEOLOCATION_SERVICES',
						'description' => 'FPF_GEOIP_GEOLOCATION_SERVICES_HEADING_DESC'
					],
					'fields' => [
						
						[
							'type' => 'GeoLocationDBStatusChecker',
							'plugin_name' => firebox()->_('FB_PLUGIN_NAME')
						],
						[
							'name' => 'geo_license_key',
							'type' => 'Text',
							'label' => 'FPF_LICENSE_KEY',
							'description' => 'FPF_GEOIP_LICENSE_KEY_DESC',
							'urltext' => 'FPF_GEOIP_LICENSE_KEY_GET',
							'urltarget' => '_blank',
							'url' => 'https://www.maxmind.com/en/geolite2/signup',
							'input_class' => ['medium', 'geoip-license-key'],
						],
						[
							'name' => 'geo_updatebutton',
							'type' => 'GeoUpdateButton',
							'label' => 'FPF_GEOIP_UPDATE_DB',
							'description' => 'FPF_GEOIP_UPDATE_DB_DESC'
						],
						[
							'name' => 'geo_last_updated',
							'type' => 'GeoLastUpdated',
							'label' => 'FPF_LAST_UPDATED',
							'description' => 'FPF_GEOIP_LAST_UPDATED_DESC',
						],
						
						
					]
				],
				// Lookup IP Address
				'geo_lookup' => [
					'title' => [
						'title' => 'FPF_GEOIP_LOOKUP',
						'description' => 'FPF_GEOIP_LOOKUP_DESC'
					],
					'fields' => [
						
						[
							'name' => 'geo_lookup',
							'type' => 'GeoLookup',
							'label' => 'FPF_GEOIP_LOOKUP',
							'description' => 'FPF_GEOIP_LOOKUP_DESC'
						],
						
						
					]
				]
			]
		];
	}

	/**
	 * Holds the Captcha settings
	 * 
	 * @return  array
	 */
	private static function getCaptchaSettings()
	{
		$turnstile_url = 'https://www.fireplugins.com/docs/security/cloudflare-turnstile-captcha/';
		$turnstile_url = \FPFramework\Base\Functions::getUTMURL($turnstile_url, '', 'notice', 'cloudflare-turnstile-captcha');
		
		$hcaptcha_url = 'https://www.fireplugins.com/docs/security/hcaptcha/';
		$hcaptcha_url = \FPFramework\Base\Functions::getUTMURL($hcaptcha_url, '', 'notice', 'hcaptcha');
		
		return [
			'title' => 'FPF_CAPTCHA',
			'content' => [
				// Cloudflare Turnstile
				'turnstile' => [
					'title' => [
						'title' => 'FPF_CLOUDFLARE_TURNSTILE',
						'description' => sprintf(firebox()->_('FB_CLOUDFLARE_TURNSTILE_DESC'), $turnstile_url)
					],
					'fields' => [
						[
							'name' => 'cloudflare_turnstile_site_key',
							'type' => 'Text',
							'label' => firebox()->_('FB_SITE_KEY'),
							'description' => firebox()->_('FB_CLOUDFLARE_TURNSTILE_SITE_KEY_DESC'),
							'input_class' => ['xlarge'],
						],
						[
							'name' => 'cloudflare_turnstile_secret_key',
							'type' => 'Text',
							'label' => firebox()->_('FB_SECRET_KEY'),
							'description' => firebox()->_('FB_CLOUDFLARE_TURNSTILE_SECRET_KEY_DESC'),
							'input_class' => ['xlarge'],
						],
					]
				],
				// hCaptcha
				'hcaptcha' => [
					'title' => [
						'title' => 'FPF_HCAPTCHA',
						'description' => sprintf(firebox()->_('FB_HCAPTCHA_DESC'), $hcaptcha_url)
					],
					'fields' => [
						[
							'name' => 'hcaptcha_site_key',
							'type' => 'Text',
							'label' => firebox()->_('FB_SITE_KEY'),
							'description' => firebox()->_('FB_HCAPTCHA_SITE_KEY_DESC'),
							'input_class' => ['xlarge'],
						],
						[
							'name' => 'hcaptcha_secret_key',
							'type' => 'Text',
							'label' => firebox()->_('FB_SECRET_KEY'),
							'description' => firebox()->_('FB_HCAPTCHA_SECRET_KEY_DESC'),
							'input_class' => ['xlarge'],
						],
					]
				]
			]
		];
	}

	/**
	 * Holds the Data settings
	 * 
	 * @return  array
	 */
	private static function getDataSettings()
	{
		$url = 'https://www.fireplugins.com/docs/troubleshoot/usage-tracking/';
		$url = \FPFramework\Base\Functions::getUTMURL($url, '', 'notice', 'usage-tracking');

		return [
			'title' => 'FPF_DATA',
			'content' => [
				// Uninstall
				'keep_data_on_uninstall' => [
					'title' => [
						'title' => 'FPF_UNINSTALL',
						'description' => 'FPF_SETTINGS_UNINSTALL_DESC'
					],
					'fields' => [
						[
							'name' => 'keep_data_on_uninstall',
							'type' => 'FPToggle',
							'label' => 'FPF_KEEP_DATA_ON_UNINSTALL',
							'description' => 'FPF_KEEP_DATA_ON_UNINSTALL_DESC',
							'checked' => true
						],
					]
				],
				'usage_tracking' => [
					'title' => [
						'title' => firebox()->_('FB_USAGE_TRACKING'),
						'description' => firebox()->_('FB_USAGE_TRACKING_DESC')
					],
					'fields' => [
						[
							'name' => 'usage_tracking',
							'type' => 'FPToggle',
							'label' => firebox()->_('FB_ALLOW_USAGE_TRACKING'),
							'description' => sprintf(firebox()->_('FB_ALLOW_USAGE_TRACKING_DESC'), $url)
						]
					]
				]
			]
		];
	}

	/**
	 * Holds the License Key settings
	 * 
	 * @return  array
	 */
	private static function getLicenseKeySettings()
	{
		$plugin_name = firebox()->_(firebox()->_('FB_PLUGIN_NAME'));
		
		$status = '';
		$status_prefix = 'You\'re using ' . $plugin_name . ' ' . ucfirst(FBOX_LICENSE_TYPE) . ' - ';
		$enjoy = fpframework()->_('FPF_ENJOY') . '! 🙂';

		

		
		$license_update_btn_label = 'FPF_ACTIVATE_LICENSE';
		$license_key = get_option( 'firebox_license_key' );
		$license_status = get_option( 'firebox_license_status' );
		if ($license_status == 'valid')
		{
			$status = $status_prefix . sprintf(fpframework()->_('FPF_YOUR_LICENSE_KEY_IS_STATUS'), 'green', fpframework()->_('FPF_VALID')) . ' ' . $enjoy;
			$license_update_btn_label = 'FPF_DEACTIVATE_LICENSE';
		}
		else if (empty($license_status) || empty($license_key))
		{
			$status = $status_prefix . fpframework()->_('FPF_PLEASE_ENTER_A_VALID_LICENSE_KEY_TO_RECEIVE_UPDATES');
		}
		else
		{
			$status = $status_prefix . sprintf(fpframework()->_('FPF_YOUR_LICENSE_KEY_IS_STATUS'), 'red', fpframework()->_('FPF_INVALID'));
		}
		

		return [
			'title' => 'FPF_LICENSE_KEY',
			'content' => [
				// License
				'license_key' => [
					'title' => [
						'title' => 'FPF_LICENSE',
						'description' => 'FPF_SETTINGS_LICENSE_DESC'
					],
					'fields' => [
						
						[
							'name' => 'license_key',
							'type' => 'Text',
							'label' => 'FPF_LICENSE_KEY',
							'description' => 'FPF_LICENSE_KEY_DESC',
							'urltext' => 'FPF_FIND_LICENSE_KEY',
							'urltarget' => '_blank',
							'url' => \FPFramework\Base\Functions::getUTMURL('https://www.fireplugins.com/docs/start/activate/#find_license_key', '', 'misc', 'activate-pro'),
							'input_class' => ['large'],
						],
						[
							'type' => 'Label',
							'text' => $status,
							'class' => ['margin-top-0']
						],
						[
							'name' => 'license_update_btn',
							'type' => 'Submit',
							'value' => fpframework()->_($license_update_btn_label),
							'class' => ['margin-top-0'],
							'input_class' => ['primary']
						],
						
						
					]
				]
			]
		];
	}
}