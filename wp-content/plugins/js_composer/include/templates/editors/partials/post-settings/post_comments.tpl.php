<?php
/**
 * Allow/disable comments section in page settings panel.
 *
 * @var WP_Post $post
 * @since 8.2
 */

?>

<div class="vc_col-xs-12 vc_column">
	<?php
	WPB_Form_Field_Toggle::render( [
		'container_classes' => 'vc_settings-comments',
		'id'      => 'vc_post_comments',
		'classes' => 'wpb_toggle-input',
		'is_checked' => 'open' === $post->comment_status,
		'title'    => __( 'Allow comments', 'js_composer' ),
	] );
	?>
</div>
