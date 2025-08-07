<?php
namespace GutenkitPro\Hooks;

defined( 'ABSPATH' ) || exit;

use Gutenkit\Helpers\Utils;
use \GutenkitProScopedDependencies\foroco\BrowserDetection;

class DisplayConditions {

    use \Gutenkit\Traits\Singleton;

    /**
     * Class constructor.
     * Private for singleton.
     *
     * @return void
     * @since 1.0.0
     */
    public function __construct() {
        if(!is_admin()){
            add_filter( 'render_block', array( $this, 'handle_display_conditions' ), 10, 3 );
        }
    }

    // Define available operators for different types of conditions
    private $numberOperators = [ '==', '!=', '>=', '<=', '>', '<' ];
    private $stringOperators = [ '==', '!=', 'contains', 'notContains' ];
    private $booleanOperators = [ '==', '!=' ];
    private $userRegisteredStatus = [ 'after', 'before' ];
    private $featuredImagesStatus = [ 'notSet', 'set' ];

    /**
     * Evaluates the condition based on the provided parameters.
     *
     * @param string $condition_type The type of condition (e.g., 'number', 'string').
     * @param string $operator The operator to use for comparison (e.g., '==', 'contains').
     * @param mixed $value The value to compare against.
     * @param mixed $target The target value for the comparison.
     * @return bool True if the condition is met, false otherwise.
     */
    public function is_met_condition($condition_type, $operator, $value, $target)
    {
        switch ($condition_type) {
            case 'postId':
            case 'postParent':
            case 'postDate':
            case 'userId':
            case 'weekday':
                return $this->evaluate_common_condition($operator, $value, $target);
            case 'date': 
                return $this->evaluate_common_date_condition($operator, $value, $target, 'F j, Y');
            case 'time':
                return $this->evaluate_common_date_condition($operator, $value, $target, 'g:i A');
            case 'dateAndTime':
                return $this->evaluate_common_date_condition($operator, $value, $target);
            case 'postTitle':
            case 'postStatus':
            case 'postAuthor':
            case 'userRole':
            case 'browser':
            case 'operatingSystem':
            case 'currentUrl':
            case 'referrerUrl':
                return $this->evaluate_string_condition($operator, $value, $target);
            case 'userRegistered':
                return $this->evaluate_date_condition($operator, $value, $target);
            case 'featuredImage':
                return $this->evaluate_featured_image_condition($operator, $value, $target);
            case 'userLogin':
                return $this->evaluate_user_login_condition($operator, $value, $target);
            default:
                return false;
        }
    }

    /**
     * Evaluates common conditions (number and boolean).
     *
     * @param string $operator The operator to use for comparison.
     * @param mixed $value The value to compare against.
     * @param mixed $target The target value for the comparison.
     * @return bool True if the condition is met, false otherwise.
     */
    private function evaluate_common_condition($operator, $value, $target)
    {
        $value = isset($value['value']) ? $value['value'] : ($value === null ? '' : $value);
        switch ($operator) {
            case '==':
                return $value == $target;
            case '!=':
                return $value != $target;
            case '>=':
                return $target >= $value;
            case '<=':
                return $target <= $value;
            case '>':
                return $target > $value;
            case '<':
                return $target < $value;
            default:
                return false;
        }
    }

    /**
     * Evaluates common conditions (number and boolean).
     *
     * @param string $operator The operator to use for comparison.
     * @param mixed $value The value to compare against.
     * @param mixed $target The target value for the comparison.
     * @param string $format The date format to use.
     * @return bool True if the condition is met, false otherwise.
     */
    private function evaluate_common_date_condition($operator, $value, $target, $format = 'F j, Y g:i A')
    {
        $value = isset($value) ? date($format, strtotime($value)) : ($value === null ? '' : $value);
        switch ($operator) {
            case '==':
                return $value == $target;
            case '!=':
                return $value != $target;
            case '>=':
                return $target >= $value;
            case '<=':
                return $target <= $value;
            case '>':
                return $target > $value;
            case '<':
                return $target < $value;
            default:
                return false;
        }
    }

    /**
     * Evaluates string conditions.
     *
     * @param string $operator The operator to use for comparison.
     * @param string $value The value to compare against.
     * @param string $target The target value for the comparison.
     * @return bool True if the condition is met, false otherwise.
     */
    private function evaluate_string_condition($operator, $value, $target)
    {
        $value = isset($value['value']) ? $value['value'] : (isset($value['url']) ? $value['url'] : ($value === null ? '' : $value));
        $value = strtolower($value);
        $target = strtolower($target);
        switch ($operator) {
            case '==':
                return $value == $target;
            case '!=':
                return $value != $target;
            case 'contains':
                return strpos($target, $value) !== false;
            case 'notContains':
                return strpos($target, $value) === false;
            default:
                return false;
        }
    }

    /**
     * Evaluates date conditions for user registration status.
     *
     * @param string $operator The operator to use for comparison.
     * @param string $value The value to compare against.
     * @param string $target The target value for the comparison.
     * @return bool True if the condition is met, false otherwise.
     */
    private function evaluate_date_condition($operator, $value, $target)
    {
        $valueDate = strtotime($value);
        $targetDate = strtotime($target);
        switch ($operator) {
            case 'after':
                return $targetDate > $valueDate;
            case 'before':
                return $targetDate < $valueDate;
            default:
                return false;
        }
    }

    /**
     * Evaluates conditions for featured image status.
     *
     * @param string $operator The operator to use for comparison.
     * @param mixed $target The target value for the comparison.
     * @return bool True if the condition is met, false otherwise.
     */
    private function evaluate_featured_image_condition($operator, $value, $target)
    {
        $value = isset($value['value']) ? $value['value'] : ($value === null ? '' : $value);
        switch ($operator) {
            case '==':
                if($value === 'set'){
                    return !empty($target);
                }else{
                    return empty($target);
                }
            case '!=':
                if($value === 'set'){
                    return empty($target);
                }else{
                    return !empty($target);
                }
            default:
                return false;
        }
    }

    /**
     * Evaluates conditions for user login status.
     *
     * @param string $operator The operator to use for comparison.
     * @param mixed $target The target value for the comparison.
     * @param mixed $value The value to compare against.
     * @return bool True if the condition is met, false otherwise.
     */
    private function evaluate_user_login_condition($operator, $value, $target)
    {
        $value = isset($value['value']) ? $value['value'] : ($value === null ? '' : $value);
        switch ($operator) {
            case '==':
                if($value === 'loggedIn'){
                    return is_user_logged_in();
                }else{
                    return !is_user_logged_in();
                }
            case '!=':
                if($value === 'loggedIn'){
                    return !is_user_logged_in();
                }else{
                    return is_user_logged_in();
                }
            default:
                return false;
        }
    }

    /**
     * Get target value based on the condition type.
     *
     * @param string $condition_type The type of condition.
     * 
     * @return mixed The target value for the condition.
     */
    private function get_target_value($condition_type)
    {
        $useragent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        switch ($condition_type) {
            case 'postId':
                return get_the_ID();
            case 'postTitle':
                return get_the_title();
            case 'postParent':
                $post = get_post(get_the_ID());
                return $post->post_parent;
            case 'postStatus':
                return get_post_status();
            case 'postAuthor':
                return get_the_author_meta('ID');
            case 'postDate':
                return get_the_date();
            case 'featuredImage':
                return get_the_post_thumbnail_url();
            case 'userLogin':
                return is_user_logged_in();
            case 'userId':
                return get_current_user_id();
            case 'userRegistered':
                return wp_get_current_user()->user_registered;
            case 'userRole':
                return isset(wp_get_current_user()->roles[0]) ? wp_get_current_user()->roles[0] : null;
            case 'weekday':
                return date("w");
            case 'date':
                return date("F j, Y");
            case 'time':
                $timestamp = current_time('timestamp');
                return date("g:i A", $timestamp);
            case 'dateAndTime':
                return date("F j, Y g:i A");
            case 'browser':
                if($useragent) {
                    $Browser = new BrowserDetection();
                    return !empty($Browser->getBrowser($useragent)['browser_name']) ? $Browser->getBrowser($useragent)['browser_name'] : null;
                }
            case 'operatingSystem':
                if($useragent) {
                    $Browser = new BrowserDetection();
                    return !empty($Browser->getOS($useragent)['os_name']) ? $Browser->getOS($useragent)['os_name'] : null;
                }
            case 'currentUrl':
                return home_url( add_query_arg( null, null ) );
            case 'referrerUrl':
                return wp_get_referer();
            default:
                return null;
        }
    }

    /**
     * Evaluates a single condition and its inner conditions.
     *
     * @param array $condition The condition to evaluate.
     * @return bool True if the condition and its inner conditions are met, false otherwise.
     */
    private function evaluate_condition($condition)
    {
        if (!empty($condition['data'])) {
            $condition_data = $condition['data'];
            $conditionType = isset($condition_data['conditionType']['value']) ? $condition_data['conditionType']['value'] : null;
            $operator = isset($condition_data['operator']['value']) ? $condition_data['operator']['value'] : null;
            $value = isset($condition_data['value']) ? $condition_data['value'] : null;
            $target = $this->get_target_value($conditionType);
            if (!$this->is_met_condition($conditionType, $operator, $value, $target)) {
                return false;
            }
            if (!empty($condition['innerConditions'])) {
                foreach ($condition['innerConditions'] as $innerCondition) {
                    $data = $innerCondition['data'];
                    if(empty($data['conditionType']['value']) || empty($data['operator']['value']) || empty($data['value'])) continue;
                    
                    if (!$this->evaluate_condition($innerCondition)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Handles display conditions for Gutenberg blocks.
     *
     * @param string $block_content The block content.
     * @param array $parsed_block The parsed block data.
     * @param array $instance The block instance.
     * @return string The block content if conditions are met, empty string otherwise.
     */
    public function handle_display_conditions($block_content, $parsed_block, $instance) {
        if (Utils::is_gkit_block($block_content, $parsed_block)) {
            if (!empty($parsed_block['attrs']['displayConditions'])) {
                $conditions = $parsed_block['attrs']['displayConditions'];
                foreach ($conditions as $condition) {
                    $data = $condition['data'];
                    if(empty($data['conditionType']['value']) || empty($data['operator']['value']) || empty($data['value'])) continue;

                    if ($this->evaluate_condition($condition)) {
                        return $block_content;
                    }else{
                        return '';
                    }
                }
            }
        }
        return $block_content;
    }
}
?>
