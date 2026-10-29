<?php

use WCML\Utilities\AdminNotices;
use WCML\Utilities\AdminPages;

class WCML_Requests {

	const ALLOWED_WARNING_SETTINGS = [
		'dismiss_doc_main',
		'dismiss_tm_warning',
		'dismiss_cart_warning',
		'dismiss_non_default_language_warning',
		\WCML\EditorScopedSync\Notices::SWITCH_HINT_DISMISS_KEY,
	];

	const DISMISS_ACTION = 'wcml_dismiss_warning';
	const DISMISS_NONCE  = 'wcml_dismiss_warning';

	const SAVE_ACTION = 'wcml_save_settings';
	const SAVE_NONCE  = 'wcml_save_settings_nonce';

	const REDIRECT_TO = 'redirect_to';

	public function __construct() {

		add_action( 'init', [ $this, 'run' ] );

	}

	public function run() {
		$policy = [
			'authorize'   => [ 'WCML_Capabilities', 'canManageWcml' ],
			'description' => 'WCML manager (shop manager or administrator role, WCML_Capabilities::canManageWcml)',
		];

		\WCML\Request\AdminPost::register(
			self::DISMISS_ACTION,
			$policy + [ 'nonce' => [ self::DISMISS_NONCE, '_wpnonce' ] ],
			[ $this, 'dismiss_warning' ]
		);
		\WCML\Request\AdminPost::register(
			self::SAVE_ACTION,
			$policy + [ 'nonce' => [ self::SAVE_NONCE, 'wcml_nonce' ] ],
			[ $this, 'save_settings' ]
		);

		\WCML\Request\Ajax::register(
			'wcml_ignore_warning',
			$policy + [ 'nonce' => [ 'wcml_ignore_warning', 'wcml_nonce' ] ],
			[ $this, 'update_settings_from_warning' ]
		);

		add_filter( 'woocommerce_cached_widget_id', [ $this, 'override_cached_widget_id' ] );
	}

	public static function dismissUrl( $setting, $redirectTo ) {
		return wp_nonce_url(
			\WCML\Request\AdminPost::url(
				self::DISMISS_ACTION,
				[
					'setting'         => $setting,
					self::REDIRECT_TO => $redirectTo,
				]
			),
			self::DISMISS_NONCE
		);
	}

	public function dismiss_warning() {
		global $woocommerce_wpml;

		if ( ! WCML_Capabilities::canManageWcml() || ! $this->nonceValid( '_wpnonce', self::DISMISS_NONCE ) ) {
			$this->deny();

			return;
		}

		$setting = isset( $_GET['setting'] ) ? sanitize_key( wp_unslash( (string) $_GET['setting'] ) ) : '';
		if ( in_array( $setting, self::ALLOWED_WARNING_SETTINGS, true ) ) {
			$woocommerce_wpml->settings[ $setting ] = 1;
			$woocommerce_wpml->update_settings();
		}

		$this->redirect( $this->redirectTo() );
	}

	public function save_settings() {
		global $woocommerce_wpml;

		if ( ! WCML_Capabilities::canManageWcml() || ! $this->nonceValid( 'wcml_nonce', self::SAVE_NONCE ) ) {
			$this->deny();

			return;
		}

		if ( isset( $_POST['trnsl_interface'] ) ) {
			$woocommerce_wpml->settings['trnsl_interface'] = filter_input( INPUT_POST, 'trnsl_interface', FILTER_SANITIZE_NUMBER_INT );
		}

		$woocommerce_wpml->settings['products_sync_date']  = empty( $_POST['products_sync_date'] ) ? 0 : 1;
		$woocommerce_wpml->settings['products_sync_order'] = empty( $_POST['products_sync_order'] ) ? 0 : 1;

		$wcml_sync_media = empty( $_POST['sync_media'] ) ? 0 : 1;
		$woocommerce_wpml->update_setting( 'sync_media', $wcml_sync_media, true );

		$reviews_in_all_languages = empty( $_POST['reviews_in_all_languages'] ) ? 0 : 1;
		$woocommerce_wpml->update_setting( 'reviews_in_all_languages', $reviews_in_all_languages, true );

		$wcml_file_path_sync = filter_input( INPUT_POST, 'wcml_file_path_sync', FILTER_SANITIZE_NUMBER_INT );

		$woocommerce_wpml->settings[ \WCML_Downloadable_Products::SYNC_MODE_SETTING_KEY ] = $wcml_file_path_sync;

		if ( isset( $_POST['cart_sync_lang'] ) && isset( $_POST['cart_sync_currencies'] ) ) {
			$woocommerce_wpml->settings['cart_sync']['lang_switch']     = (int) filter_input( INPUT_POST, 'cart_sync_lang', FILTER_SANITIZE_NUMBER_INT );
			$woocommerce_wpml->settings['cart_sync']['currency_switch'] = (int) filter_input( INPUT_POST, 'cart_sync_currencies', FILTER_SANITIZE_NUMBER_INT );
		}


		$new_value = 0 === (int) $wcml_file_path_sync ? 2 : (int) $wcml_file_path_sync;
		\WPML\TM\Settings\PreferenceWriter::setModes(
			'post',
			[
				'_downloadable_files' => $new_value,
				'_file_paths'         => $new_value,
			]
		);

		$notices = AdminNotices::get();
		$notice  = $notices->create_notice(
			'wcml-settings-saved',
			/* translators: Confirmation notice shown after the plugin's settings were stored. */
			__( 'Your settings have been saved.', 'woocommerce-multilingual' ),
			'wcml-settings'
		);
		$notice->set_flash( true );
		$notice->set_css_class_types( 'notice-success' );
		$notice->set_restrict_to_pages( [ AdminPages::BONDED_SETTINGS_PAGE ] );
		$notices->add_notice( $notice );

		$woocommerce_wpml->update_settings();

		do_action( 'wcml_settings_form_saved' );

		$this->redirect( $this->redirectTo() );
	}

	private function nonceValid( $field, $action ) {
		$nonce = isset( $_REQUEST[ $field ] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST[ $field ] ) ) : '';

		return '' !== $nonce && false !== wp_verify_nonce( $nonce, $action );
	}

	private function redirectTo() {
		$to = isset( $_REQUEST[ self::REDIRECT_TO ] ) ? esc_url_raw( wp_unslash( (string) $_REQUEST[ self::REDIRECT_TO ] ) ) : '';

		return wp_validate_redirect( $to, AdminPages::isWcmlSettings() ? \WCML\Utilities\AdminUrl::getSettingsTab() : admin_url() );
	}

	protected function redirect( $url ) {
		wcml_safe_redirect( $url );
	}

	protected function deny() {
		/* translators: Error page shown when the logged-in user is not allowed to run the request. */
		wp_die( esc_html__( 'You do not have permission to perform this action.', 'woocommerce-multilingual' ), '', [ 'response' => 403 ] );
	}

	public function update_settings_from_warning() {
		$nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wcml_ignore_warning' ) ) {
			die( 'Invalid nonce' );
		}

		WCML_Capabilities::requireCanManageWcmlForAjax();

		$setting = sanitize_key( (string) filter_input( INPUT_POST, 'setting' ) );
		if ( ! in_array( $setting, self::ALLOWED_WARNING_SETTINGS, true ) ) {
			wp_send_json_error( 'Invalid setting' );
		}

		global $woocommerce_wpml;

		$woocommerce_wpml->settings[ $setting ] = 1;
		$woocommerce_wpml->update_settings();

	}

	public function override_cached_widget_id( $widget_id ) {

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$widget_id .= ':' . ICL_LANGUAGE_CODE;
		}

		return $widget_id;
	}

}
