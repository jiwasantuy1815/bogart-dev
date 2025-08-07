<?php
namespace Barn2\Plugin\Document_Library_Pro\Admin\Page;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles our plugin protect page in the admin.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Protect implements Standard_Service, Registerable, Conditional {

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
		return Lib_Util::is_admin();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'add_protect_page' ] );
	}

	/**
	 * Add the Import sub menu page.
	 */
	public function add_protect_page() {
        if ( Lib_Util::is_plugin_installed( 'password-protected-categories/password-protected-categories.php' ) ) {
			return;
		}

		add_submenu_page(
			'document_library_pro',
			__( 'Protect', 'document-library-pro' ),
			__( 'Protect', 'document-library-pro' ),
			'manage_options',
			'dlp_protect',
			[ $this, 'render' ],
			12
		);
	}

	/**
	 * Render the import page.
	 */
	public function render() {
		?>
		<div class="wrap dlw-settings">
			<h1><?php esc_html_e( 'Create private document libraries with Password Protected Categories', 'document-library-pro' ); ?></h1>

			<?php
			printf(
				'<div class="promo-wrapper">
					<p class="promo">' .
						/* translators: %1: Document Library Pro link start, %2: Document Library Pro link end */
						esc_html__( 'Do you need to control who can access your documents? You can easily do this with the %1$sPassword Protected Categories%2$s plugin', 'document-library-pro' ) .
					'</p>
					<p class="promo">' .
						esc_html__( 'Password Protected Categories lets you restrict access to any or all of your document categories - either to specific users, roles, or to anyone with the password.', 'document-library-pro' ) .
					'</p>
					<a class="promo" href="%3$s" target="_blank"><img class="promo" src="%4$s" /></a>
				</div>',
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				Lib_Util::format_link_open( Lib_Util::barn2_url( 'wordpress-plugins/password-protected-categories/?utm_source=settings&utm_medium=settings&utm_campaign=upsellpg&utm_content=dlp-ppc' ), true ),
				'</a>',
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				Lib_Util::barn2_url( 'wordpress-plugins/password-protected-categories/?utm_source=settings&utm_medium=settings&utm_campaign=upsellpg&utm_content=dlp-ppc' ),
				esc_url( $this->plugin->get_dir_url() . 'assets/images/promo-protect.png' )
			);
			?>

		<?php
	}

}
