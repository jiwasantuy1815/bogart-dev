<?php

// Load the theme's custom Widgets so that they appear in the Elementor element panel.
add_action( 'elementor/widgets/register', 'onum_register_elementor_widgets' );
function onum_register_elementor_widgets() {
    // We check if the Elementor plugin has been installed / activated.
    if ( defined( 'ELEMENTOR_PATH' ) && class_exists('Elementor\Widget_Base') ) {
        // Include Elementor Widget files here.
        
        // Remove this 2 require_once line below after completed the theme.
        require_once( get_template_directory() . '/inc/backend/elementor-widgets/ot-widgets.php' );
        require_once( get_template_directory() . '/inc/backend/elementor/header/ot-header-widgets.php' );
    }
}

// Add a custom 'category_onum' category for to the Elementor element panel so that our theme's widgets have their own category.
add_action( 'elementor/init', function() {
    \Elementor\Plugin::$instance->elements_manager->add_category( 
        'category_onum',
        [
            'title' => __( 'ONUM', 'onum' ),
            'icon' => 'fa fa-plug', //default icon
        ],
        1 // position
    );
    \Elementor\Plugin::$instance->elements_manager->add_category( 
        'category_onum_header',
        [
            'title' => __( 'ONUM Header', 'onum' ),
            'icon' => 'fa fa-plug', //default icon
        ],
        2 // position
    );
});

// Post types with Elementor
function onum_add_cpt_support() {
    
    //if exists, assign to $cpt_support var
    $cpt_support = get_option( 'elementor_cpt_support' );
    
    //check if option DOESN'T exist in db
    if( ! $cpt_support ) {
        $cpt_support = [ 'page', 'post', 'ot_portfolio', 'ot_header_builders', 'ot_footer_builders' ]; //create array of our default supported post types
        update_option( 'elementor_cpt_support', $cpt_support ); //write it to the database
    }
    
    //if it DOES exist, but portfolio is NOT defined
    else {
        $ot_portfolio       = in_array( 'ot_portfolio', $cpt_support );
        $ot_header_builders = in_array( 'ot_header_builders', $cpt_support );
        $ot_footer_builders = in_array( 'ot_footer_builders', $cpt_support );
        if( !$ot_portfolio ){
            $cpt_support[] = 'ot_portfolio'; //append to array
        }
        if( !$ot_header_builders ){
            $cpt_support[] = 'ot_header_builders'; //append to array
        }
        if( !$ot_footer_builders ){
            $cpt_support[] = 'ot_footer_builders'; //append to array
        }
        update_option( 'elementor_cpt_support', $cpt_support ); //update database
    }
    
    //otherwise do nothing, portfolio already exists in elementor_cpt_support option
}
add_action( 'elementor/init', 'onum_add_cpt_support' );


// Upload SVG for Elementor
function onum_unfiltered_files_upload() {
    
    //if exists, assign to $cpt_support var
    $cpt_support = get_option( 'elementor_unfiltered_files_upload' );
    
    //check if option DOESN'T exist in db
    if( ! $cpt_support ) {
        $cpt_support = '1'; //create string value default to enable upload svg
        update_option( 'elementor_unfiltered_files_upload', $cpt_support ); //write it to the database
    }
}
add_action( 'elementor/init', 'onum_unfiltered_files_upload' );

/**
 * enables default language and translation management for 'ot_header_builders', 'ot_footer_builders' in Polylang
 */

if ( function_exists( 'pll_the_languages' ) ) {
    add_filter( 'pll_get_post_types', 'add_cpt_to_pll', 10, 2 );
    function add_cpt_to_pll( $post_types ) {
        $post_types['ot_header_builders'] = 'ot_header_builders';
        $post_types['ot_footer_builders'] = 'ot_footer_builders';
        return $post_types;
    }
}

/*Fix Elementor Pro*/
function onum_register_elementor_locations( $elementor_theme_manager ) {
    $elementor_theme_manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'onum_register_elementor_locations' );

/*Add options to sections*/
add_action('elementor/element/section/section_typo/after_section_end', function( $section, $args ) {

    /* header options */
    $section->start_controls_section(
        'onum_header_options',
        [
            'label' => __( 'For Header', 'onum' ),
            'tab'   => \Elementor\Controls_Manager::TAB_LAYOUT,
        ]
    );
    $section->add_control(
        'topbar_off',
        [
            'label'        => __( 'Top bar Scroll On/Off', 'theratio' ),
            'type'         => Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'topbar__off',
            'prefix_class' => '',
        ]
    );
    $section->add_control(
        'sticky_class',
        [
            'label'        => __( 'Custom Sticky On/Off', 'theratio' ),
            'type'         => Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'header__sticky',
            'prefix_class' => '',
        ]
    );
    $section->add_control(
        'sticky_background',
        [
            'label'     => __( 'Background Scroll', 'onum' ),
            'type'      => Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.is-header-sticky' => 'background: {{VALUE}};',
            ],
            'condition' => [
                'sticky_class' => 'header__sticky',
            ],
        ]
    );
    $section->add_control(
        'sticky_border',
        [
            'label'     => __( 'Border Bottom Scroll', 'onum' ),
            'type'      => Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.is-header-sticky' => 'border-bottom-color: {{VALUE}};',
            ],
            'condition' => [
                'sticky_class' => 'header__sticky',
            ],
        ]
    );
    $section->add_group_control(
        \Elementor\Group_Control_Box_Shadow::get_type(),
        [
            'name'      => 'sticky_boxshadow',
            'label'     => __( 'Box Shadow Scroll', 'onum' ),
            'selector'  => '{{WRAPPER}}.is-header-sticky',
            'condition' => [
                'sticky_class' => 'header__sticky',
            ],
        ]
    );

    $section->end_controls_section();

    /*Particles*/
    $section->start_controls_section(
        'section_custom_class',
        [
            'label' => __( 'Particles', 'onum' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
    );
    $section->add_control(
        'particles_class',
        [
            'label'        => __( 'Particles On/Off', 'onum' ),
            'type'         => Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'particles-js',
            'prefix_class' => '',
        ]
    );
    $section->add_control(
        'add_particles_color',
        [
            'label'        => __( 'Particles Colors', 'onum' ),
            'type'         => Elementor\Controls_Manager::TEXT,
            'default'      => '#fe4c1c,#00c3ff,#0160e7',
            'condition' => [
                'particles_class' => 'particles-js',
            ]
        ]
    );
    $section->end_controls_section();

    /*Grid Lines*/
    $section->start_controls_section(
        'section_custom_lines',
        [
            'label' => __( 'Grid Lines', 'onum' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
    );
    $section->add_control(
        'lines_class',
        [
            'label'        => __( 'Grid Lines On/Off', 'onum' ),
            'type'         => Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'has-lines',
            'prefix_class' => '',
        ]
    );
    $section->add_control(
        'heading_line1',
        [
            'label' => __( 'Line Left', 'onum' ),
            'type' => Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_responsive_control(
        'line1_space',
        [
            'label' => __( 'Position Line', 'onum' ),
            'type' => Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => -200,
                    'max' => 0,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .line-left' => 'top: {{SIZE}}{{UNIT}}; height: calc(100% - {{SIZE}}{{UNIT}});',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_control(
        'line1_color',
        [
            'label'        => __( 'Line Color', 'onum' ),
            'type'         => Elementor\Controls_Manager::COLOR,
            'default'      => '',
            'selectors'    => [
                '{{WRAPPER}} .line-left' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_responsive_control(
        'dot1_space',
        [
            'label' => __( 'Position Dot', 'onum' ),
            'type' => Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 500,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .line-left span' => 'top: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_control(
        'dot1_color',
        [
            'label'        => __( 'Dot Left Color', 'onum' ),
            'type'         => Elementor\Controls_Manager::COLOR,
            'default'      => '',
            'selectors'    => [
                '{{WRAPPER}} .line-left span' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );

    $section->add_control(
        'heading_line2',
        [
            'label' => __( 'Line Center Left', 'onum' ),
            'type' => Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_responsive_control(
        'line2_space',
        [
            'label' => __( 'Position Line', 'onum' ),
            'type' => Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => -200,
                    'max' => 0,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .line-cleft' => 'top: {{SIZE}}{{UNIT}}; height: calc(100% - {{SIZE}}{{UNIT}});',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_control(
        'line2_color',
        [
            'label'        => __( 'Line Color', 'onum' ),
            'type'         => Elementor\Controls_Manager::COLOR,
            'default'      => '',
            'selectors'    => [
                '{{WRAPPER}} .line-cleft' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_responsive_control(
        'dot2_space',
        [
            'label' => __( 'Position Dot', 'onum' ),
            'type' => Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 500,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .line-cleft span' => 'top: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_control(
        'dot2_color',
        [
            'label'        => __( 'Dot Color', 'onum' ),
            'type'         => Elementor\Controls_Manager::COLOR,
            'default'      => '',
            'selectors'    => [
                '{{WRAPPER}} .line-cleft span' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );

    $section->add_control(
        'heading_line3',
        [
            'label' => __( 'Line Center Right', 'onum' ),
            'type' => Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_responsive_control(
        'line3_space',
        [
            'label' => __( 'Position Line', 'onum' ),
            'type' => Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => -200,
                    'max' => 0,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .line-cright' => 'top: {{SIZE}}{{UNIT}}; height: calc(100% - {{SIZE}}{{UNIT}});',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_control(
        'line3_color',
        [
            'label'        => __( 'Line Color', 'onum' ),
            'type'         => Elementor\Controls_Manager::COLOR,
            'default'      => '',
            'selectors'    => [
                '{{WRAPPER}} .line-cright' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_responsive_control(
        'dot3_space',
        [
            'label' => __( 'Position Dot', 'onum' ),
            'type' => Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 500,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .line-cright span' => 'top: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_control(
        'dot3_color',
        [
            'label'        => __( 'Dot Color', 'onum' ),
            'type'         => Elementor\Controls_Manager::COLOR,
            'default'      => '',
            'selectors'    => [
                '{{WRAPPER}} .line-cright span' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );

    $section->add_control(
        'heading_line4',
        [
            'label' => __( 'Line Right', 'onum' ),
            'type' => Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_responsive_control(
        'line4_space',
        [
            'label' => __( 'Position Line', 'onum' ),
            'type' => Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => -200,
                    'max' => 0,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .line-right' => 'top: {{SIZE}}{{UNIT}}; height: calc(100% - {{SIZE}}{{UNIT}});',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_control(
        'line4_color',
        [
            'label'        => __( 'Line Color', 'onum' ),
            'type'         => Elementor\Controls_Manager::COLOR,
            'default'      => '',
            'selectors'    => [
                '{{WRAPPER}} .line-right' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_responsive_control(
        'dot4_space',
        [
            'label' => __( 'Position Dot', 'onum' ),
            'type' => Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 500,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .line-right span' => 'top: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->add_control(
        'dot4_color',
        [
            'label'        => __( 'Dot Color', 'onum' ),
            'type'         => Elementor\Controls_Manager::COLOR,
            'default'      => '',
            'selectors'    => [
                '{{WRAPPER}} .line-right span' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'lines_class' => 'has-lines',
            ],
        ]
    );
    $section->end_controls_section();

}, 10, 2 );

/*Add options to container*/
add_action('elementor/element/container/section_shape_divider/after_section_end', function( $container, $args ) {

    /*Particles*/
    $container->start_controls_section(
        'container_custom_class',
        [
            'label' => __( 'Particles', 'onum' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
    );
    $container->add_control(
        'container_particles_class',
        [
            'label'        => __( 'Particles On/Off', 'onum' ),
            'type'         => Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'particles-js',
            'prefix_class' => '',
        ]
    );
    $container->add_control(
        'container_add_particles_color',
        [
            'label'        => __( 'Particles Colors', 'onum' ),
            'type'         => Elementor\Controls_Manager::TEXT,
            'default'      => '#fe4c1c,#00c3ff,#0160e7',
            'condition' => [
                'container_particles_class' => 'particles-js',
            ]
        ]
    );
    $container->end_controls_section();

}, 10, 2 );

add_action( 'elementor/frontend/section/before_render',function( $element ) {
    // Make sure we are in a section element
    if( 'section' !== $element->get_name() ) {
        return;
    }
    $section = $element->get_settings_for_display();
    if( $section['add_particles_color'] ){
        $element->add_render_attribute( '_wrapper', 'data-color', $section['add_particles_color'] );
    }
    
});
add_action( 'elementor/frontend/container/before_render',function( $element ) {
    // Make sure we are in a section element
    if( 'container' !== $element->get_name() ) {
        return;
    }
    $container = $element->get_settings_for_display();
    if( $container['container_add_particles_color'] ){
        $element->add_render_attribute( '_wrapper', 'data-color', $container['container_add_particles_color'] );
    }
    
});
