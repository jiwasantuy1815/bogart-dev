<?php
namespace GutenkitPro\Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue registrar.
 *
 * @since 1.0.0
 * @access public
 */
class Init {

	use \Gutenkit\Traits\Singleton;

	/**
	 * class constructor.
	 * private for singleton
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		\GutenkitPro\Hooks\PageSettings::instance();
		\GutenkitPro\Hooks\ScrollingEffect::instance();
		\GutenkitPro\Hooks\AdvancedTooltip::instance();
		\GutenkitPro\Hooks\OnePageScroll::instance();
		\GutenkitPro\Hooks\DynamicContent::instance();
		\GutenkitPro\Hooks\Sticky::instance();
		\GutenkitPro\Hooks\MouseTilt::instance();
		\GutenkitPro\Hooks\MouseTrack::instance();
		\GutenkitPro\Hooks\CollectedCss::instance();
		\GutenkitPro\Hooks\DisplayConditions::instance();
		\GutenkitPro\Hooks\SmoothScroll::instance();
		\GutenkitPro\Hooks\Interactions::instance();
		\GutenkitPro\Hooks\VideoScroller::instance();
		\GutenkitPro\Hooks\Particle::instance();
		\GutenkitPro\Hooks\Particle::instance();
		\GutenkitPro\Hooks\ScrollSpy::instance();
	}
}
