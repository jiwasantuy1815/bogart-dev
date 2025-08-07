<?php

namespace Barn2\Plugin\Document_Library_Pro\Table_Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data\Abstract_Table_Data;
use Barn2\Plugin\Document_Library_Pro\Document;

/**
 * Gets post data for the document title column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Title extends Abstract_Table_Data {

	protected $title_link;
	protected $new_tab;

	/**
	 * Constructor.
	 *
	 * @param WP_Post $post
	 * @param Table_Args $args
	 */
	public function __construct( $post, $args ) {
		parent::__construct( $post, $args->links );

		$this->title_link = $args->table_document_title_link;
		$this->new_tab    = $args->new_tab_links;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_data() {
		$document = new Document( $this->post->ID );
		$title    = $document->get_title( $this->title_link, $this->new_tab );

		/**
		 * Filters the document title before applying legacy filter.
		 *
		 * @param string   $title The document title.
		 * @param WP_Post $post  The post object.
		 */
		$title = apply_filters( 'document_library_pro_data_document_title', $title, $this->post );

		/**
		 * Legacy filter for the document title.
		 *
		 * @param string   $title The document title.
		 * @param WP_Post $post  The post object.
		 */
		$title = apply_filters( 'document_library_pro_data_title', $title, $this->post );

		return $title;
	}
}
