<?php 
//Custom Style Frontend
if(!function_exists('onum_color_scheme')){
    function onum_color_scheme(){
        $color_scheme = array();        

        //Main Color
        if( onum_get_option('main_color') != '#fe4c1c' || onum_get_option('second_color') != '#00c3ff' || onum_get_option('third_color') != '#0160e7' ){
            $color_scheme[] = 
            '
            .bg-gradient,
            .bg-hover-gradient:hover,
            .author-widget_wrapper,
            .service-box .number-box,
            .service-box .overlay,
            .icon-box.s1 .icon-main, .icon-box.s4 .icon-main,
            .icon-box.s3 .icon-main,
            .icon-box.s3 .bg-s3,
            .icon-box-grid,
            .process-box .number-box,
            .ot-testimonials .testimonial-wrap .bg-block,
            .projects-box .portfolio-info .portfolio-info-inner{ 
                background-color: '.onum_get_option('third_color').';
                background-image:-moz-linear-gradient(145deg, '.onum_get_option('third_color').', '.onum_get_option('second_color').');
                background-image:-webkit-linear-gradient(145deg, '.onum_get_option('third_color').', '.onum_get_option('second_color').');
                background-image:linear-gradient(145deg, '.onum_get_option('third_color').', '.onum_get_option('second_color').'); 
            }

            /**** Main Color ****/

            	/* Background Color */
                blockquote:before,
                .bg-primary,
                .octf-btn.octf-btn-white i,
                .octf-btn-primary,
                .post-box .post-cat a,
                .blog-post .share-post a,
                .widget-area .widget .widget-title:before,
                .search-form .search-submit i,
                .ot-pricing-table.s3 .title-table,
                .ot-tabs .tab-link,
                .ot-counter h6:before,
                .dc-text.dc-bg-primary .elementor-drop-cap,
                .mc4wp-form-fields .subscribe-inner-form .subscribe-btn-icon i{ background-color: '.onum_get_option('main_color').'; }
    			
    			/* Color */
                .text-primary,
                .octf-btn.octf-btn-white,
                .octf-btn.octf-btn-white:visited, .octf-btn.octf-btn-white:hover, .octf-btn.octf-btn-white:focus,                
                a:hover, a:focus, a:active,
                .header-topbar a:hover,
                .header-overlay .header-topbar a:hover,
                .header_mobile .mobile_nav .mobile_mainmenu li li a:hover,.header_mobile .mobile_nav .mobile_mainmenu ul > li > ul > li.current-menu-ancestor > a,
                .header_mobile .mobile_nav .mobile_mainmenu > li > a:hover, .header_mobile .mobile_nav .mobile_mainmenu > li.current-menu-item > a,.header_mobile .mobile_nav .mobile_mainmenu > li.current-menu-ancestor > a,
                .page-header .breadcrumbs li a:hover,
                .post-box .post-cat a:hover,
                .post-box .entry-meta a:hover i,
                .post-box .entry-title a:hover,
                .blog-post .author-bio .author-info .author-socials a:hover,
                .drop-cap span,
                .sl-wrapper .sl-icon svg,
                .comments-area .comment-item .comment-meta .comment-reply-link:hover,
                .comment-respond .comment-reply-title small a:hover,
                .comment-form .logged-in-as a:hover,
                .icon-box .content-box h5 a:hover,
                .icon-box.s3:hover h5 a:hover, .icon-box.s3:hover p a:hover,
                .icon-box-grid .icon-box .content-box h5 a:hover,
                .ot-pricing-table.s3 h2,
                .ot-tabs .tab-content ul.has-icon li i,
                .ot-counter h6,
                .video-popup a,
                .dc-text .elementor-drop-cap span{ color: '.onum_get_option('main_color').'; }
    			
    		/**** Second Color ****/
            
    		    /* Background Color */
                .bg-second,
                .slick-arrow:not(.slick-disabled):hover,
                .octf-btn-secondary,
                .octf-btn-secondary.octf-btn-white i,
                .main-navigation > ul > li:after,.main-navigation > ul > li:before,
                .main-navigation ul li li a:before,
                .cart-contents .count,
                .post-box .btn-play i:hover,
                .page-pagination li span, .page-pagination li a:hover,
                .blog-post .tagcloud a:hover,
                .widget .tagcloud a:hover,
                .widget-area .widget ul:not(.recent-news) > li a:hover:before,
                .search-form .search-submit i:hover,
                .ot-heading.text-light h6:before, .ot-heading.text-light h6:after,
                .team-wrap .team-social a, .team-wrap .team-social span,
                .ot-progress .progress-bar,
                .ot-pricing-table .title-table,
                .ot-tabs .tab-link.current, .ot-tabs .tab-link:hover,
                .ot-accordions .acc-item .acc-toggle i,
                .slider,
                .video-popup a:hover,
                .dc-text.dc-bg-second .elementor-drop-cap,
                div .custom .tp-bullet:after,
                .grid-lines .line-cleft .g-dot,
                .grid-lines .line-cright .g-dot,
                .project_filters li a:after{ background-color: '.onum_get_option('second_color').'; }        

                /* Color */            
                .text-second,
                .slick-arrow,
                .octf-btn-secondary i,
                .octf-btn-secondary.octf-btn-white,
                .octf-btn-secondary.octf-btn-white:visited, .octf-btn-secondary.octf-btn-white:hover, .octf-btn-secondary.octf-btn-white:focus,
                a,
                a:visited,
                .topbar-info li i,
                .main-navigation ul > li > a:hover,
                .main-navigation ul li li a:hover,.main-navigation ul ul li.current-menu-item > a,.main-navigation ul ul li.current-menu-ancestor > a,
                .header-style-1.header-overlay .btn-cta-header a,
                .post-box .link-box a:hover,
                .post-box .link-box i,
                .post-box .quote-box i,
                .post-box .btn-play i,
                .widget-area .widget ul:not(.recent-news) > li a:hover,
                .widget-area .widget ul:not(.recent-news) > li a:hover + span,
                .widget .recent-news h6 a:hover,
                .service-box:hover .number-box,
                .service-box-s2 .number-box,
                .active .service-box .number-box,
                .icon-box.s1:hover .icon-main, .icon-box.s4:hover .icon-main,
                .icon-box.s3:hover .icon-main,
                .active .icon-box.s1 .icon-main,
                .active .icon-box.s3 .icon-main,
                .team-wrap .team-social.active span,
                .ot-pricing-table .inner-table h2,
                .ot-accordions .acc-item .acc-toggle:hover,
                .ot-accordions .acc-item.current .acc-toggle,
                .slick-dots li.slick-active button:before,
                .real-numbers > span.active,
                .real-numbers .chart-boxs .chart-item h2,
                .dc-text.dc-text-second .elementor-drop-cap span,
                .projects-style-2 .projects-box .portfolio-info .portfolio-cates,
                .projects-style-2 .projects-box .portfolio-info .portfolio-cates a,
                .project_filters li a:hover, .project_filters li a.selected,
                .ot-countdown li.seperator,
                #back-to-top{ color: '.onum_get_option('second_color').'; }

                /* Border Color */
                .video-popup a:hover span{ border-color: '.onum_get_option('second_color').'; }

            /**** Third Color ****/
                
                /* Background Color */
                .bg-third,
                .octf-btn-third,
                .octf-btn-third.octf-btn-white i,
                .ot-pricing-table.s2 .title-table,
                .message-box .icon-main,
                input:checked + .slider,
                .dc-text.dc-bg-third .elementor-drop-cap,
                .grid-lines .g-dot{ background-color: '.onum_get_option('third_color').'; }

                /* Color */
                .text-third,
                .octf-btn-third i,
                .octf-btn-third.octf-btn-white,
                .octf-btn-third.octf-btn-white:visited, .octf-btn-third.octf-btn-white:hover, .octf-btn-third.octf-btn-white:focus,
                .post-nav a,
                .post-nav a:hover span,
                .icon-box.s2 .icon-main,
                .icon-box-grid .icon-box:hover .icon-main,
                .ot-pricing-table.s2 h2,
                .tab-titles .title-item:hover .icon-main, .tab-titles .title-item.tab-active .icon-main,
                .real-numbers > span.a-switch.active,
                .dc-text.dc-text-third .elementor-drop-cap span{ color: '.onum_get_option('third_color').'; }       

                /* Custom box-shadow when main color change */
                .octf-btn, .octf-btn-primary.octf-btn, .octf-btn-third.octf-btn, .octf-btn-secondary.octf-btn {
                    box-shadow: 12px 12px 20px 0px rgba(42, 67, 113, 0.3);
                  -webkit-box-shadow: 12px 12px 20px 0px rgba(42, 67, 113, 0.3);
                  -moz-box-shadow: 12px 12px 20px 0px rgba(42, 67, 113, 0.3);
                }
                .octf-btn-primary.octf-btn-icon, .octf-btn-third.octf-btn-icon, .octf-btn-secondary.octf-btn-icon {
                    box-shadow: 8px 8px 18px 0px rgba(42, 67, 113, 0.3);
                  -webkit-box-shadow: 8px 8px 18px 0px rgba(42, 67, 113, 0.3);
                  -moz-box-shadow: 8px 8px 18px 0px rgba(42, 67, 113, 0.3);
                }
                .icon-box-grid, .author-widget_wrapper {
                    box-shadow: 30px 30px 65px 0px rgba(42, 67, 113, 0.3);
                    -webkit-box-shadow: 30px 30px 65px 0px rgba(42, 67, 113, 0.3);
                    -moz-box-shadow: 30px 30px 65px 0px rgba(42, 67, 113, 0.3);
                }
                .icon-box.s1 .icon-main, .icon-box.s4 .icon-main {
                    box-shadow: 8px 8px 20px 0px rgba(42, 67, 113, 0.3);
                    -webkit-box-shadow: 8px 8px 20px 0px rgba(42, 67, 113, 0.3);
                    -moz-box-shadow: 8px 8px 20px 0px rgba(42, 67, 113, 0.3);
                }
                .icon-box.s3 .icon-main {
                    box-shadow: 8px 8px 30px 0px rgba(42, 67, 113, 0.3);
                    -webkit-box-shadow: 8px 8px 30px 0px rgba(42, 67, 113, 0.3);
                    -moz-box-shadow: 8px 8px 30px 0px rgba(42, 67, 113, 0.3);
                }
                .ot-testimonials .testimonial-wrap .bg-block {
                    box-shadow: 30px 30px 45px 0px rgba(42, 67, 113, 0.3);
                    -webkit-box-shadow: 30px 30px 45px 0px rgba(42, 67, 113, 0.3);
                    -moz-box-shadow: 30px 30px 45px 0px rgba(42, 67, 113, 0.3);
                }
                .team-wrap .team-social a, .team-wrap .team-social span {
                    box-shadow: 5px 5px 18px 0px rgba(42, 67, 113, 0.3);
                    -webkit-box-shadow: 5px 5px 18px 0px rgba(42, 67, 113, 0.3);
                    -moz-box-shadow: 5px 5px 18px 0px rgba(42, 67, 113, 0.3);
                }
                .process-box .number-box {
                    box-shadow: 10px 10px 30px 0px rgba(42, 67, 113, 0.3);
                    -webkit-box-shadow: 10px 10px 30px 0px rgba(42, 67, 113, 0.3);
                    -moz-box-shadow: 10px 10px 30px 0px rgba(42, 67, 113, 0.3);
                }
                .page-pagination li span, .page-pagination li a:hover {
                    box-shadow: 6px 6px 13px 0px rgba(42, 67, 113, 0.3);
                    -webkit-box-shadow: 6px 6px 13px 0px rgba(42, 67, 113, 0.3);
                    -moz-box-shadow: 6px 6px 13px 0px rgba(42, 67, 113, 0.3);
                }
                .projects-box .portfolio-info.s2 .portfolio-info-inner {
                    background-color: transparent;
                    background-image: -moz-linear-gradient(145deg, transparent, transparent);
                    background-image: -webkit-linear-gradient(145deg, transparent, transparent);
                    background-image: linear-gradient(145deg, transparent, transparent);
                }         
			';
        }

        if ( onum_get_option('cta_box_shadow_header') != '' ) {
            $color_scheme[] = '                
                /* CTA Button */
                .site-header .btn-cta-header a, .header-style-1.header-overlay .btn-cta-header a {
                    box-shadow: 12px 12px 20px 0px '.onum_get_option('cta_box_shadow_header').';
                    -webkit-box-shadow: 12px 12px 20px 0px '.onum_get_option('cta_box_shadow_header').';
                    -moz-box-shadow: 12px 12px 20px 0px '.onum_get_option('cta_box_shadow_header').';
                }
            ';
        }

        $defaults_plink = array(
            'link'   => '#ffffff',
            'hover'  => '#a5b7d2',
            'active' => '#a5b7d2',
        );
        $defaults_pbreadcrumb_link = array(
            'link'   => '#a5b7d2',
            'hover'  => '#fe4c1c',
            'active' => '#fe4c1c',
        );
        $value_color_plink = onum_get_option( 'single_post_plink_color1', $defaults_plink );
        $value_color_pbreadcrumb_link = onum_get_option( 'single_post_breadcrumb_link_color1', $defaults_pbreadcrumb_link );
        $color_scheme[] = '                
            /* Page Header Single Post */
            .single-page-header .breadcrumbs li a {color: ' . $value_color_pbreadcrumb_link['link'] . ';}
            .single-page-header.post-box .sing-page-header-content .entry-meta a {
                color: ' . $value_color_plink['link'] . ';
            }
            .single-page-header .breadcrumbs li a:hover {color: ' . $value_color_pbreadcrumb_link['hover'] . ';}
            .single-page-header.post-box .sing-page-header-content .entry-meta a:hover {
                color: ' . $value_color_plink['hover'] . ';
            }
            .single-page-header .breadcrumbs li a:active {color: ' . $value_color_pbreadcrumb_link['active'] . ';}
            .single-page-header.post-box .sing-page-header-content .entry-meta a:active {
                color: ' . $value_color_plink['active'] . ';
            }
        ';

        $body_typo_value = onum_get_option( 'body_typo', [] );
        if ( !empty( $body_typo_value['font-family'] ) && $body_typo_value['font-family'] != 'Red Hat Text' ) {            
            $color_scheme[] = '                
                /* Font Family: Red Hat Text */
                .projects-box .portfolio-info .portfolio-info-title, .elementor-element .elementor-widget-text-editor,
                .elementor-element .elementor-widget-icon-list .elementor-icon-list-item {font-family:' . $body_typo_value['font-family'] . ';}
            ';
        }

        $body_typo2_value = onum_get_option( 'body_typo2', [] );
        if ( !empty( $body_typo2_value['font-family'] ) && $body_typo2_value['font-family'] != 'Red Hat Text' ) {
            $variant = '';
             if ( isset( $body_typo2_value['variant'] ) ) { 
                $variant = 'font-weight:' . $body_typo2_value['variant'] . ';'; 
            };
            $color_scheme[] = '                
                /* Font Family: Red Hat Display */
                h1, h2, h3, h4, h5, h6,
                div.elementor-widget.elementor-widget-heading .elementor-heading-title,
                .projects-style-3 .projects-box .portfolio-info .portfolio-info-title,
                blockquote,
                .font-second,
                .octf-btn,
                .btn-readmore a,
                .header-topbar,
                .topbar_menu ul li a,
                .topbar_languages select,
                .main-navigation ul,
                .cart-contents .count,
                .header_mobile,
                .page-header,
                .post-box .post-cat a,
                .post-box .entry-meta,
                .post-box .link-box a,
                .page-pagination,
                .blog-post .share-post a,
                .post-nav,
                .sl-wrapper .sl-count, .sl-wrapper .sl-text,
                .comments-area .comment-item .comment-meta .comment-time,
                .comments-area .comment-item .comment-meta .comment-reply-link,
                .comment-form .logged-in-as,
                .widget-area .widget ul:not(.recent-news) > li,
                .widget .recent-news li,
                .service-box .big-number,
                .service-box-s2 .number-box,
                .circle-progress .inner-bar > span,
                .ot-pricing-table .title-table,
                .ot-counter,
                .process-box .number-box,
                .video-popup > span,
                div .custom.tp-bullets .tp-bullet,
                .projects-box .portfolio-info .portfolio-cates,
                .projects-style-2 .projects-box .portfolio-info h5 a,
                .project_filters li a,
                .ot-countdown li span,                
                .site-footer,
                .footer-menu ul li a, .woocommerce ul.products li.product, .woocommerce-page ul.products li.product,
                .woocommerce table.shop_table, .woocommerce .quantity .qty, .cart_totals h2,
                #add_payment_method .cart-collaterals .cart_totals table td, #add_payment_method .cart-collaterals .cart_totals table th,.woocommerce-cart .cart-collaterals .cart_totals table td, .woocommerce-cart .cart-collaterals .cart_totals table th,.woocommerce-checkout .cart-collaterals .cart_totals table td, .woocommerce-checkout .cart-collaterals .cart_totals table th,
                .product-categories > li,
                .woocommerce ul.product_list_widget li a:not(.remove),
                .woocommerce .widget_shopping_cart .cart_list .quantity,
                .woocommerce .widget_shopping_cart .total strong,.woocommerce.widget_shopping_cart .total strong,
                .woocommerce .widget_shopping_cart .total .woocommerce-Price-amount,.woocommerce.widget_shopping_cart .total .woocommerce-Price-amount,
                .woocommerce-mini-cart__buttons a.button.wc-forward,
                .woocommerce .woocommerce-widget-layered-nav-list,
                .woocommerce .widget_price_filter .price_slider_amount,
                .woocommerce .widget_price_filter .price_slider_amount button.button,
                .woocommerce #respond input#submit.disabled, .woocommerce #respond input#submit:disabled,.woocommerce #respond input#submit:disabled[disabled], .woocommerce a.button.disabled,.woocommerce a.button:disabled, .woocommerce a.button:disabled[disabled], .woocommerce button.button.disabled,.woocommerce button.button:disabled, .woocommerce button.button:disabled[disabled],.woocommerce input.button.disabled, .woocommerce input.button:disabled, .woocommerce input.button:disabled[disabled],.woocommerce #respond input#submit, .woocommerce a.button,.woocommerce button.button, .woocommerce input.button,.woocommerce #respond input#submit.alt, .woocommerce a.button.alt,.woocommerce button.button.alt, .woocommerce input.button.alt,
                .product_meta > span,
                .woocommerce-review-link,
                .woocommerce div.product .entry-summary p.price,.woocommerce div.product .entry-summary span.price,
                .woocommerce div.product .woocommerce-tabs ul.tabs li a, .ot-heading .heading__text_stroke {font-family:' . $body_typo2_value['font-family'] . '; ' . $variant . '}
            ';
        }

        if( ! empty( $color_scheme ) ){
			echo '<style id="onum-inline-styles" type="text/css">'. implode( ' ', $color_scheme ) .'</style>';
		}
    }
}
add_action('wp_head', 'onum_color_scheme');