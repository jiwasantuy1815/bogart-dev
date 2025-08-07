<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ONUM
 */
?>

	</div><!-- #content -->
	<?php
		if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
			onum_footer_builder();
		}
	?>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>