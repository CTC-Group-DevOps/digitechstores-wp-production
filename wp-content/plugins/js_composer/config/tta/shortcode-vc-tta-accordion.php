<?php
/**
 * Configuration file for [vc_tta_accordion] shortcode of 'Accordion' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'dropdown',
		'param_name' => 'style',
		'value' => [
			esc_html__( 'Classic', 'js_composer' ) => 'classic',
			esc_html__( 'Modern', 'js_composer' ) => 'modern',
			esc_html__( 'Flat', 'js_composer' ) => 'flat',
			esc_html__( 'Outline', 'js_composer' ) => 'outline',
		],
		'heading' => esc_html__( 'Style', 'js_composer' ),
		'description' => esc_html__( 'Select accordion display style.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'styles',
	],
	[
		'type' => 'dropdown',
		'param_name' => 'shape',
		'value' => [
			esc_html__( 'Rounded', 'js_composer' ) => 'rounded',
			esc_html__( 'Square', 'js_composer' ) => 'square',
			esc_html__( 'Round', 'js_composer' ) => 'round',
		],
		'heading' => esc_html__( 'Shape', 'js_composer' ),
		'description' => esc_html__( 'Select accordion shape.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'styles',
	],
	[
		'type' => 'colorpicker',
		'param_name' => 'active_color',
		'std' => '',
		'settings' => [
			'default_colorpicker_color' => '#F8F8F8',
		],
		'dependency' => [
			'element' => 'style',
			'value_not_equal_to' => [ 'outline' ],
		],
		'heading' => esc_html__( 'Active color', 'js_composer' ),
		'description' => esc_html__( 'Select active accordion color.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'styles',
	],
	[
		'type' => 'colorpicker',
		'param_name' => 'color',
		'std' => '#F8F8F8',
		'settings' => [
			'default_colorpicker_color' => '#F8F8F8',
		],
		'dependency' => [
			'element' => 'style',
			'value_not_equal_to' => [ 'outline' ],
		],
		'heading' => esc_html__( 'Inactive color', 'js_composer' ),
		'description' => esc_html__( 'Select accordion color.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'styles',
	],
	[
		'type' => 'colorpicker',
		'param_name' => 'outline_color',
		'std' => '',
		'settings' => [
			'default_colorpicker_color' => '#EBEBEB',
		],
		'dependency' => [
			'element' => 'style',
			'value' => [ 'outline' ],
		],
		'heading' => esc_html__( 'Color', 'js_composer' ),
		'description' => esc_html__( 'Select accordion outline color.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-12',
		'section' => 'styles',
	],
	[
		'type' => 'colorpicker',
		'param_name' => 'active_title_color',
		'std' => '',
		'settings' => [
			'default_colorpicker_color' => '#666',
		],
		'heading' => esc_html__( 'Active title color', 'js_composer' ),
		'description' => esc_html__( 'Select active accordion title color.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'styles',
	],
	[
		'type' => 'colorpicker',
		'param_name' => 'inactive_title_color',
		'std' => '',
		'settings' => [
			'default_colorpicker_color' => '#666',
		],
		'heading' => esc_html__( 'Inactive title color', 'js_composer' ),
		'description' => esc_html__( 'Select inactive accordion title color.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'styles',
	],
	[
		'type' => 'toggle',
		'param_name' => 'fill_content_area',
		'std' => 'true',
		'heading' => esc_html__( 'Fill content area', 'js_composer' ),
		'description' => esc_html__( 'Enable to fill the content area with the active color.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'styles',
	],
	[
		'type' => 'dropdown',
		'param_name' => 'section_title_tag',
		'value' => [
			'h1' => 'h1',
			'h2' => 'h2',
			'h3' => 'h3',
			'h4' => 'h4',
			'h5' => 'h5',
			'h6' => 'h6',
			'p' => 'p',
		],
		'std' => 'h4',
		'heading' => esc_html__( 'Title tag', 'js_composer' ),
		'description' => esc_html__( 'Select section title tag.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'structure',
	],
	[
		'type' => 'button_group',
		'param_name' => 'c_align',
		'value'       => vc_config()->get_text_align_param_value( [ 'justify' ] ),
		'std' => 'left',
		'heading' => esc_html__( 'Alignment', 'js_composer' ),
		'description' => esc_html__( 'Select accordion section title alignment.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'structure',
	],
	[
		'type' => 'number',
		'param_name' => 'spacing',
		'std' => '',
		'settings' => [
			'min' => 0,
			'step' => 1,
			'units' => true,
		],
		'heading' => esc_html__( 'Spacing', 'js_composer' ),
		'description' => esc_html__( 'Select accordion spacing.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'structure',
	],
	[
		'type' => 'number',
		'param_name' => 'gap',
		'std' => '',
		'settings' => [
			'min' => 0,
			'step' => 1,
			'units' => true,
		],
		'heading' => esc_html__( 'Gap', 'js_composer' ),
		'description' => esc_html__( 'Select accordion gap.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'structure',
	],
	[
		'type' => 'dropdown',
		'param_name' => 'c_icon',
		'value' => [
			esc_html__( 'None', 'js_composer' ) => '',
			esc_html__( 'Chevron', 'js_composer' ) => 'chevron',
			esc_html__( 'Plus', 'js_composer' ) => 'plus',
			esc_html__( 'Triangle', 'js_composer' ) => 'triangle',
		],
		'std' => 'plus',
		'heading' => esc_html__( 'Icon', 'js_composer' ),
		'description' => esc_html__( 'Select accordion navigation icon.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'structure',
	],
	[
		'type' => 'dropdown',
		'param_name' => 'c_position',
		'value' => [
			esc_html__( 'Default', 'js_composer' ) => 'default',
			esc_html__( 'Left', 'js_composer' ) => 'left',
			esc_html__( 'Right', 'js_composer' ) => 'right',
		],
		'dependency' => [
			'element' => 'c_icon',
			'not_empty' => true,
		],
		'heading' => esc_html__( 'Position', 'js_composer' ),
		'description' => esc_html__( 'Select accordion navigation icon position.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'structure',
	],
	[
		'type' => 'toggle',
		'param_name' => 'collapsible_all',
		'std' => 'false',
		'heading' => esc_html__( 'Allow to collapse all', 'js_composer' ),
		'description' => esc_html__( 'Allow collapse all accordion sections.', 'js_composer' ),
		'section' => 'structure',
	],
	[
		'type' => 'number',
		'param_name' => 'autoplay',
		'std' => '',
		'settings' => [
			'min' => 0,
			'step' => 1,
		],
		'heading' => esc_html__( 'Autoplay', 'js_composer' ),
		'description' => esc_html__( 'Select auto rotate for accordion in seconds (Note: disabled by default).', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'autoplay',
	],
	[
		'type' => 'number',
		'param_name' => 'active_section',
		'heading' => esc_html__( 'Active section', 'js_composer' ),
		'value' => '1',
		'settings' => [
			'min' => 0,
			'step' => 1,
		],
		'description' => esc_html__( 'Enter active section number (Note: to have all sections closed on initial load enter non-existing number).', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'autoplay',
	],
	[
		'type' => 'textfield',
		'param_name' => 'title',
		'heading' => esc_html__( 'Widget title', 'js_composer' ),
		'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
	],
	[
		'type' => 'dropdown',
		'param_name' => 'title_tag',
		'value' => [
			'h1' => 'h1',
			'h2' => 'h2',
			'h3' => 'h3',
			'h4' => 'h4',
			'h5' => 'h5',
			'h6' => 'h6',
			'p' => 'p',
		],
		'std' => 'h2',
		'heading' => esc_html__( 'Widget title tag', 'js_composer' ),
		'description' => esc_html__( 'Select widget title tag.', 'js_composer' ),
	],
];

return [
	'name' => esc_html__( 'Accordion', 'js_composer' ),
	'base' => 'vc_tta_accordion',
	'icon' => 'icon-wpb-ui-accordion',
	'is_container' => true,
	'show_settings_on_create' => false,
	'as_parent' => [
		'only' => 'vc_tta_section',
	],
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Collapsible content panels', 'js_composer' ),
	'js_view' => 'VcBackendTtaAccordionView',
	'sections' => array_merge( [ 'styles', 'structure', 'autoplay' ], vc_config()->get_advanced_sections() ),
	'custom_markup' => '
<div class="vc_tta-container" data-vc-action="collapseAll">
	<div class="vc_general vc_tta vc_tta-accordion vc_tta-color-backend-accordion-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-o-shape-group vc_tta-controls-align-left vc_tta-gap-2">
	   <div class="vc_tta-panels vc_clearfix {{container-class}}">
	      {{ content }}
	      <div class="vc_tta-panel vc_tta-section-append">
	         <div class="vc_tta-panel-heading">
	            <h4 class="vc_tta-panel-title vc_tta-controls-icon-position-left">
	               <a href="javascript:;" aria-expanded="false" class="vc_tta-backend-add-control">
	                   <span class="vc_tta-title-text">' . esc_html__( 'Add Section', 'js_composer' ) . '</span>
	                    <i class="vc_tta-controls-icon vc_tta-controls-icon-plus"></i>
					</a>
	            </h4>
	         </div>
	      </div>
	   </div>
	</div>
</div>',
	'default_content' => '[vc_tta_section title="' . sprintf( '%s %d', esc_html__( 'Section', 'js_composer' ), 1 ) . '"][/vc_tta_section][vc_tta_section title="' . sprintf( '%s %d', esc_html__( 'Section', 'js_composer' ), 2 ) . '"][/vc_tta_section]',
	'params' => vc_config()->merge_default_params( $params ),
];
