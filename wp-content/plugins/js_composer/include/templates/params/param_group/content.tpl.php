<?php
/**
 * Template for element param group content.
 *
 * @var string $item_id
 * @var int $number
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$template = vc_include_template( 'params/param_group/inner_content.tpl.php', [
	'item_id' => $item_id,
	'number' => $number,
] );

return '<li class="vc_param wpb_vc_row vc_param_group-collapsed">' . $template . '</li>';
