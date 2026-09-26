<?php
/**
 * Textfield form field class.
 *
 * Handles textfield specific logic and rendering.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Textfield
 *
 * Textfield form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Textfield extends WPB_Form_Field_Abstract {
	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'textfield';
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
			'placeholder'       => '',
			'value_type'        => '',
			'underscore_code'   => '',
			'style'             => '',
			'data_attr_list'    => [],
		];
	}
}
