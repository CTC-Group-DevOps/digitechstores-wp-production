<?php

abstract class WC_Conditional_Content_Input_Base {

	/**
	 * The type of the field.  This is used to determine which class to use for rendering.
	 *
	 * @var string The type of input field.
	 */
	public string $type = '';

	/**
	 * Default values which will be merged with the field data.
	 *
	 * @var array Default values for the input field.
	 */
	public array $defaults = array();

	/**
	 * Constructor.
	 */
	abstract public function __construct();

	/**
	 * Render the input field.
	 *
	 * @param array $field The field data.
	 * @param null $value The value of the field to be used for rendering.
	 */
	abstract public function render( array $field, $value = null ): void;
}
