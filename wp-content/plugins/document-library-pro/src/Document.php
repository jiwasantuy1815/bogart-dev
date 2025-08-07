<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Util\SVG_Icon;
use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Util\Media as Media_Util;
use Barn2\Plugin\Document_Library_Pro\Util\Util;

defined( 'ABSPATH' ) || exit;

/**
 * Document Controller
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document {
	/**
	 * ID
	 *
	 * @var int
	 */
	protected $id = 0;
	public $post_object;

	/**
	 * Constructor
	 *
	 * @param integer   $id
	 * @param array     $data [ name, excerpt, file_size, ]
	 */
	public function __construct( $id = 0, $data = [] ) {
		if ( $id === 0 ) {
			$this->create_document( $data );
		} elseif ( is_int( $id ) || ctype_digit( $id ) ) {
			$this->fetch_document( $id );
		}
	}

	/**
	 * Creates a new document
	 *
	 * @param array $data
	 */
	protected function create_document( $data ) {
		// Create the WP_Post, Cats and Tags are handled here as well
		$document_id = $this->create_wp_post( $data );

		$this->id = $document_id;

		// Set file size
		if ( isset( $data['file_size'] ) && ! empty( $data['file_size'] ) ) {
			$this->set_file_size( $data['file_size'] );
		}

		// If we have a file_url download and attach it.
		if ( isset( $data['file_url'] ) && ! empty( $data['file_url'] ) ) {
			$file_id = Media_Util::attach_file_from_url( $data['file_url'], $this->id );
			$this->set_document_link( 'file', [ 'file_id' => $file_id ] );
			$this->set_file_type( $file_id );

			// Check for external (direct) url
		} elseif ( isset( $data['direct_url'] ) && ! empty( $data['direct_url'] ) ) {
			$this->set_document_link( 'url', [ 'direct_url' => $data['direct_url'] ] );
		}

		// Featured image url
		if ( isset( $data['featured_image'] ) && ! empty( $data['featured_image'] ) ) {
			$image_id = Media_Util::attach_file_from_url( $data['featured_image'], $this->id );
			set_post_thumbnail( $this->id, $image_id );
		}

		// Set expiry date
		if ( isset( $data['expiry_timestamp'] ) && ! empty( $data['expiry_timestamp'] ) ) {
			$this->set_expiry_timestamp( $data['expiry_timestamp'] );
		}

		// Set any custom meta data
		if ( isset( $data['meta_data'] ) ) {
			foreach ( $data['meta_data'] as $meta ) {
				$this->set_meta_data( $meta['key'], $meta['value'] );
			}
		}

		// Set any Advanced Custom Fields data
		if ( Util::is_acf_active() && isset( $data['acf'] ) ) {
			foreach ( $data['acf'] as $key => $value ) {
				$this->set_acf_data( $key, $value );
			}
		}

		// Set any Easy Post Types data
		if ( Util::is_ept_active() && isset( $data['ept'] ) ) {
			foreach ( $data['ept'] as $key => $value ) {
				$this->set_ept_data( $key, $value );
			}
		}
	}

	/**
	 * Fetch and setup existing document
	 *
	 * @param int $id
	 * @throws \Exception Document does not exist.
	 */
	protected function fetch_document( $id ) {
		$this->post_object = get_post( $id, 'object' );

		if ( is_null( $this->post_object ) ) {
			throw new \Exception( esc_html__( 'Document does not exist', 'document-library-pro' ) );
		}

		$this->id = $this->post_object->ID;
	}

	/**
	 * Creates the WP_Post and associates taxonomies
	 *
	 * @param   array $data
	 * @throws \Exception Failed to create document.
	 * @return  int $document_id
	 */
	protected function create_wp_post( $data ) {
		$default_data = [
			'name'         => '',
			'excerpt'      => '',
			'published'    => 1,
			'content'      => '',
			'category_ids' => [],
			'tag_ids'      => [],
			'author_ids'   => [],
		];

		$data = array_merge( $default_data, $data );

		$tax_input = [
			Taxonomies::CATEGORY_SLUG => $data['category_ids'],
			Taxonomies::TAG_SLUG      => $data['tag_ids'],
			Taxonomies::AUTHOR_SLUG   => $data['author_ids'],
		];

		if ( ! empty( $data['taxonomies'] ) ) {
			$tax_input = array_merge( $tax_input, $data['taxonomies'] );
		}

		$document_id = wp_insert_post(
			[
				// 'post_author',
				'post_title'   => $data['name'],
				'post_excerpt' => $data['excerpt'],
				'post_status'  => $data['published'] === -1 ? 'draft' : 'publish',
				'post_type'    => Post_Type::POST_TYPE_SLUG,
				'post_content' => $data['content'],
			],
			true
		);

		if ( is_wp_error( $document_id ) ) {
			throw new \Exception( esc_html__( 'Failed to create document.', 'document-library-pro' ) );
		}

		foreach ( $tax_input as $taxonomy => $terms ) {
			if ( ! empty( $terms ) ) {
				wp_set_object_terms( $document_id, $terms, $taxonomy );
			}
		}

		return $document_id;
	}

	/**
	 * Retrieves meta data from the post
	 *
	 * @param   string $key
	 * @return  string
	 */
	public function get_meta_data( $key ) {
		return get_post_meta( $this->id, $key, true );
	}

	/**
	 * Sets the document link data
	 *
	 * @param   string  $type 'url' | 'file' | 'none
	 * @param   array   $data Should contain 'direct_url' for 'url' or 'file_id' for 'file'
	 */
	public function set_document_link( $type, $data = [] ) {
		update_post_meta( $this->id, '_dlp_document_link_type', $type );

		switch ( $type ) {
			case 'none':
				wp_set_object_terms( $this->id, null, Taxonomies::FILE_TYPE_SLUG );
				delete_post_meta( $this->id, '_dlp_direct_link_url' );

				// Remove attached file
				$this->maybe_clean_up_attached_file();
				break;

			case 'url':
				if ( ! filter_var( $data['direct_url'], FILTER_VALIDATE_URL ) ) {
					$data['direct_url'] = sprintf( 'https://%s', $data['direct_url'] );
				}

				// Try get file extension or use 'www'
				$filename       = Media_Util::maybe_get_filename_from_url( $data['direct_url'] );
				$file_extension = $filename ? pathinfo( $filename, PATHINFO_EXTENSION ) : 'www';

				// Set data
				wp_set_object_terms( $this->id, $file_extension, Taxonomies::FILE_TYPE_SLUG );
				$this->set_meta_data( '_dlp_direct_link_url', $data['direct_url'] );

				// Remove attached file
				$this->maybe_clean_up_attached_file();
				break;

			case 'file':
				$this->maybe_clean_up_attached_file();

				if ( filter_var( $data['file_id'], FILTER_VALIDATE_INT ) ) {
					$this->set_file_id( $data['file_id'] );
					$this->autocalculate_file_size( $data['file_id'] );
					$this->set_file_type( $data['file_id'] );
					$this->maybe_set_file_as_featured_image();

					wp_set_object_terms( $data['file_id'], 'document-download', Taxonomies::DOCUMENT_DOWNLOAD_SLUG );
				} else {
					update_post_meta( $this->id, '_dlp_document_link_type', 'none' );
				}

				delete_post_meta( $this->id, '_dlp_direct_link_url' );
				break;
		}
	}

	/**
	 * Set the file size meta
	 *
	 * @param string $file_size
	 */
	public function set_file_size( $file_size ) {
		$this->set_meta_data( '_dlp_document_file_size', $file_size );
	}

	/**
	 * Set the download count meta
	 *
	 * @param string $download_count
	 */
	public function set_download_count( $download_count ) {
		$this->set_meta_data( '_dlp_download_count', $download_count );
	}

	/**
	 * Set the version history meta
	 *
	 * @param array $version_history
	 */
	public function set_version_history( $version_history ) {
		if ( Options::get_version_control_mode() === false ) {
			return;
		}

		$version_history = array_filter(
			$version_history,
			function ( $key ) {
				return in_array( $key, [ 'url', 'file' ], true );
			},
			ARRAY_FILTER_USE_KEY
		);

		$this->purge_attachments( $version_history );

		if ( empty( $version_history['url'] ) && empty( $version_history['file'] ) ) {
			delete_post_meta( $this->id, '_dlp_version_history' );
		} else {
			$this->set_meta_data( '_dlp_version_history', $version_history );
		}
	}

	/**
	 * Delete version control attachment.
	 *
	 * @param int|string $new_id
	 */
	private function maybe_delete_attachment( $new_id ) {
		if ( Options::get_version_control_mode() !== 'delete' ) {
			return;
		}

		$attachment_id = (int) $this->get_meta_data( '_dlp_attached_file_id' );

		if ( $attachment_id && $attachment_id !== $new_id ) {
			wp_delete_attachment( $attachment_id );
		}
	}

	/**
	 * Purge attachments that are no longer in the version history.
	 *
	 * @param array $version_history
	 */
	private function purge_attachments( $version_history ) {
		if ( Options::get_version_control_mode() === false ) {
			return;
		}

		$files                   = $version_history['file'] ?? [];
		$current_version_history = $this->get_version_history();
		$current_files           = $current_version_history['file'] ?? [];

		foreach ( $current_files as $attachment_id => $version ) {
			if ( ! isset( $files[ $attachment_id ] ) ) {
				wp_delete_attachment( $attachment_id );
			}
		}
	}

	/**
	 * Determines the attached file size.
	 *
	 * @param int $file_id
	 */
	private function autocalculate_file_size( $file_id ) {
		$file = get_attached_file( $file_id );

		if ( ! $file ) {
			return;
		}

		$file_size = filesize( $file );

		if ( ! $file_size ) {
			return;
		}

		$this->set_file_size( size_format( $file_size ) );
	}

	/**
	 * Set the file id meta
	 *
	 * @param string $file_id
	 */
	public function set_file_id( $file_id ) {
		$this->maybe_delete_attachment( (int) $file_id );
		$this->set_meta_data( '_dlp_attached_file_id', $file_id );
	}

	/**
	 * Sets meta data
	 *
	 * @param string $key
	 * @param string $value
	 */
	protected function set_meta_data( $key, $value ) {
		update_post_meta( $this->id, $key, $value );
	}

	/**
	 * Sets Advanced Custom Fields data
	 *
	 * @param string $key
	 * @param string $value
	 */
	protected function set_acf_data( $key, $value ) {
		update_field( $key, $value, $this->id );
	}

	/**
	 * Sets Easy Post Types data
	 *
	 * @param string $key
	 * @param string $value
	 */
	protected function set_ept_data( $key, $value ) {
		update_post_meta( $this->id, 'dlp_document_' . $key, $value );
	}

	/**
	 * Sets the expiry timestamp
	 *
	 * @param mixed $timestamp
	 */
	public function set_expiry_timestamp( $timestamp ) {
		$this->set_meta_data( '_dlp_expiry_timestamp', $timestamp );
	}

	/**
	 * Sets the file type taxonomy if there is associated file
	 */
	protected function set_file_type() {
		$file_name = $this->get_file_name();

		if ( $file_name ) {
			$file_type = wp_check_filetype( $file_name );

			if ( isset( $file_type['ext'] ) ) {
				wp_set_object_terms( $this->id, $file_type['ext'], Taxonomies::FILE_TYPE_SLUG );
			}
		}
	}

	/**
	 * Downloads a file from a URL and attaches it to the document
	 *
	 * @param  string   $url        Attachment URL.
	 */
	protected function attach_file_from_url( $url ) {
		_deprecated_function( __METHOD__, '1.2.3', esc_html( Media_Util::class . '::attach_file_from_url' ) );

		Media_Util::attach_file_from_url( $url, $this->id );
	}

	/**
	 * Returns the document ID
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Retrieves the attached file id
	 *
	 * @return string
	 */
	public function get_file_id() {
		return $this->get_meta_data( '_dlp_attached_file_id' );
	}

	/**
	 * Retrieves the document expiry status
	 *
	 * @return string
	 */
	public function get_expiry_status() {
		$expiry_timestamp = get_post_meta( $this->id, '_dlp_expiry_timestamp', true );

		if ( ! $expiry_timestamp ) {
			return 'never';
		}

		if ( $expiry_timestamp < time() ) {
			return 'expired';
		}

		return 'active';
	}

	/**
	 * Retrieves the document expiry timestamp
	 *
	 * @return string
	 */
	public function get_expiry_timestamp() {
		$expiry_timestamp = get_post_meta( $this->id, '_dlp_expiry_timestamp', true );

		if ( ! $expiry_timestamp ) {
			return null;
		}

		return $expiry_timestamp;
	}

	/**
	 * Retrieves a list of files and urls attached to this document.
	 *
	 * This method can be passed an option $type parameter. If no parameter is passed,
	 * $type will default to the result of get_link_type().
	 * If any truthy value other than 'url' or 'file' is passed,
	 * the version history for both link types will be returned.
	 *
	 * @since 1.8.0
	 * @return bool|array A list of file versions or false if version history is disabled
	 */
	public function get_version_history() {
		if ( Options::get_version_control_mode() === false ) {
			return [];
		}

		$version_history = array_filter( (array) $this->get_meta_data( '_dlp_version_history' ) );

		foreach ( $version_history as $type => $type_history ) {
			$key = $type === 'file' ? $this->get_file_id() : md5( $this->get_direct_link() );

			if ( $key && ! isset( $type_history[ $key ] ) ) {
				// if the current url or file is not part of the version history
				// the version history was activated or reactivated after the current document link was set
				// so we add that document link as the first element in the current version history
				// and we set the last_used date as the date when the document was last modified
				$type_history = [
					$key => [
						'version'   => '',
						'last_used' => get_post_datetime( $this->post_object, 'modified' ),
					],
				] + $type_history;
			}

			uasort(
				$type_history,
				function ( $a, $b ) {
					if ( is_array( $a ) && is_array( $b ) ) {
						return (int) $b['last_used'] <=> (int) $a['last_used'];
					}

					return 0;
				}
			);

			$version_history[ $type ] = $type_history;
		}

		return $version_history;
	}

	/**
	 * Retrieves the attached file name
	 *
	 * @param string $link
	 * @return string
	 */
	public function get_file_name( $link = 'none', $new_tab = false ) {
		$filename = false;

		if ( $this->get_link_type() === 'file' ) {
			$file_path = get_attached_file( $this->get_file_id() );
			$filename  = $file_path ? wp_basename( $file_path ) : false;
		}

		if ( $this->get_link_type() === 'url' ) {
			$filename = Media_Util::maybe_get_filename_from_url( $this->get_direct_link() );
		}

		if ( $link === 'download_file' ) {
			$href = $this->get_download_url() ? $this->get_download_url() : get_permalink( $this->get_id() );
		} elseif ( $link === 'single_document' ) {
			$href = get_permalink( $this->get_id() );
		} else {
			$href = false;
		}

		if ( $href && $filename ) {
			$filename = sprintf( '<a href="%1$s" %2$s>%3$s</a>', $href, $new_tab ? 'target="_blank"' : '', $filename );
		}

		return $filename;
	}

	/**
	 * Retrieves the title and links it if requested.
	 *
	 * @param string $link
	 * @param bool $new_tab
	 */
	public function get_title( $link = 'none', $new_tab = false ) {
		$title = get_the_title( $this->post_object );

		if ( $link === 'none' ) {
			return $title;
		}

		if ( $link === 'download_file' ) {
			$href = $this->get_download_url() ? $this->get_download_url() : get_permalink( $this->get_id() );
		} elseif ( $link === 'single_document' ) {
			$href = get_permalink( $this->get_id() );
		} else {
			$href = false;
		}

		if ( $href ) {
			$title = sprintf( '<a href="%1$s" %2$s>%3$s</a>', $href, $new_tab ? 'target="_blank"' : '', $title );
		}

		return $title;
	}

	/**
	 * Gets the direct link URL
	 *
	 * @return string
	 */
	public function get_direct_link() {
		return $this->get_meta_data( '_dlp_direct_link_url' );
	}

	/**
	 * Gets the link type
	 *
	 * @return string
	 */
	public function get_link_type() {
		$saved_meta = $this->get_meta_data( '_dlp_document_link_type' );

		return $saved_meta ? $saved_meta : 'none';
	}

	/**
	 * Get the filename
	 */
	public function get_filename() {
		$file_name = $this->get_file_name();

		if ( $file_name ) {
			return $file_name;
		}

		$direct_link = $this->get_direct_link();

		if ( $direct_link ) {
			$direct_link = wp_parse_url( $direct_link, PHP_URL_PATH );
			$direct_link = explode( '/', $direct_link );

			return end( $direct_link );
		}

		return false;
	}

	/**
	 * Gets the associated file type
	 *
	 * @return string
	 */
	public function get_file_type( $link = false, $new_tab = false ) {
		$file_types = wp_get_object_terms( $this->id, Taxonomies::FILE_TYPE_SLUG );

		if ( is_wp_error( $file_types ) || ! is_array( $file_types ) || ! isset( $file_types[0] ) ) {
			return '';
		}

		if ( $link ) {
			return sprintf( '<a href="%1$s" %3$s>%2$s</a>', get_term_link( $file_types[0] ), $file_types[0]->name, $new_tab ? ' target="_blank"' : '' );
		} else {
			return strtolower( $file_types[0]->name );
		}
	}

	/**
	 * Retrieves the associated file type icon
	 *
	 * @return string $file_icon
	 */
	public function get_file_icon() {
		$file_type = $this->get_file_type();

		if ( $file_type ) {
			$file_icon = SVG_Icon::get( SVG_Icon::get_file_extension_icon( $file_type ), [ 'dlp-file-icon' ] );
		} else {
			$file_icon = SVG_Icon::get( 'default', [ 'dlp-file-icon' ] );
		}

		return $file_icon;
	}

	/**
	 * Gets the file size meta
	 *
	 * @return string
	 */
	public function get_file_size() {
		return $this->get_meta_data( '_dlp_document_file_size' );
	}

	/**
	 * Gets the file size meta
	 *
	 * @return string
	 */
	public function get_download_count() {
		return $this->get_meta_data( '_dlp_download_count' );
	}

	/**
	 * Gets the comma seperated category list.
	 *
	 * @param bool $link
	 * @param bool $new_tab
	 * @return string $category_list
	 */
	public function get_category_list( $link = false, $new_tab = false ) {

		if ( $link ) {
			$category_list = $this->get_the_linked_term_names( $this->id, Taxonomies::CATEGORY_SLUG, ', ', $new_tab );
		} else {
			$category_list = $this->get_the_term_names( $this->id, Taxonomies::CATEGORY_SLUG );
		}

		return $category_list;
	}

	/**
	 * Gets the comma seperated tag list
	 *
	 * @param bool $link
	 * @param bool $new_tab
	 * @return string $tag_list
	 */
	public function get_tag_list( $link = false, $new_tab = false ) {
		if ( $link ) {
			$tag_list = $this->get_the_linked_term_names( $this->id, Taxonomies::TAG_SLUG, ', ', $new_tab );
		} else {
			$tag_list = $this->get_the_term_names( $this->id, Taxonomies::TAG_SLUG );
		}

		return $tag_list;
	}

	/**
	 * Gets the comma seperated author list
	 *
	 * @param bool $link
	 * @param bool $new_tab
	 * @return string $author_list
	 */
	public function get_author_list(  $link = false, $new_tab = false ) {
		if ( $link ) {
			$author_list = $this->get_the_linked_term_names( $this->id, Taxonomies::AUTHOR_SLUG, ', ', $new_tab );
		} else {
			$author_list = $this->get_the_term_names( $this->id, Taxonomies::AUTHOR_SLUG );
		}

		return $author_list;
	}

	/**
	 * Gets the custom fields list.
	 *
	 * @return string $custom_fields_list
	 */
	public function get_custom_fields_list() {
		$meta = get_post_meta( $this->id );

		if ( function_exists( '\Barn2\Plugin\Easy_Post_Types_Fields\ept' ) ) {
			$ept_custom_fields = \Barn2\Plugin\Easy_Post_Types_Fields\Util::get_custom_fields( 'dlp_document' );
		}

		$custom_fields_list = [];
		foreach ( (array) $meta as $key => $values ) {
			if ( ! is_int( $key ) && strpos( $key, '_' ) !== 0 ) {
				$label = $key;
				foreach ( $values as $value_key => $value ) {
					if ( function_exists( 'get_field_object' ) && $field = get_field_object( $key, $this->id ) ) {
						$label = $field['label'];
						$value = $field['value'];
					} elseif ( function_exists( '\Barn2\Plugin\Easy_Post_Types_Fields\ept' ) ) {
						foreach ( $ept_custom_fields as $ept_custom_field ) {
							if ( $key === 'dlp_document_' . $ept_custom_field['slug'] ) {
								$label = $ept_custom_field['name'];
							}
						}
					}
					$custom_fields_list[ $key ]['value'] = ( $custom_fields_list[ $key ]['value'] ?? '' ) . ( $value_key > 0 ? ', ' : '' ) . $value;
				}
				$custom_fields_list[ $key ]['label'] = $label;
			}
		}

		$custom_fields_list = apply_filters( 'document_library_pro_custom_fields', $custom_fields_list, $this->id );

		return $custom_fields_list;
	}

	/**
	 * Retrieves a list of term names.
	 *
	 * @param WP_Post $post
	 * @param string $taxonomy
	 * @param string $sep
	 * @return false|string
	 */
	private function get_the_term_names( $post, $taxonomy, $sep = ', ' ) {
		$terms = get_the_terms( $post, $taxonomy );

		if ( ! $terms || ! is_array( $terms ) ) {
			return false;
		}

		return implode( $sep, wp_list_pluck( $terms, 'name' ) );
	}

	/**
	 * Retrieves a linked list of term names.
	 *
	 * @param WP_Post $post
	 * @param string $taxonomy
	 * @param string $sep
	 * @param bool $new_tab
	 * @return false|string
	 */
	private function get_the_linked_term_names( $post, $taxonomy, $sep = ', ', $new_tab = false ) {
		$terms = get_the_terms( $post, $taxonomy );

		if ( ! $terms || ! is_array( $terms ) ) {
			return false;
		}

		$linked_term_names = array_map(
			function ( $term ) use ( $new_tab ) {
				return sprintf( '<a href="%1$s" %3$s>%2$s</a>', get_term_link( $term ), $term->name, $new_tab ? ' target="_blank"' : '' );
			},
			$terms
		);

		return implode( $sep, $linked_term_names );
	}

	/**
	 * Gets the download URL
	 *
	 * @return string
	 */
	public function get_download_url() {
		switch ( $this->get_link_type() ) {
			case 'none':
				$url = false;
				break;

			case 'url':
				$url = $this->get_direct_link();
				break;

			case 'file':
				$url = wp_get_attachment_url( $this->get_file_id() );
				break;

			default:
				$url = false;
				break;
		}

		return $url;
	}

	/**
	 * Get's the preview URL.
	 *
	 * Mainly used to format Dropbox URLs for the preview.
	 */
	public function get_preview_url() {
		$url = $this->get_download_url();

		if ( Media_Util::is_dropbox_link( $url ) ) {
			$url = Media_Util::maybe_get_dropbox_direct_link( $url );
		}

		/**
		 * Filter: Allows you to make changes to the preview url for a document.
		 *
		 * @param string $url
		 * @param Document $document
		 */
		$url = apply_filters( 'document_library_pro_preview_url', $url, $this );

		return $url;
	}

	/**
	 * Gets the link version, if any
	 *
	 * @return string
	 */
	public function get_link_version() {
		if ( Options::get_version_control_mode() === false ) {
			return false;
		}

		$link_type = $this->get_link_type();

		switch ( $link_type ) {
			case 'none':
				$version = false;
				break;

			case 'url':
			case 'file':
				$version_history = $this->get_version_history();
				if ( empty( $version_history ) || ! isset( $version_history[ $link_type ] ) || empty( $version_history[ $link_type ] ) ) {
					$version = false;
				}

				$latest_version = current( $version_history[ $link_type ] );
				$version        = $latest_version['version'] ?? false;
				break;

			default:
				$version = false;
				break;
		}

		return $version;
	}

	/**
	 * Generate the download button HTML markup.
	 *
	 * @param string $link_text
	 * @param string $link_style
	 * @param string $link_destination
	 * @param bool $link_target
	 * @return string
	 */
	public function get_download_button( $link_text, $link_style = 'button', $link_destination = 'direct', $link_target = false ) {
		/**
		 * Filter: when used it can determine if the download button
		 * should be displayed or not.
		 *
		 * @param boolean $display
		 * @param Document $document
		 * @param string $link_text
		 * @param string $link_style
		 * @param string $view
		 */
		$should_display = apply_filters( 'document_library_pro_download_button_should_display', true, $this, $link_text, $link_style, $link_destination );

		if ( ! $should_display ) {
			return '';
		}

		$link_text    = $this->ensure_download_button_link_text( $link_text );
		$button_class = in_array( $link_style, [ 'icon_only', 'icon', 'text' ], true ) ? '' : apply_filters( 'document_library_pro_button_column_button_class', 'dlp-download-button document-library-pro-button button btn' );

		// Add additional class for single link destination.
		if ( $link_destination === 'single' ) {
			$button_class .= ' dlp-single-link';
		}

		$href_attribute     = $this->get_download_button_href( $link_destination );
		$download_attribute = $link_destination === 'direct' ? $this->get_download_button_attributes( $link_target ) : '';
		$target_attribute   = $link_target === true ? 'target="_blank" ' : '';

		$anchor_open = sprintf(
			'<a href="%1$s" class="dlp-download-link %2$s" data-download-id="%3$d" %4$s%5$s>',
			esc_url( $href_attribute ),
			esc_attr( $button_class ),
			$this->id,
			$target_attribute,
			$download_attribute
		);

		$anchor_text = [
			'button'           => $link_text,
			'button_icon_text' => SVG_Icon::get( 'download', [ 'dlp-button-icon', 'dlp-button-icon-text' ] ) . $link_text,
			'button_icon'      => SVG_Icon::get( 'download', [ 'dlp-button-icon' ] ),
			'icon_only'        => SVG_Icon::get( 'download', [ 'dlp-button-icon' ] ),
			'icon'             => $this->get_file_icon(), // file type icon
			'text'             => $link_text,
		];

		$anchor_close = '</a>';

		return $anchor_open . $anchor_text[ $link_style ] . $anchor_close;
	}

	/**
	 * Generate the download button HTML markup.
	 *
	 * @param string $link_text
	 * @param string $link_style
	 * @param bool $link_icon
	 * @param string $link_destination
	 * @param bool $link_target
	 * @return string
	 */
	public function get_download_button_v2( $link_text, $link_style = 'button', $link_icon = false, $link_destination = 'direct', $link_target = false ) {
		/**
		 * Filter: when used it can determine if the download button
		 * should be displayed or not.
		 *
		 * @param boolean $display
		 * @param Document $document
		 * @param string $link_text
		 * @param string $link_style
		 * @param string $view
		 */
		$should_display = apply_filters( 'document_library_pro_download_button_should_display', true, $this, $link_text, $link_style, $link_destination );

		if ( ! $should_display ) {
			return '';
		}

		$button_class = $link_style === 'button' ? apply_filters( 'document_library_pro_button_column_button_class', 'dlp-download-button document-library-pro-button button btn' ) : '';

		// Add additional class for single link destination.
		if ( $link_destination === 'single_document' ) {
			$button_class .= ' dlp-single-link';
		}

		$href_attribute     = $this->get_download_button_href( $link_destination );
		$download_attribute = $link_destination === 'download_file' ? $this->get_download_button_attributes( $link_target ) : '';
		$target_attribute   = $link_target === true ? 'target="_blank" ' : '';

		$anchor_open = sprintf(
			'<a href="%1$s" class="dlp-download-link %2$s" data-download-id="%3$d" %4$s%5$s>',
			esc_url( $href_attribute ),
			esc_attr( $button_class ),
			$this->id,
			$target_attribute,
			$download_attribute
		);

		$anchor_text = $this->get_download_button_anchor_text( $link_style, $link_icon, $link_text );

		$anchor_close = '</a>';

		return $anchor_open . $anchor_text . $anchor_close;
	}

	/**
	 * Get the download button anchor text.
	 *
	 * @param mixed $link_style
	 * @param mixed $link_icon
	 * @param mixed $link_text
	 * @return mixed|void
	 */
	private function get_download_button_anchor_text( $link_style, $link_icon, $link_text ) {
		if ( $link_style === 'file_icon' ) {
			return $this->get_file_icon();
		}

		if ( $link_icon === false ) {
			return $this->ensure_download_button_link_text( $link_text );
		} elseif ( empty( $link_text ) ) {
			return SVG_Icon::get( 'download', [ 'dlp-button-icon' ] );
		} else {
			return SVG_Icon::get( 'download', [ 'dlp-button-icon', 'dlp-button-icon-text' ] ) . $this->ensure_download_button_link_text( $link_text );
		}
	}

	/**
	 * Retrieve the download button 'href' attribute.
	 *
	 * @param string $link_destination
	 * @return string|false
	 */
	private function get_download_button_href( $link_destination ) {
		if ( in_array( $link_destination, [ 'download_file', 'direct' ], true ) ) {
			$href = $this->get_download_url() ? $this->get_download_url() : get_permalink( $this->get_id() );
		} else {
			$href = get_permalink( $this->get_id() );
		}

		return $href;
	}

	/**
	 * Retrieves the 'download' attribute.
	 *
	 * @param bool $link_target
	 * @return string
	 */
	private function get_download_button_attributes( $link_target = false ) {

		if ( $this->get_link_type() !== 'file' ) {
			return '';
		}

		$mime_type = get_post_mime_type( $this->get_file_id() );

		if ( $link_target && ( strpos( $mime_type, 'image/' ) !== false || $mime_type === 'application/pdf' ) ) {
			return '';
		}

		return sprintf( ' download="%1$s" type="%2$s"', basename( get_attached_file( $this->get_file_id() ) ), $mime_type );
	}

	/**
	 * Retrieves the download button text
	 *
	 * @param string $link_text
	 * @return string
	 */
	private function ensure_download_button_link_text( $link_text ) {
		$link_text = $link_text ? $link_text : get_the_title( $this->get_id() );

		return apply_filters( 'document_library_pro_button_column_button_text', $link_text );
	}

	/**
	 * Generate the Preview button HTML markup
	 *
	 * The preview scripts should still be enqueued if this is
	 * used outside of the core DLP preview areas.
	 *
	 * @param string $link_text
	 * @param string $link_style
	 * @param string $view
	 * @return string
	 */
	public function get_preview_button( $link_text, $link_style = 'button_icon', $view = 'table' ) {
		/**
		 * Filter: when used it can determine if the preview button
		 * should be displayed or not.
		 *
		 * @param boolean $display
		 * @param Document $document
		 * @param string $link_text
		 * @param string $link_style
		 * @param string $view
		 */
		$should_display = apply_filters( 'document_library_pro_preview_button_should_display', true, $this, $link_text, $link_style, $view );

		if ( ! $this->is_allowed_preview_mime_type() || ! $should_display ) {
			return '';
		}

		$download_url = $this->get_preview_url();
		$mime_type    = $this->get_mime_type();
		$button_class = in_array( $link_style, [ 'icon_only', 'link' ], true ) ? '' : apply_filters( 'document_library_pro_button_column_button_class', 'document-library-pro-button button btn' );
		$button_class = $this->is_allowed_preview_mime_type() ? $button_class : sprintf( '%s preview-disabled', $button_class );

		$anchor_open = sprintf(
			'<a href="javascript:void(0)" class="dlp-preview-button %1$s" data-download-url="%2$s" data-download-type="%3$s" data-title="%4$s" data-view="%5$s" data-download-id="%6$d">',
			esc_attr( $button_class ),
			esc_attr( $download_url ),
			esc_attr( $mime_type ),
			esc_attr( get_the_title( $this->id ) ),
			esc_attr( $view ),
			$this->id
		);

		$anchor_text = [
			'button'           => $link_text,
			'button_icon_text' => SVG_Icon::get( 'preview', [ 'dlp-button-icon', 'dlp-button-icon-text' ] ) . $link_text,
			'button_icon'      => SVG_Icon::get( 'preview', [ 'dlp-button-icon' ] ),
			'icon_only'        => SVG_Icon::get( 'preview', [ 'dlp-button-icon' ] ),
			'link'             => $link_text,
		];

		$anchor_close = '</a>';

		return $anchor_open . $anchor_text[ $link_style ] . $anchor_close;
	}

	/**
	 * Generate the Preview button HTML markup
	 *
	 * The preview scripts should still be enqueued if this is
	 * used outside of the core DLP preview areas.
	 *
	 * @param string $preview_text
	 * @param string $preview_style
	 * @param bool $preview_icon
	 * @param string $view
	 * @return string
	 */
	public function get_preview_button_v2( $preview_text, $preview_style = 'button', $preview_icon = false, $view = 'table' ) {
		/**
		 * Filter: when used it can determine if the preview button
		 * should be displayed or not.
		 *
		 * @param boolean $display
		 * @param Document $document
		 * @param string $preview_text
		 * @param string $preview_style
		 * @param string $view
		 */
		$should_display = apply_filters( 'document_library_pro_preview_button_should_display', true, $this, $preview_text, $preview_style, $view );

		if ( ! $this->is_allowed_preview_mime_type() || ! $should_display ) {
			return '';
		}

		$download_url = $this->get_preview_url();
		$mime_type    = $this->get_mime_type();
		$button_class = $preview_style === 'button' ? apply_filters( 'document_library_pro_button_column_button_class', 'document-library-pro-button button btn' ) : '';
		$button_class = $this->is_allowed_preview_mime_type() ? $button_class : sprintf( '%s preview-disabled', $button_class );

		$anchor_open = sprintf(
			'<a href="javascript:void(0)" class="dlp-preview-button %1$s" data-download-url="%2$s" data-download-type="%3$s" data-title="%4$s" data-view="%5$s" data-download-id="%6$d">',
			esc_attr( $button_class ),
			esc_attr( $download_url ),
			esc_attr( $mime_type ),
			esc_attr( get_the_title( $this->id ) ),
			esc_attr( $view ),
			$this->id
		);

		$anchor_text = $this->get_preview_button_anchor_text( $preview_icon, $preview_text );

		$anchor_close = '</a>';

		return $anchor_open . $anchor_text . $anchor_close;
	}

	/**
	 * Retrieves the anchor text for the preview button.
	 *
	 * @param bool $preview_icon
	 * @param string $preview_text
	 * @return string
	 */
	private function get_preview_button_anchor_text( $preview_icon, $preview_text ) {
		if ( $preview_icon === false ) {
			return $preview_text;
		} elseif ( empty( $preview_text ) ) {
			return SVG_Icon::get( 'preview', [ 'dlp-button-icon' ] );
		} else {
			return SVG_Icon::get( 'preview', [ 'dlp-button-icon', 'dlp-button-icon-text' ] ) . $preview_text;
		}
	}

	/**
	 * Retrieves the mime type of the file.
	 *
	 * @return string|null
	 */
	public function get_mime_type() {
		$mime_type = null;

		if ( $this->get_link_type() === 'file' ) {
			$mime_type = get_post_mime_type( $this->get_file_id() );
		}

		if ( $this->get_link_type() === 'url' && $filename = Media_Util::maybe_get_filename_from_url( $this->get_direct_link() ) ) {
			$mime_type = Media_Util::infer_external_file_mime_type( $filename );
		}

		return $mime_type;
	}

	/**
	 * Determine if the file mime type can have a preview.
	 *
	 * @return bool
	 */
	public function is_allowed_preview_mime_type() {
		if ( $this->get_link_type() === 'none' ) {
			return false;
		}

		$mime_type     = $this->get_mime_type();
		$allowed_mimes = Media_Util::get_allowed_preview_mime_types();

		if ( $this->maybe_check_office_file_exceeds_size_limit() ) {
			return false;
		}

		return in_array( $mime_type, $allowed_mimes, true );
	}

	/**
	 * Checks if the document is an MS Office document and exceeds the preview limit.
	 *
	 * @return bool
	 */
	private function maybe_check_office_file_exceeds_size_limit() {
		if ( ! apply_filters( 'document_library_pro_enable_ms_office_preview', true ) ) {
			return false;
		}

		if ( $this->get_link_type() !== 'file' ) {
			return false;
		}

		if ( ! in_array( $this->get_mime_type(), Media_Util::get_office_mime_types(), true ) ) {
			return false;
		}

		// Get the actual current file size
		$file = get_attached_file( $this->get_file_id() );

		if ( ! $file ) {
			return false;
		}

		$file_size = filesize( $file );

		// Word - 100MB
		if ( in_array( $this->get_mime_type(), Media_Util::get_ms_word_mime_types(), true ) && $file_size > 100 * 1024 * 1024 ) {
			return true;
		}

		// Excel - 25MB
		if ( in_array( $this->get_mime_type(), Media_Util::get_ms_excel_mime_types(), true ) && $file_size > 25 * 1024 * 1024 ) {
			return true;
		}

		// PowerPoint - 300MB
		if ( in_array( $this->get_mime_type(), Media_Util::get_ms_powerpoint_mime_types(), true ) && $file_size > 300 * 1024 * 1024 ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if we have a valid file and if so, set it as the featured image
	 */
	public function maybe_set_file_as_featured_image() {
		// check if we already have a featured image
		if ( has_post_thumbnail( $this->id ) ) {
			return false;
		}

		// check if we have a file
		if ( $this->get_link_type() !== 'file' || ! $this->get_file_id() ) {
			return false;
		}

		if ( get_post_meta( $this->id, '_dlp_skip_auto_featured_image', true ) ) {
			delete_post_meta( $this->id, '_dlp_skip_auto_featured_image' );

			return false;
		}

		/**
		 * Filter the condition determining whether the uploaded file should be used as a featured image
		 *
		 * This will only apply if the file being uploaded is an image.
		 *
		 * @param boolean                             $use_file    Whether the uploaded attachment should be used as a featured image
		 * @param Barn2\Document_Library_Pro\Document $document    The document object being created
		 * @param int                                 $file_id     The id of the upoaded file
		 */
		$use_file_as_featured_image = apply_filters( 'document_library_pro_use_file_as_featured_image', true, $this, $this->get_file_id() );

		if ( ! $use_file_as_featured_image ) {
			return false;
		}

		// handle image types
		if ( wp_attachment_is_image( $this->get_file_id() ) ) {
			set_post_thumbnail( $this->id, $this->get_file_id() );
			update_post_meta( $this->id, '_dlp_has_auto_featured_image', $this->get_file_id() );

			return true;
		}

		// handle PDF (check they have thumbnail generated by WP)
		if ( wp_attachment_is( 'pdf', $this->get_file_id() ) && ! empty( wp_get_attachment_image( $this->get_file_id(), 'full' ) ) ) {
			set_post_thumbnail( $this->id, $this->get_file_id() );
			update_post_meta( $this->id, '_dlp_has_auto_featured_image', $this->get_file_id() );

			return true;
		}

		return false;
	}

	/**
	 * Clean up attached file (used for switching/updating document link)
	 */
	public function maybe_clean_up_attached_file() {
		if ( ! $this->get_file_id() || ! is_numeric( $this->get_file_id() ) ) {
			return;
		}

		wp_set_object_terms( $this->get_file_id(), null, Taxonomies::DOCUMENT_DOWNLOAD_SLUG );
		delete_post_meta( $this->id, '_dlp_attached_file_id' );

		// check if we have auto featured image set
		if ( get_post_meta( $this->id, '_dlp_has_auto_featured_image', true ) ) {
			delete_post_meta( $this->id, '_dlp_has_auto_featured_image' );
			delete_post_thumbnail( $this->id );
		}
	}
}
