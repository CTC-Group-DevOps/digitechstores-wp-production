<?php
/**
 * Beta version field template.
 *
 * @var boolean $checked
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="wpb-settings-beta-version">
	<?php
	if ( vc_license()->isActivated() ) {
		vc_include_template( 'editors/partials/param-info.tpl.php', [
			'description' => esc_html__( "Enable the plugin's beta version for this website for testing purposes. This feature is not recommended for production/live websites.", 'js_composer' ),
		] );
		?>
		<?php
		WPB_Form_Field_Checkbox::render( [
			'id'      => 'wpb_js_beta_version',
			'name'    => 'wpb_js_beta_version',
			'value'   => '1',
			'checked' => $checked,
			'label'   => esc_html__( 'Enable', 'js_composer' ),
		] );
		?>
		<br>
		<p class="wpb-beta-title-agreement">
			<?php esc_html_e( 'By enabling beta, I agree to be contacted by WPBakery to collect feedback.', 'js_composer' ); ?>
		</p>
		<?php
	} else {
		esc_html_e( 'An active license is needed to access beta version controls.', 'js_composer' );
	}
	?>
</div>
