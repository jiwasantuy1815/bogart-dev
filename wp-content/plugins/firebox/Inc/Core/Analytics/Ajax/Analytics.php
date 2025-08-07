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

namespace FireBox\Core\Analytics\Ajax;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use \FireBox\Core\Helpers\BoxHelper;

class Analytics
{
    use Shared;

    public function __construct()
    {
        add_action('wp_ajax_firebox_analytics_most_popular_campaigns', [$this, 'firebox_analytics_most_popular_campaigns']);
        add_action('wp_ajax_nopriv_firebox_analytics_most_popular_campaigns', [$this, 'firebox_analytics_most_popular_campaigns']);

        add_action('wp_ajax_firebox_analytics_get_campaign', [$this, 'firebox_analytics_get_campaign']);
        add_action('wp_ajax_nopriv_firebox_analytics_get_campaign', [$this, 'firebox_analytics_get_campaign']);

        add_action('wp_ajax_firebox_analytics_get_popular_view_items', [$this, 'firebox_analytics_get_popular_view_items']);
        add_action('wp_ajax_nopriv_firebox_analytics_get_popular_view_items', [$this, 'firebox_analytics_get_popular_view_items']);

        add_action('wp_ajax_firebox_analytics_get_day_of_the_week', [$this, 'firebox_analytics_get_day_of_the_week']);
        add_action('wp_ajax_nopriv_firebox_analytics_get_day_of_the_week', [$this, 'firebox_analytics_get_day_of_the_week']);

        add_action('wp_ajax_firebox_analytics_get_shared_data', [$this, 'firebox_analytics_get_shared_data']);
        add_action('wp_ajax_nopriv_firebox_analytics_get_shared_data', [$this, 'firebox_analytics_get_shared_data']);

        add_action('wp_ajax_firebox_analytics_get_referrers', [$this, 'firebox_analytics_get_referrers']);
        add_action('wp_ajax_nopriv_firebox_analytics_get_referrers', [$this, 'firebox_analytics_get_referrers']);

        add_action('wp_ajax_firebox_analytics_get_conversions_data', [$this, 'firebox_analytics_get_conversions_data']);
        add_action('wp_ajax_nopriv_firebox_analytics_get_conversions_data', [$this, 'firebox_analytics_get_conversions_data']);
    }

    /**
     * Most Popular Campaigns
     * 
     * @return  void
     */
    public function firebox_analytics_most_popular_campaigns()
    {
		if (!current_user_can('manage_options'))
		{
			return;
        }
        
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        
        // verify nonce
        if (!$verify = wp_verify_nonce($nonce, 'fpf_js_nonce'))
        {
            return false;
		}

        $start_date = isset($_POST['start_date']) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : '';
        $end_date = isset($_POST['end_date']) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : '';

        if (!$start_date || $start_date === 'false' || !$end_date || $end_date === 'false')
        {
            return;
        }

        
        $start_date_ts = strtotime($start_date);
        $end_date_ts = strtotime($end_date);
        $days_between = ceil(abs($end_date_ts - $start_date_ts) / 86400);

        // prepare labels
        $labels = [];

        // We are fetching data for a single day
        if ($days_between == 1)
        {
            for ($hour = 0; $hour < 24; $hour++)
            {
                $start_hour = sprintf('%02d', $hour);
                $labels[] = $start_hour . ':00';
            }
        }
        // Multiple days
        else
        {
            for ($i = 0; $i < $days_between; $i++)
            {
                $labels[] = gmdate("Y-m-d", $start_date_ts);
                $start_date_ts = strtotime("+1 day", $start_date_ts);
            }
        }

        $data = new \FireBox\Core\Analytics\Data($start_date, $end_date);

        $metrics = [
            'views',
            'conversions',
            'conversionrate'
        ];
        $data->setMetrics($metrics);
		
		$data->setLimit(1);

        $top_campaigns = $data->getData('top_campaign');

        $chart_data = [];

        foreach ($top_campaigns as $metric => $metricData)
        {
            $campaignId = isset($metricData[0]->id) ? $metricData[0]->id : false;
            $campaignTitle = isset($metricData[0]->label) ? $metricData[0]->label : false;
            $campaignTotal = isset($metricData[0]->total) ? $metricData[0]->total : 0;

            if (!$campaignId)
            {
                $chart_data[$metric] = [];
                continue;
            }
            
            $data->setMetrics([$metric]);
            $data->setLimit(null);
            $data->setFilters(['campaign' => ['value' => [$campaignId]]]);

            $_chartData = $data->getData('list');

            $chart_data[$metric] = [
                'title' => $campaignTitle,
                'currentTotal' => (float) $campaignTotal,
                'previousTotal' => $this->getPreviousPeriodTotal($metric, $campaignId, $start_date, $end_date),
                'data' => $_chartData[$metric]
            ];
        }

        echo wp_json_encode([
            'labels' => $labels,
            'data' => $chart_data
        ]);
        wp_die();
        

        
    }

    
    private function getPreviousPeriodTotal($metric, $campaignId, $start_date, $end_date)
    {
        $total = 0;

        // Also calculate the previous period data
        $start_date_ts = strtotime($start_date);
        $end_date_ts = strtotime($end_date);
        $days_between = ceil(abs($end_date_ts - $start_date_ts) / 86400);

        if ($previousPeriodDates = $this->getPreviousPeriodDates($start_date, $days_between))
        {
            $previousData = new \FireBox\Core\Analytics\Data($previousPeriodDates[0], $previousPeriodDates[1]);
    
            $metrics = [
                $metric
            ];
            $previousData->setMetrics($metrics);
    
            $filters = [];
            if ($campaignId)
            {
                $filters['campaign'] = [
                    'value' => [$campaignId]
                ];
            }
            $previousData->setFilters($filters);

            $previousPeriodData = $previousData->getData('count');

            $total = isset($previousPeriodData[$metric]) ? (float) $previousPeriodData[$metric] : $total;
        }
        
        return $total;
    }
    

    /**
     * Get single campaign data
     * 
     * @return  void
     */
    public function firebox_analytics_get_campaign()
    {
		if (!current_user_can('manage_options'))
		{
			return;
        }
        
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        
        // verify nonce
        if (!$verify = wp_verify_nonce($nonce, 'fpf_js_nonce'))
        {
            return false;
		}

        $campaign = isset($_POST['campaign']) ? intval($_POST['campaign']) : '';
        if (!$campaign)
        {
            return;
        }

        // Get campaign
        $campaign = BoxHelper::getBoxData($campaign);
        if (!$campaign)
        {
            return;
        }

        // Get campaign meta
        $campaign->params = BoxHelper::getMeta($campaign->ID);

        // Get last date viewed
        $campaign->last_date_viewed = BoxHelper::getCampaignLastDateViewed($campaign->ID);

        echo wp_json_encode([
            'error' => false,
            'campaign' => $campaign
        ]);
        wp_die();
    }

    /**
     * Get popular view times data
     * 
     * @return  void
     */
    public function firebox_analytics_get_popular_view_items()
    {
		if (!current_user_can('manage_options'))
		{
			return;
        }
        
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        
        // verify nonce
        if (!$verify = wp_verify_nonce($nonce, 'fpf_js_nonce'))
        {
            return false;
		}

        $start_date = isset($_POST['start_date']) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : '';
        $end_date = isset($_POST['end_date']) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : '';
        $weekday = isset($_POST['weekday']) ? intval($_POST['weekday']) : false;

        if (!$start_date || $start_date === 'false' || !$end_date || $end_date === 'false')
        {
            return;
        }

        
        // Get campaign
        $campaign = isset($_POST['campaign']) ? intval($_POST['campaign']) : false;

        $options = [];

        if ($weekday || $weekday === 0)
        {
            $options['weekday'] = $weekday;
        }
        
        $data = new \FireBox\Core\Analytics\Data($start_date, $end_date, $options);

        $metrics = [
            'views'
        ];
        $data->setMetrics($metrics);

        // Set campaign filter
        if ($campaign)
        {
            $data->setFilters(['campaign' => ['value' => [$campaign]]]);
        }
		
        $data = $data->getData('popular_view_times');

        $data = isset($data['views']) ? $data['views'] : [];
        
        echo wp_json_encode($data);
        wp_die();
        

        
    }

    /**
     * Get Day of the week data
     * 
     * @return  void
     */
    public function firebox_analytics_get_day_of_the_week()
    {
		if (!current_user_can('manage_options'))
		{
			return;
        }
        
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        
        // verify nonce
        if (!$verify = wp_verify_nonce($nonce, 'fpf_js_nonce'))
        {
            return false;
		}

        $start_date = isset($_POST['start_date']) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : '';
        $end_date = isset($_POST['end_date']) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : '';

        if (!$start_date || $start_date === 'false' || !$end_date || $end_date === 'false')
        {
            return;
        }

        
        // Get campaign
        $campaign = isset($_POST['campaign']) ? intval($_POST['campaign']) : false;

        $data = new \FireBox\Core\Analytics\Data($start_date, $end_date);

        $metrics = [
            'views',
            'conversions',
            'conversionrate'
        ];
        $data->setMetrics($metrics);

        // Set campaign filter
        if ($campaign)
        {
            $data->setFilters(['campaign' => ['value' => [$campaign]]]);
        }
		
        $data = $data->getData('day_of_week');

        echo wp_json_encode($data);
        wp_die();
        

        
    }

    /**
     * Get shared data.
     * 
     * Countries
     * Referrers
     * Devices
     * Events
     * Pages
     * 
     * @return  void
     */
    public function firebox_analytics_get_shared_data()
    {
		if (!current_user_can('manage_options'))
		{
			return;
        }
        
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        
        // verify nonce
        if (!$verify = wp_verify_nonce($nonce, 'fpf_js_nonce'))
        {
            return false;
		}

        $start_date = isset($_POST['start_date']) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : '';
        $end_date = isset($_POST['end_date']) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : '';

        if (!$start_date || $start_date === 'false' || !$end_date || $end_date === 'false')
        {
            return;
        }

        
        $item = isset($_POST['item']) ? sanitize_text_field(wp_unslash($_POST['item'])) : '';

        $allowed_items = [
            'countries',
            'referrers',
            'devices',
            'events',
            'pages',
            'top_campaign'
        ];

        if (!in_array($item, $allowed_items))
        {
            return;
        }

        $data = new \FireBox\Core\Analytics\Data($start_date, $end_date);

        // Set campaign filter
        $campaign = isset($_POST['campaign']) ? intval($_POST['campaign']) : false;
        if ($campaign)
        {
            $data->setFilters(['campaign' => ['value' => [$campaign]]]);
        }

        $metrics = [
            'views',
            'conversions',
            'conversionrate'
        ];

        // For events, we only care about views
        if ($item === 'events')
        {
            $metrics = ['views'];
        }
        $data->setMetrics($metrics);
        $data->setLimit(30);

        $data = $data->getData($item);

        echo wp_json_encode($data);
        wp_die();
        
        
        
    }

    /**
     * Get conversions and conversion rate data per popup.
     * 
     * @return  void
     */
    public function firebox_analytics_get_conversions_data()
    {
		if (!current_user_can('manage_options'))
		{
			return;
        }
        
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        
        // verify nonce
        if (!$verify = wp_verify_nonce($nonce, 'fpf_js_nonce'))
        {
            return false;
		}

        $start_date = isset($_POST['start_date']) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : '';
        $end_date = isset($_POST['end_date']) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : '';

        if (!$start_date || $start_date === 'false' || !$end_date || $end_date === 'false')
        {
            return;
        }

        
        $data = new \FireBox\Core\Analytics\Data($start_date, $end_date);

        $data->setMetrics(['conversions']);

        $top_campaigns = $data->getData('top_campaign');

        $finalData = [];

        foreach ($top_campaigns['conversions'] as $metricData)
        {
            $campaignId = isset($metricData->id) ? $metricData->id : false;

            if (!$campaignId)
            {
                continue;
            }

            $campaignTitle = isset($metricData->label) ? $metricData->label : false;
            $campaignTotal = isset($metricData->total) ? $metricData->total : 0;
            
            // Get conversion rate
            $data->setMetrics(['conversionrate']);
            $data->setFilters(['campaign' => ['value' => [$campaignId]]]);
            $conversionRate = $data->getData('count');

            $finalData[] = [
                'id' => $campaignId,
                'title' => $campaignTitle,
                'conversions' => (float) $campaignTotal,
                'conversionrate' => isset($conversionRate['conversionrate']) ? ((float) $conversionRate['conversionrate']) . '%' : 0
            ];
        }

        echo wp_json_encode($finalData);
        wp_die();
        

        
    }
}