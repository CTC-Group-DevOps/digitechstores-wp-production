<?php
/**
 * Dropdown form field class.
 *
 * Handles dropdown specific logic and rendering.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Dropdown
 *
 * Dropdown form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Dropdown extends WPB_Form_Field_Abstract {
	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'dropdown';
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
			'has_search'        => false,
			'data_attributes'   => [],
			'options'           => [],
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
