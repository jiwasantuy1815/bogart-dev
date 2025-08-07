<?php

//Admin Style
if ( ! function_exists( 'onum_custom_wp_admin_style' ) ) :
    function onum_custom_wp_admin_style() {
        wp_register_style( 'onum_custom_wp_admin_css', get_template_directory_uri() . '/inc/backend/css/admin-style.css', false, '1.0.0' );
        wp_enqueue_style( 'onum_custom_wp_admin_css' );

        wp_enqueue_script( 'onum_custom_wp_admin_js', get_template_directory_uri()."/inc/backend/js/admin-script.js", array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'onum_custom_wp_admin_js' );
    }
    add_action( 'admin_enqueue_scripts', 'onum_custom_wp_admin_style' );
endif;

//Upload SVG file
function onum_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  $mimes['svgz'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'onum_mime_types', 10, 1);

// Header post type
add_action( 'init', 'onum_create_header_builder' ); 
function onum_create_header_builder() {
    register_post_type( 'ot_header_builders',
        array(
            'labels' => array(
                'name'              => esc_html__( 'Header Builders', 'onum' ),
                'singular_name'     => esc_html__( 'Header Builder', 'onum' ),
                'add_new'           => esc_html__( 'Add New', 'onum' ),
                'add_new_item'      => esc_html__( 'Add New Header Builder', 'onum' ),
                'edit'              => esc_html__( 'Edit', 'onum' ),
                'edit_item'         => esc_html__( 'Edit Header Builder', 'onum' ),
                'new_item'          => esc_html__( 'New Header Builder', 'onum' ),
                'view'              => esc_html__( 'View', 'onum' ),
                'view_item'         => esc_html__( 'View Header Builder', 'onum' ),
                'search_items'      => esc_html__( 'Search Header Builders', 'onum' ),
                'not_found'         => esc_html__( 'No Header Builders found', 'onum' ),
                'not_found_in_trash'=> esc_html__( 'No Header Builders found in Trash', 'onum' ),
                'parent'            => esc_html__( 'Parent Header Builder', 'onum' )
            ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'menu_position'         => 60,
            'supports'              => array( 'title', 'editor' ),
            'menu_icon'             => 'dashicons-editor-kitchensink',
            'publicly_queryable'    => true,
            'exclude_from_search'   => true,
            'has_archive'           => true,
            'query_var'             => true,
            'can_export'            => true,
            'capability_type'       => 'post'
        )
    );
}

// Footer post type
add_action( 'init', 'onum_create_footer_builder' ); 
function onum_create_footer_builder() {
    register_post_type( 'ot_footer_builders',
        array(
            'labels' => array(
                'name'              => esc_html__( 'Footer Builders', 'onum' ),
                'singular_name'     => esc_html__( 'Footer Builder', 'onum' ),
                'add_new'           => esc_html__( 'Add New', 'onum' ),
                'add_new_item'      => esc_html__( 'Add New Footer Builder', 'onum' ),
                'edit'              => esc_html__( 'Edit', 'onum' ),
                'edit_item'         => esc_html__( 'Edit Footer Builder', 'onum' ),
                'new_item'          => esc_html__( 'New Footer Builder', 'onum' ),
                'view'              => esc_html__( 'View', 'onum' ),
                'view_item'         => esc_html__( 'View Footer Builder', 'onum' ),
                'search_items'      => esc_html__( 'Search Footer Builders', 'onum' ),
                'not_found'         => esc_html__( 'No Footer Builders found', 'onum' ),
                'not_found_in_trash'=> esc_html__( 'No Footer Builders found in Trash', 'onum' ),
                'parent'            => esc_html__( 'Parent Footer Builder', 'onum' )
            ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'menu_position'         => 60,
            'supports'              => array( 'title', 'editor' ),
            'menu_icon'             => 'dashicons-editor-kitchensink',
            'publicly_queryable'    => true,
            'exclude_from_search'   => true,
            'has_archive'           => true,
            'query_var'             => true,
            'can_export'            => true,
            'capability_type'       => 'post'
        )
    );
}
