<?php
/**
 * Range form field class.
 *
 * Handles range slider with number input specific logic and rendering.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Range
 *
 * Range form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Range extends WPB_Form_Field_Abstract {

	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'range';
	}

	/**
	 * Get default values for range arguments.
	 *
	 * @since 9.0
	 *
	 * @return array Default argument values.
	 */
	protected function get_defaults() {
		return [
			'name'     => '',
			'value'    => '',
			'min'      => '',
			'max'      => '',
			'step'     => '',
			'settings' => [],
			'param_id' => '',
			'unit'     => '',
		];
	}
}
