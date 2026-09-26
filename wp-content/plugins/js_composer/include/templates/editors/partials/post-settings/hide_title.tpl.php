<?php
/**
 * Page hide title section in page settings panel template.
 *
 * @since 8.2
 * @var array $page_settings_data
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>


<div class="vc_col-sm-12 vc_column">
	<?php
	WPB_Form_Field_Toggle::render( [
		'id'      => 'wpb_post-hide-title',
		'classes' => 'wpb_toggle-input',
		'is_checked' => $page_settings_data['is_hide_title'],
		'title'    => __( 'Hide title', 'js_composer' ),
		'container_classes' => 'vc_settings-comments',
	] );
	?>
</div>
