<?php

namespace WCML\User\Store;

use WCML\Rest\Functions;
use function WCML\functions\isCli;

class Store implements Strategy {

	private static $isStartingSession = false;

	public function get( $key ) {

		$key = $this->adjustKey( $key );

		return $this->getStrategy( $key )->get( $key );
	}

	public function set( $key, $value ) {

		$key = $this->adjustKey( $key );

		$this->getStrategy( $key )->set( $key, $value );
	}

	public function isPending( $key ) {
		global $woocommerce;

		return $this->getStrategy( $this->adjustKey( $key ) ) instanceof Noop
			&& ! isset( $woocommerce->session )
			&& self::canWooCommerceStartSession();
	}

	private function getStrategy( $key ) {

		switch ( apply_filters( 'wcml_user_store_strategy', 'wc-session', $key ) ) {
			case 'cookie':
				$store = \WPML\Container\make( Cookie::class );
				break;

			case 'wc-session':
			default:
				$store = $this->getSessionStrategy();
		}

		return $store;
	}

	private function getSessionStrategy() {
		global $woocommerce;

		if ( isset( $woocommerce->session ) ) {
			return new WcSession( $woocommerce->session );
		}

		$session = self::startSession();

		return $session ? new WcSession( $session ) : new Noop();
	}

	private static function startSession() {
		if ( self::$isStartingSession || ! self::canWooCommerceStartSession() || ! function_exists( 'WC' ) ) {
			return null;
		}

		$wc = WC();

		self::$isStartingSession = true;

		try {
			$wc->initialize_session();
		} finally {
			self::$isStartingSession = false;
		}

		return $wc->session instanceof \WC_Session ? $wc->session : null;
	}

	private static function canWooCommerceStartSession() {
		if ( isCli() || wp_doing_cron() || ! did_action( 'woocommerce_init' ) ) {
			return false;
		}

		if ( Functions::isStoreAPIRequest() ) {
			return true;
		}

		return ( ! is_admin() || wp_doing_ajax() ) && ! Functions::isRestRequest();
	}

	private function adjustKey( $key ) {

		$prefix = 'wcml_';

		return strpos( $key, $prefix ) === 0 ? $key : $prefix . $key;
	}

}
