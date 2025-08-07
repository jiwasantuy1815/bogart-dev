<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ONUM
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php
    // You can start editing here -- including this comment!
    if ( have_comments() ) : ?>

        <h2 class="comments-title"><?php comments_number( esc_html__('Comments (0)', 'onum'), esc_html__('Comment (1)', 'onum'), esc_html__(  'Comments (%)', 'onum') ); ?></h2>

        <ol class="comment-list">
            <?php wp_list_comments('callback=onum_comment_list'); ?>
        </ol><!-- .comment-list -->

        <?php
        the_comments_navigation();

        // If comments are closed and there are comments, let's leave a little note, shall we?
        if ( ! comments_open() ) :
            ?>
            <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'onum' ); ?></p>
        <?php
        endif;

    endif; // Check for have_comments().

    // Custom comments_args here: https://codex.wordpress.org/Function_Reference/comment_form
    $commenter = wp_get_current_commenter();
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );

    $comments_args = array(
        'title_reply'   => esc_html__('Leave a comment', 'onum'),
        'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="4" aria-required="true" placeholder="'. esc_attr__( 'Comment*', 'onum' ) .'" required></textarea></p>',
        'fields'        => apply_filters( 'comment_form_default_fields', array(

            'website'   => '<p class="comment-form-website"><input id="website" name="website" type="text" value="' . esc_attr(  $commenter['comment_author_url'] ) .'" size="30" placeholder="'. esc_attr__( 'Website', 'onum' ) .'" /></p>',

            'author'    => '<div class="row"><p class="comment-form-author col-md-6"><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .'" size="30" placeholder="'. esc_attr__( 'Name *', 'onum' ) .'" required /></p>',

            'email'     => '<p class="comment-form-email col-md-6"><input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .'" size="30" placeholder="'. esc_attr__( 'Email *', 'onum' ) .'" required /></p></div>',

        )),
        'class_submit' => 'octf-btn octf-btn-secondary',
        'format'       => 'xhtml'
    );
    comment_form( $comments_args );
    ?>

</div><!-- #comments -->