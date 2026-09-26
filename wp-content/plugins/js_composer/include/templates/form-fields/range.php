<?php
/**
 * Range form field template.
 *
 * @var string $name     Field name attribute.
 * @var string $value    Field value.
 * @var string $min      Minimum value.
 * @var string $max      Maximum value.
 * @var string $step     Step increment.
 * @var string $placeholder     Step increment.
 * @var string $unit     Unit label (e.g., 'px', '%').
 * @var array  $settings Element param settings array.
 * @var string $param_id Element param id.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<div class="wpb-range-container">
	<?php
	if ( ! empty( $unit ) && is_string( $unit ) ) :
		vc_include_template( 'editors/partials/param-unit-selector.tpl.php', [
			'units'         => [ $unit ],
			'selected_unit' => $unit,
		] );
	endif;
	?>
	<input
		name="<?php echo esc_attr( $name ); ?>-range"
		type="range"
		value="<?php echo esc_attr( $value ); ?>"
		<?php echo '' !== $step ? 'step="' . esc_attr( $step ) . '"' : ''; ?>
		<?php echo '' !== $min ? 'min="' . esc_attr( $min ) . '"' : ''; ?>
		<?php echo '' !== $max ? 'max="' . esc_attr( $max ) . '"' : ''; ?>
	/>
	<?php
	vc_include_template( 'form-fields/number/input.php', [
		'value'    => $value,
		'id'       => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
		'classes'  => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'] ),
		'name'     => $name,
		'min'      => $min,
		'max'      => $max,
		'step'     => $step,
		'placeholder'     => $placeholder,
	] );
	vc_include_template( 'form-fields/number/arrows.php' );
	?>
</div>
