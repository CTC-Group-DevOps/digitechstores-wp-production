<?php
/**
 * Linked fields form field class.
 *
 * Handles linked_fields specific logic and rendering.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Linked_Fields
 *
 * Linked_fields form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Linked_Fields extends WPB_Form_Field_Abstract {

	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'linked_fields';
	}

	/**
	 * Get default values.
	 *
	 * @since 9.0
	 *
	 * @return array Default argument values.
	 */
	protected function get_defaults() {
		return [
			'settings' => [],
			'param_id' => '',
			'value' => '',
			'class' => '',
			'heading' => '',
			'parsed_value' => [],
			'is_linked' => true,
		];
	}
}
