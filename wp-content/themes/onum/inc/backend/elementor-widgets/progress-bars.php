<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Section Heading 
 */
class ONUM_Progress_Bars extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'iprogress';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'Onum Progress Bars', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-skill-bar';
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
				'label' => __( 'Content', 'onum' ),
			]
		);

		$this->add_control(
			'bar_style',
			[
				'label' 	=> __( 'Bar Style', 'onum' ),
				'type'  	=> Controls_Manager::SELECT,
				'default' 	=> 'line',
				'options' 	=> [
					'line'    => __( 'Style 1: Line', 'onum' ),
					'circle'  => __( 'Style 2: Circle', 'onum' ),
				]
			]
		);

		$this->add_control(
			'title',
			[
				'label' => 'Title',
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Keyword Research', 'onum' ),
			]
		);
		$this->add_control(
			'percent',
			[
				'label' => 'Percentage',
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 70,
					'unit' => '%',
				],
			]
		);
		$this->add_control(
			'percent_text',
			[
				'label'   => esc_html__( 'Show Percentage', 'onum' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'bar_style_section',
			[
				'label' => __( 'Progress Bar', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'bar_color1',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#0160e7',
				'selectors' => [
					'{{WRAPPER}} .progress-bar' => 'background: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'bar_color2',
			[
				'label' => __( 'Color 2(Gradient)', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#00c3ff',
				'condition' => [
					'bar_style' => 'circle',
				]
			]
		);
		$this->add_control(
			'bg_color',
			[
				'label' => __( 'Background 100%', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .iprogress' => 'background: {{VALUE}};',
					'{{WRAPPER}} .circle-progress span' => 'border-color: {{VALUE}};',
				]
			]
		);
		$this->add_responsive_control(
			'bar_height',
			[
				'label' => __( 'Height', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .iprogress' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'bar_style' => 'line',
				]
			]
		);
		$this->add_responsive_control(
			'bar_radius',
			[
				'label' => __( 'Border Raidus', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .iprogress, {{WRAPPER}} .progress-bar' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'bar_style' => 'line',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'title_box_shadow',
				'selector' => '{{WRAPPER}} .progress-bar',
				'condition' => [
					'bar_style' => 'line',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_text_section',
			[
				'label' => __( 'Text', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		//Title
		$this->add_control(
			'heading_title',
			[
				'label' => __( 'Title', 'onum' ),
				'type' => Controls_Manager::HEADING,
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
					'{{WRAPPER}} .pname' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .circle-progress h4' => 'margin-top: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .ot-progress, {{WRAPPER}} .circle-progress h4' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .ot-progress, {{WRAPPER}} .circle-progress h4',
			]
		);

		//Percentage
		$this->add_control(
			'heading_percent',
			[
				'label' => __( 'Percentage', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'per_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ppercent, {{WRAPPER}} .circle-progress span' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'per_typography',
				'selector' => '{{WRAPPER}} .ppercent, {{WRAPPER}} .circle-progress span',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>

		<?php if( $settings['bar_style'] == 'line' ) { ?>
		<div class="ot-progress">
			<div class="overflow">
	        	<?php if( $settings['title'] ) echo '<span class="pname fleft">' . $settings['title'] . '</span>'; ?>
	        	<?php if( $settings['percent_text'] ) echo '<span class="ppercent fright">' . $settings['percent']['size'] . '%</span>'; ?>
	        </div>
	        <div class="iprogress">
				<div class="progress-bar" data-percent="<?php echo esc_attr( $settings['percent']['size'] ).'%'; ?>"></div>
			</div>
	    </div>
		<?php }else{ ?>
		<div class="circle-progress" data-color1="<?php echo esc_attr( $settings['bar_color1'] ); ?>" data-color2="<?php echo esc_attr( $settings['bar_color2'] ); ?>">
			<div class="inner-bar" data-percent="<?php echo esc_attr( $settings['percent']['size'] ); ?>">
				<span>
					<?php if( $settings['percent_text'] ) echo '<span class="percent">' . $settings['percent']['size'] . '</span>%'; ?>
				</span>
			</div>
			<?php if( $settings['title'] ) echo '<h4>' . $settings['title'] . '</h4>'; ?>
		</div>
		
	    <?php }
	}

	
}
// After the Schedule class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new ONUM_Progress_Bars() );