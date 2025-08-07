<?php 
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: News Slider
 */
class Onum_Post_Carousel extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'ipost_carousel';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'Onum Post Carousel', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-posts-carousel';
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
				'label' => __( 'Posts Settings', 'onum' ),
			]
		);

		$this->add_control(
			'post_cat',
			[
				'label' => __( 'Select Categories', 'onum' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->select_param_cate_post(),
				'multiple' => true,
				'label_block' => true,
				'placeholder' => __( 'All Categories', 'onum' ),
			]
		);
		$this->add_control(
			'thumbnail_size',
			[
				'label' => __( 'Post Image Size', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'onum-blog-grid-post-thumbnail',
				'options' => [					
					'onum-blog-grid-post-thumbnail' => __( 'Default - 750 x 500', 'onum' ),
					'medium_large' 					=> __( 'Medimum Large 768x0', 'onum' ),
					'large' 						=> __( 'Large 1024x1024', 'onum' ),
					'full' 							=> __( 'Full', 'onum' ),
					'custom' 						=> __( 'Custom', 'onum' ),
				],
			]
		);
		$this->add_control(
			'thumbnail_dimension',
			[
				'label' => __( 'Image Dimension', 'onum' ),
				'type'  => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => __( 'Crop the original image size to any custom size. Set custom width or height to keep the original size ratio.', 'onum' ),
				'default' => [
					'width' => '',
					'height' => '',
				],
				'condition' => [
					'thumbnail_size' => 'custom',
				]
			]
		);
		$this->add_control(
			'number_show',
			[
				'label' => __( 'Show Number Posts', 'onum' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '9',
			]
		);	
		$this->add_control(
            'show_excerpt',
            [
                'label' => __('Show Excerpt?', 'onum'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'onum'),
                'label_off' => __('Hidden', 'onum'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
		$this->add_control(
			'exc',
			[
				'label' => esc_html__( 'Number Excerpt Length', 'onum' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '15',
				'condition' => [
					'show_excerpt' => 'yes',
				]
			]
		);		
		$this->add_control(
            'show_cate',
            [
                'label' => __('Show Category', 'onum'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'onum'),
                'label_off' => __('Hidden', 'onum'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'display_meta',
            [
                'label' => __('Show Entry Meta (Author/Date)', 'onum'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'onum'),
                'label_off' => __('Hidden', 'onum'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'show_btn',
            [
                'label' => __('Show read-more button', 'onum'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'onum'),
                'label_off' => __('Hidden', 'onum'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->add_control(
			'detail_btn',
			[
				'label' => 'Button',
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'View details <i class="flaticon-arrow-pointing-to-right"></i>', 'onum' ),
				'condition' => [
					'show_btn' => 'yes',
				]
			]
		);
		$this->add_control(
			'heading_slider',
			[
				'label' => __( 'Slider', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
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

		$this->add_control(
			'gaps',
			[
				'label' => __( 'Show Gap', 'onum' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'onum' ),
				'label_off' => __( 'Hide', 'onum' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_responsive_control(
			'w_gaps',
			[
				'label' => __( 'Gap Width', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .blog-item' => 'padding: 0 calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .onum-blog-slider' => 'margin: 0 calc(-{{SIZE}}{{UNIT}}/2);',
				],
				'condition' => [
					'gaps' => 'yes',
				]
			]
		);
		$this->end_controls_section();

		/*Style*/
		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Inner post', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'info_padd',
			[
				'label' => __( 'Padding', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .post-box .inner-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'info_bg',
			[
				'label' => __( 'Backgroung Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .post-box .post-inner' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .post-box .post-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} .post-box .post-inner',
			]
		);

		$this->end_controls_section();

		//Entry Meta
		$this->start_controls_section(
			'entry_meta_section',
			[
				'label' => __( 'Entry Meta', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display_meta' => 'yes',
				]
			]
		);
		$this->add_responsive_control(
			'entry_meta_spacing',
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
					'{{WRAPPER}} .post-box .inner-post .entry-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$this->add_control(
			'entry_meta_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .post-box .inner-post .entry-meta a, {{WRAPPER}} .post-box .inner-post .entry-meta > span:not(:last-child):after' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'entry_meta_color_hover',
			[
				'label' => __( 'Color Hover', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .post-box .inner-post .entry-meta a:hover' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'entry_meta_typography',
				'selector' => '{{WRAPPER}} .post-box .inner-post .entry-meta'
			]
		);

		$this->end_controls_section();

		//Title
		$this->start_controls_section(
			'title_section',
			[
				'label' => __( 'Title', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);
		$this->add_responsive_control(
			'title_spacing',
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
					'{{WRAPPER}} .post-box .inner-post h3' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .post-box .inner-post h3 a' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'title_hcolor',
			[
				'label' => __( 'Hover Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .post-box .inner-post h3 a:hover' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .post-box .inner-post h3',
			]
		);

		$this->end_controls_section();

		//Excerpt
		$this->start_controls_section(
			'excerpt_section',
			[
				'label' => __( 'Excerpt', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_excerpt' => 'yes',
				]
			]
		);
		$this->add_responsive_control(
			'exc_spacing',
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
					'{{WRAPPER}} .entry-summary' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$this->add_control(
			'exc_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .entry-summary' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'exc_typography',
				'selector' => '{{WRAPPER}} .entry-summary'
			]
		);

		$this->end_controls_section();

		//Button Read More
		$this->start_controls_section(
			'btn_section',
			[
				'label' => __( 'Button Read More', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_btn' => 'yes',
				]
			]
		);
		$this->add_responsive_control(
			'btn_readmore_spacing',
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
					'{{WRAPPER}} .btn-readmore' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$this->add_control(
			'btn_readmore_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .btn-readmore a' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'btn_readmore_color_hover',
			[
				'label' => __( 'Text Color Hover', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .btn-readmore a:hover' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'btn_readmore_typography',
				'selector' => '{{WRAPPER}} .btn-readmore a'
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
				'condition' => [
					'tdots' => 'true',
				]
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
		$width    = ( ! empty ( $settings['thumbnail_dimension']['width'] ) ? $settings['thumbnail_dimension']['width'] : 0 );
		$height   = ( ! empty( $settings['thumbnail_dimension']['height'] ) ? $settings['thumbnail_dimension']['height'] : 0 );
		$thumbnail_size = $settings['thumbnail_size'];
		?>

		<div class="onum-blog-slider" <?php if ( is_rtl() ) { echo'dir="rtl"'; } ?> data-show="<?php echo $settings['tshow']; ?>" data-scroll="<?php echo $settings['scroll']; ?>" data-arrow="<?php echo $settings['tarrow']; ?>" data-dots="<?php echo $settings['tdots']; ?>">
        <?php
        	$number_show = ( !empty($settings['number_show'] ) ? $settings['number_show'] : 9);
        	$exc = ( !empty( $settings['exc'] ) ? $settings['exc'] : 15 );

        	if( $settings['post_cat'] ){
                $args = array(
		            'post_type' => 'post',
		            'post_status' => 'publish',
		            'posts_per_page' => $number_show,
		            'tax_query' => array(
				        array(
				            'taxonomy' => 'category',
				            'field'    => 'slug',
				            'terms'    => $settings['post_cat']
				        ),
				    ),
		        );
            }else{
                $args = array(
                    'post_type' => 'post',
		            'post_status' => 'publish',
		            'posts_per_page' => $number_show,
                );
            }

	        $blogpost = new \WP_Query($args);
	        if($blogpost->have_posts()) : while($blogpost->have_posts()) : $blogpost->the_post();
	            $format = get_post_format();
				$link_video  = get_post_meta(get_the_ID(),'post_video', true);
				$link_audio  = get_post_meta(get_the_ID(),'post_audio', true);
				$link_link   = get_post_meta(get_the_ID(),'post_link', true);
				$text_link   = get_post_meta(get_the_ID(),'text_link', true);
				$quote_text  = get_post_meta(get_the_ID(),'post_quote', true);
				$quote_name  = get_post_meta(get_the_ID(),'quote_name', true);
			?> 
				<article id="post-<?php the_ID(); ?>" <?php post_class('post-box blog-item'); ?>>
					<div class="post-inner">
					    <?php if( $format == 'gallery' ) { ?>
							<div class="entry-media">
								<?php if( 'yes' === $settings['show_cate'] ){ onum_posted_in(); } ?>
								<div class="gallery-post" <?php if ( is_rtl() ) { echo'dir="rtl"'; } ?>>
								<?php if( function_exists( 'rwmb_meta' ) ) { ?>
						            <?php 
						            if ( $settings['thumbnail_size'] != 'custom' ) {
										$images = rwmb_meta( 'post_gallery', array( 'size' => $thumbnail_size ) ); 
									} else {
										$images = rwmb_meta( 'post_gallery', array( 'size' => array( $width, $height ) ) ); 
									}	
						            ?>
						            <?php if($images){ ?>              
						                <?php foreach ( $images as $image ) {  ?>		                    
						                    <div>
						                    	<div class="item-image">
							                    	<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo esc_attr( $image['width'] ); ?>" height="<?php echo esc_attr( $image['height'] ); ?>">
							                    </div>
						                    </div>                    
						                <?php } ?>                
						            <?php } ?>
						        <?php } ?>
						        </div>
							</div>			
					    <?php }elseif( $format == 'image' ) { ?>
					    	<div class="entry-media">
								<?php if( 'yes' === $settings['show_cate'] ){ onum_posted_in(); } ?>
								<?php if( function_exists( 'rwmb_meta' ) ) { ?>
								    <?php 
						            if ( $settings['thumbnail_size'] != 'custom' ) {
										$images = rwmb_meta( 'post_image', array( 'size' => $thumbnail_size ) ); 
									} else {
										$images = rwmb_meta( 'post_image', array( 'size' => array( $width, $height ) ) ); 
									}	
						            ?>
								    <?php if($images){ ?>              
								        <?php foreach ( $images as $image ) {  ?>				            
								            <a href="<?php the_permalink(); ?>">
								            	<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo esc_attr( $image['width'] ); ?>" height="<?php echo esc_attr( $image['height'] ); ?>">
								            </a>
								        <?php } ?>                
								    <?php } ?>
								<?php } ?>
							</div>						
					    <?php }elseif( $format == 'audio' ){ ?>
							<div class="audio-box padding-box">
								<iframe scrolling="no" frameborder="no" src="<?php echo esc_url( $link_audio ); ?>"></iframe>
							</div>
					    <?php }elseif( $format == 'video' ){ ?>
							<div class="entry-media">
								<a class="btn-play" href="<?php echo esc_url( $link_video ); ?>">
									<i class="fas fa-play"></i>
								</a>
								<?php if( 'yes' === $settings['show_cate'] ){ onum_posted_in(); } ?>
								<?php if( function_exists( 'rwmb_meta' ) ) { ?>
								    <?php 
						            if ( $settings['thumbnail_size'] != 'custom' ) {
										$images = rwmb_meta( 'bg_video', array( 'size' => $thumbnail_size ) ); 
									} else {
										$images = rwmb_meta( 'bg_video', array( 'size' => array( $width, $height ) ) ); 
									}	
						            ?>
								    <?php if($images){ ?>              
								        <?php  foreach ( $images as $image ) {  ?>
								            <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo esc_attr( $image['width'] ); ?>" height="<?php echo esc_attr( $image['height'] ); ?>">
								        <?php } ?>                
								    <?php } ?>
								<?php } ?>
							</div>
					    <?php }elseif( $format == 'link' ){ ?>
							<div class="link-box padding-box">
								<i class="flaticon-link"></i>
								<a href="<?php echo esc_url( $link_link ); ?>"><?php echo esc_html( $text_link ); ?></a>
							</div>
					    <?php }elseif( $format == 'quote' ){ ?>
							<div class="quote-box padding-box font-second">
								<i class="flaticon-quotation"></i>
								<div class="quote-text">
									<?php echo esc_html( $quote_text ); ?>
									<span><?php echo esc_html( $quote_name ); ?></span>
								</div>
							</div>
					    <?php }elseif ( has_post_thumbnail() ) { ?>
					        <div class="entry-media">
					        	<?php if( 'yes' === $settings['show_cate'] ){ onum_posted_in(); } ?>
					            <a href="<?php the_permalink(); ?>">
			                		<?php 
			                		if ( $settings['thumbnail_size'] != 'custom' ) {
										the_post_thumbnail( $thumbnail_size );
									} else {
										the_post_thumbnail( array( $width, $height ) );
									}	 
									?>
					            </a>
					        </div>					       
					    <?php } ?>
					    <div class="inner-post">
					    	<?php 
						    	if( $format != 'gallery' && $format != 'image' && $format != 'video' ) {
						    		if( 'yes' === $settings['show_cate'] ){ onum_posted_in(); }
						    	} 
					    	?>
					        <div class="entry-header">
					        	<?php if( 'yes' === $settings['display_meta'] ){ ?>
						            <div class="entry-meta">
						                <?php onum_post_meta(); ?>
						            </div><!-- .entry-meta -->
					            <?php } ?>
					            <?php the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
					        </div><!-- .entry-header -->

					        <?php if ( 'yes' === $settings['show_excerpt'] ) { ?>
					        <div class="entry-summary the-excerpt">
					            <?php echo onum_excerpt( $exc ); ?>
					        </div><!-- .entry-content -->
					        <?php } ?>

					        <?php if ( 'yes' === $settings['show_btn'] ) { ?><div class="btn-readmore"><a href="<?php the_permalink(); ?>"><?php echo $settings['detail_btn']; ?></a></div><?php } ?>
					    </div>
					</div>
				</article>
	        <?php endwhile; wp_reset_postdata(); endif; ?>
	    </div>
		<?php
	}

	

	protected function select_param_cate_post() {
		$args = array( 'orderby=name&order=ASC&hide_empty=0' );
		$terms = get_terms( 'category', $args );
		$cat = array();
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){		    
		    foreach ( $terms as $term ) {
		        $cat[$term->slug] = $term->name;
		    }
		}
	  	return $cat;
	}
}
// After the Onum_Post_Carousel class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new Onum_Post_Carousel() );