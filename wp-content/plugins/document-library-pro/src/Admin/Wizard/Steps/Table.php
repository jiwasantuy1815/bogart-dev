<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Wizard\Steps;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Api;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Step;
use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

/**
 * Table Settings Step.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table extends Step {


	/**
	 * The default or user setting
	 *
	 * @var array
	 */
	private $values;

	/**
	 * Init.
	 */
	public function init() {
		$this->set_id( 'table' );
		$this->set_name( esc_html__( 'Tables', 'document-library-pro' ) );
		$this->set_description( esc_html__( 'Now, choose which information will appear in the list of documents.', 'document-library-pro' ) );
		$this->set_title( esc_html__( 'Tables', 'document-library-pro' ) );
		$this->set_hidden( true );

		$this->values = Options::get_user_shortcode_options();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$fields = [
			'columns'         => [
				'label'                => __( 'Columns', 'document-library-pro' ),
				'description'          => __( 'Enter the fields to include in your document tables.', 'document-library-pro' ) . ' ' . Lib_Util::barn2_link( 'kb/document-library-columns/', esc_html__( 'Read more', 'document-library-pro' ), true ),
				'type'                 => 'document_columns',
				'supported_options'    => $this->get_supported_columns(),
				'supported_taxonomies' => Options::get_supported_taxonomies(),
				'content_type'         => [
					'slug'  => 'dlp_document',
					'label' => esc_html__( 'Document', 'document-library-pro' ),
				],
				'value'                => $this->is_fresh_install_without_columns() ? $this->values['columns_editor'] : $this->values['columns'],
			],
			'multi_downloads' => [
				'title' => __( 'Multi-downloads', 'document-library-pro' ),
				'label' => __( 'Allow documents to be downloaded in bulk', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['multi_downloads'] ),
			],
			'lazy_load'       => [
				'title' => __( 'Lazy load', 'document-library-pro' ),
				'type'  => 'checkbox',
				'label' => __( 'Load the document table one page at a time', 'document-library-pro' ),
				'description' => __( 'Enable this if you will have lots of documents, otherwise leave it blank.', 'document-library-pro' ),
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['lazy_load'] ),
			],
		];
		return $fields;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array $values
	 */
	public function submit( array $values ) {
		$multi_downloads = isset( $values['multi_downloads'] ) ? Options::convert_to_checkbox_value( $values['multi_downloads'] ) : '0';
		$columns         = isset( $values['columns'] ) ? $values['columns'] : '';
		$lazy_load       = isset( $values['lazy_load'] ) ? Options::convert_to_checkbox_value( $values['lazy_load'] ) : '0';

		Options::update_settings(
			[
				'multi_downloads' => $multi_downloads,
				'columns'         => $columns,
				'lazy_load'       => $lazy_load,
			]
		);

		return Api::send_success_response();
	}

	/**
	 * Get the supported columns.
	 *
	 * @return array
	 */
	private function get_supported_columns() {
		$default_columns = array_map(
			function ( $key, $value ) {
				return [
					'value' => $key,
					'label' => $value,
				];
			},
			array_keys( Options::get_supported_columns() ),
			array_values( Options::get_supported_columns() )
		);

		return $default_columns;
	}

	/**
	 * Check if the plugin is a fresh install and the columns option is not set.
	 *
	 * @return bool
	 */
	private function is_fresh_install_without_columns() {
		$db_version = get_option( 'dlp_db_version' );
		if ( version_compare( $db_version, '2.0.0', '<' ) ) {
			return false;
		}

		$saved_options = Options::get_settings();

		return ! isset( $saved_options['columns'] );
	}
}
