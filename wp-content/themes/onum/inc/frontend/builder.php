<?php
/** header desktop **/
if ( ! function_exists( 'onum_header_builder' ) ) {
    function onum_header_builder (){
        $header_builder = '';    

        if ( is_page() ) {
            if ( function_exists( 'rwmb_meta' ) ) {
                global $wp_query;
                $metabox_hb = rwmb_meta( 'select_header', 'field_type=select_advanced', $wp_query->get_queried_object_id() ); 
                if ( $metabox_hb != '' ) {
                    $header_builder = $metabox_hb;
                }else{
                    $header_builder = onum_get_option('header_version');
                }
            } 
        } else {
            $header_builder = onum_get_option('header_version');
        }

        if( ! $header_builder ) {
            get_template_part('inc/frontend/header-default');
        } else {
            echo '<div class="header__desktop-builder">';
            if ( did_action( 'elementor/loaded' ) ) { 
                if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && is_plugin_active( 'wpml-string-translation/plugin.php' ) ) {
                    $translated_header_builder = apply_filters( 'wpml_object_id', $header_builder );
                    if( !empty( $translated_header_builder ) ){
                        echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $translated_header_builder );
                    }else{
                        echo \Elementor\Plugin::$instance->frontend->get_builder_content( $header_builder );
                    }
                }else{
                    echo \Elementor\Plugin::$instance->frontend->get_builder_content( $header_builder );
                }
            } 
            echo '</div>';
        }
    }
}

/** header mobile **/
if ( ! function_exists( 'onum_mobile_builder' ) ) {
    function onum_mobile_builder (){
        
        if ( is_page() ) {
            if ( function_exists('rwmb_meta') ) {
                global $wp_query;
                $metabox_hmb = rwmb_meta( 'select_header_mobile', 'field_type=select_advanced', $wp_query->get_queried_object_id() ); 
                if ( $metabox_hmb != '' ) {
                    $mobile_builder = $metabox_hmb;
                }else{
                    $mobile_builder = onum_get_option('header_mobile');
                }
            } 
        } else {
            $mobile_builder = onum_get_option('header_mobile');
        }

        if( ! $mobile_builder ) {
            get_template_part('inc/frontend/header-mobile');
        } else {
            echo '<div class="header__mobile-builder">';
            if ( did_action( 'elementor/loaded' ) ) {  
                if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && is_plugin_active( 'wpml-string-translation/plugin.php' ) ) {
                    $translated_mheader_builder = apply_filters( 'wpml_object_id', $mobile_builder );
                    if( !empty( $translated_mheader_builder ) ){
                        echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $translated_mheader_builder );
                    }else{
                        echo \Elementor\Plugin::$instance->frontend->get_builder_content( $mobile_builder );
                    }
                } else {
                    echo \Elementor\Plugin::$instance->frontend->get_builder_content( $mobile_builder ); 
                }
            }
            echo '</div>';
        }
    }
}

/** side panel **/
if ( ! function_exists( 'onum_sidepanel_builder' ) ) {
    function onum_sidepanel_builder (){

        $panel_builder = onum_get_option('sidepanel_layout');

        if( ! $panel_builder ) {
            return;
        } else {
            if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && is_plugin_active( 'wpml-string-translation/plugin.php' ) ) {
                $translated_panel_builder = apply_filters( 'wpml_object_id', $panel_builder );
                if( !empty( $translated_panel_builder ) ){
                    echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $translated_panel_builder );
                }else{
                    echo \Elementor\Plugin::$instance->frontend->get_builder_content( $panel_builder );
                }
            } else {
                echo \Elementor\Plugin::$instance->frontend->get_builder_content( $panel_builder ); 
            }
        }
    }
}

/** 404 template **/
if ( ! function_exists( 'onum_404_builder' ) ) {
    function onum_404_builder () {

        $error_builder = onum_get_option('page_404');

        if( ! $error_builder ) {
        ?>
            <div class="container">
                <div class="error-404 not-found text-center">
                    <h2>404 <img class="error-image" src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/404.png" alt="404"></h2>
                    <h1><?php wp_kses( _e( 'Sorry! Page Not Found!', 'onum' ), wp_kses_allowed_html('post')  ); ?></h1>
                    <div class="content-404">
                        <p><?php wp_kses( _e( 'Oops! The page you are looking for does not exist. Please return to the site is homepage.', 'onum' ), wp_kses_allowed_html('post')  ); ?></p>
                        <?php get_search_form(); ?>
                        <a class="octf-btn octf-btn-third octf-btn-icon" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Take Me Home', 'onum' ); ?><i class="flaticon-right-arrow-1"></i></a>
                    </div>
                </div>
            </div>
        <?php    
        } else {
            if ( did_action( 'elementor/loaded' ) ) {  
                if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && is_plugin_active( 'wpml-string-translation/plugin.php' ) ) {
                    $translated_error_builder = apply_filters( 'wpml_object_id', $error_builder );
                    if( !empty( $translated_error_builder ) ){
                        echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $translated_error_builder );
                    }else{
                        echo \Elementor\Plugin::$instance->frontend->get_builder_content( $error_builder );
                    }
                } else {
                    echo \Elementor\Plugin::$instance->frontend->get_builder_content( $error_builder );
                }
            }
        }
    }
}

if ( ! function_exists( 'onum_footer_builder' ) ) {
    function onum_footer_builder (){
        $footer_builder = '';    

        if ( is_page() ) {
            if ( function_exists('rwmb_meta') ) {
                global $wp_query;
                $metabox_fb = rwmb_meta( 'select_footer', 'field_type=select_advanced', $wp_query->get_queried_object_id() ); 
                if ( $metabox_fb != '' ) {
                    $footer_builder = $metabox_fb;
                }else{
                    $footer_builder = onum_get_option('footer_layout');
                }
            } 
        }else{
            $footer_builder = onum_get_option('footer_layout');
        }

        if( ! $footer_builder ) {
            return;
        }else{
            echo '<footer id="site-footer" class="site-footer" itemscope="itemscope" itemtype="http://schema.org/WPFooter">';
            if ( did_action( 'elementor/loaded' ) ) {
                if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && is_plugin_active( 'wpml-string-translation/plugin.php' ) ) {
                    $translated_footer_builder = apply_filters( 'wpml_object_id', $footer_builder );
                    if( !empty( $translated_footer_builder ) ){
                        echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $translated_footer_builder );
                    }else{
                        echo \Elementor\Plugin::$instance->frontend->get_builder_content( $footer_builder );
                    }
                } else {
                    echo \Elementor\Plugin::$instance->frontend->get_builder_content( $footer_builder );
                }
            }
            echo '</footer>';
        }
    }
}