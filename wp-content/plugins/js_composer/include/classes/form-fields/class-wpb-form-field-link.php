<?php
/**
 * Link form field class.
 *
 * Handles link specific logic and rendering.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Link
 *
 * Link form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Link extends WPB_Form_Field_Abstract {
	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'link';
	}

	/**
	 * Get the template path for the link form field.
	 *
	 * @since 9.0
	 *
	 * @param string $type Field type.
	 * @return string Template path.
	 */
	protected function get_template_path( $type ) {
		return 'form-fields/link/link.php';
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
			'value' => '',
			'link' => [
				'url' => '',
				'target' => '_self',
				'title' => '',
				'rel' => '',
			],
			'name' => '',
			'type' => 'link',
			'settings' => [],
			'id' => '',
		];
	}
}
