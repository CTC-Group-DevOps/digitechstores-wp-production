<?php
/**
 * Template for linked_fields parameter type.
 *
 * @var array  $settings
 * @var string $param_id
 * @var string $value
 * @var string $class
 * @var string $heading
 * @var array  $parsed_value
 * @var bool   $is_linked
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="wpb_linked_fields_container <?php echo esc_attr( $class ); ?>" role="group"<?php echo $heading ? ' aria-label="' . esc_attr( $heading ) . '"' : ''; ?>>
	<input type="hidden"
		<?php wpbakery()->editForm()->output_value_control_attr_class( $settings['param_name'], $settings['type'] ); ?>
		name="<?php echo esc_attr( $settings['param_name'] ); ?>"
		value="<?php echo esc_attr( $value ); ?>" />
	<?php
	if ( ! empty( $settings['settings']['units'] ) && is_array( $settings['settings']['units'] ) ) :
		$unit_selected = in_array( $parsed_value['unit'] ?? '', $settings['settings']['units'], true ) ? $parsed_value['unit'] : $settings['settings']['units'][0];
		vc_include_template( 'editors/partials/param-unit-selector.tpl.php', [
			'units'         => $settings['settings']['units'],
			'selected_unit' => $unit_selected,
		] );
	endif;

	$main_id = wpbakery()->editForm()->get_value_control_id( $settings['type'], $param_id );
	$index = 0;
	foreach ( $settings['value'] as $key => $label ) :
		$input_id = $main_id . '_' . $index;
		$index++;
		?>
		<div class="wpb_linked_field_item">
			<input type="number"
				id="<?php echo esc_attr( $input_id ); ?>"
				class="wpb_linked_field_input wpb-form-input"
				data-name="<?php echo esc_attr( $key ); ?>"
				value="<?php echo esc_attr( $parsed_value[ $key ] ); ?>"
			/>
			<?php if ( ! empty( $label ) ) : ?>
				<label for="<?php echo esc_attr( $input_id ); ?>" class="wpb_linked_field_label"><?php echo esc_html( $label ); ?></label>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
	<div class="wpb_linked_fields_control">
		<button type="button"
			class="wpb_linked_fields_toggle <?php echo $is_linked ? 'linked' : ''; ?>"
			title="<?php echo esc_attr__( 'Link values toggle', 'js_composer' ); ?>"
			aria-pressed="<?php echo $is_linked ? 'true' : 'false'; ?>"
			aria-label="<?php echo esc_attr__( 'Link values', 'js_composer' ); ?>">
			<i class="vc-composer-icon vc-c-linked" aria-hidden="true"></i>
			<i class="vc-composer-icon vc-c-not-linked" aria-hidden="true"></i>
		</button>
	</div>
</div>
