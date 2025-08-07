<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Logo
 */
class ONUM_Logo extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'ilogo';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'OT Header Logo', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-logo';
	}

	// The get_categories method, lets you set the category of the widget, return the category name as a string.
	public function get_categories() {
		return [ 'category_onum_header' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Logo', 'onum' ),
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
				],
				'selectors' => [
					'{{WRAPPER}} .site__logo' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'logo_image',
			 [
				'label' => esc_html__( 'Logo', 'onum' ),
				'type'  => Controls_Manager::MEDIA,
				'default' => [
					'url' => get_template_directory_uri().'/images/logo.svg',
				],
			 ]
		);

		$this->add_control(
			'logo_image_scroll',
			 [
				'label' => esc_html__( 'Logo Scroll (Use in header sticky)', 'onum' ),
				'type'  => Controls_Manager::MEDIA,
				'default' => [
					'url' => get_template_directory_uri().'/images/logo-dark.svg',
				],
			 ]
		);

		$this->add_responsive_control(
			'logo_width',
			[
				'label' => __( 'Width', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .site__logo img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'logo_spacing',
			[
				'label' => __( 'Spacing', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .site__logo' => 'padding: {{SIZE}}{{UNIT}} 0px;',
				]
			]
		);	
		$this->end_controls_section();
		
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$logo_class = '';
		if ( ! empty( $settings['logo_image_scroll']['id'] ) ) {
			$logo_class = 'logo-static';
		}		
		?>
			
		<div class="site__logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php echo wp_get_attachment_image( $settings['logo_image']['id'], 'full', "", ["class" => $logo_class] ); ?>
				<?php echo wp_get_attachment_image( $settings['logo_image_scroll']['id'], 'full', "", ["class" => "logo-scroll"] ); ?>					
			</a>		
		</div>
		    
	    <?php
	}

	
}
// After the ONUM_Logo class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new ONUM_Logo() );