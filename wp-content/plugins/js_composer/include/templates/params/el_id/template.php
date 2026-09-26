<?php
/**
 * Template for element param el_id.
 *
 * @var array $settings
 * @var string $value
 * @var string $param_id
 * @var string $value
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="vc-param-el_id">
<?php
WPB_Form_Field_Textfield::render(
	[
		'id'          => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
		'classes'     => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'] . '_field' ),
		'name'        => $settings['param_name'],
		'value'       => $value,
	]
);
?>
</div>
