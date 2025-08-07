<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Testimonial Slider 2
 */
class Onum_Testimonials2 extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'itestimonials2';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'Onum Testimonial Slider 2', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	// The get_categories method, lets you set the category of the widget, return the category name as a string.
	public function get_categories() {
		return [ 'category_onum' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_testimonials',
			[
				'label' => __( 'Testimonials', 'onum' ),
			]
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'tcontent',
			[
				'label' => __( 'Content', 'onum' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => '10',
				'default' => '"I am so happy, my dear friend, so absorbed in the exquisite sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at the present moment."',
			]
		);

		$repeater->add_control(
			'timage',
			[
				'label' => __( 'Avatar', 'onum' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => get_template_directory_uri().'/images/testi1.png',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Name', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Emilia Clarke',
			]
		);

		$repeater->add_control(
			'tjob',
			[
				'label' => __( 'Job', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Developer',
			]
		);

		$this->add_control(
		    'testi_slider',
		    [
		        'label'       => '',
		        'type'        => Controls_Manager::REPEATER,
		        'show_label'  => false,
		        'default'     => [
		            [
		             	'tcontent' => __( '"I am so happy, my dear friend, so absorbed in the exquisite sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at the present moment."', 'onum' ),
		                'timage'  => [
							'url' => get_template_directory_uri().'/images/testi1.png',
						],
						'title'	  => 'Emilia Clarke',
						'tjob'	  => 'Developer'
		 
		            ],
		            [
		             	'tcontent' => __( '"I am so happy, my dear friend, so absorbed in the exquisite sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at the present moment."', 'onum' ),
		                'timage'  => [
							'url' => get_template_directory_uri().'/images/testi1.png',
						],
						'title'	  => 'Emilia Clarke',
						'tjob'	  => 'Developer'
		 
		            ],
		            [
		             	'tcontent' => __( '"I am so happy, my dear friend, so absorbed in the exquisite sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at the present moment."', 'onum' ),
		                'timage'  => [
							'url' => get_template_directory_uri().'/images/testi1.png',
						],
						'title'	  => 'Emilia Clarke',
						'tjob'	  => 'Developer'
		 
		            ]
		            
		        ],
		        'fields'      => $repeater->get_controls(),
		        'title_field' => '{{{title}}}',
		    ]
		);
		$this->add_control(
			'tarrow',
			[
				'label' => __( 'Nav Slider', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'true',
				'options' => [
					'true' => __( 'Yes', 'onum' ),
					'false' => __( 'No', 'onum' ),
				]
			]
		);
		$this->add_control(
			'autoplay',
			[
				'label' 	=> __( 'Autoplay', 'onum' ),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'false',
				'options' 	=> [
					'true' 	=> __( 'Yes', 'onum' ),
					'false' => __( 'No', 'onum' ),
				]
			]
		);
		$this->add_control(
			'autoplaySpeed',
			[
				'label' 	=> __( 'Autoplay Speed', 'onum' ),
				'type' 		=> Controls_Manager::NUMBER,
				'min' 		=> 1000,
				'max' 		=> 10000,
				'step' 		=> 100,
				'default' 	=> 2000,
				'condition' => [
					'autoplay' => 'true',
				]
			]
		);
		$this->add_control(
			'fade',
			[
				'label' 	=> __( 'Fade Effect', 'onum' ),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'false',
				'options' 	=> [
					'true' 	=> __( 'Yes', 'onum' ),
					'false' => __( 'No', 'onum' ),
				]
			]
		);
		$this->add_control(
			'text_light',
			[
				'label' => __( 'Text light', 'onum' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'onum' ),
				'label_off' => __( 'No', 'onum' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->end_controls_section();

		// Styling.
		$this->start_controls_section(
			'style_tcontent',
			[
				'label' => __( 'Content Box', 'onum' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);		
		$this->add_control(
			'tcontent_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-testimonials-carousel .testimonial-item .ttext' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .ot-testimonials-carousel .testimonial-item .ttext',
			]
		);

		$this->add_responsive_control(
			'tcontent_padding',
			[
				'label' => __( 'Padding', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ot-testimonials-carousel .testimonial-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Image.
		$this->start_controls_section(
			'style_timage',
			[
				'label' => __( 'Avatars', 'onum' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'spacing_img',
			[
				'label' => __( 'Spacing', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ot-testimonials-thumbnails' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_size',
			[
				'label' => __( 'Size', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ot-testimonials-thumbnails .testimonials-avatar img' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ot-testimonials-thumbnails' => 'width: calc({{SIZE}}{{UNIT}} * 3);',
				],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ot-testimonials-thumbnails .testimonials-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Name.
		$this->start_controls_section(
			'style_tname',
			[
				'label' => __( 'Name', 'onum' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-testimonials-carousel .testimonial-inner .tname' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}} .ot-testimonials-carousel .testimonial-inner .tname',
			]
		);

		$this->add_control(
			'spacing_name',
			[
				'label' => __( 'Spacing', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ot-testimonials-carousel .testimonial-inner .tname' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Job.
		$this->start_controls_section(
			'style_tjob',
			[
				'label' => __( 'Job', 'onum' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'job_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-testimonials-carousel .testimonial-inner .tjob' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'job_typography',
				'selector' => '{{WRAPPER}} .ot-testimonials-carousel .testimonial-inner .tjob',
			]
		);		

		$this->end_controls_section();

		// Arrow.
		$this->start_controls_section(
			'style_nav',
			[
				'label' => __( 'Arrow', 'onum' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'tarrow' => 'true',
				]
			]
		);	
		$this->add_control(
			'size_nav',
			[
				'label' => __( 'Size', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 40,
						'max' => 100,
					],
				],
				'selectors' => [					
					'{{WRAPPER}} .slick-arrow' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
				]
			]
		);	
		$this->add_control(
			'spacing_nav',
			[
				'label' => __( 'Spacing', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
					'%' => [
						'min' => -100,
						'max' => 200,
					],
				],
				'selectors' => [					
					'{{WRAPPER}} .ot-testimonials-nav .prev-nav' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ot-testimonials-nav .next-nav' => 'right: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->start_controls_tabs( 'tabs_style_arrow' );
		$this->start_controls_tab(
			'tab_arrow_normal',
			[
				'label' => __( 'Normal', 'onum' ),
			]
		);
		$this->add_control(
			'arrow_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .slick-arrow' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'arrow_bgcolor',
			[
				'label' => __( 'Background', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .slick-arrow' => 'background: {{VALUE}};',
				],
			]
		);	
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'arrow_shadow',
				'label' => __( 'Box Shadow', 'onum' ),
				'selector' => '{{WRAPPER}} .slick-arrow',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrow_hover',
			[
				'label' => __( 'Hover', 'onum' ),
			]
		);
		$this->add_control(
			'arrow_hcolor',
			[
				'label' => __( 'Color Hover', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .slick-arrow:hover' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'arrow_hbgcolor',
			[
				'label' => __( 'Background Hover', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .slick-arrow:hover' => 'background: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'arrow_hshadow',
				'label' => __( 'Box Shadow Hover', 'onum' ),
				'selector' => '{{WRAPPER}} .slick-arrow:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="ot-testimonials-carousel-wrap" data-arrow="<?php echo $settings['tarrow']; ?>" data-autoplay="<?php echo $settings['autoplay']; ?>" data-autoplaySpeed="<?php echo $settings['autoplaySpeed']; ?>" data-fade="<?php echo $settings['fade']; ?>">	
			<!-- THUMBNAILS -->
			<div class="ot-testimonials-thumbnails" <?php if ( is_rtl() ) { echo'dir="rtl"'; } ?>>
				<?php if ( ! empty( $settings['testi_slider'] ) ) : foreach ( $settings['testi_slider'] as $testi_thumb ) : ?>
					<?php if( $testi_thumb['timage']['url'] ) { ?>
					<div class="testimonials-avatar">
			        	<img src="<?php echo $testi_thumb['timage']['url']; ?>" alt="<?php echo $testi_thumb['title']; ?>">			        	
					</div>
					<?php } ?>
				<?php endforeach; endif; ?>
			</div>

			<!-- MAIN SLIDES -->					
			<div class="ot-testimonials-carousel" <?php if ( is_rtl() ) { echo'dir="rtl"'; } ?>>
				<?php if ( ! empty( $settings['testi_slider'] ) ) : foreach ( $settings['testi_slider'] as $testi_content ) : ?>
				<div class="testimonial-item">	
					<div class="testimonial-inner <?php if( $settings['text_light'] === 'yes' ){ echo 'text-light'; } ?>">				
				        <div class="ttext"><?php echo $testi_content['tcontent']; ?></div>
				        <div class="tinfo font-second">
			        		<?php if( $testi_content['title'] ) { ?><h6 class="tname"><?php echo $testi_content['title']; ?></h6><?php } ?>
			        		<?php if( $testi_content['tjob'] ) { ?><span class="tjob"><?php echo $testi_content['tjob']; ?></span><?php } ?>
			        	</div>
		        	</div>
				</div>
				<?php endforeach; endif; ?>
			</div>	
			<?php if ( 'true' === $settings['tarrow'] ) {echo '<div class="ot-testimonials-nav"></div>';} ?>				
	    </div>
	    <?php
	}

	
}
// After the Schedule class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new Onum_Testimonials2() );