<?php

class WCML_PayPal_Standard_Payment_Check {

	const IPN_ACTION = 'valid-paypal-standard-ipn-request';

	const PRIORITY_BEFORE_THE_IPN_HANDLER = 1;

	private $gateway;

	private $woocommerce_wpml;

	private $order_id = 0;

	private $settlement_currency = '';

	private $settled_total = '';

	public function __construct( WCML_Payment_Gateway $gateway, woocommerce_wpml $woocommerce_wpml ) {
		$this->gateway          = $gateway;
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {
		add_action( self::IPN_ACTION, [ $this, 'answer_the_check_as_paypal_sees_it' ], self::PRIORITY_BEFORE_THE_IPN_HANDLER );
	}

	public function answer_the_check_as_paypal_sees_it( $posted ) {
		if ( ! is_array( $posted ) ) {
			return;
		}

		$order = $this->get_the_order_paypal_is_reporting( $posted );

		if ( ! $order ) {
			return;
		}

		$pinned_refund       = new WCML_Pinned_Refund( $this->gateway, $this->woocommerce_wpml );
		$settlement_currency = $pinned_refund->get_settlement_currency( $order );

		if ( null === $settlement_currency || ( $posted['mc_currency'] ?? '' ) !== $settlement_currency ) {
			return;
		}

		$this->order_id            = $order->get_id();
		$this->settlement_currency = $settlement_currency;
		$this->settled_total       = $this->get_the_total_the_gateway_was_asked_for( $order, $settlement_currency );

		add_filter( 'woocommerce_order_get_currency', [ $this, 'say_the_settlement_currency' ], 10, 2 );
		add_filter( 'woocommerce_order_get_total', [ $this, 'say_the_settled_total' ], 10, 2 );
		add_action( 'woocommerce_before_order_object_save', [ $this, 'stop_answering' ] );
	}

	public function say_the_settlement_currency( $currency, $order ) {
		return $this->is_the_order_being_checked( $order ) ? $this->settlement_currency : $currency;
	}

	public function say_the_settled_total( $total, $order ) {
		return $this->is_the_order_being_checked( $order ) ? $this->settled_total : $total;
	}

	public function stop_answering() {
		remove_filter( 'woocommerce_order_get_currency', [ $this, 'say_the_settlement_currency' ], 10 );
		remove_filter( 'woocommerce_order_get_total', [ $this, 'say_the_settled_total' ], 10 );
		remove_action( 'woocommerce_before_order_object_save', [ $this, 'stop_answering' ] );

		$this->order_id = 0;
	}

	private function is_the_order_being_checked( $order ) {
		return $this->order_id > 0 && $order instanceof WC_Order && $this->order_id === $order->get_id();
	}

	private function get_the_order_paypal_is_reporting( array $posted ) {
		$custom = json_decode( (string) ( $posted['custom'] ?? '' ), true );

		if ( ! is_array( $custom ) || empty( $custom['order_id'] ) || empty( $custom['order_key'] ) ) {
			return null;
		}

		$order = wc_get_order( absint( $custom['order_id'] ) );

		if ( ! $order instanceof WC_Order || $order->get_order_key() !== $custom['order_key'] ) {
			return null;
		}

		return $this->gateway->get_id() === $order->get_payment_method() ? $order : null;
	}

	private function get_the_total_the_gateway_was_asked_for( WC_Order $order, $settlement_currency ) {
		$recorded = $order->get_meta( WCML_Pinned_Refund::SETTLED_TOTAL_META );

		if ( is_numeric( $recorded ) ) {
			return (string) $recorded;
		}

		$prices = $this->woocommerce_wpml->multi_currency->prices;

		return (string) $prices->convert_price_amount(
			$prices->unconvert_price_amount( $order->get_total(), $order->get_currency() ),
			$settlement_currency
		);
	}
}
