<?php
/**
 * Hidden form field class.
 *
 * Handles hidden specific logic and rendering.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Hidden
 *
 * Hidden form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Hidden extends WPB_Form_Field_Abstract {
	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'hidden';
	}

	/**
	 * Get default values for arguments.
	 *
	 * @since 9.0
	 *
	 * @return array Default argument values.
	 */
	protected function get_defaults() {
		return [
			'name' => '',
			'value' => '',
			'classes' => '',
			'is_value_escape' => true,
			'data_attributes' => [],
		];
	}
}
