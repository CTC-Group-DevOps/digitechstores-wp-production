<?php

namespace WCML\AdminNotices;

use woocommerce_wpml;
use WPML\FP\Obj;
use WPML_Notices;

class CurrencySwitcherUseTwigTemplate implements \IWPML_Backend_Action, \IWPML_DIC_Action {

	const NOTICE_ID = 'wcml-multi-currency-currency-switcher-php-template-missing4';

	private $wcml;

	private $notices;

	public function __construct( woocommerce_wpml $wcml, WPML_Notices $notices ) {
		$this->wcml    = $wcml;
		$this->notices = $notices;
	}

	public function add_hooks() {
		if ( wcml_is_multi_currency_on() ) {
			add_action( 'admin_init', [ $this, 'initNotice' ] );
		}
	}

	public function initNotice() {
		$notice      = $this->notices->get_notice( self::NOTICE_ID );
		$needsNotice = wcml_is_multi_currency_on() && $this->hasUniqueCurrency();

		if ( $needsNotice && ! $notice ) {
			add_action( 'wpml_currency_switcher_uses_twig_templates', [ $this, 'addNotice' ] );
		} elseif ( ! $needsNotice && ( $notice instanceof \WPML_Notice ) ) {
			$this->removeNotice( $notice );
		}
	}

	public function addNotice( string $templateSlug ) {
		$activeTemplateNames = array_keys( $this->wcml->cs_templates->get_active_templates() );
		if ( ! in_array( $templateSlug, $activeTemplateNames, true ) ) {
			return;
		}

		/* translators: Heading of an admin notice telling the shop owner that Twig currency-switcher templates are going away. Keep the bold tags around the feature name. */
		$text  = '<h2>' . wcml_bold_names( __( 'Important: Action Needed for Your <b>Currency Switcher</b>', 'woocommerce-multilingual' ) ) . '</h2>';
		/* translators: First paragraph of the admin notice about Twig currency-switcher templates. Keep the bold tags around the feature name; "Twig" is a template technology and stays in English. */
		$text .= '<p>' . wcml_bold_names( __( 'We detected that your site is using a <b>Currency Switcher</b> built with Twig templates. For security reasons, support for Twig-based templates will be discontinued in the upcoming version of WPML Multilingual & Multicurrency for WooCommerce 5.6.', 'woocommerce-multilingual' ) ) . '</p>';
		/* translators: Line that introduces the list of steps in the admin notice about Twig currency-switcher templates. Keep the bold tags around the feature name. */
		$text .= '<p>' . wcml_bold_names( __( 'To ensure your <b>Currency Switcher</b> continues working smoothly:', 'woocommerce-multilingual' ) ) . '</p>';

		$text .= '<ul>';
		$text .= '<li>';
		$text .= sprintf(
			/* translators: %1$s and %2$s are opening and closing HTML link tags */
			esc_html__( '- Switch to a PHP-based template - you can select one from the %1$sconfiguration page%2$s.', 'woocommerce-multilingual' ),
			'<a href="' . \WCML\Utilities\AdminUrl::getMultiCurrencyTab( 'currency-switcher-product' ) . '">',
			'</a>'
		);
		$text .= '</li>';
		$text .= '<li>';
		$text .= sprintf(
			/* translators: %1$s and %2$s are opening and closing HTML link tags */
			esc_html__( '- Need a custom solution? %1$sContact our support%2$s - we’re here to help you migrate safely.', 'woocommerce-multilingual' ),
			'<a href="' . \WCML_Tracking_Link::getWpmlSupport() . '">',
			'</a>'
		);
		$text .= '</li>';
		$text .= '</ul>';
		/* translators: Closing paragraph of the admin notice about Twig currency-switcher templates. Keep the bold tags around the feature name. */
		$text .= '<p>' . wcml_bold_names( __( 'We recommend updating your <b>Currency Switcher</b> as soon as possible to avoid disruptions when the new version of WPML Multilingual & Multicurrency for WooCommerce is released.', 'woocommerce-multilingual' ) ) . '</p>';

		$notice = $this->notices->create_notice( self::NOTICE_ID, $text );
		$notice->set_css_class_types( 'notice-warning' );
		$notice->set_restrict_to_screen_ids( RestrictedScreens::get() );
		$notice->set_dismissible( true );

		$this->notices->add_notice( $notice );
	}

	private function removeNotice( \WPML_Notice $notice ) {
		$this->notices->remove_notice( $notice->get_group(), $notice->get_id() );
	}

	private function hasUniqueCurrency(): bool {
		return count( (array) Obj::path( [ 'settings', 'currency_options' ], $this->wcml ) ) >= 1;
	}
}
