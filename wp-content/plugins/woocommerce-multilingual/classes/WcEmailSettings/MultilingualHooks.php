<?php

namespace WCML\WcEmailSettings;

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use WPML\LIB\WP\Hooks as WPHooks;

use function WPML\FP\spreadArgs;

class MultilingualHooks implements \IWPML_Frontend_Action, \IWPML_Backend_Action {
	const WCML_EMAIL_HEADER_ALIGNMENT_ID = 'wcml_email_header_alignment';

	const MULTILINGUAL_HEADER_ALIGNMENT_OFF = 'off';
	const MULTILINGUAL_HEADER_ALIGNMENT_ON  = 'on';

	public function add_hooks() {
		WPHooks::onFilter( 'woocommerce_email_settings' )
				->then( spreadArgs( [ $this, 'woocommerce_email_settings_add_wcml_header_alignment' ] ) );

		WPHooks::onFilter( 'pre_option_woocommerce_email_header_alignment' )
				->then( spreadArgs( [ $this, 'pre_option_woocommerce_email_header_alignment' ] ) );
	}

	public function woocommerce_email_settings_add_wcml_header_alignment( $settings ) {
		$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );

		$new_settings = [];

		$wcml_option = [
			/* translators: Label of the setting that aligns the header of translated WooCommerce emails, on the WooCommerce email settings. */
			'title'     => __( 'Header alignment for translations', 'woocommerce-multilingual' ),
			'id'        => self::WCML_EMAIL_HEADER_ALIGNMENT_ID,
			/* translators: %s is an HTML break line - after about 65 characters the text will no longer be visible */
			'desc'      => sprintf( __( 'Automatically align email headers based on%slanguage direction (right-to-left or left-to-right).', 'woocommerce-multilingual' ), '<br >' ),
			'default'   => self::MULTILINGUAL_HEADER_ALIGNMENT_OFF,
			'type'      => 'radio',
			'options'   => [
				/* translators: First option of the email header-alignment setting. "Header alignment" is the name of the WooCommerce setting shown above it and is translated the same way there. */
				self::MULTILINGUAL_HEADER_ALIGNMENT_OFF => __( 'Off – use the "Header alignment" setting above', 'woocommerce-multilingual' ),
				/* translators: Second option of the email header-alignment setting: WPML picks the alignment from the language's writing direction. */
				self::MULTILINGUAL_HEADER_ALIGNMENT_ON  => __( 'On – align automatically', 'woocommerce-multilingual' ),
			],
			'row_class' => $email_improvements_enabled ? '' : 'disabled',
		];

		foreach ( $settings as $setting ) {
			$new_settings[] = $setting;

			$settingId = $setting['id'] ?? null;
			if ( 'woocommerce_email_header_alignment' === $settingId ) {
				$new_settings[] = $wcml_option;
			}
		}

		return $new_settings;
	}

	public function pre_option_woocommerce_email_header_alignment( $value ) {
		if ( self::MULTILINGUAL_HEADER_ALIGNMENT_ON !== get_option( self::WCML_EMAIL_HEADER_ALIGNMENT_ID ) ) {
			return $value;
		}

		return is_rtl() ? 'right' : 'left';
	}
}
