<?php
namespace Barn2\Plugin\Document_Library_Pro\Table_Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data\Abstract_Table_Data;
use Barn2\Plugin\Document_Library_Pro\Document;

defined( 'ABSPATH' ) || exit;

/**
 * Gets data for the 'filename' column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Filename extends Abstract_Table_Data {

	protected $filename_link;
	protected $new_tab;

	/**
	 * Constructor.
	 *
	 * @param WP_Post $post
	 * @param Table_Args $args
	 */
	public function __construct( $post, $args ) {
		parent::__construct( $post );

		$this->filename_link = $args->table_filename_link;
		$this->new_tab       = $args->new_tab_links;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_data() {
        $document = new Document( $this->post->ID );

		$filename = apply_filters( 'document_library_pro_data_filename', $document->get_file_name( $this->filename_link, $this->new_tab ), $this->post );

		return $filename;
	}
}
