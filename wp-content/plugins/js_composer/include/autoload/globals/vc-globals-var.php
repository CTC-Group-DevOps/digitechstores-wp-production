<?php
/**
 * Set plugin globals variables here.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $vc_row_layouts;
$vc_row_layouts = [
	[
		'cells' => '11',
		'mask' => '12',
		'title' => '1/1',
		'icon_class' => '1-1',
	],
	[
		'cells' => '12_12',
		'mask' => '26',
		'title' => '1/2 + 1/2',
		'icon_class' => '1-2_1-2',
	],
	[
		'cells' => '13_13_13',
		'mask' => '312',
		'title' => '1/3 + 1/3 + 1/3',
		'icon_class' => '1-3_1-3_1-3',
	],
	[
		'cells' => '14_14_14_14',
		'mask' => '420',
		'title' => '1/4 + 1/4 + 1/4 + 1/4',
		'icon_class' => '1-4_1-4_1-4_1-4',
	],
	[
		'cells' => '15_15_15_15_15',
		'mask' => '530',
		'title' => '1/5 + 1/5 + 1/5 + 1/5 + 1/5',
		'icon_class' => 'l_15_15_15_15_15',
	],
	[
		'cells' => '16_16_16_16_16_16',
		'mask' => '642',
		'title' => '1/6 + 1/6 + 1/6 + 1/6 + 1/6 + 1/6',
		'icon_class' => '1-6_1-6_1-6_1-6_1-6_1-6',
	],
	[
		'cells' => '23_13',
		'mask' => '29',
		'title' => '2/3 + 1/3',
		'icon_class' => '2-3_1-3',
	],
];
