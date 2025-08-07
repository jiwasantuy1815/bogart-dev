<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Menu_Mobile
 */
class ONUM_Menu_Mobile extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'imenu_mobile';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'OT Menu Mobile', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-select';
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

		$this->add_control(
			'pos_menu',
			[
				'label' => __( 'Position', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'on-right',
				'options' => [
					'on-left' 	=> __( 'On Left', 'onum' ),
					'on-right'  => __( 'On Right', 'onum' ),
				]
			]
		);

		$this->end_controls_section();
		
		/*** Style ***/
		$this->start_controls_section(
			'style_icon_section',
			[
				'label' => __( 'Icon', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'style_icon_tabs'
		);

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => __( 'Normal', 'onum' ),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .mmenu_toggle button svg' => 'fill: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'icon_bgcolor',
			[
				'label' => __( 'Background Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .mmenu_toggle button' => 'background: {{VALUE}};',
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
			'icon_scroll_color',
			[
				'label' => __( 'Scroll Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.cd-header.is-fixed .mmenu_toggle button svg' => 'fill: {{VALUE}} !important;',
				]
			]
		);
		$this->add_control(
			'icon_scroll_bgcolor',
			[
				'label' => __( 'Scroll Background Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.cd-header.is-fixed .mmenu_toggle button' => 'background: {{VALUE}} !important;',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();		

		$this->add_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mmenu_toggle button svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'btn_size',
			[
				'label' => __( 'Button Size', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mmenu_toggle button' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'btn_line_height',
			[
				'label' => __( 'Line Height', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mmenu_toggle button' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_mmenu_section',
			[
				'label' => __( 'Menu', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);		
		$this->add_control(
			'bg_mmenu',
			[
				'label' => __( 'Background', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .mmenu-wrapper' => 'background: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'color_mmenu',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .mmenu-wrapper .mobile_mainmenu li a, {{WRAPPER}} .mmenu-wrapper .mobile_mainmenu > li.menu-item-has-children .arrow i' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'hcolor_mmenu',
			[
				'label' => __( 'Text Color Hover', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .mobile_mainmenu > li > a:hover, {{WRAPPER}} .mobile_mainmenu > li.current-menu-item > a, {{WRAPPER}} .mobile_mainmenu > li.current-menu-ancestor > a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .mobile_mainmenu li li a:hover, {{WRAPPER}} .mobile_mainmenu ul > li > ul > li.current-menu-ancestor > a' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'bcolor_mmenu',
			[
				'label' => __( 'Border Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .mmenu-wrapper .mobile_mainmenu li a' => 'border-color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'arrow_mmenu',
			[
				'label' => __( 'Arrow Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .mobile_nav .mobile_mainmenu > li.menu-item-has-children .arrow i' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_responsive_control(
			'space_menu',
			[
				'label' => __( 'Spacing', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mobile_nav .mobile_mainmenu li a' => 'padding: {{SIZE}}{{UNIT}} 0px;',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mmenu_typography',
				'selector' => '{{WRAPPER}} .mmenu-wrapper .mobile_mainmenu li a',
			]
		);
		$this->add_control(
			'color_back',
			[
				'label' => __( 'Back Button Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .mmenu-wrapper .mmenu-close' => 'color: {{VALUE}};',
				]
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
		?>
			
	    	<div class="octf-menu-mobile octf-cta-header">
				<div id="mmenu-toggle" class="mmenu_toggle">
					<button>
				        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"  viewBox="0 0 24.75 24.75" style="enable-background:new 0 0 24.75 24.75;" xml:space="preserve" >
						<g>
							<path d="M0,3.875c0-1.104,0.896-2,2-2h20.75c1.104,0,2,0.896,2,2s-0.896,2-2,2H2C0.896,5.875,0,4.979,0,3.875z M22.75,10.375H2
								c-1.104,0-2,0.896-2,2c0,1.104,0.896,2,2,2h20.75c1.104,0,2-0.896,2-2C24.75,11.271,23.855,10.375,22.75,10.375z M22.75,18.875H2
								c-1.104,0-2,0.896-2,2s0.896,2,2,2h20.75c1.104,0,2-0.896,2-2S23.855,18.875,22.75,18.875z"/>
						</g>
						</svg>
					</button>
				</div>
				<div class="site-overlay mmenu-overlay"></div>
				<div id="mmenu-wrapper" class="mmenu-wrapper <?php echo $settings['pos_menu']; ?>">
					<div class="mmenu-inner">
						<a class="mmenu-close" href="#"><i class="flaticon-close"></i></a>
						<div class="mobile_nav">
							<?php
								wp_nav_menu( array(
									'menu' 			 => $settings['nav_menu'],
									'menu_class'     => 'mobile_mainmenu',
									'container'      => '',
								) );
							?>
						</div>   	
					</div>   	
				</div>
			</div>
	    <?php
	}

	
}
// After the ONUM_Menu_Mobile class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new ONUM_Menu_Mobile() );