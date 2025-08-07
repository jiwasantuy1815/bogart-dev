<?php

defined('ABSPATH') || exit;

// Custom skin class to suppress feedback
class GkitQuietSkin extends \WP_Upgrader_Skin {
	public function feedback($string, ...$args) {
		// Suppress feedback
	}
}