<?php
/**
 * Custom CSS template.
 *
 * @var array $value
 * @var string $field_prefix
 */

?>

<div class="vc_ui-settings-text-wrapper">
	<?php
	if ( vc_modules_manager()->is_module_on( 'vc-ai' ) ) {
		wpb_add_ai_icon_to_code_field( 'custom_css', 'wpb_css_editor' );
	}
	?>
</div>
<?php
WPB_Form_Field_Textarea::render( [
	'name'  => $field_prefix . 'custom_css',
	'value' => $value,
	'class' => 'wpb_code_editor custom_code',
	'style' => 'display:none',
] );
WPB_Form_Field_Textarea_Ace::render([
	'id' => 'wpb_css_editor',
	'classes' => 'wpb_content_element',
	'decoded_value' => $value,
	'data_attributes' => [
		'ace-location' => 'plugin-settings',
	],
]);

