<?php
/**
 * ONUM functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package ONUM
 */

if ( ! function_exists( 'onum_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function onum_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on _s, use a find and replace
		 * to change 'onum' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'onum', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary', 'onum' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support( 'post-formats', array(
			'image',
			'video',
			'quote',
			'gallery',
			'audio',
			'link',
		) );

		/* Add image sizes 
		* x_crop_position accepts ‘left’ ‘center’, or ‘right’.
		* y_crop_position accepts ‘top’, ‘center’, or ‘bottom’.
		* 750 pixels wide by 500 pixels tall
		*/

        add_image_size( 'onum-blog-grid-post-thumbnail', 750, 500, array( 'center', 'top' ) );
        add_image_size( 'onum-blog-list-post-thumbnail', 870, 520, array( 'center', 'top' ) );
        add_image_size( 'onum-slide-portfolio-thumbnail', 800, 557, array( 'left', 'top' ) );
        add_image_size( 'onum-slide-portfolio-thumbnail-2', 600, 680, array( 'left', 'top' ) );
        add_image_size( 'onum-portfolio-thumbnail-left-top', 760, 760, array( 'left', 'top' ) ); 

		/*
		 * This theme styles the visual editor to resemble the theme style,
		 * specifically font, colors, and column width.
	 	 */
		add_editor_style( array( 'css/editor-style.css', onum_fonts_url() ) );
		
	}
endif;
add_action( 'after_setup_theme', 'onum_setup' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function onum_widgets_init() {
	/* Register the 'primary' sidebar. */
	register_sidebar( array(
		'name'          => esc_html__( 'Primary Sidebar', 'onum' ),
		'id'            => 'primary',
		'description'   => esc_html__( 'Add widgets here.', 'onum' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h5 class="widget-title">',
		'after_title'   => '</h5>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Topbar Widget', 'onum' ),
		'id'            => 'topbar_widget',
		'description'   => esc_html__( 'Add languages switcher widgets here.', 'onum' ),
		'before_widget' => '<div id="%1$s" class="topbar_widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h5 class="topbar_widget-title">',
		'after_title'   => '</h5>',
	) );
	/* Repeat register_sidebar() code for additional sidebars. */
}
add_action( 'widgets_init', 'onum_widgets_init' );

/**
 * Register custom fonts.
 */
if ( ! function_exists( 'onum_fonts_url' ) ) :
/**
 * Register Google fonts for ONUM.
 *
 * Create your own onum_fonts_url() function to override in a child theme.
 *
 * @since ONUM 1.0
 *
 * @return string Google fonts URL for the theme.
 */
function onum_fonts_url() {
	$fonts_url = '';
	$font_families     = array();
	$subsets   = 'latin';

	$body_typo_value = onum_get_option( 'body_typo', [] );
	$body_typo2_value = onum_get_option( 'body_typo2', [] );
	
	if ( empty( $body_typo_value['font-family'] ) ) {
		$font_families[] = 'Red Hat Text:400,400i,500,500i,700,700i';
	}

	if ( empty( $body_typo2_value['font-family'] ) ) {
		$font_families[] = 'Red Hat Display:400,400i,500,500i,700,700i,900,900i';
	}

	if ( $font_families ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( $subsets ),
		), 'https://fonts.googleapis.com/css' );
	}
	return esc_url_raw( $fonts_url );
}
endif;

/**
 * Enqueue scripts and styles.
 */
function onum_scripts() {

	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'onum-fonts', onum_fonts_url(), array(), null );

	/** All frontend css files **/ 
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.css', array(), '3.3.7', 'all');

	/** load fonts **/
    wp_enqueue_style( 'onum-awesome-font', get_template_directory_uri().'/css/font-awesome.css');
    wp_enqueue_style( 'onum-flaticon-font', get_template_directory_uri().'/css/flaticon.css');

    /** Slick slider **/
    wp_enqueue_style( 'slick-slider', get_template_directory_uri().'/css/slick.css');
    wp_enqueue_style( 'slick-theme', get_template_directory_uri().'/css/slick-theme.css');

    /** Magnific Popup **/
    wp_enqueue_style( 'magnific-popup', get_template_directory_uri().'/css/magnific-popup.css');

    if ( onum_get_option('preload') != false ) {
	    wp_enqueue_style('onum-preload', get_template_directory_uri().'/css/royal-preload.css');    	
	}
	/** Theme stylesheet. **/
	wp_enqueue_style( 'onum-style', get_stylesheet_uri() );	

    wp_enqueue_script( 'slick-slider', get_template_directory_uri() . '/js/slick.min.js', array( 'jquery' ), '20190829', true );
    wp_enqueue_script( 'magnific-popup', get_template_directory_uri() . '/js/jquery.magnific-popup.min.js', array( 'jquery' ), '20190829', true );  
    wp_enqueue_script( 'isotope', get_template_directory_uri().'/js/jquery.isotope.min.js', array('jquery'), '20190829',  true ); 
    wp_enqueue_script( 'imagesloaded' );        
	wp_enqueue_script( 'easypiechart', get_template_directory_uri() . '/js/easypiechart.js', array( 'jquery' ), '20190829', true );
	wp_enqueue_script( 'particles', get_template_directory_uri() . '/js/particles.min.js', array( 'jquery' ), '20190829', true );
	wp_enqueue_script( 'countdown', get_template_directory_uri() . '/js/jquery.countdown.min.js', array( 'jquery' ), '20190829', true );	
	wp_enqueue_script( 'onum-elementor', get_template_directory_uri() . '/js/elementor.js', array( 'jquery' ), '20190829', true );

	if ( onum_get_option('preload') != false ) {
		wp_enqueue_script('onum-royal-preloader', get_template_directory_uri()."/js/royal_preloader.min.js",array('jquery'), '1.0', true);
	}

	wp_enqueue_script( 'onum-scripts', get_template_directory_uri() . '/js/scripts.js', array( 'jquery' ), '20190829', true );
	wp_enqueue_script( 'onum-header-mobile-scripts', get_template_directory_uri() . '/js/header-mobile.js', array('jquery'), '20190829', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'onum_scripts' );



/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/frontend/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/frontend/template-functions.php';

/**
 * Custom Page Header for this theme.
 */
require get_template_directory() . '/inc/frontend/breadcrumbs.php';
require get_template_directory() . '/inc/frontend/page-header.php';

/**
 * Detect plugin. For use on Front End only.
 */
if ( ! function_exists( 'is_plugin_active' ) ) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( did_action( 'elementor/loaded' ) ) {  
	require get_template_directory() . '/inc/backend/elementor/elementor-functions.php';
	require get_template_directory() . '/inc/backend/elementor/column.php';
}
require get_template_directory() . '/inc/frontend/builder.php';

/**
 * Functions which add more to backend.
 */
require get_template_directory() . '/inc/backend/admin-functions.php';

/**
 * Custom metabox for this theme.
 */
require get_template_directory() . '/inc/backend/meta-boxes.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/backend/customizer.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/backend/color.php';

/**
 * Register the required plugins for this theme.
 */
require get_template_directory() . '/inc/backend/plugin-requires.php';

/**
 * Import demo content
 */
require get_template_directory() . '/inc/backend/importer.php';

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'woocommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce/woocommerce.php';
}

/**
 * Preloader js & css
 */
require get_template_directory() . '/inc/frontend/preloader.php';

/**
 * Post Like System.
 */
if ( onum_get_option('single_like_post') != false ){
	require get_template_directory() . '/inc/backend/post-like.php';
}