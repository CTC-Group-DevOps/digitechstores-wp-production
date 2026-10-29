<?php

use WCML\Multicurrency\StripeConnect\Connect;
use WCML\Multicurrency\StripeConnect\Mode;
use WCML\Multicurrency\StripeConnect\Storage;

class WCML_Payment_Gateway_Stripe extends WCML_Payment_Gateway {

	const ID = 'stripe';

	private $default_secret_key = null;

	public function get_output_model() {
		return [
			'id'            => $this->get_id(),
			'title'         => $this->get_title(),
			'isSupported'   => true,
			'supportsOAuth' => Connect::isAvailable(),
			'settings'      => $this->get_currencies_details(),
			'tooltip'       => '',
			'strings'       => [
				/* translators: Column heading and field label for the currency of an order, in the WooCommerce analytics reports, the payment-gateway settings and the plugin's multicurrency screen. Noun, singular. */
				'labelCurrency'           => __( 'Currency', 'woocommerce-multilingual' ),
				/* translators: Label of the field holding the Stripe publishable key used on the live shop for this currency. */
				'labelLivePublishableKey' => __( 'Live Publishable Key', 'woocommerce-multilingual' ),
				/* translators: Label of the field holding the Stripe secret key used on the live shop for this currency. */
				'labelLiveSecretKey'      => __( 'Live Secret Key', 'woocommerce-multilingual' ),
				/* translators: Label of the field holding the Stripe publishable key used in test mode for this currency. */
				'labelTestPublishableKey' => __( 'Test Publishable Key', 'woocommerce-multilingual' ),
				/* translators: Label of the field holding the Stripe secret key used in test mode for this currency. */
				'labelTestSecretKey'      => __( 'Test Secret Key', 'woocommerce-multilingual' ),
				/* translators: Button label. The merchant clicks it to connect a Stripe account to this currency for the live shop. */
				'labelConnect'            => __( 'Connect with Stripe', 'woocommerce-multilingual' ),
				/* translators: Button label. The merchant clicks it to connect a Stripe test account to this currency. */
				'labelConnectTest'        => __( 'Connect a test account', 'woocommerce-multilingual' ),
				/* translators: Button label. The currency already has a live Stripe account, and the merchant clicks it to connect a different one. */
				'labelReconnect'          => __( 'Reconnect with Stripe', 'woocommerce-multilingual' ),
				/* translators: Button label. The currency already has a Stripe test account, and the merchant clicks it to connect a different one. */
				'labelReconnectTest'      => __( 'Reconnect the test account', 'woocommerce-multilingual' ),
				/* translators: Status text. It tells the merchant that this currency has a Stripe account. */
				'labelConnected'          => __( 'Connected with Stripe', 'woocommerce-multilingual' ),
				/* translators: Confirmation message. WCML shows it before it sends the merchant to Stripe, because unsaved changes on the screen are lost. */
				'confirmLeave'            => __( 'You will continue on Stripe. Save your changes first. Anything unsaved on this screen is lost.', 'woocommerce-multilingual' ),
				/* translators: Error message. WCML shows it when the connection to Stripe does not complete. */
				'connectError'            => __( 'Stripe could not be connected. Please try again.', 'woocommerce-multilingual' ),
			],
		];
	}

	public function save_setting( $key, $value ) {
		if ( is_array( $value ) ) {
			$value = Storage::preserveServerOnlyKeys( (array) $this->get_setting( $key ), $value );
		}

		parent::save_setting( $key, $value );
	}

	public function add_hooks() {
		$client_currency = $this->woocommerce_wpml->multi_currency->get_client_currency();

		if ( $this->is_client_currency_supported( $client_currency ) ) {
			add_filter( 'wc_stripe_generate_create_intent_request', [
				$this,
				'convert_stripe_payment_request'
			], 10, 3 );
		}
	}

	public function add_order_hooks() {
		add_filter( 'wc_stripe_refund_request', [ $this, 'convert_stripe_refund_request' ], 10, 2 );
	}

	public function convert_stripe_refund_request( $request, $order ) {
		if (
			! is_array( $request )
			|| ! isset( $request['amount'] )
			|| ! $order instanceof WC_Order
			|| ! class_exists( \WC_Stripe_Helper::class )
			|| ! class_exists( \WC_Stripe_Exception::class )
		) {
			return $request;
		}

		$this->use_the_account_that_took_the_payment( $order->get_currency() );

		$pinned_refund       = new WCML_Pinned_Refund( $this, $this->woocommerce_wpml );
		$settlement_currency = $pinned_refund->get_settlement_currency( $order );

		if ( null === $settlement_currency ) {
			return $request;
		}

		$order_currency = $order->get_currency();
		$capture        = $this->read_capture_record( $request['charge'] ?? '', $order );

		$amount = $pinned_refund->convert(
			[
				'amount'         => $request['amount'],
				'order_total'    => WC_Stripe_Helper::get_stripe_amount( $order->get_total(), $order_currency ),
				'refunded_total' => $pinned_refund->get_amount_refunded_through_the_gateway( $order, [ \WC_Stripe_Helper::class, 'get_stripe_amount' ] ) + $request['amount'],
				'currency'       => $order_currency,
				'order_id'       => $order->get_id(),
				'capture'        => $capture,
				'to_major'       => [ \WC_Stripe_Helper::class, 'convert_from_stripe_amount' ],
				'to_minor'       => [ \WC_Stripe_Helper::class, 'get_stripe_amount' ],
			],
			$settlement_currency
		);

		$this->refuse_a_zero_refund( $amount, $request, $order, $settlement_currency, $capture );

		$request['amount'] = $amount;

		return $request;
	}

	private function refuse_a_zero_refund( $amount, array $request, $order, $settlement_currency, $capture ) {
		if ( $amount > 0 || $request['amount'] <= 0 ) {
			return;
		}

		if ( is_array( $capture ) && $capture['amount'] - $capture['refunded'] <= 0 ) {
			$log     = sprintf(
				'Order %1$s: the refund was stopped because Stripe holds nothing left of charge %2$s (captured %3$s, already refunded %4$s). Nothing was sent (wcml-5546).',
				$order->get_id(),
				$request['charge'] ?? '',
				$capture['amount'],
				$capture['refunded']
			);
			$message = __( 'This payment is already refunded in full on Stripe, so no refund was sent. Record the refund manually in WooCommerce instead.', 'woocommerce-multilingual' );
		} elseif ( is_array( $capture ) ) {
			$log     = sprintf(
				'Order %1$s: the refund was stopped because %2$s %3$s is too small: its share of the %4$s capture rounds to nothing. Nothing was sent (wcml-5546).',
				$order->get_id(),
				$order->get_currency(),
				$request['amount'],
				$settlement_currency
			);
			$message = __( 'The refund amount is too small to send to Stripe in the currency of the payment, so no refund was sent. Refund a larger amount, or record the refund manually in WooCommerce.', 'woocommerce-multilingual' );
		} else {
			$log     = sprintf(
				'Order %1$s: the refund was stopped because %2$s %3$s worked out to nothing in %4$s. An exchange rate for %2$s is the first thing to check. Nothing was sent, so the refund can be retried (wcml-5546).',
				$order->get_id(),
				$order->get_currency(),
				$request['amount'],
				$settlement_currency
			);
			$message = __( 'WPML could not work out how much to refund on this order, so no refund was sent. Check the exchange rate for the order\'s currency in WPML\'s multicurrency settings, then try the refund again.', 'woocommerce-multilingual' );
		}

		wc_get_logger()->error( $log, [ 'source' => WCML_Pinned_Refund::LOG_SOURCE ] );

		throw new WC_Stripe_Exception( esc_html( $message ), esc_html( $message ) );
	}

	protected function read_capture_record( $charge_id, $order ) {
		if ( ! $charge_id || ! class_exists( \WC_Stripe_API::class ) ) {
			return null;
		}

		$charge = WC_Stripe_API::retrieve( 'charges/' . $charge_id );

		if ( ! is_object( $charge ) || ! empty( $charge->error ) || ! isset( $charge->amount ) || ! is_numeric( $charge->amount ) ) {
			$this->log_unreadable_capture( $charge_id, $order, $charge );

			$message = __( 'WPML could not read this payment from Stripe, so no refund was sent. Please try the refund again.', 'woocommerce-multilingual' );

			throw new WC_Stripe_Exception( esc_html( $message ), esc_html( $message ) );
		}

		return [
			'amount'   => $this->get_captured_amount( $charge ),
			'refunded' => isset( $charge->amount_refunded ) && is_numeric( $charge->amount_refunded )
				? (int) $charge->amount_refunded
				: 0,
		];
	}

	private function get_captured_amount( $charge ) {
		if ( isset( $charge->amount_captured ) && is_numeric( $charge->amount_captured ) ) {
			return (int) $charge->amount_captured;
		}

		return (int) $charge->amount;
	}

	private function use_the_account_that_took_the_payment( $order_currency ) {
		if ( ! class_exists( \WC_Stripe_API::class ) ) {
			return;
		}

		$secret_key = $this->get_currency_secret_key( $order_currency );

		if ( '' === $secret_key ) {
			$this->restore_the_default_stripe_account();

			return;
		}

		if ( null === $this->default_secret_key ) {
			$this->default_secret_key = (string) WC_Stripe_API::get_secret_key();

			add_action( 'shutdown', [ $this, 'restore_the_default_stripe_account' ] );
		}

		WC_Stripe_API::set_secret_key( $secret_key );
	}

	public function restore_the_default_stripe_account() {
		if ( null === $this->default_secret_key ) {
			return;
		}

		WC_Stripe_API::set_secret_key( $this->default_secret_key );

		$this->default_secret_key = null;
	}

	private function get_currency_secret_key( $order_currency ) {
		$setting = $this->get_setting( $order_currency );

		if ( ! is_array( $setting ) ) {
			return '';
		}

		$mode = $this->get_account_mode();

		if ( ! Storage::hasKeys( $setting, $mode ) ) {
			return '';
		}

		return (string) $setting[ Mode::key( Storage::SECRET_KEY, $mode ) ];
	}

	private function get_account_mode() {
		if ( class_exists( \WC_Stripe_Mode::class ) ) {
			return WC_Stripe_Mode::is_test() ? Mode::TEST : Mode::LIVE;
		}

		return 'yes' === ( $this->get_gateway()->settings['testmode'] ?? 'no' ) ? Mode::TEST : Mode::LIVE;
	}

	private function log_unreadable_capture( $charge_id, $order, $charge ) {
		$error = is_object( $charge ) && isset( $charge->error->message ) ? $charge->error->message : 'no charge returned';

		wc_get_logger()->error(
			sprintf(
				'Order %1$s: the refund was stopped because Stripe did not answer the charge %2$s it was to be taken from (%3$s). Nothing was sent, so the refund can be retried (wcml-5546).',
				$order->get_id(),
				$charge_id,
				$error
			),
			[ 'source' => WCML_Pinned_Refund::LOG_SOURCE ]
		);
	}

	public function convert_stripe_payment_request( $request, $order, $source ) {
		$client_currency = $request ['currency'] ?? null;
		$client_currency = strtoupper( $client_currency );

		$convert_to_currency = $this->maybe_convert_currency( $client_currency );
		if ( null === $convert_to_currency ) {
			return $request;
		}

		if ( $client_currency !== $convert_to_currency ) {
			$convert_price = $this->get_convert_price_callable( $convert_to_currency );

			$request = $convert_price( $request );

			$this->record_the_payment( $order, $convert_to_currency );
		}

		return $request;
	}

	public function get_currencies_details() {
		$currencies_details = [];
		$default_currency   = wcml_get_woocommerce_currency_option();
		$active_currencies  = get_woocommerce_currencies();

		foreach ( $active_currencies as $code => $currency ) {

			if ( $default_currency === $code ) {
				$currencies_details[ $code ]['currency']             = $code;
				$currencies_details[ $code ]['publishable_key']      = $this->get_gateway()->settings['publishable_key'];
				$currencies_details[ $code ]['secret_key']           = $this->get_gateway()->settings['secret_key'];
				$currencies_details[ $code ]['test_publishable_key'] = $this->get_gateway()->settings['test_publishable_key'];
				$currencies_details[ $code ]['test_secret_key']      = $this->get_gateway()->settings['test_secret_key'];
				$currencies_details[ $code ]['connection_type']      = $this->get_gateway()->settings['connection_type'] ?? '';
				$currencies_details[ $code ]['test_connection_type'] = $this->get_gateway()->settings['test_connection_type'] ?? '';
			} else {
				$currency_gateway_setting                            = $this->get_setting( $code );
				$currencies_details[ $code ]['currency']             = $currency_gateway_setting['currency'] ?? '';
				$currencies_details[ $code ]['publishable_key']      = $currency_gateway_setting['publishable_key'] ?? '';
				$currencies_details[ $code ]['secret_key']           = $currency_gateway_setting['secret_key'] ?? '';
				$currencies_details[ $code ]['test_publishable_key'] = $currency_gateway_setting['test_publishable_key'] ?? '';
				$currencies_details[ $code ]['test_secret_key']      = $currency_gateway_setting['test_secret_key'] ?? '';
				$currencies_details[ $code ]['connection_type']      = $currency_gateway_setting['connection_type'] ?? '';
				$currencies_details[ $code ]['test_connection_type'] = $currency_gateway_setting['test_connection_type'] ?? '';
			}
		}

		return $currencies_details;

	}

	public static function filter_stripe_settings( $settings ) {
		if ( is_admin() ) {
			return $settings;
		}

		global $woocommerce_wpml;

		$client_currency  = $woocommerce_wpml->multi_currency->get_client_currency();
		$gateway_settings = get_option( self::OPTION_KEY . self::ID, [] );

		if ( $gateway_settings && isset( $gateway_settings[ $client_currency ] ) ) {
			$gateway_setting = (array) $gateway_settings[ $client_currency ];

			$account_keys = [
				Storage::PUBLISHABLE_KEY,
				Storage::SECRET_KEY,
				Storage::CONNECTION_TYPE,
				Storage::REFRESH_TOKEN,
			];

			foreach ( Mode::all() as $mode ) {
				if ( ! Storage::hasKeys( $gateway_setting, $mode ) ) {
					continue;
				}

				foreach ( $account_keys as $key ) {
					$settings[ Mode::key( $key, $mode ) ] = $gateway_setting[ Mode::key( $key, $mode ) ] ?? '';
				}
			}
		}

		return $settings;
	}

	private function get_convert_price_callable( $convert_to_currency ): callable {
		return function ( array $price_params ) use ( $convert_to_currency ): array {
			$value    = $price_params['amount'];
			$currency = strtoupper( $price_params['currency'] );

			$price_default   = $this->woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $value, $currency );
			$price_converted = $this->woocommerce_wpml->multi_currency->prices->convert_price_amount( $price_default, $convert_to_currency );

			$price_params['amount']   = (int) $price_converted;
			$price_params['currency'] = strtolower( $convert_to_currency );

			return $price_params;
		};
	}

	private function maybe_convert_currency( string $client_currency ) {
		$gateway_setting = $this->get_setting( $client_currency );

		return $gateway_setting['currency'] ?? null;
	}

	private function is_client_currency_supported( string $client_currency ): bool {
		$newCurrency = $this->maybe_convert_currency( $client_currency );

		if ( null === $newCurrency ) {
			return false;
		}

		return true;
	}
}
