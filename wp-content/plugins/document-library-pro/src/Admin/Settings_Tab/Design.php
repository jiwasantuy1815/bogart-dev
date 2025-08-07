<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Settings_Tab;

use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Fields\ {
	Color,
	Color_Size,
	Image_Label,
	Radio,
	Number,
	Select,
	Textarea,
	Button
};
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Tabs\Tab;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Barn2\Settings_API\Sections\Section;
use Barn2\Plugin\Document_Library_Pro\Util\Template_Defaults;

/**
 * Handles the design tab.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Design {

	/**
	 * The tab ID.
	 *
	 * @var string
	 */
	public const TAB_ID = 'design';

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
	 * Design tab.
	 *
	 * @return Tab
	 */
	public function get_tab() {
		return new Tab(
			'design',
			esc_html__( 'Design', 'document-library-pro' ),
			[
				$this->get_design_header_section(),
				$this->get_table_design_section(),
				$this->get_grid_design_section(),
				$this->get_folder_design_section(),
			]
		);
	}

	/**
	 * Design header section.
	 *
	 * @return Section
	 */
	private function get_design_header_section() {
		$section = new Section(
			'design',
			'',
			__( 'Use the following options to customize the design of the document table and grid.', 'document-library-pro' )
		);

		return $section;
	}

	/**
	 * Design section.
	 *
	 * @return Section
	 */
	private function get_table_design_section() {

		$section = new Section(
			'table_design',
			esc_html__( 'Table', 'document-library-pro' ),
		);

		$design = new Radio(
			'table_design',
			esc_html__( 'Design', 'document-library-pro' ),
			'',
			'',
			[],
			'default'
		);

		$design->set_options(
			[
				[
					'label' => __( 'Default', 'document-library-pro' ),
					'value' => 'default',
				],
				[
					'label' => __( 'Custom', 'document-library-pro' ),
					'value' => 'custom',
				],
			]
		);

		$custom_design_condition = [
			'rules' => [
				'table_design' => [
					'op'    => 'eq',
					'value' => 'custom',
				],
			],
		];

		$color_size_default = [
			'color' => '',
			'size'  => '',
		];

		$default_table_template = new Image_Label(
			'default_table_template',
			__( 'Default table template', 'document-library-pro' ),
			'',
			'',
			[],
			'',
			$custom_design_condition
		);

		$default_table_template->set_options(
			[
				[
					'label' => __( 'Default', 'document-library-pro' ),
					'value' => 'default',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-default-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-default.png' ),
				],
				[
					'label' => __( 'Minimal', 'document-library-pro' ),
					'value' => 'minimal',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-minimal-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-minimal.png' ),
				],
				[
					'label' => __( 'Delicate', 'document-library-pro' ),
					'value' => 'delicate',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-delicate-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-delicate.png' ),
				],
				[
					'label' => __( 'Neutral', 'document-library-pro' ),
					'value' => 'neutral',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-neutral-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-neutral.png' ),
				],
				[
					'label' => __( 'Dark', 'document-library-pro' ),
					'value' => 'dark',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-dark-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-dark.png' ),
				],
				[
					'label' => __( 'Blue', 'document-library-pro' ),
					'value' => 'blue',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-blue-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-blue.png' ),
				],
				[
					'label' => __( 'Nature', 'document-library-pro' ),
					'value' => 'nature',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-nature-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-nature.png' ),
				],
			]
		);
		$external_border = new Color_Size(
			'external_border',
			esc_html__( 'Borders', 'document-library-pro' ),
			$this->get_icon( 'external-border.svg', __( 'External border icon', 'document-library-pro' ) ) . esc_html__( 'External', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Width', 'document-library-pro' ),
			],
			$color_size_default,
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'external_border' )
		);
		$external_border->set_max( 50 );

		$header_border = new Color_Size(
			'header_border',
			'',
			$this->get_icon( 'header-border.svg', __( 'Header border icon', 'document-library-pro' ) ) . esc_html__( 'Header', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Width', 'document-library-pro' ),
			],
			$color_size_default,
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'header_border' )
		);
		$header_border->set_max( 50 );

		$horizontal_border = new Color_Size(
			'border_horizontal_cell',
			'',
			$this->get_icon( 'horizontal-cell.svg', __( 'Horizontal border icon', 'document-library-pro' ) ) . esc_html__( 'Horizontal', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Width', 'document-library-pro' ),
			],
			$color_size_default,
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'border_horizontal_cell' )
		);
		$horizontal_border->set_max( 50 );

		$vertical_border = new Color_Size(
			'border_vertical_cell',
			'',
			$this->get_icon( 'vertical-cell.svg', __( 'Vertical border icon', 'document-library-pro' ) ) . esc_html__( 'Vertical', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Width', 'document-library-pro' ),
			],
			$color_size_default,
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'border_vertical_cell' )
		);
		$vertical_border->set_max( 50 );

		$bottom_border = new Color_Size(
			'border_bottom',
			'',
			$this->get_icon( 'bottom-border.svg', __( 'Bottom border icon', 'document-library-pro' ) ) . esc_html__( 'Bottom', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Width', 'document-library-pro' ),
			],
			$color_size_default,
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'border_bottom' )
		);
		$bottom_border->set_max( 50 );

		$header_bg = new Color(
			'header_bg',
			esc_html__( 'Background colors', 'document-library-pro' ),
			esc_html__( 'Header', 'document-library-pro' ),
			'',
			[
				'class' => 'custom-class',
			],
			'',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'header_bg' )
		);

		$body_bg = new Color(
			'body_bg',
			'',
			esc_html__( 'Main', 'document-library-pro' ),
			'',
			[],
			'',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'body_bg' )
		);

		$body_bg_alt = new Color(
			'body_bg_alt',
			'',
			esc_html__( 'Alternative', 'document-library-pro' ),
			'',
			[],
			'',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'body_bg_alt' )
		);

		$button_background = new Color(
			'button_bg',
			'',
			esc_html__( 'Button', 'document-library-pro' ),
			'',
			[],
			'',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'button_bg' )
		);

		$disabled_button_bg = new Color(
			'button_disabled_bg',
			'',
			esc_html__( 'Disabled button', 'document-library-pro' ),
			'',
			[],
			'',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'button_disabled_bg' )
		);

		$button_background_hover = new Color(
			'button_bg_hover',
			'',
			esc_html__( 'Button hover', 'document-library-pro' ),
			'',
			[],
			'',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'button_bg_hover' )
		);

		$header_text = new Color_Size(
			'header_text',
			esc_html__( 'Fonts', 'document-library-pro' ),
			esc_html__( 'Header', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Size', 'document-library-pro' ),
			],
			$color_size_default,
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'header_text' )
		);
		$header_text->set_min( 8 );
		$header_text->set_max( 50 );

		$hyperlink_font = new Color_Size(
			'hyperlink_font',
			'',
			esc_html__( 'Hyperlink', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Size', 'document-library-pro' ),
			],
			$color_size_default,
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'hyperlink_font' )
		);
		$hyperlink_font->set_min( 8 );
		$hyperlink_font->set_max( 50 );

		$button_font = new Color_Size(
			'button_font',
			'',
			esc_html__( 'Button', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Size', 'document-library-pro' ),
			],
			$color_size_default,
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'button_font' )
		);
		$button_font->set_min( 8 );
		$button_font->set_max( 50 );

		$cell_backgrounds = new Radio(
			'cell_backgrounds',
			esc_html__( 'Cell backgrounds', 'document-library-pro' ),
			'',
			'',
			[],
			'no-alternate',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'cell_backgrounds' )
		);

		$cell_backgrounds->set_options(
			[
				[
					'label' => __( 'No alternate', 'document-library-pro' ),
					'value' => 'no-alternate',
				],
				[
					'label' => __( 'Alternate rows', 'document-library-pro' ),
					'value' => 'alternate-rows',
				],
				[
					'label' => __( 'Alternate columns', 'document-library-pro' ),
					'value' => 'alternate-columns',
				],
			]
		);

		$corner_style = new Radio(
			'table_corner_style',
			esc_html__( 'Corner style', 'document-library-pro' ),
			'',
			'',
			[],
			'theme-default',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'table_corner_style' )
		);

		$corner_style->set_options(
			[
				[
					'label' => __( 'Theme default', 'document-library-pro' ),
					'value' => 'theme-default',
				],
				[
					'label' => __( 'Square corners', 'document-library-pro' ),
					'value' => 'square-corners',
				],
				[
					'label' => __( 'Rounded corners', 'document-library-pro' ),
					'value' => 'rounded-corners',
				],
				[
					'label' => __( 'Fully rounded corners', 'document-library-pro' ),
					'value' => 'fully-rounded',
				],
			]
		);

		$disabled_button_font = new Color_Size(
			'disabled_button_font',
			'',
			esc_html__( 'Disabled button', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Size', 'document-library-pro' ),
			],
			$color_size_default,
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'disabled_button_font' )
		);
		$disabled_button_font->set_min( 8 );
		$disabled_button_font->set_max( 50 );

		$body_text = new Color_Size(
			'body_text',
			'',
			esc_html__( 'Main text', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Size', 'document-library-pro' ),
			],
			$color_size_default,
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'body_text' )
		);
		$body_text->set_min( 8 );
		$body_text->set_max( 50 );

		$dropdown_background = new Color(
			'dropdown_background',
			esc_html__( 'Dropdown', 'document-library-pro' ),
			esc_html__( 'Background', 'document-library-pro' ),
			'',
			[],
			'',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'dropdown_background' )
		);

		$dropdown_font = new Color(
			'dropdown_font',
			'',
			esc_html__( 'Font', 'document-library-pro' ),
			'',
			[],
			'',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'dropdown_font' )
		);

		$dropdown_border = new Color_Size(
			'dropdown_border',
			'',
			esc_html__( 'Border', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Width', 'document-library-pro' ),
			],
			$color_size_default,
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'dropdown_border' )
		);
		$dropdown_border->set_max( 50 );

		$spacing = new Select(
			'table_spacing',
			esc_html__( 'Spacing', 'document-library-pro' ),
			'',
			'',
			[],
			'default',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_table_template', 'table_spacing' )
		);

		$spacing->set_options(
			[
				[
					'label' => __( 'Theme default', 'document-library-pro' ),
					'value' => 'default',
				],
				[
					'label' => __( 'Compact', 'document-library-pro' ),
					'value' => 'compact',
				],
				[
					'label' => __( 'Normal', 'document-library-pro' ),
					'value' => 'normal',
				],
				[
					'label' => __( 'Spacious', 'document-library-pro' ),
					'value' => 'spacious',
				],
			]
		);

		$reset_settings = new Button(
			'reset_table_settings',
			esc_html__( 'Reset to the default', 'document-library-pro' ),
			'',
			esc_html__( 'This will reset the design settings to the default of the current template', 'document-library-pro' ),
			[],
			'',
			$custom_design_condition,
		);
		$reset_settings->set_button_data(
			[
				'text' => esc_html__( 'Reset', 'document-library-pro' ),
			]
		);

		$section->add_fields(
			[
				$design,
				$default_table_template,
				$external_border,
				$header_border,
				$horizontal_border,
				$vertical_border,
				$bottom_border,
				$header_bg,
				$body_bg,
				$body_bg_alt,
				$button_background,
				$disabled_button_bg,
				$button_background_hover,
				$header_text,
				$body_text,
				$button_font,
				$disabled_button_font,
				$hyperlink_font,
				$cell_backgrounds,
				$corner_style,
				$dropdown_background,
				$dropdown_font,
				$dropdown_border,
				$spacing,
				$reset_settings,
			]
		);

		return $section;
	}

	/**
	 * Grid design section.
	 *
	 * @return Section
	 */
	public function get_grid_design_section() {
		$color_size_default = [
			'color' => '',
			'size'  => '',
		];

		$custom_design_condition = [
			'rules' => [
				'grid_design' => [
					'op'    => 'eq',
					'value' => 'custom',
				],
			],
		];

		$section     = new Section(
			'grid_design',
			esc_html__( 'Grid', 'document-library-pro' )
		);
		$grid_design = new Radio(
			'grid_design',
			esc_html__( 'Design', 'document-library-pro' ),
			'',
			'',
			[],
			'default'
		);

		$grid_design->set_options(
			[
				[
					'label' => __( 'Default', 'document-library-pro' ),
					'value' => 'default',
				],
				[
					'label' => __( 'Custom', 'document-library-pro' ),
					'value' => 'custom',
				],
			]
		);

		$default_grid_template = new Image_Label(
			'default_grid_template',
			__( 'Default grid template', 'document-library-pro' ),
			'',
			'',
			[],
			'',
			$custom_design_condition
		);

		$default_grid_template->set_options(
			[
				[
					'label' => __( 'Default', 'document-library-pro' ),
					'value' => 'default',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-default-grid-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-default-grid.png' ),
				],
				[
					'label' => __( 'Minimal', 'document-library-pro' ),
					'value' => 'minimal',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-minimal-grid-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-minimal-grid.png' ),
				],
				[
					'label' => __( 'Delicate', 'document-library-pro' ),
					'value' => 'delicate',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-delicate-grid-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-delicate-grid.png' ),
				],
				[
					'label' => __( 'Neutral', 'document-library-pro' ),
					'value' => 'neutral',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-neutral-grid-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-neutral-grid.png' ),
				],
				[
					'label' => __( 'Dark', 'document-library-pro' ),
					'value' => 'dark',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-dark-grid-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-dark-grid.png' ),
				],
				[
					'label' => __( 'Nature', 'document-library-pro' ),
					'value' => 'nature',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-nature-grid-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-nature-grid.png' ),
				],
				[
					'label' => __( 'Blue', 'document-library-pro' ),
					'value' => 'blue',
					'icon'  => Util::get_asset_url( 'images/templates/dlp-blue-grid-icon.png' ),
					'image' => Util::get_asset_url( 'images/templates/dlp-blue-grid.png' ),
				],
			]
		);

		$grid_image_bg = new Color(
			'grid_image_bg',
			esc_html__( 'Background colors', 'document-library-pro' ),
			esc_html__( 'Document image', 'document-library-pro' ),
			'',
			[],
			'#03A0C7',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_grid_template', 'grid_image_bg' )
		);

		$grid_category_bg = new Color(
			'grid_category_bg',
			'',
			esc_html__( 'Category badge', 'document-library-pro' ),
			'',
			[],
			'#03A0C7',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_grid_template', 'grid_category_bg' )
		);

		$button_background       = new Color(
			'grid_button_background',
			'',
			esc_html__( 'Button', 'document-library-pro' ),
			'',
			[],
			'#03A0C7',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_grid_template', 'grid_button_background' )
		);
		$button_background_hover = new Color(
			'grid_button_background_hover',
			'',
			esc_html__( 'Button hover', 'document-library-pro' ),
			'',
			[],
			'#0390b3',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_grid_template', 'grid_button_background_hover' )
		);

		$card_background = new Color(
			'grid_card_background',
			'',
			esc_html__( 'Grid card', 'document-library-pro' ),
			'',
			[],
			'#fff',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_grid_template', 'grid_card_background' )
		);

		$main_text = new Color_Size(
			'grid_body_text',
			esc_html__( 'Fonts', 'document-library-pro' ),
			esc_html__( 'Main text', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Size', 'document-library-pro' ),
			],
			[
				'color' => '#000',
				'size'  => '',
			],
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_grid_template', 'grid_body_text' )
		);
		$main_text->set_min( 8 );
		$main_text->set_max( 50 );

		$hyperlink_font = new Color_Size(
			'grid_hyperlink_font',
			'',
			esc_html__( 'Hyperlink', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Size', 'document-library-pro' ),
			],
			[
				'color' => '#03A0C7',
				'size'  => '',
			],
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_grid_template', 'grid_hyperlink_font' )
		);
		$hyperlink_font->set_min( 8 );
		$hyperlink_font->set_max( 50 );

		$button_text = new Color_Size(
			'grid_button_font',
			'',
			esc_html__( 'Button', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Size', 'document-library-pro' ),
			],
			[
				'color' => '#fff',
				'size'  => '',
			],
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_grid_template', 'grid_button_font' )
		);
		$button_text->set_min( 8 );
		$button_text->set_max( 50 );

		$dropdown_border = new Color_Size(
			'grid_dropdown_border',
			esc_html__( 'Borders', 'document-library-pro' ),
			esc_html__( 'Dropdown', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Width', 'document-library-pro' ),
			],
			[
				'color' => '#000',
				'size'  => 1,
			],
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_grid_template', 'grid_dropdown_border' )
		);
		$dropdown_border->set_max( 50 );

		$button_border = new Color_Size(
			'grid_button_border',
			'',
			esc_html__( 'Button', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Width', 'document-library-pro' ),
			],
			[
				'color' => '#fff',
				'size'  => '',
			],
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_grid_template', 'grid_button_border' )
		);
		$button_border->set_max( 50 );

		$card_border = new Color_Size(
			'grid_card_border',
			'',
			esc_html__( 'Grid card', 'document-library-pro' ),
			'',
			[
				'placeholder' => __( 'Width', 'document-library-pro' ),
			],
			[
				'color' => '#DDDDDD',
				'size'  => 1,
			],
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_grid_template', 'grid_card_border' )
		);
		$card_border->set_max( 50 );

		$grid_corner_style = new Radio(
			'grid_corner_style',
			esc_html__( 'Corner style', 'document-library-pro' ),
			'',
			'',
			[],
			'theme-default',
			$custom_design_condition,
			Template_Defaults::get_field_effects( 'default_grid_template', 'grid_corner_style' )
		);

		$grid_corner_style->set_options(
			[
				[
					'label' => __( 'Default', 'document-library-pro' ),
					'value' => 'theme-default',
				],
				[
					'label' => __( 'Square corners', 'document-library-pro' ),
					'value' => 'square-corners',
				],
				[
					'label' => __( 'Rounded corners', 'document-library-pro' ),
					'value' => 'rounded-corners',
				],
				[
					'label' => __( 'Fully rounded', 'document-library-pro' ),
					'value' => 'fully-rounded',
				],
			]
		);

		$reset_settings = new Button(
			'reset_grid_settings',
			esc_html__( 'Reset to the default', 'document-library-pro' ),
			'',
			esc_html__( 'This will reset the design settings to the default of the current template', 'document-library-pro' ),
			[],
			'',
			$custom_design_condition,
		);
		$reset_settings->set_button_data(
			[
				'text' => esc_html__( 'Reset', 'document-library-pro' ),
			]
		);

		$section->add_fields(
			[
				$grid_design,
				$default_grid_template,
				$grid_image_bg,
				$button_background,
				$button_background_hover,
				$card_background,
				$grid_category_bg,
				$main_text,
				$hyperlink_font,
				$button_text,
				$dropdown_border,
				$card_border,
				$button_border,
				$grid_corner_style,
				$reset_settings,
			]
		);

		return $section;
	}

	/**
	 * Folder design section
	 */
	public function get_folder_design_section() {
		$folder_design = new Radio(
			'folder_design',
			esc_html__( 'Design', 'document-library-pro' ),
			'',
			'',
			[],
			'default'
		);

		$folder_design->set_options(
			[
				[
					'label' => __( 'Default', 'document-library-pro' ),
					'value' => 'default',
				],
				[
					'label' => __( 'Custom', 'document-library-pro' ),
					'value' => 'custom',
				],
			]
		);

		$custom_design_condition = [
			'rules' => [
				'folder_design' => [
					'op'    => 'eq',
					'value' => 'custom',
				],
			],
		];

		$section = new Section(
			'folder_design',
			esc_html__( 'Folders', 'document-library-pro' )
		);

		$top_folder_color = new Color(
			'folder_icon_color',
			esc_html__( 'Top-level Folder color', 'document-library-pro' ),
			esc_html__( 'Change the color of the top-level folder icons.', 'document-library-pro' ),
			'',
			[],
			'#FFB608',
			$custom_design_condition
		);

		$sub_folder_color = new Color(
			'folder_icon_subcolor',
			esc_html__( 'Sub-level folder color', 'document-library-pro' ),
			esc_html__( 'Change the color of the sub-level folder icons.', 'document-library-pro' ),
			'',
			[],
			'#000',
			$custom_design_condition
		);

		$folder_icon_svg_closed = new Textarea(
			'folder_icon_svg_closed',
			esc_html__( 'Closed folder icon', 'document-library-pro' ),
			sprintf(
				/* translators: %1: knowledge base link start, %2: knowledge base link end */
				esc_html__( 'Input the SVG code of the icon you want to use for closed folders. %1$sRead more%2$s.', 'document-library-pro' ),
				Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-folders/#adding-your-own-folder-icon' ), true ),
				'</a>'
			),
			'',
			[
				'sanitize' => false,
			],
			$this->defaults['folder_icon_svg_closed'],
			$custom_design_condition
		);

		$folder_icon_svg_open = new Textarea(
			'folder_icon_svg_open',
			esc_html__( 'Open folder icon', 'document-library-pro' ),
			sprintf(
				/* translators: %1: knowledge base link start, %2: knowledge base link end */
				esc_html__( 'Input the SVG code of the icon you want to use for open folders. %1$sRead more%2$s.', 'document-library-pro' ),
				Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-folders/#adding-your-own-folder-icon' ), true ),
				'</a>'
			),
			'',
			[
				'sanitize' => false,
			],
			$this->defaults['folder_icon_svg_open'],
			$custom_design_condition
		);

		$section->add_fields(
			[
				$folder_design,
				$top_folder_color,
				$sub_folder_color,
				$folder_icon_svg_closed,
				$folder_icon_svg_open,
			]
		);

		return $section;
	}

	private function get_icon( $icon, $alt_text = '' ) {
		return sprintf(
			'<img src="%1$s" alt="%2$s" width="22" height="22" class="icon" />',
			Util::get_asset_url( 'images/' . ltrim( $icon, '/' ) ),
			$alt_text
		);
	}
}
