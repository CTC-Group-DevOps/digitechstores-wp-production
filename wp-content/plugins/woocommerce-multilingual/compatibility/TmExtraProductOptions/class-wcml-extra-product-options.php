<?php

class WCML_Extra_Product_Options implements \IWPML_Action {

	const TRANSLATION_DOMAIN = 'wc_extra_product_options';

	public function add_hooks() {
		add_action( 'tm_before_extra_product_options', [ $this, 'inf_translate_product_page_strings' ] );
		add_action( 'tm_before_price_rules', [ $this, 'inf_translate_strings' ] );
	}

	public function inf_translate_strings() {
		if ( isset( $_GET['page'] ) && 'tm-global-epo' === sanitize_text_field( $_GET['page'] ) ) {
			$this->inf_message( 'Options Form' );
		}
	}

	public function inf_translate_product_page_strings() {
		$this->inf_message( 'Product' );
	}

	public function inf_message( $text ) {
		$dashboardUrl  = \WCML\Utilities\AdminUrl::getWPMLTMDashboardStringDomain( self::TRANSLATION_DOMAIN );
		$message       = '<div><p class="icl_cyan_box">';
		$message      .= sprintf(
			/* translators: Help text on the extra product options panel. %1$s: opening link tag, %2$s: closing link tag. "Translation Dashboard" is a WPML screen name. */
			esc_html__( 'To translate custom product fields, save your changes here first, then go to the %1$sTranslation Dashboard%2$s.', 'woocommerce-multilingual' ),
			'<a href="' . esc_url( $dashboardUrl ) . '">',
			'</a>'
		);
		$message .= '</p></div>';

		echo $message;
	}
}
