<?php

class WCML_Pinned_Refund {

	const LOG_SOURCE = 'wcml';

	const SETTLEMENT_CURRENCY_META = '_wcml_settlement_currency';

	const SETTLED_TOTAL_META = '_wcml_settled_total';

	private $gateway;

	private $woocommerce_wpml;

	public function __construct( WCML_Payment_Gateway $gateway, woocommerce_wpml $woocommerce_wpml ) {
		$this->gateway          = $gateway;
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function get_settlement_currency( $order ) {
		$order_currency = $order->get_currency();

		if ( ! $order_currency ) {
			return null;
		}

		$recorded = $order->get_meta( self::SETTLEMENT_CURRENCY_META );

		if ( is_string( $recorded ) && '' !== $recorded ) {
			return $order_currency === $recorded ? null : $recorded;
		}

		return $this->get_settlement_currency_from_settings( $order_currency );
	}

	private function get_settlement_currency_from_settings( $order_currency ) {
		if ( wcml_get_woocommerce_currency_option() === $order_currency ) {
			return null;
		}

		if ( ! $this->woocommerce_wpml->multi_currency->currencies_payment_gateways->is_enabled( $order_currency ) ) {
			return null;
		}

		$gateway_setting = $this->gateway->get_setting( $order_currency );

		if (
			! is_array( $gateway_setting )
			|| empty( $gateway_setting['currency'] )
			|| $order_currency === $gateway_setting['currency']
		) {
			return null;
		}

		return $gateway_setting['currency'];
	}

	public function convert( array $refund, $settlement_currency ) {
		$capture = $refund['capture'] ?? null;

		if ( is_array( $capture ) && $refund['order_total'] > 0 ) {
			return $this->get_proportional_amount( $capture, $refund );
		}

		return $this->convert_at_current_rates( $refund, $settlement_currency );
	}

	private function get_proportional_amount( array $capture, array $refund ) {
		$remaining = max( 0, $capture['amount'] - $capture['refunded'] );

		if ( $this->completes_the_order( $refund ) ) {
			return (int) $remaining;
		}

		$proportional = (int) round(
			$capture['amount'] * ( $refund['amount'] / $refund['order_total'] ),
			0,
			PHP_ROUND_HALF_UP
		);

		return (int) min( $proportional, $remaining );
	}

	public function get_amount_refunded_through_the_gateway( $order, callable $to_minor ) {
		$refunded = 0;

		foreach ( $order->get_refunds() as $refund ) {
			if ( $refund->get_refunded_payment() ) {
				$refunded += (float) $refund->get_amount();
			}
		}

		return (int) call_user_func( $to_minor, $refunded, $order->get_currency() );
	}

	public function completes_the_order( array $refund ) {
		return $refund['refunded_total'] >= $refund['order_total'];
	}

	private function convert_at_current_rates( array $refund, $settlement_currency ) {
		$prices = $this->woocommerce_wpml->multi_currency->prices;

		$amount_in_order_currency = call_user_func( $refund['to_major'], $refund['amount'], $refund['currency'] );
		$amount_in_default        = $prices->unconvert_price_amount( $amount_in_order_currency, $refund['currency'] );
		$amount_settled           = $prices->convert_price_amount( $amount_in_default, $settlement_currency );

		$this->log_missing_capture_record( $refund, $settlement_currency );

		return (int) call_user_func( $refund['to_minor'], $amount_settled, $settlement_currency );
	}

	private function log_missing_capture_record( array $refund, $settlement_currency ) {
		wc_get_logger()->warning(
			sprintf(
				'Order %1$s: the refund was converted from %2$s to %3$s at today\'s exchange rate, because the gateway exposes no record of what it captured. The amount the gateway receives can differ from the share of the capture the order is refunding (wcml-5546).',
				$refund['order_id'] ?? '',
				$refund['currency'] ?? '',
				$settlement_currency
			),
			[ 'source' => self::LOG_SOURCE ]
		);
	}
}
