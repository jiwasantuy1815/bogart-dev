<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Settings_Tab;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;
use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Fields\ {
	Checkbox,
	Checkboxes,
	Color,
	Columns_Editor,
	License,
	Radio,
	Select,
	Text,
	Textarea,
};
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Tabs\Tab;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Sections\Section;

/**
 * Handles the display tab.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Display {

	/**
	 * The tab ID.
	 *
	 * @var string
	 */
	public const TAB_ID = 'display';

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
	 * Display tab.
	 *
	 * @return Tab
	 */
	public function get_tab() {
		return new Tab(
			self::TAB_ID,
			esc_html__( 'Display', 'document-library-pro' ),
			[
				$this->get_display_section(),
				$this->get_table_section(),
				$this->get_grid_section(),
				$this->get_sort_by_section(),
				$this->get_download_button_section(),
				$this->get_preview_button_section(),
				$this->get_multi_downloads_section(),
				$this->get_folders_section(),
			]
		);
	}

	/**
	 * Display section.
	 *
	 * @return Section
	 */
	private function get_display_section() {
		$section = new Section(
			'display',
			'',
			__( 'The following options control the contents and layout of your document lists.', 'document-library-pro' )
		);

		$layout = new Radio(
			'layout',
			esc_html__( 'Default layout', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['layout']
		);

		$layout->set_options(
			[
				[
					'value' => 'table',
					'label' => esc_html__( 'Table', 'document-library-pro' ),
				],
				[
					'value' => 'grid',
					'label' => esc_html__( 'Grid', 'document-library-pro' ),
				],
			]
		);

		$section->add_fields(
			[
				$layout,
			]
		);
		return $section;
	}

	/**
	 * Table section
	 *
	 * @return Section
	 */
	private function get_table_section() {
		$section = new Section(
			'table',
			__( 'Table', 'document-library-pro' )
		);

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

		$columns_editor = new Columns_Editor(
			'columns',
			esc_html__( 'Columns', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['columns_editor']
		);

		$columns_editor->set_content_type(
			[
				'slug'  => 'dlp_document',
				'label' => esc_html__( 'Document', 'document-library-pro' ),
			]
		);

		$columns_editor->set_columns( $default_columns );
		$columns_editor->set_taxonomies( Options::get_supported_taxonomies() );

		$section->add_fields(
			[
				$columns_editor,
			]
		);

		return $section;
	}

	/**
	 * Grid section.
	 *
	 * @return Section
	 */
	private function get_grid_section() {
		$section = new Section(
			'grid',
			__( 'Grid', 'document-library-pro' )
		);

		$grid_content = new Checkboxes(
			'grid_content',
			esc_html__( 'Grid content', 'document-library-pro' ),
			esc_html__( 'Choose which information to display in the grid of documents.', 'document-library-pro' ),
			'',
			[],
			Options::migrate_multicheckbox_settings( $this->defaults['grid_content'] )
		);

		$grid_content->set_options(
			[
				[
					'value' => 'image',
					'label' => esc_html__( 'Image', 'document-library-pro' ),
				],
				[
					'value' => 'title',
					'label' => esc_html__( 'Title', 'document-library-pro' ),
				],
				[
					'value' => 'filename',
					'label' => esc_html__( 'Filename', 'document-library-pro' ),
				],
				[
					'value' => 'file_type',
					'label' => esc_html__( 'File type', 'document-library-pro' ),
				],
				[
					'value' => 'file_size',
					'label' => esc_html__( 'File size', 'document-library-pro' ),
				],
				[
					'value' => 'download_count',
					'label' => esc_html__( 'Download count', 'document-library-pro' ),
				],
				[
					'value' => 'doc_categories',
					'label' => esc_html__( 'Categories', 'document-library-pro' ),
				],
				[
					'value' => 'doc_author',
					'label' => esc_html__( 'Authors', 'document-library-pro' ),
				],
				[
					'value' => 'excerpt',
					'label' => esc_html__( 'Excerpt/content', 'document-library-pro' ),
				],
				[
					'value' => 'custom_fields',
					'label' => esc_html__( 'Custom fields', 'document-library-pro' ),
				],
				[
					'value' => 'link',
					'label' => esc_html__( 'Document link', 'document-library-pro' ),
				],
			]
		);

		$grid_links = new Text(
			'grid_links',
			esc_html__( 'Clickable fields', 'document-library-pro' ),
			sprintf(
				/* translators: %1: knowledge base link start, %2: knowledge base link end */
				esc_html__( 'Control which fields are clickable, in addition to the \'link\' field. %1$sRead more%2$s.', 'document-library-pro' ),
				Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-library-settings/#clickable-fields' ), true ),
				'</a>'
			),
			'',
			[],
			$this->defaults['grid_links']
		);

		$grid_columns = new Select(
			'grid_columns',
			esc_html__( 'Number of columns', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['grid_columns']
		);

		$grid_columns->set_options(
			[
				[
					'value' => 'autosize',
					'label' => esc_html__( 'Auto-size', 'document-library-pro' ),
				],
				[
					'value' => '1',
					'label' => esc_html__( '1 column', 'document-library-pro' ),
				],
				[
					'value' => '2',
					'label' => esc_html__( '2 columns', 'document-library-pro' ),
				],
				[
					'value' => '3',
					'label' => esc_html__( '3 columns', 'document-library-pro' ),
				],
				[
					'value' => '4',
					'label' => esc_html__( '4 columns', 'document-library-pro' ),
				],
			]
		);

		$document_title_link = new Select(
			'grid_document_title_link',
			esc_html__( 'Document title', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['grid_document_title_link']
		);
		$document_title_link->set_options(
			[
				[
					'value' => 'single_document',
					'label' => esc_html__( 'Link to single document page', 'document-library-pro' ),
				],
				[
					'value' => 'download_file',
					'label' => esc_html__( 'Download file', 'document-library-pro' ),
				],
				[
					'value' => 'none',
					'label' => esc_html__( 'No link', 'document-library-pro' ),
				],
			]
		);

		$filename_link = new Select(
			'grid_filename_link',
			esc_html__( 'File name', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['grid_filename_link']
		);
		$filename_link->set_options(
			[
				[
					'value' => 'single_document',
					'label' => esc_html__( 'Link to single document page', 'document-library-pro' ),
				],
				[
					'value' => 'download_file',
					'label' => esc_html__( 'Download file', 'document-library-pro' ),
				],
				[
					'value' => 'none',
					'label' => esc_html__( 'No link', 'document-library-pro' ),
				],
			]
		);

		$section->add_fields(
			[
				$grid_content,
				$grid_links,
				$grid_columns,
				$document_title_link,
				$filename_link,
			]
		);

		return $section;
	}

	/**
	 * Sort by section
	 *
	 * @return Section
	 */
	private function get_sort_by_section() {
		$section = new Section(
			'sort_by',
			__( 'Sorting', 'document-library-pro' )
		);

		$sort_by = new Select(
			'sort_by',
			esc_html__( 'Sort by', 'document-library-pro' ),
			sprintf(
				/* translators: %1: knowledge base link start, %2: knowledge base link end */
				esc_html__( 'The initial sort order of the document library. %1$sRead more%2$s.', 'document-library-pro' ),
				Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-library-sort-options/#sort-by' ), true ),
				'</a>'
			),
			'',
			[],
			$this->defaults['sort_by']
		);

		$sort_by->set_options(
			[
				[
					'value' => 'title',
					'label' => esc_html__( 'Title', 'document-library-pro' ),
				],
				[
					'value' => 'id',
					'label' => esc_html__( 'ID', 'document-library-pro' ),
				],
				[
					'value' => 'date',
					'label' => esc_html__( 'Date published', 'document-library-pro' ),
				],
				[
					'value' => 'modified',
					'label' => esc_html__( 'Date modified', 'document-library-pro' ),
				],
				[
					'value' => 'menu_order',
					'label' => esc_html__( 'Page order (menu order)', 'document-library-pro' ),
				],
				[
					'value' => 'name',
					'label' => esc_html__( 'Post slug', 'document-library-pro' ),
				],
				[
					'value' => 'author',
					'label' => esc_html__( 'Author', 'document-library-pro' ),
				],
				[
					'value' => 'comment_count',
					'label' => esc_html__( 'Number of comments', 'document-library-pro' ),
				],
				[
					'value' => 'rand',
					'label' => esc_html__( 'Random', 'document-library-pro' ),
				],
				[
					'value' => 'custom',
					'label' => esc_html__( 'Other', 'document-library-pro' ),
				],
			]
		);

		$sort_by_custom = new Text(
			'sort_by_custom',
			esc_html__( 'Sort column', 'document-library-pro' ),
			esc_html__( 'Enter any column in your table. Note: only available for the table layout and when lazy load is disabled. Not used for the grid layout.', 'document-library-pro' ),
			'',
			[],
			'',
			[
				'rules'   => [
					'sort_by' => [
						'op'    => 'eq',
						'value' => 'custom',
					],
				],
				'satisfy' => 'ANY',
			]
		);

		$sort_order = new Select(
			'sort_order',
			esc_html__( 'Sort direction', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['sort_order']
		);

		$sort_order->set_options(
			[
				[
					'value' => '',
					'label' => esc_html__( 'Automatic', 'document-library-pro' ),
				],
				[
					'value' => 'asc',
					'label' => esc_html__( 'Ascending (A to Z, oldest to newest)', 'document-library-pro' ),
				],
				[
					'value' => 'desc',
					'label' => esc_html__( 'Descending (Z to A, newest to oldest)', 'document-library-pro' ),
				],
			]
		);

		$section->add_fields(
			[
				$sort_by,
				$sort_by_custom,
				$sort_order,
			]
		);

		return $section;
	}

	/**
	 * Download button section.
	 *
	 * @return Section
	 */
	private function get_download_button_section() {
		$section = new Section(
			'download_button',
			esc_html__( 'Download button', 'document-library-pro' )
		);

		$link_destination = new Select(
			'link_destination',
			esc_html__( 'Button behavior', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['link_destination']
		);

		$link_destination->set_options(
			[
				[
					'value' => 'direct',
					'label' => esc_html__( 'Download file', 'document-library-pro' ),
				],
				[
					'value' => 'single',
					'label' => esc_html__( 'Link to single document page', 'document-library-pro' ),
				],
			]
		);

		$link_style = new Select(
			'link_style',
			esc_html__( 'Style', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['link_style']
		);

		$link_style->set_options(
			[
				[
					'value' => 'button',
					'label' => esc_html__( 'Button', 'document-library-pro' ),
				],
				[
					'value' => 'file_icon',
					'label' => esc_html__( 'File type button', 'document-library-pro' ),
				],
				[
					'value' => 'link',
					'label' => esc_html__( 'Text link', 'document-library-pro' ),
				],
			]
		);

		$link_text = new Text(
			'link_text',
			esc_html__( 'Button/link text', 'document-library-pro' ),
			esc_html__( 'The text displayed on the button or link.', 'document-library-pro' ),
			'',
			[],
			$this->defaults['link_text'],
			[
				'rules' => [
					'link_style' => [
						'op'    => 'neq',
						'value' => 'file_icon',
					],
				],
			]
		);

		$link_icon = new Checkbox(
			'link_icon',
			esc_html__( 'Icon', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['link_icon'],
			[
				'rules' => [
					'link_style' => [
						'op'    => 'neq',
						'value' => 'file_icon',
					],
				],
			]
		);

		$link_icon->set_help( esc_html__( 'Display download icon', 'document-library-pro' ) );

		$link_target = new Checkbox(
			'link_target',
			esc_html__( 'New tab', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['link_target']
		);

		$link_target->set_help( esc_html__( 'Open download in a new tab', 'document-library-pro' ) );

		$section->add_fields(
			[
				$link_destination,
				$link_style,
				$link_text,
				$link_icon,
				$link_target,
			]
		);

		return $section;
	}

	/**
	 * Preview button section.
	 *
	 * @return Section
	 */
	private function get_preview_button_section() {
		$section = new Section(
			'preview_button',
			esc_html__( 'Preview button', 'document-library-pro' )
		);

		$preview = new Checkbox(
			'preview',
			esc_html__( 'Enable preview?', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['preview']
		);
		$preview->set_help( esc_html__( 'Show a preview button for supported file types', 'document-library-pro' ) );

		$preview_style = new Select(
			'preview_style',
			esc_html__( 'Style', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['preview_style']
		);
		$preview_style->set_options(
			[
				[
					'value' => 'button',
					'label' => esc_html__( 'Button', 'document-library-pro' ),
				],
				[
					'value' => 'link',
					'label' => esc_html__( 'Text link', 'document-library-pro' ),
				],
			]
		);

		$preview_text = new Text(
			'preview_text',
			esc_html__( 'Button/link text', 'document-library-pro' ),
			esc_html__( 'The text displayed on the button or link.', 'document-library-pro' ),
			'',
			[],
			$this->defaults['preview_text']
		);

		$preview_icon = new Checkbox(
			'preview_icon',
			esc_html__( 'Icon', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['preview_icon']
		);
		$preview_icon->set_help( esc_html__( 'Display preview icon', 'document-library-pro' ) );

		$section->add_fields(
			[
				$preview,
				$preview_style,
				$preview_text,
				$preview_icon,
			]
		);

		return $section;
	}

	/**
	 * Multi downloads section.
	 *
	 * @return Section
	 */
	private function get_multi_downloads_section() {
		$section = new Section(
			'multi_downloads',
			esc_html__( 'Multi-downloads', 'document-library-pro' )
		);

		$multi_downloads = new Checkbox(
			'multi_downloads',
			esc_html__( 'Allow multiple downloads?', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['multi_downloads']
		);
		$multi_downloads->set_help( esc_html__( 'Allow documents to be downloaded in bulk', 'document-library-pro' ) );

		$multi_download_text = new Text(
			'multi_download_text',
			esc_html__( 'Button text', 'document-library-pro' ),
			esc_html__( 'The text for the button to download all selected documents.', 'document-library-pro' ),
			'',
			[],
			$this->defaults['multi_download_text']
		);

		$multi_download_button = new Select(
			'multi_download_button',
			esc_html__( 'Button position', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['multi_download_button']
		);
		$multi_download_button->set_options(
			[
				[
					'value' => 'above',
					'label' => esc_html__( 'Above library', 'document-library-pro' ),
				],
				[
					'value' => 'below',
					'label' => esc_html__( 'Below library', 'document-library-pro' ),
				],
				[
					'value' => 'both',
					'label' => esc_html__( 'Above and below library', 'document-library-pro' ),
				],
			]
		);

		$section->add_fields(
			[
				$multi_downloads,
				$multi_download_text,
				$multi_download_button,
			]
		);

		return $section;
	}

	/**
	 * Folders section.
	 *
	 * @return Section
	 */
	private function get_folders_section() {
		$section = new Section(
			'folders',
			esc_html__( 'Folders', 'document-library-pro' ),
		);

		$folders = new Checkbox(
			'folders',
			esc_html__( 'Enable folders', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['folders']
		);
		$folders->set_help( esc_html__( 'Group the document library into folders, one per category', 'document-library-pro' ) );

		$folders_order_by = new Select(
			'folders_order_by',
			esc_html__( 'Sort folders by', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['folders_order_by']
		);
		$folders_order_by->set_options(
			[
				[
					'value' => 'name',
					'label' => esc_html__( 'Category name', 'document-library-pro' ),
				],
				[
					'value' => 'slug',
					'label' => esc_html__( 'Category slug', 'document-library-pro' ),
				],
				[
					'value' => 'term_order',
					'label' => esc_html__( 'Category order', 'document-library-pro' ),
				],
				[
					'value' => 'count',
					'label' => esc_html__( 'Number of documents in category', 'document-library-pro' ),
				],
			]
		);

		$folder_status = new Select(
			'folder_status',
			esc_html__( 'Folder display', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['folder_status']
		);
		$folder_status->set_options(
			[
				[
					'value' => 'open',
					'label' => esc_html__( 'Open', 'document-library-pro' ),
				],
				[
					'value' => 'closed',
					'label' => esc_html__( 'Closed', 'document-library-pro' ),
				],
				[
					'value' => 'custom',
					'label' => esc_html__( 'Custom', 'document-library-pro' ),
				],
			]
		);

		$folder_custom = new Text(
			'folder_status_custom',
			esc_html__( 'Custom', 'document-library-pro' ),
			esc_html__( 'List the category IDs of folders you want opened when the document library is displayed.', 'document-library-pro' ),
			'',
			[],
			$this->defaults['folder_status_custom'],
			[
				'rules' => [
					'folder_status' => [
						'op'    => 'eq',
						'value' => 'custom',
					],
				],
			]
		);

		$section->add_fields(
			[
				$folders,
				$folders_order_by,
				$folder_status,
				$folder_custom,
			]
		);

		return $section;
	}
}
