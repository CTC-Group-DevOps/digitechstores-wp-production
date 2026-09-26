<?php
/**
 * Page title section in page settings panel template.
 *
 * @since 8.2
 * @var array $page_settings_data
 */

?>

<div class="vc_col-sm-12 vc_column" id="vc_settings-title-container">
	<div class="wpb_settings-title">
		<label for="vc_page-title-field" class="wpb_element_label">
			<?php
			echo esc_html( sprintf( __( '%s title', 'js_composer' ), wpb_get_post_type_noun() ) );
			?>
		</label>
	</div>
	<div class="edit_form_line">
		<?php
		if ( vc_modules_manager()->is_module_on( 'vc-ai' ) ) {
			wpb_add_ai_icon_to_text_field( 'textfield', 'vc_page-title-field' );
		}
		WPB_Form_Field_Textfield::render(
			[
				'classes' => 'vc_title_name',
				'id' => 'vc_page-title-field',
				'name' => 'post_title',
				'value' => $page_settings_data['post_title'],
				'placeholder' => sprintf( __( 'Please enter %s title', 'js_composer' ), wpb_get_post_type_noun() ),
			]
		);
		?>
	</div>
</div>
