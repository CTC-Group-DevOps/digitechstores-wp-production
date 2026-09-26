<?php
/**
 * Font container param font_family attribute template.
 *
 * @var array $fields
 * @var string $param_id
 * @var array $settings
 * @var array $dropdown_atts
 * @var array $container_class
 * @var string $edit_field_class
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>

<div class="vc_column <?php echo esc_attr( $edit_field_class ); ?>">
	<div class="wpb-param-heading">
		<label for="<?php echo esc_attr( wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'font_family' ) ); ?>" class="wpb_element_label">
			<?php echo esc_html__( 'Font family', 'js_composer' ); ?>
		</label>
		<?php
		if ( isset( $fields['font_family_description'] ) && strlen( $fields['font_family_description'] ) > 0 ) :
			vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => $fields['font_family_description'] ] );
		endif;
		?>
	</div>
	<div class="<?php echo esc_attr( $container_class ); ?>">
		<?php
		WPB_Form_Field_Dropdown::render( $dropdown_atts );
		?>
	</div>
</div>
