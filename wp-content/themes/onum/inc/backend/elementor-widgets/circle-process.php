<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Icon Box
 */
class Onum_Circle_Process extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'icircle_process';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'Onum Circle Process', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-counter-circle';
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
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'list_number', [
				'label' => __( 'Number', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '01' , 'onum' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'list_title', [
				'label' => __( 'Title', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'List Title' , 'onum' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'list_content', [
				'label' => __( 'Content', 'onum' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'List Content' , 'onum' ),
				'show_label' => false,
			]
		);

		$repeater->add_responsive_control(
			'list_align',
			[
				'label' => __( 'Content Align', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'text-left'  => __( 'Text Left', 'onum' ),
					'text-right' => __( 'Text Right', 'onum' ),
					'text-center' => __( 'Text Center', 'onum' ),
				],
			]
		);

		$repeater->add_responsive_control(
			'list_position',
			[
				'label' => __( 'Position', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'allowed_dimensions' => [ 'top', 'left' ],				
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ot-cprocess-item' => 'top: {{TOP}}{{UNIT}};left: {{LEFT}}{{UNIT}};'
				],
			]
		);

		$repeater->add_responsive_control(
			'list_spacing',
			[
				'label' => __( 'Spacing between content and number', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.text-right .ot-cprocess-item-inner' => 'padding-right:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.text-left .ot-cprocess-item-inner' => 'padding-left:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.text-center .ot-cprocess-item-number' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$repeater->add_control(
			'list_color',
			[
				'label' => __( 'Dot & Number Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ot-cprocess-item-number' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ot-cprocess-item-dot' => 'background-color: {{VALUE}}'
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'list_box_shadow',
				'label' => __( 'Box Shadow for Dot', 'onum' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .ot-cprocess-item-dot',
			]
		);

		$this->add_control(
			'list_process',
			[
				'label' => __( 'Process List', 'onum' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'list_number' => __( '01', 'onum' ),
						'list_title' => __( 'Title #1', 'onum' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'onum' ),
					],
					[
						'list_number' => __( '02', 'onum' ),
						'list_title' => __( 'Title #2', 'onum' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'onum' ),
					],
				],
				'title_field' => '{{{ list_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Style Circle', 'onum' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);		

		$this->add_responsive_control(
			'circle_width_height',
			[
				'label' => __( 'Circle Width/Height', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 500,
				],
				'selectors' => [
					'{{WRAPPER}} .ot-cprocess' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'circle_border_width',
			[
				'label' => __( 'Circle Border Width', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
			]
		);

		$this->add_control(
			'circle_bgcolor1',
			[
				'label' => __( 'Gradient Color 1', 'onum' ),
				'type' => Controls_Manager::COLOR
			]
		);

		$this->add_control(
			'circle_bgcolor2',
			[
				'label' => __( 'Gradient Color 2', 'onum' ),
				'type' => Controls_Manager::COLOR
			]
		);

		$this->add_control(
			'circle_rotate',
			[
				'label' => __( 'Angle', 'onum' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1000,
				'default' => 45
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'circle_title_section',
			[
				'label' => __( 'Title', 'onum' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'circle_title_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-cprocess .ot-cprocess-item .ot-cprocess-item-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'circle_title_typography',
				'selector' => '{{WRAPPER}} .ot-cprocess .ot-cprocess-item .ot-cprocess-item-title',
			]
		);
		$this->end_controls_section();


		$this->start_controls_section(
			'circle_subtitle_section',
			[
				'label' => __( 'SubTitle', 'onum' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'circle_subtitle_color',
			[
				'label' => __( 'Text Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-cprocess .ot-cprocess-item .ot-cprocess-item-desc' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'circle_subtitle_typography',
				'selector' => '{{WRAPPER}} .ot-cprocess .ot-cprocess-item .ot-cprocess-item-desc',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'circle_number_section',
			[
				'label' => __( 'Number', 'onum' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);		
		$this->add_responsive_control(
			'circle_number_wh',
			[
				'label' => __( 'Width/Height', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ot-cprocess .ot-cprocess-item .ot-cprocess-item-number' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};    line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'circle_number_typography',
				'selector' => '{{WRAPPER}} .ot-cprocess .ot-cprocess-item .ot-cprocess-item-number',
			]
		);
		$this->add_control(
			'circle_number_bgcolor',
			[
				'label' => __( 'Background Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-cprocess .ot-cprocess-item .ot-cprocess-item-number' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'circle_number_box_shadow',
				'label' => __( 'Box Shadow', 'onum' ),
				'selector' => '{{WRAPPER}} .ot-cprocess .ot-cprocess-item .ot-cprocess-item-number',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'circle_logo_section',
			[
				'label' => __( 'Circle Logo', 'onum' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'circle_logo_image',
			[
				'label' => __( 'Choose Logo Image', 'onum' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'circle_logo_position_top',
			[
				'label' => __( 'Position Top', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .circle_logo' => 'top: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'circle_logo_position_left',
			[
				'label' => __( 'Position Left', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .circle_logo' => 'left: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
	?>

		<div class="ot-cprocess">
			<?php 
				$settings = $this->get_settings_for_display();				
				$cwh = $settings['circle_width_height']['size'];
				$cbw = $settings['circle_border_width']['size'];
				$cx_cy = ( $cwh / 2 );
				$cr = ( $cx_cy - $cbw );
				$stroke_width = ( $cbw * 2 );
				if ( $settings['list_process'] ) {					
					foreach (  $settings['list_process'] as $item ) {						
						echo '<div class="ot-cprocess-item clearfix elementor-repeater-item-' . $item['_id'] . ' ' . $item['list_align'] . '">';
							echo '<div class="ot-cprocess-item-number font-second">' . $item['list_number'] . '</div>';
							echo '<div class="ot-cprocess-item-inner">';
								echo '<div class="ot-cprocess-item-dot"></div>';
								echo '<div class="ot-cprocess-item-title font-second">' . $item['list_title'] . '</div>';
								echo '<div class="ot-cprocess-item-desc font-main">' . $item['list_content'] . '</div>';
							echo '</div>';
						echo '</div>';
					}					
				} 
			?>			
			<?php if ($settings['circle_logo_image']) { ?>
				<div class="circle_logo">
					<?php echo wp_get_attachment_image( $settings['circle_logo_image']['id'], 'full' ); ?>
				</div>
			<?php } ?>
			<svg class="ot-cprocess-circle-chart" height="<?php echo esc_attr( $cwh ); ?>" width="<?php echo esc_attr( $cwh ); ?>">
				<circle cx="<?php echo esc_attr( $cx_cy ); ?>" cy="<?php echo esc_attr( $cx_cy ); ?>" r="<?php echo esc_attr( $cr ); ?>" stroke="url(#gradient)" stroke-width="<?php echo esc_attr( $stroke_width ); ?>" fill="none" />
				<linearGradient id="gradient" gradientTransform="rotate(<?php echo esc_attr( $settings['circle_rotate'] ); ?>)">
					<stop offset="0%"  stop-color="<?php echo esc_attr( $settings['circle_bgcolor1'] ); ?>"/>	
					<stop offset="100%" stop-color="<?php echo esc_attr( $settings['circle_bgcolor2'] ); ?>"/>
				</linearGradient>  				
			</svg>
		</div>

	<?php
	}

	
}
// After the Schedule class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new Onum_Circle_Process() );