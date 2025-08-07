<?php

namespace GutenkitPro\Traits;

/**
 * Trait for authentication 
 * 
 * @package GutenkitPro\Traits
 */
trait Auth{
    public static function validate($request){
                
        self::nonce_check($request);
        self::permission_check($request);
    }
    public static function nonce_check($request){
        
        if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
			wp_send_json_error([
				'status'  => 'fail',
				'message' => ['Nonce mismatch.'],
			]);
		}
    }
    public static function permission_check($request){
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
			wp_send_json_error([
				'status'  => 'fail',
				'message' => ['Access denied.'],
			]);
		}
    }

}