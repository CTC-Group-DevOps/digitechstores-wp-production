<?php
/**
 * Font container param color attribute template.
 *
 * @since 9.0
 * @var array $fields
 * @var array $values
 * @var string $param_id
 * @var array $settings
 * @var string $edit_field_class
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>

<div class="vc_column <?php echo esc_attr( $edit_field_class ); ?>">
	<div class="vc_wrapper-param-type-colorpicker wpb_el_type_colorpicker">
	<div class="wpb-param-heading">
		<label for="<?php echo esc_attr( wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'color' ) ); ?>" class="wpb_element_label">
			<?php echo esc_html__( 'Color', 'js_composer' ); ?>
		</label>
		<?php
		if ( isset( $fields['color_description'] ) && strlen( $fields['color_description'] ) > 0 ) :
			vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => $fields['color_description'] ] );
		endif;
		?>
	</div>
	<div class="vc_font_container_form_field-color-container">
		<?php
		WPB_Form_Field_Colorpicker::render(
			[
				'id'                => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'color' ),
				'classes'           => 'vc_font_container_form_field-color-input',
				'value'             => $values['color'],
				'data_attributes'    => [
					'default-colorpicker-color' => $fields['default_colorpicker_color'],
				],
			],
		);
		?>
	</div>
	</div>
</div>
