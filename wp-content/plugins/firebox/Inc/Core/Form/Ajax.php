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

namespace FireBox\Core\Form;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use \FireBox\Core\Helpers\Form\Form;
use \FireBox\Core\Helpers\BoxHelper;

class Ajax
{
	public function __construct()
	{
		$this->setupAjax();

		new Actions\Ajax();
    }
    
	/**
	 * Setup ajax requests
	 * 
	 * @return  void
	 */
	public function setupAjax()
	{
		add_action('wp_ajax_fb_form_submission_status_change', [$this, 'fb_form_submission_status_change']);

		add_action('wp_ajax_fb_form_submit', [$this, 'fb_form_submit']);
        add_action('wp_ajax_nopriv_fb_form_submit', [$this, 'fb_form_submit']);
    }

	/**
	 * Update submission status.
	 * 
	 * @return  void
	 */
	public function fb_form_submission_status_change()
	{
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

        // verify nonce
        if (!$verify = wp_verify_nonce($nonce, 'fb_form_submission_action'))
        {
			echo wp_json_encode([
				'error' => true,
				'message' => 'Cannot verify request.'
			]);
			wp_die();
        }

		$submission_id = isset($_POST['submission_id']) ? sanitize_key(wp_unslash($_POST['submission_id'])) : '';
		$new_state = isset($_POST['new_state']) ? sanitize_key(wp_unslash($_POST['new_state'])) : '';

		$new_state = $new_state === 'publish' ? 1 : 0;
		
		if (!\FireBox\Core\Helpers\Form\Submission::updateState($submission_id, $new_state))
		{
			echo wp_json_encode([
				'error' => false,
				'message' => 'Submission state couldn\'t be updated.'
			]);
			wp_die();
		}

		echo wp_json_encode([
			'error' => false,
			'message' => 'Submission state updated successfully.'
		]);
		wp_die();
	}
    
    /**
     * Form submit.
     * 
     * @return void
     */
    public function fb_form_submit()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

        // verify nonce
        if (!$verify = wp_verify_nonce($nonce, 'fbox_js_nonce'))
        {
			echo wp_json_encode([
				'error' => true,
				'message' => 'Cannot verify request.'
			]);
			wp_die();
        }

		$form_data = isset($_POST['form_data']) ? sanitize_text_field(wp_unslash($_POST['form_data'])) : '';
		$form_data = $form_data ? json_decode(html_entity_decode(stripslashes($form_data)), true) : '';
		
		if (!$form_data)
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'Cannot submit form.'
			]);
			wp_die();
		}

		$form_id = isset($form_data['form_id']) ? $form_data['form_id'] : false;
		if (!$form_id)
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'Missing Form ID.'
			]);
			wp_die();
		}

		$values = isset($form_data['fields']) ? $form_data['fields'] : false;
		if (!$form_id)
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'Missing submission data.'
			]);
			wp_die();
		}

		$form_id = str_replace('form-', '', $form_id);
		if (!$form = Form::getFormByID($form_id))
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'This form does not exist.'
			]);
			wp_die();
		}

		$form_block = $form['block'];
		$form_fields = $form['fields'];

		// Get the Campaign ID
		$box_id = isset($_POST['box_id']) ? sanitize_key(wp_unslash($_POST['box_id'])) : false;

		// Get box
		$box = firebox()->box->get($box_id);

		// Allow to hook into the form submission process and validate the submission data
		try {
			$values = apply_filters('firebox/form/process', $values, $box, $form_id);
		}
		catch (\Exception $e)
		{
			echo wp_json_encode([
				'error' => true,
				'message' => $e->getMessage()
			]);
			wp_die();
		}
		
		try {
			$validated_fields = Form::validate($form_fields, $values);
		}
		catch (\Exception $e)
		{
			echo wp_json_encode([
				'error' => true,
				'message' => $e->getMessage()
			]);
			wp_die();
		}

		if (isset($validated_fields['error']))
		{
			$payload = [
				'error' => true,
				'message' => isset($validated_fields['message']) ? $validated_fields['message'] : 'Form is invalid.'
			];

			if (is_array($validated_fields['error']))
			{
				$payload['validation'] = $validated_fields['error'];
			}
			echo wp_json_encode($payload);
			wp_die();
		}

		$submission = [];
		$submission_meta_data = [];

		/**
		 * Also set the popup log id in field values.
		 * 
		 * This is useful for analytics purposes, i.e. to track form conversions.
		 */
		$box_log_id = isset($_POST['box_log_id']) && !empty($_POST['box_log_id']) ? sanitize_key(wp_unslash($_POST['box_log_id'])) : false;
		if ($box_log_id)
		{
			$submission_meta_data['box_log_id'] = $box_log_id;
		}

		// $submission_meta_data is the raw submitted data that are saved in the database
		foreach ($validated_fields as $field)
		{
			$field_id = $field->getOptionValue('id');
			$field_name = $field->getOptionValue('name');

			if (!isset($values[$field_name]))
			{
				continue;
			}
			
			$submission_meta_data[$field_id] = $values[$field_name];
		}

		// Determine whether to store the submission and store it
		$storeSubmissions = isset($form_block['attrs']['storeSubmissions']) ? $form_block['attrs']['storeSubmissions'] : true;
		if (!$submission = Form::storeSubmission($form_id, $form_block, $validated_fields, $submission_meta_data, $storeSubmissions))
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'Could not save submission. Please try again.'
			]);
			wp_die();
		}

		// Track conversion after storing the submission
		if ($box_log_id)
		{
			/**
			 * Track conversion
			 */
			$factory = new \FPFramework\Base\Factory();
			$data = [
				'log_id' => $box_log_id,
				'event' => 'conversion',
				'event_source' => 'form',
				'event_label' => 'FireBox #' . $box_id . ' Form',
				'date' => $factory->getDate()->format('Y-m-d H:i:s')
			];
	
			firebox()->tables->boxlogdetails->insert($data);
		}

		// Replace Smart Tags in form attributes
		Form::replaceSmartTags($form_block['attrs'], $values, $submission);

		// Determine whether to run actions and run them
		if (isset($form_block['attrs']['actions']) && is_array($form_block['attrs']['actions']) && count($form_block['attrs']['actions']))
		{
			if ($box_id)
			{
				$submission['box_id'] = (int) $box_id;
			}

			$actions = new \FireBox\Core\Form\Actions\Actions($form_block, $submission);
			if (!$actions->run())
			{
				echo wp_json_encode([
					'error' => true,
					'message' => $actions->getErrorMessage()
				]);
				wp_die();
			}
		}

		$action = Form::getSubmissionAction($form_block['attrs']);

		/**
		 * Fires after a successful form submission.
		 * 
		 * @param  array  $box  	   The campaign settings
		 * @param  array  $values      The form values
		 * @param  array  $submission  The submission
		 */
		do_action('firebox/form/success', $box, $values, $submission);

		/**
		 * Allow to hook into the success action and customize it.
		 * 
		 * @param   array  $action
		 */
		$action = apply_filters('firebox/form/submit_action', $action);

		echo wp_json_encode(array_merge([
			'error' => false
		], $action));
		wp_die();
    }
}