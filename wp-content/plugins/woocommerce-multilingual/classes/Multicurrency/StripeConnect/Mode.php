<?php

namespace WCML\Multicurrency\StripeConnect;

class Mode {

	public const LIVE = 'live';
	public const TEST = 'test';

	public static function all() {
		return [ self::LIVE, self::TEST ];
	}

	public static function normalize( $mode ) {
		return self::TEST === $mode ? self::TEST : self::LIVE;
	}

	public static function isTest( $mode ) {
		return self::TEST === self::normalize( $mode );
	}

	public static function prefix( $mode ) {
		return self::isTest( $mode ) ? 'test_' : '';
	}

	public static function key( $key, $mode ) {
		return self::prefix( $mode ) . $key;
	}
}
