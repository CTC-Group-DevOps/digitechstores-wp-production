<?php
/**
 * Radio form field class.
 *
 * Handles radio-specific logic and rendering.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Radio
 *
 * Radio form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Radio extends WPB_Form_Field_Abstract {

	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'radio';
	}

	/**
	 * Get default values for radio arguments.
	 *
	 * @since 9.0
	 *
	 * @return array Default argument values.
	 */
	protected function get_defaults() {
		return [
			'id'               => '',
			'name'             => '',
			'value'            => '',
			'label'            => '',
			'checked'          => false,
			'class'            => '',
			'wrapper_class'    => '',
			'input_attr_class' => 'class="wpb_radio-input"',
			'description'      => '',
			'data_attributes'  => [],
			'label_attributes' => [],
		];
	}
}
