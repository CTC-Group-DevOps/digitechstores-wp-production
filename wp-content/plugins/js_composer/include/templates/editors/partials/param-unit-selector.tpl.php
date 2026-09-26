<?php
/**
 * Unit selector template.
 *
 * @var array  $units             Array of unit strings (e.g., ['px', 'em', 'rem']).
 * @var string $selected_unit     Currently selected unit.
 * @var array  $data_attributes   Optional data attributes (e.g., ['layer' => 'margin']).
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( empty( $data_attributes ) || ! is_array( $data_attributes ) ) {
	$data_attributes = [];
}
?>
<?php if ( 1 === count( $units ) ) : ?>
<span class="wpb-unit-label"
	<?php foreach ( $data_attributes as $attr_name => $attr_value ) : ?>
		data-<?php echo esc_attr( $attr_name ); ?>="<?php echo esc_attr( $attr_value ); ?>"
	<?php endforeach; ?>
	aria-label="<?php echo esc_attr__( 'Unit', 'js_composer' ); ?>"><?php echo esc_html( $units[0] ); ?></span>
<?php else : ?>
<select class="wpb-unit-selector"
	<?php foreach ( $data_attributes as $attr_name => $attr_value ) : ?>
		data-<?php echo esc_attr( $attr_name ); ?>="<?php echo esc_attr( $attr_value ); ?>"
	<?php endforeach; ?>
	aria-label="<?php echo esc_attr__( 'Unit', 'js_composer' ); ?>">
	<?php foreach ( $units as $unit ) : ?>
		<option value="<?php echo esc_attr( $unit ); ?>"
			<?php selected( $unit, $selected_unit ); ?>
		><?php echo esc_html( $unit ); ?></option>
	<?php endforeach; ?>
</select>
<?php endif; ?>
