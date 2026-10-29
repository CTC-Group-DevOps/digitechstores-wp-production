<?php

class WCML_PayPal_Refund_Notice {

	const FEES_META = '_ppcp_paypal_fees';

	const REFUNDS_FEATURE = 'refunds';

	private $gateway;

	private $woocommerce_wpml;

	private $order_in_context;

	public function __construct( WCML_Payment_Gateway $gateway, woocommerce_wpml $woocommerce_wpml ) {
		$this->gateway          = $gateway;
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {
		add_filter( 'woocommerce_payment_gateway_supports', [ $this, 'hide_the_gateway_refund' ], 10, 3 );
		add_action( 'woocommerce_admin_order_data_after_order_details', [ $this, 'show_on_the_order_screen' ] );
		add_action( 'woocommerce_create_refund', [ $this, 'remember_the_order_being_refunded' ], 10, 2 );
	}

	public function show_on_the_order_screen( $order ) {
		$this->remember_the_order( $order );
		$this->say_where_the_refund_has_to_be_made( $order );
	}

	public function remember_the_order( $order ) {
		$this->order_in_context = $order instanceof WC_Order ? $order : null;
	}

	public function remember_the_order_being_refunded( $refund, $args ) {
		$order_id = is_array( $args ) && isset( $args['order_id'] ) ? absint( $args['order_id'] ) : 0;

		$this->remember_the_order( $order_id ? wc_get_order( $order_id ) : null );
	}

	public function hide_the_gateway_refund( $supports, $feature, $gateway ) {
		if (
			true !== $supports
			|| self::REFUNDS_FEATURE !== $feature
			|| ! $gateway instanceof WC_Payment_Gateway
			|| $this->gateway->get_id() !== $gateway->id
		) {
			return $supports;
		}

		$order = $this->order_in_context;

		return $order && $this->get_settlement_currency( $order ) ? false : $supports;
	}

	public function say_where_the_refund_has_to_be_made( $order ) {
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		$settlement_currency = $this->get_settlement_currency( $order );

		if ( ! $settlement_currency ) {
			return;
		}

		printf(
			'<div class="clear"></div><div class="notice notice-warning inline wcml-paypal-refund-notice"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: %1$s is the currency PayPal took the payment in, for example USD. %2$s is the currency of the order, for example AED. */
					__( 'PayPal took this payment in %1$s, and WooCommerce can only send a refund in %2$s. Refund this order in your PayPal account, then record it here with "Refund manually".', 'woocommerce-multilingual' ),
					$settlement_currency,
					$order->get_currency()
				)
			)
		);
	}

	private function get_settlement_currency( WC_Order $order ) {
		if ( $this->gateway->get_id() !== $order->get_payment_method() ) {
			return null;
		}

		$charged_in = $this->get_currency_paypal_charged( $order );

		if ( $charged_in ) {
			return $charged_in === $order->get_currency() ? null : $charged_in;
		}

		$pinned_refund = new WCML_Pinned_Refund( $this->gateway, $this->woocommerce_wpml );

		return $pinned_refund->get_settlement_currency( $order );
	}

	private function get_currency_paypal_charged( WC_Order $order ) {
		$fees = $order->get_meta( self::FEES_META );

		if ( ! is_array( $fees ) ) {
			return '';
		}

		return (string) ( $fees['gross_amount']['currency_code'] ?? '' );
	}
}
