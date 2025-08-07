<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Icon Box
 */
class Onum_Pricing_Table extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'ipricingtable';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'Onum Pricing Table', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-price-table';
	}

	// The get_categories method, lets you set the category of the widget, return the category name as a string.
	public function get_categories() {
		return [ 'category_onum' ];
	}

	protected function register_controls() {

		//Content Service box
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Pricing Table', 'onum' ),
			]
		);

		$this->add_control(
			'table_style',
			[
				'label' 	=> __( 'Table Style', 'onum' ),
				'type'  	=> Controls_Manager::SELECT,
				'default' 	=> 's1',
				'options' 	=> [
					's1'  		=> __( 'Second Color', 'onum' ),
					's2'  		=> __( 'Third Color', 'onum' ),
					's3'  		=> __( 'Primary Color', 'onum' ),
					'pink'   	=> __( 'Pink Color', 'onum' ),
					'purple'   	=> __( 'Purple Color', 'onum' ),
					'aqua'   	=> __( 'Aqua Color', 'onum' ),
				]
			]
		);

		$this->add_control(
	       'price_image',
	        [
	           'label' => esc_html__( 'Pricing Image', 'onum' ),
	           'type'  => Controls_Manager::MEDIA,
				'default' => [
					'url' => get_template_directory_uri().'/images/price1.png',
			  	]
		    ]
	    );

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Standard', 'onum' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'price',
			[
				'label' => __( 'Price', 'onum' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( '<sup>$</sup> 69.99', 'onum' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'price_for',
			[
				'label' => __( 'Text Under Price', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Monthly Package', 'onum' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'details',
			[
				'label' => 'Details',
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( '<ul><li>Social Media Marketing</li><li>2.100 Keywords</li><li>One Way Link Building</li></ul>', 'onum' ),
			]
		);

		$this->add_control(
			'label_link',
			[
				'label' => 'Button',
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Choose Plane', 'onum' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'onum' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'onum' )
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'style_table_section',
			[
				'label' => __( 'Table Box', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label' => __( 'Padding Box', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ot-pricing-table' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'box_border',
				'label' => __( 'Border', 'onum' ),
				'selector' => '{{WRAPPER}} .ot-pricing-table',
			]
		);
		$this->add_responsive_control(
			'box_radius',
			[
				'label' => __( 'Border Radius', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ot-pricing-table' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ot-pricing-table:before' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .ot-pricing-table:after' => 'border-radius: 0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'accs_box_shadow',
				'selector' => '{{WRAPPER}} .ot-pricing-table',
				'separator' => 'before',
			]
		);		
		$this->add_control(
			'heading_bg_top',
			[
				'label' => __( 'Background Top Box', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'before_bg_color',
				'label' => __( 'Background', 'onum' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} .ot-pricing-table:before',
			]
		);
		$this->add_control(
			'heading_bg_bot',
			[
				'label' => __( 'Background Bottom Box', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'after_bg_color',
				'label' => __( 'Background', 'onum' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} .ot-pricing-table:after',
			]
		);
		
		$this->end_controls_section();
		
		$this->start_controls_section(
			'style_content_section',
			[
				'label' => __( 'Content', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		//Title
		$this->add_control(
			'heading_title',
			[
				'label' => __( 'Title', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'title_bgcolor',
			[
				'label' => __( 'Background Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .title-table' => 'background: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .title-table' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'title_shadow',
				'label' => __( 'Box Shadow', 'onum' ),
				'selector' => '{{WRAPPER}} .title-table',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .title-table',
			]
		);

		//Image
		$this->add_control(
			'heading_image',
			[
				'label' => __( 'Image', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'image_space',
			[
				'label' => __( 'Spacing', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ot-pricing-table .inner-table img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);		

		//Price
		$this->add_control(
			'heading_price',
			[
				'label' => __( 'Price', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'price_space',
			[
				'label' => __( 'Spacing', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ot-pricing-table h2' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'price_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-pricing-table h2' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				'selector' => '{{WRAPPER}} .ot-pricing-table h2',
			]
		);

		//Under Price
		$this->add_control(
			'heading_price_for',
			[
				'label' => __( 'Under Price', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'price_for_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .inner-table > p' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_for_typography',
				'selector' => '{{WRAPPER}} .inner-table > p',
			]
		);

		//Details
		$this->add_control(
			'heading_des',
			[
				'label' => __( 'Details', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'des_padding',
			[
				'label' => __( 'Padding Top', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ot-pricing-table .details' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'des_margin',
			[
				'label' => __( 'Margin Top', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ot-pricing-table .details' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'des_border_color',
			[
				'label' => __( 'Border Top Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-pricing-table .details' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'des_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-pricing-table .details' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'des_typography',
				'selector' => '{{WRAPPER}} .ot-pricing-table .details',
			]
		);

		//Button
		$this->add_control(
			'heading_btn',
			[
				'label' => __( 'Button', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-pricing-table .octf-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .octf-btn i' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_bg_color',
			[
				'label' => __( 'Background Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-pricing-table .octf-btn' => 'background: {{VALUE}};',
					'{{WRAPPER}} .octf-btn i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_border_radius',
			[
				'label' => __( 'Border Radius', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .octf-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'btn_box_shadow',
				'label' => __( 'Box Shadow', 'onum' ),
				'selector' => '{{WRAPPER}} .octf-btn',
			]
		);

		$this->add_responsive_control(
			'btn_text_padding',
			[
				'label' => __( 'Padding', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .octf-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'btn_typography',
				'selector' => '{{WRAPPER}} .octf-btn',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'button', 'href', $settings['link']['url'] );

			if ( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'button', 'target', '_blank' );
			}

			if ( $settings['link']['nofollow'] ) {
				$this->add_render_attribute( 'button', 'rel', 'nofollow' );
			}
		}

		$this->add_render_attribute( 'button', 'class', 'octf-btn octf-btn-icon' );

		if ( $settings['table_style'] == 's1' ) {
			$this->add_render_attribute( 'button', 'class', 'octf-btn-secondary' );
		}elseif ( $settings['table_style'] == 's2' ) {
			$this->add_render_attribute( 'button', 'class', 'octf-btn-third' );
		}elseif ( $settings['table_style'] == 's3' ) {
			$this->add_render_attribute( 'button', 'class', 'octf-btn-primary' );
		}elseif ( $settings['table_style'] == 'pink' ) {
			$this->add_render_attribute( 'button', 'class', 'octf-btn-pink' );
		}elseif ( $settings['table_style'] == 'purple' ) {
			$this->add_render_attribute( 'button', 'class', 'octf-btn-purple' );
		}else{
			$this->add_render_attribute( 'button', 'class', 'octf-btn-aqua' );
		}

		?>

		<div class="ot-pricing-table bg-shape <?php echo esc_attr( $settings['table_style'] ); ?>">
			<span class="title-table"><?php echo esc_html( $settings['title'] ); ?></span>
			<div class="inner-table">
				<img src="<?php echo esc_url( $settings['price_image']['url'] ); ?>" alt="<?php echo esc_html__( $settings['title'] ); ?>">
				<h2><?php echo $settings['price']; ?></h2>
				<p><?php echo esc_html( $settings['price_for'] ); ?></p>
				<div class="details"><?php echo $settings['details']; ?></div>
			</div>
			<div class="ot-pricing-btn">
				<a <?php echo $this->get_render_attribute_string( 'button' ); ?>><?php echo esc_html( $settings['label_link'] ); ?> <i class="flaticon-right-arrow-1"></i></a>
			</div>
		</div>

	    <?php
	}

	
}
// After the Schedule class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new Onum_Pricing_Table() );