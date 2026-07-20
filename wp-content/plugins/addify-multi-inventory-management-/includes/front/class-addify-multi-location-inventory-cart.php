<?php
/**
 * Cart file of Module
 *
 * Manage all settings and actions of cart
 *
 * @package  Addify Product Multi Location Inventory
 * @version  1.0.0
 */

defined('ABSPATH') || exit();

if ('yes' != get_option('mli_gen_backend_mode_only') && 'yes' == get_option('mli_shop_loc_name_stock')) {
	add_action('woocommerce_after_shop_loop_item_title', 'af_mli_shop_page_inven');
}

add_filter('woocommerce_add_cart_item_data', 'af_add_inven_data_in_cart_menu', 10, 2);

add_filter('woocommerce_get_item_data', 'af_mli_add_cart_item', 10, 2);

// Add option data in order.
add_action('woocommerce_checkout_create_order_line_item', 'af_checkout_create_order_line_item', 10, 4);

if ('yes' == get_option('mli_shop_hide_out_of_stock_prod')) {

	add_action('woocommerce_product_query', 'af_hide_out_of_stock_products', 10, 1);

}


function af_mli_shop_page_inven() {
	global $product;
	$_p_id = $product->get_id();

	$prod = $product;

	if ('variation' != $prod->get_type() && 'yes' != get_post_meta($_p_id, 'prod_level_inven', true)) {

		return;
	}


	$all_inven_values = af_mi_get_current_product_inventory($product->get_id());
	$all_inven_values = af_mli_custom_array_filter($all_inven_values);

	foreach ($all_inven_values as $key => $inven_value) {

		if (!is_array($inven_value) || !isset($inven_value['name'])) {
			continue;
		}

		$name = '';
		if ('yes' == get_option('mli_shop_loc_name_stock') && isset($inven_value['text'])) {
			$name .= $inven_value['text'];
		}
		if ('yes' == get_option('mli_shop_loc_price') && isset($inven_value['formatted_price'])) {
			$name .= ' - ' . $inven_value['formatted_price'];

		}
		if (!empty($name)) {
			$name = '<span>' . $name . '</span><br>';
			echo wp_kses($name, wp_kses_allowed_html('post'));

		}
	}
}

function af_hide_out_of_stock_products( $q ) {

	if (is_admin() || !$q->is_main_query()) {

		return;
	}

	if (is_shop()) {

		$q->set(
			'meta_query',
			array(

				'relation' => 'OR',

				array(

					'key'     => '_stock_status',

					'value'   => 'instock',

					'compare' => '=',

				),

				array(

					'key'     => '_stock_status',

					'value'   => 'onbackorder',

					'compare' => '=',

				),
			)
		);
	}
}
function af_add_inven_data_in_cart_menu( $cart_item_data, $product_id, $variation_id = 0 ) {

	$pro_id = $variation_id > 0 ? $variation_id : $product_id;

	if (isset($_POST['af_mli_inven_selector']) && !empty($_POST['af_mli_inven_selector'])) {

		$nonce = isset($_POST['af_inven_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['af_inven_nonce_field'])) : 0;

		if (isset($_POST['af_prd_page_field']) && !wp_verify_nonce($nonce, 'af_inven_nonce')) {

			wp_die('Failed Security Check');
		}
		$inven_selector = sanitize_text_field(wp_unslash($_POST['af_mli_inven_selector']));

		$cart_item_data['selected_location'] = array(
			'heading'        => 'Location',
			'selected_value' => $inven_selector,
		);

	} else {

		$all_inven_values = (array) af_mi_get_current_product_inventory($pro_id);
		$all_inven_values = af_mli_custom_array_filter($all_inven_values);

		if ('yes' == get_option('af_mli_auto_select_inventory_with_highest_stock')) {

			usort($all_inven_values, function ( $a, $b ) {
				if (isset($a['total_stock']) && isset($b['total_stock'])) {
					if ($b['total_stock'] > $a['total_stock']) {
						return 1;
					} elseif ($b['total_stock'] < $a['total_stock']) {
						return -1;
					} else {
						return 0;
					}
				} else {
					return 0; 
				}
			});
		}

		if (( count($all_inven_values) >= 1 )) {

			foreach ($all_inven_values as $location_object) {

				if (is_array($location_object) && isset($location_object['total_stock']) && $location_object['total_stock'] >= 1) {

					$cart_item_data['selected_location'] = array(
						'heading'        => 'Location',
						'selected_value' => $location_object['value'],
					);

					return $cart_item_data;
				}
			}

		}
	}


	return $cart_item_data;
}

function af_mli_add_cart_item( $item_data, $cart_item_data ) {

	$quantity      = $cart_item_data['quantity'];
	$_prod_id      = $cart_item_data['product_id'];
	$location_name = '';

	if (in_array('selected_location', array_keys($cart_item_data))) {

		$inventory = $cart_item_data['selected_location'];

		if (!empty($inventory) && !empty($inventory['selected_value'])) {

			$_loc = $inventory['selected_value'];

			$term = get_term($_loc);

		} else {

			$term = get_term(get_post_meta($_prod_id, 'in_location', true));

		}


		if (!is_wp_error($term)) {

			$location_name = !empty($term) ? $term->name : '';

			$item_data[] = array(
				'key'   => $inventory['heading'],
				'value' => $location_name,
			);

		}
	}

	return $item_data;
}

add_filter('woocommerce_get_cart_item_from_session', 'af_mli_get_cart_item_from_session', 1, 2);
function af_mli_get_cart_item_from_session( $cart_item, $values ) {

	$get_prod_id = $cart_item['variation_id'] > 0 ? $cart_item['variation_id'] : $cart_item['product_id'];


	if (isset($cart_item['selected_location']) && is_array($cart_item['selected_location']) && isset($cart_item['selected_location']['selected_value'])) {

		$i_id = $cart_item['selected_location']['selected_value'];

		// if ( get_post_meta($get_prod_id, 'in_location', true) != $i_id ) {

		$inven_posts = get_posts(
			array(
				'post_type'   => 'af_prod_lvl_invent',
				'post_status' => 'publish',
				'numberposts' => -1,
				'post_parent' => $get_prod_id,
				'fields'      => 'ids',
			)
		);

		foreach ($inven_posts as $inven_id) {
			// !empty(get_post_meta($inven_id, 'in_add_price', true)) && 
			if (get_post_meta($inven_id, 'in_location', true) == $i_id ) {

				$af_price = (float) ( empty(get_post_meta($inven_id, 'in_sale_price', true)) ? get_post_meta($inven_id, 'in_price', true) : get_post_meta($inven_id, 'in_sale_price', true) );

				if ($af_price >= 0.1) {
					$cart_item['data']->set_price($af_price);
				}

			}

		}
		// }


	}

	return $cart_item;
}


function af_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {

	foreach (WC()->cart->get_cart() as $item_key => $value_check) {


		if ($item_key == $cart_item_key && isset($value_check['selected_location'])) {

			$get_data_of_files = (array) $value_check['selected_location'];

			$item->add_meta_data('selected_location', $get_data_of_files, true);
		}
	}
}
