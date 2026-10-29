<?php

namespace WCML\Rest\Language;

use WPML\API\Sanitize;
use WPML\FP\Obj;

class Set {

	const PRIORITY_AFTER_THE_REQUEST = PHP_INT_MAX;

	private static $requestScopeIsOpen = false;

	private static $callbackScopes = [];

	public static function fromUrlQueryVar() {
		$lang = self::sanitize( Obj::prop( 'lang', $_GET ) );

		if ( ! self::$requestScopeIsOpen && self::opensAScopeFor( $lang ) ) {
			self::$requestScopeIsOpen = true;
			wpml_switch_language_action( $lang );
			add_action( 'shutdown', [ __CLASS__, 'closeRequestScope' ], self::PRIORITY_AFTER_THE_REQUEST );
		}
	}

	public static function closeRequestScope() {
		if ( self::$requestScopeIsOpen ) {
			self::$requestScopeIsOpen = false;
			wpml_switch_language_action();
		}
	}

	public static function beforeCallbacks( $response, $handler, \WP_REST_Request $request ) {
		$lang = self::getFromRequestParams( $request )
			?: self::getFromProduct( $handler, $request );

		$opened = self::opensAScopeFor( $lang );

		if ( $opened ) {
			wpml_switch_language_action( $lang );
		}

		self::$callbackScopes[] = $opened;

		return $response;
	}

	public static function afterCallbacks( $response ) {
		if ( array_pop( self::$callbackScopes ) ) {
			wpml_switch_language_action();
		}

		return $response;
	}

	private static function getFromRequestParams( \WP_REST_Request $request ) {
		return self::sanitize( $request->get_param( 'lang' ) );
	}

	private static function getFromProduct( $handler, \WP_REST_Request $request ) {
		$callback = Obj::prop( 'callback', $handler );

		if (
			is_array( $callback )
			&& Obj::prop( 0, $callback ) instanceof \WC_REST_Products_Controller
			&& $request->get_param( 'id' )
		) {
			return (string) Obj::prop(
				'language_code',
				apply_filters( 'wpml_post_language_details', [], $request->get_param( 'id' ) )
			);
		}

		return '';
	}

	private static function opensAScopeFor( $lang ) {
		$currentLanguage = (string) apply_filters( 'wpml_current_language', null );

		return (bool) $lang && $currentLanguage !== $lang;
	}

	private static function sanitize( $lang ) {
		return Sanitize::string( $lang );
	}
}
