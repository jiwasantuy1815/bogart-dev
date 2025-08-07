<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Settings_Tab;

use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Util\Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Tabs\Tab;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Sections\Section;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Fields\ {
	Button,
	Checkbox,
	Number,
	Select,
	Text
};

/**
 * Handles the advanced tab.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Advanced {

	const TAB_ID = 'advanced';

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
	 * Gets the advanced tab.
	 *
	 * @return Tab
	 */
	public function get_tab() {
		return new Tab(
			self::TAB_ID,
			esc_html__( 'Advanced', 'document-library-pro' ),
			[
				$this->get_advanced_section(),
				$this->get_table_options_section(),
				$this->get_pagination_section(),
			]
		);
	}

	/**
	 * Advanced section.
	 *
	 * @return Section
	 */
	private function get_advanced_section(): Section {
		$section = new Section(
			'advanced',
			'',
			__( 'The following options allow you to configure advanced settings for your document libraries.', 'document-library-pro' )
		);

		$content_length = new Number(
			'content_length',
			esc_html__( 'Content length', 'document-library-pro' ),
			esc_html__( 'Enter -1 to display the full content.', 'document-library-pro' ),
			'',
			[
				'min' => -1,
			],
			$this->defaults['content_length']
		);

		$shortcodes = new Checkbox(
			'shortcodes',
			esc_html__( 'Shortcodes and media', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['shortcodes']
		);
		$shortcodes->set_help( esc_html__( 'Allow shortcodes and media files in the document library content', 'document-library-pro' ) );


		$new_tab_links = new Checkbox(
			'new_tab_links',
			esc_html__( 'Text links', 'document-library-pro' ),
			'',
			__( 'Open text-based links in the document library and single document page in a new tab. This will not affect the behavior of Download buttons, which you can configure on the \'Display\' page.', 'document-library-pro' ),
			[],
			$this->defaults['new_tab_links']
		);
		$new_tab_links->set_help( esc_html__( 'Open text links in a new tab', 'document-library-pro' ) );

		$lightbox = new Checkbox(
			'lightbox',
			esc_html__( 'Image lightbox', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['lightbox']
		);

		$lightbox->set_help( esc_html__( 'Display images in a lightbox when opened', 'document-library-pro' ) );

		$section->add_fields(
			[
				$content_length,
				$shortcodes,
				$new_tab_links,
				$lightbox,
			]
		);

		return $section;
	}

	/**
	 * Table options section.
	 *
	 * @return Section
	 */
	private function get_table_options_section(): Section {
		$section = new Section(
			'table_options',
			__( 'Table options', 'document-library-pro' )
		);

		$image_size = new Text(
			'image_size',
			esc_html__( 'Image size', 'document-library-pro' ),
			esc_html__( 'Enter WxH in pixels (e.g. 80x80).', 'document-library-pro' ) . Util::read_more_link( 'kb/document-library-image-options/#image-size' ),
			'',
			[],
			$this->defaults['image_size']
		);

		$lazy_load = new Checkbox(
			'lazy_load',
			esc_html__( 'Lazy load', 'document-library-pro' ),
			'',
			__( 'Enable this if you have many documents or experience slow page load times.', 'document-library-pro' ) . '<br>' .
			__( 'Warning: Lazy load limits the searching and sorting features in the document library. Only use it if you definitely need it.', 'document-library-pro' ) .
			Util::read_more_link( 'kb/document-library-lazy-load/' ),
			[],
			$this->defaults['lazy_load']
		);
		$lazy_load->set_help( esc_html__( 'Load the documents table one page at a time', 'document-library-pro' ) );

		$accent_neutralise = new Checkbox(
			'accent_neutralise',
			esc_html__( 'Accent-insensitive search', 'document-library-pro' ),
			'',
			__( 'Enable this to make searches match accented characters with their non-accented counterparts. For example, searching for "Zurich" will also match "Zürich" in the table.', 'document-library-pro' ),
			[],
			$this->defaults['accent_neutralise'],
			[
				'rules' => [
					'lazy_load' => [
						'op'    => 'eq',
						'value' => false,
					],
				],
			]
		);

		$accent_neutralise->set_help( esc_html__( 'Make searches match accented and non-accented characters when lazy load is disabled', 'document-library-pro' ) );

		$diacritics_sort = new Checkbox(
			'diacritics_sort',
			esc_html__( 'Diacritics sorting', 'document-library-pro' ),
			'',
			__( 'Enable this to sort accented characters naturally. For example, "árvíztűrő" will be sorted with "A" words instead of after "Z".', 'document-library-pro' ),
			[],
			$this->defaults['diacritics_sort'] ?? false,
			[
				'rules' => [
					'lazy_load' => [
						'op'    => 'eq',
						'value' => false,
					],
				],
			]
		);

		$diacritics_sort->set_help( esc_html__( 'Improve sorting of accented characters when lazy load is disabled', 'document-library-pro' ) );

		$cache = new Checkbox(
			'cache',
			esc_html__( 'Caching', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['cache'],
			[
				'rules' => [
					'lazy_load' => [
						'op'    => 'eq',
						'value' => false,
					],
				],
			]
		);
		$cache->set_help( esc_html__( 'Cache the documents table to improve load times', 'document-library-pro' ) );

		$cache_expiry = new Number(
			'cache_expiry',
			__( 'Cache expires after', 'document-library-pro' ),
			__( 'Your table data will be refreshed after this length of time.', 'document-library-pro' ),
			'',
			[
				'min' => 1,
				'max' => 9999,
			],
			$this->defaults['cache_expiry'],
			[
				'rules' => [
					'cache' => [
						'op'    => 'eq',
						'value' => true,
					],
					'lazy_load' => [
						'op'    => 'eq',
						'value' => false,
					],
				],
			]
		);

		$cache_expiry->set_suffix( __( 'hours', 'document-library-pro' ) );
		$cache_expiry->set_size( 'small' );

		$clear_cache = new Button(
			'clear_cache',
			esc_html__( 'Clear cache', 'document-library-pro' ),
			'',
			'',
			[],
			'',
			[
				'rules' => [
					'cache' => [
						'op'    => 'eq',
						'value' => true,
					],
					'lazy_load' => [
						'op'    => 'eq',
						'value' => false,
					],
				],
			]
		);

		$clear_cache->set_button_data(
			[
				'text'         => esc_html__( 'Clear cache', 'document-library-pro' ),
				'restEndpoint' => 'document-library-pro/v1/clear-cache',
			]
		);

		$post_limit = new Number(
			'post_limit',
			esc_html__( 'Document limit', 'document-library-pro' ),
			esc_html__( 'The maximum number of documents to display in each table. Enter -1 to show all documents.', 'document-library-pro' ),
			'',
			[
				'min' => -1,
				'max' => 1000,
			],
			$this->defaults['post_limit']
		);

		$table_responsive_display = new Select(
			'responsive_display',
			esc_html__( 'Responsive display', 'document-library-pro' ),
			'',
			esc_html__( 'How extra data is displayed when there are columns that are not visible in the main table.', 'document-library-pro' ),
			[],
			$this->defaults['responsive_display']
		);
		$table_responsive_display->set_options(
			[
				[
					'value' => 'child_row',
					'label' => esc_html__( 'Click a plus icon to display a hidden child row', 'document-library-pro' ),
				],
				[
					'value' => 'child_row_visible',
					'label' => esc_html__( 'Expand the child row automatically', 'document-library-pro' ),
				],
				[
					'value' => 'modal',
					'label' => esc_html__( 'Click a plus icon to open a modal', 'document-library-pro' ),
				],
			]
		);

		$section->add_fields(
			[
				$image_size,
				$lazy_load,
				$accent_neutralise,
				$diacritics_sort,
				$cache,
				$cache_expiry,
				$clear_cache,
				$post_limit,
				$table_responsive_display,
			]
		);

		return $section;
	}

	/**
	 * Pagination section.
	 *
	 * @return Section
	 */
	private function get_pagination_section() {
		$section = new Section(
			'pagination',
			esc_html__( 'Pagination', 'document-library-pro' ),
		);

		$rows_per_page = new Number(
			'rows_per_page',
			esc_html__( 'Documents per page', 'document-library-pro' ),
			esc_html__( 'The number of documents per page of the document library. Enter -1 to display all documents on one page.', 'document-library-pro' ),
			'',
			[
				'min' => -1,
			],
			$this->defaults['rows_per_page']
		);

		$totals = new Select(
			'totals',
			esc_html__( 'Document total', 'document-library-pro' ),
			__( "The position of the document total, e.g. '25 documents'.", 'document-library-pro' ),
			'',
			[],
			$this->defaults['totals']
		);
		$totals->set_options(
			[
				[
					'value' => 'top',
					'label' => esc_html__( 'Above library', 'document-library-pro' ),
				],
				[
					'value' => 'bottom',
					'label' => esc_html__( 'Below library', 'document-library-pro' ),
				],
				[
					'value' => 'both',
					'label' => esc_html__( 'Above and below library', 'document-library-pro' ),
				],
				[
					'value' => 'false',
					'label' => esc_html__( 'Hidden', 'document-library-pro' ),
				],
			]
		);

		$paging_type = new Select(
			'paging_type',
			esc_html__( 'Pagination style', 'document-library-pro' ),
			'',
			'',
			[],
			$this->defaults['paging_type']
		);
		$paging_type->set_options(
			[
				[
					'value' => 'numbers',
					'label' => esc_html__( 'Numbers only', 'document-library-pro' ),
				],
				[
					'value' => 'simple',
					'label' => esc_html__( 'Prev|Next', 'document-library-pro' ),
				],
				[
					'value' => 'simple_numbers',
					'label' => esc_html__( 'Prev|Next + Numbers', 'document-library-pro' ),
				],
				[
					'value' => 'full',
					'label' => esc_html__( 'Prev|Next|First|Last', 'document-library-pro' ),
				],
				[
					'value' => 'full_numbers',
					'label' => esc_html__( 'Prev|Next|First|Last + Numbers', 'document-library-pro' ),
				],
			]
		);

		$pagination = new Select(
			'pagination',
			esc_html__( 'Pagination position', 'document-library-pro' ),
			esc_html__( 'The position of the paging buttons which scroll between results.', 'document-library-pro' ),
			'',
			[],
			$this->defaults['pagination']
		);
		$pagination->set_options(
			[
				[
					'value' => 'top',
					'label' => __( 'Above library', 'document-library-pro' ),
				],
				[
					'value' => 'bottom',
					'label' => __( 'Below library', 'document-library-pro' ),
				],
				[
					'value' => 'both',
					'label' => __( 'Above and below library', 'document-library-pro' ),
				],
				[
					'value' => 'false',
					'label' => __( 'Hidden', 'document-library-pro' ),
				],
			]
		);

		$page_length = new Select(
			'page_length',
			esc_html__( 'Page length', 'document-library-pro' ),
			esc_html__( "The position of the 'Show [x] entries' dropdown list.", 'document-library-pro' ),
			'',
			[],
			$this->defaults['page_length']
		);
		$page_length->set_options(
			[
				[
					'value' => 'top',
					'label' => __( 'Above library', 'document-library-pro' ),
				],
				[
					'value' => 'bottom',
					'label' => __( 'Below library', 'document-library-pro' ),
				],
				[
					'value' => 'both',
					'label' => __( 'Above and below library', 'document-library-pro' ),
				],
				[
					'value' => 'false',
					'label' => __( 'Hidden', 'document-library-pro' ),
				],
			]
		);

		$section->add_fields(
			[
				$rows_per_page,
				$totals,
				$paging_type,
				$pagination,
				$page_length,
			]
		);

		return $section;
	}
}
