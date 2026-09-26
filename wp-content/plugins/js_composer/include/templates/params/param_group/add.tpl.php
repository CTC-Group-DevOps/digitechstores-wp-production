<?php
/**
 * Template for element param group add.
 *
 * @var string $item_id
 * @var int $number
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$template = vc_include_template( 'params/param_group/inner_content.tpl.php', [
	'item_id' => '',
	'number' => '',
] );

return '<li class="vc_param wpb_vc_row vc_param_group-collapsed vc_param_group-add_content-wrapper" style="display:none" role="button" tabindex="0" aria-label="' . esc_attr__( 'Add new', 'js_composer' ) . '">' . $template . '</li>';
