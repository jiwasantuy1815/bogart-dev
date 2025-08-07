<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Portfolio Filter
 */
class Onum_PortfolioFilter extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'ipfilter';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'Onum Portfolio Filter', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	// The get_categories method, lets you set the category of the widget, return the category name as a string.
	public function get_categories() {
		return [ 'category_onum' ];
	}

	public static function get_onum_heading_html_tag() {
		return [
			'h1'  => __( 'H1', 'onum' ),
			'h2'  => __( 'H2', 'onum' ),
			'h3'  => __( 'H3', 'onum' ),
			'h4'  => __( 'H4', 'onum' ),
			'h5'  => __( 'H5', 'onum' ),
			'h6'  => __( 'H6', 'onum' ),
			'div'  => __( 'div', 'onum' ),
			'span'  => __( 'span', 'onum' ),
			'p'  => __( 'p', 'onum' ),
		];
	}

	protected function register_controls() {

		//Content
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'General', 'onum' ),
			]
		);
		$this->add_control(
			'project_cat',
			[
				'label' => __( 'Select Categories', 'onum' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->select_param_cate_project(),
				'multiple' => true,
				'label_block' => true,
				'placeholder' => __( 'All Categories', 'onum' ),
			]
		);
		$this->add_control(
			'filter',
			[
				'label' => __( 'Show Filter', 'onum' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'onum' ),
				'label_off' => __( 'Hide', 'onum' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'all_text',
			[
				'label' => __( 'All Text', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'All',
				'condition' => [
					'filter' => 'yes',
				],
			]
		);
		$this->add_control(
			'column',
			[
				'label' => __( 'Columns', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'2'  	=> __( '2 Column', 'onum' ),
					'3' 	=> __( '3 Column', 'onum' ),
					'4' 	=> __( '4 Column', 'onum' ),
					'5' 	=> __( '5 Column', 'onum' ),
				],
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
				'selectors' => [
					'{{WRAPPER}} .project-item' => 'padding: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .projects-grid' => 'margin: calc(-{{SIZE}}{{UNIT}}/2);',
				],
				'condition' => [
					'gaps' => 'yes',
				]
			]
		);
		$this->add_control(
			'project_num',
			[
				'label' => __( 'Show Number Projects', 'onum' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '9',
			]
		);		
		$this->add_control(
			'layout',
			[
				'label' => __( 'Portfolio Style', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style1',
				'options' => [
					'style1'  	=> __( 'Style 1', 'onum' ),
					'style2' 	=> __( 'Style 2', 'onum' ),
					'style3' 	=> __( 'Style 3', 'onum' ),
				],
			]
		);
		$this->add_control(
			'overlay_style',
			[
				'label' => __( 'Overlay Style', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => 's1',
				'options' => [
					's1'  	=> __( 'Style 1', 'onum' ),
					's2' 	=> __( 'Style 2', 'onum' ),
				],
				'condition' => [
					'layout' => 'style1',
				]
			]
		);
		$this->add_control(
			'thumbnail_size',
			[
				'label' => __( 'Image Size', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'onum-portfolio-thumbnail-left-top',
				'options' => [					
					'onum-portfolio-thumbnail-left-top' => __( 'Default - 760 x 760', 'onum' ),
					'medium_large' 						=> __( 'Medium Large - 768 x 0', 'onum' ),
					'large' 							=> __( 'Large - 1024 x 1024', 'onum' ),
					'full' 								=> __( 'Full', 'onum' ),
					'custom' 							=> __( 'Custom', 'onum' ),
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
		$this->add_responsive_control(
			'box_radius',
			[
				'label' => __( 'Border Radius', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .projects-box .projects-thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'filter_style_section',
			[
				'label' => __( 'Filter', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'filter' => 'yes',
				]
			]
		);
		$this->add_responsive_control(
			'filter_align',
			[
				'label' => __( 'Alignment', 'onum' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'onum' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'onum' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'onum' ),
						'icon' => 'eicon-text-align-right',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .project_filters' => 'text-align: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'filter_spacing',
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
					'{{WRAPPER}} .project_filters' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'filter_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .project_filters li a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'filter_hcolor',
			[
				'label' => __( 'Text Hover Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .project_filters li a:hover, {{WRAPPER}} .project_filters li a.selected' => 'color: {{VALUE}};',
					'{{WRAPPER}} .project_filters li a:after' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'filter_typography',
				'selector' => '{{WRAPPER}} .project_filters li a',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'overlay_style_section',
			[
				'label' => __( 'Overlay Box', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'overlay_align',
			[
				'label' => __( 'Alignment', 'onum' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'onum' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'onum' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'onum' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'onum' ),
						'icon' => 'eicon-text-align-justify',
					],
				],				
				'selectors' => [
					'{{WRAPPER}} .projects-box .portfolio-info .portfolio-info-inner' => 'text-align: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'overlay_pos',
			[
				'label' => __( 'Position Bottom', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .projects-box .portfolio-info' => 'bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .projects-box ' => 'border-bottom-width: calc(0px - {{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'layout' => 'style2',
				]
			]
		);
		$this->add_responsive_control(
			'overlay_pos_hover',
			[
				'label' => __( 'Position Bottom Hover', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .projects-box:hover .portfolio-info' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout' => 'style2',
				]
			]
		);		
		$this->add_responsive_control(
			'overlay_width',
			[
				'label' => __( 'Width', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .projects-box .portfolio-info' => 'width: {{SIZE}}{{UNIT}};right:-{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout' => 'style1',
				]
			]
		);
		$this->add_responsive_control(
			'overlay_padd',
			[
				'label' => __( 'Padding', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .projects-box .portfolio-info .portfolio-info-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'overlay_radius',
			[
				'label' => __( 'Border Radius', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .projects-box .portfolio-info .portfolio-info-inner, {{WRAPPER}} .projects-box .s2' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_background',
				'label' => __( 'Background', 'onum' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .projects-style-1 .projects-box .portfolio-info:not(.s2) .portfolio-info-inner, {{WRAPPER}} .projects-box .s2, {{WRAPPER}} .projects-style-3 .projects-box .portfolio-info',
			]			
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'overlay_box_shadow',
				'label' => __( 'Box Shadow', 'onum' ),
				'selector' => '{{WRAPPER}} .projects-style-1 .projects-box .portfolio-info:not(.s2) .portfolio-info-inner, {{WRAPPER}} .projects-style-2 .projects-box .portfolio-info .portfolio-info-inner',
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
					'{{WRAPPER}} .projects-box .portfolio-info .portfolio-info-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .projects-box .portfolio-info .portfolio-info-title a' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .projects-box .portfolio-info .portfolio-info-title a',
			]
		);
		$this->add_control(
			'title_html_tag',
			[
				'label' => __( 'Title HTML Tag', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h5',
				'options' => self::get_onum_heading_html_tag(),
			]
		);

		//Category
		$this->add_control(
			'heading_overlay',
			[
				'label' => __( 'Category', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'show_cat',
			[
				'label' => __( 'Show Category', 'onum' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'onum' ),
				'label_off' => __( 'Hide', 'onum' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_responsive_control(
			'cat_spacing',
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
					'{{WRAPPER}} .projects-box .portfolio-info .portfolio-cates' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_cat' => 'yes',
				]
			]
		);
		$this->add_control(
			'cat_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .projects-box .portfolio-info .portfolio-cates a, {{WRAPPER}} .projects-box .portfolio-info .portfolio-cates span' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_cat' => 'yes',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cat_typography',
				'selector' => '{{WRAPPER}} .projects-box .portfolio-info .portfolio-cates a, {{WRAPPER}} .projects-box .portfolio-info .portfolio-cates span',
				'condition' => [
					'show_cat' => 'yes',
				]
			]
		);
		$this->end_controls_section();		

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$titletag = $settings['title_html_tag'];
		$width    = ( ! empty ( $settings['thumbnail_dimension']['width'] ) ? $settings['thumbnail_dimension']['width'] : 0 );
		$height   = ( ! empty( $settings['thumbnail_dimension']['height'] ) ? $settings['thumbnail_dimension']['height'] : 0 );
		$thumbnail_size = $settings['thumbnail_size'];

		$wrap_class = array();
		if( $settings['layout'] == 'style2' ){ 
			$wrap_class[] = 'projects-style-2';
		} elseif ( $settings['layout'] == 'style3' ){ 
			$wrap_class[] = 'projects-style-3';
		} else {
			$wrap_class[] = 'projects-style-1';
		}

		if( $settings['column'] == '5' ){ 
			$wrap_class[] = 'pf_5_cols';
		}elseif( $settings['column'] == '4' ){ 
			$wrap_class[] = 'pf_4_cols';
		}elseif( $settings['column'] == '2' ){ 
			$wrap_class[] = 'pf_2_cols'; 
		}else{ 
			$wrap_class[] = ''; 
		} 

		?>

		<div class="project-filter-wrapper">
	        <?php if( 'yes' === $settings['filter'] ) { ?>
	        	<div class="container">
	        		<ul class="project_filters">
	        			<?php if( $settings['all_text'] ) { ?>
	        			 	<li><a href="#" data-filter="*" class="selected"><?php echo esc_html( $settings['all_text'] ); ?></a></li>
	        			<?php } ?>
		                <?php
		                if( $settings['project_cat'] ){
		                    $categories = $settings['project_cat'];
		                    foreach( (array)$categories as $categorie){
		                        $cates = get_term_by('slug', $categorie, 'portfolio_cat');
		                        $cat_name = $cates->name;
		                        $cat_slug = $cates->term_id;

		                ?>
		                	<li><a href='#' data-filter='.category-<?php echo esc_attr( $cat_slug ); ?>'><?php echo esc_html( $cat_name ); ?></a></li>	                   
		                <?php } }else{
		                    $categories = get_terms('portfolio_cat');
		                    if ( ! empty( $categories ) && ! is_wp_error( $categories ) ){
			                    foreach( (array)$categories as $categorie){
			                        $cat_name = $categorie->name;
			                        $cat_slug = $categorie->term_id;
			                    ?>
		                    <li><a href='#' data-filter='.category-<?php echo esc_attr( $cat_slug ); ?>'><?php echo esc_html( $cat_name ); ?></a></li>	                    
		                <?php } } } ?>
					</ul>
				</div>
	        <?php } ?>

	        <div class="projects-grid <?php echo implode( ' ', $wrap_class ); ?>">
	        	<div class="project-grid-sizer"></div>
	            <?php
	            if ( $settings['project_cat'] ) {
	                $args = array(	                    
	                    'post_type' => 'ot_portfolio',
	                    'post_status' => 'publish',
	                    'posts_per_page' => $settings['project_num'],
	                    'tax_query' => array(
	                        array(
	                            'taxonomy' => 'portfolio_cat',
	                            'field' => 'slug',
	                            'terms' => $settings['project_cat'],
	                        ),
	                    ),              
	                );
	            } else {
	                $args = array(
	                    'post_type' => 'ot_portfolio',
	                    'post_status' => 'publish',
	                    'posts_per_page' => $settings['project_num'],
	                );
	            }
	            $wp_query = new \WP_Query($args);
	            while ($wp_query -> have_posts()) : $wp_query -> the_post();
	                $cates = get_the_terms( get_the_ID(), 'portfolio_cat' );
	                $cate_name = '';
	                $cate_slug = '';
	                if ( ! is_wp_error( $cates ) && ! empty( $cates ) ) :
		                foreach( $cates as $cate ){		                	 	                    
	                        $cate_name .= $cate->name .'<span> / </span>';
	                        $cate_slug .= 'category-' . $cate->term_id . ' ';		                    
		                }
		            endif;
	            ?>

	            	<div class="project-item <?php echo esc_attr( $cate_slug ); ?>">
						<div class="projects-box">
							<div class="projects-thumbnail">
								<a href="<?php the_permalink(); ?>">
									<?php
										if ( has_post_thumbnail() ) {
											if ( $settings['thumbnail_size'] != 'custom' ) {
												the_post_thumbnail( $thumbnail_size );
											} else {
												the_post_thumbnail( array( $width, $height ) );
											}	
										}
									?>
								</a>
							</div>
							<div class="portfolio-info <?php echo esc_attr($settings['overlay_style']); ?>">
								<div class="portfolio-info-inner">
									<<?php echo $titletag; ?> class="portfolio-info-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo $titletag; ?>>
									<?php 
										if( 'yes' === $settings['show_cat'] ) {
											$terms = get_the_terms( get_the_ID(), 'portfolio_cat' );	
											if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) :
												echo '<p class="portfolio-cates">';	 
											    foreach ( $terms as $term ) {
											    	// The $term is an object, so we don't need to specify the $taxonomy.
									    			$term_link = get_term_link( $term );
									    			// If there was an error, continue to the next term.
												    if ( is_wp_error( $term_link ) ) {
												        continue;
												    }
											        // We successfully got a link. Print it out.
									    			echo '<a href="' . esc_url( $term_link ) . '">' . $term->name . '</a><span>/</span>';
											    }		                         
											    
												echo '</p>';    
											endif; 
										}
									?> 
								</div>
							</div>
						</div>
					</div>

	            <?php endwhile; wp_reset_postdata(); ?>
	        </div>
	    </div>

	    <?php
	}

	protected function select_param_cate_project() {
	  	$category = get_terms( 'portfolio_cat' );
	  	$cat = array();
	  	if ( ! empty( $category ) && ! is_wp_error( $category ) ){
		  	foreach( $category as $item ) {
		     	if( $item ) {
		        	$cat[$item->slug] = $item->name;
		     	}
		  	}
		}
	  	return $cat;
	}
}
// After the Schedule class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new Onum_PortfolioFilter() );