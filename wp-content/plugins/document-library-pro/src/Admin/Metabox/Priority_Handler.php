<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Metabox;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;
use Barn2\Plugin\Document_Library_Pro\Post_Type;

defined( 'ABSPATH' ) || exit;

/**
 * Repositions the metaboxes in the document add/edit screen
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Priority_Handler implements Registerable, Standard_Service, Conditional {

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Util::is_admin();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'add_meta_boxes', [ $this, 'reposition_metaboxes' ] );
	}

	/**
	 * Reposition the metaboxes
	 */
	public function reposition_metaboxes() {
		global $wp_meta_boxes;

		if ( ! isset( $wp_meta_boxes[ Post_Type::POST_TYPE_SLUG ] ) ) {
			return;
		}

		// get the metaboxes
		$document_link = $wp_meta_boxes[ Post_Type::POST_TYPE_SLUG ]['side']['core'][ Document_Link::ID ];
		$file_size = $wp_meta_boxes[ Post_Type::POST_TYPE_SLUG ]['side']['core'][ File_Size::ID ];

		// remove the metaboxes
		unset( $wp_meta_boxes[ Post_Type::POST_TYPE_SLUG ]['side']['core'][ Document_Link::ID ] );
		unset( $wp_meta_boxes[ Post_Type::POST_TYPE_SLUG ]['side']['core'][ File_Size::ID ] );

		// add the metabox as second item in side core
		$wp_meta_boxes[ Post_Type::POST_TYPE_SLUG ]['side']['core'] = array_merge(
			array_slice( $wp_meta_boxes[ Post_Type::POST_TYPE_SLUG ]['side']['core'], 0, 1 ),
			[ Document_Link::ID => $document_link, File_Size::ID => $file_size ],
			array_slice( $wp_meta_boxes[ Post_Type::POST_TYPE_SLUG ]['side']['core'], 1 )
		);
	}

}
