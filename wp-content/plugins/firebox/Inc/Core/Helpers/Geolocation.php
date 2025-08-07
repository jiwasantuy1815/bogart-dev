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

class Geolocation
{
    /**
     * Checks whether there are any campaigns using Geolocation conditions.
     * 
     * @return  bool
     */
    public static function campaignsUsingGeolocation()
    {
        $campaigns = get_posts([
            'post_type' => 'firebox',
            'posts_per_page' => 1000,
            'post_status' => 'publish',
            'fields' => 'ids'
        ]);

        $geolocationConditions = [
            'Geo\City',
            'Geo\Country',
            'Geo\Region',
            'Geo\Continent'
        ];

        $found = false;

        foreach ($campaigns as $id)
        {
            $params = \FireBox\Core\Helpers\BoxHelper::getMeta($id);

            $display_conditions_type = isset($params['display_conditions_type']) ? $params['display_conditions_type'] : '';

            if ($display_conditions_type !== 'custom')
            {
                continue;
            }

            $conditions = is_string($params['rules']) ? json_decode($params['rules'], true) : $params['rules'];
            
            if (!$conditions)
            {
                continue;
            }
            
            foreach ($conditions as $key => $set)
            {
                if (!isset($set['enabled']) || $set['enabled'] != '1')
                {
                    continue;
                }

                foreach ($set['rules'] as $key2 => $rule)
                {
                    if (!isset($rule['name']))
                    {
                        continue;
                    }

                    if (!in_array($rule['name'], $geolocationConditions))
                    {
                        continue;
                    }

                    if (!isset($rule['enabled']) || $rule['enabled'] != '1')
                    {
                        continue;
                    }

                    if (!isset($rule['value']))
                    {
                        continue;
                    }

                    if (!\FPFramework\Base\Conditions\ConditionBuilder::prepareRepeaterValue($rule['value']))
                    {
                        continue;
                    }
                    
                    $found = true;
                    break;
                }
            }
        }
        
        return $found;
    }
}