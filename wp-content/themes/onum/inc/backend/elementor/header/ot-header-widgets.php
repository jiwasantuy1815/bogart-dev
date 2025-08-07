<?php
require_once( get_template_directory() . '/inc/backend/elementor/header/logo.php' );
require_once( get_template_directory() . '/inc/backend/elementor/header/menu.php' );
require_once( get_template_directory() . '/inc/backend/elementor/header/search.php' );
require_once( get_template_directory() . '/inc/backend/elementor/header/side-panel.php' );
require_once( get_template_directory() . '/inc/backend/elementor/header/menu-mobile.php' );
require_once( get_template_directory() . '/inc/backend/elementor/header/list-menu-item.php' );
if ( class_exists( 'woocommerce' ) ) {
    require_once( get_template_directory() . '/inc/backend/elementor/header/cart.php' );
}