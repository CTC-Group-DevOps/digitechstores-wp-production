<?php
/**
 * Menu section in page settings panel template.
 *
 * @since 8.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="vc_col-sm-12 vc_column" id="vc_settings-post_menu">
	<a class="vc_general vc_ui-button vc_ui-button-action vc_ui-button-shape-rounded" href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Manage Menu', 'js_composer' ); ?></a>
</div>
