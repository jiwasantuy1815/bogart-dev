<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Premium_Service;

defined( 'ABSPATH' ) || exit;

/**
 * Register REST fields for document meta.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Rest_Fields implements Registerable, Premium_Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'init', [ $this, 'register_rest_fields' ], 15 );
	}

    /**
     * Register the REST fields.
     */
    public function register_rest_fields() {
        register_rest_field( Post_Type::POST_TYPE_SLUG, 'download_url', [
            'get_callback'    => [ $this, 'get_download_url' ],
            'update_callback' => null,
            'schema'          => null,
        ] );

        register_rest_field( Post_Type::POST_TYPE_SLUG, 'file_size', [
            'get_callback'    => [ $this, 'get_file_size' ],
            'update_callback' => null,
            'schema'          => null,
        ] );

        register_rest_field( Post_Type::POST_TYPE_SLUG, 'filename', [
            'get_callback'    => [ $this, 'get_filename' ],
            'update_callback' => null,
            'schema'          => null,
        ] );

        register_rest_field( Post_Type::POST_TYPE_SLUG, 'download_count', [
            'get_callback'    => [ $this, 'get_download_count' ],
            'update_callback' => null,
            'schema'          => null,
        ] );

        register_rest_field( Post_Type::POST_TYPE_SLUG, 'version_history', [
            'get_callback'    => [ $this, 'get_version_history' ],
            'update_callback' => null,
            'schema'          => null,
        ] );
    }

    /**
     * Get the document link.
     *
     * @param array $object The object from the response.
     *
     * @return string The document link.
     */
    public function get_download_url( $object ) {
        $document = new Document( $object['id'] );

        return $document->get_download_url();
    }

    /**
     * Get the file size.
     *
     * @param array $object The object from the response.
     *
     * @return string The file size.
     */
    public function get_file_size( $object ) {
        $document = new Document( $object['id'] );

        return $document->get_file_size();
    }

    /**
     * Get the filename.
     *
     * @param array $object The object from the response.
     *
     * @return string The filename.
     */
    public function get_filename( $object ) {
        $document = new Document( $object['id'] );

        return $document->get_file_name();
    }

    /**
     * Get the download count.
     *
     * @param array $object The object from the response.
     *
     * @return int The download count.
     */
    public function get_download_count( $object ) {
        $document = new Document( $object['id'] );

        return $document->get_download_count();
    }

    /**
     * Get the version history.
     *
     * @param array $object The object from the response.
     *
     * @return array The version history.
     */
    public function get_version_history( $object ) {
        $document = new Document( $object['id'] );

        return $document->get_version_history();
    }
}
