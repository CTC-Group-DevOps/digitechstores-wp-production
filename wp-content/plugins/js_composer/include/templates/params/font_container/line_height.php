<?php
/**
 * Font container param line_height attribute template.
 *
 * @var array $fields
 * @var array $options
 * @var string $param_id
 * @var array $settings
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
		<label for="<?php echo esc_attr( wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'line_height' ) ); ?>" class="wpb_element_label">
			<?php echo esc_html__( 'Line height', 'js_composer' ); ?>
		</label>
		<?php
		if ( isset( $fields['line_height_description'] ) && strlen( $fields['line_height_description'] ) > 0 ) :
			vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => $fields['line_height_description'] ] );
		endif;
		?>
	</div>
	<div class="vc_font_container_form_field-line_height-container wpb-inner-number">
		<?php
		$unit_data = WPB_Unit_Option::parse_value( $values['line_height'], array_merge( [ 'None' ], vc_get_shared( 'css-units' ) ) );
		WPB_Form_Field_Number::render( [
			'id'            => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'line_height' ),
			'classes'       => 'vc_font_container_form_field-line_height-input',
			'value'         => $unit_data['value'],
			'units'         => $unit_data['units'],
			'selected_unit' => $unit_data['selected_unit'],
			'min'           => 0.1,
			'step'          => 0.1,
		] );
		?>
	</div>
</div>
