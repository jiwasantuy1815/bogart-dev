<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Wizard\Steps;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Api;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Step;
use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

/**
 * Grid Settings Step.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Grid extends Step {

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
		$this->set_id( 'grid' );
		$this->set_name( esc_html__( 'Grid', 'document-library-pro' ) );
		$this->set_description( esc_html__( 'Now, choose which information will appear in the list of documents.', 'document-library-pro' ) );
		$this->set_title( esc_html__( 'Document Grid', 'document-library-pro' ) );
		$this->set_hidden( true );

		$this->values = $this->get_values();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {

		$fields = [
			'image'          => [
				'label' => __( 'Library content', 'document-library-pro' ),
				'title' => __( 'Image', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['grid_content']['image'] ),
			],
			'title'          => [
				'title' => __( 'Title', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['grid_content']['title'] ),
			],
			'filename'       => [
				'title' => __( 'Filename', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['grid_content']['filename'] ),
			],
			'file_type'      => [
				'title' => __( 'File type', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['grid_content']['file_type'] ),
			],
			'file_size'      => [
				'title' => __( 'File size', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['grid_content']['file_size'] ),
			],
			'download_count' => [
				'title' => __( 'Download count', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['grid_content']['download_count'] ),
			],
			'doc_categories' => [
				'title' => __( 'Categories', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['grid_content']['doc_categories'] ),
			],
			'doc_author'     => [
				'title' => __( 'Author', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['grid_content']['doc_author'] ),
			],
			'excerpt'        => [
				'title' => __( 'Excerpt/content', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['grid_content']['excerpt'] ),
			],
			'custom_fields'  => [
				'title' => __( 'Custom fields', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['grid_content']['custom_fields'] ),
			],
			'link'           => [
				'title' => __( 'Document link', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['grid_content']['link'] ),
			],
			'grid_links'     => [
				'label'       => __( 'Clickable fields', 'document-library-pro' ),
				'description' => sprintf(
					/* translators: %1: knowledge base link start, %2: knowledge base link end */
					esc_html__( 'Control which fields are clickable, in addition to the \'link\' field. %1$sRead more%2$s.', 'document-library-pro' ),
					Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-library-settings/#clickable-fields' ), true ),
					'</a>'
				),
				'type'        => 'text',
				'value'       => $this->values['grid_links'],
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
		$grid_content = array_filter( array_keys( $values ), function( $key ) use ( $values ) {
			return isset( $values[ $key ] ) && '1' === $values[ $key ];
		} );

		$grid_links = isset( $values['grid_links'] ) ? $values['grid_links'] : '';

		Options::update_shortcode_option(
			[
				'grid_content' => $grid_content,
				'grid_links'   => $grid_links,
			]
		);

		return Api::send_success_response();
	}

	/**
	 * Get the grid content value.
	 *
	 * @return []
	 */
	private function get_values() {
		$defaults = Options::get_user_shortcode_options();
		$settings = Options::get_settings();

		if ( isset( $settings['grid_content'] ) ) {
			$grid_content = Options::sanitize_grid_content( implode( ',', $settings['grid_content'] ) );
		} else {
			$grid_content = Options::sanitize_grid_content( $defaults['grid_content'] );
		}

		return [
			'grid_content' => ! is_array( $grid_content ) ? [] : $grid_content,
			'grid_links'   => $defaults['grid_links'],
		];
	}
}
