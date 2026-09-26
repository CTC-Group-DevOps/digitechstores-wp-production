<?php
/**
 * Layout edit functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Edit row layout
 *
 * @since 4.3
 */
class Vc_Edit_Layout {
	/**
	 * Render UI template.
	 */
	public function renderUITemplate() {
		global $vc_row_layouts;
		$row_info = vc_get_template( 'editors/partials/param-info.tpl.php', [ 'description' => esc_html__( 'Choose predefined or set custom column layout within row.', 'js_composer' ) ] );
		vc_include_template( 'editors/popups/vc_ui-panel-row-layout.tpl.php', [
			'id' => 'row-layout',
			'box' => $this,
			'vc_row_layouts' => $vc_row_layouts,
			'row_info' => $row_info,
		]
		);
	}
}
