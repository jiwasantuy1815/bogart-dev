<!-- #site-header-open -->
<header id="site-header" class="site-header <?php onum_header_class(); ?>" itemscope="itemscope" itemtype="http://schema.org/WPHeader">

    <!-- #header-desktop-open -->
    <?php onum_header_builder(); ?>
    <!-- #header-desktop-close -->

    <!-- #header-mobile-open -->
    <?php onum_mobile_builder(); ?>
    <!-- #header-mobile-close -->

</header>
<!-- #site-header-close -->

<!-- #side-panel-open -->
<?php if ( !empty( onum_get_option('is_sidepanel') ) && onum_get_option('sidepanel_layout') != '' ) { ?>
    <div class="site-overlay panel-overlay"></div>
    <div id="side-panel" class="side-panel <?php if ( onum_get_option( 'sidepanel_position' ) != false ) { echo 'on-left'; } ?>">
        <a href="#" class="side-panel-close"><i class="flaticon-close"></i></a>
        <div class="side-panel-block">
            <?php onum_sidepanel_builder(); ?>	
        </div>
    </div>
<?php } ?>
<!-- #side-panel-close -->