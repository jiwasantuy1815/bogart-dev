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

namespace FireBox\Core\FB;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FireBox\Core\Helpers\BoxHelper;
use FPFramework\Libs\Registry;
use FPFramework\Helpers\Fields\DimensionsHelper;
use FPFramework\Helpers\CSS;

class Box
{
	/**
	 * Send useful JS snippet once in first box
	 * 
	 * @var  boolean
	 */
	static $loadedLocalizedScript = false;

	/**
	 * The box.
	 * 
	 * @param   object
	 */
	private $box = null;

	/**
	 * Factory
	 * 
	 * @var  Factory
	 */
	private $factory = null;

	/**
	 * FireBox settings.
	 * 
	 * @var  object
	 */
	private $params = null;

	/**
	 * Popup CSS.
	 * 
	 * @var  CSS
	 */
	public $css = null;

	/**
	 * Constructor.
	 * 
	 * @param   object  $box
	 * @param   object  $factory
	 * 
	 * @return  void
	 */
	public function __construct($box = null, $factory = null)
	{
		if ($box)
		{
			$this->box = $this->prepareConstructorBox($box);
		}

		if (!$factory)
		{
			$factory = new \FPFramework\Base\Factory();
		}
		$this->factory = $factory;

		$this->params = new Registry(BoxHelper::getParams());
	}

	/**
	 * Allow to set either a box ID or box object
	 * and we then set the box object.
	 * 
	 * @param   mixed   $box
	 * 
	 * @return  object
	 */
	private function prepareConstructorBox($box)
	{
		if (!is_object($box))
		{
			$box = $this->get($box);
		}
		
		return $box;
	}

	/**
	 * Get a box.
	 * 
	 * @param   int     $id
	 * @param   string  $status
	 * 
	 * @return  object
	 */
	public function get($id = null, $status = null)
	{
		if (!$id)
		{
			return;
		}

		$payload = [
			'where' => [
				'ID' => ' = ' . esc_sql(intval($id)),
				'post_type' => " = 'firebox'"
			]
		];

		// apply status if given
		if ($status)
		{
			$payload['where']['post_status'] = ' = \'' . esc_sql($status) . '\'';
		}
		
		if (!$box = firebox()->tables->box->getResults($payload))
		{
			return [];
		}

		if (!isset($box[0]))
		{
			return [];
		}
		
		$this->box = $box[0];

		// get meta options for box
		$meta = \FireBox\Core\Helpers\BoxHelper::getMeta($id);
		$this->box->params = new Registry($meta);

		return $this->box;
	}

	/**
	 * Renders the box.
	 * 
	 * @return  void
	 */
	public function render()
	{
		// Check Publishing Assignments
        if (!$this->pass())
        {
			return false;
		}

		$fbox = $this->box;
		
		/**
		 * Runs before rendering the box.
		 */
		$this->box = apply_filters('firebox/box/before_render', $this->box);

		$this->prepare();
		
		$css = $this->getCustomCSS();

		add_action('wp_enqueue_scripts', function() use ($fbox, $css) {
			// Loads all media files.
			$this->loadBoxMedia($fbox);

			// Load CSS
			if ($css)
			{
				wp_add_inline_style('firebox', $css);
			}

			
			if ($fbox->params->get('triggermethod') === 'floatingbutton' || $fbox->params->get('floating_button_show_on_close', '0'))
			{
				$floating_button_vars = [
					'color' => $fbox->params->get('floatingbutton_message.textcolor', '#fff'),
					'bgColor' => $fbox->params->get('floatingbutton_message.bgcolor', '#4285F4'),
					'fontSize' => $fbox->params->get('floatingbutton_message.fontsize', '16px')
				];
	
				$floating_button_cssvars = \FPFramework\Helpers\CSS::cssVarsToString($floating_button_vars, '.fb-' . $fbox->ID . '.fb-floating-button');
				wp_add_inline_style('firebox', $floating_button_cssvars);
			}
			
		});
		
		// Allow to manipulate the box before rendering
		$this->box = apply_filters('firebox/box/edit', $this->box);

		// payload
		$payload = [
			'box' => $this->box,
			'params' => $this->params,
		];

		// print campaign HTML
		add_action('wp_footer', function() use ($payload) {
			echo $this->getFinalCampaignHTML($payload); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		});

		return true;
	}

	public function renderEmbed()
	{
		// Check Publishing Assignments
        if (!$this->pass())
        {
			return false;
		}

		/**
		 * Runs before rendering the box.
		 */
		$this->box = apply_filters('firebox/box/before_render', $this->box);

		$this->prepare();
		
		// Loads all media files.
		$this->loadBoxMedia($this->box);

		$css = $this->getCustomCSS();

		wp_register_style('fireboxStyle', false);
		wp_enqueue_style('fireboxStyle');

		// Load CSS
		if ($css)
		{
			wp_add_inline_style('fireboxStyle', $css);
		}

		
		if ($this->box->params->get('triggermethod') === 'floatingbutton' || $this->box->params->get('floating_button_show_on_close', '0'))
		{
			$floating_button_vars = [
				'color' => $this->box->params->get('floatingbutton_message.textcolor', '#fff'),
				'bgColor' => $this->box->params->get('floatingbutton_message.bgcolor', '#4285F4'),
				'fontSize' => (int) $this->box->params->get('floatingbutton_message.fontsize', 16) . 'px'
			];

			$floating_button_cssvars = \FPFramework\Helpers\CSS::cssVarsToString($floating_button_vars, '.fb-' . $this->box->ID . '.fb-floating-button');
			wp_add_inline_style('fireboxStyle', $floating_button_cssvars);
		}
		
		
		// Allow to manipulate the box before rendering
		$this->box = apply_filters('firebox/box/edit', $this->box);

		// payload
		$payload = [
			'box' => $this->box,
			'params' => $this->params,
		];

		// return box template
		return $this->getFinalCampaignHTML($payload);
	}

	public function getFinalCampaignHTML($payload)
	{
		$html = firebox()->renderer->public->render('box', $payload, true);

		/**
		 * Runs after rendering the box.
		 */
		return apply_filters('firebox/box/after_render', $html, $payload['box']);
	}

	/**
	 * Gets the Custom CSS of the popup.
	 * 
	 * @return  string
	 */
	public function getCustomCSS()
	{
		return $this->box->params->get('customcss', '');
	}

	/**
	 * Send a helpful object to JavaScript files
	 *
	 * @return  void
	 */
	public static function setJSObject()
	{
		if (self::$loadedLocalizedScript)
		{
			return;
		}
		self::$loadedLocalizedScript = true;
		
		$data = [
			'ajax_url'	=> admin_url('admin-ajax.php'),
			'nonce'		=> wp_create_nonce('fbox_js_nonce'),
			'site_url'	=> site_url('/'),
			'referrer'	=> isset($_SERVER['HTTP_REFERER']) ? sanitize_url(wp_unslash($_SERVER['HTTP_REFERER'])) : ''
		];

		wp_add_inline_script('firebox-main', 'const fbox_js_object = ' . wp_json_encode($data), 'before');
	}

	/**
	 * Load Box Media
	 * 
	 * @return  void
	 */
	public function loadBoxMedia($box)
	{
		$box = new Registry($box);

		wp_enqueue_style(
			'firebox-animations',
			FBOX_MEDIA_PUBLIC_URL . 'css/vendor/animate.min.css',
			[],
			FBOX_VERSION
		);

		/**
		 * FireBox JS
		 */
		wp_enqueue_script(
			'firebox-main',
			FBOX_MEDIA_PUBLIC_URL . 'js/firebox.js',
			[],
			FBOX_VERSION,
			true
		);

		// Add Custom Javascript
		$custom_code = $box->get('params.data.customcode', '');
		if (is_string($custom_code) && !empty($custom_code))
		{
			$custom_code = html_entity_decode(stripslashes($custom_code));
			wp_add_inline_script('firebox-main', $custom_code, 'after');
		}

		// run above the main JS script to run only once
        self::setJSObject();
		
		/**
		 * FireBox CSS
		 */
		if ($this->params->get('loadCSS', true))
		{
			wp_enqueue_style(
				'firebox',
				FBOX_MEDIA_PUBLIC_URL . 'css/firebox.css',
				[],
				FBOX_VERSION
			);
		}

		/**
		 * Page Slide mode JS
		 */
		if ($box->get('params.data.mode') == 'pageslide')
		{
			wp_enqueue_script(
				'firebox-pageslide-mode',
				FBOX_MEDIA_PUBLIC_URL . 'js/pageslide_mode.js',
				['firebox-main'],
				FBOX_VERSION,
				true
			);
		}

		
		if (apply_filters('firebox/box/load_gatracker', true))
		{
			/**
			 * Google Analytics JS
			 */
			wp_enqueue_script(
				'firebox-gatracker',
				FBOX_MEDIA_PUBLIC_URL . 'js/gatracker.js',
				['firebox-main'],
				FBOX_VERSION,
				true
			);
		}

		// Load the expression script only if an expression shortcode is found in the content
		if (strpos($box->get('post_content'), '{fbExpr') !== false)
		{
			wp_enqueue_script(
				'firebox-expression',
				FBOX_MEDIA_PUBLIC_URL . 'js/expression.js',
				['firebox-main'],
				FBOX_VERSION,
				true
			);
		}
		
	}

	/**
	 * Prepares the box before rendering
	 * 
	 * @return  void
	 */
	public function prepare()
	{
		remove_filter('the_content', 'wptexturize');

		$cParam = BoxHelper::getParams();
		$cParam = new Registry($cParam);

		$this->box->post_content = apply_filters('the_content', $this->box->post_content);

		$mode = $this->box->params->get('mode');
		
        /* Classes */
        $css_class = [
            $this->box->ID,
			$mode
		];

		if (in_array($mode, ['popup', 'stickybar', 'sidebar', 'floating', 'slide-in']))
		{
			$position = $this->box->params->get('position', '');
			$position = !is_string($position) ? '' : $position;
			if ($position)
			{
				$css_class[] = $position;
			}
		}
		else if ($mode === 'fullscreen')
		{
			if ($center_content = $this->box->params->get('center_content', false)) {
				$css_class[] = 'center-content';
			}
		}
		
		self::prefixCSSClasses($css_class);
		
		// Class suffix
		$classSuffix = $this->box->params->get('classsuffix', '');
		$classSuffix = is_string($classSuffix) ? $classSuffix : '';
		
        $css_class[] = $classSuffix;
		
		$this->box->classes = $css_class;
		
		// Dialog CSS Classes
        $dialog_css_classes = [
			// Add Box shadow
            $this->box->params->get('boxshadow') ? 'shdelevation' : null
		];

		// Align Content
		$aligncontent = is_string($this->box->params->get('aligncontent')) ? explode(' ', $this->box->params->get('aligncontent')) : [];
        $dialog_css_classes = array_merge($dialog_css_classes, $aligncontent);
		
        self::prefixCSSClasses($dialog_css_classes);
		$this->box->dialog_classes = $dialog_css_classes;
		
        $trigger_point_methods = [
            'pageload'     => 'onPageLoad',
            'onclick'      => 'onClick',
            'elementHover' => 'onHover',
            'ondemand'     => 'onDemand',
			
            'pageheight'   => 'onScrollDepth',
            'element'      => 'onElementVisibility',
            'userleave'    => 'onExit',
            'onexternallink' => 'onExternalLink',
			
		];

		/* Other Settings */
		$this->box->params->set('animation_duration', $this->box->params->get('animation_duration', 0.2));

		$scroll_amount = $this->box->params->get('scroll_amount', '80%');

		// Parse scroll_amount to extract unit and value
		$scroll_amount_data = $this->parseScrollAmount($scroll_amount);

		$delay = in_array($this->box->params->get('triggermethod'), ['floatingbutton', 'onexternallink']) ? 0 : (int) $this->box->params->get('triggerdelay') * 1000;

		$trigger_method = (is_string($this->box->params->get('triggermethod'))) && array_key_exists($this->box->params->get('triggermethod'), $trigger_point_methods) ? $trigger_point_methods[$this->box->params->get('triggermethod')] : $this->box->params->get('triggermethod');

		$trigger_element = is_scalar($this->box->params->get('triggerelement', '')) ? $this->box->params->get('triggerelement', '') : '';

        // Use Namespaced classes for each trigger point and let them manipulate the settings dynamicaly.
        $this->box->settings = [
			'name'				   => $this->box->post_title,
            'trigger'              => $trigger_method,
            'trigger_selector'     => $trigger_method === 'onExternalLink' ? '' : rtrim($trigger_element, ','),
            'delay'                => $delay,
			
            'scroll_depth'         => $scroll_amount_data['unit'] === '%' ? 'percentage' : 'pixel',
			'scroll_depth_value'   => (int) $scroll_amount_data['value'],
			'firing_frequency'     => (int) $this->box->params->get('firing_frequency', 1),
            'early_trigger' 	   => (bool) $this->box->params->get('early_trigger', false),
            'reverse_scroll_close' => (bool) $this->box->params->get('autohide'),
			'threshold'            => (float) $this->box->params->get('threshold', 0) / 100,
			'close_out_viewport'   => (bool) $this->box->params->get('close_out_viewport', false),
            'exit_timer'           => (int) $this->box->params->get('exittimer') * 1000,
            'idle_time'            => (int) $this->box->params->get('idle_time') * 1000,
			
            'close_on_esc'         => (bool) $this->box->params->get('close_on_esc', false),
            'animation_open'       => $this->box->params->get('animationin'),
            'animation_close'      => $this->box->params->get('animationout'),
			'animation_duration'   => (float) $this->box->params->get('animation_duration') * 1000,
			'prevent_default'      => true,
            'backdrop'             => (bool) $this->box->params->get('overlay'),
            'backdrop_color'       => $this->box->params->get('overlay_color'),
            'backdrop_click'       => (bool) $this->box->params->get('overlayclick'),
            'disable_page_scroll'  => (bool) $this->box->params->get('preventpagescroll'),
            'test_mode'            => (bool) $this->box->params->get('testmode'),
            'debug'                => (bool) $cParam->get('debug', false),
			'auto_focus'		   => (bool) $this->box->params->get('autofocus', false),
			'mode'				   => $this->box->params->get('mode')
		];

		$this->css = new Styling\CSS($this->box);

		// Apply Popup CSS
		$this->box->params->set('customcss', $this->box->params->get('customcss') . $this->css->getCSS());

		$this->replaceBoxSmartTags();

		add_filter('the_content', 'wptexturize');
	}

	/**
	 * Parses scroll_amount to extract unit and value
	 * 
	 * @param   mixed  $scroll_amount
	 * 
	 * @return  array
	 */
	private function parseScrollAmount($scroll_amount)
	{
		if (is_array($scroll_amount))
		{
			return [
				'unit' => $scroll_amount['unit'] ?? '%',
				'value' => $scroll_amount['value'] ?? 80
			];
		}

		if (is_string($scroll_amount) && preg_match('/^(\d+)(px|%)$/', $scroll_amount, $matches))
		{
			return [
				'unit' => $matches[2],
				'value' => $matches[1]
			];
		}

		return [
			'unit' => '%',
			'value' => 80
		];
	}

	/**
	 * Replaces all box smart tags
	 * 
	 * @return  object
	 */
	public function replaceBoxSmartTags()
	{
		$tags = new \FPFramework\Base\SmartTags\SmartTags();

		// register FB Smart Tags
		$tags->register('\FireBox\Core\SmartTags', FBOX_BASE_FOLDER . '/Inc/Core/SmartTags', $this->box);

		$this->box = $tags->replace($this->box);
	}

	/**
	 * Checks if a box passes assignments
	 * 
	 * @return  boolean
	 */
	public function pass()
    {
        if (!$this->box || !is_object($this->box))
        {
            return false;
		}

        // Check first local assignments
        if (!$this->passLocalAssignments())
        {
            return false;
        }

        $displayConditionsType = $this->box->params->get('display_conditions_type', '');

        // If empty, display popup sitewide
        if (empty($displayConditionsType) || $displayConditionsType === 'all')
        {
            return true;
        }
		
        // Mirror Display Conditions of another popup.
        if ($displayConditionsType == 'mirror' && $mirror_box_id = $this->box->params->get('mirror_box'))
        {
            $this->box->params->merge(self::getAssignmentsForMirroring($mirror_box_id));
        }

		// Get a recursive array of all rules
		$rules = $this->box->params->get('rules', []);
		$rules = is_string($rules) ? json_decode($rules, true) : json_decode(wp_json_encode($rules), true);

		// If testmode is enabled disable the User Groups condition
        if ($this->box->params->get('testmode'))
        {
            foreach ($rules as $key => &$group)
            {
                foreach ($group['rules'] as $_key => &$rule)
                {
                    if (!isset($rule['name']) || empty($rule['name']))
                    {
                        continue;
                    }

                    if ($rule['name'] === 'WP\UserGroup')
                    {
                        unset($group['rules'][$_key]);
                    }
                }
            }
        }

        // Check framework based conditions
        return \FPFramework\Base\Conditions\ConditionBuilder::pass($rules, $this->factory);
	}

    /**
     * Check if a box passes local conditions
     *
     * @return  boolean
     */
    private function passLocalAssignments()
    {
        $localAssignments = new \FireBox\Core\FB\Assignments($this, $this->factory);
        return $localAssignments->passAll();
    }
	
	/**
	 * Gets assignments of mirrored box
	 * 
	 * @param   int  $box_id
	 * 
	 * @return  object
	 */
	private function getAssignmentsForMirroring($box_id)
    {   
		$payload = [
			'where' => [
				'ID' => ' = ' . intval($box_id),
				'post_status' => " = 'publish'",
				'post_type' => " = 'firebox'"
			]
		];
		
        // Load box
		if (!$box = firebox()->tables->box->getResults($payload))
		{
            return;
		}
		
		$box = $box[0];
		
		// get meta options for box
		$meta = BoxHelper::getMeta($box->ID);
		$box->params = new Registry($meta);

        return new Registry(['rules' => $box->params->get('rules')]);
    }

	/**
	 * Prefixes the CSS classes
	 * 
	 * @param   array   $classes
	 * @param   string  $prefix
	 * 
	 * @return  void
	 */
    private static function prefixCSSClasses(&$classes, $prefix = 'fb-')
    {
		$classes = array_filter($classes);
		
		if (empty($classes))
		{
			return;
		}

        foreach ($classes as &$class)
        {
            $class = $prefix . $class;
        }
    }

	/**
	 * Track box open
	 * 
	 * @param   integer  $box_id
	 * @param   string   $page
	 * @param   string   $referrer
	 * 
	 * @return  void
	 */
    public function logOpenEvent($box_id, $page = null, $referrer = null)
    {
        $box = $this->get($box_id);

        // Do not track if statistics option is disabled
		$track_open_event = (bool) (is_null($box->params->get('stats', null)) ? true : $box->params->get('stats'));
        if (!$track_open_event)
        {
            return;
        }

        return firebox()->log->track($box_id, 1, null, $page, $referrer);
    }

	/**
	 * Track box close
	 * 
	 * @param   integer  $box_id
	 * @param   integer  $box_log_id
	 * 
	 * @return  void
	 */
    public function logCloseEvent($box_id, $box_log_id)
    {
        $box = $this->get($box_id);

        // Do not track if statistics option is disabled
		$track_open_event = (bool) (is_null($box->params->get('stats', null)) ? true : $box->params->get('stats'));
        if (!$track_open_event)
        {
            return null;
        }

        firebox()->log->track($box_id, 2, $box_log_id);
	}

	/**
	 * Get total box impressions
	 * 
	 * @param   array  $payload
	 * 
	 * @return  array
	 */
	public function getTotalImpressions($payload)
	{
		$impressions = firebox()->tables->boxlog->getResults($payload);
		
		return count($impressions);
	}

	/**
	 * Returns the cookie instance.
	 * 
	 * @return  mixed
	 */
	public function getCookie()
	{
		if (!$this->box)
		{
			return;
		}
		
		return new Cookie($this->box);
	}

	/**
	 * Returns the box.
	 * 
	 * @return  object
	 */
	public function getBox()
	{
		return $this->box;
	}

	/**
	 * Sets the box.
	 * 
	 * @param   object  $box
	 * 
	 * @return  Box
	 */
	public function setBox($box)
	{
		$this->box = $box;

		return $this;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function setParams($params)
	{
		$this->params = $params;

		return $this;
	}

	public function getCampaignParams()
	{
		return isset($this->box->params) ? $this->box->params : null;
	}

	public function setCampaignParams($params)
	{
		if (isset($this->box->params))
		{
			$this->box->params = $params;
		}

		return $this;
	}
}