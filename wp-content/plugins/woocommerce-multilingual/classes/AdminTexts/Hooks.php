<?php

namespace WCML\AdminTexts;

use WPML\FP\Lst;
use WPML\LIB\WP\Hooks as WPHooks;

use function WPML\FP\spreadArgs;

class Hooks implements \IWPML_Frontend_Action, \IWPML_Backend_Action {

	public function add_hooks() {
		WPHooks::onFilter( 'wpml_st_blacklisted_options' )
			->then( spreadArgs( Lst::concat( [
				'woocommerce_permalinks',
				'woocommerce_calc_taxes',
				'woocommerce_prices_include_tax',
				'woocommerce_tax_based_on',
				'woocommerce_calc_discounts_sequentially',
			] ) ) );
	}

}
