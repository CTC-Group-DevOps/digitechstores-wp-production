<?php
/**
 * Font container param text_align attribute template.
 *
 * @var string $param_id
 * @var array $settings
 * @var array $fields
 * @var string $value
 * @var string $edit_field_class
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>

<div class="vc_column <?php echo esc_attr( $edit_field_class ); ?>">
	<div class="wpb-param-heading">
		<label for="<?php echo esc_attr( wpbakery()->editForm()->get_value_control_id( $param_id, 'button_group' ) . '_0' ); ?>" class="wpb_element_label">
			<?php echo esc_html__( 'Alignment', 'js_composer' ); ?>
		</label>
		<?php
		if ( isset( $fields['text_align_description'] ) && strlen( $fields['text_align_description'] ) > 0 ) :
			vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => $fields['text_align_description'] ] );
		endif;
		?>
	</div>
	<div class="vc_font_container_form_field-text_align-container">
		<?php
		$input_classes = wpbakery()->editForm()->get_value_control_classes( 'text_align', 'button_group' );
		$input_id = wpbakery()->editForm()->get_value_control_id( $param_id, 'button_group' );

		WPB_Form_Field_Button_Group::render(
			[
				'id' => $input_id,
				'name' => 'text_align',
				'input_classes' => $input_classes,
				'class' => 'vc_font_container_form_field-text_align-select',
				'heading' => esc_html__( 'Text align', 'js_composer' ),
				'options' => vc_config()->get_text_align_param_value(),
				'current_value' => $value,
			]
		);
		?>
	</div>
</div>
