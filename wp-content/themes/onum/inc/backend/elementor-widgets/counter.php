<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Contact Info
 */
class ONUM_Counter extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'icounter';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'Onum Counter', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-counter';
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

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Counter', 'onum' ),
			]
		);

		$this->add_control(
			'box_style',
			[
				'label' 	=> __( 'Style', 'onum' ),
				'type'  	=> Controls_Manager::SELECT,
				'default' 	=> 's1',
				'options' 	=> [
					's1'  => __( 'Number Top', 'onum' ),
					's2'  => __( 'Number Bottom', 'onum' ),
				]
			]
		);
		$this->add_control(
			'number_stroke',
			[
				'label' => __( 'Number Stroke', 'onum' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'onum' ),
				'label_off' => __( 'Off', 'onum' ),
				'return_value' => 'yes',
				'default' => 'no',
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
					]
				],
				// 'prefix_class' => 'onum%s-align-',
				'selectors' => [
					'{{WRAPPER}} .ot-counter' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'onum' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Projects Done', 'onum' ),
			]
		);

		$this->add_control(
			'number',
			[
				'label' => 'Number',
				'type' => Controls_Manager::TEXT,
				'default' => __( '1990', 'onum' ),
			]
		);

		$this->add_control(
			'extra',
			[
				'label' => __( 'After Number', 'onum' ),
				'type' => Controls_Manager::TEXT,
			]
		);		

		$this->add_control(
			'time',
			[
				'label' => __( 'Duration', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 1000,
						'max'  => 10000,
						'step' => 1000,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2000,
				],
			]
		);
		$this->add_control(
			'title_html_tag',
			[
				'label' => __( 'Title HTML Tag', 'onum' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h6',
				'options' => self::get_onum_heading_html_tag(),
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'style_content_section',
			[
				'label' => __( 'Style', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		//Icon
		$this->add_control(
			'heading_number',
			[
				'label' => __( 'Number', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'number_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-counter span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .text__stroke span.num' => '-webkit-text-stroke-color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'number_typography',
				'selector' => '{{WRAPPER}} .ot-counter span',
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
						'min' => -30,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ot-counter .counter-title' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ot-counter.s2 .counter-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'title_padding',
			[
				'label' => __( 'Padding', 'onum' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ot-counter .counter-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .ot-counter .counter-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ot-counter .counter-title:before' => 'background: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'title_bgcolor',
			[
				'label' => __( 'Background Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-counter .counter-title' => 'background-color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .ot-counter .counter-title, .text__stroke.ot-counter .counter-title',
			]
		);

		//Dot
		$this->add_control(
			'heading_dot',
			[
				'label' => __( 'Dot', 'onum' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'dot_space',
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
					'{{WRAPPER}} .ot-counter .counter-title, {{WRAPPER}} .ot-counter .num' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'dot_size',
			[
				'label' => __( 'Size', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ot-counter .counter-title:before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; margin-top: {{SIZE}}{{UNIT}}/2;',
				],
			]
		);
		$this->add_control(
			'dot_color',
			[
				'label' => __( 'Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ot-counter .counter-title:before' => 'background: {{VALUE}};',
				]
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$titletag = $settings['title_html_tag'];
		?>
			<?php if( $settings['box_style'] === 's2' ) {  ?>
				<div class="ot-counter s2 <?php if ( 'yes' === $settings['number_stroke'] ) { echo 'text__stroke'; } ?>">
		        	<<?php echo $titletag; ?> class="counter-title"><?php echo $settings['title']; ?></<?php echo $titletag; ?>>	
		        	<div>
		        		<span class="num" data-to="<?php echo $settings['number']; ?>" data-time= "<?php echo $settings['time']['size']; ?>"></span>
		        		<?php if ($settings['extra'] != '') { ?><span><?php echo $settings['extra']; ?></span><?php } ?>
		        	</div>
			    </div>
		    <?php }else { ?>
		    	<div class="ot-counter <?php if ( 'yes' === $settings['number_stroke'] ) { echo 'text__stroke'; } ?>">
		        	<div>
		        		<span class="num" data-to="<?php echo $settings['number']; ?>" data-time= "<?php echo $settings['time']['size']; ?>"></span>
		        		<?php if ($settings['extra'] != '') { ?><span><?php echo $settings['extra']; ?></span><?php } ?>
		        	</div>
		        	<<?php echo $titletag; ?> class="counter-title"><?php echo $settings['title']; ?></<?php echo $titletag; ?>>        				        
			    </div>
		    <?php } ?>
	    <?php
	}

	
}
// After the Schedule class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new ONUM_Counter() );