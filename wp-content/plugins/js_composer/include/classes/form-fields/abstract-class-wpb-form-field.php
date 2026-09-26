<?php
/**
 * Abstract form field class.
 *
 * Provides base functionality for form field rendering.
 * Each field type extends this class and implements its specific logic.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Abstract
 *
 * Abstract base class for form fields.
 *
 * @since 9.0
 * @phpstan-consistent-constructor
 */
abstract class WPB_Form_Field_Abstract {

	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type (e.g., 'checkbox', 'toggle', 'radio').
	 */
	abstract protected function get_type();

	/**
	 * Get default values for field arguments.
	 *
	 * @since 9.0
	 *
	 * @return array Default argument values.
	 */
	abstract protected function get_defaults();

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

		if ( isset( $args['label_attributes'] ) && is_array( $args['label_attributes'] ) ) {
			// Build label attributes string.
			$args['label_attr_string'] = $this->build_data_attributes( $args['label_attributes'] );
		}
		if ( isset( $args['data_attributes'] ) && is_array( $args['data_attributes'] ) ) {
			// Build input data attributes string.
			$args['data_attr_string'] = $this->build_data_attributes( $args['data_attributes'] );
		}

		return $args;
	}

	/**
	 * Get HTML for the form field.
	 *
	 * @since 9.0
	 *
	 * @param array $args Field arguments.
	 * @return string HTML output.
	 */
	public static function get( array $args = [] ) {
		$instance      = new static();
		$prepared_args = $instance->prepare_args( $args );
		$template_path = $instance->get_template_path( $instance->get_type() );

		return vc_get_template( $template_path, $prepared_args );
	}

	/**
	 * Render and output the form field.
	 *
	 * @since 9.0
	 *
	 * @param array $args Field arguments.
	 */
	public static function render( array $args = [] ) {
		$instance      = new static();
		$prepared_args = $instance->prepare_args( $args );
		$template_path = $instance->get_template_path( $instance->get_type() );

		vc_include_template( $template_path, $prepared_args );
	}

	/**
	 * Get the base template path for form fields.
	 *
	 * @since 9.0
	 *
	 * @param string $type Field type.
	 * @return string Template base path.
	 */
	protected function get_template_path( $type ) {
		return 'form-fields/' . $type . '.php';
	}

	/**
	 * Build a data attributes string from an array.
	 *
	 * @since 9.0
	 *
	 * @param array $attributes Key-value pairs for data attributes.
	 * @return string HTML data attributes string.
	 */
	protected function build_data_attributes( array $attributes ) {
		$attr_string = '';

		foreach ( $attributes as $key => $val ) {
			$attr_string .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $val ) . '"';
		}

		return $attr_string;
	}
}
