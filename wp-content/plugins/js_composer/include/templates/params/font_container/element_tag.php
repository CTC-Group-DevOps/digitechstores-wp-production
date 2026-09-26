<?php
/**
 * Font container param element_tag attribute template.
 *
 * @var array $fields
 * @var array $options
 * @var string $param_id
 * @var array $settings
 * @var string $edit_field_class
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<div class="vc_column <?php echo esc_attr( $edit_field_class ); ?>">
	<div class="wpb-param-heading">
		<label for="<?php echo esc_attr( wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'element_tag' ) ); ?>" class="wpb_element_label">
			<?php echo esc_html__( 'Element tag', 'js_composer' ); ?>
		</label>
		<?php
		if ( isset( $fields['tag_description'] ) && strlen( $fields['tag_description'] ) > 0 ) :
			vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => $fields['tag_description'] ] );
		endif;
		?>
	</div>
	<div class="vc_font_container_form_field-tag-container">
		<?php
		WPB_Form_Field_Dropdown::render( [
			'classes' => 'vc_font_container_form_field-tag-select',
			'options' => $options,
			'id' => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'element_tag' ),
		]);
		?>
	</div>
</div>

