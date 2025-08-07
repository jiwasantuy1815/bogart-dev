<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Wizard\Steps;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Interfaces\Deferrable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Steps\Ready;
use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

/**
 * Completed Step.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Completed extends Ready implements Deferrable {

	/**
	 * {@inheritdoc}
	 */
	public function get_step_details() {
		return [
			'label'       => esc_html__( 'Ready', 'document-library-pro' ),
			'heading'     => esc_html__( 'Setup Complete', 'document-library-pro' ),
			'description' => $this->get_custom_description(),
		];
	}

	/**
	 * Retrieves the description.
	 *
	 * @return string
	 */
	private function get_custom_description() {
		$options               = Options::get_user_shortcode_options();
		$document_page_id      = $options['document_page'];
		$document_library_page = get_permalink( $document_page_id );
		$add_document_page     = admin_url( 'post-new.php?post_type=dlp_document' );
		$import_page           = admin_url( 'admin.php?page=dlp_import' );

		return sprintf(
			/* translators: %1: Add document link open, %2: Add document link close, %3: Import page link open, %4: Import page link close, %5: Document library link open, %6: Document library link close */
			esc_html__( 'Congratulations, you have finished setting up the plugin! The next step is to start %1$sadding%2$s or %3$simporting%4$s documents. Your documents will be listed on the %5$sdocument library page%6$s.', 'document-library-pro' ),
			Lib_Util::format_link_open( $add_document_page, true ),
			'</a>',
			Lib_Util::format_link_open( $import_page, true ),
			'</a>',
			Lib_Util::format_link_open( $document_library_page, true ),
			'</a>'
		);
	}
}
