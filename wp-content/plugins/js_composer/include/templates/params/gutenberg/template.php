<?php
/**
 * Template for gutenberg parameter type.
 *
 * @var array $settings
 * @var string $value
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>

<div class="vc_gutenberg-field-wrapper">
	<button class="vc_general vc_ui-button vc_ui-button-action vc_ui-button-shape-rounded vc_ui-button-fw" data-vc-action="open">
		<?php echo esc_html__( 'Open Gutenberg', 'js_composer' ); ?>
	</button>
	<div class="vc_gutenberg-modal-wrapper"></div>
	<?php
	WPB_Form_Field_Hidden::render([
		'name' => $settings['param_name'],
		'classes' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'], '', 'vc_gutenberg-field' ),
		'value' => $value,
		'is_value_escape' => false,
	])
	?>
</div>
