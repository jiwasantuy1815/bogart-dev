<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Widget Name: Cart
 */
class ONUM_Cart extends Widget_Base{

 	// The get_name() method is a simple one, you just need to return a widget name that will be used in the code.
	public function get_name() {
		return 'icart';
	}

	// The get_title() method, which again, is a very simple one, you need to return the widget title that will be displayed as the widget label.
	public function get_title() {
		return __( 'OT Header Cart', 'onum' );
	}

	// The get_icon() method, is an optional but recommended method, it lets you set the widget icon. you can use any of the eicon or font-awesome icons, simply return the class name as a string.
	public function get_icon() {
		return 'eicon-cart';
	}

	// The get_categories method, lets you set the category of the widget, return the category name as a string.
	public function get_categories() {
		return [ 'category_onum_header' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'style_icon_section',
			[
				'label' => __( 'Icon', 'onum' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .octf-cart i:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_width',
			[
				'label' => __( 'Icon Width', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .octf-btn-cta .octf-cta-icons i' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .octf-cart i, {{WRAPPER}} .octf-cart i:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'icon_bgcolor',
			[
				'label' => __( 'Icon Background Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .octf-cart i' => 'background-color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'icon_scroll_color',
			[
				'label' => __( 'Icon Color Scroll', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .is-icon-sticky.octf-btn-cta .octf-cta-icons i, {{WRAPPER}} .is-icon-sticky.octf-btn-cta .octf-cta-icons i:before' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_control(
			'icon_scroll_bgcolor',
			[
				'label' => __( 'Icon Background Color Scroll', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .is-icon-sticky.octf-btn-cta .octf-cta-icons i' => 'background-color: {{VALUE}} !important;',
				]
			]
		);
		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'count_color',
			[
				'label' => __( 'Count Color', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .octf-cart .count' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'bg_count',
			[
				'label' => __( 'Count Background', 'onum' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .octf-cart .count' => 'background: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'count_top',
			[
				'label' => __( 'Count Top', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -30,
						'max' => 30,
						'step' => 1,
					],					
				],
				'selectors' => [
					'{{WRAPPER}} .octf-cart .count' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'count_left',
			[
				'label' => __( 'Count Left', 'onum' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -30,
						'max' => 30,
						'step' => 1,
					],					
				],
				'selectors' => [
					'{{WRAPPER}} .octf-cart .count' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		
	}

	public static function otcore_render_menu_cart() {
		if ( null === WC()->cart ) {
			return;
		}
		$product_count = sprintf ( _n( '%d', '%d', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() );
		$cart_url = esc_url( wc_get_cart_url() );
		$widget_cart_is_hidden = apply_filters( 'woocommerce_widget_cart_is_hidden', false );
		?>
		<?php if ( ! $widget_cart_is_hidden ) : ?>
			<div class="octf-cart octf-btn-cta">
				<div class="octf-cta-icons">
					<a class="cart-contents" href="<?php echo $cart_url; ?>" title="<?php esc_attr_e( 'View your shopping cart', 'onum' ); ?>"><i class="flaticon-supermarket"></i> <span class="count"><?php echo $product_count; ?></span>
					</a>
				</div>
				<?php if( !is_cart() && !is_checkout() ) { ?>
				<div class="site-header-cart">
					<?php the_widget( 'WC_Widget_Cart', array( 'title' => '' ) ); ?>
				</div>	
				<?php } ?>
			</div>
		<?php endif; ?>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		self::otcore_render_menu_cart();
	}

	
}
// After the ONUM_Cart class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new ONUM_Cart() );