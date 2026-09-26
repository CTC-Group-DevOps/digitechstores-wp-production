<?php
/**
 * Template for element param tag_input.
 *
 * @var array $settings
 * @var array $value
 * @var string $param_id
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="edit_form_line">
	<select
			<?php wpbakery()->editForm()->output_value_control_attr_id( $param_id, $settings['type'] ); ?>
			<?php wpbakery()->editForm()->output_value_control_attr_class( $settings['param_name'], $settings['type'] ); ?>
			name="<?php echo esc_attr( $settings['param_name'] ); ?>"
			multiple="multiple">

		<?php
		foreach ( $value as $option ) :
			?>
			<option value="<?php echo esc_attr( $option ); ?>" selected="selected"><?php echo esc_html( $option ); ?></option>
		<?php endforeach; ?>
	</select>
</div>
