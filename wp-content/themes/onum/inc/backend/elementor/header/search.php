<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Search
 */
class ONUM_Search extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'isearch';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'OT Header Search', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-search';
	}

	// The get_categories method, lets you set the category of the widget, return the category name as a string.
	public function get_categories() {
		return [ 'category_onum_header' ];
	}

	protected function register_controls() {
		
		/*** Style ***/
		$this->start_controls_section(
			'style_icon_section',
			[
				'label' => __( 'Icon', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .octf-btn-cta .toggle_search i:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_width',
			[
				'label' => __( 'Icon Width', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .octf-btn-cta .octf-cta-icons i' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .octf-btn-cta .toggle_search i, {{WRAPPER}}  .octf-btn-cta .toggle_search i:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'icon_bgcolor',
			[
				'label' => __( 'Icon Background Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .octf-btn-cta .toggle_search i' => 'background-color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'icon_scroll_color',
			[
				'label' => __( 'Icon Color Scroll', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .is-icon-sticky.octf-btn-cta .octf-cta-icons i, {{WRAPPER}} .is-icon-sticky.octf-btn-cta .octf-cta-icons i:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'icon_scroll_bgcolor',
			[
				'label' => __( 'Icon Background Color Scroll', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .is-icon-sticky.octf-btn-cta .octf-cta-icons i' => 'background-color: {{VALUE}};',
				]
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'style_form_section',
			[
				'label' => __( 'Form', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'bg_form_color',
			[
				'label' => __( 'Background Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .h-search-form-inner' => 'background: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'text_form_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .h-search-form-inner input, {{WRAPPER}} .h-search-form-inner button, {{WRAPPER}} .h-search-form-inner ::placeholder' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'border_input_color',
			[
				'label' => __( 'Border Input Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .h-search-form-inner input' => 'border-color: {{VALUE}};',
				]
			]
		);
		$this->add_responsive_control(
			'form_width',
			[
				'label' => __( 'Width', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .h-search-form-field' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'form_top',
			[
				'label' => __( 'Top', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .h-search-form-field' => 'top: calc(100% + {{SIZE}}{{UNIT}});',
				],
			]
		);
		$this->add_responsive_control(
			'form_right',
			[
				'label' => __( 'Right', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .h-search-form-field' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();	
			
		?>
	    	<div class="octf-search octf-btn-cta octf-search-builder">
				<div class="toggle_search toggle_click_search_2 octf-cta-icons">
					<i class="flaticon-search"></i>
				</div>
				<!-- Form Search on Header -->
				<div class="h-search-form-field">
					<div class="h-search-form-inner">
						<?php get_search_form(); ?>
					</div>									
				</div>
			</div>		    
	    <?php
	}

	
}
// After the ONUM_Search class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new ONUM_Search() );