<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Util\Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;

defined( 'ABSPATH' ) || exit;

/**
 * Handler for general document expiry hooks
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Expiry_Handler implements Registerable, Standard_Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		// add an expired prefix to the title on the frontend
		add_filter( 'private_title_format', [ $this, 'maybe_add_expired_title_prefix' ], 10, 2 );

		// reset expiry on post visibility change from private
		add_action( 'transition_post_status', [ $this, 'remove_expiry_on_status_change' ], 10, 3 );
	}

	/**
	 * Add an expired prefix to the title
	 *
	 * @param string $title
	 * @param \WP_Post $post
	 * @return string
	 */
	public function maybe_add_expired_title_prefix( $title, $post ) {
		if ( Util::is_expired_document( $post ) ) {
			/* translators: %s is the document title */
			return __( 'Expired: %s', 'document-library-pro' );
		}

		return $title;
	}

	/**
	 * Remove the expiry stamp on post status change from private
	 *
	 * @param string $new_status
	 * @param string $old_status
	 * @param \WP_Post $post
	 */
	public function remove_expiry_on_status_change( $new_status, $old_status, $post ) {
		if ( $old_status === $new_status ) {
			return;
		}

		if ( Post_Type::POST_TYPE_SLUG !== $post->post_type ) {
			return;
		}

		if ( Util::is_expired_document( $post ) && 'private' === $old_status && 'private' !== $new_status ) {
			delete_post_meta( $post->ID, '_dlp_expiry_timestamp' );
		}
	}
}
