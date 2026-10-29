<?php

namespace WCML\Multicurrency\StripeConnect;

use WCML\Multicurrency\UI\Hooks as MultiCurrencyUI;
use WCML\Request\Ajax;
use WCML\StandAlone\IStandAloneAction;
use WCML\Utilities\AdminUrl;
use WPML\FP\Obj;

class Hooks implements \IWPML_Action, IStandAloneAction {

	public const ACTION_INIT = 'wcml_stripe_connect_init';

	private const PRIORITY_BEFORE_GATEWAY_REDIRECT = 5;

	private $connect;

	public function __construct( Connect $connect ) {
		$this->connect = $connect;
	}

	public function add_hooks() {
		add_action( Connect::REFRESH_ACTION, [ $this->connect, 'refresh' ], 10, 2 );

		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_init', [ $this, 'handleReturn' ], self::PRIORITY_BEFORE_GATEWAY_REDIRECT );
		Ajax::register(
			self::ACTION_INIT,
			[
				'authorize'   => [ 'WCML_Capabilities', 'canManageWcml' ],
				'description' => 'WCML manager (shop manager or administrator role, WCML_Capabilities::canManageWcml)',
				'nonce'       => [ MultiCurrencyUI::HANDLE, 'nonce' ],
			],
			[ $this, 'ajaxInit' ]
		);
	}

	public function handleReturn() {
		if ( null === $this->connect->handleReturn() ) {
			return;
		}

		wp_safe_redirect( AdminUrl::getMultiCurrencyDestination() );
		exit;
	}

	public function ajaxInit() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! wp_verify_nonce( $nonce, MultiCurrencyUI::HANDLE ) ) {
			wp_send_json_error( __( 'Invalid nonce', 'woocommerce-multilingual' ) );
		}

		\WCML_Capabilities::requireCanManageWcmlForAjax();

		$data     = json_decode( stripslashes( Obj::propOr( '{}', 'data', $_POST ) ), true );
		$currency = sanitize_text_field( (string) Obj::propOr( '', 'currency', $data ) );

		if ( '' === $currency ) {
			wp_send_json_error( __( 'Stripe could not be connected. Please try again.', 'woocommerce-multilingual' ) );
		}

		$mode = Mode::normalize( sanitize_text_field( (string) Obj::propOr( '', 'mode', $data ) ) );
		$url  = $this->connect->getOAuthUrl( $currency, $mode );

		if ( is_wp_error( $url ) ) {
			wp_send_json_error( $url->get_error_message() );
		}

		wp_send_json_success( [ 'oauthUrl' => $url ] );
	}
}
