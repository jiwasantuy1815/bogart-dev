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

namespace FireBox\Core\Form\Actions\Actions;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class MailChimp extends \FireBox\Core\Form\Actions\Action
{
	protected function prepare()
	{
		$this->action_settings = [
			'api_key' => isset($this->form_settings['attrs']['mailchimpAPIKey']) ? trim($this->form_settings['attrs']['mailchimpAPIKey']) : '',
			'list_id' => isset($this->form_settings['attrs']['mailchimpListID']) ? trim($this->form_settings['attrs']['mailchimpListID']) : '',
			'doubleoptin' => isset($this->form_settings['attrs']['mailchimpDoubleOptin']) ? $this->form_settings['attrs']['mailchimpDoubleOptin'] : false,
			'updateexisting' => isset($this->form_settings['attrs']['mailchimpUpdateExisting']) ? $this->form_settings['attrs']['mailchimpUpdateExisting'] : true,
			
			'tags' => isset($this->form_settings['attrs']['mailchimpTags']) ? $this->form_settings['attrs']['mailchimpTags'] : '',
			'tags_replace' => isset($this->form_settings['attrs']['mailchimpTagsReplace']) ? $this->form_settings['attrs']['mailchimpTagsReplace'] : true,
			'interests' => isset($this->form_settings['attrs']['mailchimpInterests']) ? $this->form_settings['attrs']['mailchimpInterests'] : '',
			'interests_replace' => isset($this->form_settings['attrs']['mailchimpInterestsReplace']) ? $this->form_settings['attrs']['mailchimpInterestsReplace'] : true
			
		];
	}

	/**
	 * Runs the action.
	 * 
	 * @throws  Exception
	 * 
	 * @return  void
	 */
	public function run()
	{
		$api = new \FPFramework\Base\Integrations\MailChimp([
			'api' => $this->action_settings['api_key']
		]);

		
		// Tags
		$tags = !empty($this->action_settings['tags']) ? array_map('trim', explode(',', $this->action_settings['tags'])) : [];
		$tags_replace = $this->action_settings['tags_replace'] ? 'replace_all' : 'add_only';

		// Interests
		$interests = !empty($this->action_settings['interests']) ? $this->getFormattedInterests($this->action_settings['interests']) : [];
		$interests_replace = $this->action_settings['interests_replace'] ? 'replace_all' : 'add_only';
		

		$api->subscribe(
			$this->action_settings['list_id'],
			$this->submission['prepared_fields']['email']['value'],
			$this->field_values,
			$this->action_settings['doubleoptin'],
			$this->action_settings['updateexisting'],
			
			$tags,
			$tags_replace,
			/**
			 * Currently, the user must enter the interests groups in a text field.
			 * Due to this, we must validate the entered interests groups labels in the integration itself.
			 * "dynamic" is used to identify this behavior.
			 * 
			 * Once we have a radio/dropdown/checkbox field, we should be able to either add the
			 * interests groups manually or load them dynamically and we will only be passing interests
			 * groups IDs so we wont need to validate them on our end.
			 * Also, then, we will just be passing $interests.
			 */
			['dynamic' => $interests],
			$interests_replace
			
		);
		
		if (!$api->success())
		{
			$error = $api->getLastError();
			$error_parts = explode(' ', $error);

			if (function_exists('mb_strpos'))
			{
				// Make MalChimp errors translatable
				if (mb_strpos($error, 'is already a list member') !== false)
				{
					$error = sprintf(fpframework()->_('FPF_ERROR_USER_ALREADY_EXIST'), $error_parts[0]);
				}
	
				if (mb_strpos($error, 'fake or invalid') !== false)
				{
					$error = sprintf(fpframework()->_('FPF_ERROR_INVALID_EMAIL_ADDRESS'), $error_parts[0]);
				}
			}

			throw new \Exception(esc_html($error));
		}

		return true;
	}

	/**
	 * Validates the action prior to running it.
	 * 
	 * @return  void
	 */
	public function validate()
	{
		if (empty($this->action_settings['api_key']))
		{
			throw new \Exception(esc_html(sprintf(firebox()->_('FB_INTEGRATION_ERROR_NO_API_KEY_SET'), 'MailChimp')));
		}

		if (empty($this->action_settings['list_id']))
		{
			throw new \Exception(esc_html(sprintf(firebox()->_('FB_INTEGRATION_ERROR_NO_LIST_SELECTED'), 'MailChimp')));
		}

		return true;
	}

	
	/**
	 * Returns the interests groups in a format recognized by Mailchimp.
	 * 
	 * @param   string  $interests
	 * 
	 * @return  array
	 */
	private function getFormattedInterests($interests = [])
	{
		if (!$interests)
		{
			return [];
		}

		$data = [];

		$interests = array_filter(explode(';', $interests));

		foreach ($interests as $interest)
		{
			$_interest = explode(':', $interest);
			if (count($_interest) !== 2)
			{
				continue;
			}

			$interest_label = trim($_interest[0]);
			$interest_values = $_interest[1];
			$interest_values = array_map('trim', explode(',', $interest_values));

			$data[$interest_label] = $interest_values;
		}
		
		return $data;
	}
	
}