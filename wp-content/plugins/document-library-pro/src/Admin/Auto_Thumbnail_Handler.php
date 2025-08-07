<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Plugin;
use Barn2\Plugin\Document_Library_Pro\Post_Type;
use Barn2\Plugin\Document_Library_Pro\Taxonomies;
use Barn2\Plugin\Document_Library_Pro\Util\Util;
use Barn2\Plugin\Document_Library_Pro\Util\Media as Media_Util;

defined( 'ABSPATH' ) || exit;

/**
 * Handler for general document expiry hooks
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Auto_Thumbnail_Handler implements Registerable, Standard_Service {

	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		// Flag off for fresh install
		add_action( 'admin_init', [ $this, 'turn_off_on_fresh_install' ], 7 );

		// Add admin notices
		add_action( 'admin_init', [ $this, 'admin_notices' ], 8 );
		add_action( 'wp_ajax_wptrt_dismiss_notice', [ $this, 'handle_dismissed_info_notice' ], 11 );

		// Link to start the auto thumbnail task
		add_action( 'admin_post_dlp_start_auto_thumbnail', [ $this, 'start_task' ] );

		// Delete auto thumbnail meta when the thumbnail is manually updated (must be priority 9)
		add_action( 'save_post_' . Post_Type::POST_TYPE_SLUG, [ $this, 'delete_auto_thumbnail' ], 9, 1 );

		// Handle disabled the auto thumbnail if the user has manually removed the thumbnail
		add_action( 'pre_post_update', [ $this, 'handle_remove_thumbnail' ], 10, 1 );
	}

	/**
	 * If we don't have any eligible documents and the task is not running, then turn off the task.
	 */
	public function turn_off_on_fresh_install() {
		if ( ! get_option( 'dlp_auto_thumbnail_task' ) && ! $this->has_eligible_documents() ) {
			update_option( 'dlp_auto_thumbnail_task', 'complete', false );
		}
	}
	/**
	 * Run any admin notices.
	 *
	 * We hook into the admin_init action to use the Notice_Provider.
	 */
	public function admin_notices() {
		if ( get_option( 'dlp_auto_thumbnail_task' ) === 'complete' ) {
			return;
		}

		$this->info_action_notice();
		$this->in_progress_notice();
	}

	/**
	 * Display the info action notice.
	 */
	public function info_action_notice() {
		if ( in_array( get_option( 'dlp_auto_thumbnail_task' ), [ 'start', 'running', 'complete' ], true ) ) {
			return;
		}

		if ( ! $this->has_eligible_documents() ) {
			return;
		}

		if ( Media_Util::has_pdf_previews() ) {
			$message = __( '<strong>Document Library Pro</strong> supports automatic featured images for documents linked to image and PDF files. If you\'d like to process existing documents, please run the batch migration.', 'document-library-pro' );
		} else {
			$message = __( '<strong>Document Library Pro</strong> supports automatic featured images for documents linked to image files. If you\'d like to process existing documents, please run the batch migration.', 'document-library-pro' );
		}

		$button_text = __( 'Run batch migration', 'document-library-pro' );
		$button_href = admin_url( 'admin-post.php?action=dlp_start_auto_thumbnail' );

		// add nonce
		$button_href = add_query_arg( 'nonce', wp_create_nonce( 'dlp_start_auto_thumbnail' ), $button_href );

		$message = sprintf(
			'<p>%1$s</p><a href="%2$s">%3$s</a>',
			wp_kses_data( $message, [ 'strong' => [] ] ),
			esc_url( $button_href ),
			esc_html( $button_text )
		);

		$this->plugin->notices()->add_info_notice(
			'dlp_auto_thumbnail_info',
			'',
			$message,
			[
				'screens' => $this->get_admin_notice_screens(),
			]
		);
	}

	/**
	 * Display the in progress notice.
	 */
	public function in_progress_notice() {
		if ( ! $this->batch_process_has_started() ) {
			return;
		}

		$message = __( 'The <strong>Document Library Pro</strong> batch automatic featured images process is in progress.', 'document-library-pro' );

		$this->plugin->notices()->add_warning_notice(
			'dlp_auto_thumbnail_progress',
			'',
			$message,
			[ 'screens' => $this->get_admin_notice_screens() ]
		);
	}

	/**
	 * Additional hook into the AJAX dismiss action to alter our task runner option.
	 */
	public function handle_dismissed_info_notice() {
		$id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// check id matches dlp_auto_thumbnail_info
		if ( $id !== 'dlp_auto_thumbnail_info' ) {
			return;
		}

		update_option( 'dlp_auto_thumbnail_task', 'complete', false );
	}

	/**
	 * Start the auto thumbnail task.
	 */
	public function start_task() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to perform this action.', 'document-library-pro' ) );
		}

		$refferer = wp_get_raw_referer();

		if ( ! $refferer || strpos( $refferer, 'admin-post.php?action=dlp_start_auto_thumbnail' ) === false ) {
			$refferer = $this->plugin->get_settings_page_url();
		}

		if ( $this->batch_process_has_started() ) {
			wp_safe_redirect( $refferer );
			exit;
		}

		update_option( 'dlp_auto_thumbnail_task', 'start', false );

		wp_safe_redirect( $refferer );
	}

	/**
	 * Delete auto thumbnail meta when the thumbnail is manually updated via Edit Document
	 *
	 * @param int $post_id
	 */
	public function delete_auto_thumbnail( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$auto_thumbnail = get_post_meta( $post_id, '_dlp_has_auto_featured_image', true );

		if ( ! $auto_thumbnail ) {
			return;
		}

		$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );

		// check if we have a new thumbnail
		if ( $auto_thumbnail === $thumbnail_id ) {
			return;
		}

		delete_post_meta( $post_id, '_dlp_has_auto_featured_image' );
	}

	/**
	 * Handle disabled the auto thumbnail if the user has manually removed the thumbnail.
	 *
	 * @param int $post_id
	 */
	public function handle_remove_thumbnail( $post_id ) {
		$previous_thumbnail = get_post_meta( $post_id, '_thumbnail_id', true );
		$new_thumbnail      = filter_input( INPUT_POST, '_thumbnail_id', FILTER_SANITIZE_NUMBER_INT );

		$skip_auto_thumbnail = $previous_thumbnail !== '' && $new_thumbnail === '-1';

		if ( $skip_auto_thumbnail ) {
			// set temp flag
			update_post_meta( $post_id, '_dlp_skip_auto_featured_image', 1 );
		}
	}

	/**
	 * Batch process has started.
	 *
	 * @return bool
	 */
	private function batch_process_has_started() {
		return in_array( get_option( 'dlp_auto_thumbnail_task' ), [ 'start', 'running' ], true );
	}

	/**
	 * Get the screens to show the notices on.
	 *
	 * @return string[]
	 */
	private function get_admin_notice_screens() {
		return [
			'toplevel_page_document_library_pro',
			sprintf( 'edit-%s', Post_Type::POST_TYPE_SLUG ),
			sprintf( 'edit-%s', Taxonomies::CATEGORY_SLUG ),
			sprintf( 'edit-%s', Taxonomies::AUTHOR_SLUG ),
			sprintf( 'edit-%s', Taxonomies::TAG_SLUG ),
		];
	}

	/**
	 * Determine if we have any eligible documents.
	 *
	 * @return bool
	 */
	private function has_eligible_documents() {
		$eligible_documents = Util::get_eligible_auto_thumbnail_documents( 1 );

		if ( ! empty( $eligible_documents ) && count( $eligible_documents ) > 0 ) {
			return true;
		}

		return false;
	}
}
