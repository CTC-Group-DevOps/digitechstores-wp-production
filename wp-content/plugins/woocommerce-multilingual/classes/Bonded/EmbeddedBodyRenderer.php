<?php

namespace WCML\Bonded;

class EmbeddedBodyRenderer implements \IWPML_Backend_Action {

	public function add_hooks() {
		add_filter( 'wpml_render_embedded_wcml_body', [ $this, 'render' ], 10, 2 );
	}

	public function render( $html, $key ) {
		if ( ! class_exists( '\WCML_Templates_Factory' ) || ! class_exists( '\WCML_Resources' ) ) {
			return $html;
		}

		$dependencies = \WPML\Container\make( \WCML_Dependencies::class );
		if ( ! $dependencies->check() ) {
			return $dependencies->get_missing_plugins_warning();
		}

		global $woocommerce_wpml, $sitepress, $sitepress_settings;

		switch ( $key ) {
			case 'settings':
				$this->enqueue( \WCML_Resources::FORCE_MODE_SETTINGS );
				return class_exists( '\WCML_Settings_UI' )
					? ( new \WCML_Settings_UI( $woocommerce_wpml, $sitepress ) )->get_view()
					: $html;

			case 'multi-currency':
				$this->enqueue( \WCML_Resources::FORCE_MODE_MULTI_CURRENCY );
				return class_exists( '\WCML_Multi_Currency_UI' )
					? ( new \WCML_Multi_Currency_UI( $woocommerce_wpml, $sitepress ) )->get_view()
					: $html;

			case 'store-url':
				$this->enqueue( \WCML_Resources::FORCE_MODE_STORE_URL );
				return class_exists( '\WCML_Store_URLs_UI' )
					? ( new \WCML_Store_URLs_UI( $woocommerce_wpml, $sitepress ) )->get_view()
					: $html;

			case 'wcml-status':
				$this->enqueue( \WCML_Resources::FORCE_MODE_STATUS );
				return class_exists( '\WCML_Status_UI' )
					? ( new \WCML_Status_UI( $woocommerce_wpml, $sitepress, $sitepress_settings ) )->get_view()
					: $html;

			case 'wcml-fix-translations':
				return $this->troubleshooting( $html, \WCML_Troubleshooting_UI::MODE_SAFE );

			case 'wcml-config-log':
				return $this->troubleshooting( $html, \WCML_Troubleshooting_UI::MODE_CONFIG );

			case 'wcml-translation-maintenance':
				return $this->troubleshooting( $html, \WCML_Troubleshooting_UI::MODE_ADVANCED );

			default:
				return $html;
		}
	}

	private function troubleshooting( $fallback, $mode ) {
		if ( ! class_exists( '\WCML_Troubleshooting_UI' ) ) {
			return $fallback;
		}
		global $woocommerce_wpml;
		$this->enqueue( \WCML_Resources::FORCE_MODE_TROUBLESHOOTING );

		return ( new \WCML_Troubleshooting_UI( $woocommerce_wpml, $mode ) )->get_view();
	}

	private function enqueue( $forceMode ) {
		\WCML_Resources::admin_scripts( $forceMode );
		\WCML_Resources::load_css( $forceMode );
	}
}
