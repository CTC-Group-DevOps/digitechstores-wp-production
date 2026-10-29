<?php

namespace WCML\Utilities;

class RequestFingerprint {

	private $trust_proxy_headers;

	public function __construct( bool $trust_proxy_headers = false ) {
		$this->trust_proxy_headers = $trust_proxy_headers;
	}

	public function sign( array $data ): array {
		$data['binder'] = $this->binder();

		return $data;
	}

	public function verify( array $data ): ?array {
		return ( ! empty( $data['binder'] ) && hash_equals( $data['binder'], $this->binder() ) ) ? $data : null;
	}

	private function binder(): string {
		if ( $this->trust_proxy_headers ) {
			$ip = class_exists( 'WC_Geolocation' ) ? \WC_Geolocation::get_ip_address() : '';
		} else {
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		}
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

		return hash_hmac( 'sha256', $ip . '|' . $ua, wp_salt( 'nonce' ) );
	}
}
