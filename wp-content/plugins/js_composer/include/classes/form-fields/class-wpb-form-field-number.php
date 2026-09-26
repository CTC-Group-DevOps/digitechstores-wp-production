<?php
/**
 * Number form field class.
 *
 * Handles number specific logic and rendering.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Number
 *
 * Number form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Number extends WPB_Form_Field_Abstract {
	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'number';
	}

	/**
	 * Get default values.
	 *
	 * @since 9.0
	 *
	 * @return array Default values.
	 */
	protected function get_defaults() {
		return [
			'settings'    => [],
			'name'        => '',
			'placeholder' => '',
			'min'         => '',
			'max'         => '',
			'step'        => '',
		];
	}

	/**
	 * Get the base template path for form fields.
	 *
	 * @since 9.0
	 *
	 * @param string $type
	 * @return string Template base path.
	 */
	protected function get_template_path( $type ) {
		return 'form-fields/' . $type . '/' . $type . '.php';
	}
}
