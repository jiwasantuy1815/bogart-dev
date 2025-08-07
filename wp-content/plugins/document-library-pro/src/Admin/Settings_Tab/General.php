<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Settings_Tab;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;
use Barn2\Plugin\Document_Library_Pro\Util\Util;
use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Fields\ {
	Checkbox,
	Checkboxes,
	License,
	Post_Select,
	Radio,
	Text,
};
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Tabs\Tab;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Sections\Section;

/**
 * Handles the general tab.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class General {

	/**
	 * The tab ID.
	 *
	 * @var string
	 */
	public const TAB_ID = 'general';

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
	 * General tab.
	 *
	 * @return Tab
	 */
	public function get_tab() {
		return new Tab(
			'general',
			esc_html__( 'General', 'document-library-pro' ),
			[
				$this->get_general_section(),
				$this->get_document_data_section(),
				$this->get_version_control_section(),
				$this->get_frontend_submission_section(),
			]
		);
	}

	/**
	 * General section.
	 *
	 * @return Section
	 */
	private function get_general_section() {
		$section = new Section(
			'general',
			'',
			esc_html__( 'The following options control the Document Library Pro extension.', 'document-library-pro' )
		);

		$document_page = new Post_Select(
			'document_page',
			esc_html__( 'Document library page', 'document-library-pro' ),
			esc_html__( 'The page to display your documents.', 'document-library-pro' ),
			esc_html__( 'You can also use the [doc_library] shortcode to list documents on other pages.', 'document-library-pro' ),
		);

		$document_page->set_content_type(
			[
				'slug'  => 'page',
				'label' => esc_html__( 'Page', 'document-library-pro' ),
			]
		);

		$section->add_fields(
			[
				new License( 'license', esc_html__( 'License key', 'document-library-pro' ) ),
				$document_page,
			]
		);

		return $section;
	}

	/**
	 * Document data section.
	 *
	 * @return Section
	 */
	private function get_document_data_section() {
		$section = new Section(
			'document_data',
			esc_html__( 'Document data', 'document-library-pro' ),
			sprintf(
				/* translators: %1: knowledge base link start, %2: knowledge base link end */
				esc_html__( 'Use the following options to manage the fields that are used to store information about your documents. You can add additional fields using a custom fields plugin and display them in the table layout. %1$sRead more%2$s.', 'document-library-pro' ),
				Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-library-settings/#document-fields' ), true ),
				'</a>'
			)
		);

		$document_fields = new Checkboxes(
			'document_fields',
			esc_html__( 'Document fields', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['document_fields']
		);

		$document_fields->set_options(
			[
				[
					'label' => esc_html__( 'Content', 'document-library-pro' ),
					'value' => 'editor',
				],
				[
					'label' => esc_html__( 'Excerpt', 'document-library-pro' ),
					'value' => 'excerpt',
				],
				[
					'label' => esc_html__( 'Featured image', 'document-library-pro' ),
					'value' => 'thumbnail',
				],
				[
					'label' => esc_html__( 'Comments', 'document-library-pro' ),
					'value' => 'comments',
				],
				[
					'label' => esc_html__( 'Custom fields', 'document-library-pro' ),
					'value' => 'custom-fields',
				],
				[
					'label' => esc_html__( 'Authors', 'document-library-pro' ),
					'value' => 'author',
				],
			]
		);

		$document_slug = new Text(
			'document_slug',
			esc_html__( 'Document slug', 'document-library-pro' ),
			esc_html__( 'Change the permalink for your documents.', 'document-library-pro' ) . Util::read_more_link( '/kb/document-library-settings/#document-slug' ),
			'',
			[],
			$this->defaults['document_slug']
		);

		$section->add_fields(
			[
				$document_fields,
				$document_slug,
			]
		);

		return $section;
	}

	/**
	 * Version control section.
	 *
	 * @return Section
	 */
	private function get_version_control_section() {
			$section = new Section(
				'version_control',
				esc_html__( 'Version control', 'document-library-pro' ),
				sprintf(
					/* translators: %1: knowledge base link start, %2: knowledge base link end */
					esc_html__( 'The version control options allow you to decide how to keep track of the uploaded files. %1$sRead more%2$s.', 'document-library-pro' ),
					Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-version-control' ), true ),
					'</a>'
				)
			);

			$version_control = new Checkbox(
				'version_control',
				esc_html__( 'Enable', 'document-library-pro' ),
				'',
				'',
				[],
				$this->defaults['version_control']
			);

			$version_control->set_help(
				esc_html__( 'Enable version control', 'document-library-pro' )
			);

			$version_control_mode = new Radio(
				'version_control_mode',
				esc_html__( 'Replacing files', 'document-library-pro' ),
				'',
				'',
				[],
				$this->defaults['version_control_mode'],
				[
					'rules'   => [
						'version_control' => [
							'op'    => 'eq',
							'value' => true,
						],
					],
					'satisfy' => 'ANY',
				]
			);

			$version_control_mode->set_options(
				[
					[
						'label' => __( 'When replacing a file, keep the original in the Media Library', 'document-library-pro' ),
						'value' => 'keep',
					],
					[
						'label' => __( 'When replacing a file, delete the old version from the Media Library', 'document-library-pro' ),
						'value' => 'delete',
					],
				]
			);

			$section->add_fields(
				[
					$version_control,
					$version_control_mode,
				]
			);

			return $section;
	}

	/**
	 * Frontend submission section.
	 *
	 * @return Section
	 */
	private function get_frontend_submission_section() {
			$section = new Section(
				'frontend_submission',
				esc_html__( 'Front end document submission', 'document-library-pro' ),
				sprintf(
					/* translators: %1: knowledge base link start, %2: knowledge base link end */
					esc_html__( 'Use the [dlp_submission_form] shortcode to allow people to add documents from the front end. %1$sRead more%2$s.', 'document-library-pro' ),
					Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/add-import-documents/#upload-documents-from-the-front-end' ), true ),
					'</a>'
				)
			);

			$frontend_email_admin = new Checkbox(
				'fronted_email_admin',
				esc_html__( 'Enable admin email', 'document-library-pro' ),
				'',
				'',
				[],
				$this->defaults['fronted_email_admin']
			);

			$frontend_email_admin->set_help( esc_html__( 'Email the site administrator when a new document is submitted', 'document-library-pro' ) );

			$frontend_moderation = new Checkbox(
				'fronted_moderation',
				esc_html__( 'Enable moderation', 'document-library-pro' ),
				'',
				'',
				[],
				$this->defaults['fronted_moderation']
			);

			$frontend_moderation->set_help(
				esc_html__( 'Hold new documents for moderation by an administrator', 'document-library-pro' )
			);

			$section->add_fields(
				[
					$frontend_email_admin,
					$frontend_moderation,
				]
			);

			return $section;
	}
}
