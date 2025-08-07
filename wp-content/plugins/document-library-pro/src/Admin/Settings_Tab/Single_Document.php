<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Settings_Tab;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;
use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Fields\ {
	Checkboxes,
};
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Tabs\Tab;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Sections\Section;

/**
 * Handles the single document tab.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Single_Document {

	/**
	 * The tab ID.
	 *
	 * @var string
	 */
	public const TAB_ID = 'single_document';

	/**
	 * The default options.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'set_defaults' ] );
	}

	/**
	 * Set defaults
	 */
	public function set_defaults() {
		$this->defaults = Options::get_defaults();
	}

	/**
	 * Get the tab.
	 *
	 * @return Tab
	 */
	public function get_tab() {
		return new Tab(
			self::TAB_ID,
			esc_html__( 'Single Document', 'document-library-pro' ),
			[
				$this->get_single_document_section(),
			]
		);
	}

	/**
	 * Single document section.
	 *
	 * @return Section
	 */
	private function get_single_document_section(): Section {
		$section = new Section(
			'single_document',
			'',
			sprintf(
				'<p>' .
				/* translators: %1: display tab link start, %2: display tab link end %3: knowledge base link start, %4: knowledge base link end */
				esc_html__( 'Document Library Pro creates an individual page for each document. Use the following options to choose what information to display on this page. Alternatively, you can disable the single document page using the link options on the %1$sDisplay tab%2$s. %3$sRead more%4$s.', 'document-library-pro' ) .
				'</p>',
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				Lib_Util::format_link_open( '#/display', false ),
				'</a>',
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				Lib_Util::format_link_open( Lib_Util::barn2_url( '/kb/single-document-page/' ), true ),
				'</a>'
			)
		);

		$single_document_fields = new Checkboxes(
			'single_document_fields',
			esc_html__( 'Display', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['single_document_fields']
		);
		$single_document_fields->set_options( $this->get_single_document_display_options() );

		$section->add_fields(
			[
				$single_document_fields,
			]
		);

		return $section;
	}

	/**
	 * Gets the single document display options.
	 *
	 * @return array{value: string, label: string}[]
	 */
	private function get_single_document_display_options() {
		$options = [
			[
				'value' => 'thumbnail',
				'label' => esc_html__( 'Featured image', 'document-library-pro' ),
			],
			[
				'value' => 'comments',
				'label' => esc_html__( 'Comments', 'document-library-pro' ),
			],
			[
				'value' => 'doc_categories',
				'label' => esc_html__( 'Categories', 'document-library-pro' ),
			],
			[
				'value' => 'doc_tags',
				'label' => esc_html__( 'Tags', 'document-library-pro' ),
			],
			[
				'value' => 'doc_author',
				'label' => esc_html__( 'Author', 'document-library-pro' ),
			],
			[
				'value' => 'file_size',
				'label' => esc_html__( 'File size', 'document-library-pro' ),
			],
			[
				'value' => 'file_type',
				'label' => esc_html__( 'File type', 'document-library-pro' ),
			],
			[
				'value' => 'filename',
				'label' => esc_html__( 'Filename', 'document-library-pro' ),
			],
			[
				'value' => 'custom-fields',
				'label' => esc_html__( 'Custom fields', 'document-library-pro' ),
			],
			[
				'value' => 'download_count',
				'label' => esc_html__( 'Download count', 'document-library-pro' ),
			],
		];

		// maybe add excerpt option to the beginning of the array
		if ( Options::uses_excerpt_option() ) {
			$options = [
				[
					'value' => 'excerpt',
					'label' => esc_html__( 'Excerpt', 'document-library-pro' ),
				],
			] + $options;
		}

		return $options;
	}
}
