<?php
/**
 * Button group form field class.
 *
 * Handles button_group specific logic and rendering.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Button_Group
 *
 * Button_Group form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Button_Group extends WPB_Form_Field_Abstract {
	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'button_group';
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
			'settings' => [],
			'id' => '',
			'name' => '',
			'class' => '',
			'input_classes' => '',
			'options' => [],
			'heading' => '',
			'current_value' => '',
			'icon_size' => '',
		];
	}
}
