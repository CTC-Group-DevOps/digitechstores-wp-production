<?php
/**
 * Template for checkbox parameter type.
 *
 * @var array  $settings
 * @var string $value
 * @var string $param_id
 * @var array  $options
 * @var string $heading
 * @var string $direction
 * @var array  $current_value
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$main_id = wpbakery()->editForm()->get_value_control_id( $settings['type'], $param_id );
$input_attr_class = wpbakery()->editForm()->get_value_control_attr_class( $settings['param_name'], $settings['type'] );
$index = 0;
?>
<div class="wpb_checkbox-container wpb_checkbox-container-<?php echo esc_attr( $direction ); ?>" role="group" aria-label="<?php echo esc_attr( $heading ); ?>">
	<?php
	foreach ( $options as $label => $option_value ) :
		$checkbox_id = $main_id . '_' . $index;
		// NOTE!! Don't use strict compare here for BC!
		// @codingStandardsIgnoreLine
		$is_checked = in_array( $option_value, $current_value );
		$index++;

		WPB_Form_Field_Checkbox::render( [
			'id'               => $checkbox_id,
			'name'             => $settings['param_name'],
			'value'            => $option_value,
			'label'            => $label,
			'checked'          => $is_checked,
			'input_attr_class' => $input_attr_class,
		] );
	endforeach;
	?>
</div>
