<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Wizard\Steps;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Api;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Step;
use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

/**
 * Layout Settings Step.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Layout extends Step {

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
		$this->set_id( 'layout' );
		$this->set_name( esc_html__( 'Layout', 'document-library-pro' ) );
		$this->set_description( esc_html__( 'First, choose the layout for your document libraries.', 'document-library-pro' ) );
		$this->set_title( esc_html__( 'Layout', 'document-library-pro' ) );

		$this->values = Options::get_user_shortcode_options();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$fields = [
			'layout'  => [
				'label'   => __( 'Default layout', 'document-library-pro' ),
				'type'    => 'radio',
				'options' => [
					[
						'value' => 'table',
						'label' => __( 'Table', 'document-library-pro' ),
					],
					[
						'value' => 'grid',
						'label' => __( 'Grid', 'document-library-pro' ),
					],
				],
				'value'   => $this->values['layout'],
			],
			'folders' => [
				'title' => __( 'Folders', 'document-library-pro' ),
				'label' => __( 'Display the document library in folders', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => Options::convert_checkbox_value_to_boolean( $this->values['folders'] ),
			],
			'preview' => [
				'title'       => __( 'Document preview', 'document-library-pro' ),
				'label'       => __( 'Allow users to preview documents in a lightbox', 'document-library-pro' ),
				'description' => __( 'The preview option will appear for supported file types only.', 'document-library-pro' ) . ' ' . Lib_Util::barn2_link( 'kb/document-preview/', esc_html__( 'Read more', 'document-library-pro' ), true ),
				'type'        => 'checkbox',
				'value'       => Options::convert_checkbox_value_to_boolean( $this->values['preview'] ),
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

		$layout  = isset( $values['layout'] ) && in_array( $values['layout'], [ 'table', 'grid' ], true ) ? $values['layout'] : 'table';
		$folders = isset( $values['folders'] ) ? Options::convert_to_checkbox_value( $values['folders'] ) : '0';
		$preview = isset( $values['preview'] ) ? Options::convert_to_checkbox_value( $values['preview'] ) : '0';

		Options::update_settings(
			[
				'layout'  => $layout,
				'folders' => $folders,
				'preview' => $preview,
			]
		);

		return Api::send_success_response();
	}
}
