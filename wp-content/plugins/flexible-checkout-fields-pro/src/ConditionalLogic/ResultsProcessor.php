<?php

namespace WPDesk\FCF\Pro\ConditionalLogic;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Adjusts checkout fields definitions to the frontend conditional logic results.
 */
class ResultsProcessor implements Hookable {

	/**
	 * Conditional logic results.
	 *
	 * @var array<string, array<string, boolean>>
	 */
	private $results;

	/**
	 * This flag prevents adding the field multiple times.
	 *
	 * @var bool
	 */
	private $is_field_added = false;

	/**
	 * ID of hiden field to store conditional logic results.
	 */
	public const RESULTS_STORAGE_FIELD_ID = 'logic_results_storage';


	/**
	 * Woo checkout templates can be overridden in the theme.
	 * Some of those templates do not have all the defualt actions fired.
	 * Lets not rely on the specific one.
	 *
	 * @var array<string>
	 */
	public const RESULTS_STORAGE_FIELD_FALLBACK_HOOKS = [
		'woocommerce_after_order_notes',
		'woocommerce_checkout_before_customer_details',
		'woocommerce_checkout_billing',
		'woocommerce_checkout_shipping',
		'woocommerce_checkout_before_order_review_heading',
		'woocommerce_checkout_before_order_review',
		'woocommerce_checkout_after_order_review',
	];


	public function hooks() {
		add_action( 'woocommerce_checkout_process', [ $this, 'setup_results' ] );
		add_action( 'woocommerce_checkout_create_order', [ $this, 'checkout_create_order' ], 10, 1 );
		add_filter( 'woocommerce_checkout_fields', [ $this, 'prepare_checkout_fields_for_validation' ], 999999, 1 );
		add_filter( 'flexible_checkout_fields/display/get_checkout_fields', [ $this, 'get_checkout_fields_for_display' ], 10, 3 );
		$this->hook_add_results_field();
	}

	/**
	 * Store conditional logic results as order meta
	 *
	 * @param \WC_Order $order
	 *
	 * @return void
	 */
	public function checkout_create_order( $order ) {
		$order->update_meta_data( self::RESULTS_STORAGE_FIELD_ID, $this->results );
	}

	/**
	 * Filter out fields that should not be displayed further
	 * (e.g. fields that were hidden on the checkout).
	 *
	 * @param array<string, array<string,mixed>> $fields
	 * @param string $section_name
	 * @param \WC_Order $order
	 *
	 * @return array<string, array<string,mixed>>
	 */
	public function get_checkout_fields_for_display( $fields, $section_name, $order ) {
		if ( ! $order instanceof \WC_Order ) {
			return $fields;
		}

		$this->results = wpdesk_get_order_meta( $order, self::RESULTS_STORAGE_FIELD_ID, true );

		if ( is_null( $section_name ) ) {
			return $this->prepare_checkout_fields_for_validation( $fields );
		}

		$fields[ $section_name ] = $fields;

		return $this->prepare_checkout_fields_for_validation( $fields )[ $section_name ];
	}


	/**
	 * Hook to add results field.
	 */
	private function hook_add_results_field(): void {
		foreach ( self::RESULTS_STORAGE_FIELD_FALLBACK_HOOKS as $hook ) {
			\add_action( $hook, [ $this, 'add_results_field' ], 10 );
		}
	}

	/**
	 * Add results hidden field to the checkout.
	 * Used to store conditional logic results for the backend.
	 *
	 * @return void
	 */
	public function add_results_field() {
		if ( $this->is_field_added ) {
			return;
		}

		$value = isset( $_POST[ self::RESULTS_STORAGE_FIELD_ID ] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? \wc_clean( \wp_unslash( $_POST[ self::RESULTS_STORAGE_FIELD_ID ] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: '';

		// some compatibility issues arise when we use woocommerce_form_field() to add the field.
		// field was added twice, becouse of woocommerce_form_field_hidden filter, lets add it manually.
		printf(
			'<input type="hidden" class="input-hidden " name="%s" id="%s" value="%s" />',
			\esc_attr( self::RESULTS_STORAGE_FIELD_ID ),
			\esc_attr( self::RESULTS_STORAGE_FIELD_ID ),
			\esc_attr( $value )
		);

		$this->is_field_added = true;
	}


	/**
	 * Sets up the result storage for the class.
	 *
	 * @return void
	 */
	public function setup_results() {
		if ( isset( $_POST[ self::RESULTS_STORAGE_FIELD_ID ] ) && $_POST[ self::RESULTS_STORAGE_FIELD_ID ] !== '' ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$this->results = \json_decode( \urldecode( \wp_unslash( $_POST[ self::RESULTS_STORAGE_FIELD_ID ] ) ), true ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			if ( json_last_error() !== JSON_ERROR_NONE ) {
				\wc_add_notice( 'Invalid JSON in ' . self::RESULTS_STORAGE_FIELD_ID, 'error' );
			}
		}
	}

	/**
	 * Prepare the checkout fields for validation, based on conditional logic results.
	 *
	 * @param array<string, mixed> $fields The checkout fields definition array.
	 * @return array<string, mixed> The checkout fields definition array ajusted for conditional logic results.
	 */
	public function prepare_checkout_fields_for_validation( $fields ) {
		if ( ! is_array( $this->results ) || count( $this->results ) === 0 ) {
			return $fields;
		}

		foreach ( $fields as $section_name => $section ) {
			foreach ( $section as $field_name => $field ) {
				if ( ! isset( $this->results[ $field_name ] ) || ! is_array( $this->results[ $field_name ] ) ) {
					continue;
				}
				foreach ( $this->results[ $field_name ] as $action => $value ) {
					switch ( $action ) {
						case 'required':
							$fields[ $section_name ][ $field_name ]['required'] = (bool) $value;
							break;
						case 'hide':
							if ( true === (bool) $value ) {
								unset( $fields[ $section_name ][ $field_name ] );
							}
							break;
						case 'show':
							if ( false === (bool) $value ) {
								unset( $fields[ $section_name ][ $field_name ] );
							}
							break;
					}
				}
			}
		}

		return $fields;
	}
}
