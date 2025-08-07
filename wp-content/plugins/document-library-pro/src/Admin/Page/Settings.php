<?php
namespace Barn2\Plugin\Document_Library_Pro\Admin\Page;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Licensed_Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles our plugin settings page in the admin.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Settings implements Standard_Service, Registerable, Conditional {
	const MENU_SLUG = 'document_library_pro';

	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param Licensed_Plugin $plugin
	 */
	public function __construct( Licensed_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Util::is_admin();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
	}


	/**
	 * Register the Settings submenu page.
	 */
	public function add_settings_page() {
		add_submenu_page(
			'document_library_pro',
			__( 'Document Library Settings', 'document-library-pro' ),
			__( 'Settings', 'document-library-pro' ),
			'manage_options',
			'document_library_pro',
			[ $this, 'render_settings_page' ],
			10
		);
	}

	/**
	 * Render the Settings page.
	 */
	public function render_settings_page() {
		?>
		<div class='woocommerce-layout__header'>
			<div class="woocommerce-layout__header-wrapper">
				<h3 class='woocommerce-layout__header-heading'>
					<?php esc_html_e( 'Document Library Pro', 'document-library-pro' ); ?>
				</h3>
				<div class="links-area">
					<?php $this->support_links(); ?>
				</div>
			</div>
		</div>

		<div class="wrap barn2-settings">
			<div class="barn2-settings-inner">

			<?php
			$settings_manager = $this->plugin->get_service('settings_controller')->get_manager();
			$settings_manager->register_and_enqueue_assets();
			$settings_manager->render_settings();
			?>

			</div>
		</div>
		<?php
	}

	/**
	 * Support links for the settings page.
	 *
	 * @return void
	 */
	public function support_links(): void {
		printf(
			'<p>%s | %s | %s</p>',
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			Util::format_link( $this->plugin->get_documentation_url(), __( 'Documentation', 'document-library-pro' ), true ),
			Util::format_link( $this->plugin->get_support_url(), __( 'Support', 'document-library-pro' ), true ),
			sprintf(
				'<a class="barn2-wiz-restart-btn" href="%s">%s</a>',
				add_query_arg( [ 'page' => $this->plugin->get_slug() . '-setup-wizard' ], admin_url( 'admin.php' ) ),
				__( 'Setup wizard', 'document-library-pro' )
			)
            // phpcs:enable
		);
	}
}
