<?php if ( onum_get_option('topbar_switch') != false ) { ?>
	<!-- Top bar start -->
	<div class="header-topbar">
		<div class="octf-area-wrap">
			<div class="<?php onum_header_width_class(); ?>">				
				<!-- Header Topbar Menus -->
				<?php 
					wp_nav_menu( array(
					    'menu'           => onum_get_option('topbar_menu'), // Do not fall back to first non-empty menu.
					    'theme_location' => '__no_such_location',
					    'fallback_cb'    => false, // Do not fall back to wp_page_menu()
					    'container_class' => 'topbar_menu',
					    'menu_class'      => 'menu clearfix'
					) );
				?>						
				<?php if ( onum_get_option('social_switch') != false ){ ?>
                    <!-- social icons -->
                    <ul class="social-list">
                        <?php $socials = onum_get_option( 'header_socials', array() ); ?>
                        <?php foreach ( $socials as $social ) { ?>
                            <li><a href="<?php echo esc_url($social['social_link']); ?>" target="<?php echo esc_attr( onum_get_option( 'social_target_link' ) ); ?>" ><i class="<?php echo esc_attr($social['social_icon']); ?>"></i></a>
                            </li>
                        <?php } ?>
                    </ul>
                    <!-- social icons close -->
                <?php } ?>		

                <?php if ( is_active_sidebar( 'topbar_widget' ) ) : ?>
		        	<div class="topbar_languages">
		        		<?php dynamic_sidebar( 'topbar_widget' ); ?>
		        	</div>
				<?php endif; ?>	

                <?php if ( onum_get_option('info_switch') != false ){ ?>
		            <!-- contact info -->
		            <ul class="topbar-info clearfix">
		                <?php $contact_infos = onum_get_option( 'header_contact_info', array() ); ?>
		                <?php foreach ( $contact_infos as $contact_info ) { ?>
		                    <li>
		                        <?php if($contact_info['info_icon'] != ''){ ?><i class="<?php echo esc_attr($contact_info['info_icon']); ?>"></i><?php } ?>
		                        <?php echo wp_specialchars_decode($contact_info['info_content']); ?>
		                    </li>
		                <?php } ?>
		            </ul>
		            <!-- contact info close -->
		        <?php } ?>				        
			</div>
		</div>
	</div>
	<!-- Top bar close -->
<?php } ?>

<!-- Main header start -->
<div class="octf-main-header">
	<div class="octf-area-wrap">
		<div class="<?php onum_header_width_class(); ?> octf-mainbar-container">
			<div class="octf-mainbar">
				<div class="octf-mainbar-row octf-row">
					<div class="octf-col">
						<div id="site-logo" class="site-logo">
							<a itemprop="url" href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<img itemprop="image" <?php if ( onum_get_option('logo_scroll') != '' ) { ?>class="logo-static"<?php } ?> src="<?php echo onum_get_option('logo') ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
								<?php if ( onum_get_option('logo_scroll') != '' ) { ?>
									<img itemprop="image" class="logo-scroll" src="<?php echo onum_get_option('logo_scroll') ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
								<?php } ?>
							</a>
						</div>
					</div>
					<div class="octf-col flex-end">
						<nav id="site-navigation" class="main-navigation">			
							<?php
								/*multisite */
								if ( is_multisite() ) {
								  	/*this plugin is network activated - Mega menu must be network activated */
								  	if ( is_plugin_active_for_network( plugin_basename(__FILE__) ) ) {
								    	$active_mmenu = is_plugin_active_for_network('ot_mega-menu/ot_mega-menu.php') ? true : false; 
								  	/*this plugin is locally activated - Mega menu can be network or locally activated */
								  	} else {
								    	$active_mmenu = is_plugin_active( 'ot_mega-menu/ot_mega-menu.php')  ? true : false;   
								  	}
									/*this plugin runs on a single site */
								} else {
								  	$active_mmenu =  is_plugin_active( 'ot_mega-menu/ot_mega-menu.php') ? true : false;     
								}
								$primary = array(
									'theme_location' => 'primary',
									'menu_id'        => 'primary-menu',
									'container'      => 'ul',
									'walker'         => $active_mmenu ? new Ot_Mega_Menu_Walker() : '',
								);
								if ( has_nav_menu( 'primary' ) ) {
				                    wp_nav_menu( $primary );
				                }
							?>
						</nav><!-- #site-navigation -->
					</div>
					<?php if ( onum_get_option('cart_switch') || onum_get_option('search_switch') || onum_get_option('header_cta_switch') ){ ?>
					<div class="octf-col text-right">
						<!-- Call To Action -->
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
							<?php if ( onum_get_option('header_cta_switch') == true ){ ?>
							<div class="octf-header-module">
								<div class="btn-cta-group btn-cta-header">
									<a class="octf-btn octf-btn-third" href="<?php echo esc_url_raw( onum_get_option('cta_link_header') ); ?>"><?php echo onum_get_option('cta_text_header'); ?></a>
								</div>
							</div>
							<?php } ?>
						</div>								
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>		
<!-- Main header close -->