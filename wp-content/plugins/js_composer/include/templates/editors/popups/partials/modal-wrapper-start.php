<?php
/**
 * Start of wrapper for modals.
 *
 * @since 9.0
 *
 * @var string $id
 * @var string|null $wrapper_classes
 * @var string|null $inner_classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div id="vc_ui-panel-<?php echo esc_attr( $id ); ?>"
	class="vc_ui-font-open-sans vc_ui-panel-window vc_media-xs vc_ui-panel <?php echo isset( $wrapper_classes ) ? esc_attr( $wrapper_classes ) : ''; ?>"
	data-vc-panel=".vc_ui-panel-header-header"
	data-vc-ui-element="panel-<?php echo esc_attr( $id ); ?>"
	role="dialog"
	aria-modal="true"
	aria-labelledby="<?php echo esc_attr( $id ); ?>-title"
>
	<div class="vc_ui-panel-window-inner <?php echo isset( $inner_classes ) ? esc_attr( $inner_classes ) : ''; ?>">
