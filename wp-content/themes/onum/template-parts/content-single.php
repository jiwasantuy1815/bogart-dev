<?php
/**
 * Template part for displaying single post content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ONUM
 */
    $format = get_post_format();
    $link_video  = get_post_meta(get_the_ID(),'post_video', true);
    $link_audio  = get_post_meta(get_the_ID(),'post_audio', true);
    $link_link   = get_post_meta(get_the_ID(),'post_link', true);
    $text_link   = get_post_meta(get_the_ID(),'text_link', true);
    $quote_text  = get_post_meta(get_the_ID(),'post_quote', true);
    $quote_name  = get_post_meta(get_the_ID(),'quote_name', true);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post-box blog-post'); ?>>
    <div class="single-post-inner">
        <?php if( $format == 'gallery' ) { ?>
            <div class="entry-media">
                <div class="gallery-post" <?php if ( is_rtl() ) { echo'dir="rtl"'; } ?>>
                <?php if( function_exists( 'rwmb_meta' ) ) { ?>
                    <?php $images = rwmb_meta( 'post_gallery', array( 'size' => 'full' ) ); ?>
                    <?php if($images){ ?>              
                        <?php foreach ( $images as $image ) {  ?>      
                            <div>
                                <div class="item-image">
                                    <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo esc_attr( $image['width'] ); ?>" height="<?php echo esc_attr( $image['height'] ); ?>">
                                </div>
                            </div>                    
                        <?php } ?>                
                    <?php } ?>
                <?php } ?>
                </div>
            </div>          

        <?php }elseif( $format == 'image' ) { ?>

            <div class="entry-media">
                <?php if( function_exists( 'rwmb_meta' ) ) { ?>
                    <?php $images = rwmb_meta( 'post_image', array( 'size' => 'full' ) ); ?>
                    <?php if($images){ ?>              
                        <?php foreach ( $images as $image ) {  ?> 
                            <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo esc_attr( $image['width'] ); ?>" height="<?php echo esc_attr( $image['height'] ); ?>">
                            
                        <?php } ?>                
                    <?php } ?>
                <?php } ?>
            </div>
            
        <?php }elseif( $format == 'audio' ){ ?>

            <div class="audio-box">
                <iframe scrolling="no" frameborder="no" src="<?php echo esc_url( $link_audio ); ?>"></iframe>
            </div>

        <?php }elseif( $format == 'video' ){ ?>

            <div class="entry-media">
                <?php if( function_exists( 'rwmb_meta' ) ) { ?>
                    <?php $images = rwmb_meta( 'bg_video', array( 'size' => 'full' ) ); ?>
                    <?php if($images){ ?>             
                        <a class="btn-play" href="<?php echo esc_url( $link_video ); ?>">
                            <i class="fas fa-play"></i>
                        </a> 
                        <?php  foreach ( $images as $image ) {  ?>
                            <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo esc_attr( $image['width'] ); ?>" height="<?php echo esc_attr( $image['height'] ); ?>">
                        <?php } ?>                
                    <?php } ?>
                <?php } ?>
            </div>

        <?php }elseif( $format == 'link' ){ ?>

            <div class="link-box">
                <i class="flaticon-link"></i>
                <a href="<?php echo esc_url( $link_link ); ?>"><?php echo esc_html( $text_link ); ?></a>
            </div>

        <?php }elseif( $format == 'quote' ){ ?>

            <div class="quote-box font-second">
                <i class="flaticon-quotation"></i>
                <div class="quote-text">
                    <?php echo esc_html( $quote_text ); ?>
                    <span><?php echo esc_html( $quote_name ); ?></span>
                </div>
            </div>

        <?php }elseif ( has_post_thumbnail() ) { ?>

            <div class="entry-media">
                <?php the_post_thumbnail( 'full' ); ?>
            </div>
            
        <?php } ?>
    </div>
    <div class="inner-post no-padding-top">
        <div class="entry-summary">
            <?php
                the_content(sprintf(
                    wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                        __('Continue reading<span class="screen-reader-text"> "%s"</span>', 'onum'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    get_the_title()
                ));

                wp_link_pages(array(
                    'before' => '<div class="page-links">' . esc_html__('Pages:', 'onum'),
                    'after' => '</div>',
                ));
            ?>
        </div>
        <div class="entry-footer clearfix">
            <?php onum_entry_footer(); ?>
        </div>
        <?php if ( onum_get_option('author_box') ) onum_author_info_box(); ?>
        <?php if ( onum_get_option('post_nav') ) onum_single_post_nav(); ?>
        <?php if ( onum_get_option('related_post') ) onum_related_posts(); ?>
    </div>
</article>