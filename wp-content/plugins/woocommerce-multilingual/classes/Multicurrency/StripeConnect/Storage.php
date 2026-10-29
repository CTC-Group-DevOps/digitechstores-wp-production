<?php

namespace WCML\Multicurrency\StripeConnect;

use WCML_Payment_Gateway;
use WCML_Payment_Gateway_Stripe;

class Storage {

	public const PUBLISHABLE_KEY = 'publishable_key';
	public const SECRET_KEY      = 'secret_key';
	public const CONNECTION_TYPE = 'connection_type';
	public const REFRESH_TOKEN   = 'refresh_token';
	public const FAILED_ATTEMPTS = 'refresh_failed_attempts';

	private const SERVER_ONLY_KEYS = [
		self::REFRESH_TOKEN,
		self::FAILED_ATTEMPTS,
	];

	public static function optionName() {
		return WCML_Payment_Gateway::OPTION_KEY . WCML_Payment_Gateway_Stripe::ID;
	}

	public static function getAll() {
		$settings = get_option( self::optionName(), [] );

		return is_array( $settings ) ? $settings : [];
	}

	public static function get( $currency ) {
		$setting = self::getAll()[ $currency ] ?? [];

		return is_array( $setting ) ? $setting : [];
	}

	public static function save( $currency, array $setting ) {
		$settings = self::getAll();

		$settings[ $currency ] = $setting;

		update_option( self::optionName(), $settings );
	}

	public static function hasKeys( array $setting, $mode ) {
		return ! empty( $setting[ Mode::key( self::PUBLISHABLE_KEY, $mode ) ] )
			&& ! empty( $setting[ Mode::key( self::SECRET_KEY, $mode ) ] );
	}

	public static function getConnectionType( array $setting, $mode ) {
		return (string) ( $setting[ Mode::key( self::CONNECTION_TYPE, $mode ) ] ?? '' );
	}

	public static function preserveServerOnlyKeys( array $stored, array $incoming ) {
		foreach ( Mode::all() as $mode ) {
			foreach ( self::SERVER_ONLY_KEYS as $key ) {
				$prefixed = Mode::key( $key, $mode );

				if ( ! isset( $incoming[ $prefixed ] ) && isset( $stored[ $prefixed ] ) ) {
					$incoming[ $prefixed ] = $stored[ $prefixed ];
				}
			}
		}

		return $incoming;
	}
}
