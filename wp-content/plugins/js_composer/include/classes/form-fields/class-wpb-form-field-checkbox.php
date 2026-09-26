<?php
/**
 * Checkbox form field class.
 *
 * Handles checkbox-specific logic and rendering.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Checkbox
 *
 * Checkbox form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Checkbox extends WPB_Form_Field_Abstract {

	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'checkbox';
	}

	/**
	 * Get default values for checkbox arguments.
	 *
	 * @since 9.0
	 *
	 * @return array Default argument values.
	 */
	protected function get_defaults() {
		return [
			'id'               => '',
			'name'             => '',
			'value'            => '1',
			'label'            => '',
			'checked'          => false,
			'disabled'         => false,
			'class'            => '',
			'input_attr_class' => '',
			'data_attributes'  => [],
			'label_attributes' => [],
		];
	}
}
