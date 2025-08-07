<?php
if ( ! function_exists( 'onum_page_header' ) ) {
    function onum_page_header (){
        $pheader = '';

        if ( function_exists('rwmb_meta') ) {
            if ( is_page() || is_singular('ot_portfolio') ) {
                $pheader = rwmb_meta('pheader_switch');
            }
            if( is_home() || is_archive() || is_search() ){
                $pheader = rwmb_meta('pheader_switch', "type=switch", get_option( 'page_for_posts' ));
            }
            if( class_exists( 'woocommerce' ) ){
                if( is_shop() || is_product_category() || is_product_tag() || is_product() ){
                    $pheader = rwmb_meta('pheader_switch', "type=switch", get_option( 'woocommerce_shop_page_id' ));
                }
            }            
        }

        if( ! $pheader || is_404() || is_singular('post') ){
            return;
        }

        if( ! onum_get_option( 'pheader_switch' ) && ! $pheader ) {
            return;
        }else{
            $bg     = '';
            $title  = '';
            $output = array();

            if ( is_home() ) {
                $title = get_the_title(get_option('page_for_posts'));
            } elseif ( is_search() ) {
                $title = esc_html__('Search Results for: ', 'onum') . get_search_query();
            } elseif ( is_archive() ) {
                $title = get_the_archive_title();
            } else {
                $title = get_the_title();
            }
            
            if ( ! function_exists( 'rwmb_meta' ) ) {
                $bg = onum_get_option( 'pheader_img' );
            } else {
                if ( is_home() ) {
                    $images = rwmb_meta('pheader_bg_image', "type=image", get_option( 'page_for_posts' ));
                } elseif ( class_exists( 'woocommerce' ) && is_shop() ){
                    $images = rwmb_meta('pheader_bg_image', "type=image", get_option( 'woocommerce_shop_page_id' ));
                } else {
                    $images = rwmb_meta('pheader_bg_image', "type=image");
                }
                if ( ! $images ) {
                    $bg = onum_get_option( 'pheader_img' );
                } else {
                    foreach ( $images as $image ) {
                        $bg = $image['full_url'];
                        break;
                    }
                }
            }

            if ($title) {
                $output[] = sprintf('%s', $title);
            }
        ?>        
            <div class="page-header dtable <?php echo esc_attr( onum_get_option( 'pheader_align' ) ); ?>" <?php if ($bg) { ?> style="background-image: url(<?php echo esc_url($bg); ?>);" <?php } ?>>
                <div class="dcell">
                    <div class="container">
                        <?php if( class_exists( 'woocommerce' ) && is_woocommerce() ) { ?>
                            <?php if( !is_product() ){ ?>
                                <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
                                    <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
                                <?php endif; ?>                            
                            <?php }else{ ?>
                                <h2 class="page-title"><?php echo esc_html( onum_get_option( 'page_title_product' ) ); ?></h2>
                            <?php } ?>    
                            <?php do_action( 'onum_woocommerce_breadcrumb' ); ?>
                        <?php }else{ ?>
                            <h1 class="page-title"><?php echo implode('', $output); ?></h1>
                        <?php 
                            if ( function_exists('onum_breadcrumbs') && onum_get_option('breadcrumbs')):
                                echo onum_breadcrumbs();
                            endif;
                        } ?>
                    </div>
                </div>
            </div>
        <?php
        }
    }
}