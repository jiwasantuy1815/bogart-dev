<?php
/**
 * Theme customizer
 *
 * @package ONUM
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ONUM_Customize {
	/**
	 * Customize settings
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * The class constructor
	 *
	 * @param array $config
	 */
	public function __construct( $config ) {
		$this->config = $config;

		if ( ! class_exists( 'Kirki' ) ) {
			return;
		}

		$this->register();
	}

	/**
	 * Register settings
	 */
	public function register() {

		/**
		 * Add the theme configuration
		 */
		if ( ! empty( $this->config['theme'] ) ) {
			Kirki::add_config(
				$this->config['theme'], array(
					'capability'  => 'edit_theme_options',
					'option_type' => 'theme_mod',
				)
			);
		}

		/**
		 * Add panels
		 */
		if ( ! empty( $this->config['panels'] ) ) {
			foreach ( $this->config['panels'] as $panel => $settings ) {
				Kirki::add_panel( $panel, $settings );
			}
		}

		/**
		 * Add sections
		 */
		if ( ! empty( $this->config['sections'] ) ) {
			foreach ( $this->config['sections'] as $section => $settings ) {
				Kirki::add_section( $section, $settings );
			}
		}

		/**
		 * Add fields
		 */
		if ( ! empty( $this->config['theme'] ) && ! empty( $this->config['fields'] ) ) {
			foreach ( $this->config['fields'] as $name => $settings ) {
				if ( ! isset( $settings['settings'] ) ) {
					$settings['settings'] = $name;
				}

				Kirki::add_field( $this->config['theme'], $settings );
			}
		}
	}

	/**
	 * Get config ID
	 *
	 * @return string
	 */
	public function get_theme() {
		return $this->config['theme'];
	}

	/**
	 * Get customize setting value
	 *
	 * @param string $name
	 *
	 * @return bool|string
	 */
	public function get_option( $name ) {

		$default = $this->get_option_default( $name );

		return get_theme_mod( $name, $default );
	}

	/**
	 * Get default option values
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public function get_option_default( $name ) {
		if ( ! isset( $this->config['fields'][ $name ] ) ) {
			return false;
		}

		return isset( $this->config['fields'][ $name ]['default'] ) ? $this->config['fields'][ $name ]['default'] : false;
	}
}

/**
 * This is a short hand function for getting setting value from customizer
 *
 * @param string $name
 *
 * @return bool|string
 */
function onum_get_option( $name ) {
	global $onum_customize;

	$value = false;

	if ( class_exists( 'Kirki' ) ) {
		$value = Kirki::get_option( 'onum', $name );
	} elseif ( ! empty( $onum_customize ) ) {
		$value = $onum_customize->get_option( $name );
	}

	return apply_filters( 'onum_get_option', $value, $name );
}

/**
 * Get default option values
 *
 * @param $name
 *
 * @return mixed
 */
function onum_get_option_default( $name ) {
	global $onum_customize;

	if ( empty( $onum_customize ) ) {
		return false;
	}

	return $onum_customize->get_option_default( $name );
}

/**
 * Move some default sections to `general` panel that registered by theme
 *
 * @param object $wp_customize
 */
function onum_customize_modify( $wp_customize ) {
	$wp_customize->get_section( 'title_tagline' )->panel     = 'general';
	$wp_customize->get_section( 'static_front_page' )->panel = 'general';
}

add_action( 'customize_register', 'onum_customize_modify' );


/**
 * Get customize settings
 *
 * Priority (Order) WordPress Live Customizer default: 
 * @link https://developer.wordpress.org/themes/customize-api/customizer-objects/
 *
 * @return array
 */
function onum_customize_settings() {
	/**
	 * Customizer configuration
	 */

	$settings = array(
		'theme' => 'onum',
	);

	$panels = array(
		'general'     	=> array(
			'priority' 		=> 5,
			'title'    		=> esc_html__( 'General', 'onum' ),
		),
		'header'        => array(
			'title'      	=> esc_html__( 'Header', 'onum' ),
			'priority'   	=> 9,
			'capability' 	=> 'edit_theme_options',
		),
		'blog'        	=> array(
			'title'      	=> esc_html__( 'Blog', 'onum' ),
			'priority'   	=> 10,
			'capability' 	=> 'edit_theme_options',
		),
	);

	$sections = array(
		//Header
		'main_header'           => array(
            'title'       => esc_html__( 'General', 'onum' ),
            'description' => '',
            'priority'    => 15,
            'capability'  => 'edit_theme_options',
            'panel'       => 'header',
        ),		
        'topbar_header'           => array(
			'title'       => esc_html__( 'Top Bar', 'onum' ),
			'description' => '',
			'priority'    => 16,
			'capability'  => 'edit_theme_options',
			'panel'       => 'header',
		),
        'logo_header'           => array(
            'title'       => esc_html__( 'Logos', 'onum' ),
            'description' => '',
            'priority'    => 17,
            'capability'  => 'edit_theme_options',
            'panel'       => 'header',
        ),
        'cta_header'           => array(
            'title'       => esc_html__( 'Call To Action', 'onum' ),
            'description' => '',
            'priority'    => 19,
            'capability'  => 'edit_theme_options',
            'panel'       => 'header',
        ),
	    'header_styling'  => array(
			'title'       => esc_html__( 'Styling', 'onum' ),
			'description' => '',
			'priority'    => 20,
			'capability'  => 'edit_theme_options',
			'panel'       => 'header',
		),		
        'menu_mobile'           => array(
            'title'       => esc_html__( 'Mobile Menu', 'onum' ),
            'description' => '',
            'priority'    => 21,
            'capability'  => 'edit_theme_options',
            'panel'       => 'header',
        ),

		//Page Header
		'page_header'     => array(
            'title'       => esc_html__( 'Page Header', 'onum' ),
            'description' => '',
            'priority'    => 9,
            'capability'  => 'edit_theme_options',
        ),

	    //Blog
		'blog_page'           => array(
			'title'       => esc_html__( 'Blog Page', 'onum' ),
			'description' => '',
			'priority'    => 10,
			'capability'  => 'edit_theme_options',
			'panel'       => 'blog',
		),
        'single_post'           => array(
			'title'       => esc_html__( 'Single Post', 'onum' ),
			'description' => '',
			'priority'    => 10,
			'capability'  => 'edit_theme_options',
			'panel'       => 'blog',
		),

		//Project
		'portfolio_page'           => array(
			'title'       => esc_html__( 'Portfolios', 'onum' ),
			'description' => '',
			'priority'    => 10,
			'capability'  => 'edit_theme_options',			
		),

		//Custom 404
		'error_404'       => array(
            'title'       => esc_html__( '404', 'onum' ),
            'description' => '',
            'priority'    => 11,
            'capability'  => 'edit_theme_options',
        ),
		// Footer
	    'footer'         => array(
			'title'      => esc_html__( 'Footer', 'onum' ),
			'priority'   => 11,
			'capability' => 'edit_theme_options',
		),
		//Typography
		'typography'           => array(
            'title'       => esc_html__( 'Typography', 'onum' ),
            'description' => '',
            'priority'    => 15,
            'capability'  => 'edit_theme_options',
        ),

	    //Color Scheme
		'color_scheme'   => array(
			'title'      => esc_html__( 'Color Scheme', 'onum' ),
			'priority'   => 200,
			'capability' => 'edit_theme_options',
		),

		//GG Analytics
		'script_code'   => array(
			'title'      => esc_html__( 'Google Analytics(Script Code)', 'onum' ),
			'priority'   => 210,
			'capability' => 'edit_theme_options',
		),
	);

	$fields = array(
		/* Main Header */	
		'header_version'   => array(
			'type'        => 'select',  
	 		'label'       => esc_attr__( 'Select Header Desktop', 'onum' ), 
	 		'description' => esc_attr__( 'Choose the header display on desktop.', 'onum' ), 
	 		'section'     => 'main_header', 
	 		'default'     => '', 
	 		'priority'    => 1,
	 		'placeholder' => esc_attr__( 'Select a header', 'onum' ), 
	 		'choices'     => ( class_exists( 'Kirki_Helper' ) ) ? Kirki_Helper::get_posts( array( 'post_type' => 'ot_header_builders', 'posts_per_page' => -1 ) ) : array(),
		),		
		'header_mobile'   => array(
			'type'        => 'select',  
	 		'label'       => esc_attr__( 'Select Header Mobile', 'onum' ), 
	 		'description' => esc_attr__( 'Choose the header display on mobile.', 'onum' ), 
	 		'section'     => 'main_header', 
	 		'default'     => '', 
	 		'priority'    => 2,
	 		'placeholder' => esc_attr__( 'Select a header', 'onum' ), 
	 		'choices'     => ( class_exists( 'Kirki_Helper' ) ) ? Kirki_Helper::get_posts( array( 'post_type' => 'ot_header_builders', 'posts_per_page' => -1 ) ) : array(),
        ),
        'is_sidepanel'    => array(
            'type'        => 'toggle',
			'label'       => esc_html__( 'Side Panel for all site?', 'onum' ),
			'section'     => 'main_header',
			'default'     => '1',
			'priority'    => 3,
        ), 
        'sidepanel_layout'     => array(
			'type'        => 'select',  
	 		'label'       => esc_attr__( 'Select Side Panel', 'onum' ), 
	 		'description' => esc_attr__( 'Choose the side panel on header.', 'onum' ), 
	 		'section'     => 'main_header', 
	 		'default'     => '', 
	 		'priority'    => 3,
	 		'placeholder' => esc_attr__( 'Select a panel', 'onum' ), 
	 		'choices'     => ( class_exists( 'Kirki_Helper' ) ) ? Kirki_Helper::get_posts( array( 'post_type' => 'ot_header_builders', 'posts_per_page' => -1 ) ) : array(),
	 		'active_callback' => array(
				array(
					'setting'  => 'is_sidepanel',
					'operator' => '!=',
					'value'    => '',
				),
			),
		),		
		'sidepanel_position'    => array(
            'type'        => 'toggle',
			'label'       => esc_html__( 'Side Panel On Left?', 'onum' ),
			'section'     => 'main_header',
			'default'     => '0',
			'priority'    => 4,
			'active_callback' => array(
				array(
					'setting'  => 'is_sidepanel',
					'operator' => '!=',
					'value'    => '',
				),
				array(
					'setting'  => 'sidepanel_layout',
					'operator' => '!=',
					'value'    => '',
				),
			),
        ),                 	
		'header_transparent'    => array(
            'type'        => 'toggle',
			'label'       => esc_html__( 'Header Transparent for all site?', 'onum' ),
			'section'     => 'main_header',
			'default'     => '0',
			'priority'    => 5,
        ), 
        'header_width'    => array(
            'type'        => 'toggle',
			'label'       => esc_html__( 'Header Width: Wide/Boxes', 'onum' ),
			'section'     => 'main_header',
			'default'     => '1',
			'priority'    => 6,
			'active_callback' => array(
				array(
					'setting'  => 'header_version',
					'operator' => '==',
					'value'    => '',
				),
			),
        ),    
        'header_spacing' => array(
            'type'     => 'dimensions',
            'label'    => esc_html__( 'Header Padding (ex: 10px)', 'onum' ),
            'section'  => 'main_header',
            'priority' => 7,
            'default'  => array(
                'padding-left'   => '',
				'padding-right'  => '',
            ),
            'choices'     => array(
				'labels' => array(
					'padding-left' => esc_html__( 'Padding Left', 'onum' ),
					'padding-right' => esc_html__( 'Padding Right', 'onum' ),
				),
			),           
			'active_callback' => array(
				array(
					'setting'  => 'header_width',
					'operator' => '==',
					'value'    => '1',
				),
				array(
					'setting'  => 'header_version',
					'operator' => '==',
					'value'    => '',
				),
			), 
			'output'    => array(
                array(
                    'element'  => '.header-fullwidth .octf-area-wrap'
                ),
            ),
        ),
        'header_desktop_sticky' => array(
            'type'     			=> 'toggle',
            'label'    			=> esc_html__( 'Sticky Header', 'onum' ),
            'section'  			=> 'main_header',
            'default'  			=> '1',
            'priority' 			=> 8,
        ),  

        /* Header TopBar */
		'topbar_switch'   => array(
			'type'        => 'toggle',
			'label'       => esc_attr__( 'Top Bar On/Off', 'onum' ),
			'section'     => 'topbar_header',
			'default'     => 1,
			'priority'    => 1,
            'active_callback'  => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
		),		

		// Topbar Menu
		'topbar_menu'     => array(
			'type'        => 'select',  
	 		'label'       => esc_attr__( 'Select Menu', 'onum' ), 
	 		'description' => esc_attr__( 'Choose a menu to show on topbar here.', 'onum' ), 
	 		'section'     => 'topbar_header', 
	 		'default'     => '', 
	 		'priority'    => 1,
            'active_callback'  => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
	 		'placeholder' => esc_attr__( 'Select a menu', 'onum' ),  
	 		'choices'     => ( class_exists( 'Kirki_Helper' ) ) ? Kirki_Helper::get_terms( 'nav_menu', array( 'hide_empty' => true ) ) : array(),
		),

		// Header Contact Info
		'info_separator'     => array(
			'type'        => 'custom',
			'label'       => '',
			'section'     => 'topbar_header',
			'default'     => '<hr>',
			'priority'    => 2,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
		),
		'info_switch'     => array(
			'type'        => 'toggle',
			'label'       => esc_attr__( 'Contact Info On/Off', 'onum' ),
			'section'     => 'topbar_header',
			'default'     => 1,
			'priority'    => 3,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
		),
		'header_contact_info'     => array(
			'type'     => 'repeater',
			'label'    => esc_html__( 'Contact Info', 'onum' ),
			'section'  => 'topbar_header',
			'priority' => 4,
			'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),            
				array(
					'setting'  => 'info_switch',
					'operator' => '==',
					'value'    => 1,
				),
			),
			'row_label' => array(
				'type' => 'field',
				'value' => esc_attr__('Contact Info', 'onum' ),
				'field' => 'info_name',
			),
			'default'  => array(),
			'fields'   => array(
				'info_name' => array(
					'type'        => 'text',
					'label'       => esc_html__( 'Contact info name', 'onum' ),
					'description' => esc_html__( 'This will be the contact info name', 'onum' ),
					'default'     => '',
				),
				'info_icon' => array(
					'type'        => 'text',
					'label'       => esc_html__( 'Icon class name', 'onum' ),
					'description' => esc_html__( 'This will be the contact info icon: https://fontawesome.com/icons?d=gallery , ex: fas fa-phone', 'onum' ),
					'default'     => '',
				),
				'info_content' => array(
					'type'        => 'textarea',
					'label'       => esc_html__( 'Contact info content', 'onum' ),
					'description' => esc_html__( 'This will be the contact info content', 'onum' ),
					'default'     => '',
				),				
			),
		),

		// Header Social
		'social_separator'     => array(
			'type'        => 'custom',
			'label'       => '',
			'section'     => 'topbar_header',
			'default'     => '<hr>',
			'priority'    => 5,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
		),
		'social_switch'     => array(
			'type'        => 'toggle',
			'label'       => esc_attr__( 'Social Network On/Off', 'onum' ),
			'section'     => 'topbar_header',
			'default'     => 1,
			'priority'    => 6,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
		),
		'header_socials'     => array(
			'type'     => 'repeater',
			'label'    => esc_html__( 'Socials Network', 'onum' ),
			'section'  => 'topbar_header',
			'priority' => 7,
			'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
				array(
					'setting'  => 'social_switch',
					'operator' => '==',
					'value'    => 1,
				),
			),
			'row_label' => array(
				'type' => 'field',
				'value' => esc_attr__('social', 'onum' ),
				'field' => 'social_name',
			),
			'default'  => array(),
			'fields'   => array(
				'social_name' => array(
					'type'        => 'text',
					'label'       => esc_html__( 'Social network name', 'onum' ),
					'description' => esc_html__( 'This will be the social network name', 'onum' ),
					'default'     => '',
				),
				'social_icon' => array(
					'type'        => 'text',
					'label'       => esc_html__( 'Icon class name', 'onum' ),
					'description' => esc_html__( 'This will be the social icon: https://fontawesome.com/icons?d=gallery , ex: fab fa-facebook', 'onum' ),
					'default'     => '',
				),
				'social_link' => array(
					'type'        => 'text',
					'label'       => esc_html__( 'Link url', 'onum' ),
					'description' => esc_html__( 'This will be the social link', 'onum' ),
					'default'     => '',
				),
			),
		),
		'social_target_link'    => array(
			'type'        => 'select',
			'label'       => esc_attr__( 'HTML a target Attribute for Socials.', 'onum' ),
			'section'     => 'topbar_header',
			'default'     => '_self',
			'priority'    => 8,
			'multiple'    => 1,
			'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
				array(
					'setting'  => 'social_switch',
					'operator' => '==',
					'value'    => 1,
				),
			),
			'choices'     => array(
				'_self' => esc_attr__( 'Same Frame', 'onum' ),
				'_blank' => esc_attr__( 'New Window', 'onum' ),
			),
		),
		
		 /* Call To Action Header */        
        'header_layout'    => array(
            'type'        => 'select',
            'label'       => esc_attr__( 'Select CTA Style', 'onum' ),
            'section'     => 'cta_header',
            'default'     => 'header1',
            'priority'    => 1,
            'multiple'    => 1,
            'choices'     => array(
                'header1' => esc_html__( 'Style 1', 'onum' ),
                'header2' => esc_html__( 'Style 2', 'onum' ),
            ),
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
				array(
					'setting'  => 'header_homepage',
					'operator' => '==',
					'value'    => '1',
				),
			), 
        ),       
        'search_switch'     => array(
            'type'        => 'toggle',
            'label'       => esc_attr__( 'Search Button On/Off', 'onum' ),
            'section'     => 'cta_header',
            'default'     => 0,
            'priority'    => 1,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
        ),      
        'cart_switch'     => array(
            'type'        => 'toggle',
            'label'       => esc_attr__( 'Cart Button On/Off', 'onum' ),
            'section'     => 'cta_header',
            'default'     => 0,
            'priority'    => 2,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
        ),          
        'separator_ctahead'     => array(
            'type'        => 'custom',
            'label'       => '',
            'section'     => 'cta_header',
            'default'     => '<hr>',
            'priority'    => 3,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
        ),  
        'header_cta_switch'     => array(
            'type'        => 'toggle',
            'label'       => esc_attr__( 'Call To Action Button On/Off', 'onum' ),
            'section'     => 'cta_header',
            'default'     => 0,
            'priority'    => 4,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
        ),  
        'cta_text_header'    => array(
            'type'     => 'text',
            'label'    => esc_html__( 'CTA Button Text', 'onum' ),
            'section'  => 'cta_header',
            'default'  => '',
            'priority' => 5,            
            'active_callback' => array(
				array(
					'setting'  => 'header_cta_switch',
					'operator' => '==',
					'value'    => 1,
				),
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
			),
        ),
        'cta_link_header'    => array(
            'type'     => 'link',
            'label'    => esc_html__( 'CTA Button Link', 'onum' ),
            'section'  => 'cta_header',
            'default'  => '',
            'priority' => 6,            
            'active_callback' => array(
				array(
					'setting'  => 'header_cta_switch',
					'operator' => '==',
					'value'    => 1,
				),
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
			),
        ),
        'cta_bgcolor_header'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'CTA Button Background Color', 'onum' ),
            'section'  => 'cta_header',
            'default'  => '',
            'priority' => 7,
            'output'    => array(
                array(
                    'element'  => '#site-header .btn-cta-header a, #site-header .btn-cta-header a:visited, #site-header .btn-cta-header a:focus, #site-header .btn-cta-header a:hover',
                    'property' => 'background'
                ),
            ),
            'active_callback' => array(
				array(
					'setting'  => 'header_cta_switch',
					'operator' => '==',
					'value'    => 1,
				),
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
			),
        ),
        'cta_textcolor_header'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'CTA Button Text Color', 'onum' ),
            'section'  => 'cta_header',
            'default'  => '',
            'priority' => 8,
            'output'    => array(
                array(
                    'element'  => '#site-header .btn-cta-header a, #site-header .btn-cta-header a:visited, #site-header .btn-cta-header a:focus, #site-header .btn-cta-header a:hover',
                    'property' => 'color'
                ),
            ),
            'active_callback' => array(
				array(
					'setting'  => 'header_cta_switch',
					'operator' => '==',
					'value'    => 1,
				),
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
			),
        ),
        'cta_box_shadow_header'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'CTA Button box-shadow color', 'onum' ),
            'section'  => 'cta_header',
            'default'  => '',
            'priority' => 9,
            'active_callback' => array(
				array(
					'setting'  => 'header_cta_switch',
					'operator' => '==',
					'value'    => 1,
				),
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
			),
        ),     

		/* Header Logos Setting */
		'logo'         => array(
			'type'     => 'image',
			'label'    => esc_attr__( 'Upload Your Static Logo Image on Header Static (.jpg, .png, .svg)', 'onum' ),
			'section'  => 'logo_header',
			'default'  => trailingslashit( get_template_directory_uri() ) . 'images/logo.svg',
			'priority' => 2,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
		),
		'logo_scroll'  => array(
			'type'     => 'image',
			'label'    => esc_attr__( 'Upload Your Logo Image on Header Scroll (.jpg, .png, .svg)', 'onum' ),
			'section'  => 'logo_header',
			'default'  => trailingslashit( get_template_directory_uri() ) . 'images/logo-dark.svg',
			'priority' => 3,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
		),
        'logo_width'   => array(
            'type'     => 'number',
            'label'    => esc_html__( 'Logo Width(px)', 'onum' ),
            'section'  => 'logo_header',
            'priority' => 4,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'default'  => '',
            'output'    => array(
                array(
                    'element'  => '#site-logo img',
                    'property' => 'width',
                    'units'	   => 'px'
                ),
            ),
        ),        
        'logo_spacing' => array(
            'type'     => 'dimensions',
            'label'    => esc_html__( 'Logo Margin (ex: 10px)', 'onum' ),
            'section'  => 'logo_header',
            'priority' => 6,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'default'  => array(
                'top'    => '25px',
                'bottom' => '25px',
                'left'   => '0',
                'right'  => '0',
            ),
            'output'    => array(
                array(
                    'element'  => '#site-logo',
                    'property' => 'padding',
                    'units'	   => 'px'
                ),
            ),
        ),

		//Header Styling  
        'bg_topbar'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Top Bar Background Color', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 1,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '.header-topbar',
                    'property' => 'background'
                ),
            ),
        ),        
        'color_topbar'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Top Bar Text Color', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 2,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '.header-topbar, .header-topbar a',
                    'property' => 'color'
                ),
            ),
        ),
        'border_topbar'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Top Bar Border Bottom Color', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 3,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '.header-topbar',
                    'property' => 'border-color'
                ),
            ),
        ),
        'separator_1'     => array(
            'type'        => 'custom',
            'label'       => '',
            'section'     => 'header_styling',
            'default'     => '<hr>',
            'priority'    => 4,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
        ),
        'bg_menu'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Main Navigation Background Color Static', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 5,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '#site-header .octf-main-header',
                    'property' => 'background'
                ),
            ),
        ),
        'color_menu'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Menu Item Color Static', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 6,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '#site-header #site-navigation > ul > li > a',
                    'property' => 'color'
                ),
            ),
        ),
        'bdcolor_menu'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Border Color Static', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 6,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '#site-header, #site-header.header-style-1',
                    'property' => 'border-bottom-color'
                ),
            ),
        ),
        'separator_2'     => array(
            'type'        => 'custom',
            'label'       => '',
            'section'     => 'header_styling',
            'default'     => '<hr>',
            'priority'    => 7,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
        ),
        'bg_menu_scroll'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Main Navigation Background Color Scroll', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 8,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '.site-header.is-fixed .octf-main-header',
                    'property' => 'background'
                ),
            ),
        ),
        'color_menu_scroll'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Menu Item Color Scroll', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 9,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '.site-header.is-fixed .octf-main-header #site-navigation > ul > li > a',
                    'property' => 'color'
                ),
            ),
        ),
		'bdcolor_menu_scroll'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Border Color Scroll', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 9,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '.site-header.is-fixed .octf-main-header',
                    'property' => 'border-bottom-color'
                ),
            ),
        ),
        'separator_3'     => array(
            'type'        => 'custom',
            'label'       => '',
            'section'     => 'header_styling',
            'default'     => '<hr>',
            'priority'    => 10,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
        ),
        'bg_smenu'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Background Color for Dropdown Menu', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 11,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '#site-navigation ul ul',
                    'property' => 'background'
                ),                
            ),
        ),        
        'color_smenu'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Text Color for Dropdown Menu Item', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 12,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '#site-navigation ul li li a',
                    'property' => 'color'
                ),
            ),
        ),
        'color_hover_smenu'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Text Hover Color for Dropdown Menu Item', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 12,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '#site-navigation ul li li a:hover, #site-navigation ul ul li.current-menu-item > a',
                    'property' => 'color'
                ),
                array(
                    'element'  => '#site-navigation ul li li a:before',
                    'property' => 'background'
                ),
            ),
        ),
        'arrow_smenu'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Arrow Color for Dropdown Menu', 'onum' ),
            'section'  => 'header_styling',
            'default'  => '',
            'priority' => 13,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'    => array(
                array(
                    'element'  => '#site-navigation > ul > li.menu-item-has-children > a:after, #site-navigation ul > li li.menu-item-has-children > a:after',
                    'property' => 'color'
                ),
            ),
        ),
        'menu_typo'=> array(
            'type'     => 'typography',
            'label'    => esc_html__( 'Menu Parent Font', 'onum' ),
            'section'  => 'header_styling',
            'priority' => 14,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'default'  => array(
                'font-family'    => '',
                'variant'        => '',                
                'font-size'      => '',                            
                'line-height'    => '',
                'letter-spacing' => '',
                'text-transform' => '',
                'subsets'        => array( 'latin', 'latin-ext' ),
            ),
            'output'      => array(
                array(
                    'element' => '.main-navigation ul > li > a',
                ),
            ),
        ),
        'submenu_typo' => array(
            'type'        => 'typography',
            'label'       => esc_attr__( 'Menu Dropdown Font', 'onum' ),
            'section'     => 'header_styling',
            'default'     => array(
                'font-family'    => '',
                'variant'        => '',
                'font-size'      => '',
                'line-height'    => '',
                'letter-spacing' => '',                
                'text-transform' => '',
                'subsets'        => array( 'latin', 'latin-ext' ),
            ),
            'priority'    => 15,
            'active_callback' => array(
                array(
                    'setting'  => 'header_version',
                    'operator' => '==',
                    'value'    => '',
                ),
            ),
            'output'      => array(
                array(
                    'element' => '.main-navigation ul li li a',
                ),
            ),
        ), 

        /*** Mobile Menu ***/	          
        'logo_mobile'         => array(
			'type'     => 'image',
			'label'    => esc_attr__( 'Mobile Logo', 'onum' ),
			'section'  => 'menu_mobile',
			'default'  => trailingslashit( get_template_directory_uri() ) . 'images/logo-dark.svg',
			'priority' => 4,
			'active_callback' => array(
				array(
					'setting'  => 'header_mobile',
					'operator' => '==',
					'value'    => '',
				),
			),
		),		
        'mlogo_width' => array(
            'type'     => 'number',
            'label'    => esc_html__( 'Logo Width(px)', 'onum' ),
            'section'  => 'menu_mobile',
            'priority' => 5,
            'default'  => '',
            'output'    => array(
                array(
                    'element'  => '.header_mobile .mlogo_wrapper img',
                    'property' => 'width',
                    'units'	   => 'px'
                ),
            ),
            'active_callback' => array(
				array(
					'setting'  => 'header_mobile',
					'operator' => '==',
					'value'    => '',
				),
			),
        ),
        'bg_mmenu'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Background Color', 'onum' ),
            'section'  => 'menu_mobile',
            'default'  => '',
            'priority' => 6,
            'output'    => array(
                array(
                    'element'  => '.site-header .header_mobile, .header_mobile .mmenu_wrapper',
                    'property' => 'background'
                ),                
            ),
            'active_callback' => array(
				array(
					'setting'  => 'header_mobile',
					'operator' => '==',
					'value'    => '',
				),
			),
        ), 
        'bg_active_mmenu'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Background Active Color', 'onum' ),
            'section'  => 'menu_mobile',
            'default'  => '',
            'priority' => 7,
            'output'    => array(
                array(
                    'element'  => '.site-header .header_mobile.open, .site-header.is-fixed .header_mobile',
                    'property' => 'background'
                ),                
            ),
            'active_callback' => array(
				array(
					'setting'  => 'header_mobile',
					'operator' => '==',
					'value'    => '',
				),
			),
        ), 
        'color_mmenu'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Text Color', 'onum' ),
            'section'  => 'menu_mobile',
            'default'  => '',
            'priority' => 8,
            'output'    => array(
                array(
                    'element'  => '.header_mobile .mobile_nav .mobile_mainmenu li a',
                    'property' => 'color'
                ),                
            ),
            'active_callback' => array(
				array(
					'setting'  => 'header_mobile',
					'operator' => '==',
					'value'    => '',
				),
			),
        ),
        'color_hover_mmenu'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Text Hover Color', 'onum' ),
            'section'  => 'menu_mobile',
            'default'  => '',
            'priority' => 9,
            'output'    => array(
                array(
                    'element'  => '.header_mobile .mobile_nav .mobile_mainmenu li a:hover, .header_mobile .mobile_nav .mobile_mainmenu > li.current-menu-item > a',
                    'property' => 'color'
                ),                
            ),
            'active_callback' => array(
				array(
					'setting'  => 'header_mobile',
					'operator' => '==',
					'value'    => '',
				),
			),
        ),
        'border_mmenu'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Border Color', 'onum' ),
            'section'  => 'menu_mobile',
            'default'  => '',
            'priority' => 10,
            'output'    => array(
                array(
                    'element'  => '.header_mobile .mobile_nav .mobile_mainmenu li a',
                    'property' => 'border-color'
                ),                
            ),
            'active_callback' => array(
				array(
					'setting'  => 'header_mobile',
					'operator' => '==',
					'value'    => '',
				),
			),
        ),  
        'color_toggle'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Toggle Button Color', 'onum' ),
            'section'  => 'menu_mobile',
            'default'  => '',
            'priority' => 11,
            'output'    => array(                
                array(
                    'element'  => '.header_mobile .mobile_nav .mobile_mainmenu > li.menu-item-has-children .arrow i',
                    'property' => 'color'
                ),                
            ),
            'active_callback' => array(
				array(
					'setting'  => 'header_mobile',
					'operator' => '==',
					'value'    => '',
				),
			),
        ),

        //Page Header
        'pheader_switch'  => array(
            'type'        => 'toggle',
            'label'       => esc_html__( 'Page Header On/Off', 'onum' ),
            'section'     => 'page_header',
            'default'     => 1,
            'priority'    => 10,
        ),
        'breadcrumbs'     => array(
            'type'        => 'toggle',
            'label'       => esc_html__( 'Breadcrumbs On/Off', 'onum' ),
            'section'     => 'page_header',
            'default'     => 1,
            'priority'    => 10,
            'active_callback' => array(
                array(
                    'setting'  => 'pheader_switch',
                    'operator' => '==',
                    'value'    => 1,
                ),
            ),
        ),
        'pheader_align'    => array(
            'type'     => 'radio',
            'label'    => esc_html__( 'Text Align', 'onum' ),
            'section'  => 'page_header',
            'default'  => 'text-center',
            'priority' => 10,
            'choices'     => array(
                'text-center'   => esc_html__( 'Center', 'onum' ),
                'text-left'     => esc_html__( 'Left', 'onum' ),
                'text-right'    => esc_html__( 'Right', 'onum' ),
            ),
            'active_callback' => array(
                array(
                    'setting'  => 'pheader_switch',
                    'operator' => '==',
                    'value'    => 1,
                ),
            ),
        ),
        'pheader_img'  => array(
            'type'     => 'image',
            'label'    => esc_html__( 'Background Image', 'onum' ),
            'section'  => 'page_header',
            'default'  => get_template_directory_uri() . '/images/bg-page-header.jpg',
            'priority' => 10,
            'output'    => array(
                array(
                    'element'  => '.page-header',
                    'property' => 'background-image'
                ),
            ),
            'active_callback' => array(
                array(
                    'setting'  => 'pheader_switch',
                    'operator' => '==',
                    'value'    => 1,
                ),
            ),
        ),
        'pheader_color'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Background Color', 'onum' ),
            'section'  => 'page_header',
            'default'  => '',
            'priority' => 10,
            'output'    => array(
                array(
                    'element'  => '.page-header',
                    'property' => 'background-color'
                ),
            ),
            'active_callback' => array(
                array(
                    'setting'  => 'pheader_switch',
                    'operator' => '==',
                    'value'    => 1,
                ),
            ),
        ),
        'ptitle_color'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Page Title Color', 'onum' ),
            'section'  => 'page_header',
            'default'  => '',
            'priority' => 10,
            'output'    => array(
                array(
                    'element'  => '.page-title, .page-header, .page-header .breadcrumbs li a, .page-header .breadcrumbs li:before',
                    'property' => 'color'
                ),
            ),
            'active_callback' => array(
                array(
                    'setting'  => 'pheader_switch',
                    'operator' => '==',
                    'value'    => 1,
                ),
            ),
        ),
        'pheader_ptop'  => array(
        	'type'        => 'dimensions',
			'label'       => esc_html__( 'Padding Top (Ex: 100px)', 'onum' ),
			'section'     => 'page_header',	
			'transport' => 'auto',		          
            'priority' => 10,
            'choices'   => array(
                'desktop' => esc_attr__( 'Desktop', 'onum' ),
                'tablet'  => esc_attr__( 'Tablet', 'onum' ),
                'mobile'  => esc_attr__( 'Mobile', 'onum' ),
            ),
            'output'   => array(
                array(
                    'choice'      => 'mobile',
                    'element'     => '.page-header',
                    'property'    => 'padding-top',
                    'media_query' => '@media (max-width: 767px)',
                ),
                array(
                    'choice'      => 'tablet',
                    'element'     => '.page-header',
                    'property'    => 'padding-top',
                    'media_query' => '@media (min-width: 768px) and (max-width: 1024px)',
                ),
                array(
                    'choice'      => 'desktop',
                    'element'     => '.page-header',
                    'property'    => 'padding-top',
                    'media_query' => '@media (min-width: 1024px)',
                ),
            ),
            'default' => array(
                'desktop' => '',
                'tablet'  => '',
                'mobile'  => '',
            ),
            'active_callback' => array(
                array(
                    'setting'  => 'pheader_switch',
                    'operator' => '==',
                    'value'    => 1,
                ),
            ),
        ),
        'pheader_pbottom'  => array(
        	'type'        => 'dimensions',
			'label'       => esc_html__( 'Padding Bottom (Ex: 100px)', 'onum' ),
			'section'     => 'page_header',	
			'transport' => 'auto',		          
            'priority' => 10,
            'choices'   => array(
                'desktop' => esc_attr__( 'Desktop', 'onum' ),
                'tablet'  => esc_attr__( 'Tablet', 'onum' ),
                'mobile'  => esc_attr__( 'Mobile', 'onum' ),
            ),
            'output'   => array(
                array(
                    'choice'      => 'mobile',
                    'element'     => '.page-header',
                    'property'    => 'padding-bottom',
                    'media_query' => '@media (max-width: 767px)',
                ),
                array(
                    'choice'      => 'tablet',
                    'element'     => '.page-header',
                    'property'    => 'padding-bottom',
                    'media_query' => '@media (min-width: 768px) and (max-width: 1024px)',
                ),
                array(
                    'choice'      => 'desktop',
                    'element'     => '.page-header',
                    'property'    => 'padding-bottom',
                    'media_query' => '@media (min-width: 1024px)',
                ),
            ),
            'default' => array(
                'desktop' => '',
                'tablet'  => '',
                'mobile'  => '',
            ),
            'active_callback' => array(
                array(
                    'setting'  => 'pheader_switch',
                    'operator' => '==',
                    'value'    => 1,
                ),
            ),
        ),
        'head_size'  => array(
            'type'     => 'dimensions',
            'label'    => esc_html__( 'Page Title Size (Ex: 20px)', 'onum' ),
            'section'  => 'page_header',
            'transport' => 'auto',
            'priority' => 10,
            'choices'   => array(
                'desktop' => esc_attr__( 'Desktop', 'onum' ),
                'tablet'  => esc_attr__( 'Tablet', 'onum' ),
                'mobile'  => esc_attr__( 'Mobile', 'onum' ),
            ),
            'output'   => array(
                array(
                    'choice'      => 'mobile',
                    'element'     => '.page-header .page-title',
                    'property'    => 'font-size',
                    'media_query' => '@media (max-width: 767px)',
                ),
                array(
                    'choice'      => 'tablet',
                    'element'     => '.page-header .page-title',
                    'property'    => 'font-size',
                    'media_query' => '@media (min-width: 768px) and (max-width: 1024px)',
                ),
                array(
                    'choice'      => 'desktop',
                    'element'     => '.page-header .page-title',
                    'property'    => 'font-size',
                    'media_query' => '@media (min-width: 1024px)',
                ),
            ),
            'default' => array(
                'desktop' => '',
                'tablet'  => '',
                'mobile'  => '',
            ),
            'active_callback' => array(
                array(
                    'setting'  => 'pheader_switch',
                    'operator' => '==',
                    'value'    => 1,
                ),
            ),
		),		        

        // Footer
		'footer_layout'     => array(
			'type'        => 'select',  
	 		'label'       => esc_attr__( 'Select Footer', 'onum' ), 
	 		'description' => esc_attr__( 'Choose a footer for all site here.', 'onum' ), 
	 		'section'     => 'footer', 
	 		'default'     => '', 
	 		'priority'    => 1,
	 		'placeholder' => esc_attr__( 'Select a footer', 'onum' ), 
	 		'choices'     => ( class_exists( 'Kirki_Helper' ) ) ? Kirki_Helper::get_posts( array( 'post_type' => 'ot_footer_builders', 'posts_per_page' => -1 ) ) : array(),
		),
		'backtotop_separator'     => array(
			'type'        => 'custom',
			'label'       => '',
			'section'     => 'footer',
			'default'     => '<hr>',
			'priority'    => 2,
		),
		'backtotop'  => array(
            'type'        => 'toggle',
            'label'       => esc_html__( 'Back To Top On/Off?', 'onum' ),
            'section'     => 'footer',
            'default'     => 1,
            'priority'    => 3,
        ),
        'bg_backtotop'    => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Back-To-Top Background Color', 'onum' ),
            'section'  => 'footer',
            'priority' => 4,
            'default'     => '',
            'output'    => array(
                array(
                    'element'  => '#back-to-top',
                    'property' => 'background',
                ),
            ),
            'active_callback' => array(
				array(
					'setting'  => 'backtotop',
					'operator' => '==',
					'value'    => 1,
				),
			),
        ),
        'color_backtotop' => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Back-To-Top Color', 'onum' ),
            'section'  => 'footer',
            'priority' => 5,
            'default'     => '',
            'output'    => array(
                array(
                    'element'  => '#back-to-top > i:before',
                    'property' => 'color',
                )
            ),
            'active_callback' => array(
				array(
					'setting'  => 'backtotop',
					'operator' => '==',
					'value'    => 1,
				),
			),
        ),
        'spacing_backtotop' => array(
            'type'     => 'dimensions',
            'label'    => esc_html__( 'Back-To-Top Spacing', 'onum' ),
            'section'  => 'footer',
            'priority' => 6,
            'default'     => array(
				'bottom'  => '',
				'left' => '',
				'right' => '',
			),
			'choices'     => array(
				'labels' => array(
					'bottom'  => esc_html__( 'Bottom', 'onum' ),
					'left' => esc_html__( 'Left', 'onum' ),
					'right' => esc_html__( 'Right', 'onum' ),
				),
			),
            'output'    => array(
                array(
                    'element'  => '#back-to-top.show',
                )
            ),
            'active_callback' => array(
				array(
					'setting'  => 'backtotop',
					'operator' => '==',
					'value'    => 1,
				),
			),
        ),

        // Blog Page
		'blog_layout'           => array(
			'type'        => 'radio-image',
			'label'       => esc_html__( 'Blog Layout', 'onum' ),
			'section'     => 'blog_page',
			'default'     => 'content-sidebar',
			'priority'    => 7,
			'description' => esc_html__( 'Select default sidebar for the blog page.', 'onum' ),
			'choices'     => array(
				'content-sidebar' 	=> get_template_directory_uri() . '/inc/backend/images/right.png',
				'sidebar-content' 	=> get_template_directory_uri() . '/inc/backend/images/left.png',
				'full-content' 		=> get_template_directory_uri() . '/inc/backend/images/full.png',
			)
		),
		'blog_style'           => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Blog Style', 'onum' ),
			'section'     => 'blog_page',
			'default'     => 'style1',
			'priority'    => 8,
			'description' => esc_html__( 'Select default style for the blog page.', 'onum' ),
			'choices'     => array(
				'style1' => esc_attr__( 'Blog List', 'onum' ),
				'style2' => esc_attr__( 'Blog Grid', 'onum' ),
			),
		),
		'blog_column'           => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Blog Column', 'onum' ),
			'section'     => 'blog_page',
			'default'     => '3cl',
			'priority'    => 9,
			'description' => esc_html__( 'Select default column for the blog page.', 'onum' ),
			'choices'     => array(
				'2cl' => esc_attr__( '2 Column', 'onum' ),
				'3cl' => esc_attr__( '3 Column', 'onum' ),
				'4cl' => esc_attr__( '4 Column', 'onum' ),
				'5cl' => esc_attr__( '5 Column', 'onum' ),
			),
			'active_callback' => array(
                array(
                    'setting'  => 'blog_style',
                    'operator' => '==',
                    'value'    => 'style2',
                ),
            ),
		),			
		'blog_img_size'   => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Image Size', 'onum' ),
			'section'     => 'blog_page',
			'default'     => 'none',
			'priority'    => 9,
			'description' => esc_html__( 'Select default image size for the post.', 'onum' ),
			'choices'     => array(
				'none'    							=> esc_html__( 'Default', 'onum' ),
				'medium_large' 						=> esc_html__( 'Medium Large - 768 x 0', 'onum' ),
				'large' 							=> esc_html__( 'Large - 1024 x 1024', 'onum' ),
				'full' 								=> esc_html__( 'Full', 'onum' ),
			),
		),	
		'excerpt_switch'  => array(
            'type'        => 'toggle',
            'label'       => esc_html__( 'Blog Excerpt?', 'onum' ),
            'section'     => 'blog_page',
            'default'     => 1,
            'priority'    => 9,
        ),
		'excerpt_length'   => array(
			'type'        => 'number',
			'label'       => esc_html__( 'Custom Excerpt Length', 'onum' ),
			'section'  => 'blog_page',
			'description' => esc_html__( 'Custom Excerpt Length for Archive, Category, Tags, Search page.', 'onum' ),
			'default'     => 30,
			'priority'    => 9,
			'choices'     => [
				'min'  => 0,
				'max'  => 80,
				'step' => 1,
			],
			'active_callback' => array(
                array(
                    'setting'  => 'excerpt_switch',
                    'operator' => '==',
                    'value'    => true,
                ),
            ),
		),
		'post_entry_meta'              => array(
            'type'     => 'multicheck',
            'label'    => esc_html__( 'Entry Meta', 'onum' ),
            'section'  => 'blog_page',
            'default'  => array( 'author', 'date', 'comm' ),
            'choices'  => array(
                'author'  => esc_html__( 'Author', 'onum' ),
                'date'    => esc_html__( 'Date', 'onum' ),
                'comm'     => esc_html__( 'Comments', 'onum' ),
            ),
            'priority' => 10,
        ),

        // Single Post
        'single_post_layout'           => array(
            'type'        => 'radio-image',
            'label'       => esc_html__( 'Layout', 'onum' ),
            'section'     => 'single_post',
            'default'     => 'content-sidebar',
            'priority'    => 10,
            'choices'     => array(
				'content-sidebar' 	=> get_template_directory_uri() . '/inc/backend/images/right.png',
				'sidebar-content' 	=> get_template_directory_uri() . '/inc/backend/images/left.png',
				'full-content' 		=> get_template_directory_uri() . '/inc/backend/images/full.png',
			)
        ),
        'single_separator_top_page'     => array(
			'type'        => 'custom',
			'label'       => esc_html__( 'Page Header', 'onum' ),
			'section'     => 'single_post',
			'default'     => '<hr>',
			'priority'    => 10,
		),		
        'single_post_bg_top_page'         => array(
			'type'     		=> 'image',
			'label'    		=> esc_attr__( 'Background Image on Top Page', 'onum' ),
			'description' 	=> esc_attr__( 'Upload the default background image for the single post.', 'onum' ),
			'section'  		=> 'single_post',
			'default'  		=> trailingslashit( get_template_directory_uri() ) . 'images/bg-single-post-header.jpg',
			'priority' 		=> 10,
		),
		'single_post_pheader_color'    => array(
            'type'     		=> 'color',
            'label'    		=> esc_html__( 'Background Color', 'onum' ),
            'section'  		=> 'single_post',
            'default'  		=> '',
            'priority' 		=> 10,
            'output'        => array(
                array(
                    'element'  => '.single-page-header .sing-page-header-content',
                    'property' => 'background-color'
                ),
            ),
        ),        
        'single_post_ptitle_color'    => array(
            'type'     			=> 'color',
            'label'    			=> esc_html__( 'Page Title Color', 'onum' ),
            'section'  			=> 'single_post',
            'default'  			=> '',
            'priority' 			=> 10,
            'output'    		=> array(
                array(
                    'element'  => '.single-page-header.post-box .sing-page-header-content .entry-title, .single-page-header, .sing-page-header-content .breadcrumbs li, .single-page-header.post-box .sing-page-header-content .entry-meta .sl-wrapper a.liked, .single-page-header.post-box .sing-page-header-content .entry-meta .sl-wrapper a.liked:hover,
						.single-page-header.post-box .sing-page-header-content .entry-meta .sl-wrapper a.liked:active,
						.single-page-header.post-box .sing-page-header-content .entry-meta .sl-wrapper a.liked:focus,
						.single-page-header.post-box .sing-page-header-content .entry-meta .sl-wrapper a.liked span.sl-count,
						.single-page-header.post-box .sing-page-header-content .entry-meta .sl-wrapper a.liked span.sl-text,
						.single-page-header.post-box .sing-page-header-content .entry-meta .sl-wrapper .sl-count,
						.single-page-header.post-box .sing-page-header-content .entry-meta .sl-wrapper .sl-text',
                    'property' => 'color'
                ),
            ),
        ),
        'single_post_plink_color1'    => array(
            'type'     			=> 'multicolor',
            'label'    			=> esc_html__( 'Entry Meta Link Color', 'onum' ),
            'section'  			=> 'single_post',            
            'priority' 			=> 10,
            'choices'     => [
		        'link'    => esc_html__( 'Color', 'onum' ),
		        'hover'   => esc_html__( 'Hover', 'onum' ),
		        'active'  => esc_html__( 'Active', 'onum' ),
		    ],
		    'default'     => [
		        'link'   => '#ffffff',
	            'hover'  => '#a5b7d2',
	            'active' => '#a5b7d2',
		    ],
        ),
        'single_separator_breadcrumb'     => array(
			'type'        => 'custom',
			'label'       => esc_html__( 'Breadcrumbs Setting', 'onum' ),
			'section'     => 'single_post',
			'default'     => '<hr>',
			'priority'    => 10,
		),	
        'single_breadcrumbs'     => array(
            'type'        => 'toggle',
            'label'       => esc_html__( 'Breadcrumbs On/Off', 'onum' ),
            'section'     => 'single_post',
            'default'     => 0,
            'priority'    => 10,
        ),
        'single_post_breadcrumb_link_color1'    => array(
            'type'     			=> 'multicolor',
            'label'    			=> esc_html__( 'Breadcrumb Link Color', 'onum' ),
            'section'  			=> 'single_post',            
            'priority' 			=> 10,
            'choices'     => [
		        'link'    => esc_html__( 'Color', 'onum' ),
		        'hover'   => esc_html__( 'Hover', 'onum' ),
		        'active'  => esc_html__( 'Active', 'onum' ),
		    ],
		    'default'     => [
		        'link'   => '#a5b7d2',
	            'hover'  => '#fe4c1c',
	            'active' => '#fe4c1c',
		    ],
		    'active_callback'  => array(
				array(
					'setting'  => 'single_breadcrumbs',
					'operator' => '==',
					'value'    => 1,
				),
			),
        ),
        'single_post_breadcrumb_arrow_color'    => array(
            'type'     		=> 'color',
            'label'    		=> esc_html__( 'Breadcrumb Arrow Color', 'onum' ),
            'section'  		=> 'single_post',
            'default'  		=> '',
            'priority' 		=> 10,
            'output'        => array(
                array(
                    'element'  => '.single-page-header .breadcrumbs li:before',
                    'property' => 'color'
                ),
            ),
            'active_callback'  => array(
				array(
					'setting'  => 'single_breadcrumbs',
					'operator' => '==',
					'value'    => 1,
				),
			),
        ),        
        'single_post_pheader_ptop'  => array(
        	'type'        	=> 'dimensions',
			'label'       	=> esc_html__( 'Padding Top (Ex: 100px)', 'onum' ),
			'section'     	=> 'single_post',	
			'transport' 	=> 'auto',		          
            'priority' 		=> 10,
            'choices'   	=> array(
                'desktop' 	=> esc_attr__( 'Desktop', 'onum' ),
                'tablet'  	=> esc_attr__( 'Tablet', 'onum' ),
                'mobile'  	=> esc_attr__( 'Mobile', 'onum' ),
            ),
            'output'   		=> array(
                array(
                    'choice'      => 'mobile',
                    'element'     => '.single-page-header .sing-page-header-content',
                    'property'    => 'padding-top',
                    'media_query' => '@media (max-width: 767px)',
                ),
                array(
                    'choice'      => 'tablet',
                    'element'     => '.single-page-header .sing-page-header-content',
                    'property'    => 'padding-top',
                    'media_query' => '@media (min-width: 768px) and (max-width: 1024px)',
                ),
                array(
                    'choice'      => 'desktop',
                    'element'     => '.single-page-header .sing-page-header-content',
                    'property'    => 'padding-top',
                    'media_query' => '@media (min-width: 1024px)',
                ),
            ),
            'default' 		=> array(
                'desktop' 	=> '',
                'tablet'  	=> '',
                'mobile'  	=> '',
            ),
        ),
        'single_post_pheader_pbottom'  => array(
        	'type'        	=> 'dimensions',
			'label'       	=> esc_html__( 'Padding Bottom (Ex: 100px)', 'onum' ),
			'section'     	=> 'single_post',	
			'transport' 	=> 'auto',		          
            'priority' 		=> 10,
            'choices'   	=> array(
                'desktop' 	=> esc_attr__( 'Desktop', 'onum' ),
                'tablet'  	=> esc_attr__( 'Tablet', 'onum' ),
                'mobile'  	=> esc_attr__( 'Mobile', 'onum' ),
            ),
            'output'   		=> array(
                array(
                    'choice'      => 'mobile',
                    'element'     => '.single-page-header .sing-page-header-content',
                    'property'    => 'padding-bottom',
                    'media_query' => '@media (max-width: 767px)',
                ),
                array(
                    'choice'      => 'tablet',
                    'element'     => '.single-page-header .sing-page-header-content',
                    'property'    => 'padding-bottom',
                    'media_query' => '@media (min-width: 768px) and (max-width: 1024px)',
                ),
                array(
                    'choice'      => 'desktop',
                    'element'     => '.single-page-header .sing-page-header-content',
                    'property'    => 'padding-bottom',
                    'media_query' => '@media (min-width: 1024px)',
                ),
            ),
            'default' => array(
                'desktop' => '',
                'tablet'  => '',
                'mobile'  => '',
            ),
        ),
        'single_post_head_size'  => array(
            'type'     		=> 'dimensions',
            'label'    		=> esc_html__( 'Page Title Size (Ex: 20px)', 'onum' ),
            'section'  		=> 'single_post',
            'transport' 	=> 'auto',
            'priority' 		=> 10,
            'choices'   	=> array(
                'desktop' 	=> esc_attr__( 'Desktop', 'onum' ),
                'tablet'  	=> esc_attr__( 'Tablet', 'onum' ),
                'mobile'  	=> esc_attr__( 'Mobile', 'onum' ),
            ),
            'output'   		=> array(
                array(
                    'choice'      => 'mobile',
                    'element'     => '.single-page-header.post-box .sing-page-header-content .entry-title',
                    'property'    => 'font-size',
                    'media_query' => '@media (max-width: 767px)',
                ),
                array(
                    'choice'      => 'tablet',
                    'element'     => '.single-page-header.post-box .sing-page-header-content .entry-title',
                    'property'    => 'font-size',
                    'media_query' => '@media (min-width: 768px) and (max-width: 1024px)',
                ),
                array(
                    'choice'      => 'desktop',
                    'element'     => '.single-page-header.post-box .sing-page-header-content .entry-title',
                    'property'    => 'font-size',
                    'media_query' => '@media (min-width: 1024px)',
                ),
            ),
            'default' 		=> array(
                'desktop' 	=> '',
                'tablet'  	=> '',
                'mobile'  	=> '',
            ),
		),
		'single_separator_like_post'     => array(
			'type'        => 'custom',
			'label'       => esc_html__( 'Like Post Setting', 'onum' ),
			'section'     => 'single_post',
			'default'     => '<hr>',
			'priority'    => 10,
		),	
        'single_like_post'     => array(
            'type'        => 'toggle',
            'label'       => esc_html__( 'Like post On/Off', 'onum' ),
            'section'     => 'single_post',
            'default'     => 1,
            'priority'    => 10,
        ),
        'single_separator1'     => array(
			'type'        => 'custom',
			'label'       => esc_html__( 'Social Share', 'onum' ),
			'section'     => 'single_post',
			'default'     => '<hr>',
			'priority'    => 10,
		),
        'post_socials' => array(
            'type'     => 'multicheck',
            'section'  => 'single_post',
            'default'  => array( 'twitter', 'facebook', 'pinterest', 'linkedin' ),
            'choices'  => array(
                'twit'  	=> esc_html__( 'Twitter', 'onum' ),
                'face'    	=> esc_html__( 'Facebook', 'onum' ),
                'pint'     	=> esc_html__( 'Pinterest', 'onum' ),
                'link'     	=> esc_html__( 'Linkedin', 'onum' ),
                'google'  	=> esc_html__( 'Google Plus', 'onum' ),
                'tumblr'    => esc_html__( 'Tumblr', 'onum' ),
                'reddit'    => esc_html__( 'Reddit', 'onum' ),
                'vk'     	=> esc_html__( 'VK', 'onum' ),
            ),
            'priority' 		=> 10,
        ),
        'single_separator2'     => array(
			'type'        => 'custom',
			'label'       => esc_html__( 'Entry Footer', 'onum' ),
			'section'     => 'single_post',
			'default'     => '<hr>',
			'priority'    => 10,
		),		
        'author_box'      => array(
			'type'        => 'checkbox',
			'label'       => esc_attr__( 'Author Info Box', 'onum' ),
			'section'     => 'single_post',
			'default'     => true,
			'priority'    => 10,
		),
		'post_nav'     	  => array(
			'type'        => 'checkbox',
			'label'       => esc_attr__( 'Post Navigation', 'onum' ),
			'section'     => 'single_post',
			'default'     => true,
			'priority'    => 10,
		),
		'related_post'    => array(
			'type'        => 'checkbox',
			'label'       => esc_attr__( 'Related Posts', 'onum' ),
			'section'     => 'single_post',
			'default'     => true,
			'priority'    => 10,
		),

		// Portfolio Page
		'portfolio_archive'           => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Portfolio Archive', 'onum' ),
			'section'     => 'portfolio_page',
			'default'     => 'archive_default',
			'priority'    => 1,
			'description' => esc_html__( 'Select page default for the portfolio archive page.', 'onum' ),
			'choices'     => array(
				'archive_default' => esc_attr__( 'Archive page default', 'onum' ),
				'archive_custom'  => esc_attr__( 'Archive page custom', 'onum' ),
			),
		),
		'archive_page_custom'     => array(
			'type'        => 'dropdown-pages',  
	 		'label'       => esc_attr__( 'Select Page', 'onum' ), 
	 		'description' => esc_attr__( 'Choose a custom page for archive portfolio page.', 'onum' ), 
	 		'section'     => 'portfolio_page', 
	 		'default'     => '', 
	 		'priority'    => 2,	 		
	 		'active_callback'  => array(
				array(
					'setting'  => 'portfolio_archive',
					'operator' => '==',
					'value'    => 'archive_custom',
				),
			),
		),
		'portfolio_column'           => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Portfolio Column', 'onum' ),
			'section'     => 'portfolio_page',
			'default'     => '3cl',
			'priority'    => 3,
			'description' => esc_html__( 'Select default column for the portfolio page.', 'onum' ),
			'choices'     => array(
				'2cl' => esc_attr__( '2 Column', 'onum' ),
				'3cl' => esc_attr__( '3 Column', 'onum' ),
				'4cl' => esc_attr__( '4 Column', 'onum' ),
				'5cl' => esc_attr__( '5 Column', 'onum' ),
			),
		),
		'portfolio_style'           => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Portfolio Style', 'onum' ),
			'section'     => 'portfolio_page',
			'default'     => 'style1',
			'priority'    => 4,
			'description' => esc_html__( 'Select default style for the portfolio page.', 'onum' ),
			'choices'     => array(
				'style1' => esc_attr__( 'Grid Normal', 'onum' ),
				'style2' => esc_attr__( 'Grid Masonry', 'onum' ),
			),
		),
		'portfolio_img_size'           => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Image Size', 'onum' ),
			'section'     => 'portfolio_page',
			'default'     => 'none',
			'priority'    => 4,
			'description' => esc_html__( 'Select default image size for the portfolio archive page.', 'onum' ),
			'choices'     => array(
				'none' 								=> esc_html__( 'Default', 'onum' ),
				'medium_large' 						=> esc_html__( 'Medium Large - 768 x 0', 'onum' ),
				'large' 							=> esc_html__( 'Large - 1024 x 1024', 'onum' ),
				'full' 								=> esc_html__( 'Full', 'onum' ),
			),
		),
		'portfolio_posts_per_page' => array(
			'type'        => 'number',
			'section'     => 'portfolio_page',
			'priority'    => 5,
			'label'       => esc_html__( 'Posts per page', 'onum' ),			
			'description' => esc_html__( 'Change Posts Per Page for Portfolio Archive Page, Taxonomy Page.', 'onum' ),
			'default'     => '',
		),
		'portfolio_separator'     => array(
			'type'        => 'custom',
			'label'       => 'Single Portfolio Page',
			'section'     => 'portfolio_page',
			'default'     => '<hr>',
			'priority'    => 6,
		),
		'pf_related_switch'     => array(
			'type'        => 'toggle',
			'label'       => esc_attr__( 'Related Projects On/Off?', 'onum' ),
			'section'     => 'portfolio_page',
			'default'     => 1,
			'priority'    => 7,
		),
		'related_prj_text'  => array(
			'type'        => 'text',
			'label'       => esc_html__('Related Projects Text', 'onum'),
			'section'     => 'portfolio_page',
			'default'     => '',
			'priority'    => 7,
			'active_callback'  => array(
				array(
					'setting'  => 'pf_related_switch',
					'operator' => '==',
					'value'    => 1,
				),
			),
		),
		'related_prj_style'           => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Related Projects Style', 'onum' ),
			'section'     => 'portfolio_page',
			'default'     => 'style1',
			'priority'    => 7,
			'description' => esc_html__( 'Select default style for the related projects.', 'onum' ),
			'choices'     => array(
				'style1' => esc_attr__( 'Grid Normal', 'onum' ),
				'style2' => esc_attr__( 'Grid Masonry', 'onum' ),
			),
			'active_callback'  => array(
				array(
					'setting'  => 'pf_related_switch',
					'operator' => '==',
					'value'    => 1,
				),
			),
		),

		/* 404 */
		'page_404'   	  => array(
			'type'        => 'dropdown-pages',  
	 		'label'       => esc_attr__( 'Select Page', 'onum' ), 
	 		'description' => esc_attr__( 'Choose a custom page for page 404.', 'onum' ),
	 		'placeholder' => esc_attr__( 'Select a page 404', 'onum' ), 
	 		'section'     => 'error_404', 
	 		'default'     => '', 
			'priority'    => 3,
		),

		// Typography        
        'body_typo'    => array(
            'type'     => 'typography',
            'label'    => esc_html__( 'Body Font 1', 'onum' ),
            'section'  => 'typography',
            'priority' => 2,
            'default'  => array(
                'font-family'    => '',
                'variant'        => '',
                'font-size'      => '',
                'line-height'    => '',
                'letter-spacing' => '',
                'subsets'        => array( 'latin', 'latin-ext' ),
                'color'          => '#606060',
                'text-transform' => '',
            ),
            'output'      => array(
                array(
                    'element' => 'body',
                ),
            ),            
        ),
        'body_typo2'    => array(
            'type'     => 'typography',
            'label'    => esc_html__( 'Body Font 2', 'onum' ),
            'section'  => 'typography',
            'priority' => 2,
            'default'  => array(
                'font-family'    => '',
                'color'          => '',
            ),            
        ),
        'heading1_typo'=> array(
            'type'     => 'typography',
            'label'    => esc_html__( 'Heading 1', 'onum' ),
            'section'  => 'typography',
            'priority' => 3,
            'default'  => array(
                'font-family'    => '',
                'variant'        => '',
                'font-size'      => '',
                'line-height'    => '',
                'letter-spacing' => '',
                'subsets'        => array( 'latin', 'latin-ext' ),
                'color'          => '',
                'text-transform' => '',
            ),
            'output'      => array(
                array(
                    'element' => 'h1, .elementor-widget.elementor-widget-heading h1.elementor-heading-title',
                ),
            ),            
        ),
        'heading2_typo'=> array(
            'type'     => 'typography',
            'label'    => esc_html__( 'Heading 2', 'onum' ),
            'section'  => 'typography',
            'priority' => 4,
            'default'  => array(
                'font-family'    => '',
                'variant'        => '',
                'font-size'      => '',
                'line-height'    => '',
                'letter-spacing' => '',
                'subsets'        => array( 'latin', 'latin-ext' ),
                'color'          => '',
                'text-transform' => '',
            ),
            'output'      => array(
                array(
                    'element' => 'h2, .elementor-widget.elementor-widget-heading h2.elementor-heading-title',
                ),
            ),
        ),
        'heading3_typo'=> array(
            'type'     => 'typography',
            'label'    => esc_html__( 'Heading 3', 'onum' ),
            'section'  => 'typography',
            'priority' => 5,
            'default'  => array(
                'font-family'    => '',
                'variant'        => '',
                'font-size'      => '',
                'line-height'    => '',
                'letter-spacing' => '',
                'subsets'        => array( 'latin', 'latin-ext' ),
                'color'          => '',
                'text-transform' => '',
            ),
            'output'      => array(
                array(
                    'element' => 'h3, .elementor-widget.elementor-widget-heading h3.elementor-heading-title',
                ),
            ),
        ),
        'heading4_typo'=> array(
            'type'     => 'typography',
            'label'    => esc_html__( 'Heading 4', 'onum' ),
            'section'  => 'typography',
            'priority' => 6,
            'default'  => array(
                'font-family'    => '',
                'variant'        => '',
                'font-size'      => '',
                'line-height'    => '',
                'letter-spacing' => '',
                'subsets'        => array( 'latin', 'latin-ext' ),
                'color'          => '',
                'text-transform' => '',
            ),
            'output'      => array(
                array(
                    'element' => 'h4, .elementor-widget.elementor-widget-heading h4.elementor-heading-title',
                ),
            ),
        ),
        'heading5_typo'=> array(
            'type'     => 'typography',
            'label'    => esc_html__( 'Heading 5', 'onum' ),
            'section'  => 'typography',
            'priority' => 7,
            'default'  => array(
                'font-family'    => '',
                'variant'        => '',
                'font-size'      => '',
                'line-height'    => '',
                'letter-spacing' => '',
                'subsets'        => array( 'latin', 'latin-ext' ),
                'color'          => '',
                'text-transform' => '',
            ),
            'output'      => array(
                array(
                    'element' => 'h5, .elementor-widget.elementor-widget-heading h5.elementor-heading-title',
                ),
            ),
        ),
        'heading6_typo'=> array(
            'type'     => 'typography',
            'label'    => esc_html__( 'Heading 6', 'onum' ),
            'section'  => 'typography',
            'priority' => 8,
            'default'  => array(
                'font-family'    => '',
                'variant'        => '',
                'font-size'      => '',
                'line-height'    => '',
                'letter-spacing' => '',
                'subsets'        => array( 'latin', 'latin-ext' ),
                'color'          => '',
                'text-transform' => '',
            ),
            'output'      => array(
                array(
                    'element' => 'h6, .elementor-widget.elementor-widget-heading h6.elementor-heading-title',
                ),
            ),
        ),

		//Color Scheme
        'bg_body'      => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Background Body', 'onum' ),
            'section'  => 'color_scheme',
            'default'  => '',
            'priority' => 10,
            'output'   => array(
                array(
                    'element'  => 'body, .site-content',
                    'property' => 'background-color',
                ),
            ),
        ),
        'main_color'   => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Primary Color', 'onum' ),
            'section'  => 'color_scheme',
            'default'  => '#fe4c1c',
            'priority' => 10,
        ),
        'second_color' => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Second Color', 'onum' ),
            'section'  => 'color_scheme',
            'default'  => '#00c3ff',
            'priority' => 10,
        ),
        'third_color' => array(
            'type'     => 'color',
            'label'    => esc_html__( 'Third Color', 'onum' ),
            'section'  => 'color_scheme',
            'default'  => '#0160e7',
            'priority' => 10,
        ),

        //Google Analytics
        'js_code'  => array(
            'type'        => 'code',
            'label'       => esc_html__( 'Google Analytics Code', 'onum' ),
            'section'     => 'script_code',
            'choices'     => [
				'language' => 'js',
			],
            'priority'    => 3,
        ),
	);
	$settings['panels']   = apply_filters( 'onum_customize_panels', $panels );
	$settings['sections'] = apply_filters( 'onum_customize_sections', $sections );
	$settings['fields']   = apply_filters( 'onum_customize_fields', $fields );

	return $settings;
}
add_action( 'after_setup_theme', 'onum_customizer_init' );
function onum_customizer_init() {
    $onum_customize = new ONUM_Customize( onum_customize_settings() );
}

