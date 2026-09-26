<?php
/**
 * Template for element param number.
 *
 * @var string $value
 * @var string $id
 * @var string $classes
 * @var string $name
 * @var string $placeholder
 * @var string $min
 * @var string $max
 * @var string $step
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="vc_param_type_number_inner_wrapper">
	<?php
	if ( ! empty( $units ) && is_array( $units ) ) :
		vc_include_template( 'editors/partials/param-unit-selector.tpl.php', [
			'units'         => $units,
			'selected_unit' => ! empty( $selected_unit ) ? $selected_unit : $units[0],
		] );
	endif;

	vc_include_template( 'form-fields/number/input.php', [
		'value'    => $value,
		'id'       => $id,
		'classes'  => $classes,
		'name'     => $name,
		'placeholder' => $placeholder,
		'min'      => $min,
		'max'      => $max,
		'step'     => $step,
	] );

	vc_include_template( 'form-fields/number/arrows.php' );
	?>
</div>
