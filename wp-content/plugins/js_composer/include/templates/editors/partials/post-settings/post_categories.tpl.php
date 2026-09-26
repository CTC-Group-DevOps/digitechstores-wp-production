<?php
/**
 * Post categories section in post settings panel template.
 *
 * @since 8.2
 */

// Category Manager Class.
require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-post-settings-category-manager.php' );

$post_id = get_the_ID();
$vc_settings_category_manager = new Vc_Post_Settings_Category_Manager( $post_id );
?>

<div class="vc_col-sm-12 vc_column" id="vc_settings-post-category">
	<div class="wpb_settings-title">
		<label for="vc_post-category-search" class="wpb_element_label"><?php esc_html_e( 'Categories', 'js_composer' ); ?></label>
		<?php
			vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => sprintf( esc_html__( 'Select WordPress post categories or add new ones.', 'js_composer' ), esc_html( get_post_type() ) ) ] );
		?>
	</div>
	<div class="edit_form_line">
		<div class="wpb-search-input">
			<input id="vc_post-category-search" class="wpb-form-input" name="vc_post-category-search" type="text" />
		</div>
		<div id="vc_post-category" class="vc_post-category">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content is escaped in render_category_options_with_indent() method
			echo $vc_settings_category_manager->render_category_options_with_indent();
			?>
		</div>
	</div>
	<?php
		vc_include_template( 'editors/partials/post-settings/post_add-category.tpl.php', [ 'vc_settings_category_manager' => $vc_settings_category_manager ] );
	?>
</div>
