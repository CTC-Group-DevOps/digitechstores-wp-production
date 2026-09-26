<?php
/**
 * Link form field modal template.
 *
 * @var array $link
 * @var string $name
 * @var array $settings
 * @var string $id
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="wpb_link-popup vc_modal modal-backdrop vc_modal-popup-container" style="display: none;" role="dialog" aria-modal="true">
	<div class="wpb_link-popup-content vc_ui-font-open-sans vc_media-xs vc_modal-popup-content">
		<div class="vc_ui-panel-window-inner">
			<div class="vc_ui-panel-header-container wpb_link-popup-header">
				<div class="vc_ui-panel-header">
					<div class="vc_ui-panel-header-header">
						<h3 class="vc_ui-panel-header-heading">
							<?php echo esc_html__( 'Insert or edit link', 'js_composer' ); ?>
						</h3>
						<div class="vc_ui-panel-header-controls">
							<button type="button" class="vc_general vc_ui-control-button wpb_link-popup-close-x" data-vc-ui-element="button-close" aria-label="<?php echo esc_attr__( 'Close', 'js_composer' ); ?>">
								<i class="vc-composer-icon vc-c-icon-close"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="vc_ui-panel-content-container wpb_link-popup-body">
				<div class="vc_ui-panel-content">
					<?php
					if ( ! isset( $settings['is_title'] ) || $settings['is_title'] ) {
						?>
						<div class="wpb_link-popup-field wpb_link-popup-title-field">
							<label for="<?php echo esc_attr( $id . '_title' ); ?>" class="wpb_link-popup-label">
								<?php echo esc_html__( 'Title', 'js_composer' ); ?>
							</label>
							<input type="text"
									id="<?php echo esc_attr( $id . '_title' ); ?>"
									class="wpb-form-input wpb_link-popup-text"
									value="<?php echo esc_attr( $link['title'] ?? '' ); ?>"
							>
						</div>
						<?php
					}
					?>

					<div class="wpb_link-popup-field">
						<label for="<?php echo esc_attr( $id . '_url' ); ?>" class="wpb_link-popup-label">
							<?php echo esc_html__( 'Link', 'js_composer' ); ?>
						</label>
						<div class="wpb-search-input">
							<input type="text"
									id="<?php echo esc_attr( $id . '_url' ); ?>"
									class="wpb-form-input wpb_link-popup-select"
									placeholder="<?php echo esc_attr__( 'Search or type URL', 'js_composer' ); ?>"
							>
							<select class="wpb-input-dropdown-select" style="display: none;" tabindex="-1"></select>
						</div>
					</div>
					<div class="wpb_link-popup-toggles">
						<?php
						$toggle_suffix = implode( '-', array_filter( [ $name, $id ] ) );
						if ( ! isset( $settings['is_target'] ) || $settings['is_target'] ) {
							WPB_Form_Field_Toggle::render( [
								'id'                => 'wpb_link-target-' . $toggle_suffix,
								'classes'           => 'wpb_toggle-input wpb_link-target-toggle',
								'name'              => 'wpb_link_target',
								'is_checked'        => '_blank' === ( $link['target'] ?? '' ),
								'title'             => __( 'Open in new tab', 'js_composer' ),
								'container_classes' => '',
							] );
						}
						if ( ! isset( $settings['is_nofollow'] ) || $settings['is_nofollow'] ) {
							WPB_Form_Field_Toggle::render( [
								'id'                => 'wpb_link-nofollow-' . $toggle_suffix,
								'classes'           => 'wpb_toggle-input wpb_link-nofollow-toggle',
								'name'              => 'wpb_link_nofollow',
								'is_checked'        => 'nofollow' === ( $link['rel'] ?? '' ),
								'title'             => __( 'Mark as nofollow', 'js_composer' ),
								'container_classes' => '',
							] );
						}
						?>
					</div>
				</div>
			</div>
			<div class="vc_ui-panel-footer-container wpb_link-popup-footer">
				<div class="vc_ui-panel-footer">
					<div class="vc_ui-button-group">
						<button type="button" class="vc_general vc_ui-button vc_ui-button-default vc_ui-button-shape-rounded wpb_link-popup-close">
							<?php echo esc_html__( 'Close', 'js_composer' ); ?>
						</button>
						<button type="button" class="vc_general vc_ui-button vc_ui-button-action vc_ui-button-shape-rounded wpb_link-popup-save">
							<?php echo esc_html__( 'Save changes', 'js_composer' ); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
