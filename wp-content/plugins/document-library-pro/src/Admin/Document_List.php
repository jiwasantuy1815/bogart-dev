<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;
use Barn2\Plugin\Document_Library_Pro\Post_Type;
use Barn2\Plugin\Document_Library_Pro\Taxonomies;
use Barn2\Plugin\Document_Library_Pro\Util\Util;

defined( 'ABSPATH' ) || exit;

/**
 * Handles functionality on the Documents list table screen
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_List implements Registerable, Standard_Service, Conditional {

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Lib_Util::is_admin();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		// Default Metaboxes
		add_filter( 'default_hidden_columns', [ $this, 'hide_author_column' ], 10, 2 );

		// Rename Document Authors column
		add_filter( 'manage_dlp_document_posts_columns', [ $this, 'rename_author_column' ] );

		// Add Taxonomy Filter Dropdowns
		add_action( 'restrict_manage_posts', [ $this, 'add_taxonomy_dropdowns' ] );
		add_action( 'parse_query', [ $this, 'parse_taxonomy_dropdown_queries' ], 10, 1 );

		// Document Expiry
		add_filter( 'display_post_states', [ $this, 'add_expired_post_state' ], 10, 2 );

		add_filter( 'post_date_column_status', [ $this, 'maybe_add_expired_date_status' ], 10, 2 );
		add_filter( 'post_date_column_time', [ $this, 'maybe_add_expired_date_time' ], 10, 2 );

		add_filter( 'views_edit-dlp_document', [ $this, 'add_expired_view' ] );
		add_filter( 'parse_query', [ $this, 'parse_expired_view_query' ] );
		add_action( 'pre_get_posts', [ $this, 'handle_expired_view_query' ] );

		// Add Analytics Column
		add_filter( 'manage_dlp_document_posts_columns', [ $this, 'add_downloads_column' ], 10, 1 );
		add_action( 'manage_dlp_document_posts_custom_column' , [ $this, 'downloads_column' ], 10, 2 );

		add_filter( 'manage_edit-dlp_document_sortable_columns', [ $this, 'add_sortable_key' ], 10, 1 );
		add_action( 'pre_get_posts', [ $this, 'handle_sortable_query' ] );
	}

	/**
	 * Hide the author column by default.
	 *
	 * @param array $hidden The list of hidden columns.
	 * @param \WP_Screen $screen The current screen.
	 * @return array The list of hidden columns.
	 */
	public function hide_author_column( $hidden, $screen ) {
		if ( $screen && 'edit-dlp_document' === $screen->id ) {
			$hidden[] = 'author';
		}

		return $hidden;
	}

	/**
	 * Rename the Document Authors column to Document Author
	 *
	 * @param array $columns
	 * @return array
	 */
	public function rename_author_column( $columns ) {
		$columns['taxonomy-doc_author'] = __( 'Document Author', 'document-library-pro' );

		return $columns;
	}

	/**
	 * Add the taxonomy dropdowns
	 */
	public function add_taxonomy_dropdowns() {
		global $typenow;

		if ( $typenow !== Post_Type::POST_TYPE_SLUG ) {
			return;
		}

		// Remove from post statuses without term counts
		$post_status = filter_input( INPUT_GET, 'post_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ?? '';
		if ( in_array( $post_status, [ 'trash', 'draft', 'pending' ], true ) ) {
			return;
		}

		$taxonomies = [ Taxonomies::CATEGORY_SLUG, Taxonomies::TAG_SLUG, Taxonomies::AUTHOR_SLUG, Taxonomies::FILE_TYPE_SLUG ];

		foreach ( $taxonomies as $taxonomy ) {

			$selected        = filter_input( INPUT_GET, $taxonomy, FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ?? '';
			$taxonomy_object = get_taxonomy( $taxonomy );

			if ( (int) get_terms(
				[
					'taxonomy'   => $taxonomy,
					'hide_empty' => true,
					'fields'     => 'count'
				]
			) === 0 ) {
				continue;
			}

			wp_dropdown_categories(
				[
					/* translators: %s: Taxonomy label */
					'show_option_all' => sprintf( esc_html__( 'All %s', 'document-library-pro' ), $taxonomy_object->label ),
					'taxonomy'        => $taxonomy,
					'name'            => $taxonomy,
					'orderby'         => 'name',
					'selected'        => $selected,
					'value_field'     => 'slug',
					'hierarchical'    => true,
					// 'show_count'      => true,
					'hide_empty'      => true,
				]
			);
		}
	}

	/**
	 * Parses the query for the taxonomy dropdowns.
	 *
	 * @param \WP_Query $query
	 */
	public function parse_taxonomy_dropdown_queries( $query ) {
		global $pagenow;

		if ( $pagenow !== 'edit.php' ) {
			return;
		}

		$taxonomies = [ Taxonomies::CATEGORY_SLUG, Taxonomies::TAG_SLUG, Taxonomies::AUTHOR_SLUG, Taxonomies::FILE_TYPE_SLUG ];
		$query_vars = &$query->query_vars;

		if ( ! isset( $query_vars['post_type'] ) || $query_vars['post_type'] !== Post_Type::POST_TYPE_SLUG ) {
			return;
		}

		foreach ( $taxonomies as $taxonomy ) {
			if ( isset( $query_vars[ $taxonomy ] ) && is_numeric( $query_vars[ $taxonomy ] ) && $query_vars[ $taxonomy ] !== 0 ) {
				$term = get_term_by( 'id', $query_vars[ $taxonomy ], $taxonomy );

				if ( $term !== false ) {
					$query_vars[ $taxonomy ] = $term->slug;
				}
			}
		}
	}

	/**
	 * Add a download count column.
	 *
	 * @param array $columns
	 * @return array
	 */
	public function add_downloads_column( $columns ) {
		$columns['downloads'] = __( 'Downloads', 'document-library-pro' );

		return $columns;
	}

	/**
	 * Output the download count column.
	 *
	 * @param string $column
	 * @param int $post_id
	 */
	public function downloads_column( $column, $post_id ) {
		if ( $column !== 'downloads' ) {
			return;
		}

		echo esc_html( get_post_meta( $post_id, '_dlp_download_count', true ) );
	}

	/**
	 * Add a download count query key.
	 *
	 * @param array $columns
	 * @return array
	 */
	public function add_sortable_key( $columns ) {
		$columns['downloads'] = 'download_count';

		return $columns;
	}

	/**
	 * Sort by downloads on document list.
	 *
	 * @param \WP_Query $query
	 */
	public function handle_sortable_query( $query ) {
		if ( ! is_admin() || ! function_exists( 'get_current_screen') ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen instanceof \WP_Screen || $screen->id !== 'edit-dlp_document' ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( 'download_count' === $orderby ) {
			$query->set( 'meta_key', '_dlp_download_count' );
			$query->set( 'orderby', 'meta_value_num' );
		}
	}


	/**
	 * Add a post display states for pages and documents
	 *
	 * @param array     $post_states An array of post display states.
	 * @param \WP_Post  $post        The current post object.
	 */
	public function add_expired_post_state( $post_states, $post ) {
		if ( Util::is_expired_document( $post ) ) {
			$post_states['dlp_expired_document'] = __( 'Expired', 'document-library-pro' );
		}

		return $post_states;
	}

	/**
	 * Maybe add expired date status
	 *
	 * @param array     $post_states An array of post display states.
	 * @param \WP_Post  $post        The current post object.
	 */
	public function maybe_add_expired_date_status( $status, $post ) {
		if ( ! Util::is_expired_document( $post ) ) {
			return $status;
		}

		return __( 'Expired', 'document-library-pro' );
	}

	/**
	 * Maybe add expired date time
	 *
	 * @param string     $time
	 * @param \WP_Post  $post        The current post object.
	 */
	public function maybe_add_expired_date_time( $time, $post ) {
		if ( ! Util::is_expired_document( $post ) ) {
			return $time;
		}

		$document = dlp_get_document( $post->ID );
		$timestamp = $document && $document->get_expiry_timestamp() ? $document->get_expiry_timestamp() : '';

		$formatted_time = sprintf(
			/* translators: 1: Post date, 2: Post time. */
			__( '%1$s at %2$s', 'document-library-pro' ),
			/* translators: Post date format. See https://www.php.net/manual/datetime.format.php */
			wp_date( __( 'Y/m/d', 'document-library-pro' ), $timestamp ),
			/* translators: Post time format. See https://www.php.net/manual/datetime.format.php */
			wp_date( __( 'g:i a', 'document-library-pro' ), $timestamp )
		);

		return $formatted_time;
	}

	/**
	 * Maybe add view link
	 *
	 * @param array $views
	 */
	public function add_expired_view( $views ) {
		$expired_documents_count = Util::count_expired_posts();

		$expired_inner_html = sprintf(
			/* translators: %s: Number of expired documents. */
			_nx(
				'Expired <span class="count">(%s)</span>',
				'Expired <span class="count">(%s)</span>',
				$expired_documents_count,
				'documents',
				'document-library-pro'
			),
			number_format_i18n( $expired_documents_count )
		);

		$link_args = [
			'url'     => esc_url( add_query_arg( [ 'post_type' => 'dlp_document', 'expired' => true ], 'edit.php' ) ),
			'label'   => $expired_inner_html,
			'current' => isset( $_REQUEST['expired'] ),
		];

		$views['expired'] = sprintf(
			'<a href="%s"%s>%s</a>',
			esc_url( $link_args['url'] ),
			isset( $link_args['current'] ) && true === $link_args['current'] ? ' class="current" aria-current="page"' : '',
			$link_args['label']
		);

		return $views;
	}

	/**
	 * Parse expired query
	 *
	 * @param \WP_Query $query
	 */
	public function parse_expired_view_query( $query ) {
		global $pagenow;

		if ( $pagenow !== 'edit.php' ) {
			return;
		}

		$query_vars = &$query->query_vars;

		if ( ! isset( $query_vars['post_type'] ) || $query_vars['post_type'] !== Post_Type::POST_TYPE_SLUG ) {
			return;
		}

		$expired = filter_input( INPUT_GET, 'expired', FILTER_VALIDATE_BOOLEAN );

		if ( ! $expired ) {
			return;
		}

		$query_vars['expired'] = true;
	}

	/**
	 * Handle expired query
	 *
	 * @param \WP_Query $query
	 */
	public function handle_expired_view_query( $query ) {
		if ( $query->get( 'expired' ) === true ) {
			$meta_query = $query->get( 'meta_query' );

			if ( ! is_array( $meta_query ) ) {
				$meta_query = [];
			}

			$meta_query[] = [
				'key'     => '_dlp_expiry_timestamp',
				'value'   => time(),
				'compare' => '<',
			];

			$query->set( 'meta_query', $meta_query );
		}
	}
}
