<?php
/**
 * Textarea HTML form field class.
 *
 * Handles textarea_html-specific logic and rendering.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Textarea_Html
 *
 * Textarea HTML form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Textarea_Html extends WPB_Form_Field_Abstract {

	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'textarea_html';
	}

	/**
	 * Get default values for textarea_html arguments.
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
			'editor_class'    => '',
			'media_buttons'   => true,
			'wpautop'         => false,
			'class'           => '',
			'data_attributes' => [],
		];
	}

	/**
	 * Prepare arguments for template rendering.
	 *
	 * Merges provided args with defaults and performs any field-specific processing.
	 *
	 * @since 9.0
	 *
	 * @param array $args User-provided arguments.
	 * @return array Prepared arguments for template.
	 */
	protected function prepare_args( array $args ) {
		$defaults = $this->get_defaults();
		$args     = array_merge( $defaults, $args );

		// Build input data attributes string.
		$args['data_attr_string'] = $this->build_data_attributes( $args['data_attributes'] );

		return $args;
	}
}
