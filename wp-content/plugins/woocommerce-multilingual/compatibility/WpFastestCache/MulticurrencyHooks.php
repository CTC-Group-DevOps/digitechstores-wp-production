<?php

namespace WCML\Compatibility\WpFastestCache;

use WPML\FP\Fns;
use WPML\FP\Lst;

class MulticurrencyHooks implements \IWPML_Action {

	public function add_hooks() {
		add_filter( 'wcml_multicurrency_supported_cache_plugins', Lst::append( 'WP Fastest Cache' ) );

		add_filter( 'wcml_user_store_strategy', Fns::always( 'cookie' ) );

		add_filter(
			'wcml_is_cache_enabled_for_switching_currency',
			[
				$this,
				'is_cache_enabled_for_switching_currency',
			]
		);
	}

	public function is_cache_enabled_for_switching_currency( $cache_enabled ) {

		$wp_fastest_cache_options = json_decode( get_option( 'WpFastestCache' ) );

		if ( isset( $wp_fastest_cache_options->wpFastestCacheStatus ) && 'on' === $wp_fastest_cache_options->wpFastestCacheStatus ) {
			$cache_enabled = true;
		}

		return $cache_enabled;
	}

}

