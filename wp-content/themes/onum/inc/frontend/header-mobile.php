<div class="header_mobile">
	<div class="container">
		<div class="mlogo_wrapper clearfix">
	        <div class="mobile_logo">
	            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo onum_get_option('logo_mobile') ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"></a>
	    	</div>
			
			<div class="octf-btn-cta">
				<?php if ( onum_get_option('cart_switch') == true ){ ?>
				<div class="octf-header-module cart-btn-hover">
					<?php if ( class_exists( 'woocommerce' ) ) { ?>
						<div class="h-cart-btn octf-cta-icons">
							<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'onum' ); ?>"><i class="flaticon-supermarket"></i> <span class="count"><?php echo sprintf ( _n( '%d', '%d', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></span>
							</a>	
						</div>	
						<?php if( !is_cart() && !is_checkout() ) { ?>
						<div class="site-header-cart">
							<?php the_widget( 'WC_Widget_Cart', array( 'title' => '' ) ); ?>
						</div>	
						<?php } ?>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if ( onum_get_option('search_switch') == true ){ ?>
				<div class="octf-header-module">
					<div class="toggle_search toggle_click_search_1 octf-cta-icons">
						<i class="flaticon-search"></i>
					</div>
					<!-- Form Search on Header -->
					<div class="h-search-form-field">
						<div class="h-search-form-inner">
							<?php get_search_form(); ?>
						</div>									
					</div>
				</div>
				<?php } ?>
				
			</div>	

	        <div id="mmenu_toggle" class="mmenu_toggle">
		        <button>
			        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
						  viewBox="0 0 24.75 24.75" style="enable-background:new 0 0 24.75 24.75;" xml:space="preserve" >
					<g>
						<path d="M0,3.875c0-1.104,0.896-2,2-2h20.75c1.104,0,2,0.896,2,2s-0.896,2-2,2H2C0.896,5.875,0,4.979,0,3.875z M22.75,10.375H2
							c-1.104,0-2,0.896-2,2c0,1.104,0.896,2,2,2h20.75c1.104,0,2-0.896,2-2C24.75,11.271,23.855,10.375,22.75,10.375z M22.75,18.875H2
							c-1.104,0-2,0.896-2,2s0.896,2,2,2h20.75c1.104,0,2-0.896,2-2S23.855,18.875,22.75,18.875z"/>
					</g>
					</svg>
				</button>
		    </div>
	    </div>
	    <div class="site-overlay mmenu__overlay"></div>
	    <div id="mmenu_wrapper" class="mmenu_wrapper">		
	    	<div class="mmenu__inner">
		    	<a class="mmenu__close" href="#"><i class="flaticon-close"></i></a>
				<div class="mobile_nav">
					<?php
						wp_nav_menu( array(
							'theme_location' => 'primary',
							'menu_class'     => 'mobile_mainmenu',
							'container'      => '',
						) );
					?>
				</div>  
			</div> 	
	    </div>
	</div>
</div>
