<?php
/**
 * Custom JS template.
 *
 * @var array $value
 * @var array $field_prefix
 * @var string $area
 */

?>

<div class="vc_ui-settings-text-wrapper">
	<p class="wpb-code-editor-tag">
		&lt;script&gt;
	</p>
	<?php
	if ( vc_modules_manager()->is_module_on( 'vc-ai' ) ) {
		wpb_add_ai_icon_to_code_field( 'custom_js', 'wpb_js_' . esc_attr( $area ) . '_editor' );
	}
	?>
</div>
<?php
WPB_Form_Field_Textarea::render( [
	'name'            => $field_prefix . 'custom_js_' . $area,
	'value'           => $value,
	'class'           => 'wpb_code_editor custom_code',
	'style'           => 'display:none',
	'data_attributes' => [
		'code-type' => 'html',
	],
] );
WPB_Form_Field_Textarea_Ace::render([
	'id' => 'wpb_js_' . $area . '_editor',
	'classes' => 'wpb_content_element',
	'decoded_value' => $value,
	'data_attributes' => [
		'ace-location' => 'plugin-settings',
	],
]);
?>
<p class="wpb-code-editor-tag">
	&lt;/script&gt;
</p>
