<?php
/**
 * Font container param font_size attribute template.
 *
 * @var array $fields
 * @var array $options
 * @var string $param_id
 * @var array $settings
 * @var array $values
 * @var string $edit_field_class
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>

<div class="vc_column <?php echo esc_attr( $edit_field_class ); ?>">
	<div class="wpb-param-heading">
		<label for="<?php echo esc_attr( wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'font_size' ) ); ?>" class="wpb_element_label">
			<?php echo esc_html__( 'Font size', 'js_composer' ); ?>
		</label>
		<?php
		if ( isset( $fields['font_size_description'] ) && strlen( $fields['font_size_description'] ) > 0 ) :
			vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => $fields['font_size_description'] ] );
		endif;
		?>
	</div>
	<div class="vc_font_container_form_field-font_size-container wpb-inner-number">
		<?php
		$unit_data = WPB_Unit_Option::parse_value( $values['font_size'], vc_get_shared( 'css-units' ) );
		WPB_Form_Field_Number::render( [
			'id'            => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'font_size' ),
			'classes'       => 'vc_font_container_form_field-font_size-input',
			'value'         => $unit_data['value'],
			'units'         => $unit_data['units'],
			'selected_unit' => $unit_data['selected_unit'],
			'min'           => 0,
		] );
		?>
	</div>
</div>
