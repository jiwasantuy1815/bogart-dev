<?php
$pheader_bgimage = '';
$pheader_bgcolor = '';
if ( ! function_exists( 'rwmb_meta' ) ) {
    $pheader_bgimage .= onum_get_option( 'single_post_bg_top_page' );
} else {
    if ( rwmb_meta( 'pheader_bgcolor_overlay' ) ) {
		$pheader_bgcolor .= 'style="background: ' . rwmb_meta( 'pheader_bgcolor_overlay' ) . '"';		
	}

	$images = rwmb_meta( 'pheader_bg_image', 'type=image');

    if ( !$images ) {
        $pheader_bgimage .= onum_get_option( 'single_post_bg_top_page' );
    } else {
        foreach ( $images as $image ) {
            $pheader_bgimage .= $image['full_url'];
            break;
        }
    }
}
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	
<div class="single-page-header post-box" <?php if ( $pheader_bgimage != '' ) { ?> style="background-image: url(<?php echo esc_url( $pheader_bgimage ); ?>);" <?php } ?> >
	<div class="single-bg-overlay" <?php echo $pheader_bgcolor; ?> ></div>
    <div class="sing-page-header-content">
    	<div class="container">
			<div class="row">	
				<div class="col-md-12">
				    <?php onum_posted_in(); ?>
				    <div class="entry-header">			        
				        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				        <?php if ( 'post' === get_post_type() ) : if ( onum_get_option( 'post_entry_meta' ) ) { ?>
					        <div class="entry-meta">
					            <?php onum_post_meta(); ?>
					            <?php 
						            if ( onum_get_option('single_like_post') != false ){
						            	echo do_shortcode( '[otfliker]' ); 
						            }
					            ?>
					        </div><!-- .entry-meta -->
				        <?php } endif; ?>
				        <?php 
                            if ( function_exists('onum_breadcrumbs') && onum_get_option('single_breadcrumbs') == true ):
                                echo onum_breadcrumbs();
                            endif;
                        ?>
				    </div>
			    </div>
	    	</div>
		</div>
    </div>
</div>
<?php endwhile; endif; // End of the loop. ?>

<div class="entry-content">
	<div class="container">
		<div class="row">
			<div id="primary" class="content-area <?php onum_content_columns(); ?>">
				<main id="main" class="site-main">
									
					<?php
						if ( have_posts() ) : while ( have_posts() ) : the_post();	
												
							get_template_part( 'template-parts/content', 'single' );

							// If comments are open or we have at least one comment, load up the comment template.
							if ( comments_open() || get_comments_number() ) :
								comments_template();
							endif;

						endwhile; endif; // End of the loop.
					?>

				</main><!-- #main -->
			</div><!-- #primary -->
			
			<?php get_sidebar(); ?>
		</div>
	</div>	
</div>