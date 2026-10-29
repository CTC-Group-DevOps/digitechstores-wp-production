<?php

namespace WPML\Dependencies\Request;

final class Ajax {

	const DENIED_STATUS = 403;

	public static function register( $action, array $spec, callable $callback, $priority = 10, $acceptedArgs = 1 ) {
		if ( self::coreAvailable() ) {
			\WPML\Request\Adapter\Ajax::register( $action, self::policy( $spec, $action ), $callback, $priority, $acceptedArgs );

			return;
		}

		$guarded = self::guard( $action, $spec, $callback );

		add_action( 'wp_ajax_' . $action, $guarded, $priority, $acceptedArgs );
		if ( isset( $spec['public'] ) || isset( $spec['machine'] ) ) {
			add_action( 'wp_ajax_nopriv_' . $action, $guarded, $priority, $acceptedArgs );
		}
	}

	public static function guard( $action, array $spec, callable $callback ) {
		return function () use ( $action, $spec, $callback ) {
			$verdict = self::evaluate( $spec, $action );

			if ( true !== $verdict ) {
				wp_send_json_error( $verdict, self::DENIED_STATUS );

				return null;
			}

			return call_user_func_array( $callback, func_get_args() );
		};
	}

	public static function evaluate( array $spec, $action ) {
		$authenticity = self::evaluateAuthenticity( $spec, $action );
		if ( true !== $authenticity ) {
			return $authenticity;
		}

		return self::evaluateAuthorization( $spec, $action );
	}

	private static function evaluateAuthenticity( array $spec, $action ) {
		if ( isset( $spec['nonce'] ) && is_array( $spec['nonce'] ) && isset( $spec['nonce'][0] ) ) {
			$fields = isset( $spec['nonce'][1] ) ? (array) $spec['nonce'][1] : array( 'nonce' );
			foreach ( $fields as $field ) {
				$value = isset( $_REQUEST[ $field ] ) ? $_REQUEST[ $field ] : null;
				if ( is_string( $value ) && wp_verify_nonce( $value, (string) $spec['nonce'][0] ) ) {
					return true;
				}
			}

			return 'invalid request';
		}

		if ( isset( $spec['machine'] ) ) {
			return true;
		}

		if ( isset( $spec['no_nonce'] ) && is_string( $spec['no_nonce'] ) && '' !== $spec['no_nonce'] ) {
			return true;
		}

		return 'no request-authenticity policy declared for ' . $action;
	}

	private static function evaluateAuthorization( array $spec, $action ) {
		if ( isset( $spec['capability'] ) ) {
			foreach ( (array) $spec['capability'] as $capability ) {
				if ( is_string( $capability ) && current_user_can( $capability ) ) {
					return true;
				}
			}

			return 'not allowed';
		}

		if ( isset( $spec['authorize'] ) ) {
			return ( is_callable( $spec['authorize'] ) && true === call_user_func( $spec['authorize'] ) ) ? true : 'not allowed';
		}

		if ( isset( $spec['authenticated'] ) ) {
			return is_user_logged_in() ? true : 'not allowed';
		}

		if ( isset( $spec['public'] ) ) {
			return true;
		}

		if ( isset( $spec['machine'] ) ) {
			return ( is_callable( $spec['machine'] ) && true === call_user_func( $spec['machine'] ) ) ? true : 'not allowed';
		}

		return 'no authorization policy declared for ' . $action;
	}

	public static function coreAvailable() {
		return class_exists( '\WPML\Request\Adapter\Ajax' ) && class_exists( '\WPML\Request\Policy\Policy' );
	}

	public static function policy( array $spec, $action ) {
		if ( isset( $spec['nonce'] ) && is_array( $spec['nonce'] ) ) {
			$authenticity = \WPML\Request\Policy\Authenticity::actionNonce( $spec['nonce'][0], isset( $spec['nonce'][1] ) ? $spec['nonce'][1] : 'nonce' );
		} elseif ( isset( $spec['machine'] ) ) {
			$authenticity = null;
		} else {
			$authenticity = \WPML\Request\Policy\Authenticity::none( isset( $spec['no_nonce'] ) ? (string) $spec['no_nonce'] : '' );
		}

		if ( isset( $spec['capability'] ) ) {
			return \WPML\Request\Policy\Policy::capability( $spec['capability'], $authenticity );
		}
		if ( isset( $spec['authorize'] ) ) {
			return \WPML\Request\Policy\Policy::authorize( $spec['authorize'], $authenticity, isset( $spec['description'] ) ? $spec['description'] : '' );
		}
		if ( isset( $spec['authenticated'] ) ) {
			return \WPML\Request\Policy\Policy::authenticated( $authenticity, (string) $spec['authenticated'] );
		}
		if ( isset( $spec['public'] ) ) {
			return \WPML\Request\Policy\Policy::publicAccess( (string) $spec['public'], $authenticity );
		}
		if ( isset( $spec['machine'] ) ) {
			return \WPML\Request\Policy\Policy::machine( $spec['machine'], isset( $spec['description'] ) ? $spec['description'] : '' );
		}

		return \WPML\Request\Policy\Policy::authorize(
			function () {
				return false;
			},
			\WPML\Request\Policy\Authenticity::none( 'malformed policy spec' ),
			'fail-closed: malformed policy spec for ' . $action
		);
	}
}
