<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Icon Box
 */
class Onum_IconBox_Grid extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'iiconbox_grid';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'Onum Icon Box Grid', 'onum' );
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

		//Content Service box
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Icon Box', 'onum' ),
			]
		);

		$this->add_responsive_control(
			'align',
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
				// 'prefix_class' => 'onum%s-align-',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
				'default' => 'center',
			]
		);
		$this->add_control(
			'column_grid',
			[
				'label' => __( 'Columns', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'col-md-4',
				'options' => [
					'col-md-12' => __( '1', 'onum' ),
					'col-md-6'  => __( '2', 'onum' ),
					'col-md-4'  => __( '3', 'onum' ),
					'col-md-3'  => __( '4', 'onum' ),
				]
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon_type',
			[
				'label' => __( 'Icon Type', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font',
				'options' => [
					'font' 	=> __( 'Font Icon', 'onum' ),
					'image' => __( 'Image Icon', 'onum' ),
					'class' => __( 'Custom Icon', 'onum' ),
				]
			]
		);
		$repeater->add_control(
			'icon_font',
			[
				'label' => __( 'Icon', 'onum' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => true,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'icon_type' => 'font',
				]
			]
		);
		$repeater->add_control(
	       'icon_image',
	        [
	           'label' => esc_html__( 'Photo', 'onum' ),
	           'type'  => Controls_Manager::MEDIA,
				'default' => [
					'url' => get_template_directory_uri().'/images/analysis.png',
			  	],
			  	'condition' => [
					'icon_type' => 'image',
				]
		    ]
	    );
	    $repeater->add_control(
			'icon_class',
			[
				'label' => __( 'Custom Class', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'flaticon-world', 'onum' ),
				'condition' => [
					'icon_type' => 'class',
				]
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Content Marketing', 'onum' ),
			]
		);

		$repeater->add_control(
			'des',
			[
				'label' => 'Description',
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'You can provide the answers that your potential customers are trying to find, so you can become the industry.', 'onum' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'onum' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'onum' ),
			]
		);

		$this->add_control(
		    'icon_boxes',
		    [
		        'label'       => '',
		        'type'        => Controls_Manager::REPEATER,
		        'show_label'  => false,
		        'default'     => [
		            [
		            	'icon_type'  => __( 'class', 'onum' ),
		            	'icon_class' => __( 'flaticon-world', 'onum' ),
		             	'title' => esc_html__( 'Content Marketing', 'onum' ),
		                'des'	=> esc_html__( 'You can provide the answers that your potential customers are trying to find, so you can become the industry.', 'onum' ),
		             	'link' => '',
		            ],
		            [
		            	'icon_type'  => __( 'class', 'onum' ),
		            	'icon_class' => __( 'flaticon-world', 'onum' ),
		             	'title' => esc_html__( 'Content Marketing', 'onum' ),
		                'des'	=> esc_html__( 'You can provide the answers that your potential customers are trying to find, so you can become the industry.', 'onum' ),
		             	'link' => '',
		            ],
		            [
		            	'icon_type'  => __( 'class', 'onum' ),
		            	'icon_class' => __( 'flaticon-world', 'onum' ),
		             	'title' => esc_html__( 'Content Marketing', 'onum' ),
		                'des'	=> esc_html__( 'You can provide the answers that your potential customers are trying to find, so you can become the industry.', 'onum' ),
		             	'link' => '',
		            ]
		        ],
		        'fields'      => $repeater->get_controls(),
		        'title_field' => '{{{title}}}',
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

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'style_box_section',
			[
				'label' => __( 'Boxes', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'heading_bg',
			[
				'label' => __( 'Background Grid', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'box_bg_color',
				'types' => [ 'gradient' ],
				'selector' => '{{WRAPPER}} .icon-box-grid',
			]
		);

		$this->add_control(
			'heading_bg_hover',
			[
				'label' => __( 'Background Hover Box', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'box_bg_hover_color',
				'types' => [ 'gradient' ],
				'selector' => '{{WRAPPER}} .icon-box:hover',
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
					'{{WRAPPER}} .icon-box-grid' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label' => __( 'Padding Box', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .icon-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'accs_box_shadow',
				'selector' => '{{WRAPPER}} .icon-box-grid',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_icon_section',
			[
				'label' => __( 'Icon', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .icon-main i, {{WRAPPER}} .icon-main span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .icon-main svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 200,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors' => [
					'{{WRAPPER}} .icon-main i, {{WRAPPER}} .icon-main span:before' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .icon-main svg, {{WRAPPER}} .icon-main img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_space',
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
					'{{WRAPPER}} .icon-main' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
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
		$this->add_responsive_control(
			'title_space',
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
					'{{WRAPPER}} .icon-box .box-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .icon-box .box-title' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .icon-box .box-title',
			]
		);

		//Description
		$this->add_control(
			'heading_des',
			[
				'label' => __( 'Description', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'des_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .icon-box p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'des_typography',
				'selector' => '{{WRAPPER}} .icon-box p',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();		
		$titletag = $settings['title_html_tag'];
		?>
		<div class="icon-box-grid">
			<?php if ( ! empty( $settings['icon_boxes'] ) ) : foreach ( $settings['icon_boxes'] as $key => $boxes ) : ?>
			<div class="<?php echo esc_attr( $settings['column_grid'] ); ?> no-padding">
				<div class="icon-box">
					<div class="icon-main">

				        <?php 
				        	if( $boxes['icon_type'] == 'font' ) { 
				        	if ( empty( $boxes['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
								// add old default
								$boxes['icon'] = 'fa fa-star';
							}

							if ( ! empty( $boxes['icon'] ) ) {
								$this->add_render_attribute( 'icon', 'class', $boxes['icon'] );
								$this->add_render_attribute( 'icon', 'aria-hidden', 'true' );
							}

							$migrated = isset( $boxes['__fa4_migrated']['icon_font'] );
							$is_new = empty( $boxes['icon'] ) && Icons_Manager::is_migration_allowed();	
				        ?>

				        	<?php if ( $is_new || $migrated ) :
								Icons_Manager::render_icon( $boxes['icon_font'], [ 'aria-hidden' => 'true' ] );
							else : ?>
								<i <?php echo $this->get_render_attribute_string( 'icon' ); ?>></i>
							<?php endif; ?>

				        <?php } ?>

					    <?php if( $boxes['icon_type'] == 'image' ) { echo Group_Control_Image_Size::get_attachment_image_html( $boxes, 'thumbnail', 'icon_image' ); } ?>
				        <?php if( $boxes['icon_type'] == 'class' ) { ?><span class="<?php echo esc_attr( $boxes['icon_class'] ); ?>"></span><?php } ?>
			        </div>
			        <div class="content-box">
			        	<?php 
			        		$title = $boxes['title'];
							if ( ! empty( $boxes['link']['url'] ) ) {
								$this->add_render_attribute( 'iconbox'.$key, 'href', $boxes['link']['url'] );

								if ( $boxes['link']['is_external'] ) {
									$this->add_render_attribute( 'iconbox'.$key, 'target', '_blank' );
								}

								if ( $boxes['link']['nofollow'] ) {
									$this->add_render_attribute( 'iconbox'.$key, 'rel', 'nofollow' );
								}
								$title = '<a '. $this->get_render_attribute_string( 'iconbox'.$key ).'>'. $boxes['title'] . '</a>';
							}
			        	?>
			            <<?php echo $titletag; ?> class="box-title"><?php echo $title; ?></<?php echo $titletag; ?>>
			            <p><?php echo $boxes['des']; ?></p>
			        </div>
			    </div>
		    </div>
		    <?php endforeach; endif; ?>
		</div>
	    <?php
	}

	
}
// After the Schedule class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new Onum_IconBox_Grid() );