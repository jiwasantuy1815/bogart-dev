<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package ONUM
 */

// Add specific CSS class to header
function onum_header_class() {

	$header_classes = array();
	
	if ( is_page() && function_exists( 'rwmb_meta' ) ) {  

		if ( rwmb_meta( 'select_header' ) == '' && onum_get_option( 'header_version' ) == '' ) {
			if( onum_get_option('header_layout') == "header2" ){
				$header_classes[] = 'header-style-2';
			}else{
				$header_classes[] = 'header-style-1';
			}

			if ( onum_get_option( 'header_width' ) == true  ) {
				$header_classes[] = 'header-fullwidth';
			}	
        } 

	    //Get header transparent.             
        if ( rwmb_meta( 'header_transparent' ) != false ) {
            $header_classes[] = 'header__transparent';
        } else {
        	if ( onum_get_option( 'header_transparent' ) != false ) {
        		$header_classes[] = 'header__transparent';
        	}
        }
    } else {
    	if ( onum_get_option( 'header_version' ) == '' ) {    		
			if( onum_get_option('header_layout') == "header2" ){
				$header_classes[] = 'header-style-2';
			}else{
				$header_classes[] = 'header-style-1';
			}

			if ( onum_get_option( 'header_width' ) == true  ) {
				$header_classes[] = 'header-fullwidth';
			}	
    	}
    	
    	//Get header transparent.
        if ( onum_get_option( 'header_transparent' ) != false ) {
    		$header_classes[] = 'header__transparent';
    	}
    }

    if ( onum_get_option('header_desktop_sticky') != false ){ 
		$header_classes[] = 'cd-header'; 
	}
			
    if ( onum_get_option( 'header_version' ) == '' ) {
    	$header_classes[] = 'header__default';
    }

    // return the $classes array
    echo implode( ' ', $header_classes );
}

if ( ! function_exists( 'onum_header_width_class' ) ) :
	function onum_header_width_class() {

		$header_width_classes = array();

		if ( true == onum_get_option( 'header_width', true ) ) :
			$header_width_classes[] = 'container-fluid';
		else :
			$header_width_classes[] = 'container';
		endif; 

	    // return the $classes array
	    echo implode( ' ', $header_width_classes );
	}
endif;

if ( ! function_exists( 'onum_portfolio_option_class' ) ) :
	function onum_portfolio_option_class() {

		$portfolio_option_class = array();

		if( onum_get_option('portfolio_column') == "2cl" ){
			$portfolio_option_class[] = 'pf_2_cols';
		}elseif( onum_get_option('portfolio_column') == "4cl" ) {
			$portfolio_option_class[] = 'pf_4_cols';
		}elseif( onum_get_option('portfolio_column') == "5cl" ) {
			$portfolio_option_class[] = 'pf_5_cols';
		}else{
			$portfolio_option_class[] = '';
		}

		if( onum_get_option('portfolio_style') == "style2" ) {
			$portfolio_option_class[] = 'projects-style-2 ';
		}

	    // return the $classes array
	    echo implode( ' ', $portfolio_option_class );
	}
endif;

if ( ! function_exists( 'onum_blog_option_class' ) ) :
	function onum_blog_option_class() {

		$blog_option_class = array();		

		if( onum_get_option('blog_style') == "style2" ) {
			if( onum_get_option('blog_column') == "2cl" ){
				$blog_option_class[] = 'pf_2_cols';
			}elseif( onum_get_option('blog_column') == "4cl" ) {
				$blog_option_class[] = 'pf_4_cols';
			}elseif( onum_get_option('blog_column') == "5cl" ) {
				$blog_option_class[] = 'pf_5_cols';
			}else{
				$blog_option_class[] = 'pf_3_cols';
			}
			$blog_option_class[] = 'blog-grid';
		}else{
			$blog_option_class[] = 'blog-list';
		}

	    // return the $classes array
	    echo implode( ' ', $blog_option_class );
	}
endif;

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function onum_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'onum_pingback_header' );

//Get layout post & page.
if ( ! function_exists( 'onum_get_layout' ) ) :
	function onum_get_layout() {
		// Get layout.
		if ( is_page() && !is_home() && function_exists( 'rwmb_meta' ) ) {
			$page_layout = rwmb_meta( 'page_layout' );
		} elseif ( is_single() ) {
			$page_layout = onum_get_option( 'single_post_layout' );
		} else {
			$page_layout = onum_get_option( 'blog_layout' );
		}

		return $page_layout;
	}
endif;

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
if ( ! function_exists( 'onum_content_columns' ) ) :
	function onum_content_columns() {

		$blog_content_width = array();

		// Check if layout is one column.
		if ( 'content-sidebar' === onum_get_layout() && is_active_sidebar( 'primary' ) ) {
			$blog_content_width[] = 'col-lg-9 col-md-9 col-sm-12 col-xs-12';
		}elseif ('sidebar-content' === onum_get_layout() && is_active_sidebar( 'primary' ) ) {
			$blog_content_width[] = 'col-lg-9 col-md-9 col-sm-12 col-xs-12 pull-right';
		}else{
			$blog_content_width[] = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
		}

		// return the $classes array
    	echo implode( ' ', $blog_content_width );
	}
endif;

/**
 * Custom Post Thumbnail
 */
if ( ! function_exists( 'onum_custom_post_thumbnail' ) ) :
	function onum_custom_post_thumbnail() {

		// Check blog style.
		if ( onum_get_option( 'blog_img_size' ) != "none" ) {
			$custom_post_thumbnail = onum_get_option( 'blog_img_size' );
		} else {
			if ( onum_get_option( 'blog_style' ) == "style2" ) {			
				$custom_post_thumbnail = 'onum-blog-grid-post-thumbnail';
			}else{
				$custom_post_thumbnail = 'onum-blog-list-post-thumbnail';
			}
		}		

		// return the $classes array
    	return $custom_post_thumbnail;
	}
endif;

/**
 * Custom Portfolio Thumbnail
 */
if ( ! function_exists( 'onum_custom_portfolio_thumbnail' ) ) :
	function onum_custom_portfolio_thumbnail() {

		// Check blog style.
		if ( onum_get_option( 'portfolio_img_size' ) != "none" ) {
			$custom_post_thumbnail = onum_get_option( 'portfolio_img_size' );
		} else {
			if ( onum_get_option( 'portfolio_style' ) == "style2" ) {			
				$custom_post_thumbnail = 'medium_large';
			}else{
				$custom_post_thumbnail = 'onum-portfolio-thumbnail-left-top';
			}
		}		

		// return the $classes array
    	return $custom_post_thumbnail;
	}
endif;

/**
 * Change Posts Per Page for Portfolio Archive.
 * 
 * @param object $query data
 *
 */
function onum_change_portfolio_posts_per_page( $query ) {
	$portfolio_ppp = (!empty( onum_get_option('portfolio_posts_per_page') ) ? onum_get_option('portfolio_posts_per_page') : '6');

	if ( !is_singular() && !is_admin() ) {		
	    if ( $query->is_post_type_archive( 'ot_portfolio' ) || $query->is_tax('portfolio_cat') && ! is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', $portfolio_ppp );
	    }
	}
    return $query;
}
add_filter( 'pre_get_posts', 'onum_change_portfolio_posts_per_page' );

add_filter('get_the_archive_title', function ($title) {
	if ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    }elseif( is_tax() ) {
        $title = single_cat_title( '', false );
    }
    return $title;
});

// [oceanthemes_date time_custom="F j, Y"]
function oceanthemes_date_func( $atts ) {
    $date_format = shortcode_atts( array(
        'time_custom' => 'Y',        
    ), $atts );

    $dt = new DateTime("now");
    $gmt_timestamp = $dt->format("{$date_format['time_custom']}");

    return $gmt_timestamp;
}
add_shortcode( 'oceanthemes_date', 'oceanthemes_date_func' );

/**
 * Back-To-Top on Footer
 */
if( !function_exists('onum_custom_back_to_top') ) {
    function onum_custom_back_to_top() {     
	    if( onum_get_option('backtotop') != false ){
	    	echo '<a id="back-to-top" href="#" class="show"><i class="flaticon-arrow-pointing-to-up"></i></a>';
	    }
    }
}
add_action('wp_footer', 'onum_custom_back_to_top');

/**
 * Google Analytics
 */
function onum_hook_javascript() {
	if ( onum_get_option('js_code') != '' ) {		
    ?>
        <script type="text/javascript">
            <?php echo onum_get_option('js_code'); ?>
        </script>
    <?php
	}
}
add_action('wp_head', 'onum_hook_javascript');

/* Show Onum version on body tag */
add_filter( 'body_class', 'onum_body_class_names', 999 );
function onum_body_class_names( $classes ) {
	
	if( is_child_theme() ) { 
		$theme = wp_get_theme()->parent(); 
	} else {
		$theme = wp_get_theme();
	}

  	$classes[] = 'onum-theme-ver-'.$theme->version;
  	$classes[] = 'wordpress-version-'.get_bloginfo( 'version' );

  	return $classes;
}