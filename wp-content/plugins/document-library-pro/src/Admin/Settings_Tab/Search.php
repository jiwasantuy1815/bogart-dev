<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Settings_Tab;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;
use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Util\Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Fields\ {
	Checkbox,
	Post_Select,
	Select,
    Text
};
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Tabs\Tab;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Sections\Section;

/**
 * Handles the search tab.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Search {

	/**
	 * The tab ID.
	 *
	 * @var string
	 */
	public const TAB_ID = 'search';

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
	 * Search tab.
	 *
	 * @return Tab
	 */
	public function get_tab() {
		return new Tab(
			self::TAB_ID,
			esc_html__( 'Search', 'document-library-pro' ),
			[
				$this->get_search_section(),
			]
		);
	}

	/**
	 * Search section.
	 *
	 * @return Section
	 */
	private function get_search_section() {
		$section = new Section(
			'search',
			'',
			__( 'Use the following options to control the search box that appears above your document lists. You can also use the Document Library Pro: Search Box widget to add a search anywhere on your site.', 'document-library-pro' )
		);

		$filters = new Select(
			'filters',
			esc_html__( 'Search filters', 'document-library-pro' ),
			esc_html__( 'Show dropdown menus to allow users to filter the documents.', 'document-library-pro' ) . Util::read_more_link( 'kb/document-library-filters/' ),
			'',
			[],
			$this->defaults['filters']
		);

		$filters->set_options(
			[
				[
					'value' => 'false',
					'label' => esc_html__( 'Disabled', 'document-library-pro' ),
				],
				[
					'value' => 'true',
					'label' => esc_html__( 'Show based on data in library', 'document-library-pro' ),
				],
				[
					'value' => 'custom',
					'label' => esc_html__( 'Custom', 'document-library-pro' ),
				],
			]
		);

		$filters_custom = new Text(
			'filters_custom',
			esc_html__( 'Custom filters', 'document-library-pro' ),
			esc_html__( 'Enter the filters as a comma-separated list.', 'document-library-pro' ) . Util::read_more_link( 'kb/document-library-filters/' ),
			'',
			[],
			'',
			[
				'rules' => [
					'filters' => [
						'op'    => 'eq',
						'value' => 'custom',
					],
				],
			]
		);

		$search_box = new Checkbox(
			'search_box',
			esc_html__( 'Search box', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['search_box']
		);
		$search_box->set_help( esc_html__( 'Display a search box above the list of documents', 'document-library-pro' ) );

		$search_page = new Post_Select(
			'search_page',
			esc_html__( 'Search results', 'document-library-pro' ),
			sprintf(
				/* translators: %1: knowledge base link start, %2: knowledge base link end */
				__( 'When using the %1$sglobal search%2$s, this page will display your search results.', 'document-library-pro' ),
				Lib_Util::format_barn2_link_open( 'kb/document-library-search/#standalone-search-box', true ),
				'</a>'
			),
			esc_html__( 'Use the widget or shortcode to perform a search from anywhere on your site.', 'document-library-pro' ),
			[]
		);

		$search_page->set_content_type(
			[
				'slug'  => 'page',
				'label' => esc_html__( 'Page', 'document-library-pro' ),
			]
		);

		$section->add_fields(
			[
				$filters,
				$filters_custom,
				$search_box,
				$search_page,
			]
		);

		return $section;
	}
}
