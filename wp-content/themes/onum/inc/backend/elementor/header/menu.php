<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Menu
 */
class ONUM_Menu extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'imenu';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'OT Nav Menu', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-nav-menu';
	}

	// The get_categories method, lets you set the category of the widget, return the category name as a string.
	public function get_categories() {
		return [ 'category_onum_header' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Menu', 'onum' ),
			]
		);

		$menus = $this->get_available_menus();
		$this->add_control(
			'nav_menu',
			[
				'label' => esc_html__( 'Select Menu', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'multiple' => false,
				'options' => $menus,
				'default' => array_keys( $menus )[0],
				'save_default' => true,

			]
		);

		$this->end_controls_section();

		/* Parent Menu */
		$this->start_controls_section(
			'style_menu_section',
			[
				'label' => __( 'Parent Menus', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);		

		$this->start_controls_tabs(
			'style_menu_tabs'
		);

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => __( 'Normal', 'onum' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .main-navigation > ul > li > a' => 'color: {{VALUE}};',					
				]
			]
		);
		$this->add_control(
			'arrow_color',
			[
				'label' => __( 'Arrow Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .main-navigation > ul > li.menu-item-has-children > a:after' => 'color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => __( 'Scroll', 'plugin-name' ),
			]
		);

		$this->add_control(
			'text_scroll_color',
			[
				'label' => __( 'Scroll Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.cd-header.is-fixed .main-navigation > ul > li > a' => 'color: {{VALUE}} !important;',					
				]
			]
		);
		$this->add_control(
			'arrow_scroll_color',
			[
				'label' => __( 'Scroll Arrow Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.cd-header.is-fixed .main-navigation > ul > li.menu-item-has-children > a:after' => 'color: {{VALUE}} !important;',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		
		$this->add_control(
			'text_hover_color',
			[
				'label' => __( 'Text Hover/Active Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .main-navigation > ul > li > a:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .main-navigation > ul > li.menu-item-has-children > a:hover:after' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .onum_menu__no-border.main-navigation > ul > li.current-menu-item > a, {{WRAPPER}} .onum_menu__no-border.main-navigation > ul > li.current-menu-ancestor > a' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .onum_menu__no-border.main-navigation > ul > li.current-menu-item > a:after, {{WRAPPER}} .onum_menu__no-border.main-navigation > ul > li.current-menu-ancestor > a:after' => 'color: {{VALUE}} !important;',
				]
			]
		);		
				
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'menu_typography',
				'selector' => '{{WRAPPER}} .main-navigation ul',
			]
		);
		$this->add_control(
			'show_border',
			[
				'label' => __( 'Show Border Menu Item', 'onum' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'onum' ),
				'label_off' => __( 'Hide', 'onum' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'border_color',
			[
				'label' => __( 'Border Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .main-navigation > ul > li:after, {{WRAPPER}} .main-navigation > ul > li:before' => 'background-color: {{VALUE}};',					
				],
				'condition' => [
	                'show_border' => 'yes',
	            ],
			]
		);
		$this->end_controls_section();

		/* Dropdown Menu */
		$this->start_controls_section(
			'style_smenu_section',
			[
				'label' => __( 'Dropdown Menus', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'smenu_width',
			[
				'label' => __( 'Width', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .main-navigation ul li ul.sub-menu' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'smenu_padd',
			[
				'label' => __( 'Padding', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .main-navigation ul li ul.sub-menu' => 'padding-top: {{TOP}}{{UNIT}};padding-bottom:{{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}} .main-navigation ul ul.sub-menu li' => 'padding-right: {{RIGHT}}{{UNIT}};padding-left: {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'smenu_top',
			[
				'label' => __( 'Top', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .main-navigation ul li ul.sub-menu:before' => 'height: {{SIZE}}{{UNIT}};top: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .main-navigation ul li ul.sub-menu' => 'top: calc(100% + {{SIZE}}{{UNIT}});',
				]
			]
		);		
		$this->add_control(
			'text_smenu_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .main-navigation ul ul.sub-menu a, {{WRAPPER}} .main-navigation ul ul.sub-menu > li.menu-item-has-children > a:after' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'text_smenu_hover_color',
			[
				'label' => __( 'Text Hover Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .main-navigation ul ul.sub-menu a:hover, {{WRAPPER}} .main-navigation ul ul.sub-menu li.current-menu-item > a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .main-navigation ul li li a:before' => 'background: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'bgsmenu_color',
			[
				'label' => __( 'Background Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .main-navigation ul ul.sub-menu' => 'background: {{VALUE}};',
				]
			]
		);
		$this->add_responsive_control(
			'bordersmenu',
			[
				'label' => __( 'Border Radius', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .main-navigation ul ul.sub-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'boxsmenu_shadow',
				'label' => __( 'Box Shadow', 'onum' ),
				'selector' => '{{WRAPPER}} .main-navigation ul ul.sub-menu',
			]
		);	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'smenu_typography',
				'selector' => '{{WRAPPER}} .main-navigation ul ul.sub-menu a',
			]
		);

		$this->end_controls_section();

	}

	protected function get_available_menus(){
		$menus = wp_get_nav_menus();
		$options = [];

		foreach ( $menus as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}

		return $options;
    }

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		  	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		
		/*multisite */
		if ( is_multisite() ) {
		  	/*this plugin is network activated - Mega menu must be network activated */
		  	if ( is_plugin_active_for_network( plugin_basename(__FILE__) ) ) {
		    	$active_mmenu = is_plugin_active_for_network('ot_mega-menu/ot_mega-menu.php') ? true : false; 
		  	/*this plugin is locally activated - Mega menu can be network or locally activated */
		  	} else {
		    	$active_mmenu = is_plugin_active( 'ot_mega-menu/ot_mega-menu.php')  ? true : false;   
		  	}
			/*this plugin runs on a single site */
		} else {
		  	$active_mmenu =  is_plugin_active( 'ot_mega-menu/ot_mega-menu.php') ? true : false;     
		}		
		?>			
	    	<nav id="site-navigation" class="main-navigation <?php if ( 'yes' !== $settings['show_border'] ) { echo 'onum_menu__no-border'; } ?>">	
				<?php
					wp_nav_menu( array(
						'menu' 			 => $settings['nav_menu'],
						'menu_id'        => 'primary-menu',
						'container'      => 'ul',
						'theme_location' => '__no_such_location',
    					'fallback_cb'    => '__return_empty_string', 
    					'walker'         => $active_mmenu ? new \Ot_Mega_Menu_Walker() : '',
					) );
				?>
			</nav>
	    <?php 
	}

	
}
// After the ONUM_Menu class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new ONUM_Menu() );