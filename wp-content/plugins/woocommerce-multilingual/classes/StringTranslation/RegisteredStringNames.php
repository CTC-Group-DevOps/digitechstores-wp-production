<?php

namespace WCML\StringTranslation;

class RegisteredStringNames {

	private $wpdb;

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function get( string $domain, string $namePrefix ) {
		if ( has_filter( 'wpml_st_registered_string_names' ) ) {
			$names = apply_filters( 'wpml_st_registered_string_names', null, $domain, $namePrefix );

			if ( is_array( $names ) ) {
				return $names;
			}
		}

		return defined( 'WPML_ST_VERSION' ) ? $this->readFromStringsTable( $domain, $namePrefix ) : null;
	}

	private function readFromStringsTable( string $domain, string $namePrefix ): array {
		$wpdb = $this->wpdb;

		return $wpdb->get_col(
			$wpdb->prepare(
				"SELECT name FROM {$wpdb->prefix}icl_strings WHERE context = %s AND name LIKE %s",
				$domain,
				$wpdb->esc_like( $namePrefix ) . '%'
			)
		);
	}
}
