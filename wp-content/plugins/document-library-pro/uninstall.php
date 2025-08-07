<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Barn2\Plugin\Document_Library_Pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$settings = get_option( 'document-library-pro_settings', [] );

if ( ! isset( $settings['delete_data'] ) || ! $settings['delete_data'] ) {
	return;
}

// Options
$options_to_delete = [
	'document-library-pro_settings',
	'dlp_auto_thumbnail_task',
	'dlp_should_flush_rewrite_rules',
	'dlp_db_version',
	'barn2_plugin_license_194365',
	'barn2_plugin_194365_license_is_pass',
];

foreach ( $options_to_delete as $option ) {
	delete_option( $option );
}

$transients_to_delete = [
	'barn2_plugin_review_banner_194365',
	'barn2_plugin_promo_194365',
];

foreach ( $transients_to_delete as $transient ) {
	delete_transient( $transient );
}

// Scheduled tasks
$scheduled_hooks = [
	'dlp_auto_thumbnail_task',
	'document_library_pro_expire_document',
];

foreach ( $scheduled_hooks as $hook ) {
	wp_clear_scheduled_hook( $hook );
}

// Document posts, taxonomies, and comments
$document_ids = $wpdb->get_col(
	$wpdb->prepare(
		"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s",
		'dlp_document'
	)
);

if ( ! empty( $document_ids ) ) {
	$document_ids         = array_map( 'absint', $document_ids );
	$post_id_placeholders = implode( ', ', array_fill( 0, count( $document_ids ), '%d' ) );

	// Delete term relationships associated with these posts.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->term_relationships} WHERE object_id IN ( $post_id_placeholders )",
			$document_ids
		)
	);

	// Delete post meta associated with these posts.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE post_id IN ( $post_id_placeholders )",
			$document_ids
		)
	);

	// Find comment IDs associated with these posts *before* deleting comments.
	$comment_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT comment_ID FROM {$wpdb->comments} WHERE comment_post_ID IN ( $post_id_placeholders )",
			$document_ids
		)
	);

	// Delete comments associated with these posts.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->comments} WHERE comment_post_ID IN ( $post_id_placeholders )",
			$document_ids
		)
	);

	// If comments were found and deleted, delete their meta.
	if ( ! empty( $comment_ids ) ) {
		$comment_ids             = array_map( 'absint', $comment_ids );
		$comment_id_placeholders = implode( ', ', array_fill( 0, count( $comment_ids ), '%d' ) );

		// Delete comment meta associated with the deleted comments.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->commentmeta} WHERE comment_id IN ( $comment_id_placeholders )",
				$comment_ids
			)
		);
	}

	// Delete the posts themselves.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->posts} WHERE post_type = %s",
			'dlp_document'
		)
	);
}

$taxonomies_to_delete = [
	'doc_categories',
	'doc_tags',
	'doc_author',
	'file_type',
	'document_download',
];

$taxonomy_placeholders = implode( ', ', array_fill( 0, count( $taxonomies_to_delete ), '%s' ) );

// Delete terms, term taxonomy entries, and term relationships for the specified taxonomies.
$wpdb->query(
	$wpdb->prepare(
		"DELETE terms, tax, relationships
			FROM {$wpdb->terms} AS terms
			LEFT JOIN {$wpdb->term_taxonomy} AS tax ON terms.term_id = tax.term_id
			LEFT JOIN {$wpdb->term_relationships} AS relationships ON tax.term_taxonomy_id = relationships.term_taxonomy_id
			WHERE tax.taxonomy IN ( $taxonomy_placeholders )",
		$taxonomies_to_delete
	)
);

// Clean up orphaned term taxonomy entries if any somehow remained
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy IN ( $taxonomy_placeholders )",
		$taxonomies_to_delete
	)
);