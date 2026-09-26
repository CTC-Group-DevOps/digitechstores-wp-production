<?php
/**
 * Template for element param group inner content.
 *
 * @var string $item_id
 * @var int $number
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
return '
<div class="vc_controls vc_controls-row vc_clearfix vc_param_group-controls">
	<a class="vc_control column_move vc_move-param" href="#" title="' . esc_attr__( 'Drag row to reorder', 'js_composer' ) . '" data-vc-control="move"><i class="vc-composer-icon vc-c-param-group-dragndrop"></i></a>
	<span class="vc_param-group-admin-labels"></span>
	<span class="vc_row_edit_clone_delete">
		<button type="button" class="vc_control column_delete vc_delete-param" title="' . esc_attr__( 'Delete this param', 'js_composer' ) . '">
		    <i class="vc-composer-icon vc-c-param-group-delete"></i>
		</button>
		<button type="button" class="vc_control column_toggle" title="' . esc_attr__( 'Toggle row', 'js_composer' ) . '" aria-controls="' . esc_attr( $item_id ) . '" aria-label="' . esc_attr__( 'Toggle details for param group item: ', 'js_composer' ) . esc_attr( $number ) . '" aria-expanded="false">
		    <i class="vc-composer-icon vc-c-number-arrow-down"></i>
		</button>
		<button type="button" class="vc_control column_clone" title="' . esc_attr__( 'Clone this row', 'js_composer' ) . '">
		    <i class="vc-composer-icon vc-c-param-group-copy"></i>
		</button>
	</span>
</div>
<div class="wpb_element_wrapper" role="region" id="' . esc_attr( $item_id ) . '" hidden>
	<div class="vc_row vc_row-fluid wpb_row_container">
		<div class="wpb_vc_column wpb_sortable vc_col-sm-12 wpb_content_holder vc_empty-column">
			<div class="wpb_element_wrapper">
				<div class="vc_fields vc_clearfix">
					%content%
				</div>
			</div>
		</div>
	</div>
</div>';
