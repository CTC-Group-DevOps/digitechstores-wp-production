<?php
/**
 * Textarea form field class.
 *
 * Handles textarea-specific logic and rendering.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Textarea
 *
 * Textarea form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Textarea extends WPB_Form_Field_Abstract {

	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'textarea';
	}

	/**
	 * Get default values for textarea arguments.
	 *
	 * @since 9.0
	 *
	 * @return array Default argument values.
	 */
	protected function get_defaults() {
		return [
			'id'              => '',
			'name'            => '',
			'value'           => '',
			'placeholder'     => '',
			'rows'            => '',
			'maxlength'       => '',
			'disabled'        => false,
			'value_type'      => '',
			'class'           => '',
			'style'           => '',
			'data_attributes' => [],
		];
	}
}
