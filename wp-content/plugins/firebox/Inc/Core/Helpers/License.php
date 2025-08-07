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

namespace FireBox\Core\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class License
{
    /**
     * Activate License Key.
     * 
     * @param   string  $license_key
     * 
     * @return  array
     */
    public static function activateLicenseKey($license_key = '')
    {
        $license_key = trim($license_key);

        if (!$license_key)
        {
            return [
                'error' => true,
                'response' => 'No license key provided.'
            ];
        }

        /**
         * The stored license key is set and is different
         * than the one on Save, delete it to start re-activation process.
         */
        delete_option('firebox_license_status');

        // data to send in our API request
        $api_params = [
            'edd_action' => 'activate_license',
            'license'    => $license_key,
            'item_id'    => FBOX_SL_ITEM_ID, // The ID of the item in EDD
            'url'        => home_url()
        ];

        // Call the custom API.
		$response = wp_remote_post(FPF_SITE_URL, ['timeout' => 15, 'sslverify' => false, 'body' => $api_params]);

        $error = true;
        $message = '';
		
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

            return [
                'error' => $error,
                'response' => $message
            ];
        }

        $status = isset($license_data->license) ? $license_data->license : 'invalid';

        if ($status !== 'invalid')
        {
            $error = false;
            $message = firebox()->_('FB_LICENSE_ACTIVATION_SUCCESS');
        }

        update_option('firebox_license_key', $license_key );
        update_option('firebox_license_status', $license_data->license );

        return [
            'error' => $error,
            'response' => $message
        ];
    }
    
    public static function getRemoteLicenseData($license)
    {
        $site_url = preg_replace('(^https?://)', '', get_home_url());
        $site_url = preg_replace('/^www\./', '', $site_url);
        $site_url = rtrim($site_url, '/');
        
        if (!$license)
        {
            return [
                'error' => false,
                'expires_in' => null,
                'expired_at' => null,
                'license_type' => 'free'
            ];
        }

        // Get remote URL
		$url = str_replace('{{LICENSE}}', $license, FBOX_GET_LICENSE_DATA_API_URL);
		$url = str_replace('{{SITE_URL}}', $site_url, $url);

        $response = wp_remote_get($url);

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code !== 200)
        {
            return [
                'error' => false,
                'expires_in' => null,
                'expired_at' => null,
                'license_type' => 'free'
            ];
        }

        $response = wp_remote_retrieve_body($response);

        return json_decode($response, true);
    }
}