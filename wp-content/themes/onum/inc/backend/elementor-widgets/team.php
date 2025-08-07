<?php 
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Team
 */
class ONUM_Team extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'imember';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'Onum Team', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-person';
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
				'label' => esc_html__( 'Member Team', 'onum' ),
			]
		);

		$this->add_control(
	       'member_image',
	        [
	           'label' => esc_html__( 'Photo', 'onum' ),
	           'type'  => Controls_Manager::MEDIA,
				'default' => [
					'url' => get_template_directory_uri().'/images/avatar.jpg',
			  	],
		    ]
	    );

	    $this->add_control(
		    'member_name',
	      	[
	          'label' => esc_html__( 'Name', 'onum' ),
	          'type'  => Controls_Manager::TEXT,
	          'default' => esc_html__( 'Jonathan Morgan', 'onum' ),
	    	]
	    );

	    $this->add_control(
		    'member_extra',
	      	[
	          'label' => esc_html__( 'Extra/Job', 'onum' ),
	          'type'  => Controls_Manager::TEXTAREA,
	          'default' => esc_html__( 'WEB Designer', 'onum' ),
	    	]
	    );

		$repeater = new Repeater();
		$repeater->add_control(
	      	'title',
		    [
		        'label'   => esc_html__( 'Name', 'onum' ),
		        'type'    => Controls_Manager::TEXT,
		        'default' => esc_html__( 'Social', 'onum' ),
		    ]
	    );

        $repeater->add_control(
            'social_icon',
            [
                'label' => esc_html__( 'Icon', 'onum' ),
                'type'  => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fab fa-twitter',
					'library' => 'fa-brand',
				],
            ]
        );

        $repeater->add_control(
            'social_link',
            [
                'label' => esc_html__( 'Link', 'onum' ),
                'type'  => Controls_Manager::URL,
				'placeholder' => __( 'https://', 'onum' ),
				'default' => [
					'url' => 'https://', 
				],
            ]
        );

        $repeater->add_control(
			'social_bg',
			[
				'label'     => esc_html__( 'Background', 'onum' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .bg-social' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
		    'social_share',
		    [
		        'label'       => esc_html__( 'Socials', 'onum' ),
		        'type'        => Controls_Manager::REPEATER,
		        'show_label'  => true,
		        'default'     => [
		            [
		             	'title'       => esc_html__( 'Twitter', 'onum' ),
		                'social_link' => esc_html__( 'https://www.twitter.com/', 'onum' ),
		                'social_icon' => [
							'value' => 'fab fa-twitter',
							'library' => 'fa-brand',
						],
		 
		            ],
		            [
		             	'title'       => esc_html__( 'Facebook', 'onum' ),
		                'social_link' => esc_html__( 'https://www.facebook.com/', 'onum' ),
		                'social_icon' => [
							'value' => 'fab fa-facebook-f',
							'library' => 'fa-brand',
						],
		 
		            ],
		            [
		             	'title'       => esc_html__( 'Pinterest', 'onum' ),
		                'social_link' => esc_html__( 'https://www.pinterest.com/', 'onum' ),
		                'social_icon' => [
							'value' => 'fab fa-pinterest-p',
							'library' => 'fa-brand',
						],
		 
		            ]
		        ],
		        'fields'      => $repeater->get_controls(),
		        'title_field' => '{{{title}}}',
		    ]
		);
		$this->add_control(
			'm_link',
			[
				'label' => __( 'Link To Details', 'onum' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://', 'onum' ),
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
					'{{WRAPPER}} .team-wrap .team-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'toggle_color',
			[
				'label'     => esc_html__( 'Toggle Button Color', 'onum' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .team-wrap .team-social span' => 'background: {{VALUE}};',
					'{{WRAPPER}} .team-wrap .active span' => 'color: {{VALUE}}; background: #fff;',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['m_link']['url'] ) ) {
			$this->add_render_attribute( 'link', 'href', $settings['m_link']['url'] );

			if ( $settings['m_link']['is_external'] ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}

			if ( $settings['m_link']['nofollow'] ) {
				$this->add_render_attribute( 'link', 'rel', 'nofollow' );
			}
		}

		?>

		<div class="team-wrap">
			<?php if( $settings['member_image']['url'] ) { ?>
			<div class="team-thumb">
				<?php if ( ! empty( $settings['m_link']['url'] ) ) echo '<a ' .$this->get_render_attribute_string( 'link' ). '>'; ?>
				<img src="<?php echo $settings['member_image']['url']; ?>" alt="<?php echo $settings['member_name'];?>">
				<?php if ( ! empty( $settings['m_link']['url'] ) ) echo '</a>'; ?>
			</div>
			<?php } ?>
			<div class="team-info">
				<h4><?php if ( ! empty( $settings['m_link']['url'] ) ) { echo '<a ' .$this->get_render_attribute_string( 'link' ). '>' . $settings['member_name'] . '</a>'; } else { echo $settings['member_name']; } ?></h4>
				<span><?php echo $settings['member_extra']; ?></span>
				<?php if ( ! empty( $settings['social_share'] ) ) : ?>
                    <div class="team-social">
                        <?php foreach ( $settings['social_share'] as $social ) : ?>
                            <?php if ( ! empty( $social['social_link'] ) ) : ?>
                                <a style="background: <?php echo $social['social_bg']; ?>" <?php if($social['social_link']['is_external'])
                                { echo 'target="_blank"'; }else{ echo 'rel="nofollow"';}?> 
                                        href="<?php echo $social['social_link']['url'];?>" class="<?php echo strtolower($social['title']);?>">
                                     <i class="<?php echo esc_attr( $social['social_icon']['value']); ?>"></i>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    	<span class="flaticon-add-1"></span>
                    </div>  
                <?php endif; ?>
			</div>
		</div>
	        
	    <?php
	}

	
}
// After the Schedule class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new ONUM_Team() );