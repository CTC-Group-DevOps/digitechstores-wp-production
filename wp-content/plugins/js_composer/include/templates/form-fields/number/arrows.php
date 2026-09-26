<?php
/**
 * Template for arrows element param number.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="vc_param_type_number_arrows">
	<button type="button" class="vc_param_type_number_chevron" aria-label="<?php esc_html_e( 'Increase value', 'js_composer' ); ?>">
		<i class="vc-composer-icon vc-c-number-arrow-up"></i>
	</button>
	<button type="button" class="vc_param_type_number_chevron" aria-label="<?php esc_html_e( 'Decrease value', 'js_composer' ); ?>">
		<i class="vc-composer-icon vc-c-number-arrow-down"></i>
	</button>
</div>
