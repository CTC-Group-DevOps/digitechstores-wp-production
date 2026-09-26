<?php
/**
 * Configuration file for [vc_row_inner] shortcode.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return [
	'name' => esc_html__( 'Inner Row', 'js_composer' ),
	'content_element' => false,
	'is_container' => true,
	'icon' => 'icon-wpb-row',
	'weight' => 1000,
	'show_settings_on_create' => false,
	'description' => esc_html__( 'Place content elements inside the inner row', 'js_composer' ),
	'params' => [
		[
			'type' => 'el_id',
			'heading' => esc_html__( 'Row ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter optional row ID. Make sure it is unique, and it is valid as w3c specification: %s (Must not have spaces)', 'js_composer' ), '<a target="_blank" href="https://www.w3schools.com/tags/att_global_id.asp">' . esc_html__( 'link', 'js_composer' ) . '</a>' ),
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Hide row', 'js_composer' ),
			'param_name' => 'disable_element',
			// Inner param name.
			'description' => esc_html__( 'If enabled the row won\'t be visible on the public side of your website. You can switch it back any time.', 'js_composer' ),
			'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
			'std' => '',
		],
		[
			'type' => 'number',
			'heading' => esc_html__( 'Column gap', 'js_composer' ),
			'param_name' => 'gap',
			'value' => '0',
			'std' => '0',
			'description' => esc_html__( 'Select gap between columns in row.', 'js_composer' ),
			'settings' => [
				'units' => true,
				'min' => 0,
			],
		],
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Content position', 'js_composer' ),
			'param_name' => 'content_placement',
			'value' => vc_config()->get_vertical_position_param_value(),
			'std' => '',
			'description' => esc_html__( 'Select content position within columns.', 'js_composer' ),
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Equal height', 'js_composer' ),
			'param_name' => 'equal_height',
			'description' => esc_html__( 'If enabled, columns will be set to equal height.', 'js_composer' ),
			'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
			'std' => '',
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'RTL columns', 'js_composer' ),
			'param_name' => 'rtl_reverse',
			'description' => esc_html__( 'If enabled, columns will be reversed in RTL.', 'js_composer' ),
			'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
			'std' => '',
			'edit_field_class' => 'vc_col-xs-6',
		],
		vc_config()->get_extra_class_params(),
		vc_config()->get_element_id_params(),
		[
			'type' => 'css_editor',
			'param_name' => 'css',
			'group' => esc_html__( 'Design options', 'js_composer' ),
		],
	],
	'js_view' => 'VcRowView',
];
