<?php
/**
 * Textarea ace form field class.
 *
 * Handles textarea-ace specific logic and rendering.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Textarea_Ace
 *
 * Textarea ace form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Textarea_Ace extends WPB_Form_Field_Abstract {
	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'textarea_ace';
	}

	/**
	 * Get default values arguments.
	 *
	 * @since 9.0
	 *
	 * @return array Default argument values.
	 */
	protected function get_defaults() {
		return [
			'id' => '',
			'classes' => '',
			'decoded_value' => '',
			'data_attributes' => [],
			'style' => '',
		];
	}
}
