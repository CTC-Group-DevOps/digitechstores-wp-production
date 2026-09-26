<?php
/**
 * More button for popups tabs.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$title = __( 'More', 'js_composer' );
?>

<li class="vc_ui-tabs-line-dropdown-toggle" data-vc-action="dropdown" data-vc-content=".vc_ui-tabs-line-dropdown" data-vc-ui-element="panel-tabs-line-toggle">
	<span class="vc_ui-tabs-line-trigger" data-vc-accordion="" data-vc-container=".vc_ui-tabs-line-dropdown-toggle" data-vc-target=".vc_ui-tabs-line-dropdown" title="<?php echo esc_attr( $title ); ?>" role="button" aria-haspopup="true" aria-label="<?php echo esc_attr( $title ); ?>"></span>
	<div class="vc_ui-tabs-line-dropdown wpb-form-select-dropdown select2-dropdown select2-dropdown--below">
		<div class="select2-results">
			<ul class="select2-results__options" data-vc-ui-element="panel-tabs-line-dropdown" role="menu"></ul>
		</div>
	</div>
</li>
