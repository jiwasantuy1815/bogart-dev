<?php
namespace Elementor; // Custom widgets must be defined in the Elementor namespace
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly (security measure)

/**
 * Elementor Style for header
 *
 *
 * @since 1.0.0
 */

/**
 * Widget Name: Contact form 7 
 */

class Onum_Contact_Form_7 extends Widget_Base {   //this name is added to plugin.php of the root folder

	public function get_name() {
		return 'onum-contact-form-7';
	}

	public function get_title() {
		return 'Contact From 7';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-mail';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'category_onum' ];    // category of the widget
	}

	/**
	 * A list of scripts that the widgets is depended in
	 * @since 1.3.0
	 **/
	protected function register_controls() {
			
		//start of a control box
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Contact Form 7', 'onum' ),   //section name for controler view
			]
		);

		$this->add_control(
			'cf7',
			[
				'label' => esc_html__( 'Select Contact Form', 'onum' ),
                'description' => esc_html__('Contact form 7 - plugin must be installed and there must be some contact forms made with the contact form 7','onum'),
				'type' => Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => $this->get_contact_form_7_posts(),
			]
		);

		$this->end_controls_section();

	}


	protected function render() {				//to show on the fontend 
		static $v_veriable=0;

		$settings = $this->get_settings();
        if(!empty($settings['cf7'])){
    	   echo'<div class="elementor-shortcode onum-cf7-'.$v_veriable.'">';
                echo do_shortcode('[contact-form-7 id="'.$settings['cf7'].'"]');    
           echo '</div>';  
    	}

 		if(!empty($settings['cf7_redirect_page'])) {  ?>
 			<script>
 			        var theform = document.querySelector('.onum-cf7-<?php echo $v_veriable; ?>');
						theform.addEventListener( 'wpcf7mailsent', function( event ) {
					    location = '<?php echo get_permalink( $settings['cf7_redirect_page'] ); ?>';
					}, false );
			</script>

		<?php  $v_veriable++;
 		}
    }

    protected function get_contact_form_7_posts(){

	 	$args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);

	    $catlist=[];
	    
	    if( $categories = get_posts($args)){
	    	foreach ( $categories as $category ) {
	    		(int)$catlist[$category->ID] = $category->post_title;
	    	}
	    }
	    else{
	        (int)$catlist['0'] = esc_html__('No contect From 7 form found', 'onum');
	    }
	  	return $catlist;
	}

	protected function onum_get_all_pages(){

	  	$args = array('post_type' => 'page', 'posts_per_page' => -1);

	    $catlist=[];
	    
	    if( $categories = get_posts($args)){
	      foreach ( $categories as $category ) {
	        (int)$catlist[$category->ID] = $category->post_title;
	      }
	    }
	    else{
	        (int)$catlist['0'] = esc_html__('No Pages Found!', 'onum');
	    }
	  	return $catlist;
	}

	
}
// After the Onum_Contact_Form_7 class is defined, I must register the new widget class with Elementor:
Plugin::instance()->widgets_manager->register( new Onum_Contact_Form_7() );
