<?php
/**
 * Colorpicker form field class.
 *
 * Handles colorpicker specific logic and rendering.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Colorpicker
 *
 * Colorpicker form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Colorpicker extends WPB_Form_Field_Abstract {
	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'colorpicker';
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
			'id'                => '',
			'classes'           => '',
			'name'              => '',
			'value'             => '',
			'data_attributes'    => [],
		];
	}
}
