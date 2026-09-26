<?php
/**
 * Control Save Buttons template.
 *
 * @var Wp_Post $post
 * @var bool $is_mobile
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$post_type = get_post_type_object( $post->post_type );
$can_publish = current_user_can( $post_type->cap->publish_posts );

?>
<li class="vc_pull-right vc_save-buttons vc_hide-desktop-more <?php echo $is_mobile ? 'vc_hide-desktop' : 'vc_hide-mobile'; ?>">
<?php
if ( ! in_array( $post->post_status, [
	'publish',
	'future',
	'private',
], true ) ) :
	if ( 'draft' === $post->post_status ) :
		vc_include_template(
			'editors/navbar/vc_control-save-button-template.php',
			[
				'id' => 'vc_button-save-draft',
				'class' => ( $is_mobile ? 'vc_icon-btn ' : 'vc_btn vc_btn-default vc_navbar-btn ' ) . 'vc_btn-save-draft',
				'title' => wpb_get_title_with_shortcut( 'Save draft' ),
				'icon' => 'save-draft',
				'text' => __( 'Save draft', 'js_composer' ),
				'tabindex' => 10,
			]
		);
	elseif ( 'pending' === $post->post_status && $can_publish ) :
		vc_include_template(
			'editors/navbar/vc_control-save-button-template.php',
			[
				'id' => 'vc_button-save-as-pending',
				'class' => ( $is_mobile ? 'vc_icon-btn ' : 'vc_btn vc_btn-primary vc_navbar-btn ' ) . 'vc_btn-save',
				'title' => wpb_get_title_with_shortcut( 'Save as Pending' ),
				'icon' => 'save-draft',
				'text' => __( 'Save as Pending', 'js_composer' ),
				'tabindex' => 10,
			]
		);
	endif;
	if ( $can_publish ) :
		vc_include_template(
			'editors/navbar/vc_control-save-button-template.php',
			[
				'id' => 'vc_button-update',
				'class' => ( $is_mobile ? 'vc_icon-btn ' : 'vc_btn vc_btn-primary vc_navbar-btn ' ) . 'vc_btn-save',
				'title' => wpb_get_title_with_shortcut( 'Publish' ),
				'icon' => 'check',
				'text' => __( 'Publish', 'js_composer' ),
				'data_change_status' => 'publish',
				'tabindex' => 11,
			]
		);
		else :
			vc_include_template(
				'editors/navbar/vc_control-save-button-template.php',
				[
					'id' => 'vc_button-update',
					'class' => ( $is_mobile ? 'vc_icon-btn ' : 'vc_btn vc_btn-primary vc_navbar-btn ' ) . 'vc_btn-save',
					'title' => wpb_get_title_with_shortcut( 'Submit for Review' ),
					'icon' => 'check',
					'text' => __( 'Submit for Review', 'js_composer' ),
					'data_change_status' => 'pending',
					'tabindex' => 11,
				]
			);
		endif;
else :
	vc_include_template(
		'editors/navbar/vc_control-save-button-template.php',
		[
			'id' => 'vc_button-update',
			'class' => ( $is_mobile ? 'vc_icon-btn ' : 'vc_btn vc_btn-primary vc_navbar-btn ' ) . 'vc_btn-save',
			'title' => wpb_get_title_with_shortcut( 'Update' ),
			'icon' => 'check',
			'text' => __( 'Update', 'js_composer' ),
			'tabindex' => 11,
		]
	);
endif;
?>
</li>
