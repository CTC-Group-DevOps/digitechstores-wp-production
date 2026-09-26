<?php
/**
 * Post type dropdown in page settings panel template.
 *
 * @since 8.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
global $post, $user_ID;
$current_user = wp_get_current_user();
$post_type_object = get_post_type_object( $post->post_type );

$cap_publish       = $post_type_object->cap->publish_posts;
$cap_edit          = $post_type_object->cap->edit_posts;
$cap_edit_others   = $post_type_object->cap->edit_others_posts;

$can_publish       = current_user_can( $cap_publish );
$can_edit          = current_user_can( $cap_edit );
$can_edit_others   = current_user_can( $cap_edit_others );
$can_submit_for_review = current_user_can( $cap_edit ) && ! current_user_can( $cap_publish );

$current_post_status = get_post_status( get_the_ID() );
$options = [];
if ( $can_edit ) {
	$options[] = [
		'value' => 'draft',
		'label' => esc_html__( 'Draft', 'js_composer' ),
		'selected' => 'draft' === $current_post_status,
	];
}
if ( $can_submit_for_review ) {
	$options[] = [
		'value' => 'pending',
		'label' => esc_html__( 'Pending Review', 'js_composer' ),
		'selected' => 'pending' === $current_post_status,
	];
}
if ( $can_publish ) {
	$options[] = [
		'value' => 'publish',
		'label' => esc_html__( 'Published', 'js_composer' ),
		'selected' => 'publish' === $current_post_status,
	];
}
?>

<?php if ( current_user_can( 'edit_posts' ) && 'draft' !== $current_post_status ) : ?>
<div class="vc_col-sm-12 vc_column" id="vc_settings-post_status">
	<label for="vc_post_status" class="wpb_element_label">
		<?php
		echo esc_html( sprintf( __( '%s status', 'js_composer' ), wpb_get_post_type_noun() ) );
		?>
	</label>
	<?php
	WPB_Form_Field_Dropdown::render( [
		'id' => 'vc_post_status',
		'classes' => 'wpb_vc_param_value wpb-input wpb-select wpb-select--full',
		'name' => 'post_status',
		'options' => $options,
	] );
	?>
</div>
<?php endif; ?>
