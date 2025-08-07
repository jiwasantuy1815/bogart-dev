<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Section Heading 
 */
class ONUM_Chart_Number extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'ichartnumber';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'Onum Chart Number', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	// The get_categories method, lets you set the category of the widget, return the category name as a string.
	public function get_categories() {
		return [ 'category_onum' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'onum' ),
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

		$repeater = new Repeater();
		$repeater->add_control(
			'num_box',
			[
				'label' => __( 'Number Before', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => '104,457',
			]
		);
		$repeater->add_control(
			'num_box_after',
			[
				'label' => __( 'Number After', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => '314,297',
			]
		);

		$repeater->add_control(
			'title_box',
			[
				'label' => __( 'Title', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Annual Organic Traffic',
			]
		);

		$repeater->add_control(
			'img_box',
			[
				'label' => __( 'Bottom Image', 'onum' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
		$repeater->add_control(
			'number_color',
			[
				'label' => __( 'Color Number', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#00c3ff',
				'selectors' => [
					'{{WRAPPER}} .anhat' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
		    'chart_boxes',
		    [
		        'label'       => '',
		        'type'        => Controls_Manager::REPEATER,
		        'show_label'  => false,
		        'default'     => [
		            [
		             	'num_box' => __( '104,457', 'onum' ),
						'title_box'	 => 'Annual Organic Traffic',
		 
		            ],
		            
		        ],
		        'fields'      => $repeater->get_controls(),
		        'title_field' => '{{{title_box}}}',
		    ]
		);
		$this->add_control(
			'columns',
			[
				'label' 	=> __( 'Columns', 'onum' ),
				'type'  	=> Controls_Manager::SELECT,
				'default' 	=> 'col-md-4',
				'options' 	=> [
					'col-md-6'  => __( '2', 'onum' ),
					'col-md-4'  => __( '3', 'onum' ),
					'col-md-3'  => __( '4', 'onum' ),
				]
			]
		);
		$this->add_control(
			'img_before',
			[
				'label' => __( 'Person Before', 'onum' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => get_template_directory_uri().'/images/woman-before.png',
				],
			]
		);
		$this->add_control(
			'img_after',
			[
				'label' => __( 'Person After', 'onum' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => get_template_directory_uri().'/images/man-after.png',
				],
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
		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .real-numbers .ot-switchs-wrap > span' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'bactive_color',
			[
				'label' => __( 'Before Active Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .real-numbers .ot-switchs-wrap > span.b-switch.active' => 'color: {{VALUE}};',
					'{{WRAPPER}} .real-numbers .switch_slider' => 'background: {{VALUE}};'
				]
			]
		);
		$this->add_control(
			'aactive_color',
			[
				'label' => __( 'After Active Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .real-numbers .ot-switchs-wrap > span.a-switch.active' => 'color: {{VALUE}};',
				 	'{{WRAPPER}} .real-numbers input:checked + .switch_slider' => 'background: {{VALUE}};'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .real-numbers .ot-switchs-wrap > span',
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
					'{{WRAPPER}} .ot-switchs-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_section();

		//Boxes
		$this->start_controls_section(
			'style_toggle_boxes',
			[
				'label' => __( 'Boxes', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'boxes_space',
			[
				'label' => __( 'Spacing Right', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .real-numbers .chart-boxs .col-md' => 'padding: 0 calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .real-numbers .chart-boxs' => 'margin: 0 calc(-{{SIZE}}{{UNIT}}/2);',
				],
			]
		);
		$this->add_responsive_control(
			'boxes_space_bottom',
			[
				'label' => __( 'Spacing Bottom', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .real-numbers .chart-boxs .col-md' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'bgbox_color',
			[
				'label' => __( 'Background', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .chart-item' => 'background: {{VALUE}};',
				]
			]
		);
		$this->add_responsive_control(
			'box_padding',
			[
				'label' => __( 'Padding Box', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .chart-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		//Number
		$this->add_control(
			'heading_number',
			[
				'label' => __( 'Number', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'number_space',
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
					'{{WRAPPER}} .chart-item h2' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'number_typography',
				'selector' => '{{WRAPPER}} .chart-item h2',
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
			'title_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .chart-item > span' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .chart-item > span',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>

		<div class="real-numbers font-second">
			<?php if ( ! empty( $settings['chart_boxes'] ) ) : ?>
			<div class="ot-switchs-wrap">
				<span class="b-switch active"><?php echo $settings['before_text']; ?></span>
				<label class="ot__switch">
				  	<input type="checkbox">
				  	<span class="switch_slider round"></span>
				</label>
				<span class="a-switch"><?php echo $settings['after_text']; ?></span>
			</div>
			<div class="chart-boxs clearfix">
				<img class="img-before" src="<?php echo esc_url( $settings['img_before']['url'] ); ?>" alt="">
				<img class="img-after" src="<?php echo esc_url( $settings['img_after']['url'] ); ?>" alt="">
				<?php foreach ( $settings['chart_boxes'] as $box ) : ?>
				<div class="col-md <?php echo $settings['columns']; ?>">
					<div class="chart-item">
						<h2 class="before" style="color: <?php echo $box['number_color']; ?>"><?php echo $box['num_box']; ?></h2>
						<h2 class="after" style="color: <?php echo $box['number_color']; ?>"><?php echo $box['num_box_after']; ?></h2>
						<span><?php echo $box['title_box']; ?></span>
						<img class="img-box" src="<?php echo esc_url( $box['img_box']['url'] ); ?>" alt="">
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>

	    <?php
	}
}
// After the Schedule class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new ONUM_Chart_Number() );