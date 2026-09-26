<?php
/**
 * Template for element param google fonts.
 *
 * @var array $settings
 * @var string $value
 * @var array $fields
 * @var array $values
 * @var array $param_id
 * @var array $font_style_options
 * @var Vc_Google_Fonts $this
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
vc_include_template( 'params/font_container/font_family.php', [
	'dropdown_atts' => [
		'id' => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'font_family' ),
		'classes' => 'vc_google_fonts_form_field-font_family-select',
		'default-font-style' => $values['font_style'],
		'options' => $font_style_options,
	],
	'fields' => $fields,
	'param_id' => $param_id,
	'settings' => $settings,
	'container_class' => 'vc_google_fonts_form_field-font_family-container',
	'edit_field_class' => $fields['font_family_edit_field_class'] ?? '',
])
?>

<?php if ( isset( $fields['no_font_style'] ) && false === $fields['no_font_style'] || ! isset( $fields['no_font_style'] ) ) : ?>
	<div class="vc_column <?php echo esc_attr( $fields['font_style_edit_field_class'] ?? '' ); ?>">
		<div class="wpb-param-heading">
			<label for="<?php echo esc_attr( wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'font_style' ) ); ?>" class="wpb_element_label">
				<?php esc_html_e( 'Font style', 'js_composer' ); ?>
			</label>
			<?php
			if ( isset( $fields['font_style_description'] ) && strlen( $fields['font_style_description'] ) > 0 ) :
				vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => $fields['font_style_description'] ] );
			endif;
			?>
		</div>
		<div class="vc_google_fonts_form_field-font_style-container">
			<?php
			WPB_Form_Field_Dropdown::render([
				'id' => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'font_style' ),
				'classes' => 'vc_google_fonts_form_field-font_style-select',
			]);
			?>
		</div>
	</div>
<?php endif ?>

<div class="vc_row-fluid vc_column vc_google_fonts_form_field-preview-wrapper">
	<div class="wpb_element_label"><?php esc_html_e( 'Font preview', 'js_composer' ); ?>:</div>
	<div class="vc_google_fonts_form_field-preview-container">
		<span><?php esc_html_e( 'Grumpy wizards make toxic brew for the evil Queen and Jack.', 'js_composer' ); ?></span>
	</div>
	<div class="vc_google_fonts_form_field-status-container"><span></span></div>
</div>

<input name="<?php echo esc_attr( $settings['param_name'] ); ?>"
		class="wpb_vc_param_value  <?php echo esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ); ?>_field" type="hidden"
		value="<?php echo esc_attr( $value ); ?>"/>
