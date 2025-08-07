<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;

defined( 'ABSPATH' ) || exit;

/**
 * Schedule once-off cron jobs at the time of expiry to
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Expirator implements Registerable, Standard_Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'document_library_pro_expire_document', [ $this, 'run' ], 10, 1 );
	}

	/**
	 * Run the cron job to expire the document (move to Private status)
	 *
	 * @param int $document_id
	 */
	public function run( $document_id ) {
		wp_update_post(
			[
				'ID'          => $document_id,
				'post_status' => 'private',
			]
		);
	}
}
