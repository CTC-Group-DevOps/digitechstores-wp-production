<?php
/**
 * Template for element param css_editor.
 *
 * @var WPBakeryCssEditor $css_editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="vc_css-editor" data-css-editor="true">
	<?php
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $css_editor->onionLayout(); // nosemgrep - we already escaped everything on this step.
	?>
	<div class="vc_settings">
		<div class="vc_settings-row">
			<div class="wpb-color-picker-wrapper">
				<?php WPB_Form_Field_Colorpicker::render( [ 'name' => 'background_color' ] ); ?>
				<label class="wpb_element_label"><?php esc_html_e( 'Background color', 'js_composer' ); ?></label>
			</div>
		</div>
		<div class="vc_settings-row">
			<div class="wpb-color-picker-wrapper wpb_border-color">
				<?php WPB_Form_Field_Colorpicker::render( [ 'name' => 'border_color' ] ); ?>
				<label class="wpb_element_label"><?php esc_html_e( 'Border color', 'js_composer' ); ?></label>
			</div>
			<div class="vc_border-style">
				<label for="<?php echo esc_attr( $css_editor->get_field_id( 'border-style' ) ); ?>"><?php esc_html_e( 'Border style', 'js_composer' ); ?></label>
				<?php
				WPB_Form_Field_Dropdown::render( [
					'id'      => $css_editor->get_field_id( 'border-style' ),
					'name'    => 'border_style',
					'classes' => 'vc_border-style',
					'options' => $css_editor->get_border_style_options(),
				] );
				?>
			</div>
		</div>
		<div class="vc_settings-row">
			<div class="vc_background-image">
				<label><?php esc_html_e( 'Image', 'js_composer' ); ?></label>
				<?php
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $css_editor->get_background_image_control(); // nosemgrep.
				?>
			</div>
			<div class="vc_background-style">
				<label for="<?php echo esc_attr( $css_editor->get_field_id( 'background-style' ) ); ?>"><?php esc_html_e( 'Background style', 'js_composer' ); ?></label>
				<?php
				WPB_Form_Field_Dropdown::render( [
					'id'      => $css_editor->get_field_id( 'background-style' ),
					'name'    => 'background_style',
					'classes' => 'vc_background-style',
					'options' => $css_editor->get_background_style_options(),
				] );
				?>
			</div>
		</div>
	</div>
	<?php
	WPB_Form_Field_Hidden::render( [
		'name'    => $css_editor->setting( 'param_name' ),
		'classes' => wpbakery()->editForm()->get_value_control_classes( $css_editor->setting( 'param_name' ), $css_editor->setting( 'type' ) . '_field' ),
		'value'   => $css_editor->value(),
	] );
	?>
</div>
<div class="vc_clearfix"></div>
