<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Switchs
 */
class ONUM_Switchs extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'iswitchs';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'Onum Switchs', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-call-to-action';
	}

	// The get_categories method, lets you set the category of the widget, return the category name as a string.
	public function get_categories() {
		return [ 'category_onum' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Titles', 'onum' ),
			]
		);

		$this->add_control(
			'before_text',
			[
				'label' => 'Before Text',
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Before', 'onum' ),
			]
		);

		$this->add_control(
			'after_text',
			[
				'label' => 'After Text',
				'type' => Controls_Manager::TEXT,
				'default' => __( 'After', 'onum' ),
			]
		);
		$this->add_control(
			'page_before',
			[
				'label' => __( 'Select a Before Page', 'onum' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->select_param_list_pages(),
				'multiple' => false,
				'label_block' => true,
				'placeholder' => __( 'All Pages', 'onum' ),
			]
		);
		$this->add_control(
			'page_after',
			[
				'label' => __( 'Select a After Page', 'onum' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->select_param_list_pages(),
				'multiple' => false,
				'label_block' => true,
				'placeholder' => __( 'All Pages', 'onum' ),
			]
		);
		$this->end_controls_section();

		//Styling		
		$this->start_controls_section(
			'style_toggle',
			[
				'label' => __( 'Toggle', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);		

		$this->start_controls_tabs( 'tabs_switch_style' );
		$this->start_controls_tab(
			'tab_switch_before',
			[
				'label' => __( 'Before', 'onum' ),
			]
		);
		$this->add_control(
			'before_textcolor',
			[
				'label' => __( 'Before Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ot_switchs_button span.b-switch.active' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'before_dot_color',
			[
				'label' => __( 'Before DOT Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ot_switchs_button .switch_slider:before' => 'background-color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'before_dot_boxshadow',
				'label' => __( 'Before DOT Box Shadow', 'onum' ),
				'selector' => '{{WRAPPER}} .ot_switchs_button .switch_slider:before',
			]
		);
		$this->add_control(
			'before_bgcolor',
			[
				'label' => __( 'Before WRAPPER Background Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ot_switchs_button .switch_slider' => 'background: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'before_border_color',
			[
				'label' => __( 'Before WRAPPER Border Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ot_switchs_button .switch_slider' => 'border-color: {{VALUE}};',
				]
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_switch_after',
			[
				'label' => __( 'After', 'onum' ),
			]
		);
		$this->add_control(
			'after_textcolor',
			[
				'label' => __( 'After Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot_switchs_button span.a-switch.active' => 'color: {{VALUE}};',				 	
				]
			]
		);
		$this->add_control(
			'after_dot_color',
			[
				'label' => __( 'After DOT Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ot_switchs_button input:checked + .switch_slider:before' => 'background: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'after_dot_boxshadow',
				'label' => __( 'After DOT Box Shadow', 'onum' ),
				'selector' => '{{WRAPPER}} .ot_switchs_button input:checked + .switch_slider:before',
			]
		);
		$this->add_control(
			'after_bgcolor',
			[
				'label' => __( 'After WRAPPER Background Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ot_switchs_button input:checked + .switch_slider' => 'background: {{VALUE}};'
				]
			]
		);
		$this->add_control(
			'after_border_color',
			[
				'label' => __( 'After WRAPPER Border Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ot_switchs_button input:checked + .switch_slider' => 'border-color: {{VALUE}};',
				]
			]
		);			

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'separator_switchs',
			[
				'label' => __( 'Default', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'label_block' => true,
				'show_label' => false,
			]
		);
		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ot_switchs_button .switch-text' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .ot_switchs_button .switch-text',
			]
		);
		$this->add_responsive_control(
			'toggle_bottom_space',
			[
				'label' => __( 'Spacing', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ot_switchs_button' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>

		<div class="ot_switchs_wrap">
			<div class="ot_switchs_button">
				<span class="b-switch switch-text active"><?php echo $settings['before_text']; ?></span>
				<label class="ot__switch">
				  	<input type="checkbox">
				  	<span class="switch_slider round"></span>
				</label>
				<span class="a-switch switch-text"><?php echo $settings['after_text']; ?></span>
			</div>
			<div class="ot_switchs_content">
				<div class="ot_switchs_before">
					<?php if( $settings['page_before'] != '' ){ echo \Elementor\Plugin::$instance->frontend->get_builder_content( $settings['page_before'] ); } ?>
				</div>
				<div class="ot_switchs_after">
					<?php if( $settings['page_after'] != '' ){ echo \Elementor\Plugin::$instance->frontend->get_builder_content( $settings['page_after'] ); } ?>
				</div>
			</div>
		</div>

	    <?php
	}

	

	protected function select_param_list_pages() {
		$pages = get_pages(); 
		$list_page = array();
		foreach ( $pages as $page ) {
			$list_page[$page->ID] = $page->post_title;
		}
		return $list_page;	  	
	}
}
// After the Schedule class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new ONUM_Switchs() );