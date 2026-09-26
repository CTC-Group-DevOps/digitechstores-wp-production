<?php
/**
 * Adobe fonts settings field button sync template.
 *
 * @var string $field_value
 * @var string $field_name
 */

?>

<div class="vc-settings-adobe-fonts">
	<?php
	vc_include_template(
		'editors/partials/param-info.tpl.php',
		[
			'print' => true,
			'format' => 'Synchronize Adobe Fonts to update the font list with your Adobe web project fonts. You can find your Adobe Project ID %s here %s.',
			'format_arguments' => [
				'<a href="https://fonts.adobe.com/my_fonts#web_projects-section" target="_blank">',
				'</a>',
			],
		]
	);
	WPB_Form_Field_Textfield::render(
		[
			'id' => $field_name,
			'name' => $field_name,
			'value' => $field_value,
			'classes' => 'css-control',
		]
	);
	?>
	<a href="#" class="vc_general vc_ui-button vc_ui-button-action vc_ui-button-shape-rounded vc_ui-button-fw vc_ui-button-with-spinner" id="vc_synchronize_adobe_fonts_button"><span class="vc_ui-button-label"><?php esc_html_e( 'Synchronize', 'js_composer' ); ?></span></a>
</div>
