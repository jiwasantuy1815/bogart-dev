<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ONUM
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post-box masonry-post-item'); ?>>
    <div class="post-inner">
        <?php if ( has_post_thumbnail() ) { ?>
            <div class="entry-media">
                <?php onum_posted_in(); ?>
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail(); ?>
                </a>
            </div>        
        <?php } ?>
        <div class="inner-post">
            <header class="entry-header">
                <?php if ( 'post' === get_post_type() ) : ?>
                <div class="entry-meta">
                    <?php onum_post_meta(); ?>
                </div><!-- .entry-meta -->
                <?php endif; ?>

                <?php the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' ); ?>

            </header><!-- .entry-header -->

            <?php if ( true == onum_get_option( 'excerpt_switch', true ) ) { ?>
                <div class="entry-summary the-excerpt">
                    <?php echo onum_excerpt_length(); ?>
                </div><!-- .entry-content -->
            <?php } ?>
        </div>
    </div>
</article>
