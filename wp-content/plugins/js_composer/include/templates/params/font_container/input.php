<?php
/**
 * Font container hidden input.
 *
 * @var array $settings
 * @var string $value
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>

<input
		class="<?php echo esc_attr( wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'] . '_field' ) ); ?>"
		name="<?php echo esc_attr( $settings['param_name'] ); ?>"
		type="hidden"
		value="<?php echo esc_attr( $value ); ?>"
>
