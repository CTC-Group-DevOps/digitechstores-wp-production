<?php

namespace WPDesk\FCF\Pro\Pricing\Type;

/**
 * Support "Percent Subtotal incl. VAT" type of Pricing
 */
class TypePercentSubtotalTaxed extends TypeAbstract {

	/**
	 * {@inheritdoc}
	 */
	public function get_calculated_price(): float {
		$totals     = \WC()->cart->get_totals();
		$base_price = ( $totals['subtotal'] + $totals['subtotal_tax'] );
		$percent    = $this->get_percentage_of_fee_net_value( $base_price );

		return ( $percent * ( $base_price * $this->get_option_data()['value'] / 100 ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_price_for_label() {
		$value = $this->get_option_data()['value'];
		$price = abs( $value );

		if ( wc_tax_enabled() && ( get_option( 'woocommerce_tax_display_cart' ) === 'excl' ) ) {
			/* translators: %s: percentage value */
			$pattern = __( '%s%% of Subtotal incl. VAT', 'flexible-checkout-fields-pro' );
		} else {
			/* translators: %s: percentage value */
			$pattern = __( '%s%% of Subtotal', 'flexible-checkout-fields-pro' );
		}

		$price = sprintf( $pattern, $price );
		return sprintf( ' (%s %s)', ( $value < 0 ) ? '-' : '+', $price );
	}
}
