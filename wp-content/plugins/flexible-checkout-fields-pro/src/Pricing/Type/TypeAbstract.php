<?php

namespace WPDesk\FCF\Pro\Pricing\Type;

/**
 * {@inheritdoc}
 */
abstract class TypeAbstract implements TypeInterface {

	/**
	 * Settings of pricing option.
	 *
	 * @var array
	 */
	private $option_data;

	/**
	 * Field value of pricing option.
	 *
	 * @var string
	 */
	private $option_value;

	/**
	 * Class constructor.
	 *
	 * @param array  $option_data  Settings of pricing option.
	 * @param string $option_value Field value of pricing option.
	 */
	public function __construct( array $option_data, string $option_value ) {
		$this->option_data  = $option_data;
		$this->option_value = $option_value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_data(): array {
		return $this->option_data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_value(): string {
		return $this->option_value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_tax_class(): string {
		$option = $this->get_option_data();
		if ( ( $option['tax_class'] === '' ) || ( $option['value'] < 0 ) ) {
			return 'non-taxable';
		}
		return ( $option['tax_class'] === 'standard' ) ? '' : $option['tax_class'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_percentage_of_fee_net_value( float $base_price ): float {
		if ( $this->get_option_data()['value'] < 0 ) {
			$totals      = \WC()->cart->get_totals();
			$total_net   = ( $totals['cart_contents_total'] + $totals['shipping_total'] );
			$total_taxes = ( $totals['cart_contents_tax'] + $this->get_taxes_for_shipping( $totals ) );
			$fees_net    = $this->get_nontaxable_fees_net();
			$fees_taxes  = $this->get_nontaxable_fees_taxes();
			$denominator = ( $total_net + $fees_net + $total_taxes + $fees_taxes );
			if ( $denominator > 0 ) {
				return ( ( $total_net + $fees_net ) / $denominator );
			} else {
				return 0;
			}
		} elseif ( $this->get_tax_class() !== 'non-taxable' ) {
			$tax_rates = \WC_Tax::get_rates( $this->get_tax_class(), \WC()->customer );
			$tax_value = array_sum( \WC_Tax::calc_tax( $base_price, $tax_rates, false ) );
			return ( ( $base_price + $tax_value ) == 0 ) ? 1 : ( $base_price / ( $base_price + $tax_value ) );
		}
		return 1;
	}

	/**
	 * Calculates amount of taxes for shipping.
	 *
	 * @param array $totals All calculated totals for Cart.
	 *
	 * @return float Taxes value for shipping.
	 */
	private function get_taxes_for_shipping( array $totals ): float {
		if ( $totals['shipping_tax'] > 0 ) {
			return $totals['shipping_tax'];
		}

		$tax_class = get_option( 'woocommerce_shipping_tax_class' );
		$cart      = \WC()->cart->get_cart();
		if ( ( $tax_class === 'inherit' ) && $cart ) {
			$tax_class = reset( $cart )['data']->get_tax_class();
		}
		$tax_rates = \WC_Tax::get_rates( $tax_class, \WC()->customer );
		return array_sum( \WC_Tax::calc_tax( $totals['shipping_total'], $tax_rates, false ) );
	}

	/**
	 * Calculate sum of net value for all positive fees.
	 *
	 * @return float Sum of net value for fees.
	 */
	private function get_nontaxable_fees_net(): float {
		$value = 0;
		foreach ( \WC()->cart->get_fees() as $fee ) {
			if ( $fee->amount > 0 ) {
				$value += $fee->amount;
			}
		}
		return $value;
	}

	/**
	 * Calculate sum of taxes value for all positive taxable fees.
	 *
	 * @return float Sum of taxes value for fees.
	 */
	private function get_nontaxable_fees_taxes(): float {
		$value = 0;
		foreach ( \WC()->cart->get_fees() as $fee ) {
			if ( ( $fee->amount > 0 ) && $fee->taxable ) {
				$tax_rates = \WC_Tax::get_rates( $fee->tax_class, \WC()->customer );
				$tax_value = array_sum( \WC_Tax::calc_tax( $fee->amount, $tax_rates, false ) );

				$value += $tax_value;
			}
		}
		return $value;
	}
}
