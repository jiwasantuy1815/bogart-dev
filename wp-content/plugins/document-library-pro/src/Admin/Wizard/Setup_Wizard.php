<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Wizard;

use Barn2\Plugin\Document_Library_Pro\Admin\Wizard\Steps;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License\EDD_Licensing;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License\Plugin_License;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Licensed_Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Setup_Wizard as Wizard;

/**
 * Main Setup Wizard Loader
 *
 * @package   Barn2\document-library-advanced
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Setup_Wizard implements Registerable, Standard_Service {

	private $plugin;
	private $wizard;

	/**
	 * Constructor.
	 *
	 * @param Licensed_Plugin $plugin
	 */
	public function __construct( Licensed_Plugin $plugin ) {
		$this->plugin = $plugin;

		$steps = [
			new Steps\License_Verification(),
			new Steps\Layout(),
			new Steps\Table(),
			new Steps\Grid(),
			new Steps\Filters(),
			new Steps\Upsell(),
			new Steps\Completed(),
		];

		$this->wizard = new Wizard( $this->plugin, $steps );
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		$this->configure();
		$this->wizard->boot();
	}

	/**
	 * Configure the wizard.
	 */
	private function configure() {
		$this->wizard->configure(
			[
				'skip_url'        => admin_url( 'admin.php?page=document_library_pro' ),
				'license_tooltip' => esc_html__( 'The licence key is contained in your order confirmation email.', 'document-library-pro' ),
				'utm_id'          => 'dlp',
				'signpost'        => [
					[
						'title' => __( 'Create a document', 'document-library-pro' ),
						'href'  => admin_url( 'post-new.php?post_type=dlp_document' ),
					],
					[
						'title' => __( 'Import documents by drag and drop or CSV', 'document-library-pro' ),
						'href'  => admin_url( 'admin.php?page=dlp_import' ),
					],
					[
						'title' => __( 'Go to settings page', 'document-library-pro' ),
						'href'  => admin_url( 'admin.php?page=document_library_pro' ),
					],
					[
						'title' => __( 'Customize design', 'document-library-pro' ),
						'href'  => admin_url( 'admin.php?page=document_library_pro#/design' ),
					],
				],
			]
		);

		$this->wizard->add_edd_api( EDD_Licensing::class );
		$this->wizard->add_license_class( Plugin_License::class );
		$this->wizard->add_restart_link( '', '' );

		$this->wizard->add_custom_asset(
			$this->plugin->get_dir_url() . 'assets/js/admin/dlp-wizard-custom.js',
			Lib_Util::get_script_dependencies( $this->plugin, 'admin/dlp-wizard-custom.js' )
		);
	}
}
