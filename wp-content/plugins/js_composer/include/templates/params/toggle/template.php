<?php
/**
 * Template for toggle parameter type.
 *
 * @var array  $settings
 * @var string $value
 * @var string $param_id
 * @var string $class
 * @var bool   $is_checked
 * @var string $checked_value
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="wpb_toggle-container <?php echo esc_attr( $class ); ?>">
	<?php
		WPB_Form_Field_Toggle::render( [
			'id'      => wpbakery()->editForm()->get_value_control_id( $settings['type'], $param_id ),
			'classes' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'], '', 'wpb_toggle-input' ),
			'name'    => $settings['param_name'],
			'is_checked' => $is_checked,
			'checked_value' => $checked_value,
		] );
		?>
</div>
