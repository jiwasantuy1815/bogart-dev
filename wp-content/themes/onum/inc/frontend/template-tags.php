<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package ONUM
 */

if ( ! function_exists( 'onum_posted_on' ) ) :
    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    function onum_posted_on() {

        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
        }

        $time_string = sprintf( $time_string,
            esc_attr( get_the_date( DATE_W3C ) ),
            esc_html( get_the_date() )
        );

        $posted_on = sprintf(
        /* translators: %s: post date. */
            esc_html_x( '%s', 'post date', 'onum' ),
            '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark"><i class="flaticon-clock"></i> ' . $time_string . '</a>'
        );

        echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

    };
endif;

if ( ! function_exists( 'onum_posted_by' ) ) :
    /**
     * Prints HTML with meta information for the current author.
     */
    function onum_posted_by() {

        $byline = sprintf(
        /* translators: %s: post author. */
            esc_html_x( '%s', 'post author', 'onum' ),
            '<a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '"><i class="flaticon-user"></i> ' . esc_html( get_the_author() ) . '</a>'
        );

        echo '<span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

    };
endif;

if ( ! function_exists( 'onum_posted_in' ) ) :
    /**
     * Prints HTML with meta information for the current author.
     */
    function onum_posted_in() {
        $categories_list = get_the_category_list( esc_html__( ' ', 'onum' ) );
        $posted_in = '';
        if ( $categories_list ) {
            /* translators: 1: list of categories. */
            $posted_in = sprintf( esc_html__( '%1$s', 'onum' ), $categories_list ); // WPCS: XSS OK.
        }

        echo '<div class="post-cat"><span class="posted-in"> ' . $posted_in . '</span></div>'; // WPCS: XSS OK.

    };
endif;

if ( ! function_exists( 'onum_post_meta' ) ) :
    /**
     * Prints HTML with meta information for the current author.
     */
    function onum_post_meta() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
        }

        $time_string = sprintf( $time_string,
            esc_attr( get_the_date( DATE_W3C ) ),
            esc_html( get_the_date() )
        );

        $posted_on = sprintf(
        /* translators: %s: post date. */
            esc_html_x( '%s', 'post date', 'onum' ),
            '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark"><i class="flaticon-clock"></i> ' . $time_string . '</a>'
        );

        $byline = sprintf(
        /* translators: %s: post author. */
            esc_html_x( '%s', 'post author', 'onum' ),
            '<a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '"><i class="flaticon-user"></i> ' . esc_html( get_the_author() ) . '</a>'
        );

        $categories_list = get_the_category_list( esc_html__( ', ', 'onum' ) );
        if ( $categories_list ) {
            /* translators: 1: list of categories. */
            $posted_in = sprintf( esc_html__( '%1$s', 'onum' ), $categories_list ); // WPCS: XSS OK.
        }

        $comment_num = sprintf(
            /* translators: %s: post author. */
            esc_html_x( '%s', 'post comment', 'onum' ),
            '<a href="' .get_comments_link(). '"><i class="flaticon-chat"></i> '. get_comments_number_text( esc_html__('0 Comments', 'onum'), esc_html__('1 Comment', 'onum'), esc_html__(  '% Comments', 'onum') ). '</a>' );

        /* translators: used between list items, there is a space after the comma */
        $tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'onum' ) );
        if ( $tags_list ) {
            /* translators: 1: list of tags. */
            $tag_with = sprintf( '<span class="tags-links">' . esc_html__( '%1$s', 'onum' ) . '</span>', $tags_list ); // WPCS: XSS OK.
        }

        $metas = onum_get_option( 'post_entry_meta' );
        if ( ! empty( $metas ) ) : 
            if( in_array('author', $metas) ) echo '<span class="byline">' . $byline . '</span>';
            if( in_array('date', $metas) ) echo '<span class="posted-on">' . $posted_on . '</span>';
            if( in_array('comm', $metas) ) echo '<span class="comment-num">' . $comment_num . '</span>';
        endif;
    };
endif;

if ( ! function_exists( 'onum_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function onum_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ' ', 'list item separator', 'onum' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<div class="tagcloud">' . esc_html__( '%1$s', 'onum' ) . '</div>', $tags_list ); // WPCS: XSS OK.
			}

            $share = onum_get_option( 'post_socials' );
            if ( ! empty( $share ) ) : 
                echo '<div class="share-post">';

                if( in_array('twit', $share) ) echo '<a class="twit" target="_blank" href="https://twitter.com/intent/tweet?text=' .get_the_title(). '&url=' .get_the_permalink(). '" title="Twitter"><i class="fab fa-twitter"></i>Twitter</a>';
                if( in_array('face', $share) ) echo '<a class="face" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' .get_the_permalink(). '" title="Facebook"><i class="fab fa-facebook-f"></i>Facebook</a>';
                if( in_array('pint', $share) ) echo '<a class="pint" target="_blank" href="https://www.pinterest.com/pin/create/button/?url=' .get_the_permalink(). '&description=' .get_the_title(). '" title="Pinterest"><i class="fab fa-pinterest-p"></i>Pinterest</a>';
                if( in_array('link', $share) ) echo '<a class="linked" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=' .get_the_permalink(). '&title=' .get_the_title(). '&summary=' .esc_url( get_home_url('/') ). '&source=' .get_bloginfo( 'name' ). '" title="LinkedIn"><i class="fab fa-linkedin-in"></i>LinkedIn</a>';
                if( in_array('google', $share) ) echo ' <a class="google" target="_blank" href="https://plus.google.com/share?url=' .get_the_permalink(). '" title="Google Plus"><i class="fab fa-google-plus-g"></i>Google Plus</a>';
                if( in_array('tumblr', $share) ) echo ' <a class="tumblr" target="_blank" href="http://www.tumblr.com/share/link?url=' .get_the_permalink(). '&name=' .get_the_title(). '&description=' .get_the_excerpt(). '" title="Tumblr"><i class="fab fa-tumblr"></i>Tumblr</a>';
                if( in_array('reddit', $share) ) echo '<a class="reddit" href="http://reddit.com/submit?url=' .get_the_permalink(). '&title=' .get_the_title(). '" target="_blank" title="Reddit"><i class="fab fa-reddit-alien" aria-hidden="true"></i>Reddit</a>';
                if( in_array('vk', $share) ) echo '<a class="vk" href="http://vk.com/share.php?url=' .get_the_permalink(). '" target="_blank" title="VK"><i class="fab fa-vk"></i>VK</a>';

                echo '</div>';
            endif;
		}
	};
endif;

/** Single Post Navigation**/
if ( ! function_exists( 'onum_single_post_nav' ) ) :

    function onum_single_post_nav(){
        if ( 'post' === get_post_type() ) {
            echo '<div class="post-nav clearfix">';
            echo '<div class="post-prev">';
            previous_post_link( '%link', '<span class="flaticon-arrow-pointing-to-left"></span>'.esc_html__( 'Previous', 'onum' ) );
            echo '</div><div class="post-next">';
            next_post_link( '%link', esc_html__( 'Next', 'onum' ).'<span class="flaticon-arrow-pointing-to-right"></span>' );
            echo '</div></div>';
        }
    };
endif;

/** Posts Navigation **/
if ( ! function_exists( 'onum_posts_navigation' ) ) :
    function onum_posts_navigation($prev = '<i class="flaticon-arrow-pointing-to-left"></i>', $next = '<i class="flaticon-arrow-pointing-to-right
"></i>', $pages='') {
        global $wp_query, $wp_rewrite;
        $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
        if($pages==''){
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if(!$pages)
            {
                $pages = 1;
            }
        }
        $pagination = array(
            'base'          => str_replace( 999999999, '%#%', get_pagenum_link( 999999999 ) ),
            'format'        => '',
            'current'       => max( 1, get_query_var('paged') ),
            'total'         => $pages,
            'prev_text'     => is_rtl() ? $next : $prev,
            'next_text'     => is_rtl() ? $prev : $next,
            'type'          => 'list',
            'end_size'      => 3,
            'mid_size'      => 3
        );
        $return =  paginate_links( $pagination );
        echo str_replace( "<ul class='page-numbers'>", '<ul class="page-pagination">', $return );
    };
endif;

/** Excerpt Section Blog Post **/
if ( ! function_exists( 'onum_excerpt' ) ) :
function onum_excerpt($limit) {

    $excerpt = explode( ' ', get_the_excerpt(), $limit );
    
    if ( count( $excerpt ) >= $limit ) {
        array_pop( $excerpt );
        $excerpt = implode(" ", $excerpt ).'...';
    } else {
        $excerpt = implode(" ", $excerpt );
    }
    $excerpt = preg_replace('`[[^]]*]`','',$excerpt);

    return $excerpt;
};
endif;

/**** Change length of the excerpt ****/
if ( ! function_exists( 'onum_excerpt_length' ) ) :
    function onum_excerpt_length() {

        if ( onum_get_option( 'excerpt_length' ) ){
            $limit = onum_get_option( 'excerpt_length' );
        } else {
            $limit = 30;
        }

        $excerpt = explode( ' ', get_the_excerpt(), $limit );

        if ( count( $excerpt ) >= $limit ) {
            array_pop( $excerpt );
            $excerpt = implode(" ", $excerpt).'...';
        } else {
            $excerpt = implode(" ", $excerpt);
        }

        $excerpt = preg_replace( '`[[^]]*]`', '', $excerpt);

        return $excerpt;
    };
endif;

//Custom comment list
if ( ! function_exists( 'onum_comment_list' ) ) :
    function onum_comment_list($comment, $args, $depth) {

        $GLOBALS['comment'] = $comment; ?>

        <li id="comment-<?php comment_ID(); ?>" class="comment-item">
            <article class="comment-wrap clearfix">

                <div class="gravatar">
                    <?php echo get_avatar( $comment, 80 ); ?>
                </div>

                <div class="comment-content">
                    <div class="comment-meta">
                        <h6 class="comment-author"><?php printf(__('%s','onum'), get_comment_author()) ?></h6>
                        <span class="comment-time"><?php comment_time( get_option( 'date_format' ) ); ?></span>
                        <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
                    </div>
                    <div class="comment-text">
                        <?php if ($comment->comment_approved == '0'){ ?>
                            <em><?php esc_html_e('Your comment is awaiting moderation.','onum') ?></em>
                        <?php }else{ ?>
                            <?php comment_text() ?>
                        <?php } ?>
                    </div>
                </div>

            </article>
        </li>

        <?php
    };
endif;

//Generate custom search form
function onum_search_form( $form ) {
    $form = '<form role="search" method="get" class="search-form" action="' . esc_url( home_url( '/' ) ) . '" >
        <input type="search" class="search-field" placeholder="' . esc_attr__( 'Search&hellip;', 'onum' ) . '" value="' . get_search_query() . '" name="s" />
    	<button type="submit" class="search-submit"><i class="flaticon-search"></i></button>
    </form>';

    return $form;
};
add_filter( 'get_search_form', 'onum_search_form' );

//Add span to category post count
function onum_cat_count_span($links) {
    $links = str_replace('</a> (', '</a> <span class="posts-count">(', $links);
    $links = str_replace(')', ')</span>', $links);
    return $links;
};
add_filter('wp_list_categories', 'onum_cat_count_span');

//Add span to archive post count
function onum_archive_count_span($links) {
    $links = str_replace('</a>&nbsp;(', '</a> <span class="posts-count">(', $links);
    $links = str_replace(')', ')</span>', $links);
    return $links;
};
add_filter('get_archives_link', 'onum_archive_count_span');


/** Add Contact Methods in the User Profile **/
function onum_user_contact_methods( $user_contact ) {
    $user_contact['facebook']   = esc_html__( 'Facebook URL', 'onum' );
    $user_contact['skype']      = esc_html__( 'Skype Username', 'onum' );
    $user_contact['twitter']    = esc_html__( 'Twitter Handle', 'onum' );
    $user_contact['youtube']    = esc_html__( 'Youtube Channel', 'onum' );
    $user_contact['linkedin']   = esc_html__( 'LinkedIn', 'onum' );
    $user_contact['googleplus'] = esc_html__( 'Google +', 'onum' );
    $user_contact['pinterest']  = esc_html__( 'Pinterest', 'onum' );
    $user_contact['instagram']  = esc_html__( 'Instagram', 'onum' );
    $user_contact['github']     = esc_html__( 'Github Profile', 'onum' ); 
    return $user_contact; 
};
add_filter( 'user_contactmethods', 'onum_user_contact_methods' );

function onum_author_info_box() {

    global $post;

    $author_details = '';
    // Get author's display name - NB! changed display_name to first_name. Error in code.
    $display_name = get_the_author_meta( 'display_name', $post->post_author );

    // If display name is not available then use nickname as display name
    if ( empty( $display_name ) )
    $display_name = get_the_author_meta( 'nickname', $post->post_author );

    // Get author's biographical information or description
    $user_description   = get_the_author_meta( 'user_description', $post->post_author );
    $user_twitter       = get_the_author_meta('twitter', $post->post_author);
    $user_facebook      = get_the_author_meta('facebook', $post->post_author);
    $user_skype         = get_the_author_meta('skype', $post->post_author);
    $user_linkedin      = get_the_author_meta('linkedin', $post->post_author);
    $user_youtube       = get_the_author_meta('youtube', $post->post_author);
    $user_googleplus    = get_the_author_meta('googleplus', $post->post_author);
    $user_pinterest     = get_the_author_meta('pinterest', $post->post_author);
    $user_instagram     = get_the_author_meta('instagram', $post->post_author);
    $user_github        = get_the_author_meta('github', $post->post_author);

    // Get link to the author archive page
    $user_posts = get_author_posts_url( get_the_author_meta( 'ID' , $post->post_author));
    if ( ! empty( $display_name ) )
    // Author avatar - - the number 90 is the px size of the image.
    $author_details .= '<div class="author-image">' . get_avatar( get_the_author_meta('ID') , 250 ) . '</div>';
    $author_details .= '<div class="author-info">';
    $author_details .= '<p class="title text-primary font-second">' . esc_html__('Author', 'onum'). '</p>';
    $author_details .= '<h6>' . $display_name . '</h6>';
    $author_details .= '<p class="des">' . get_the_author_meta( 'description' ). '</p>';
    $author_details .= '<div class="author-socials">';

    // Check if author has Twitter in their profile
    if ( ! empty( $user_twitter ) ) {
        $author_details .= ' <a href="' . $user_twitter .'" target="_blank" rel="nofollow" title="Twitter" class="tooltip"><i class="fab fa-twitter"></i> </a>';
    }

    if ( ! empty( $user_facebook ) ) {
        $author_details .= ' <a href="' . $user_facebook .'" target="_blank" rel="nofollow" title="Facebook" class="tooltip"><i class="fab fa-facebook-f"></i> </a>';
    }

    if ( ! empty( $user_skype ) ) {
        $author_details .= ' <a href="' . $user_skype .'" target="_blank" rel="nofollow" title="Username paaljoachim Skype" class="tooltip"><i class="fab fa-skype"></i> </a>';
    }

    if ( ! empty( $user_linkedin ) ) {
        $author_details .= ' <a href="' . $user_linkedin .'" target="_blank" rel="nofollow" title="LinkedIn" class="tooltip"><i class="fab fa-linkedin-in"></i> </a>';
    }

    if ( ! empty( $user_youtube ) ) {
        $author_details .= ' <a href="' . $user_youtube .'" target="_blank" rel="nofollow" title="Youtube" class="tooltip"><i class="fab fa-youtube"></i> </a>';
    }

    if ( ! empty( $user_googleplus ) ) {
        $author_details .= ' <a href="' . $user_googleplus .'" target="_blank" rel="nofollow" title="Google+" class="tooltip"><i class="fab fa-google-plus"></i> </a>';
    }

    if ( ! empty( $user_pinterest ) ) {
        $author_details .= ' <a href="' . $user_pinterest .'" target="_blank" rel="nofollow" title="Pinterest" class="tooltip"><i class="fab fa-pinterest"></i> </a>';
    }

    if ( ! empty( $user_instagram ) ) {
        $author_details .= ' <a href="' . $user_instagram .'" target="_blank" rel="nofollow" title="Instagram" class="tooltip"><i class="fab fa-instagram"></i> </a>';
    }

    if ( ! empty( $user_github ) ) {
        $author_details .= ' <a href="' . $user_github .'" target="_blank" rel="nofollow" title="Github" class="tooltip"><i class="fab fa-github"></i> </a>';
    }

    $author_details .= '</div></div>';

    // Pass all this info to post content 
    echo '<div class="author-bio" >' . $author_details . '</div>';
}
/** Allow HTML in author bio section **/
remove_filter('pre_user_description', 'wp_filter_kses');

/** Related Posts **/
function onum_related_posts() {

    global $post;

    $related = get_posts( array( 'category__in' => wp_get_post_categories($post->ID), 'numberposts' => 2, 'post__not_in' => array($post->ID) ) );
    if( $related ) : 

    echo '<div class="related-posts">';
    echo '<h2>'.esc_html__( 'Related Posts', 'onum' ).'</h2>';
    echo '<div class="row">';
    foreach( $related as $post ) {
    setup_postdata($post); ?>
    
    <div class="col-md-6">
        <div class="post-box post-item">
            <div class="post-inner">
                <?php if ( has_post_thumbnail() ) { ?>
                <div class="entry-media">
                    <?php onum_posted_in(); ?>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'onum-blog-grid-post-thumbnail' ); ?></a>
                </div>
                <?php } ?>
                <div class="inner-post">
                    <?php if( ! has_post_thumbnail() ) onum_posted_in(); ?>
                    <div class="entry-header">

                        <?php if ( 'post' === get_post_type() ) : if ( onum_get_option( 'post_entry_meta' ) ) { ?>
                        <div class="entry-meta">
                            <?php onum_posted_by(); onum_posted_on(); ?>
                        </div><!-- .entry-meta -->
                        <?php } endif; ?>

                        <?php the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' ); ?>

                    </div><!-- .entry-header -->

                    <div class="the-excerpt">
                        <?php echo onum_excerpt(15); ?>
                    </div><!-- .entry-content -->
                </div>
            </div>
        </div>
    </div>

    <?php } wp_reset_postdata();

    echo '</div>';
    echo '</div>';

    endif;
};

/** Custom widget recent post **/
require get_template_directory() . '/inc/frontend/widgets/recent-posts.php';
require get_template_directory() . '/inc/frontend/widgets/author-widget.php';