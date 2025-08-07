<?php
namespace Barn2\Plugin\Document_Library_Pro\Util;

use Imagick;

defined( 'ABSPATH' ) || exit;

/**
 * Media Library Utilities
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Media {

	/**
	 * Removes extension from a filename
	 *
	 * @param string $file_name
	 * @return string
	 */
	public static function get_filename_without_extension( $file_name ) {
		return substr( $file_name, 0, strrpos( $file_name, '.' ) );
	}

	/**
	 * Downloads a file from a URL and attaches it to the document
	 *
	 * @param  string   $url        Attachment URL.
	 * @param  int      $document_id Document ID
	 * @return int
	 * @throws \Exception If attachment cannot be loaded.
	 */
	public static function attach_file_from_url( $url, $document_id ) {
		if ( empty( $url ) ) {
			return 0;
		}

		$file_id    = 0;
		$upload_dir = wp_upload_dir( null, false );
		$base_url   = $upload_dir['baseurl'] . '/';

		// Check if the file could already be in the Media Library
		if ( strpos( $url, $base_url ) === 0 ) {
			$file_id = self::get_attachment_id_from_url( $url );
		}

		if ( ! $file_id ) {
			// This is an external URL or not in the media library, so compare to source.
			$args = [
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'fields'      => 'ids',
				'meta_query'  => [
					[
						'value' => $url,
						'key'   => '_dlp_attachment_source',
					],
				],
			];

			$file_ids = get_posts( $args );

			if ( $file_ids ) {
				$file_id = current( $file_ids );
			}
		}

		// Upload if attachment does not exists.
		if ( ! $file_id && stristr( $url, '://' ) ) {
			$upload = self::upload_document_from_url( $url );

			if ( is_wp_error( $upload ) ) {
				throw new \Exception( esc_html( $upload->get_error_message() ), 400 );
			}

			$file_id = self::set_uploaded_document_as_attachment( $upload, $document_id );

			// Save attachment source for future reference.
			update_post_meta( $file_id, '_dlp_attachment_source', $url );
		}

		if ( ! $file_id ) {
			/* translators: %s: document URL */
			throw new \Exception( sprintf( esc_html__( 'Unable to use document "%s".', 'document-library-pro' ), esc_url( $url ) ), 400 );
		}

		return $file_id;
	}

	/**
	 * Upload document from URL.
	 *
	 * @param   string              $document_url File URL.
	 * @return  array|\WP_Error     Attachment data or error message.
	 */
	private static function upload_document_from_url( $document_url ) {
		$parsed_url = wp_parse_url( $document_url );

		// Check parsed URL.
		if ( ! $parsed_url || ! is_array( $parsed_url ) ) {
			return new \WP_Error(
				'document_library_import_invalid_url',
				/* translators: %s: image URL */
				sprintf( __( 'Invalid URL %s.', 'document-library-pro' ), $document_url ),
				[ 'status' => 400 ]
			);
		}

		// Ensure url and filename is valid.
		$document_url   = esc_url_raw( $document_url );
		$safe_file_name = sanitize_file_name( urldecode( basename( current( explode( '?', $document_url ) ) ) ) );

		// download_url function is part of wp-admin.
		if ( ! function_exists( 'download_url' ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php';
		}

		/**
		 * Update imported File URL for specific file storage clouds
		 *
		 * @param string $document_url URL of file to be downloaded
		 *
		 * @hooked [ 'Barn2\Plugin\Document_Library_Pro\Util\Media', 'maybe_sanitize_dropbox_link' ]
		 * @hooked [ 'Barn2\Plugin\Document_Library_Pro\Util\Media', 'maybe_get_google_drive_download_link' ]
		 */
		$document_url = apply_filters( 'document_library_pro_import_document_url', $document_url );

		$file_array         = [];
		$file_array['name'] = $safe_file_name;

		// Download file to temp location.
		$file_array['tmp_name'] = download_url( $document_url );

		/**
		 * Filter mime types allowed to be brought into Document Library Pro
		 *
		 * @param array $mime_types in format provided by wp_get_mime_types()
		 */
		$mimes_allowed = apply_filters( 'document_library_pro_import_mimes_allowed', wp_get_mime_types() );

		// If error storing temporarily, return the error.
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return new \WP_Error(
				'document_library_import_invalid_remote_url',
				/* translators: %s: image URL */
				sprintf( __( 'Error getting remote document %s.', 'document-library-pro' ), $document_url ) . ' '
				/* translators: %s: error message */
				. sprintf( __( 'Error: %s', 'document-library-pro' ), $file_array['tmp_name']->get_error_message() ),
				[ 'status' => 400 ]
			);
		}

		// Get the file type of the temporary file
		$tmp_file_type = wp_check_filetype( $file_array['tmp_name'] );

		// Ensure the filename has the correct extension
		if ($tmp_file_type['ext'] && !preg_match('/\.' . $tmp_file_type['ext'] . '$/', $file_array['name'])) {
			$file_array['name'] .= '.' . $tmp_file_type['ext'];
		}

		/**
		 * Update file name of imported file
		 *
		 * @param array $file_array [ name, tmp_name ]
		 * @param string $document_url the sanitized File URL being imported
		 *
		 * @hooked [ 'Barn2\Plugin\Document_Library_Pro\Util\Media', 'maybe_update_imported_google_drive_file_name' ], 10, 2
		 */
		$file_array    = apply_filters( 'document_library_pro_import_file_array', $file_array, $document_url );

		// Do the validation and storage stuff.
		$file = wp_handle_sideload(
			$file_array,
			[
				'test_form' => false,
				'mimes'     => $mimes_allowed,
			],
			current_time( 'Y/m' )
		);

		if ( isset( $file['error'] ) ) {
			// phpcs:disable WordPress.WP.AlternativeFunctions.unlink_unlink
			// phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged
			@unlink( $file_array['tmp_name'] );
			// phpcs:enable WordPress.WP.AlternativeFunctions.unlink_unlink
			// phpcs:enable WordPress.PHP.NoSilencedErrors.Discouraged

			return new \WP_Error(
				'document_library_pro_import_invalid_document',
				sprintf(
					/* translators: %s: error message */
					__( 'Invalid document: %s', 'document-library-pro' ),
					$file['error']
				),
				[ 'status' => 400 ]
			);
		}

		return $file;
	}

	/**
	 * Set uploaded document as attachment.
	 *
	 * @param   array   $upload Upload information from wp_upload_bits.
	 * @param   int     $id Post ID. Default to 0.
	 * @return  int     Attachment ID
	 */
	private static function set_uploaded_document_as_attachment( $upload, $id = 0 ) {
		$info = wp_check_filetype( $upload['file'] );

		$attachment = [
			'post_mime_type' => $info['type'],
			'guid'           => $upload['url'],
			'post_parent'    => $id,
			'post_title'     => basename( $upload['file'] ),
			'post_content'   => '',
		];

		$attachment_id = wp_insert_attachment( $attachment, $upload['file'], $id );
		if ( ! is_wp_error( $attachment_id ) ) {
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
		}

		return $attachment_id;
	}

	/**
	 * Retrieves an attachment object based on a URL
	 *
	 * @param string $attachment_url
	 * @return mixed
	 */
	private static function get_attachment_id_from_url( $attachment_url ) {
		global $wpdb;

		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid=%s;", $attachment_url ) );

		if ( ! $attachment ) {
			return null;
		}

		return $attachment[0];
	}

	/**
	 * Cleans dropbox preview URLs for downloading.
	 *
	 * @param string $document_url
	 * @return string
	 */
	public static function maybe_sanitize_dropbox_link( $document_url ) {
		// check if potentially dropbox url
		if ( self::is_dropbox_link( $document_url ) === false ) {
			return $document_url;
		}

		// Check if we have a dropbox direct url
		if ( self::is_dropbox_direct_link( $document_url ) ) {
			return $document_url;
		}

		// Check if we have a dropbox normal url
		if ( self::is_dropbox_site_link( $document_url ) === false ) {
			return $document_url;
		}

		// ensure dl=1
		$document_url = add_query_arg( 'dl', '1', $document_url );

		return $document_url;
	}

	/**
	 * Get raw dropbox link for direct linking.
	 *
	 * @param string $document_url
	 *
	 * @return string
	 */
	public static function maybe_get_dropbox_direct_link( $document_url ) {
		// already a direct link
		if ( self::is_dropbox_direct_link( $document_url ) ) {
			return $document_url;
		}

		// ensure we have a dropbox link
		if ( self::is_dropbox_site_link( $document_url ) === false ) {
			return $document_url;
		}

		// remove any dl parameter
		$document_url = remove_query_arg( 'dl', $document_url );

		// add raw=1
		$document_url = add_query_arg( 'raw', '1', $document_url );

		return $document_url;
	}

	/**
	 * Get raw google drive link for direct downloading (primarily used by importer).
	 *
	 * @param string $document_url
	 *
	 * @return string
	 */
	public static function maybe_get_google_drive_download_link( $document_url ) {
		// already a direct link
		if ( self::is_google_drive_download_link( $document_url ) ) {
			return $document_url;
		}

		// ensure we have a google share link
		if ( self::is_google_drive_share_link( $document_url ) === false ) {
			return $document_url;
		}

		// replace /file/d path with /uc and export query parameter
		$document_url = str_replace( '/file/d/', '/uc?export=download&id=', $document_url );

		// find everything after the id and delete it
		$leftovers_pos = strpos( $document_url, '/', strpos( $document_url, '&id=' ) );
		$document_url  = substr( $document_url, 0, $leftovers_pos );

		return $document_url;
	}

	/**
	 * Check if a URL is a Dropbox link
	 *
	 * @param string $url
	 * @return bool
	 */
	public static function is_dropbox_link( $url ) {
		if ( strpos( $url, 'dropbox.com' ) !== false || strpos( $url, 'dropboxusercontent.com' ) !== false ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a URL is a Dropbox link
	 *
	 * @param string $url
	 * @return bool
	 */
	public static function is_dropbox_site_link( $url ) {
		$dropbox_urls = [
			'//dropbox.com/',
			'//www.dropbox.com/',
		];

		$is_dropbox = array_filter(
			$dropbox_urls,
			function ( $domain ) use ( $url ) {
				return stripos( $url, $domain ) !== false;
			}
		);

		return count( $is_dropbox ) > 0;
	}

	/**
	 * Check if a URL is a Google Drive preview link
	 *
	 * @param string $url
	 * @return bool
	 */
	public static function is_google_drive_share_link( $url ) {
		$share_urls = [
			'//drive.google.com/file/d',
		];

		$is_gdrive = array_filter(
			$share_urls,
			function ( $domain ) use ( $url ) {
				return stripos( $url, $domain ) !== false;
			}
		);

		return count( $is_gdrive ) > 0;
	}

	/**
	 * Check if a URL is a direct link to a Dropbox file
	 *
	 * @param string $url
	 * @return bool
	 */
	public static function is_dropbox_direct_link( $url ) {
		$direct_urls = [
			'//dl.dropbox.com/',
			'//dl.dropboxusercontent.com/',
		];

		$is_dropbox_direct_link = array_filter(
			$direct_urls,
			function ( $domain ) use ( $url ) {
				return stripos( $url, $domain ) !== false;
			}
		);

		return count( $is_dropbox_direct_link ) > 0;
	}

	/**
	 * Check if a URL is a direct link to a Google Drive file
	 *
	 * @param string $url
	 * @return bool
	 */
	public static function is_google_drive_download_link( $url ) {
		$direct_urls = [
			'https://drive.google.com/uc',
		];

		$is_google_drive_direct_domain = array_filter(
			$direct_urls,
			function ( $domain ) use ( $url ) {
				return stripos( $url, $domain ) !== false;
			}
		);

		return count( $is_google_drive_direct_domain ) > 0 && str_contains( $url, 'export=download' );
	}

	/**
	 * Google Drive download URLs don't include the original filename, so we extract it from the temporary file
	 *
	 * @param array $file_array [ name, tmp_name ]
	 * @param string $download_url
	 *
	 * @return array [ name, tmp_name ]
	 */
	public static function maybe_update_imported_google_drive_file_name( $file_array, $download_url ) {

		if ( ! self::is_google_drive_download_link( $download_url ) ) {
			return $file_array;
		}

		$file_array['name'] = basename( $file_array['tmp_name'] );

		return $file_array;

	}

	/**
	 * Convert file url to full path.
	 *
	 * @param string $url url of the file.
	 * @return string|bool
	 */
	public static function get_file_url( $url ) {

		if ( strpos( $url, '/../' ) !== false ) {
			return false;
		}

		$upload_dir = wp_get_upload_dir();

		$file = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $url );

		if ( file_exists( $file ) ) {
			return $file;
		}

		return false;
	}

	/**
	 * Get the file extension from a file name
	 *
	 * @param string $url
	 * @return string|null
	 */
	public static function maybe_get_filename_from_url( $url ) {
		$path = wp_parse_url( $url, PHP_URL_PATH );

		if ( ! $path ) {
			return null;
		}

		$potential_file_name = basename( $path );

		// Check if the file name has an extension
		if ( strpos( $potential_file_name, '.' ) === false ) {
			return null;
		}

		return $potential_file_name;
	}

	/**
	 * Determine the mime type of an external file
	 *
	 * Accepts a full URL to a file and returns the mime type
	 *
	 * @param string $filename
	 * @return string
	 */
	public static function infer_external_file_mime_type( $filename ) {
		$file_type = wp_check_filetype( $filename );

		if ( ! $file_type['type'] ) {
			$file_type['type'] = 'application/octet-stream';
		}

		return $file_type['type'];
	}

	/**
	 * Get allowed preview mime_types
	 */
	public static function get_allowed_preview_mime_types() {
		$mimes = [
			'application/pdf',
			'application/x-pdf',
			'image/jpeg',
			'image/gif',
			'image/png',
			'image/webp',
			'image/svg+xml',
			'video/mp4',
			'video/ogg',
			'audio/mp3',
			'audio/mp4',
			'audio/mpeg',
			'audio/ogg',
			'audio/aac',
			'audio/aacp',
			'audio/flac',
			'audio/wav',
			'audio/webm',
		];

		/**
		 * Filter Whether to allow Word, Excel and Powerpoint documents to be previewed with the Office Web Viewer.
		 *
		 * @param bool $enabled
		 */
		if ( apply_filters( 'document_library_pro_enable_ms_office_preview', true ) ) {
			$mimes = array_merge(
				$mimes,
				self::get_office_mime_types()
			);
		}

		return $mimes;
	}

	/**
	 * Returns the Office mime types for the Office Web Viewer.
	 *
	 * Specifically, this is Excel, Word, and PowerPoint.
	 *
	 * @return string[]
	 */
	public static function get_office_mime_types() {
		return array_merge(
			self::get_ms_word_mime_types(),
			self::get_ms_excel_mime_types(),
			self::get_ms_powerpoint_mime_types()
		);
	}

	/**
	 * Get the MS Word mime types
	 *
	 * @return string[]
	 */
	public static function get_ms_word_mime_types() {
		return [
			'application/msword',
			'application/vnd.ms-write',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-word.document.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'application/vnd.ms-word.template.macroEnabled.12',
		];
	}

	/**
	 * Get the MS Excel mime types
	 *
	 * @return string[]
	 */
	public static function get_ms_excel_mime_types() {
		return [
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.ms-excel.sheet.macroEnabled.12',
			'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'application/vnd.ms-excel.template.macroEnabled.12',
			'application/vnd.ms-excel.addin.macroEnabled.12',
		];
	}

	/**
	 * Get the MS PowerPoint mime types
	 *
	 * @return string[]
	 */
	public static function get_ms_powerpoint_mime_types() {
		return [
			'application/vnd.ms-powerpoint',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.template',
			'application/vnd.ms-powerpoint.template.macroEnabled.12',
			'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.slide',
			'application/vnd.ms-powerpoint.slide.macroEnabled.12',
		];
	}

	/**
	 * Determine if PDF previews are available in WP via Imagick.
	 *
	 * @return bool
	 */
	public static function has_pdf_previews() {
		$fallback_sizes = [
			'thumbnail',
			'medium',
			'large',
		];

		if ( empty( apply_filters( 'fallback_intermediate_image_sizes', $fallback_sizes, [] ) ) ) {
			return false;
		}

		if ( ! extension_loaded( 'imagick' ) ) {
			return false;
		}

		if ( ! wp_image_editor_supports( [ 'mime_type' => 'application/pdf' ] ) ) {
			return false;
		}

		if ( ! class_exists( 'Imagick' ) ) {
			return false;
		}

		$imagick = new Imagick();
		$formats = $imagick->queryFormats();

		if ( in_array( 'PDF', $formats, true ) ) {
			return true;
		}

		return false;
	}
}
