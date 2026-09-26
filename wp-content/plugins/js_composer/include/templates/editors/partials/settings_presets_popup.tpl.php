<?php
/**
 * Settings presets popup template.
 *
 * @var array $list_presets
 * @var string $shortcode_name
 * @var int $default_id
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$save_as_template_elements = apply_filters( 'vc_popup_save_as_template_elements', [
	'vc_row',
	'vc_section',
] );
$custom_tag = 'script'; // TODO: Remove this file after 6.2 when BC is completed.
?>
<div class="wpb-form-select-dropdown select2-dropdown select2-dropdown--below">
	<div class="select2-results">
		<ul class="select2-results__options">
			<li class="select2-results__option select2-results__option--disabled vc_ui-dropdown-item vc_ui-prompt-close">
				<?php esc_html_e( 'Edit element', 'js_composer' ); ?>
			</li>
		<?php if ( in_array( $shortcode_name, $save_as_template_elements ) && vc_user_access()->part( 'templates' )->checkStateAny( true, null )->get() ) : ?>
			<li class="select2-results__option vc_ui-dropdown-item vc_ui-list-bar-item-trigger" data-vc-save-template>
				<?php esc_html_e( 'Save as template', 'js_composer' ); ?>
			</li>
		<?php endif; ?>
		<?php if ( ! in_array( $shortcode_name, $save_as_template_elements ) && vc_user_access()->part( 'presets' )->checkStateAny( true, null )->get() ) : ?>
			<li class="select2-results__option vc_ui-dropdown-item vc_ui-list-bar-item-trigger" data-vc-save-settings-preset>
				<?php esc_html_e( 'Save as element preset', 'js_composer' ); ?>
			</li>
		<?php endif; ?>
		</ul>
		<<?php echo esc_attr( $custom_tag ); ?>>
			window.vc_presets_data = {
				"presets": <?php echo wp_json_encode( $list_presets ); ?>,
				"presetsCount": <?php echo count( $list_presets[0] ) + count( $list_presets[1] ); ?>,
				"defaultId": <?php echo (int) $default_id; ?>,
				"can": <?php echo (int) vc_user_access()->part( 'presets' )->can()->get(); ?>,
				"defaultTitle": "<?php esc_attr_e( 'Untitled', 'js_composer' ); ?>"
			}
		</<?php echo esc_attr( $custom_tag ); ?>>
	</div>
</div>
