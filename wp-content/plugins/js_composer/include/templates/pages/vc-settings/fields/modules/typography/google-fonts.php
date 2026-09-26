<?php
/**
 * Google fonts settings field button sync template.
 */

?>
<div class="wpb-settings-google-fonts">
	<?php
	vc_include_template( 'editors/partials/param-info.tpl.php', [
		'description' => esc_html__( 'Synchronize Google Fonts to update the font list with the latest fonts from Google.', 'js_composer' ),
	] );
	?>
	<a href="#" class="vc_general vc_ui-button vc_ui-button-action vc_ui-button-shape-rounded vc_ui-button-fw vc_ui-button-with-spinner" id="vc_synchronize_google_fonts_button"><span class="vc_ui-button-label"><?php esc_html_e( 'Synchronize', 'js_composer' ); ?></span></a>
</div>
