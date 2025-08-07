<?php
/**
 * Registering meta boxes
 *
 * Using Meta Box plugin: http://www.deluxeblogtips.com/meta-box/
 *
 * @see https://docs.metabox.io/
 *
 * @param array $meta_boxes Default meta boxes. By default, there are no meta boxes.
 *
 * @return array All registered meta boxes
 */

function onum_register_meta_boxes( $meta_boxes ) {
	
	$meta_boxes[] = array(
		'id'       => 'page-settings',
		'title'    => esc_html__( 'Page Header Settings', 'onum' ),
		'pages'    => array( 'page' ),
		'context'  => 'normal',
		'priority' => 'high',
		'autosave' => true,
		'fields'   => array(
            array(
                'id'        			=> 'page_layout',
                'name'      			=> esc_html__( 'Page Layout', 'onum' ),
                'type'      			=> 'image_select',
                'options'   			=> array(
                    'full-content'    	=> get_template_directory_uri() . '/inc/backend/images/full.png',
                    'content-sidebar' 	=> get_template_directory_uri() . '/inc/backend/images/right.png',
                    'sidebar-content' 	=> get_template_directory_uri() . '/inc/backend/images/left.png',
                ),
                'std'       			=> 'full-content'
            ),
            array(
                'name'             => esc_html__( 'Page Header On/Off', 'onum' ),
                'id'               => 'pheader_switch',
                'type'             => 'switch',
                'style'            => 'rounded',
                'on_label'         => esc_html__( 'On', 'onum' ),
                'off_label'        => esc_html__( 'Off', 'onum' ),
                'std'              => 'on'
            ),
            array(
                'name'             => esc_html__( 'Background Page Header', 'onum' ),
                'id'               => 'pheader_bg_image',
                'type'             => 'image_advanced',
                'max_file_uploads' => 1,
            )
		),
	);
    $meta_boxes[] = array(
        'id'       => 'extra-settings',
        'title'    => esc_html__( 'Page Header Settings', 'onum' ),
        'pages'    => array( 'ot_service', 'ot_portfolio' ),
        'context'  => 'normal',
        'priority' => 'high',
        'autosave' => true,
        'fields'   => array(
            array(
                'name'             => esc_html__( 'Page Header On/Off', 'onum' ),
                'id'               => 'pheader_switch',
                'type'             => 'switch',
                'style'            => 'rounded',
                'on_label'         => esc_html__( 'On', 'onum' ),
                'off_label'        => esc_html__( 'Off', 'onum' ),
                'std'              => 'on'
            ),
            array(
                'name'             => esc_html__( 'Background Page Header', 'onum' ),
                'id'               => 'pheader_bg_image',
                'type'             => 'image_advanced',
                'max_file_uploads' => 1,
            )
        ),
    );
	$meta_boxes[] = array (
		'id' 			=> 'select-header-footer',
		'title' 		=> esc_html__( 'Header/Footer Settings', 'onum' ),
		'pages' 		=>   array ('page'),
		'context' 		=> 'normal',
		'priority' 		=> 'high',
		'autosave' 		=> false,
		'fields' 		=>   array (  
      		array(
        		'name' 					=> esc_html__( 'Header Layout', 'onum' ),
				'id' 					=> 'select_header',
				'type'  				=> 'post',
		    	'post_type'   			=> 'ot_header_builders',
		    	'field_type'  			=> 'select_advanced',
		    	'placeholder' 			=> esc_html__( 'Select a header', 'onum' ),
		    	'query_args'  			=> array(
		        	'post_status'    	=> 'publish',
		        	'posts_per_page' 	=> - 1,
		        	'orderby' 		 	=> 'date',
		        	'order' 		 	=> 'ASC',
		    	),
			),				
			array(
        		'name' 					=> esc_html__( 'Header Mobile Layout', 'onum' ),
				'id' 					=> 'select_header_mobile',
				'type'  				=> 'post',
		    	'post_type'   			=> 'ot_header_builders',
		    	'field_type'  			=> 'select_advanced',
		    	'placeholder' 			=> esc_html__( 'Select a header mobile', 'onum' ),
		    	'query_args'  			=> array(
		        	'post_status'    	=> 'publish',
		        	'posts_per_page' 	=> - 1,
		        	'orderby' 		 	=> 'date',
		        	'order' 		 	=> 'ASC',
		    	),
			),
			array(
                'name'              => esc_html__( 'Header Transparent On/Off', 'onum' ),
                'id'                => 'header_transparent',
                'type'              => 'switch',
                'style'             => 'rounded',
                'on_label'         	=> esc_html__( 'On', 'onum' ),
                'off_label'        	=> esc_html__( 'Off', 'onum' ),
                'std'               => '0'
            ),			
			array (
        		'name' 					=> esc_html__( 'Footer Layout', 'onum' ),
				'id' 					=> 'select_footer',
				'type'  				=> 'post',
		    	'post_type'   			=> 'ot_footer_builders',
		    	'field_type'  			=> 'select_advanced',
		    	'placeholder' 			=> esc_html__( 'Select a footer', 'onum' ),
		    	'query_args'  			=> array(
		        	'post_status'    	=> 'publish',
		        	'posts_per_page' 	=> - 1,
		        	'orderby' 		 	=> 'date',
		        	'order' 		 	=> 'ASC',
		    	),
        	),
      	),
    );

	// Post format's meta box
	$meta_boxes[] = array(
		'id'       => 'format_detail',
		'title'    => esc_html__( 'Format Details', 'onum' ),
		'pages'    => array( 'post' ),
		'context'  => 'normal',
		'priority' => 'high',
		'autosave' => true,
		'fields'   => array(
			array(
                'name'             	=> esc_html__( 'Background Image Page Header', 'onum' ),
                'id'               	=> 'pheader_bg_image',
                'type'             	=> 'image_advanced',
                'max_file_uploads' 	=> 1,
                'class' 			=> 'gallery link image quote video audio standard',
            ),
			array(
			    'name'          	=> esc_html__( 'Background Overlay Page Header', 'onum' ),
			    'id'            	=> 'pheader_bgcolor_overlay',
			    'type'          	=> 'color',
			    'alpha_channel' 	=> true,
			    'class' 			=> 'gallery link image quote video audio standard',
			),
			array(
				'name'             	=> esc_html__( 'Image', 'onum' ),
				'id'               	=> 'post_image',
				'type'             	=> 'image_advanced',
				'class'            	=> 'image',
				'max_file_uploads' 	=> 1,
    			'image_size'       	=> 'thumbnail',
			),
			array(
				'name'  			=> esc_html__( 'Gallery', 'onum' ),
				'id'    			=> 'post_gallery',
				'type'  			=> 'image_advanced',
				'class' 			=> 'gallery',
    			'image_size'       	=> 'thumbnail',
			),			
			array(
				'name'  => esc_html__( 'Audio', 'onum' ),
				'id'    => 'post_audio',
				'type'  => 'textarea',
				'cols'  => 20,
				'rows'  => 2,
				'class' => 'audio',
				'desc'  => 'Example: https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/139083759',
			),
			array(
				'name'  => esc_html__( 'Video', 'onum' ),
				'id'    => 'post_video',
				'type'  => 'textarea',
				'cols'  => 20,
				'rows'  => 2,
				'class' => 'video',
				'desc'  => 'Example: https://vimeo.com/87959439',
			),
			array(
				'name'  => esc_html__( 'Background Video', 'onum' ),
				'id'    => 'bg_video',
				'type'  => 'image_advanced',
				'class' => 'video',
				'max_file_uploads' => 1,
			),
			array(
				'name'  => esc_html__( 'Link', 'onum' ),
				'id'    => 'post_link',
				'type'  => 'textarea',
				'cols'  => 20,
				'rows'  => 2,
				'class' => 'link',
			),
			array(
				'name'  => esc_html__( 'Text Link', 'onum' ),
				'id'    => 'text_link',
				'type'  => 'textarea',
				'cols'  => 20,
				'rows'  => 2,
				'class' => 'link',
			),
			array(
				'name'  => esc_html__( 'Quote', 'onum' ),
				'id'    => 'post_quote',
				'type'  => 'textarea',
				'class' => 'quote',
			),
			array(
				'name'  => esc_html__( 'Quote Name', 'onum' ),
				'id'    => 'quote_name',
				'type'  => 'text',
				'class' => 'quote',
			)		
		),
	);

	return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'onum_register_meta_boxes' );
