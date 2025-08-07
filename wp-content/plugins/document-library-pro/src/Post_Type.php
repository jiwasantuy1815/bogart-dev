<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Licensed_Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Register the Document Library post type
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Type implements Registerable, Standard_Service {

	const POST_TYPE_SLUG = 'dlp_document';

	private $plugin;
	private $license;

	/**
	 * Constructor.
	 *
	 * @param Licensed_Plugin $plugin
	 */
	public function __construct( Licensed_Plugin $plugin ) {
		$this->plugin         = $plugin;
		$this->license        = $this->plugin->get_license();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'init', [ $this, 'register_post_type' ], 15 );
		add_action( 'init', [ $this, 'flush_rewrite_rules' ], 16 );
		add_filter( 'use_block_editor_for_post_type', [ $this, 'disable_block_editor' ], 10, 2 );
	}

	/**
	 * Register the Document post type.
	 */
	public function register_post_type() {
		$default_fields = array_merge( [ 'author', 'title' ], Util\Options::get_document_fields() );
		$document_slug  = Util\Options::get_document_slug();

		$labels = [
			'name'                  => _x( 'Documents', 'Post Type General Name', 'document-library-pro' ),
			'singular_name'         => _x( 'Document', 'Post Type Singular Name', 'document-library-pro' ),
			'menu_name'             => _x( 'Documents', 'Admin Menu text', 'document-library-pro' ),
			'name_admin_bar'        => _x( 'Document', 'Add New on Toolbar', 'document-library-pro' ),
			'archives'              => __( 'Documents Archives', 'document-library-pro' ),
			'attributes'            => __( 'Documents Attributes', 'document-library-pro' ),
			'parent_item_colon'     => __( 'Parent Documents:', 'document-library-pro' ),
			'all_items'             => __( 'All Documents', 'document-library-pro' ),
			'add_new_item'          => __( 'Add New Document', 'document-library-pro' ),
			'add_new'               => __( 'Add New', 'document-library-pro' ),
			'new_item'              => __( 'New Document', 'document-library-pro' ),
			'edit_item'             => __( 'Edit Document', 'document-library-pro' ),
			'update_item'           => __( 'Update Document', 'document-library-pro' ),
			'view_item'             => __( 'View Document', 'document-library-pro' ),
			'view_items'            => __( 'View Documents', 'document-library-pro' ),
			'search_items'          => __( 'Search Documents', 'document-library-pro' ),
			'not_found'             => __( 'Not found', 'document-library-pro' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'document-library-pro' ),
			'featured_image'        => __( 'Featured Image', 'document-library-pro' ),
			'set_featured_image'    => __( 'Set featured image', 'document-library-pro' ),
			'remove_featured_image' => __( 'Remove featured image', 'document-library-pro' ),
			'use_featured_image'    => __( 'Use as featured image', 'document-library-pro' ),
			'insert_into_item'      => __( 'Insert into Document', 'document-library-pro' ),
			'uploaded_to_this_item' => __( 'Uploaded to this document', 'document-library-pro' ),
			'items_list'            => __( 'Document list', 'document-library-pro' ),
			'items_list_navigation' => __( 'Documents list navigation', 'document-library-pro' ),
			'filter_items_list'     => __( 'Filter Documents list', 'document-library-pro' ),
		];

		$args = [
			'label'               => __( 'Documents', 'document-library-pro' ),
			'description'         => __( 'Document Library Pro documents.', 'document-library-pro' ),
			'labels'              => $labels,
			'menu_icon'           => 'dashicons-media-document',
			'supports'            => $default_fields,
			'taxonomies'          => [],
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'document_library_pro',
			'menu_position'       => 26,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'hierarchical'        => false,
			'exclude_from_search' => false,
			'show_in_rest'        => true,
			'publicly_queryable'  => $this->license->is_valid(),
			'capability_type'     => 'post',
			'rewrite'             => [ 'slug' => $document_slug ],
		];

		register_post_type( self::POST_TYPE_SLUG, $args );
	}

	/**
	 * Disable the block editor for the post type.
	 *
	 * @param bool $enabled Whether the block editor is enabled.
	 * @param string $post_type The post type.
	 * @return bool Whether the block editor is enabled.
	 */
	public function disable_block_editor( $enabled, $post_type ) {
		if ( $post_type === self::POST_TYPE_SLUG ) {
			return false;
		}

		return $enabled;
	}

	/**
	 * Flushes rewrite rules once after successful license activation.
	 *
	 * This is done now as publicly_queryable is set to true after license activation.
	 */
	public function flush_rewrite_rules() {
		if ( $this->license->is_valid() && get_option( 'dlp_should_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			update_option( 'dlp_should_flush_rewrite_rules', false );
		}
	}
}
