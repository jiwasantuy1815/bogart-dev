<?php
/**
 * The template for displaying all portfolios
 *
 * This is the template that displays all portfolio by default.
 * Please note that this is the WordPress construct of portfolios
 * and that other 'portfolios' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Engitech
 */

get_header();

    while ( have_posts() ) : the_post();
        the_content();                            
    endwhile; 
?>

<div class="entry-content project-bottom">
    <div class="container">
        <div id="primary" class="content-area">
            <main id="main" class="site-main">
                <div class="single-portfolio-navigation">
                    <?php 
                        echo '<div class="post-nav clearfix">';
                        echo '<div class="post-prev">';
                        previous_post_link( '%link', '<span class="flaticon-arrow-pointing-to-left"></span>'.esc_html__( 'Previous', 'onum' ) );
                        echo '</div><div class="post-next">';
                        next_post_link( '%link', esc_html__( 'Next', 'onum' ).'<span class="flaticon-arrow-pointing-to-right"></span>' );
                        echo '</div></div>';
                    ?>
                </div>

                <?php if( onum_get_option('pf_related_switch') ) { ?>
                <div class="portfolio-related-posts-wrap">
                    <div class="portfolio-related-title-wrap">
                        <h2 class="portfolio-related-title">
                            <?php 
                            if ( onum_get_option('related_prj_text') != '' ) {
                                echo onum_get_option('related_prj_text');
                            } else {
                                esc_html_e( 'Related Projects', 'onum' );
                            }
                            ?>                                
                        </h2>
                    </div>
                    <div class="portfolio-related-posts projects-grid <?php if( onum_get_option('related_prj_style') == 'style2' ){ echo 'projects-style-2'; } ?>">                    
                        <?php 
                        // get the custom post type's taxonomy terms                    
                        $custom_taxterms = wp_get_object_terms( $post->ID, 'portfolio_cat', array('fields' => 'ids') );
                        // arguments
                        $args = array(
                            'post_type' => 'ot_portfolio',
                            'post_status' => 'publish',
                            'posts_per_page' => 3, // you may edit this number
                            'ignore_sticky_posts' => 1,
                            'orderby' => 'rand',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'portfolio_cat',
                                    'field' => 'id',
                                    'terms' => $custom_taxterms
                                )
                            ),
                            'post__not_in' => array ($post->ID),
                        );
                        $second_query = new WP_Query( $args );

                        //Loop through posts and display...
                        if ( $second_query->have_posts() ) : while ( $second_query->have_posts() ) : $second_query->the_post(); 
                            /*
                             * Include the Post-Type-specific template for the content.
                             * If you want to override this in a child theme, then include a file
                             * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                             */
                            get_template_part( 'template-parts/content', get_post_type() );
                        endwhile; wp_reset_query(); endif; 
                        ?>
                    </div>
                </div>
                <?php } ?>
            </main><!-- #main -->
        </div>
    </div><!-- #primary -->            
</div>

<?php
get_footer();