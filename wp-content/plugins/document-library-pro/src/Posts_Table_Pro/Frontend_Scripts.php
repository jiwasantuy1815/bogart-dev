<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data\Post_Hidden_Filter;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\CSS_Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Premium_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

/**
 * Responsible for registering the front-end styles and scripts in Posts Table Pro.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Frontend_Scripts implements Premium_Service, Registerable, Conditional {

	const SCRIPT_HANDLE      = 'document-library-pro';
	const DATATABLES_VERSION = '1.13.1';

	private $plugin;
	private $script_version;

	public function __construct( Plugin $plugin ) {
		$this->plugin         = $plugin;
		$this->script_version = $this->plugin->get_version();
	}

	public function is_required() {
		return Lib_Util::is_front_end();
	}

	public function register() {
		// Register front-end styles and scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_head_scripts' ], 20 );
	}

	public function register_styles() {
		wp_register_style( 'jquery-datatables-ptp', Util::get_asset_url( 'js/datatables/datatables.min.css' ), [], self::DATATABLES_VERSION );
		wp_register_style( 'photoswipe', Util::get_asset_url( 'js/photoswipe/photoswipe.min.css' ), [], '4.1.3' );
		wp_register_style( 'photoswipe-default-skin', Util::get_asset_url( 'js/photoswipe/default-skin/default-skin.min.css' ), [ 'photoswipe' ], '4.1.3' );
		wp_register_style( 'select2-ptp', Util::get_asset_url( 'js/select2/select2.min.css' ), [], '4.0.13' );

		wp_register_style( self::SCRIPT_HANDLE, Util::get_asset_url( 'css/styles.css' ), [ 'jquery-datatables-ptp', 'select2-ptp' ], $this->script_version );

		// Add RTL data - we need suffix to correctly format RTL stylesheet when minified.
		wp_style_add_data( self::SCRIPT_HANDLE, 'rtl', 'replace' );
		wp_style_add_data( self::SCRIPT_HANDLE, 'suffix', '.min' );

		$misc_options = self::get_additional_options();

		// If using custom style, build CSS and add inline style data.
		if ( isset( $misc_options['table_design'] ) && $misc_options['table_design'] !== 'default' ) {
			wp_add_inline_style( self::SCRIPT_HANDLE, self::build_custom_styles( $misc_options, Util::TABLE_CLASS ) );
		}

		// Search Box (Shortcode & Widget)
		wp_register_style( 'posts-table-pro-search-box', Util::get_asset_url( 'css/search-box.css' ), [], '1.0.0' );

		// Header styles - we just a dummy handle as we only need inline styles in <head>.
		wp_register_style( 'posts-table-pro-head', false, false, $this->plugin->get_version() );

		// Ensure tables don't 'flicker' on page load - visibility is set by JS when table initialised.
		wp_add_inline_style( 'posts-table-pro-head', 'table.posts-data-table { visibility: hidden; }' );
	}

	public function register_scripts() {
		$suffix = Lib_Util::get_script_suffix();

		wp_register_script( 'jquery-datatables-ptp', Util::get_asset_url( "js/datatables/datatables{$suffix}.js" ), [ 'jquery' ], self::DATATABLES_VERSION, true );
		wp_register_script( 'jquery-blockui', Util::get_asset_url( "js/jquery-blockui/jquery.blockUI{$suffix}.js" ), [ 'jquery' ], '2.70', true );
		wp_register_script( 'photoswipe', Util::get_asset_url( "js/photoswipe/photoswipe{$suffix}.js" ), [], '4.1.3', true );
		wp_register_script( 'photoswipe-ui-default', Util::get_asset_url( "js/photoswipe/photoswipe-ui-default{$suffix}.js" ), [ 'photoswipe' ], '4.1.3', true );
		wp_register_script( 'select2-ptp', Util::get_asset_url( "js/select2/select2.full{$suffix}.js" ), [ 'jquery' ], '4.0.13', true );
		wp_register_script( 'fitvids', Util::get_asset_url( "js/jquery-fitvids/jquery.fitvids{$suffix}.js" ), [ 'jquery' ], '1.1', true );

		wp_register_script(
			self::SCRIPT_HANDLE,
			Util::get_asset_url( 'js/posts-table-pro.js' ),
			[ 'jquery', 'jquery-datatables-ptp', 'jquery-blockui', 'select2-ptp' ],
			$this->script_version,
			true
		);

		$script_params = [
			'ajax_url'              => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'            => wp_create_nonce( self::SCRIPT_HANDLE ),
			'ajax_action'           => 'dlp_load_posts',
			'table_class'           => esc_attr( Util::get_table_class() ),
			'enable_select2'        => apply_filters( 'document_library_pro_enable_select2', true ),
			'filter_term_separator' => Post_Hidden_Filter::get_term_separator(),
			'language'              => apply_filters(
				'document_library_pro_language_defaults',
				[
					'infoFiltered'      => __( '(_MAX_ in total)', 'document-library-pro' ),
					'lengthMenu'        => __( 'Show _MENU_ per page', 'document-library-pro' ),
					'search'            => apply_filters( 'document_library_pro_search_label', __( 'Search:', 'document-library-pro' ) ),
					'searchPlaceholder' => apply_filters( 'document_library_pro_search_placeholder', '' ),
					'paginate'          => [
						'first'    => __( 'First', 'document-library-pro' ),
						'last'     => __( 'Last', 'document-library-pro' ),
						'next'     => __( 'Next', 'document-library-pro' ),
						'previous' => __( 'Previous', 'document-library-pro' ),
					],
					'thousands'         => _x( ',', 'thousands separator', 'document-library-pro' ),
					'decimal'           => _x( '.', 'decimal mark', 'document-library-pro' ),
					'aria'              => [
						/* translators: ARIA text for sorting column in ascending order */
						'sortAscending'  => __( ': activate to sort column ascending', 'document-library-pro' ),
						/* translators: ARIA text for sorting column in descending order */
						'sortDescending' => __( ': activate to sort column descending', 'document-library-pro' ),
					],
					'filterBy'          => apply_filters( 'document_library_pro_search_filter_label', '' ),
					'emptyFilter'       => __( 'No results found', 'document-library-pro' ),
					'resetButton'       => apply_filters( 'document_library_pro_reset_button', __( 'Reset', 'document-library-pro' ) ),
				]
			),
		];

		wp_add_inline_script(
			self::SCRIPT_HANDLE,
			sprintf( 'var posts_table_params = %s;', wp_json_encode( apply_filters( 'document_library_pro_script_params', $script_params ) ) ),
			'before'
		);
	}

	public function load_head_scripts() {
		wp_enqueue_style( 'posts-table-pro-head' );
	}

	public static function load_table_scripts( ?Table_Args $args = null ) {
		if ( ! apply_filters( 'document_library_pro_load_frontend_scripts', true ) ) {
			return;
		}

		wp_enqueue_style( self::SCRIPT_HANDLE );
		wp_enqueue_script( self::SCRIPT_HANDLE );

		if ( $args ) {
			// Add fitVids.js for responsive video if we're displaying shortcodes.
			if ( apply_filters( 'document_library_pro_use_fitvids', true ) ) {
				wp_enqueue_script( 'fitvids' );
			}

			// Queue media element and playlist scripts/styles.
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-playlist' );

			add_action( 'wp_footer', 'wp_underscore_playlist_templates', 0 );

			// Enqueue Photoswipe for image lightbox.
			if ( $args->lightbox ) {
				wp_enqueue_style( 'photoswipe-default-skin' );
				wp_enqueue_script( 'photoswipe-ui-default' );

				add_action( 'wp_footer', [ self::class, 'load_photoswipe_template' ] );
			}
		}
	}

	public static function load_photoswipe_template() {
		Util::include_template( 'photoswipe.php' );
	}

	/**
	 * Builds the custom CSS styles for the table based on the provided options.
	 *
	 * @param array  $options     The custom styling options.
	 * @param string $table_class The CSS class of the table.
	 * @return string The generated CSS styles.
	 */
	private function build_custom_styles( $options, $table_class ) {
		$styles                 = [];
		$result                 = '';
		$table_selector         = 'table.' . $table_class;
		$table_wrapper_selector = '.posts-table-wrapper';

		if ( ( $options['table_design'] ?? 'default' ) === 'default' ) {
			return '';
		}

		// External border.
		if ( $this->valid_color_size_setting( $options['external_border'] ) ) {
			$styles[] = [
				'selector' => $table_selector,
				'css'      => CSS_Util::build_border_style( $options['external_border'], 'all', true ),
			];
		}
		// Header border.
		if ( $this->valid_color_size_setting( $options['header_border'] ) ) {
			$styles[] = [
				'selector' => $table_selector . ' thead th',
				'css'      => CSS_Util::build_border_style( $options['header_border'], 'bottom', true ),
			];

			$styles[] = [
				'selector' => $table_selector . ' tfoot th',
				'css'      => CSS_Util::build_border_style( $options['header_border'], 'top', true ),
			];
		}
		// Horizontal borders between rows
		if ( $this->valid_color_size_setting( $options['border_horizontal_cell'] ) ) {
			$styles[] = [
				'selector' => "$table_selector tbody tr:not(:last-child) td",
				'css'      => CSS_Util::build_border_style( $options['border_horizontal_cell'], 'bottom', true ),
			];
		}

		// Vertical borders between columns
		if ( $this->valid_color_size_setting( $options['border_vertical_cell'] ) ) {
			$styles[] = [
				'selector' => "$table_selector tbody td:not(:last-child)",
				'css'      => CSS_Util::build_border_style( $options['border_vertical_cell'], 'right', true ),
			];

			// Also apply to header cells for consistency
			$styles[] = [
				'selector' => "$table_selector thead th:not(:last-child), $table_selector tfoot th:not(:last-child)",
				'css'      => CSS_Util::build_border_style( $options['border_vertical_cell'], 'right', true ),
			];
		}

		// Bottom border of the table
		if ( $this->valid_color_size_setting( $options['border_bottom'] ) ) {
			$styles[] = [
				'selector' => "$table_selector",
				'css'      => CSS_Util::build_border_style( $options['border_bottom'], 'bottom', true ),
			];

			$styles[] = [
				'selector' => "$table_selector tfoot",
				'css'      => CSS_Util::build_border_style( $options['border_bottom'], 'bottom', true ),
			];
		}

		// Header background color.
		if ( ! empty( $options['header_bg'] ) ) {
			$styles[] = [
				'selector' => sprintf( '%1$s thead th, %1$s tfoot th', $table_selector ),
				'css'      => CSS_Util::build_background_style( $options['header_bg'], true ),
			];
		}

		// Body background color.
		if ( ! empty( $options['body_bg'] ) ) {
			$styles[]      = [
				'selector' => "$table_selector.datatables-no-rows-found",
				'css'      => CSS_Util::build_background_style( $options['body_bg'], true ),
			];
			$styles[]      = [
				'selector' => "$table_selector tbody tr",
				'css'      => 'background-color: transparent !important;',
			];
			$body_selector = "$table_selector tbody td";
			if ( ! empty( $options['cell_backgrounds'] ) && $options['cell_backgrounds'] === 'alternate-rows' ) {
				$styles[] = [
					'selector' => "$table_selector tbody tr:nth-child(odd) td",
					'css'      => CSS_Util::build_background_style( $options['body_bg'], true ),
				];
				$styles[] = [
					'selector' => "$table_selector tbody tr:nth-child(even) td",
					'css'      => CSS_Util::build_background_style( $options['body_bg_alt'], true ),
				];
			}
			if ( ! empty( $options['cell_backgrounds'] ) && $options['cell_backgrounds'] === 'alternate-columns' ) {
				$styles[] = [
					'selector' => "$table_selector tbody tr td:nth-child(odd)",
					'css'      => CSS_Util::build_background_style( $options['body_bg'], true ),
				];
				$styles[] = [
					'selector' => "$table_selector tbody tr td:nth-child(even)",
					'css'      => CSS_Util::build_background_style( $options['body_bg_alt'], true ),
				];
			}
			if ( ! empty( $options['cell_backgrounds'] ) && $options['cell_backgrounds'] === 'no-alternate' ) {
				$styles[] = [
					'selector' => "$table_selector tbody tr td",
					'css'      => CSS_Util::build_background_style( $options['body_bg'], true ),
				];
			}
		}

		// Body text
		if ( $this->valid_color_size_setting( $options['body_text'] ) ) {
			$styles[] = [
				'selector' => "$table_selector tbody td, .posts-table-controls label, .wc-product-table-below, .wc-product-table-above, .posts-table-wrapper .posts-table-controls>div, .select2-dropdown.posts-table-dropdown .select2-results__option",
				'css'      => CSS_Util::build_font_style( $options['body_text'], true ),
			];
			if ( ! empty( $options['body_text']['color'] ) ) {
				$styles[] = [
					'selector' => "$table_wrapper_selector input[type=\"search\"]:focus",
					'css'      => sprintf( 'outline-color: %s;', $options['body_text']['color'] ),
				];
			}
			$styles[] = [
				'selector' => '.select2-selection.select2-selection--single',
				'css'      => sprintf( 'border-color: %s;', $options['body_text']['color'] ),
			];
			$styles[] = [
				'selector' => '.select2-container--default .select2-selection--single .select2-selection__arrow b',
				'css'      => sprintf( 'border-color: %s transparent transparent transparent;', $options['body_text']['color'] ),
			];
			$styles[] = [
				'selector' => '.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b',
				'css'      => sprintf( 'border-color: transparent transparent %s transparent;', $options['body_text']['color'] ),
			];
		}

		// Header text.
		if ( $this->valid_color_size_setting( $options['header_text'] ) ) {
			$styles[] = [
				'selector' => "$table_selector thead th, $table_selector thead td, $table_selector tfoot th, $table_selector tfoot td",
				'css'      => CSS_Util::build_font_style( $options['header_text'], true ),
			];
		}

		// Hyperlink font
		if ( ! empty( $options['hyperlink_font'] ) ) {
			$styles[] = [
				'selector' => "$table_selector tbody td a, .posts-table-reset a",
				'css'      => CSS_Util::build_font_style( $options['hyperlink_font'], true ),
			];
		}

		// Button font
		if ( ! empty( $options['button_font'] ) ) {
			$styles[] = [
				'selector' => "$table_selector tbody td .document-library-pro-button, .dlp-multiple-download-btn:not([disabled])",
				'css'      => CSS_Util::build_font_style( $options['button_font'], true ),
			];
			$styles[] = [
				'selector' => '.posts-table-controls .wc-product-table-multi-form input[type=submit], .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button:hover, .select2-container--default .select2-results__option--highlighted[aria-selected]',
				'css'      => CSS_Util::build_font_style( $options['button_font'], true ),
			];
		}

		if ( ! empty( $options['disabled_button_font'] ) ) {
			$styles[] = [
				'selector' => '.dlp-multiple-download-btn[disabled]',
				'css'      => CSS_Util::build_font_style( $options['disabled_button_font'], true ),
			];
		}

		if ( ! empty( $options['button_disabled_bg'] ) ) {
			$styles[] = [
				'selector' => '.dlp-multiple-download-btn[disabled]',
				'css'      => CSS_Util::build_background_style( $options['button_disabled_bg'], true ),
			];
		}

		// Button background
		if ( ! empty( $options['button_bg'] ) ) {
			$styles[] = [
				'selector' => "$table_selector tbody td .document-library-pro-button, .dlp-multiple-download-btn:not([disabled])",
				'css'      => CSS_Util::build_background_style( $options['button_bg'], true ),
			];
			$styles[] = [
				'selector' => '.posts-table-controls .wc-product-table-multi-form input[type=submit], .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button:hover, .select2-container--default .select2-results__option--highlighted[aria-selected]',
				'css'      => CSS_Util::build_background_style( $options['button_bg'], true ),
			];
			$styles[] = [
				'selector' => '.dataTables_wrapper .dataTables_paginate .paginate_button:hover, .select2-results__option.select2-results__option--highlighted',
				'css'      => sprintf( 'background: %1$s !important;', $options['button_bg'] ),
			];
			$styles[] = [
				'selector' => ".posts-table-controls .wc-product-table-multi-form input[type=submit], .dataTables_wrapper .dataTables_paginate .paginate_button.current, $table_selector tbody td .document-library-pro-button",
				'css'      => 'border-width: 1px !important;',
			];
			$styles[] = [
				'selector' => ".posts-table-controls .wc-product-table-multi-form input[type=submit], $table_selector tbody td .document-library-pro-button, .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover",
				'css'      => sprintf( 'border-color: %s !important;', esc_attr( $options['button_bg'] ) ),
			];
		}
		// Button background hover
		if ( ! empty( $options['button_bg_hover'] ) ) {
			$styles[] = [
				'selector' => "$table_selector tbody td .document-library-pro-button:hover, .dlp-multiple-download-btn:not([disabled]):hover",
				'css'      => CSS_Util::build_background_style( $options['button_bg_hover'], true ),
			];
			$styles[] = [
				'selector' => '.posts-table-controls .wc-product-table-multi-form input[type=submit]:hover, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover, .dataTables_wrapper .dataTables_paginate .paginate_button:hover',
				'css'      => CSS_Util::build_background_style( $options['button_bg_hover'], true ),
			];
			$styles[] = [
				'selector' => ".posts-table-controls .wc-product-table-multi-form input[type=submit]:hover, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover, .dataTables_wrapper .dataTables_paginate .paginate_button:hover, $table_selector tbody td .document-library-pro-button:hover, .dlp-multiple-download-btn:not([disabled]):hover",
				'css'      => 'border-width: 1px !important;',
			];
			$styles[] = [
				'selector' => '.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover, .dataTables_wrapper .dataTables_paginate .paginate_button:hover, .dlp-multiple-download-btn:not([disabled]):hover',
				'css'      => sprintf( 'border-color: %s', esc_attr( $options['button_bg_hover'] ) ),
			];
		}
		// Dropdown background
		if ( ! empty( $options['dropdown_background'] ) ) {
			$styles[] = [
				'selector' => '.posts-table-controls .select2-container .select2-selection--single',
				'css'      => CSS_Util::build_background_style( $options['dropdown_background'], true ),
			];
		}
		// Text background
		if ( ! empty( $options['text_background'] ) ) {
			$styles[] = [
				'selector' => '.posts-table-controls input[type=search]',
				'css'      => CSS_Util::build_background_style( $options['text_background'], true ),
			];
		}
		// Text font
		if ( ! empty( $options['text_font'] ) ) {
			$styles[] = [
				'selector' => '.posts-table-controls input[type=search]',
				'css'      => sprintf( 'color: %s !important', esc_attr( $options['text_font'] ) ),
			];
		}
		// Text border
		if ( ! empty( $options['text_border'] ) ) {
			$styles[] = [
				'selector' => '.posts-table-controls input[type=search]',
				'css'      => CSS_Util::build_border_style( $options['text_border'], 'all', true ),
			];
		}
		// Dropdown font
		if ( ! empty( $options['dropdown_font'] ) ) {
			$styles[] = [
				'selector' => '.select2-container--default .select2-selection--single .select2-selection__rendered',
				'css'      => sprintf( 'color: %s !important', esc_attr( $options['dropdown_font'] ) ),
			];
		}
		// Dropdown border
		if ( ! empty( $options['dropdown_border']['color'] ) && ! empty( $options['dropdown_border']['size'] ) ) {
			$styles[] = [
				'selector' => '.posts-table-controls .select2-container .select2-selection--single',
				'css'      => sprintf( 'border: %spx solid %s !important', esc_attr( $options['dropdown_border']['size'] ), esc_attr( $options['dropdown_border']['color'] ) ),
			];
		}
		// Spacing
		if ( 'default' !== $options['table_spacing'] ) {
			$padding = null;

			switch ( $options['table_spacing'] ) {
				case 'compact':
					$padding = 5;
					break;
				case 'normal':
					$padding = 8;
					break;
				case 'spacious':
					$padding = 12;
					break;
			}

			if ( $padding ) {
				$left_right_padding = $padding + 2;

				$styles[] = [
					'selector' => sprintf( 'table.%s tbody td', $table_class ),
					'css'      => sprintf( 'padding: %upx %upx;', $padding, $left_right_padding ),
				];

				$header_padding = $padding + 2;

				$styles[] = [
					'selector' => sprintf( 'table.%1$s thead th, table.%1$s tfoot th', $table_class ),
					'css'      => sprintf( 'padding: %1$upx 18px %1$upx %2$upx;', $header_padding, $left_right_padding ),
				];

				$styles[] = [
					'selector' => sprintf( '.rtl table.%1$s thead th, .rtl table.%1$s tfoot th', $table_class ),
					'css'      => sprintf( 'padding-left: 18px; padding-right: %upx;', $left_right_padding ),
				];
			}
		}
		// Replace the existing corner style code with this more concise version
		if ( ! empty( $options['table_corner_style'] ) && $options['table_corner_style'] !== 'square-corners' ) {
			// Set radius based on style
			$radius = $options['table_corner_style'] === 'fully-rounded' ? 19 : 6;

			// Common selectors with different radius values
			$corners = [
				"$table_selector"                      => "{$radius}px",
				"$table_selector thead th:first-child" => "{$radius}px 0 0 0",
				"$table_selector thead th:not(.dtr-hidden):last-of-type" => "0 {$radius}px 0 0",
				"$table_selector.no-footer tr:last-child td:first-child" => "0 0 0 {$radius}px",
				"$table_selector.no-footer tr:last-child td:last-child" => "0 0 {$radius}px 0",
				"$table_selector tfoot th:first-child" => "0 0 0 {$radius}px",
				"$table_selector tfoot th:last-child"  => "0 0 {$radius}px 0",
			];

			// Add fully-rounded specific styles
			if ( $options['table_corner_style'] === 'fully-rounded' ) {
				$corners[ "$table_selector tfoot th:first-child, $table_selector tbody tr:last-child td:first-child:not(.dataTables_empty)" ] = "0 0 0 {$radius}px !important";
				$corners[ "$table_selector tbody tr:last-child td.dataTables_empty" ] = "0 0 {$radius}px {$radius}px !important";
				$corners[ "$table_selector tfoot th:last-child, $table_selector tbody tr:last-child td:last-child:not(.dataTables_empty)" ] = "0 0 {$radius}px 0 !important";
			}

			// Process all corner styles
			foreach ( $corners as $selector => $value ) {
				$styles[] = [
					'selector' => $selector,
					'css'      => "border-radius: {$value};",
				];
			}

			// Handle external border
			if ( ! empty( $options['external_border']['size'] ) ) {
				$border_radius = $radius;
				if ( $options['table_corner_style'] === 'rounded-corners' ) {
					$border_radius = $radius + ( ! empty( $options['external_border']['size'] ) ? $options['external_border']['size'] : 1 );
				}

				$styles[] = [
					'selector' => "$table_selector",
					'css'      => "border-radius: {$border_radius}px !important;",
				];
			}

			// Form elements
			$form_radius = $options['table_corner_style'] === 'fully-rounded' ? "{$radius}px !important" : "{$radius}px";
			$styles[]    = [
				'selector' => "$table_selector tbody td .document-library-pro-button, .dlp-multiple-download-btn, .posts-table-controls .wc-product-table-multi-form input[type=submit], $table_selector tbody td .quantity input.qty, .posts-table-controls .select2-container .select2-selection--single, .posts-table-controls input[type=search], .posts-table-controls .dataTables_paginate .paginate_button",
				'css'      => "border-radius: {$form_radius};",
			];
		}

		// Build the CSS styles
		foreach ( $styles as $style ) {
			if ( ! empty( $style['css'] ) ) {
				$result .= sprintf( '%1$s { %2$s } ', $style['selector'], $style['css'] );
			}
		}

		return $result;
	}

	/**
	 * Checks if the color/size setting is valid.
	 * A setting is valid if it's an array and has either a numeric 'size' or a non-empty 'color'.
	 *
	 * @param mixed $color_size The color/size setting to validate.
	 * @return bool True if valid, false otherwise.
	 */
	private function valid_color_size_setting( $color_size ) {
		if ( ! is_array( $color_size ) ) {
			return false;
		}

		return ( isset( $color_size['size'] ) && is_numeric( $color_size['size'] ) ) || ! empty( $color_size['color'] );
	}

	private static function get_additional_options() {
		$defaults = [
			'cache_expiry' => 6,
			'design'       => 'default',
		];

		return self::get_option( Options::MISC_OPTION_KEY, $defaults );
	}

	private static function get_option( $option, $default ) {
		$value = get_option( $option, $default );

		if ( empty( $value ) || ( is_array( $default ) && ! is_array( $value ) ) ) {
			$value = $default;
		}

		if ( is_array( $value ) && is_array( $default ) ) {
			$value = array_merge( $default, $value );
		}

		return $value;
	}
}
