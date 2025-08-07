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

class AcyMailing extends \FireBox\Core\Form\Actions\Action
{
	protected function prepare()
	{
		$this->action_settings = [
			'list_id' => isset($this->form_settings['attrs']['acymailingListID']) ? trim($this->form_settings['attrs']['acymailingListID']) : '',
			'doubleoptin' => isset($this->form_settings['attrs']['acymailingDoubleOptin']) ? $this->form_settings['attrs']['acymailingDoubleOptin'] : false,
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
		$api = new \FPFramework\Base\Integrations\AcyMailing();
		$api->setMetadata($this->getMetadata());

		$api->subscribe(
			$this->getEmailValue(),
			$this->field_values,
			$this->action_settings['list_id'],
			$this->action_settings['doubleoptin']
		);
		
		if (!$api->success())
		{
			throw new \Exception(esc_html($api->getLastError()));
		}

		return true;
	}

	public function getMetadata()
	{
		$source = 'FireBox - #' . $this->submission['box_id'] . ' - ' . get_the_title($this->submission['box_id']);
		
		return [
			'source' => $source
		];
	}

	/**
	 * Validates the action prior to running it.
	 * 
	 * @return  void
	 */
	public function validate()
	{
		if (empty($this->action_settings['list_id']))
		{
			throw new \Exception(esc_html(sprintf(firebox()->_('FB_INTEGRATION_ERROR_NO_LIST_SELECTED'), 'AcyMailing')));
		}

		return true;
	}
}