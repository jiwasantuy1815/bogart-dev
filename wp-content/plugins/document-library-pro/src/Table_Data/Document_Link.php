<?php
namespace Barn2\Plugin\Document_Library_Pro\Table_Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data\Abstract_Table_Data;
use Barn2\Plugin\Document_Library_Pro\Document;

defined( 'ABSPATH' ) || exit;
/**
 * Gets data for the 'link' column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Link extends Abstract_Table_Data {

	private $document_link;
	private $link_text;
	private $link_destination;
	private $link_style;
	private $link_target;
	private $link_icon;
	private $multi_downloads;
	private $preview;
	private $preview_text;
	private $preview_style;
	private $preview_icon;
	private $button_class;

	/**
	 * Constructor.
	 *
	 * @param WP_Post $post
	 * @param Table_Args $args
	 */
	public function __construct( $post, $args ) {

		parent::__construct( $post );

		$this->document_link    = $args->document_link;
		$this->link_text        = $args->link_text;
		$this->link_destination = $args->link_destination;
		$this->link_style       = $args->link_style;
		$this->link_target      = $args->link_target;
		$this->link_icon        = $args->link_icon;
		$this->multi_downloads  = $args->multi_downloads;
		$this->preview          = $args->preview;
		$this->preview_style    = $args->preview_style;
		$this->preview_text     = $args->preview_text;
		$this->preview_icon     = $args->preview_icon;
		$this->button_class     = apply_filters( 'document_library_pro_button_column_button_class', 'dlp-download-button document-library-pro-button button btn' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_data() {
		$html     = '';
		$document = new Document( $this->post->ID );

		if ( $this->document_link ) {
			$html .= $document->get_download_button_v2( $this->link_text, $this->link_style, $this->link_icon, $this->link_destination, $this->link_target );
		}

		if ( $this->preview ) {
			$html .= $document->get_preview_button_v2( $this->preview_text, $this->preview_style, $this->preview_icon, 'table' );
		}

		$should_display = apply_filters( 'document_library_pro_download_checkboxes_should_display', true, $document, $this->link_text, $this->link_style, $this->link_destination );

		if ( $should_display && $this->document_link && $this->multi_downloads && $document->get_link_type() === 'file' ) {
			$html .= sprintf( '<input type="checkbox" name="zip-urls" data-download-url="%1$s" data-download-id="%2$d" />', $document->get_download_url(), $this->post->ID );
		}

		$html = sprintf( '<div class="dlp-table-document-link-wrap">%s</div>', $html );

		return apply_filters( 'document_library_pro_data_link', $html, $this->post );
	}
}
