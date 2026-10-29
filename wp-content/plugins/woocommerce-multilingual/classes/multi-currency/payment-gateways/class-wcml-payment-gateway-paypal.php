<?php

class WCML_Payment_Gateway_PayPal extends WCML_Payment_Gateway {

	const ID = 'paypal';

	const CART_AMOUNT_ARGS = [ 'tax_cart', 'discount_amount_cart', 'shipping_1' ];

	const MINOR_UNIT = 100;

	const NVP_SANDBOX_ENDPOINT = 'https://api-3t.sandbox.paypal.com/nvp';
	const NVP_LIVE_ENDPOINT    = 'https://api-3t.paypal.com/nvp';

	const SUPPORTED_CURRENCIES = [
		'AUD',
		'BRL',
		'CAD',
		'MXN',
		'NZD',
		'HKD',
		'SGD',
		'USD',
		'EUR',
		'JPY',
		'TRY',
		'NOK',
		'CZK',
		'DKK',
		'HUF',
		'ILS',
		'MYR',
		'PHP',
		'PLN',
		'SEK',
		'CHF',
		'TWD',
		'THB',
		'GBP',
		'RMB',
		'RUB',
		'INR',
	];

	public function get_output_model() {
		return [
			'id'          => $this->get_id(),
			'title'       => $this->get_title(),
			'isSupported' => true,
			'settings'    => $this->get_currencies_details(),
			'tooltip'     => '',
			'strings'     => [
				/* translators: Column heading and field label for the currency of an order, in the WooCommerce analytics reports, the payment-gateway settings and the plugin's multicurrency screen. Noun, singular. */
				'labelCurrency'       => __( 'Currency', 'woocommerce-multilingual' ),
				/* translators: Label of the field holding the PayPal address that receives payments in this currency. */
				'labelPayPalEmail'    => __( 'PayPal Email', 'woocommerce-multilingual' ),
				// translators: %s is currency code.
				'tooltipNotSupported' => __( 'This gateway does not support %s. To show this gateway please select another currency.', 'woocommerce-multilingual' ),
			],
		];
	}

	public function is_valid_for_use( $currency ) {

		$filter_removed = remove_filter( 'woocommerce_paypal_supported_currencies', [ 'WCML_Payment_Gateway_PayPal', 'filter_supported_currencies' ] );

		$is_valid = in_array(
			$currency,
			apply_filters(
				'woocommerce_paypal_supported_currencies',
				self::SUPPORTED_CURRENCIES
			),
			true
		);

		if ( $filter_removed ) {
			add_filter( 'woocommerce_paypal_supported_currencies', [ 'WCML_Payment_Gateway_PayPal', 'filter_supported_currencies' ] );
		}

		return $is_valid;
	}

	public function get_currencies_details() {

		$currencies_details     = [];
		$default_currency       = wcml_get_woocommerce_currency_option();
		$woocommerce_currencies = get_woocommerce_currencies();

		foreach ( $woocommerce_currencies as $code => $currency ) {

			if ( $default_currency === $code ) {
				$currencies_details[ $code ]['value']    = $this->get_gateway()->settings['email'] ?? '';
				$currencies_details[ $code ]['currency'] = $code;
				$currencies_details[ $code ]['isValid']  = $this->is_valid_for_use( $default_currency );
			} else {
				$currency_gateway_setting                = $this->get_setting( $code );
				$currencies_details[ $code ]['value']    = $currency_gateway_setting ? $currency_gateway_setting['value'] : '';
				$currencies_details[ $code ]['currency'] = $currency_gateway_setting ? $currency_gateway_setting['currency'] : $code;
				$currencies_details[ $code ]['isValid']  = $this->is_valid_for_use( $code );
			}
		}

		return $currencies_details;

	}

	public function add_hooks() {
		add_filter( 'woocommerce_paypal_args', [ $this, 'filter_paypal_args' ], 10, 2 );
	}

	public function filter_paypal_args( $args, $order ) {
		if ( ! is_array( $args ) || ! $order instanceof WC_Order ) {
			return $args;
		}

		$order_currency  = $order->get_currency();
		$gateway_setting = $this->get_setting( $order_currency );

		if ( ! is_array( $gateway_setting ) ) {
			return $args;
		}

		if ( ! empty( $gateway_setting['value'] ) ) {
			$args['business'] = $gateway_setting['value'];
		}

		$settlement_currency = $gateway_setting['currency'] ?? '';

		if ( ! $settlement_currency || $order_currency === $settlement_currency ) {
			return $args;
		}

		$args                  = $this->convert_the_amounts( $args, $order_currency, $settlement_currency );
		$args['currency_code'] = $settlement_currency;

		$this->record_the_payment( $order, $settlement_currency, $this->get_the_total_asked_for( $args, $settlement_currency ) );

		return $args;
	}

	private function get_the_total_asked_for( array $args, $settlement_currency ) {
		$total = 0.0;

		foreach ( $args as $key => $value ) {
			if ( 1 === preg_match( '/^amount_(\d+)$/', $key, $line ) ) {
				$total += (float) $value * (int) ( $args[ 'quantity_' . $line[1] ] ?? 1 );
			}
		}

		$total += (float) ( $args['tax_cart'] ?? 0 );
		$total += (float) ( $args['shipping_1'] ?? 0 );
		$total -= (float) ( $args['discount_amount_cart'] ?? 0 );

		return round( $total, $this->get_currency_decimals( $settlement_currency ) );
	}

	private function convert_the_amounts( array $args, $order_currency, $settlement_currency ) {
		$decimals = $this->get_currency_decimals( $settlement_currency );

		foreach ( $args as $key => $value ) {
			if ( ! $this->is_an_amount( $key ) || ! is_numeric( $value ) ) {
				continue;
			}

			$args[ $key ] = number_format(
				(float) $this->convert_amount( $value, $order_currency, $settlement_currency ),
				$decimals,
				'.',
				''
			);
		}

		return $args;
	}

	private function is_an_amount( $key ) {
		return in_array( $key, self::CART_AMOUNT_ARGS, true ) || 1 === preg_match( '/^amount_\d+$/', $key );
	}

	private function convert_amount( $amount, $order_currency, $settlement_currency ) {
		$prices = $this->woocommerce_wpml->multi_currency->prices;

		return $prices->convert_price_amount(
			$prices->unconvert_price_amount( $amount, $order_currency ),
			$settlement_currency
		);
	}

	private function get_currency_decimals( $currency ) {
		$details = $this->woocommerce_wpml->multi_currency->get_currency_details_by_code( $currency );

		return isset( $details['num_decimals'] ) ? (int) $details['num_decimals'] : wc_get_price_decimals();
	}

	public function add_order_hooks() {
		add_filter( 'woocommerce_paypal_refund_request', [ $this, 'convert_paypal_refund_request' ], 10, 3 );

		$payment_check = new WCML_PayPal_Standard_Payment_Check( $this, $this->woocommerce_wpml );

		$payment_check->add_hooks();
	}

	public function convert_paypal_refund_request( $request, $order, $amount ) {
		if ( ! is_array( $request ) || ! $order instanceof WC_Order || null === $amount ) {
			return $request;
		}

		$pinned_refund       = new WCML_Pinned_Refund( $this, $this->woocommerce_wpml );
		$settlement_currency = $pinned_refund->get_settlement_currency( $order );

		if ( null === $settlement_currency ) {
			return $request;
		}

		$refund_amount = self::to_minor( $amount );
		$refund        = [
			'amount'         => $refund_amount,
			'order_total'    => self::to_minor( $order->get_total() ),
			'refunded_total' => $pinned_refund->get_amount_refunded_through_the_gateway( $order, [ self::class, 'to_minor' ] ) + $refund_amount,
			'currency'       => $order->get_currency(),
			'order_id'       => $order->get_id(),
			'capture'        => null,
			'to_major'       => [ self::class, 'to_major' ],
			'to_minor'       => [ self::class, 'to_minor' ],
		];

		if ( $pinned_refund->completes_the_order( $refund ) ) {
			unset( $request['AMT'], $request['CURRENCYCODE'] );

			$request['REFUNDTYPE'] = 'Full';

			return $request;
		}

		$refund['capture'] = $this->read_capture_record( $request, $order );

		$settled = $pinned_refund->convert( $refund, $settlement_currency );

		$this->refuse_a_zero_refund( $settled, $refund, $settlement_currency );

		$request['AMT']          = number_format( self::to_major( $settled ), 2, '.', '' );
		$request['CURRENCYCODE'] = $refund['capture']['currency'] ?? $settlement_currency;
		$request['REFUNDTYPE']   = 'Partial';

		return $request;
	}

	private function read_capture_record( array $request, $order ) {
		$transaction_id = $request['TRANSACTIONID'] ?? '';

		if ( ! $transaction_id ) {
			return null;
		}

		$transaction = $this->ask_paypal_about_the_transaction( $request, $transaction_id );

		if ( ! isset( $transaction['AMT'], $transaction['CURRENCYCODE'] ) || ! is_numeric( $transaction['AMT'] ) ) {
			$this->log_unreadable_capture( $transaction_id, $order, $transaction );

			throw new Exception( esc_html__( 'WPML could not read this payment from PayPal, so no refund was sent. Please try the refund again.', 'woocommerce-multilingual' ) );
		}

		return [
			'amount'   => self::to_minor( $transaction['AMT'] ),
			'refunded' => 0,
			'currency' => (string) $transaction['CURRENCYCODE'],
		];
	}

	private function ask_paypal_about_the_transaction( array $request, $transaction_id ) {
		$response = wp_safe_remote_post(
			WC_Gateway_Paypal_API_Handler::$sandbox ? self::NVP_SANDBOX_ENDPOINT : self::NVP_LIVE_ENDPOINT,
			[
				'body'        => [
					'VERSION'       => $request['VERSION'] ?? '',
					'USER'          => $request['USER'] ?? '',
					'PWD'           => $request['PWD'] ?? '',
					'SIGNATURE'     => $request['SIGNATURE'] ?? '',
					'METHOD'        => 'GetTransactionDetails',
					'TRANSACTIONID' => $transaction_id,
				],
				'timeout'     => 70,
				'user-agent'  => 'WooCommerce/' . WC()->version,
				'httpversion' => '1.1',
			]
		);

		if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
			return [];
		}

		parse_str( $response['body'], $transaction );

		return (array) $transaction;
	}

	private function refuse_a_zero_refund( $settled, array $refund, $settlement_currency ) {
		if ( $settled > 0 || $refund['amount'] <= 0 ) {
			return;
		}

		if ( is_array( $refund['capture'] ) ) {
			$log     = sprintf(
				'Order %1$s: the refund was stopped because %2$s %3$s is too small: its share of the %4$s payment rounds to nothing. Nothing was sent (wcml-5557).',
				$refund['order_id'],
				$refund['currency'],
				self::to_major( $refund['amount'] ),
				$settlement_currency
			);
			$message = __( 'The refund amount is too small to send to PayPal in the currency of the payment, so no refund was sent. Refund a larger amount, or record the refund manually in WooCommerce.', 'woocommerce-multilingual' );
		} else {
			$log     = sprintf(
				'Order %1$s: the refund was stopped because %2$s %3$s worked out to nothing in %4$s. An exchange rate for %2$s is the first thing to check. Nothing was sent, so the refund can be retried (wcml-5557).',
				$refund['order_id'],
				$refund['currency'],
				self::to_major( $refund['amount'] ),
				$settlement_currency
			);
			$message = __( 'WPML could not work out how much to refund on this order, so no refund was sent. Check the exchange rate for the order\'s currency in WPML\'s multicurrency settings, then try the refund again.', 'woocommerce-multilingual' );
		}

		wc_get_logger()->error( $log, [ 'source' => WCML_Pinned_Refund::LOG_SOURCE ] );

		throw new Exception( esc_html( $message ) );
	}

	private function log_unreadable_capture( $transaction_id, $order, array $transaction ) {
		wc_get_logger()->error(
			sprintf(
				'Order %1$s: the refund was stopped because PayPal did not answer the transaction %2$s it was to be taken from (%3$s). Nothing was sent, so the refund can be retried (wcml-5557).',
				$order->get_id(),
				$transaction_id,
				$transaction['L_LONGMESSAGE0'] ?? 'no transaction returned'
			),
			[ 'source' => WCML_Pinned_Refund::LOG_SOURCE ]
		);
	}

	public static function to_minor( $amount, $currency = '' ) {
		return (int) round( (float) $amount * self::MINOR_UNIT );
	}

	public static function to_major( $amount, $currency = '' ) {
		return (float) $amount / self::MINOR_UNIT;
	}

	public static function filter_supported_currencies( $supported_currencies ) {
		global $woocommerce_wpml;

		$client_currency = $woocommerce_wpml->multi_currency->get_client_currency();

		if ( ! in_array( $client_currency, self::SUPPORTED_CURRENCIES, true ) ) {
			$gateway_settings = get_option( self::OPTION_KEY . self::ID, [] );

			if ( $gateway_settings && isset( $gateway_settings[ $client_currency ] ) ) {

				if ( in_array( $gateway_settings[ $client_currency ]['currency'], self::SUPPORTED_CURRENCIES, true ) ) {
					$supported_currencies[] = $client_currency;
				}
			}
		}

		return $supported_currencies;
	}

}
