<?php
/**
 * Configuration file for [vc_tta_toggle] shortcode of 'Toggle Container' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'dropdown',
		'param_name' => 'tab_position',
		'value' => [
			esc_html__( 'Top', 'js_composer' ) => 'top',
			esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
		],
		'std' => 'top',
		'heading' => esc_html__( 'Toggle position', 'js_composer' ),
		'description' => esc_html__( 'Select pageable navigation position.', 'js_composer' ),
	],
	[
		'type' => 'colorpicker',
		'value' => '#5188F1',
		'heading' => esc_html__( 'Active color', 'js_composer' ),
		'param_name' => 'color',
		'description' => esc_html__( 'Select custom toggle color.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'colorpicker',
		'value' => '#898989',
		'heading' => esc_html__( 'Inactive color', 'js_composer' ),
		'param_name' => 'hover_color',
		'description' => esc_html__( 'Select custom toggle hover color.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'hidden',
		'param_name' => 'no_fill_content_area',
		'std' => true,
	],
	// we need this hidden values cos we use pagination when switch container with toggle.
	[
		'type' => 'hidden',
		'param_name' => 'active_section',
		'value' => 1,
	],
	[
		'type' => 'hidden',
		'param_name' => 'pagination_style',
		'value' => 'outline-square',
	],
];

return [
	'name' => esc_html__( 'Toggle container', 'js_composer' ),
	'base' => 'vc_tta_toggle',
	'icon' => 'icon-wpb-tta-toggle',
	'is_container' => true,
	'show_settings_on_create' => false,
	'as_parent' => [
		'only' => 'vc_tta_section',
	],
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Pageable content container', 'js_composer' ),
	'js_view' => 'VcBackendTtaPageableView',
	'custom_markup' => '
<div class="vc_tta-container vc_tta-o-non-responsive" data-vc-action="collapse">
	<div class="vc_general vc_tta vc_tta-tabs vc_tta-pageable vc_tta-color-backend-tabs-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-spacing-1 vc_tta-tabs-position-top vc_tta-controls-align-left">
		<div class="vc_tta-tabs-container"><ul class="vc_tta-tabs-list"><li class="vc_tta-tab" data-hide-add-control="true" data-vc-tab data-vc-target-model-id="{{ model_id }}" data-element_type="vc_tta_section"><a href="javascript:;" data-vc-tabs data-vc-container=".vc_tta" data-vc-target="[data-model-id=\'{{ model_id }}\']" data-vc-target-model-id="{{ model_id }}"><span class="vc_tta-title-text">{{ section_title }}</span></a></li>' . '</ul>
		</div>
		<div class="vc_tta-panels vc_clearfix {{container-class}}">
		  {{ content }}
		</div>
	</div>
</div>',
	'default_content' => '
[vc_tta_toggle_section section_index=1 title="' . esc_html__( 'Monthly', 'js_composer' ) . '"][/vc_tta_toggle_section]
[vc_tta_toggle_section section_index=2 title="' . esc_html__( 'Yearly', 'js_composer' ) . '"][/vc_tta_toggle_section]
	',
	'admin_enqueue_js' => [
		vc_asset_url( 'lib/vc/vc_tabs/vc-tabs.min.js' ),
	],
	'params' => vc_config()->merge_default_params( $params ),
];
