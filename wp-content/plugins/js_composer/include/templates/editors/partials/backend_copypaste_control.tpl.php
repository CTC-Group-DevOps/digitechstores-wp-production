<?php
/**
 * Backend editor copy/paste control template.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<span class="vc_control-btn vc_control-btn-copypaste" title="<?php echo esc_attr__( 'Copy and paste', 'js_composer' ); ?>">
	<select class="vc_copypaste-select" data-placeholder="<?php echo esc_attr__( 'Copy and paste', 'js_composer' ); ?>">
		<option></option>
		<option value="copy" title="<?php echo esc_attr__( 'Copy element', 'js_composer' ); ?>"><?php echo esc_html__( 'Copy element', 'js_composer' ); ?></option>
		<option value="paste" title="<?php echo esc_attr__( 'Paste element', 'js_composer' ); ?>"><?php echo esc_html__( 'Paste element', 'js_composer' ); ?></option>
	</select>
</span>
