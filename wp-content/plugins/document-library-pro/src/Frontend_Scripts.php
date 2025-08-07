<?php
namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Frontend_Scripts as PTP_Frontend_Scripts;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util as PTP_Util;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args;
use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Util\Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Premium_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

defined( 'ABSPATH' ) || exit;
/**
 * Responsible for registering the front-end styles and scripts in Document Library Pro.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Frontend_Scripts implements Premium_Service, Registerable, Conditional {

	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Lib_Util::is_front_end();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		// Register front-end styles and scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );

		add_action( 'document_library_pro_before_get_table', [ $this, 'enqueue_table_scripts' ], 10, 1 );
		add_action( 'document_library_pro_before_get_grid', [ $this, 'enqueue_grid_scripts' ], 10, 1 );
	}

	/**
	 * Register the CSS assets.
	 */
	public function register_styles() {
		wp_register_style( 'dlp-folders', $this->asset_url( 'css/dlp-folders.css' ), [], $this->plugin->get_version() );
		wp_register_style( 'dlp-table', $this->asset_url( 'css/dlp-table.css' ), [], $this->plugin->get_version() );
		wp_register_style( 'dlp-grid', $this->asset_url( 'css/dlp-grid.css' ), [ 'select2-ptp' ], $this->plugin->get_version() );
		wp_register_style( 'dlp-search-box', $this->asset_url( 'css/dlp-search-box.css' ), [], $this->plugin->get_version() );

		if ( is_singular( Post_Type::POST_TYPE_SLUG ) ) {
			wp_enqueue_style( 'dlp-single-post', $this->asset_url( 'css/dlp-single-post.css' ), [], $this->plugin->get_version() );
		}

		$shortcode_options = Options::get_user_shortcode_options();
		$misc_options      = array_merge( Options::get_additional_options(), [ 'grid_columns' => $shortcode_options['grid_columns'] ] );

		wp_add_inline_style( 'dlp-grid', self::build_custom_grid_styles( $misc_options ) );
	}

	/**
	 * Register JS assets.
	 */
	public function register_scripts() {
		// Folders
		$script_dependencies = array_merge( Lib_Util::get_script_dependencies( $this->plugin, 'dlp-folders.js' )['dependencies'], [ 'jquery', 'jquery-blockui' ] );
		wp_register_script( 'dlp-folders', $this->asset_url( 'js/dlp-folders.js' ), $script_dependencies, $this->plugin->get_version(), true );
		Util::add_inline_script_params(
			'dlp-folders',
			'dlp_folders_params',
			apply_filters(
				'document_library_pro_folders_script_params',
				[
					'ajax_url'                 => admin_url( 'admin-ajax.php' ),
					'ajax_nonce'               => wp_create_nonce( 'dlp-folders' ),
					'ajax_action'              => 'dlp_fetch_table',
					'ajax_folder_search'       => 'dlp_folder_search',
					'ajax_folder_library'      => 'dlp_folder_library',
					'ajax_min_search_term_len' => max( 1, absint( apply_filters( 'document_library_pro_minimum_search_term_length', 3 ) ) ),
				]
			)
		);

		// Grid
		$script_dependencies = array_merge( Lib_Util::get_script_dependencies( $this->plugin, 'dlp-grid.js' )['dependencies'], [ 'jquery', 'jquery-blockui', 'select2-ptp', 'dlp-folders' ] );
		wp_register_script( 'dlp-grid', $this->asset_url( 'js/dlp-grid.js' ), $script_dependencies, $this->plugin->get_version(), true );
		Util::add_inline_script_params(
			'dlp-grid',
			'dlp_grid_params',
			[
				'ajax_url'                 => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'               => wp_create_nonce( 'dlp-grid' ),
				'ajax_action'              => 'dlp_fetch_grid',
				'ajax_min_search_term_len' => max( 1, absint( apply_filters( 'document_library_pro_minimum_search_term_length', 3 ) ) ),

			]
		);

		// Download Zip
		wp_register_script( 'dlp-download-zip', $this->asset_url( 'js/dlp-download-zip.js' ), [], $this->plugin->get_version(), true );
		wp_register_script( 'dlp-multi-download', $this->asset_url( 'js/dlp-multi-download.js' ), [ 'jquery', 'dlp-download-zip' ], $this->plugin->get_version(), true );
		wp_localize_script(
			'dlp-multi-download',
			'dlp_multi_download_params',
			[
				'zip_failed_error' => __( 'Failed to create the zip file. Please reselect your documents and try again.', 'document-library-pro' ),
			]
		);

		// Preview
		wp_register_script( 'micromodal', $this->asset_url( 'js/micromodal/micromodal.min.js' ), [], '0.4.6', true );
		wp_register_script( 'dlp-preview', $this->asset_url( 'js/dlp-preview.js' ), [ 'jquery', 'micromodal' ], $this->plugin->get_version(), true );
		wp_localize_script(
			'dlp-preview',
			'dlp_preview_params',
			[
				'pdf_error'   => __( 'Sorry, your browser doesn\'t support embedded PDFs.', 'document-library-pro' ),
				'audio_error' => __( 'Sorry, your browser doesn\'t support embedded audio.', 'document-library-pro' ),
				'video_error' => __( 'Sorry, your browser doesn\'t support embedded video.', 'document-library-pro' ),
				'spinner_url' => $this->asset_url( 'images/spinner.svg' ),
			]
		);

		// Download Count
		$script_dependencies = array_merge( Lib_Util::get_script_dependencies( $this->plugin, 'dlp-count.js' )['dependencies'], [ 'jquery' ] );
		wp_register_script( 'dlp-count', $this->asset_url( 'js/dlp-count.js' ), $script_dependencies, $this->plugin->get_version(), true );
		Util::add_inline_script_params(
			'dlp-count',
			'dlp_count_params',
			[
				'ajax_action' => 'dlp_download_count',
				'ajax_nonce'  => wp_create_nonce( 'dlp-count' ),
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
			]
		);

		// Check if accent_neutralise option is enabled
		$settings = Options::get_settings();

		if ( isset( $settings['accent_neutralise'] ) && $settings['accent_neutralise'] === '1' ) {
			wp_register_script( 'dlp-accent-neutralise', '//cdn.datatables.net/plug-ins/2.3.0/filtering/type-based/accent-neutralise.js', [ 'jquery-datatables-ptp' ], $this->plugin->get_version(), true );
		}

		if ( isset( $settings['diacritics_sort'] ) && $settings['diacritics_sort'] === '1' ) {
			wp_register_script( 'dlp-diacritics-sort', '//cdn.datatables.net/plug-ins/2.3.2/sorting/diacritics-sort.js', [ 'jquery-datatables-ptp' ], $this->plugin->get_version(), true );
		}
	}

	/**
	 * Enqueue the table assets.
	 *
	 * @param Posts_Table $posts_table
	 */
	public function enqueue_table_scripts( $posts_table ) {
		self::load_document_table_scripts( $posts_table->args );
	}

	/**
	 * Enqueue the grid assets.
	 *
	 * @param Document_Grid $document_grid
	 */
	public function enqueue_grid_scripts( $document_grid ) {
		self::load_document_grid_scripts( $document_grid->args );
	}

	/**
	 * Load the table assets.
	 *
	 * @param mixed|null $args
	 */
	public static function load_document_table_scripts( $args = null ) {
		wp_enqueue_style( 'dlp-table' );

		if ( ! $args ) {
			return;
		}

		if ( $args->multi_downloads ) {
			wp_enqueue_script( 'dlp-multi-download' );
		}

		if ( $args->preview ) {
			self::load_preview_scripts();
		}

		self::load_download_count_scripts();

		self::load_accent_neutralise_scripts();
		self::load_diacritics_sort_scripts();
	}

	/**
	 * Load the document grid assets.
	 *
	 * @param Table_Args|null $args
	 */
	public static function load_document_grid_scripts( $args = null ) {
		wp_enqueue_style( 'dlp-grid' );
		wp_enqueue_script( 'dlp-grid' );

		if ( ! $args ) {
			return;
		}

		if ( $args->preview ) {
			self::load_preview_scripts();
		}

		if ( $args->lightbox ) {
			wp_enqueue_style( 'photoswipe-default-skin' );
			wp_enqueue_script( 'photoswipe-ui-default' );

			add_action( 'wp_footer', [ self::class, 'load_photoswipe_template' ] );
		}

		if ( $args->shortcodes ) {
			// Add fitVids.js for responsive video if we're displaying shortcodes.
			if ( apply_filters( 'document_library_pro_use_fitvids', true ) ) {
				wp_enqueue_script( 'fitvids' );
			}

			// Queue media element and playlist scripts/styles.
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-playlist' );

			add_action( 'wp_footer', 'wp_underscore_playlist_templates', 0 );
		}

		self::load_download_count_scripts();

		self::load_accent_neutralise_scripts();
		self::load_diacritics_sort_scripts();
	}

	/**
	 * Load Photoswipe Template.
	 */
	public static function load_photoswipe_template() {
		PTP_Util::include_template( 'photoswipe.php' );
	}

	/**
	 * Load Preview Scripts.
	 */
	public static function load_preview_scripts() {
		wp_enqueue_script( 'dlp-preview' );
	}

	/**
	 * Load Download Count Scripts.
	 */
	public static function load_download_count_scripts() {
		wp_enqueue_script( 'dlp-count' );
	}

	/**
	 * Load Accent Neutralise Scripts.
	 */
	public static function load_accent_neutralise_scripts() {
		wp_enqueue_script( 'dlp-accent-neutralise' );
	}

	/**
	 * Load Diacritics Sort Scripts.
	 */
	public static function load_diacritics_sort_scripts() {
		wp_enqueue_script( 'dlp-diacritics-sort' );
	}

	/**
	 * Load Folder Scripts.
	 *
	 * @param Table_Args $args
	 */
	public static function load_folder_scripts( $args ) {
		if ( ! apply_filters( 'document_library_pro_load_frontend_scripts', true ) ) {
			return;
		}

		if ( $args->layout === 'table' ) {
			PTP_Frontend_Scripts::load_table_scripts( $args );
			self::load_document_table_scripts( $args );
		}

		if ( $args->layout === 'grid' ) {
			self::load_document_grid_scripts( $args );
		}

		wp_enqueue_script( 'dlp-folders' );
		wp_enqueue_style( 'dlp-folders' );
	}

	/**
	 * Get the assets url.
	 *
	 * @param string $path
	 * @return string
	 */
	private function asset_url( $path ) {
		return $this->plugin->get_dir_url() . 'assets/' . ltrim( $path, '/' );
	}

	/**
	 * Build Custom Grid Styles.
	 *
	 * @param array $options
	 * @return string
	 */
	private static function build_custom_grid_styles( $options ) {
		$styles = [];

		if ( ( $options['grid_design'] ?? 'default' ) === 'default' ) {
			return '';
		}

		// Ensure all keys for grid design options are set.
		$options = array_merge(
			array_fill_keys(
				[
					'grid_image_bg',
					'grid_category_bg',
					'grid_body_text',
					'grid_card_border',
					'grid_dropdown_border',
					'grid_hyperlink_font',
					'grid_button_font',
					'grid_button_border',
					'grid_corner_style',
					'grid_button_background',
					'grid_button_background_hover',
					'grid_card_background',
				],
				''
			),
			$options
		);

		if ( ! empty( $options['grid_image_bg'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-card-featured-icon',
				'css'      => sprintf( 'background-color: %1$s !important;', $options['grid_image_bg'] ),
			];
		}

		if ( ! empty( $options['grid_category_bg'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-card-categories span',
				'css'      => sprintf( 'background-color: %1$s !important;', $options['grid_category_bg'] ),
			];
		}

		if ( ! empty( $options['grid_button_font']['color'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-card-categories span a',
				'css'      => sprintf( 'color: %1$s !important;', $options['grid_button_font']['color'] ),
			];
		}

		if ( ! empty( $options['grid_corner_style'] ) ) {
			$grid_border_radius = '';
			switch ( $options['grid_corner_style'] ) {
				case 'rounded-corners':
					$grid_border_radius = '6px';
					break;
				case 'fully-rounded':
					$grid_border_radius = '300px';
					break;
				default:
					$grid_border_radius = '0px';
					break;
			}
			$styles[] = [
				'selector' => '.dlp-grid-card-categories span, .dlp-grid-card-content a.document-library-pro-button, .dlp-grid-container .select2-container--default .select2-selection--single, .dlp-grid-paginate-button',
				'css'      => sprintf( 'border-radius: %1$s !important;', $grid_border_radius ),
			];
			$styles[] = [
				'selector' => '.dlp-grid-dropdown .select2-results__options li:last-child',
				'css'      => sprintf( 'border-radius: 0 0 %1$s %1$s !important;', $grid_border_radius ),
			];
		}
		if ( ! empty( $options['grid_button_background'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-container .dlp-grid-card-content a.document-library-pro-button, .dlp-grid-paginate-button.current',
				'css'      => sprintf( 'background: %1$s !important;', $options['grid_button_background'] ),
			];
		}
		if ( ! empty( $options['grid_button_background_hover'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-container .dlp-grid-card-content a.document-library-pro-button:hover, .dlp-grid-paginate-button:hover',
				'css'      => sprintf( 'background: %1$s !important;', $options['grid_button_background_hover'] ),
			];
		}
		if ( ! empty( $options['grid_card_background'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-card-content',
				'css'      => sprintf( 'background-color: %1$s !important;', $options['grid_card_background'] ),
			];
		}
		if ( ! empty( $options['grid_body_text']['color'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-card-excerpt, .dlp-grid-card-authors, .dlp-grid-card-file-info, .dlp-grid-length, .dlp-grid-totals, .dlp-grid-search, .dlp-grid-paginate-button, .dlp-grid-paginate-button:hover, .dlp-grid-paginate-button.disabled, .dlp-grid-documents, .dlp-grid-search input',
				'css'      => sprintf( 'color: %1$s !important;', $options['grid_body_text']['color'] ),
			];
			$styles[] = [
				'selector' => '.dlp-grid-paginate-button.current, .dlp-grid-paginate-button:hover',
				'css'      => sprintf( 'border-color: %1$s !important; color: %2$s !important;', $options['grid_body_text']['color'], $options['grid_button_font']['color'] ),
			];
			$styles[] = [
				'selector' => '.dlp-grid-search input:focus',
				'css'      => sprintf( 'outline-color: %1$s !important;', $options['grid_body_text']['color'] ),
			];
		}
		if ( ! empty( $options['grid_body_text']['size'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-card-excerpt',
				'css'      => sprintf( 'font-size: %1$spx !important;', $options['grid_body_text']['size'] ),
			];
		}
		if ( ! empty( $options['grid_card_border']['color'] ) && ! empty( $options['grid_card_border']['size'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-card',
				'css'      => sprintf( 'border-radius: 8px; border: %1$spx solid %2$s !important;', $options['grid_card_border']['size'], $options['grid_card_border']['color'] ),
			];
		}
		if ( ! empty( $options['grid_dropdown_border']['color'] ) && ! empty( $options['grid_dropdown_border']['size'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-container .select2-container--default .select2-selection--single',
				'css'      => sprintf( 'border: %1$spx solid %2$s !important;', $options['grid_dropdown_border']['size'], $options['grid_dropdown_border']['color'] ),
			];
		}
		if ( ! empty( $options['grid_button_border']['color'] ) && ! empty( $options['grid_button_border']['size'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-container .dlp-grid-card-content a.document-library-pro-button',
				'css'      => sprintf( 'border: %1$spx solid %2$s !important;', $options['grid_button_border']['size'], $options['grid_button_border']['color'] ),
			];
		}
		if ( ! empty( $options['grid_hyperlink_font']['size'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-card-content a',
				'css'      => sprintf( 'font-size: %1$spx !important;', $options['grid_hyperlink_font']['size'] ),
			];
		}
		if ( ! empty( $options['grid_hyperlink_font']['color'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-card-content a',
				'css'      => sprintf( 'color: %1$s !important;', $options['grid_hyperlink_font']['color'] ),
			];
		}
		if ( ! empty( $options['grid_button_font']['size'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-container .dlp-grid-card-content a.document-library-pro-button',
				'css'      => sprintf( 'font-size: %1$spx !important;', $options['grid_button_font']['size'] ),
			];
		}
		if ( ! empty( $options['grid_button_font']['color'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-container .dlp-grid-card-content a.document-library-pro-button, .dlp-grid-container .dlp-grid-card-content a.document-library-pro-button .dlp-icon',
				'css'      => sprintf( 'color: %1$s !important;', $options['grid_button_font']['color'] ),
			];
		}
		if ( ! empty( $options['grid_body_text']['color'] ) ) {
			$dropdown_color       = $options['grid_body_text']['color'];
			$dropdown_background  = $options['grid_button_background'];
			$dropdown_hover_color = $options['grid_button_font']['color'];
			$styles[]             = [
				'selector' => '.dlp-grid-controls .select2-container .select2-selection--single .select2-selection__rendered, .select2-dropdown.dlp-grid-dropdown .select2-results__option',
				'css'      => sprintf( 'color: %1$s !important;', $dropdown_color ),
			];
			$styles[]             = [
				'selector' => '.select2-container--default .select2-selection--single .select2-selection__arrow b',
				'css'      => sprintf( 'border-color: %1$s transparent transparent transparent!important', $dropdown_color ),
			];
			$styles[]             = [
				'selector' => '.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b',
				'css'      => sprintf( 'border-color: transparent transparent %1$s transparent!important', $dropdown_color ),
			];
			$styles[]             = [
				'selector' => '.select2-container--default .select2-results__option--highlighted[aria-selected]',
				'css'      => sprintf( 'color: %2$s!important ;background-color: %1$s!important', $dropdown_background, $dropdown_hover_color ),
			];
		}

		$result = array_reduce(
			$styles,
			function ( $carry, $style ) {
				if ( ! empty( $style['css'] ) ) {
					$carry .= sprintf( '%1$s { %2$s } ', $style['selector'], $style['css'] );
				}
				return $carry;
			},
			''
		);

		return $result;
	}
}
