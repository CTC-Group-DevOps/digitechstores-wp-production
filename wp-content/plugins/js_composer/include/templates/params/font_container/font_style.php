<?php
/**
 * Font container param font_style attribute template.
 *
 * @var array $fields
 * @var string $param_id
 * @var array $values
 * @var string $edit_field_class
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>

<div class="vc_column <?php echo esc_attr( $edit_field_class ); ?>">
	<div class="wpb-param-heading">
		<div class="wpb_element_label">
			<?php echo esc_html__( 'Font style', 'js_composer' ); ?>
		</div>
		<?php
		if ( isset( $fields['font_style_description'] ) && strlen( $fields['font_style_description'] ) > 0 ) :
			vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => $fields['font_style_description'] ] );
		endif;
		?>
	</div>
	<div class="vc_font_container_form_field-font_style-container wpb_checkbox-container wpb_checkbox-container-vertical" role="group" aria-label="<?php echo esc_html__( 'Font style', 'js_composer' ); ?>">
		<?php
		WPB_Form_Field_Checkbox::render( [
			'id'               => 'wpb-font-style-italic-' . $param_id,
			'value'            => 'italic',
			'checked'          => '1' === $values['font_style_italic'],
			'label'            => esc_html__( 'italic', 'js_composer' ),
			'input_attr_class' => 'class="vc_font_container_form_field-font_style-checkbox italic"',
		] );
		?>
		<?php
		WPB_Form_Field_Checkbox::render( [
			'id'               => 'wpb-font-style-bold-' . $param_id,
			'value'            => 'bold',
			'checked'          => '1' === $values['font_style_bold'],
			'label'            => esc_html__( 'bold', 'js_composer' ),
			'input_attr_class' => 'class="vc_font_container_form_field-font_style-checkbox bold"',
		] );
		?>
	</div>
</div>
