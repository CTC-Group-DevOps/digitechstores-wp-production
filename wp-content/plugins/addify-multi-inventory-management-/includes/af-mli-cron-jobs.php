<?php

function custom_cron_schedules( $schedules ) {
	// Add a custom interval of 30 seconds
	$schedules['every_60_seconds'] = array(
		'interval' => 60, // Interval in seconds
		'display'  => 'Every 60 Seconds',
	);
	return $schedules;
}
add_filter('cron_schedules', 'custom_cron_schedules');


function schedule_out_of_stock_check() {
	if (!wp_next_scheduled('af_mli_product_and_locations_get_stock_detail')) {
		wp_schedule_event(time(), 'every_60_seconds', 'af_mli_product_and_locations_get_stock_detail');
	}
}
add_action('wp', 'schedule_out_of_stock_check');
function af_mli_product_and_locations_get_stock_detail() {

	$af_products = wc_get_products(

		array(

			'type'   => array_merge(array( 'variation' ), array_keys(wc_get_product_types())),

			'status' => 'publish',

			'limit'  => -1,

			'return' => 'ids',
		)
	);

	$prod_total_quantity = 0;

	$prod_total_stock_q = 0;

	$out_stock = 0;

	$low_stock_prod = array();

	$over_stock_prod = array();

	$out_of_stocks_product = array();

	$prod_threshold = (int) !empty(get_option('af_mli_over_stock_quantity')) ? get_option('af_mli_over_stock_quantity') : 200;

	foreach ($af_products as $pro_id) {

		$pro_obj  = wc_get_product($pro_id);
		$prod_obj = $pro_obj;
		if ('variation' == $prod_obj->get_type()) {
			$parent_product_Detail = wc_get_product($prod_obj->get_parent_id());
			if ($parent_product_Detail && 'variable' != $parent_product_Detail->get_type()) {
				continue;
			}
		}

		af_mli_update_product_location_taxonomy($pro_obj);
		$prod_price               = !empty($pro_obj->get_price()) ? $pro_obj->get_price() : 0;
		$prod_stock_quantity      = (float) $pro_obj->get_stock_quantity();
		$prod_total_stock_price   = $prod_price * $prod_stock_quantity;
		$prod_total_quantity      = $prod_total_quantity + $prod_total_stock_price;
		$prod_total_stock_q      += $prod_stock_quantity;
		$prod_low_stock_threshold = (float) $pro_obj->get_low_stock_amount();
		$prod_curr_stock          = (float) $pro_obj->get_stock_quantity();

		if ('outofstock' == $pro_obj->get_stock_status() || 0 == $pro_obj->get_stock_quantity()) {

			++$out_stock;

		}

		$prod_inven = get_posts(
			array(
				'post_type'   => 'af_prod_lvl_invent',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'      => 'ids',
				'post_parent' => $pro_id,
			)
		);

		foreach ($prod_inven as $prod_inven_id) {

			if (empty($prod_inven_id)) {

				continue;
			}

			$inven_stock_quan = (float) get_post_meta($prod_inven_id, 'in_stock_quantity', true);

			$inven_price = (float) get_post_meta($prod_inven_id, 'in_price', true);

			$inven_total_stock_price = $inven_stock_quan * $inven_price;

			$prod_total_quantity = $prod_total_quantity + $inven_total_stock_price;

			$prod_total_stock_q = $prod_total_stock_q + $inven_stock_quan;
		}

		if (( $prod_low_stock_threshold && $prod_curr_stock ) && ( $prod_low_stock_threshold >= $prod_curr_stock )) {

			$low_stock_prod[ $prod_obj->get_id() ] = $prod_curr_stock . '---' . $prod_obj->get_name();
		}

		if (( $prod_threshold && $prod_curr_stock ) && ( $prod_threshold <= $prod_curr_stock )) {

			$over_stock_prod[ $prod_obj->get_id() ] = $prod_curr_stock . '---' . $prod_obj->get_name();
		}

		if (( !$prod_obj->is_in_stock() ) || ( $prod_obj->get_manage_stock() && ( $prod_obj->get_stock_quantity() <= 0 || 0 == $prod_curr_stock ) )) {



			$out_of_stocks_product[ $prod_obj->get_id() ] = $prod_curr_stock . '---' . $prod_obj->get_name();
		}
	}

	$prod_stock_con_price = convert_in_readable_numbers($prod_total_quantity);

	$converted_readable_number = get_woocommerce_currency_symbol() . ' ' . $prod_stock_con_price;

	$final_detil = array(
		'low_stock_prod'            => $low_stock_prod,
		'over_stock_prod'           => $over_stock_prod,
		'out_of_stocks_product'     => $out_of_stocks_product,
		'out_stock'                 => $out_stock,
		'converted_readable_number' => $converted_readable_number,
		'prod_total_stock_q'        => $prod_total_stock_q,
		// 'converted_readable_number' => $converted_readable_number,

	);
	update_option('af_mli_product_and_locations_get_stock_detail', $final_detil);
	// echo '<pre>';

	// print_r(count($out_of_stocks_product));
	// print_r($final_detil);


	// echo '</pre>';



	// -------------------------------------- Over Stock --------------------------------------.

	$all_prod_ids = new WC_Product_Query(
		array(
			'type'   => array( 'simple', 'variation', 'variable', 'grouped', 'external' ),

			'status' => 'publish',

			'limit'  => -1,

			'return' => 'ids',
		)
	);

	$prod_threshold = (int) get_option('af_mli_over_stock_quantity');

	$af_products = $all_prod_ids->get_products();

	$low_stock_product_ids  = array();
	$over_stock_product_ids = array();
	$outstock_product_ids   = array();

	foreach ($af_products as $prod_id) {

		$prod_obj = wc_get_product($prod_id);

		$prod_curr_stock = $prod_obj->get_stock_quantity();

		$prod_low_stock_threshold = $prod_obj->get_low_stock_amount();

		if ($prod_obj->get_manage_stock() && ( $prod_low_stock_threshold && $prod_curr_stock ) && ( $prod_low_stock_threshold >= $prod_curr_stock )) {

			$low_stock_product_ids[] = $prod_id;
		}

		if ($prod_obj->get_manage_stock() && $prod_curr_stock >= $prod_threshold) {

			$over_stock_product_ids[] = $prod_id;
		}


		if (( !$prod_obj->is_in_stock() ) || ( $prod_obj->get_manage_stock() && $prod_obj->get_stock_quantity() <= 0 )) {

			$outstock_product_ids[] = $prod_id;
		}

	}


	update_option('af_mli_main_product_detail', array(
		'low_stock_product_ids'  => $low_stock_product_ids,
		'over_stock_product_ids' => $over_stock_product_ids,
		'outstock_product_ids'   => $outstock_product_ids,
	));
}
add_action('af_mli_product_and_locations_get_stock_detail', 'af_mli_product_and_locations_get_stock_detail');

add_action('wp_footer', function () {
	//     af_mli_product_and_locations_get_stock_detail();
});

function af_mli_update_product_location_taxonomy( $product ) {
	if (!$product) {
		return;
	}

	if ('variation' == $product->get_type() || 'variable' == $product->get_type()) {

		$main_product_id = 'variation' == $product->get_type() ? wp_get_post_parent_id($product->get_id()) : $product->get_id();

		$main_product = wc_get_product($main_product_id);

		$all_vartion_attach_taxonomy = array();

		foreach ($main_product->get_children() as $variation_id) {

			$variation_obj = wc_get_product($variation_id);

			if (is_object($variation_obj) && ( !$variation_obj->get_manage_stock() || 'yes' != get_post_meta($variation_id, 'prod_level_inven', true) )) {
				continue;
			}

			// $all_vartion_attach_taxonomy[] = get_post_meta($variation_id, 'in_location', true);

			$inven_posts = get_posts(
				array(
					'post_type'   => 'af_prod_lvl_invent',

					'post_status' => 'publish',

					'numberposts' => -1,

					'post_parent' => $variation_id,

					'orderby'     => 'menu_order',

					'order'       => 'ASC',

					'fields'      => 'ids',
				)
			);
			$total_stock = 0;
			foreach ($inven_posts as $current_invent_id) {
				$all_vartion_attach_taxonomy[] = get_post_meta($current_invent_id, 'in_location', true);
				$total_stock                  += (int) get_post_meta($current_invent_id, 'in_stock_quantity', true);
			}

			if ($variation_obj->get_manage_stock()) {

				$variation_obj->set_stock_quantity($total_stock);

				update_post_meta($variation_obj->get_id(), '_stock', $total_stock);
				$variation_obj->save();
			}
		}

		wp_set_post_terms($main_product_id, (array) $all_vartion_attach_taxonomy, 'mli_location');

	} else {

		if ('yes' != get_post_meta($product->get_id(), 'prod_level_inven', true)) {
			return;
		}

		// $all_vartion_attach_taxonomy = [get_post_meta($product->get_id(), 'in_location', true)];
		$all_vartion_attach_taxonomy = array();

		$inven_posts = get_posts(
			array(
				'post_type'   => 'af_prod_lvl_invent',

				'post_status' => 'publish',

				'numberposts' => -1,

				'post_parent' => $product->get_id(),

				'orderby'     => 'menu_order',

				'order'       => 'ASC',

				'fields'      => 'ids',
			)
		);



		$total_stock = 0;
		foreach ($inven_posts as $current_invent_id) {
			$all_vartion_attach_taxonomy[] = get_post_meta($current_invent_id, 'in_location', true);
			$total_stock                  += (int) get_post_meta($current_invent_id, 'in_stock_quantity', true);
		}

		if ($product->get_manage_stock()) {

			$product->set_stock_quantity($total_stock);

			update_post_meta($product->get_id(), '_stock', $total_stock);
			$product->save();
		}
		wp_set_post_terms($product->get_id(), (array) $all_vartion_attach_taxonomy, 'mli_location');

	}
}
