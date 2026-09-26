<?php
/**
 * Control button template for popup headers.
 *
 * @since 9.0
 *
 * @var string $control_slug
 * @var string $title
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<button type="button" class="vc_general vc_ui-control-button vc_ui-<?php echo esc_attr( $control_slug ); ?>-button" data-vc-ui-element="button-<?php echo esc_attr( $control_slug ); ?>" title="<?php echo esc_attr( $title ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
	<i class="vc-composer-icon vc-c-icon-<?php echo esc_attr( $control_slug ); ?>"></i>
</button>
