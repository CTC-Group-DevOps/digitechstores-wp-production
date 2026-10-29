<?php
/**
 * Floating Blocks Main Class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Floating_Blocks;

use XTS\Singleton;

/**
 * Floating Blocks Main Class.
 */
class Main extends Singleton {
	/**
	 * Constructor.
	 */
	public function init() {
		add_action( 'init', array( $this, 'include_files' ), 10 );

		add_action( 'elementor/documents/register', array( $this, 'register_elementor_document_types' ) );
	}

	/**
	 * Register Elementor document types for floating blocks and popups.
	 *
	 * @param \Elementor\Core\Documents_Manager $documents_manager Elementor documents manager.
	 */
	public function register_elementor_document_types( $documents_manager ) {
		if ( ! woodmart_is_elementor_installed() ) {
			return;
		}

		$dir = WOODMART_THEMEROOT . '/inc/modules/floating-blocks/';

		require_once $dir . 'integrations/elementor/class-fb-doc.php';
		require_once $dir . 'integrations/elementor/class-popup-doc.php';

		$documents_manager->register_document_type( 'wd_floating_block', Integrations\Fb_Document::class );
		$documents_manager->register_document_type( 'wd_popup', Integrations\Popup_Document::class );

		add_action( 'elementor/editor/before_enqueue_scripts', 'woodmart_enqueue_admin_scripts' );
	}

	/**
	 * Include required admin files.
	 */
	public function include_files() {
		$dir = WOODMART_THEMEROOT . '/inc/modules/floating-blocks/';

		require_once $dir . '/class-manager.php';

		// WPBakery.
		if ( defined( 'WPB_VC_VERSION' ) && ! isset( $_GET['vcv-gutenberg-editor'] ) ) { // phpcs:ignore.
			require_once $dir . '/integrations/wpb/class-fb.php';
			require_once $dir . '/integrations/wpb/class-popup.php';
		}

		if ( woodmart_is_gutenberg_blocks_enabled() ) {
			require_once $dir . '/integrations/gutenberg/class-popup.php';
			require_once $dir . '/integrations/gutenberg/class-fb.php';
		}

		require_once $dir . '/class-import.php';
		require_once $dir . '/class-admin.php';
		require_once $dir . '/class-frontend.php';
	}
}

Main::get_instance();
