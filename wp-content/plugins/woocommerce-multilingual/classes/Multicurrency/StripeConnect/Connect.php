<?php

namespace WCML\Multicurrency\StripeConnect;

use WCML\Utilities\AdminNotices;
use WCML\Utilities\AdminPages;
use WCML\Utilities\AdminUrl;
use WP_Error;

class Connect {

	public const ARG_CURRENCY = 'wcml_stripe_currency';
	public const ARG_MODE     = 'wcml_stripe_mode';
	public const NONCE_ACTION = 'wcml_stripe_connect';

	private const ARG_CODE  = 'wcs_stripe_code';
	private const ARG_STATE = 'wcs_stripe_state';
	private const ARG_TYPE  = 'wcs_stripe_type';

	public const TYPE_CONNECT = 'connect';
	public const TYPE_APP     = 'app';

	public const REFRESH_ACTION = 'wcml_stripe_refresh_connection';

	private const STATE_TRANSIENT_PREFIX = 'wcml_stripe_oauth_state_';
	private const STATE_LIFETIME         = 6 * HOUR_IN_SECONDS;

	private const NOTICE_ID     = 'wcml-stripe-connect';
	private const NOTICES_GROUP = 'wcml-multi-currency';

	private const REFRESH_GROUP = 'wcml-stripe-connect';

	private const REFRESH_INTERVAL = 55 * MINUTE_IN_SECONDS;

	private const MAX_REFRESH_ATTEMPTS = 10;

	private $api;

	private $returnUrl;

	public function __construct( $api, $returnUrl ) {
		$this->api       = $api;
		$this->returnUrl = $returnUrl;
	}

	public static function isAvailable() {
		return class_exists( \WC_Stripe_Connect_API::class );
	}

	public function getOAuthUrl( $currency, $mode ) {
		$mode      = Mode::normalize( $mode );
		$returnUrl = $this->buildReturnUrl( $currency, $mode );

		if ( ! Mode::isTest( $mode ) && 0 !== strpos( $returnUrl, 'https://' ) ) {
			return new WP_Error(
				'wcml_stripe_connect_https_required',
				__( 'Your site needs HTTPS to connect a live Stripe account. You can still connect a test account.', 'woocommerce-multilingual' )
			);
		}

		$response = $this->api->get_stripe_oauth_init( $returnUrl, $mode );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! isset( $response->oauthUrl, $response->state ) ) {
			return new WP_Error(
				'wcml_stripe_connect_invalid_response',
				__( 'Stripe did not return a connection link. Please try again.', 'woocommerce-multilingual' )
			);
		}

		set_transient( self::stateKey( $currency, $mode ), $response->state, self::STATE_LIFETIME );

		return $response->oauthUrl;
	}

	public function handleReturn() {
		if ( ! isset( $_GET[ self::ARG_CURRENCY ], $_GET[ self::ARG_CODE ], $_GET[ self::ARG_STATE ] ) ) {
			return null;
		}

		$nonceIsValid = (bool) wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) ), self::NONCE_ACTION );

		$query    = array_map( 'sanitize_text_field', wp_unslash( $_GET ) );
		$currency = self::readArg( $query, self::ARG_CURRENCY );

		if ( '' === $currency ) {
			return null;
		}

		if ( ! $nonceIsValid || ! \WCML_Capabilities::canManageWcml() ) {
			return $this->notify( $currency, false );
		}

		$mode = Mode::normalize( self::readArg( $query, self::ARG_MODE ) );

		if ( ! $this->isExpectedState( $currency, $mode, self::readArg( $query, self::ARG_STATE ) ) ) {
			return $this->notify( $currency, false );
		}

		$type = self::TYPE_APP === self::readArg( $query, self::ARG_TYPE ) ? self::TYPE_APP : self::TYPE_CONNECT;
		$keys = $this->api->get_stripe_oauth_keys( self::readArg( $query, self::ARG_CODE ), $type, $mode );

		return $this->notify( $currency, $this->storeCredentials( $currency, $mode, $type, $keys ) );
	}

	public function refresh( $currency, $mode ) {
		$mode    = Mode::normalize( $mode );
		$setting = Storage::get( $currency );

		if ( self::TYPE_APP !== Storage::getConnectionType( $setting, $mode ) ) {
			return;
		}

		$refreshToken = $setting[ Mode::key( Storage::REFRESH_TOKEN, $mode ) ] ?? '';

		if ( '' === $refreshToken ) {
			return;
		}

		$keys = $this->api->refresh_stripe_app_oauth_keys( $refreshToken, $mode );

		if ( $this->storeCredentials( $currency, $mode, self::TYPE_APP, $keys ) ) {
			return;
		}

		$attempts = (int) ( $setting[ Mode::key( Storage::FAILED_ATTEMPTS, $mode ) ] ?? 0 ) + 1;

		$setting[ Mode::key( Storage::FAILED_ATTEMPTS, $mode ) ] = $attempts;
		Storage::save( $currency, $setting );

		if ( $attempts < self::MAX_REFRESH_ATTEMPTS ) {
			$this->scheduleRefresh( $currency, $mode );
		}
	}

	private function isExpectedState( $currency, $mode, $state ) {
		$stored = get_transient( self::stateKey( $currency, $mode ) );
		delete_transient( self::stateKey( $currency, $mode ) );

		return $stored && hash_equals( (string) $stored, $state );
	}

	private function storeCredentials( $currency, $mode, $type, $keys ) {
		if ( is_wp_error( $keys ) || ! isset( $keys->publishableKey, $keys->secretKey ) ) {
			return false;
		}

		if ( self::TYPE_APP === $type && ! isset( $keys->refreshToken ) ) {
			return false;
		}

		$setting = Storage::get( $currency );

		if ( empty( $setting['currency'] ) ) {
			$setting['currency'] = $currency;
		}

		$setting[ Mode::key( Storage::PUBLISHABLE_KEY, $mode ) ] = $keys->publishableKey;
		$setting[ Mode::key( Storage::SECRET_KEY, $mode ) ]      = $keys->secretKey;
		$setting[ Mode::key( Storage::CONNECTION_TYPE, $mode ) ] = $type;
		$setting[ Mode::key( Storage::FAILED_ATTEMPTS, $mode ) ] = 0;

		if ( self::TYPE_APP === $type ) {
			$setting[ Mode::key( Storage::REFRESH_TOKEN, $mode ) ] = $keys->refreshToken;
		}

		Storage::save( $currency, $setting );

		if ( self::TYPE_APP === $type ) {
			$this->scheduleRefresh( $currency, $mode );
		}

		return true;
	}

	private function scheduleRefresh( $currency, $mode ) {
		if ( ! function_exists( 'as_schedule_single_action' ) ) {
			return;
		}

		as_unschedule_all_actions( self::REFRESH_ACTION, [ $currency, $mode ], self::REFRESH_GROUP );

		as_schedule_single_action(
			time() + self::REFRESH_INTERVAL,
			self::REFRESH_ACTION,
			[ $currency, $mode ],
			self::REFRESH_GROUP
		);
	}

	private function notify( $currency, $success ) {
		$notices = AdminNotices::get();

		$notice = $notices->create_notice(
			self::NOTICE_ID,
			$success
				? sprintf(
					/* translators: %s is a currency code, for example NZD. */
					__( 'Stripe is now connected for %s.', 'woocommerce-multilingual' ),
					$currency
				)
				: sprintf(
					/* translators: %s is a currency code, for example NZD. */
					__( 'Stripe could not be connected for %s. Please try again.', 'woocommerce-multilingual' ),
					$currency
				),
			self::NOTICES_GROUP
		);
		$notice->set_flash( true );
		$notice->set_css_class_types( $success ? 'notice-success' : 'notice-error' );
		$notice->set_restrict_to_pages( [ AdminPages::BONDED_SETTINGS_PAGE, AdminUrl::PAGE_WPML_WCML ] );
		$notices->add_notice( $notice );

		return $currency;
	}

	private function buildReturnUrl( $currency, $mode ) {
		return add_query_arg(
			[
				self::ARG_CURRENCY => $currency,
				self::ARG_MODE     => $mode,
				'_wpnonce'         => wp_create_nonce( self::NONCE_ACTION ),
			],
			$this->returnUrl
		);
	}

	private static function readArg( array $query, string $name ): string {
		return isset( $query[ $name ] ) && is_string( $query[ $name ] ) ? $query[ $name ] : '';
	}

	private static function stateKey( $currency, $mode ) {
		return self::STATE_TRANSIENT_PREFIX . $mode . '_' . $currency;
	}
}
