<?php
/**
 * Template for radio parameter type.
 *
 * @var array  $settings
 * @var string $value
 * @var string $param_id
 * @var array  $options
 * @var string $heading
 * @var string $direction
 * @var string $current_value
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$main_id = wpbakery()->editForm()->get_value_control_id( $settings['type'], $param_id );
$input_attr_class = wpbakery()->editForm()->get_value_control_attr_class( $settings['param_name'], $settings['type'], '', 'wpb_radio-input' );
$index = 0;
?>
<div class="wpb_radio-container wpb_radio-container-<?php echo esc_attr( $direction ); ?>" role="radiogroup" aria-label="<?php echo esc_attr( $heading ); ?>">
	<?php
	foreach ( $options as $label => $option_value ) :
		$radio_id = $main_id . '_' . $index;
		$is_checked = ( (string) $current_value === (string) $option_value );
		$index++;

		WPB_Form_Field_Radio::render( [
			'id'               => $radio_id,
			'name'             => $settings['param_name'],
			'value'            => $option_value,
			'label'            => $label,
			'checked'          => $is_checked,
			'input_attr_class' => $input_attr_class,
		] );
	endforeach;
	?>
</div>
