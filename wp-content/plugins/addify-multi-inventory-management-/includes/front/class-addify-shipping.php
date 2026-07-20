<?php

defined('ABSPATH') || exit;

class Addify_Shipping {


	public function __construct() {

		add_filter('woocommerce_package_rates', array( $this, 'af_package_rates_filter' ), 10, 2);

		add_action('woocommerce_checkout_order_created', array( $this, 'af_mli_checkout_order_created' ), 10, 1);

		add_filter('woocommerce_available_payment_gateways', array( $this, 'af_mli_remove_payment_gateway' ), 10, 1);
	}

	public function af_mli_remove_payment_gateway( $gateways ) {

		if ('yes' == get_option('mli_shipp_payment_method_of_loc') && WC()->cart) {

			$loc_id = 0;

			$show_this_payment_method = array();


			foreach (WC()->cart->get_cart() as $cart_item_key => $cart_contents) {

				if (isset($cart_contents['selected_location'])) {

					$loc_id              = $cart_contents['selected_location']['selected_value'];
					$loc_payment_methods = (array) get_term_meta((int) $loc_id, 'af_mli_tax_payment_methods', true);

					$show_this_payment_method = array_merge($loc_payment_methods, $show_this_payment_method);
				}
			}

			if (count($show_this_payment_method) >= 1) {



				foreach ($gateways as $gateway_key => $gateway) {


					if (!in_array($gateway_key, $show_this_payment_method)) {

						unset($gateways[ $gateway_key ]);
					}
				}
			}
		}

		return $gateways;
	}

	public function af_package_rates_filter( $package_rates, $package ) {

		if ('yes' == get_option('mli_shipp_payment_method_of_loc')) {

			$loc_id                          = 0;
			$af_shipping_zones               = array();
			$af_mli_allowed_shipping_methods = array();

			foreach (WC()->cart->get_cart() as $cart_item_key => $cart_contents) {

				if (isset($cart_contents['selected_location'])) {

					$loc_id = $cart_contents['selected_location']['selected_value'];

					$af_shipping_zones = array_merge($af_shipping_zones, (array) get_term_meta((int) $loc_id, 'af_mli_tax_shipping_zones', true));

					$af_mli_allowed_shipping_methods = array_merge($af_mli_allowed_shipping_methods, (array) get_term_meta((int) $loc_id, 'af_mli_tax_shipping_methods', true));

				}
			}

			$shipping_packages = WC()->cart->get_shipping_packages();

			$shipping_zone = wc_get_shipping_zone(reset($shipping_packages));

			// echo '<pre>';
			// print_r($af_mli_allowed_shipping_methods);
			// print_r($package_rates);

			// echo '</pre>';

			if (!empty($af_mli_allowed_shipping_methods) && count($af_mli_allowed_shipping_methods) >= 1) {

				foreach ($package_rates as $shipping_ids => $shipping_obj) {

					if (!in_array($shipping_ids, $af_mli_allowed_shipping_methods)) {
						unset($package_rates[ $shipping_ids ]);
					}

				}

			}
		}

		return $package_rates;
	}

	public function af_mli_checkout_order_created( $order ) {

		$manager_email = '';

		$order_user_email = $order->get_data()['billing']['email'];

		$prod_name = '';

		$quantity = '';

		$location_name = '';

		foreach ($order->get_items() as $item_id => $item_object) {

			$item_meta = (array) wc_get_order_item_meta($item_id, 'selected_location');

			$product_id = $item_object->get_product_id();

			$prod_info = wc_get_product($product_id);

			$quantity = $item_object->get_quantity();

			$prod_name = $item_object->get_name();

			$manager_email = get_term_meta(get_post_meta($product_id, 'in_location', true), 'af_mli_tax_email', true);

			$term = get_term(get_post_meta($product_id, 'in_location', true));

			$location_name = !empty($term) ? $term->name : '';

			$inventories_ids = get_posts(
				array(

					'post_type'   => 'af_prod_lvl_invent',

					'post_status' => 'publish',

					'numberposts' => -1,

					'post_parent' => $product_id,

					'fields'      => 'ids',
				)
			);

			foreach ($inventories_ids as $inventory_id) {

				if (empty($inventory_id)) {

					continue;

				}

				if (get_post_meta($inventory_id, 'in_location', true) == $item_meta['selected_value']) {

					$manager_email = get_term_meta($item_meta['selected_value'], 'af_mli_tax_email', true);

					$term = get_term(get_post_meta($inventory_id, 'in_location', true));

					$location_name = !empty($term) ? $term->name : '';

					$remaining_stock = get_post_meta($inventory_id, 'in_stock_quantity', true);

					$remaining_stock = $remaining_stock - $quantity;

					update_post_meta($inventory_id, 'in_stock_quantity', $remaining_stock);

					$prod_info->set_stock_quantity($prod_info->get_stock_quantity() + $quantity);

					$prod_info->save();

				}
			}

			WC()->mailer()->emails['af_mli_shop_manager_email']->trigger($manager_email, $order_user_email, $prod_name, $quantity, $location_name);

		}
	}
}

new Addify_Shipping();
