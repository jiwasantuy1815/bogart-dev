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

namespace FireBox\Core\Controllers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Form;
use FPFramework\Base\FieldsParser;
use FPFramework\Base\Ui\Tabs;

class BoxSettings extends BaseController
{
    protected $action = '';
    
	/**
	 * The form settings name
	 * 
	 * @var  string
	 */
	const settings_name = 'firebox_settings';
	
    public function __construct()
    {
        add_action('update_option_firebox_settings', [$this, 'after_update_settings'], 10, 3);
    }

	/**
	 * Render the page content
	 * 
	 * @return  void
	 */
	public function render()
	{
		// page content
		add_action('firebox/settings_page', [$this, 'settingsPageContent']);
		
		// render layout
		firebox()->renderer->admin->render('pages/settings');
	}

    /**
     * Stop the usage tracking if the user disables the tracking behavior.
     * 
     * @param   array  $old_value
     * @param   array  $new_value
     * 
     * @return  void
     */
    public function after_update_settings($old_value, $new_value)
    {
        if (isset($new_value['usage_tracking']) && !$new_value['usage_tracking'])
        {
            $tracking = new \FireBox\Core\UsageTracking\SendUsage();
            $tracking->stop();
        }
    }

	/**
	 * Load required media files
	 * 
	 * @return void
	 */
	public function addMedia()
	{
		// load geoip js
		wp_register_script(
			'fpf-geoip',
			FPF_MEDIA_URL . 'admin/js/fpf_geoip.js',
			[],
			FPF_VERSION,
			false
		);
		wp_enqueue_script('fpf-geoip');
	}

	/**
	 * Callback used to handle the processing of settings.
	 * Useful when using a Repeater field to remove the template from the list of submitted items.
	 * 
	 * @param   array  $input
	 * 
	 * @return  void
	 */
	public function processBoxSettings($input)
	{
        if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], ['firebox_download_key_notice_activate', 'firebox_enable_usage_tracking']))
        {
            return $input;
        }
        
		// run a quick security check
        if (!check_admin_referer('fpf_form_nonce_firebox_settings', 'fpf_form_nonce_firebox_settings'))
        {
			return; // get out if we didn't click the Activate button
        }

        // Disable usage tracking
        if (!isset($input['usage_tracking']))
        {
            $tracking = new \FireBox\Core\UsageTracking\SendUsage();
            $tracking->stop();
        }

		
		// create a unique db option to use it on all plugins to fetch the geo license key
		$geo_license_key = '';
		if (isset($input['geo_license_key']) && !empty($input['geo_license_key']))
		{
			$geo_license_key = $input['geo_license_key'];
		}
		update_option('fpf_geo_license_key', $geo_license_key);

        // Store license key in a db option
        if (isset($input['license_update_btn']) && $input['license_update_btn'] == 'Activate License')
        {
            $license_key = '';
            if (isset($input['license_key']) && !empty($input['license_key']) && isset($input['license_update_btn']) && !empty($input['license_update_btn']) && empty($this->action))
            {
                $this->action = 'activate';
                
                $input['license_key'] = trim($input['license_key']);
                
                $license_key = $input['license_key'];
				// Handle license activation
				$this->validateAndActivateLicense($license_key);
			}

			update_option('firebox_license_key', $license_key);
        }
        else if (isset($input['license_update_btn']) && $input['license_update_btn'] == 'Deactivate License' && empty($this->action))
        {
            $this->action = 'deactivate';

            // Handle license deactivation
            $this->checkAndDeactivateLicense();
        }
		
		
		// Filters the fields value
		\FPFramework\Helpers\FormHelper::filterFields($input, \FireBox\Core\Admin\Forms\Settings::getSettings());

		\FPFramework\Libs\AdminNotice::displaySuccess(fpframework()->_('FPF_SETTINGS_SAVED'));
		
		return $input;
	}

    
    /**
     * Validates the license key and activates the license
     * 
     * @param   string  $license_key
     * 
     * @return  mixed
     */
    private function validateAndActivateLicense($license_key)
    {
        if (!$license_key || !is_string($license_key) || empty($license_key))
        {
            delete_option('firebox_license_key');
            delete_option('firebox_license_status');
            return;
        }

        /**
         * The stored license key is set and is different
         * than the one on Save, delete it to start re-activation process.
         */
        delete_option('firebox_license_status');

        $license = trim($license_key);

        // data to send in our API request
        $api_params = [
            'edd_action' => 'activate_license',
            'license'    => $license,
            'item_id'    => FBOX_SL_ITEM_ID, // The ID of the item in EDD
            'url'        => home_url()
        ];

        // Call the custom API.
		$response = wp_remote_post(FPF_SITE_URL, ['timeout' => 15, 'sslverify' => false, 'body' => $api_params]);
		
        // make sure the response came back okay
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response))
        {
            $message = (is_wp_error($response) && ! empty($response->get_error_message())) ? $response->get_error_message() : fpframework()->_('FPF_ERROR_OCCURRED_PLEASE_TRY_AGAIN');
        }
        else
        {
            $license_data = json_decode(wp_remote_retrieve_body($response));

            if (!$license_data)
            {
                $message = fpframework()->_('FPF_INVALID_LICENSE');
            }
            else if (false === $license_data->success)
            {
                switch ($license_data->error)
                {
                    case 'expired' :
                        $message = sprintf(
                            fpframework()->_('FPF_LICENSE_KEY_EXPIRED_ON'),
                            date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                        );
                        break;

                    case 'revoked' :
                        $message = fpframework()->_('FPF_LICENSE_KEY_REVOKED');
                        break;

                    case 'missing' :
                        $message = fpframework()->_('FPF_INVALID_LICENSE');
                        break;

                    case 'invalid' :
                    case 'site_inactive' :
                        $message = fpframework()->_('FPF_LICENSE_KEY_NOT_VALID_FOR_THIS_URL');
                        break;

                    case 'item_name_mismatch' :
                        $message = sprintf(fpframework()->_('FPF_LICENSE_MISMATCH'), firebox()->_('FB_PLUGIN_NAME'));
                        break;

                    case 'no_activations_left':
                        $message = fpframework()->_('FPF_LICENSE_LIMIT_REACHED');
                        break;

                    default :
                        $message = fpframework()->_('FPF_ERROR_OCCURRED_PLEASE_TRY_AGAIN');
                        break;
                }
            }
        }
        
        // Check if anything passed on a message constituting a failure
        if (!empty($message))
        {
            delete_option('firebox_license_key');
            update_option('firebox_license_status', 'invalid');

            $base_url = admin_url('admin.php?page=firebox-settings#license');
            $redirect = add_query_arg(['sl_activation' => 'false', 'message' => urlencode($message)], $base_url);

            wp_redirect($redirect);
            return;
        }

        $status = isset($license_data->license) ? $license_data->license : 'invalid';

        update_option('firebox_license_key', $license_key );
        update_option('firebox_license_status', $license_data->license );
        wp_redirect(admin_url('admin.php?page=firebox-settings#license'));
        return;
	}

    /**
     * Checks the saved license key and deactivates it
     * 
     * @return  boolean
     */
    private function checkAndDeactivateLicense()
    {
		$license_key = get_option('firebox_license_key');
		if (empty($license_key))
		{
            // Empty the license key status
            update_option('firebox_license_status', 'invalid');
			return false;
        }

        $fbox_settings = get_option('firebox_settings');
        if (!isset($fbox_settings['license_key']))
        {
            return false;
        }
        
        // data to send in our API request
        $api_params = [
            'edd_action' => 'deactivate_license',
            'license'    => $license_key,
            'item_name'  => urlencode(firebox()->_('FB_PLUGIN_NAME')),
            'url'        => home_url()
        ];

        // Call the custom API.
		$response = wp_remote_post(FPF_SITE_URL, ['timeout' => 15, 'sslverify' => false, 'body' => $api_params]);
		
        // make sure the response came back okay
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response))
        {
            $message = is_wp_error($response) ? $response->get_error_message() : fpframework()->_('FPF_ERROR_OCCURRED_PLEASE_TRY_AGAIN');

            $base_url = admin_url('admin.php?page=firebox-settings#license');
            $redirect = add_query_arg(['sl_activation' => 'false', 'message' => urlencode($message)], $base_url);

            wp_redirect($redirect);
            exit();
        }

        // decode the license data
        $license_data = json_decode(wp_remote_retrieve_body($response));

        unset($fbox_settings['license_key']);
        update_option('firebox_settings', $fbox_settings);

        $status = isset($license_data->license) ? $license_data->license : 'failed';
        
        update_option('firebox_license_status', $status);
        update_option('firebox_license_key', '');

        wp_redirect(admin_url('admin.php?page=firebox-settings#license'));
        exit();
    }
	

	/**
	 * What the settings page will contain
	 * 
	 * @return  void
	 */
	public function settingsPageContent()
	{
		$fieldsParser = new FieldsParser([
			'fields_name_prefix' => 'firebox_settings'
		]);

		$settings = \FireBox\Core\Admin\Forms\Settings::getSettings();
		foreach ($settings['data'] as $key => $value)
		{
			ob_start();
			$fieldsParser->renderContentFields($value);
			$html = ob_get_contents();
			ob_end_clean();

			$settings['data'][$key]['title'] = $value['title'];
			$settings['data'][$key]['content'] = $html;
		}

		// render settings as tabs
		$tabs = new Tabs($settings);

		// render form
		$form = new Form($tabs->render(), [
			'section_name' => self::settings_name
		]);
        
		echo $form->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}