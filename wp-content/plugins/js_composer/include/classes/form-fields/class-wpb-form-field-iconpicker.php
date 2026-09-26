<?php
/**
 * Iconpicker form field class.
 *
 * Handles iconpicker specific logic and rendering.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Iconpicker
 *
 * Iconpicker form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Iconpicker extends WPB_Form_Field_Abstract {
	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'iconpicker';
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
			'icon_lib_list'   => [],
			'name'            => '',
			'classes'         => '',
			'value'           => '',
			'id'              => '',
			'data_attributes' => [],
		];
	}
}
