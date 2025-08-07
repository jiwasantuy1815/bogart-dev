<?php 
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Team
 */
class ONUM_Team_Carousel extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'iteams';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'Onum Team Carousel', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-carousel';
	}

	// The get_categories method, lets you set the category of the widget, return the category name as a string.
	public function get_categories() {
		return [ 'category_onum' ];
	}

	protected function register_controls() {

		/**TAB_CONTENT**/
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Team', 'onum' ),
			]
		);

		$repeater = new Repeater();
		$repeater->add_control(
	       'member_image',
	        [
	           'label' => esc_html__( 'Photo', 'onum' ),
	           'type'  => Controls_Manager::MEDIA,
				'default' => [
					'url' => get_template_directory_uri().'/images/avatar.jpg',
			  	],
		    ]
	    );

	    $repeater->add_control(
		    'member_name',
	      	[
	          'label' => esc_html__( 'Name', 'onum' ),
	          'type'  => Controls_Manager::TEXT,
	          'default' => esc_html__( 'Gina Bruno', 'onum' ),
	    	]
	    );

	    $repeater->add_control(
		    'member_extra',
	      	[
	          'label' => esc_html__( 'Extra/Job', 'onum' ),
	          'type'  => Controls_Manager::TEXTAREA,
	          'default' => esc_html__( 'CEO of Company', 'onum' ),
	    	]
	    );

	    $repeater->add_control(
			'member_link',
			[
				'label' => __( 'Link To Details', 'onum' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://', 'onum' ),
			]
		);

	    $repeater->add_control(
			'socials',
			[
				'label' => __( 'Socials', 'onum' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'onum' ),
				'label_off' => __( 'Hide', 'onum' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
			]
		);

	    $repeater->add_control(
		    'social1',
	      	[
	          	'label' => esc_html__( 'Icon Social 1', 'onum' ),
                'type'  => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fab fa-twitter',
					'library' => 'fa-brand',
				],
				'condition' => [
					'socials' => 'yes',
				],
	    	]
	    );
	    $repeater->add_control(
			'social1_link',
			[
				'label' => __( 'Link Social 1', 'onum' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://twitter.com/', 'onum' ),
				'condition' => [
					'socials' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'social1_bg',
			[
				'label'     => esc_html__( 'Background Social 1', 'onum' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bg-social' => 'background: {{VALUE}};',
				],
				'condition' => [
					'socials' => 'yes',
				],
			]
		);

		$repeater->add_control(
		    'social2',
	      	[
	          	'label' => esc_html__( 'Icon Social 2', 'onum' ),
                'type'  => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fab fa-facebook-f',
					'library' => 'fa-brand',
				],
				'separator' => 'before',
				'condition' => [
					'socials' => 'yes',
				],
	    	]
	    );
	    $repeater->add_control(
			'social2_link',
			[
				'label' => __( 'Link Social 2', 'onum' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://facebook.com/', 'onum' ),
				'condition' => [
					'socials' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'social2_bg',
			[
				'label'     => esc_html__( 'Background Social 2', 'onum' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bg-social' => 'background: {{VALUE}};',
				],
				'condition' => [
					'socials' => 'yes',
				],
			]
		);

		$repeater->add_control(
		    'social3',
	      	[
	          	'label' => esc_html__( 'Icon Social 3', 'onum' ),
                'type'  => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fab fa-pinterest-p',
					'library' => 'fa-brand',
				],
				'separator' => 'before',
				'condition' => [
					'socials' => 'yes',
				],
	    	]
	    );
	    $repeater->add_control(
			'social3_link',
			[
				'label' => __( 'Link Social 3', 'onum' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://pinterest.com/', 'onum' ),
				'condition' => [
					'socials' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'social3_bg',
			[
				'label'     => esc_html__( 'Background Social 3', 'onum' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bg-social' => 'background: {{VALUE}};',
				],
				'condition' => [
					'socials' => 'yes',
				],
			]
		);


		$this->add_control(
		    'members',
		    [
		        'label'       => esc_html__( 'Team', 'onum' ),
		        'type'        => Controls_Manager::REPEATER,
		        'show_label'  => false,
		        'default'     => [
		            [
		             	'member_name' => esc_html__( 'Gina Bruno', 'onum' ),
		                'member_extra' => esc_html__( 'CEO of Company', 'onum' ),		 
		            ],
		            [
		             	'member_name' => esc_html__( 'Gina Bruno', 'onum' ),
		                'member_extra' => esc_html__( 'CEO of Company', 'onum' ),		 
		            ],
		            [
		             	'member_name' => esc_html__( 'Gina Bruno', 'onum' ),
		                'member_extra' => esc_html__( 'CEO of Company', 'onum' ),		 
		            ],
		            [
		             	'member_name' => esc_html__( 'Gina Bruno', 'onum' ),
		                'member_extra' => esc_html__( 'CEO of Company', 'onum' ),		 
		            ],
		        ],
		        'fields'      => $repeater->get_controls(),
		        'title_field' => '{{{member_name}}}',
		    ]
		);

		$this->add_control(
			'tshow',
			[
				'label' => __( 'Slides to Show', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'2' => __( '2', 'onum' ),
					'3' => __( '3', 'onum' ),
					'4' => __( '4', 'onum' ),
					'5' => __( '5', 'onum' ),
				]
			]
		);
		$this->add_control(
			'scroll',
			[
				'label' => __( 'Slides to Scroll', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'1' => __( '1', 'onum' ),
					'2' => __( '2', 'onum' ),
					'3' => __( '3', 'onum' ),
					'4' => __( '4', 'onum' ),
					'5' => __( '5', 'onum' ),
				]
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
			'tdots',
			[
				'label' => __( 'Dots Slider', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'false',
				'options' => [
					'true' => __( 'Yes', 'onum' ),
					'false' => __( 'No', 'onum' ),
				]
			]
		);

		$this->end_controls_section();

		/**TAB_STYLE**/
		$this->start_controls_section(
			'image_style',
			[
				'label' => esc_html__( 'Photo', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'member_image[url]!' => '',
				]
			]
		);
		$this->add_responsive_control(
			'image_radius',
			[
				'label' => esc_html__( 'Border Radius', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_style',
			[
				'label' => esc_html__( 'Infomation', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'info_bg',
			[
				'label'     => esc_html__( 'Background', 'onum' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-info' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'info_space',
			[
				'label' => esc_html__( 'Spacing', 'onum' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -150,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-info' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'info_padding',
			[
				'label' => esc_html__( 'Padding', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'info_radius',
			[
				'label' => esc_html__( 'Border Radius', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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

		$this->add_responsive_control(
			'title_space',
			[
				'label' => esc_html__( 'Spacing(px)', 'onum' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-info h4' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'onum' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-info h4' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'title_typography',
					'label'    => esc_html__( 'Typography', 'onum' ),
					'selector' => '{{WRAPPER}} .team-wrap .team-info h4',
				]
		);

		//Extra
		$this->add_control(
			'heading_job',
			[
				'label' => __( 'Extra/Job', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'job_color',
			[
				'label'     => esc_html__( 'Color', 'onum' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-info > span' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'job_typography',
					'label'    => esc_html__( 'Typography', 'onum' ),
					'selector' => '{{WRAPPER}} .team-wrap .team-info > span',
				]
		);

		$this->end_controls_section();

		//Socials
		$this->start_controls_section(
			'icon_style',
			[
				'label' => esc_html__( 'Social Icon', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => esc_html__( 'Font Size', 'onum' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 40,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-social a' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label' => esc_html__( 'Spacing', 'onum' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-social a' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Color', 'onum' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-social a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'onum' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-social a:hover' => 'color: {{VALUE}};',
				],
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
		$this->add_responsive_control(
			'spacing_nav',
			[
				'label' => __( 'Spacing', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .prev-nav' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .next-nav' => 'right: {{SIZE}}{{UNIT}};',
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

		// Dots.
		$this->start_controls_section(
			'style_dots',
			[
				'label' => __( 'Dots', 'onum' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'spacing_dots',
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
					'{{WRAPPER}} .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'dots_bgcolor',
			[
				'label' => __( 'Background', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'dots_active_bgcolor',
			[
				'label' => __( 'Background Active', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .slick-dots li.slick-active button:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>

		<div class="team-slider" <?php if ( is_rtl() ) { echo'dir="rtl"'; } ?> data-show="<?php echo $settings['tshow']; ?>" data-scroll="<?php echo $settings['scroll']; ?>" data-arrow="<?php echo $settings['tarrow']; ?>" data-dots="<?php echo $settings['tdots']; ?>">
			<?php 
				foreach ( $settings['members'] as $index => $mem ) : 
				
				$link_detail = '';
				if ( ! empty( $mem['member_link']['url'] ) ) {
					$link_key = 'link_' . $index;
					$this->add_render_attribute( $link_key, 'href', $mem['member_link']['url'] );

					if ( $mem['member_link']['is_external'] ) {
						$this->add_render_attribute( $link_key, 'target', '_blank' );
					}

					if ( $mem['member_link']['nofollow'] ) {
						$this->add_render_attribute( $link_key, 'rel', 'nofollow' );
					}
					$link_detail = '<a ' . $this->get_render_attribute_string( $link_key ) . '>';
				}	
			?>

			<div class="team-wrap">
				<?php if ( $mem['member_image']['url'] ) { ?>
				<div class="team-thumb">
					<?php if ( ! empty( $mem['member_link']['url'] ) ) echo $link_detail; ?>
					<img src="<?php echo $mem['member_image']['url']; ?>" alt="<?php echo $mem['member_name'];?>">
					<?php if ( ! empty( $mem['member_link']['url'] ) ) echo '</a>'; ?>
				</div>
				<?php } ?>
				<div class="team-info">
					<h4><?php if ( ! empty( $mem['member_link']['url'] ) ) { echo $link_detail . $mem['member_name'] . '</a>'; } else { echo $mem['member_name']; } ?></h4>
					<span><?php echo $mem['member_extra']; ?></span>
					<?php if ( $mem['socials'] ) : ?>
	                <div class="team-social">
                        <a style="background: <?php echo $mem['social1_bg']; ?>" <?php if($mem['social1_link']['is_external'])
                        { echo 'target="_blank"'; }else{ echo 'rel="nofollow"';}?> 
                                href="<?php echo $mem['social1_link']['url'];?>">
                             <i class="<?php echo esc_attr( $mem['social1']['value']); ?>"></i>
                        </a>
                        <?php if ( ! empty( $mem['social2'] ) ) : ?>
                        <a style="background: <?php echo $mem['social2_bg']; ?>" <?php if($mem['social2_link']['is_external'])
                        { echo 'target="_blank"'; }else{ echo 'rel="nofollow"';}?> 
                                href="<?php echo $mem['social2_link']['url'];?>">
                             <i class="<?php echo esc_attr( $mem['social2']['value']); ?>"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ( ! empty( $mem['social3'] ) ) : ?>
                        <a style="background: <?php echo $mem['social3_bg']; ?>" <?php if($mem['social3_link']['is_external'])
                        { echo 'target="_blank"'; }else{ echo 'rel="nofollow"';}?> 
                                href="<?php echo $mem['social3_link']['url'];?>">
                             <i class="<?php echo esc_attr( $mem['social3']['value']); ?>"></i>
                        </a>
                        <?php endif; ?>
	                	<span class="flaticon-add-1"></span>
	                </div>
	                <?php endif; ?>
				</div>
			</div>
	        <?php endforeach; ?>
		</div>
	        
	    <?php
	}

	
}
// After the Schedule class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new ONUM_Team_Carousel() );