<?php
/**
 * Admin file of Module
 *
 * Manage all settings and actions of admin
 *
 * @package  Addify Product Multi Location Inventory
 * @version  1.0.0
 */

defined('ABSPATH') || exit();

add_action('add_meta_boxes', 'af_mli_inventory_metabox_add');

add_action('admin_enqueue_scripts', 'af_mli_enqueue_script', 11);

add_filter('woocommerce_product_data_store_cpt_get_products_query', 'handle_custom_query_var', 100, 2);

add_action('admin_init', 'af_csv_import_export');

add_action('woocommerce_order_status_changed', 'custom_order_status_changed', 1, 4);

add_action('woocommerce_process_shop_order_meta', 'af_mli_shop_order', 10, 1);
function af_mli_enqueue_script() {

	wp_enqueue_media();

	wp_enqueue_script('google-charts', 'https://www.gstatic.com/charts/loader.js', array( 'jquery' ), '1.1.0', true);

	wp_enqueue_style('mli_admin_css', plugins_url('../../assets/css/af-m-l-i-admin.css', __FILE__), array(), '1.0.0');

	wp_enqueue_script('mli_admin_js', plugins_url('../../assets/js/af-m-l-i-admin.js', __FILE__), array( 'jquery' ), '1.1.0', true);

	wp_enqueue_style('mli_frontawesome', AFMLI_URL . 'assets/font-awesome/css/font-awesome.min.css', array(), '4.7.0');

	wp_enqueue_style('select2-css', plugins_url('assets/css/select2.css', WC_PLUGIN_FILE), array(), '5.7.2');

	wp_enqueue_script('select2-js', plugins_url('assets/js/select2/select2.min.js', WC_PLUGIN_FILE), array( 'jquery' ), '4.0.3', true);

	$in_s_color = get_option('af_mli_in_stock_color');

	$o_of_s_color = get_option('af_mli_out_of_stock_color');

	$b_o_color = get_option('af_mli_back_order_color');

	$l_s_color = get_option('af_mli_low_stock_color');

	$o_s_color = get_option('af_mli_over_stock_color');

	$over_s_th = get_option('af_mli_over_stock_quantity');

	$low_stock = get_option('woocommerce_notify_low_stock_amount');

	$empty_stock = get_option('woocommerce_notify_no_stock_amount');

	$af_mli_colors = array( $in_s_color, $o_of_s_color, $b_o_color, $l_s_color, $o_s_color );

	ob_start();

	af_mli_display_field_border_radius();

	$af_field_border_radius = ob_get_clean();

	ob_start();

	af_mli_display_field_padding();

	$af_mli_display_field_padding = ob_get_clean();

	ob_start();

	af_mli_gen_excl_loc_front_end();

	$af_mli_gen_excl_loc_front_end = ob_get_clean();

	ob_start();

	af_mli_gen_backend_mode_only_rules();

	$af_mli_gen_backend_mode_only_rules = ob_get_clean();

	$af_mli_ajax_nonce = array(

		'admin_url'                          => admin_url('admin-ajax.php'),

		'nonce'                              => wp_create_nonce('_addify_mli_nonce'),

		// 'chart_array' => af_get_posts(),

		'colors'                             => $af_mli_colors,

		'over_stock'                         => $over_s_th,

		'low_stock'                          => $low_stock,

		'empty_stock'                        => $empty_stock,

		'af_field_border_radius'             => $af_field_border_radius,

		'af_mli_display_field_padding'       => $af_mli_display_field_padding,

		'af_mli_gen_excl_loc_front_end'      => $af_mli_gen_excl_loc_front_end,

		'af_mli_gen_backend_mode_only_rules' => $af_mli_gen_backend_mode_only_rules,

		'manage_stock_tab'                   => admin_url('admin.php?page=af_i_m_l_gen_settings&tab=Manage_Stock'),
	);

	wp_localize_script('mli_admin_js', 'addify_multi_inventory', $af_mli_ajax_nonce);
}

function af_get_posts() {

	$logs = get_posts(
		array(

			'post_type'   => 'af_stock_log',

			'post_status' => 'publish',

			'numberposts' => -1,

			'date_query'  => array(
				array(
					'column' => 'post_date_gmt',
					'after'  => gmdate('Y-m-d', strtotime('-30 days')),
				),
			),

			'orderby'     => 'post_date',

			'order'       => 'ASC',
		)
	);

	$date_array = array();

	$chart_main_array = array();

	$started_date = gmdate('Y-m-d', strtotime('-30 days'));

	$chart_main_array[0][0] = 'Date';

	$chart_main_array[0][1] = 'Main Stock';

	$inventory_locations = get_terms(
		array(
			'number'     => 0,
			'hide_empty' => false,
			'taxonomy'   => 'mli_location',
		)
	);

	$mli_counter = 2;

	for ($i = 0; $i < 30; $i++) {

		$date_array[] = gmdate('Y-m-d', strtotime($started_date . '+' . $i . ' days'));
	}

	foreach ($inventory_locations as $key => $location) {

		$chart_main_array[0][ $mli_counter ] = $location->name;

		++$mli_counter;

	}

	foreach ($date_array as $date) {

		$date_data = array();

		$date_data[] = $date;

		$date_data['main_stock'] = 0;

		foreach ($inventory_locations as $key => $location) {

			$date_data[ $location->term_id ] = 0;
		}

		foreach ($logs as $key => $log) {

			$log_post_date = gmdate('Y-m-d', strtotime($log->post_date_gmt));

			if ($date == $log_post_date) {

				$stock_detail = get_post_meta($log->ID, 'stock_details', true);

				$term_id = get_post_meta($log->ID, 'term_id', true);

				$date_data[ $term_id ] = (int) $stock_detail;
			}
		}

		$chart_main_array[] = array_slice(array_values($date_data), 0, count($chart_main_array[0]));
	}

	return $chart_main_array;
}

function af_mli_inventory_metabox_add() {

	add_meta_box(
		'af_prod_stock_log_metabox_id',
		esc_html__('Stock', 'addify-multi-inventory-management'),
		'af_prod_stock_log_callback',
		'af_stock_log'
	);
}

function af_prod_stock_log_callback() {

	$stock_details = get_post_meta(get_the_ID(), 'stock_details', true);

	?>

	<div class="af_mli_metabox">

		<div class="af_mli_heading">

			<h3><b><?php echo esc_html__('Current Stock', 'addify-multi-inventory-management'); ?></b></h3>

		</div>

		<div class="af_mli_description">

			<input type="text" name="af_mli_log_stock" class="af_mli_log_stock" readonly
				value="<?php echo esc_attr($stock_details); ?>">

			<p><i><?php echo esc_html__('Current Stock on this', 'addify-multi-inventory-management'); ?></i></p>
		</div>
	</div>
	<?php
}

function handle_custom_query_var( $query, $query_vars ) {

	$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

	if (isset($_POST['min_stock_quan']) && !wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}

	$min_stock_quan = isset($_POST['min_stock_quan']) ? sanitize_text_field(wp_unslash($_POST['min_stock_quan'])) : '';

	$max_stock_quan = isset($_POST['max_stock_quan']) ? sanitize_text_field(wp_unslash($_POST['max_stock_quan'])) : '';

	if (!empty($query_vars['stock_quantity_range'])) {

		$query['meta_query'][] = array(

			'relation' => 'OR',

			array(

				'key'     => '_stock',

				'value'   => array( $min_stock_quan, $max_stock_quan ),

				'type'    => 'numeric',

				'compare' => 'BETWEEN',

			),
		);
	}

	return $query;
}

function af_csv_import_export() {

	include AFMLI_PLUGIN_DIR . 'includes/admin/inven_csv/class-addify-inventory-csv-export-import.php';

	$class = new Addify_Inventory_CSV_Export_Import();

	if (isset($_POST['af_export_file'])) {

		$nonce = isset($_POST['af_mli_export_csv_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['af_mli_export_csv_nonce_field'])) : 0;

		if (!wp_verify_nonce($nonce, 'af_mli_export_csv_nonce')) {

			wp_die('Failed Security Check');
		}

		$name = $class->af_inven_export_csv();
	}
	if (isset($_POST['af_mli_import_button']) && !empty($_FILES['uploadFile'])) {

		$nonce = isset($_POST['af_mli_import_csv_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['af_mli_import_csv_nonce_field'])) : 0;

		if (!wp_verify_nonce($nonce, 'af_mli_import_csv_nonce')) {

			wp_die('Failed Security Check');
		}

		$name = $class->af_inven_import_csv();
	}
}

function custom_order_status_changed( $order_id, $old_status, $new_status, $order ) {

	if (in_array($old_status, array( 'processing', 'completed' )) && in_array($new_status, array( 'cancelled', 'cancel' ))) {


		foreach ($order->get_items() as $item_id => $item_object) {

			$item_meta = (array) wc_get_order_item_meta($item_id, 'selected_location');

			$af_old_loc_select = isset($item_meta['selected_value']) ? $item_meta['selected_value'] : 0;

			$af_mli_inventory_detail = (array) wc_get_order_item_meta($item_id, 'af_mli_inventory_detail', true);

			$product_id = $item_object->get_variation_id() ? $item_object->get_variation_id() : $item_object->get_product_id();

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

				if (get_post_meta($inventory_id, 'in_location', true) == $af_old_loc_select ) {

					// if (isset($af_mli_inventory_detail[$af_old_loc_select])) {
					// $remaining_stock = $af_mli_inventory_detail[$af_old_loc_select];
					// update_post_meta($inventory_id, 'in_stock_quantity', $remaining_stock);

					// }

					$old_stock = (float) get_post_meta($inventory_id, 'in_stock_quantity', true);
					update_post_meta($inventory_id, 'in_stock_quantity', $old_stock + $item_object->get_quantity());

				} elseif ($af_old_loc_select == $product_id) {

					// if (isset($af_mli_inventory_detail[$af_old_loc_select])) {

					//     $remaining_stock = $af_mli_inventory_detail[$af_old_loc_select];
					//     update_post_meta($inventory_id, 'in_stock_quantity', $remaining_stock);

					// }


					$old_stock = (float) get_post_meta($product_id, 'in_stock_quantity', true);
					update_post_meta($product_id, 'in_stock_quantity', $old_stock + $item_object->get_quantity());

				}

			}


		}
	}

	if (in_array($new_status, array( 'processing', 'completed' )) && in_array($old_status, array( 'cancelled', 'cancel', 'hold' ))) {


		$nonce = isset($_POST['order_detail_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['order_detail_nonce_field'])) : '';
		if (!is_ajax()) {

			if ('yes' == get_option('mli_gen_backend_mode_only') && !wp_verify_nonce($nonce, 'order_detail_nonce')) {

				wp_die('Failed Security Check');
			}

			if (!wp_verify_nonce($nonce, 'order_detail_nonce')) {

				wp_die('Failed Security Check');
			}

		}


		$order = wc_get_order($order_id);


		foreach ($order->get_items() as $item_id => $item_object) {

			$product_id = $item_object->get_variation_id() && $item_object->get_variation_id() >= 1 ? $item_object->get_variation_id() : $item_object->get_product_id();
			$item_meta  = (array) wc_get_order_item_meta($item_id, 'selected_location');
			$item_meta  = af_mli_custom_array_filter($item_meta);

			$af_old_loc_select = isset($item_meta['selected_value']) ? $item_meta['selected_value'] : 0;

			$af_i_loc_select = isset($_POST['af_i_loc_select'][ $item_id ]) ? sanitize_text_field(wp_unslash($_POST['af_i_loc_select'][ $item_id ])) : $af_old_loc_select;

			$af_mli_last_qty_detail = (array) wc_get_order_item_meta($item_id, 'af_mli_last_qty_detail', true);

			$af_mli_last_qty_detail = af_mli_custom_array_filter($af_mli_last_qty_detail);

			$inventories_ids         = get_posts(
				array(
					'post_type'   => 'af_prod_lvl_invent',
					'post_status' => 'publish',
					'numberposts' => -1,
					'post_parent' => $product_id,
					'fields'      => 'ids',
				)
			);
			$af_mli_inventory_detail = (array) wc_get_order_item_meta($item_id, 'af_mli_inventory_detail', true);

			// if ($af_i_loc_select == $af_old_loc_select && (isset($af_mli_inventory_detail[$af_old_loc_select]))) {

			if ($af_i_loc_select != $af_old_loc_select) {

				// echo 'continue';
				// exit;

				continue;
			}

			// echo $af_i_loc_select . ' != ' . $af_old_loc_select;
			// exit;

			$prod_info = wc_get_product($product_id);
			$quantity  = $item_object->get_quantity();

			$prod_name     = $item_object->get_name();
			$manager_email = get_term_meta($af_i_loc_select, 'af_mli_tax_email', true);

			$term = get_term($af_i_loc_select);

			$location_name = !empty($term) ? $term->name : '';
			$location_id   = !empty($term) ? $term->id : '';


			foreach ($inventories_ids as $inventory_id) {

				if (empty($inventory_id)) {
					continue;
				}

				// echo '<br> --> current inventory id ==>' . get_post_meta($inventory_id, 'in_location', true);


				// ----------------- deducting quantity of current inventory -----------------.

				if (get_post_meta($inventory_id, 'in_location', true) == $af_i_loc_select) {

					$manager_email = get_term_meta($af_i_loc_select, 'af_mli_tax_email', true);

					$term = get_term(get_post_meta($inventory_id, 'in_location', true));

					$remaining_stock = get_post_meta($inventory_id, 'in_stock_quantity', true);

					if (isset($af_mli_last_qty_detail['last_deducted_qty'])) {

						if ($af_mli_last_qty_detail['last_deducted_qty'] != $quantity) {

							$remaining_stock += ( (float) $af_mli_last_qty_detail['last_deducted_qty'] - (float) $quantity );

						}
					}

					$remaining_stock -= $quantity;

					if ($remaining_stock <= 0) {
						$remaining_stock = 0;
					}


					$af_mli_inventory_detail[ $inventory_id ] = $remaining_stock;

					// echo '<br> Now --- Title => ' . get_term(get_post_meta($inventory_id, 'in_location', true))->name . ' remaining_stock ==> ' . $remaining_stock . ' ===> before deduction ===>>>>' . get_post_meta($inventory_id, 'in_stock_quantity', true);

					update_post_meta($inventory_id, 'in_stock_quantity', $remaining_stock);

					WC()->mailer()->emails['af_mli_shop_manager_email']->trigger($manager_email, $order->get_billing_email(), $prod_name, $quantity, $location_name);

				}
				// elseif ($af_i_loc_select == $product_id) {

					// $af_mli_last_qty_detail['last_deducted_qty'] = $quantity;

					// $loc_id = get_post_meta($product_id, 'in_location', true);

					// $manager_email = get_term_meta($loc_id, 'af_mli_tax_email', true);

					// $term = get_term(get_post_meta($product_id, 'in_location', true));

					// $location_name = !empty($term) ? $term->name : '';

					// $remaining_stock = $prod_info->get_stock_quantity();

					// $remaining_stock -= $quantity;

					// if ($remaining_stock <= 0) {
					//  $remaining_stock = 0;
					// }
					// $af_mli_inventory_detail[$inventory_id] = $remaining_stock;

					// $af_mli_inventory_detail['remaining_stock'] = $remaining_stock;

					// update_post_meta($inventory_id, 'in_stock_quantity', $remaining_stock);

					// WC()->mailer()->emails['af_mli_shop_manager_email']->trigger($manager_email, $order->get_billing_email(), $prod_name, $quantity, $location_name);

				// }


			}

			wc_update_order_item_meta($item_id, 'af_mli_inventory_detail', $af_mli_inventory_detail);
			wc_update_order_item_meta($item_id, 'af_mli_last_qty_detail', $af_mli_last_qty_detail);

		}

	}
}
function af_mli_shop_order( $order_id ) {

	$order = wc_get_order($order_id);
	if ($order && in_array($order->get_status(), array( 'processing', 'completed' ))) {
		foreach ($order->get_items() as $item_id => $item_object) {

			$product_id = $item_object->get_variation_id() && $item_object->get_variation_id() >= 1 ? $item_object->get_variation_id() : $item_object->get_product_id();

			$item_meta = (array) wc_get_order_item_meta($item_id, 'selected_location');
			$item_meta = af_mli_custom_array_filter($item_meta);

			$af_old_loc_select = isset($item_meta['selected_value']) ? $item_meta['selected_value'] : 0;

			$af_i_loc_select = isset($_REQUEST['af_i_loc_select'][ $item_id ]) ? sanitize_text_field(wp_unslash($_REQUEST['af_i_loc_select'][ $item_id ])) : $af_old_loc_select;

			$af_mli_last_qty_detail = (array) wc_get_order_item_meta($item_id, 'af_mli_last_qty_detail', true);

			$af_mli_last_qty_detail = af_mli_custom_array_filter($af_mli_last_qty_detail);

			wc_update_order_item_meta($item_id, 'af_i_loc_select', $af_i_loc_select);

			if (!empty($af_i_loc_select)) {
				wc_update_order_item_meta($item_id, 'selected_location', array(
					'heading'        => 'Locations',
					'selected_value' => $af_i_loc_select,
				));
			}

			$inventories_ids         = get_posts(
				array(

					'post_type'   => 'af_prod_lvl_invent',

					'post_status' => 'publish',

					'numberposts' => -1,

					'post_parent' => $product_id,

					'fields'      => 'ids',
				)
			);
			$af_mli_inventory_detail = (array) wc_get_order_item_meta($item_id, 'af_mli_inventory_detail', true);

			// if ($af_i_loc_select == $af_old_loc_select && (isset($af_mli_inventory_detail[$af_old_loc_select]))) {
			if ($af_i_loc_select == $af_old_loc_select) {

				// foreach ($inventories_ids as $inventory_id) {

				//  if (empty($inventory_id)) {
				//      continue;
				//  }

				//  if ($af_old_loc_select == get_post_meta($inventory_id, 'in_location', true) && $af_mli_inventory_detail[$af_old_loc_select] != get_post_meta($inventory_id, 'in_stock_quantity', true)) {
				//      // continue 2;
				//  }
				// }

				if (isset($af_mli_last_qty_detail['last_deducted_qty']) && $item_object->get_quantity() == $af_mli_last_qty_detail['last_deducted_qty']) {

					continue;

				}

			}

			$prod_info = wc_get_product($product_id);
			$quantity  = $item_object->get_quantity();

			$prod_name     = $item_object->get_name();
			$manager_email = get_term_meta($af_i_loc_select, 'af_mli_tax_email', true);

			$term = get_term($af_i_loc_select);

			$location_name = !empty($term) ? $term->name : '';
			$location_id   = !empty($term) ? $term->id : '';

			// echo $af_old_loc_select . ' ====== ' . $af_i_loc_select;

			foreach ($inventories_ids as $inventory_id) {

				if (empty($inventory_id)) {
					continue;
				}

				// echo '<br> --> current inventory id ==>' . get_post_meta($inventory_id, 'in_location', true);

				if (get_post_meta($inventory_id, 'in_location', true) == $af_old_loc_select) {

					$remaining_stock  = (float) get_post_meta($inventory_id, 'in_stock_quantity', true);
					$remaining_stock += $quantity;

					// if (isset($af_mli_inventory_detail[ $af_old_loc_select ])) {
						// $remaining_stock = $af_mli_inventory_detail[$af_old_loc_select];
					// }

					if (isset($af_mli_last_qty_detail['last_deducted_qty'])) {
						$remaining_stock = (float) get_post_meta($inventory_id, 'in_stock_quantity', true) + (float) $af_mli_last_qty_detail['last_deducted_qty'];
					}

					// echo '<br> Update Old Title => ' . get_term(get_post_meta($inventory_id, 'in_location', true))->name . ' remaining_stock ==> ' . $remaining_stock;

					update_post_meta($inventory_id, 'in_stock_quantity', $remaining_stock);

				} elseif ($af_old_loc_select == $product_id) {

					$remaining_stock  = $prod_info->get_stock_quantity();
					$remaining_stock += $quantity;

					// if (isset($af_mli_inventory_detail[ $af_old_loc_select ])) {
						// $remaining_stock = $af_mli_inventory_detail[$af_old_loc_select];
					// }

					// update_post_meta($inventory_id, 'in_stock_quantity', $remaining_stock);
				}


				// ----------------- deducting quantity of current inventory -----------------.

				if (get_post_meta($inventory_id, 'in_location', true) == $af_i_loc_select) {

					$manager_email = get_term_meta($af_i_loc_select, 'af_mli_tax_email', true);

					$term = get_term(get_post_meta($inventory_id, 'in_location', true));

					$location_name = !empty($term) ? $term->name : '';

					$remaining_stock = get_post_meta($inventory_id, 'in_stock_quantity', true);

					if (isset($af_mli_last_qty_detail['last_deducted_qty'])) {

						if ($af_mli_last_qty_detail['last_deducted_qty'] != $quantity) {

							$remaining_stock += ( (float) $af_mli_last_qty_detail['last_deducted_qty'] - (float) $quantity );

						}
					}

					$remaining_stock -= $quantity;

					if ($remaining_stock <= 0) {
						$remaining_stock = 0;
					}

					$af_mli_last_qty_detail['last_deducted_qty']    = $quantity;
					$af_mli_last_qty_detail['if_decrease_set_this'] = ( (float) $remaining_stock - (float) $quantity );
					$af_mli_last_qty_detail['if_increase_set_this'] = ( (float) $remaining_stock + (float) $quantity );


					$af_mli_inventory_detail[ $inventory_id ] = $remaining_stock;

					// echo '<br> Now --- Title => ' . get_term(get_post_meta($inventory_id, 'in_location', true))->name . ' remaining_stock ==> ' . $remaining_stock . ' ===> before deduction ===>>>>' . get_post_meta($inventory_id, 'in_stock_quantity', true);

					update_post_meta($inventory_id, 'in_stock_quantity', $remaining_stock);

					WC()->mailer()->emails['af_mli_shop_manager_email']->trigger($manager_email, $order->get_billing_email(), $prod_name, $quantity, $location_name);

				}
				// elseif ($af_i_loc_select == $product_id) {

					// $af_mli_last_qty_detail['last_deducted_qty'] = $quantity;

					// $loc_id = get_post_meta($product_id, 'in_location', true);

					// $manager_email = get_term_meta($loc_id, 'af_mli_tax_email', true);

					// $term = get_term(get_post_meta($product_id, 'in_location', true));

					// $location_name = !empty($term) ? $term->name : '';

					// $remaining_stock = $prod_info->get_stock_quantity();

					// $remaining_stock -= $quantity;

					// if ($remaining_stock <= 0) {
					//  $remaining_stock = 0;
					// }
					// $af_mli_inventory_detail[$inventory_id] = $remaining_stock;

					// $af_mli_inventory_detail['remaining_stock'] = $remaining_stock;

					// update_post_meta($inventory_id, 'in_stock_quantity', $remaining_stock);

					// WC()->mailer()->emails['af_mli_shop_manager_email']->trigger($manager_email, $order->get_billing_email(), $prod_name, $quantity, $location_name);

				// }


			}

			wc_update_order_item_meta($item_id, 'af_mli_inventory_detail', $af_mli_inventory_detail);
			wc_update_order_item_meta($item_id, 'af_mli_last_qty_detail', $af_mli_last_qty_detail);


		}

		// exit;
	}
}