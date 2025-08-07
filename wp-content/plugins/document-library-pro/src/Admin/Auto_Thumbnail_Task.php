<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Schedulable;
use Barn2\Plugin\Document_Library_Pro\Document;
use Barn2\Plugin\Document_Library_Pro\Util\Util;

defined( 'ABSPATH' ) || exit;

/**
 * Schedule a cron job to set auto thumbnails for documents
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Auto_Thumbnail_Task implements Schedulable, Registerable, Standard_Service {

	private $event_hook = 'dlp_batch_auto_thumbnail';

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( $this->event_hook, [ $this, 'run' ] );

		$this->schedule();
	}

	/**
	 * {@inheritdoc}
	 */
	public function schedule() {
		if ( get_option( 'dlp_auto_thumbnail_task' ) !== 'start' ) {
			return;
		}

		wp_schedule_single_event( time(), $this->event_hook );
	}

	/**
	 * {@inheritdoc}
	 */
	public function unschedule() {
		wp_clear_scheduled_hook( $this->event_hook );
	}

	/**
	 * Run the auto thumbnail task.
	 *
	 * Will schedule another single event if there are still documents to process.
	 */
	public function run() {
		global $wpdb;
		$post_ids = $this->get_documents_batch();

		if ( empty( $post_ids ) ) {
			// if we have no documents, then we are done.
			update_option( 'dlp_auto_thumbnail_task', 'complete', false );
			wp_clear_scheduled_hook( $this->event_hook );

			// clear all temp processed post meta
			$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_dlp_auto_thumbnail_processed'" );
			return;
		}

		update_option( 'dlp_auto_thumbnail_task', 'running', false );

		foreach ( $post_ids as $post_id ) {
			$document = new Document( $post_id );
			$document->maybe_set_file_as_featured_image();

			update_post_meta( $post_id, '_dlp_auto_thumbnail_processed', true );
		}

		wp_schedule_single_event( time(), $this->event_hook );
	}

	/**
	 * Get the documents to process
	 *
	 * @return WP_Post[]
	 */
	protected function get_documents_batch() {
		$batch_size = apply_filters( 'document_library_pro_auto_thumbnail_batch_size', 100 );

		$posts = Util::get_eligible_auto_thumbnail_documents( $batch_size );

		return $posts;
	}
}
