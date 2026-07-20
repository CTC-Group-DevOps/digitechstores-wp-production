<?php

defined('ABSPATH') || exit;



add_action('wp_ajax_af_mli_prod_ajax', 'af_mli_prod_ajax');

add_action('wp_ajax_af_prd_lvl_add_inventory', 'af_prd_lvl_add_inventory');

add_action('wp_ajax_af_pord_inven_remove', 'af_pord_inven_remove');

add_action('wp_ajax_af_dash_graph_filter', 'af_dash_graph_filter');

add_action('wp_ajax_af_dash_graph_refresh', 'af_dash_graph_refresh');

add_filter('posts_where', 'af_mli_filter_by_title', 99, 2);

add_action('wp_ajax_af_dependable_shipp_methods', 'af_dependable_shipp_methods');

add_action('wp_ajax_af_dependable_country_states', 'af_dependable_country_states');

add_action('wp_ajax_af_vari_prod_inven', 'af_vari_prod_inven');

add_action('wp_ajax_nopriv_af_vari_prod_inven', 'af_vari_prod_inven');

add_action('wp_ajax_af_ms_update_optn', 'af_ms_update_optn');

function af_ms_update_optn() {

	$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

	if (!wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}

	$selected_checkboxes = isset($_POST['selected_checkboxes']) ? sanitize_meta('', wp_unslash($_POST['selected_checkboxes']), '') : array();

	update_option('af_mli_ms_checkbox', $selected_checkboxes);

	wp_die();
}

function af_mli_prod_ajax() {

	$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

	if (!wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}
	$pro = isset($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';


	$data_array = array();

	$pros = get_posts(
		array(
			'post_type'   => 'product',

			'post_status' => 'any',

			'numberposts' => -1,

			's'           => $pro,
		)
	);

	if (!empty($pros)) {

		foreach ($pros as $proo) {

			$title = ( mb_strlen($proo->post_title) > 50 ) ? mb_substr($proo->post_title, 0, 49) . '...' : $proo->post_title;

			$data_array[] = array( $proo->ID, $title ); // array( Post ID, Post Title ).
		}
	}

	echo wp_json_encode($data_array);

	wp_die();
}

function af_prd_lvl_add_inventory() {

	$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

	if (!wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}

	if (isset($_POST['current_rule_id']) && isset($_POST['inventory_name']) && isset($_POST['prod_type'])) {

		$current_rule_id = sanitize_text_field(wp_unslash($_POST['current_rule_id']));

		$inventory_name = sanitize_text_field(wp_unslash($_POST['inventory_name']));

		$prod_type = sanitize_text_field(wp_unslash($_POST['prod_type']));

		$insert_field = wp_insert_post(
			array(
				'post_type'   => 'af_prod_lvl_invent',
				'post_status' => 'publish',
				'post_parent' => $current_rule_id,
				'post_title'  => $inventory_name,
			)
		);

		$prod_inven_id = $insert_field;

		$current_prod_id = get_the_ID();

		?>

		<input type="hidden" name="prd_lvl_inven_hidden" class="prd_lvl_inven_hidden"
			value="<?php echo esc_attr($prod_inven_id); ?>">

		<?php

		if ('simple' == $prod_type) {

			af_mli_prod_lvl_ajax_inventory_template($current_prod_id, $prod_inven_id);

		} else {

			af_mli_variation_inventory_fields_template($current_prod_id, $prod_inven_id);

		}
	}

	wp_die();
}

function af_vari_prod_inven() {

	$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

	if (!wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}

	$inventories              = '';
	$whole_inventories_detail = array();

	ob_start();

	if (isset($_POST['form_data'])) {

		parse_str(sanitize_text_field(wp_unslash($_POST['form_data'])), $form_data);

		if (!empty($form_data['variation_id'])) {

			if (!empty(get_post_meta($form_data['variation_id'], 'in_location', true))) {

				af_mi_get_product_inventory($form_data['variation_id']);
			}

			$all_inven_values = (array) af_mi_get_current_product_inventory($form_data['variation_id']);
			$all_inven_values = af_mli_custom_array_filter($all_inven_values);

			$af_selected_location = (array) wc()->session->get('af_user_selected_location');
			$af_selected_location = af_mli_custom_array_filter($af_selected_location);


			if ('hide_disable_add_to_cart' == get_option('af_mli_on_lcation_selection')) {

				if (count($af_selected_location) >= 1 && ( count($all_inven_values) < 1 )) {

					$inventory_name = array();

					foreach ($af_selected_location as $location_id) {

						$location_object = get_term($location_id);
						if (is_object($location_object) && isset($location_object->name)) {
							$inventory_name[] = $location_object->name;
						}
					}
					$html = str_replace('{inventory_name}', implode(' , ', $inventory_name), get_option('af_mli_out_of_stock_error_msg'));
					$whole_inventories_detail['hide_add_to_cart'] = 'yes';
					$whole_inventories_detail['error']            = $html;

				}
			}
		}

	}

	$inventories = ob_get_clean();

	$whole_inventories_detail['inventories_detail'] = $inventories;


	wp_send_json_success($whole_inventories_detail);

	wp_die();
}

function af_pord_inven_remove() {

	$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

	if (!wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}

	if (isset($_POST['current_prod_id']) && isset($_POST['remove_inventory_id'])) {

		$current_prod_id = isset($_POST['current_prod_id']) ? sanitize_text_field(wp_unslash($_POST['current_prod_id'])) : '';

		$remove_inventory_id = isset($_POST['remove_inventory_id']) ? sanitize_text_field(wp_unslash($_POST['remove_inventory_id'])) : '';

		wp_delete_post($remove_inventory_id);
	}

	wp_die();
}

function af_dash_graph_filter() {
	$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

	if (!wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}

	$f_data                   = isset($_POST['form_data']) ? sanitize_text_field($_POST['form_data']) : 0;
	$af_dash_filter_warehouse = array();
	if (isset($_POST['af_dash_filter_warehouse']) && !empty($_POST['af_dash_filter_warehouse'])) {

		$af_dash_filter_warehouse = sanitize_meta('', wp_unslash($_POST['af_dash_filter_warehouse']), '');

	}

	if (count($af_dash_filter_warehouse) < 1 || in_array('all', $af_dash_filter_warehouse)) {

		$af_dash_filter_warehouse = array();
		$terms                    = get_terms(array(
			'number'     => 0, // Retrieve all terms
			'taxonomy'   => 'mli_location',
			'hide_empty' => false, // Include terms without assigned posts
		));
		foreach ($terms as $term) {
			$af_dash_filter_warehouse[] = $term->term_id;
		}

	}

	// Parse the query string
	parse_str($f_data, $form_data);

	$nonce = isset($form_data['nonce']) ? sanitize_text_field(wp_unslash($form_data['nonce'])) : 0;

	if (!wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}


	// $warehouse = isset($form_data['af_dash_to_ware_house']) ? (array) $form_data['af_dash_to_ware_house'] : array();

	$warehouse = $af_dash_filter_warehouse;

	$date_array = array();

	// $chart_main_array = array(['Date', 'Main Stock']);
	$chart_main_array = array( array( 'Date' ) );

	$min_date = isset($form_data['af_dash_from_date']) && !empty($form_data['af_dash_from_date']) ? $form_data['af_dash_from_date'] : gmdate('Y-1-1');

	$max_date = isset($form_data['af_dash_to_date']) && !empty($form_data['af_dash_to_date']) ? $form_data['af_dash_to_date'] : gmdate('Y-m-d');

	$max_date = gmdate('Y-m-d', strtotime($max_date . ' +1 day'));

	if (!empty($min_date) && !empty($max_date)) {

		$min_date_comp = strtotime($min_date);

		$max_date_comp = strtotime($max_date);

		if ($min_date_comp > $max_date_comp) {

			ob_start();

			?>

			<div id="myModal" class="new_dashboard_modal">

				<div class="af_dash_model_content">

					<h3 class="af_title_message">
						<em><?php echo esc_html__('You have entered wrong date, kindly enter it in correct format ', 'addify-multi-inventory-management'); ?></em>
					</h3>

					<span>

						<i name="af_ok_call_ajax"
							class="af_ok_call_ajax"><?php echo esc_html__('Ok', 'addify-multi-inventory-management'); ?></i>

					</span>

					<br><br><br>

				</div>

			</div>

			<?php

			$result = ob_get_clean();

			wp_send_json(
				array(

					'new_html'   => $result,

					'date_error' => 'yes',
				)
			);
		}
	}

	$min_date_1 = date_create($min_date);

	$max_date_1 = date_create($max_date);

	$diff = date_diff($min_date_1, $max_date_1);

	$date_difference = $diff->format('%a');

	for ($i = 0; $i <= $date_difference; $i++) {

		$date_array[] = gmdate('Y-m-d', strtotime($min_date . '+' . $i . ' days'));
	}

	$args = array(

		'post_type'   => 'af_stock_log',
		'post_status' => 'publish',
		'numberposts' => -1,
		'field'       => 'ids',
		'date_query'  => array(
			array(

				'column' => 'post_date_gmt',

				'before' => $max_date,

				'after'  => $min_date,
			),
		),
		'meta_query'  => array(
			array(
				'key'     => 'term_id',
				'value'   => $warehouse,
				'compare' => 'IN',
			),
		),
	);


	$logs = get_posts($args);


	$mli_counter = 1;

	foreach ($warehouse as $warehouse_id) {

		$term = get_term($warehouse_id);

		if ($term) {

			$chart_main_array[0][ $mli_counter ] = $term->name;

			++$mli_counter;
		}
	}

	foreach ($date_array as $date) {

		$date_data = array();

		$date_data[] = $date;

		// $date_data['main_stock'] = 0;

		foreach ($warehouse as $key => $location) {

			$date_data[ $location ] = 0;
		}

		foreach ($logs as $key => $current_post) {

			$log = get_post($current_post);

			if (!$log) {
				continue;
			}

			$log_post_date = gmdate('Y-m-d', strtotime($log->post_date_gmt));

			if ($date == $log_post_date) {

				$stock_detail = get_post_meta($log->ID, 'stock_details', true);

				$term_id = get_post_meta($log->ID, 'term_id', true);

				$date_data[ $term_id ] = $stock_detail;
			}
		}

		$chart_main_array[] = array_values($date_data);
	}

	if (count($chart_main_array) >= 2) {

		$arraykeys = array_keys($chart_main_array);

		unset($chart_main_array[ end($arraykeys) ]);

	}

	wp_send_json(array( 'chart_data' => $chart_main_array ));

	wp_die();
}

function af_dash_graph_refresh() {

	$f_data = isset($_POST['form_data']) ? sanitize_text_field(wp_unslash($_POST['form_data'])) : 0;

	parse_str($f_data, $form_data);

	$nonce = isset($form_data['nonce']) ? sanitize_text_field($form_data['nonce']) : 0;

	if (!wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}

	$args = array(

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
	);

	$date_array = array();

	$chart_main_array = array();

	$started_date = gmdate('Y-m-d', strtotime('-30 days'));

	$logs = get_posts($args);

	for ($i = 0; $i < 30; $i++) {

		$date_array[] = gmdate('Y-m-d', strtotime($started_date . '+' . $i . ' days'));
	}

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

				$date_data[ $term_id ] = $stock_detail;
			}
		}

		$chart_main_array[] = array_values($date_data);
	}

	wp_send_json(
		array(

			'chart_data' => $chart_main_array,

			'from_date'  => gmdate('Y-m-d', strtotime('-30 days')),

			'to_date'    => gmdate('Y-m-d'),
		)
	);

	wp_die();
}

function af_mli_filter_by_title( $where, $the_query ) {

	global $wpdb;

	if ($the_query->get('search_title')) {
		$search_title = $the_query->get('search_title');
		$where       .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . $search_title . '%\'';
	}
	return $where;
}

function af_dependable_shipp_methods() {

	$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

	if (!wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}

	$all_zone_shipping_methods = array();

	ob_start();

	if (isset($_POST['zone_value']) && !empty($_POST['zone_value'])) {

		$zone_value = sanitize_meta('', wp_unslash($_POST['zone_value']), '');

		$zones = WC_Shipping_Zones::get_zones();

		foreach ($zones as $value) {
			if (in_array($value['id'], $zone_value) && $value['id'] && $value['zone_name'] && $value['shipping_methods']) {

				foreach ($value['shipping_methods'] as $shipping_methods_value) {

					$instance_id_and_shipping_id = $shipping_methods_value->id . ':' . $shipping_methods_value->instance_id;

					?>

					<option value="<?php echo esc_attr($instance_id_and_shipping_id); ?>">

						<?php echo esc_attr($shipping_methods_value->method_title); ?>

					</option>

					<?php

				}
			}
		}


	} else {

		?>

		<option value="" selected disabled>

			<?php echo esc_html('Select method'); ?>

		</option>

		<?php
	}

	$result = ob_get_clean();

	wp_send_json($result);

	wp_die();
}

function af_dependable_country_states() {

	$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

	if (!wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}

	ob_start();

	if (isset($_POST['country_value']) && !empty($_POST['country_value'])) {

		$country_value = sanitize_text_field(wp_unslash($_POST['country_value']));

		$countries_obj = new WC_Countries();

		$states = $countries_obj->get_states($country_value);

		foreach ($states as $state_key => $state_name) {

			?>

			<option value="<?php echo esc_attr($state_key); ?>">

				<?php echo esc_attr($state_name); ?>

			</option>

			<?php
		}
	} else {

		?>
		<option value="" selected disabled>

			<?php echo esc_html('Select State'); ?>

		</option>
		<?php
	}

	$result = ob_get_clean();

	wp_send_json($result);

	wp_die();
}

function af_mli_prod_lvl_ajax_inventory_template( $current_prod_id, $prod_inven_id ) {

	$inventory_locations = get_terms(
		array(
			'number'     => 0,
			'hide_empty' => false,
			'taxonomy'   => 'mli_location',
		)
	);

	$inven_location = get_post_meta($prod_inven_id, 'in_location', true);

	?>
	<div class="af_mli_class" style="margin-left: 5px; margin-right: 5px; ">

		<div class="af_mli_inventory_field_div">

			<div
				class="af_mli_inventory_field_open_close_div <?php echo esc_attr($prod_inven_id); ?>af_mli_inventory_field_open_close_div">

				<div class="af_mli_title_heading_div">

					<p><b><em><?php echo esc_attr(get_the_title($prod_inven_id)); ?></em></b></p>

				</div>

				<div class="af_mli_remove_btn_div">

					<input type="hidden" name="inven_hidden_id" class="inven_hidden_id"
						value="<?php echo esc_attr($prod_inven_id); ?>">

					<button name="af_mli_remove_inventory_btn"
						class="af_mli_remove_inventory_btn <?php echo esc_attr($prod_inven_id); ?>af_mli_remove_inventory_btn"
						data-current_prod_id="<?php echo esc_attr($current_prod_id); ?>"
						data-current_inventory_id="<?php echo esc_attr($prod_inven_id); ?>"><?php echo esc_html__('Remove', 'addify-multi-inventory-management'); ?></button>

					<button class="fa fa-sort-up <?php echo esc_attr($prod_inven_id); ?>fa-sort-up"
						data-inven_id="<?php echo esc_attr($prod_inven_id); ?>"></button>

					<button class="fa fa-sort-down <?php echo esc_attr($prod_inven_id); ?>fa-sort-down"
						data-inven_id="<?php echo esc_attr($prod_inven_id); ?>"></button>

				</div>

			</div>

			<p class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_loc_p af_mli_prd_lvl_loc_p">

				<label><?php echo esc_html__('Location', 'addify-multi-inventory-management'); ?></label>

				<select name="in_location[<?php echo esc_attr($prod_inven_id); ?>]" style="width: 50%;">

					<?php

					foreach ($inventory_locations as $inventory_location) {

						?>

						<option value="<?php echo esc_attr($inventory_location->term_id); ?>" 
													<?php
													if ($inventory_location->term_id == $inven_location) {
														echo 'selected';
													}
													?>
							>

							<?php echo esc_attr($inventory_location->name); ?>

						</option>

						<?php
					}

					?>

				</select>

				<?php echo wp_kses(wc_help_tip('Select Location'), wp_kses_allowed_html('post')); ?>

			</p>

			<p
				class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_priority_p af_mli_prd_lvl_priority_p">

				<label><?php echo esc_html__('Inventory Priority', 'addify-multi-inventory-management'); ?></label>

				<input type="number" min="0" name="in_field_priority[<?php echo esc_attr($prod_inven_id); ?>]"
					style="width: 50%;"
					value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_field_priority', true)); ?>"
					placeholder="0" min="0">
				<?php echo wp_kses(wc_help_tip('Set Priority of this Inventory'), wp_kses_allowed_html('post')); ?>
			</p>

			<p class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_sku_p af_mli_prd_lvl_sku_p">

				<label><?php echo esc_html__('Inventory SKU', 'addify-multi-inventory-management'); ?></label>

				<input type="text" name="in_sku[<?php echo esc_attr($prod_inven_id); ?>]" style="width: 50%;"
					value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_sku', true)); ?>" min="0">
				<?php echo wp_kses(wc_help_tip('Enter SKU of this Inventory'), wp_kses_allowed_html('post')); ?>
			</p>


			<p
				class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_inven_date_p af_mli_prd_lvl_inven_date_p">

				<label><?php echo esc_html__('Inventory Date', 'addify-multi-inventory-management'); ?></label>

				<input type="date" name="in_date[<?php echo esc_attr($prod_inven_id); ?>]" style="width: 50%"
					value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_date', true)); ?>">
				<?php echo wp_kses(wc_help_tip('Set date of this Inventory'), wp_kses_allowed_html('post')); ?>
			</p>

			<p
				class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_inven_expiry_date_p af_mli_prd_lvl_inven_expiry_date_p">

				<label><?php echo esc_html__('Inventory Expiry Date', 'addify-multi-inventory-management'); ?></label>

				<input type="date" name="in_expiry_date[<?php echo esc_attr($prod_inven_id); ?>]" style="width: 50%"
					value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_expiry_date', true)); ?>">
				<?php echo wp_kses(wc_help_tip('Set expiry date of this Inventory'), wp_kses_allowed_html('post')); ?>
			</p>

			<p style="display:none;"
				class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_inven_add_price_p af_mli_prd_lvl_inven_add_price_p">

				<label><?php echo esc_html__('Add inventory price?', 'addify-multi-inventory-management'); ?></label>

				<input type="checkbox" name="in_add_price[<?php echo esc_attr($prod_inven_id); ?>]" value="yes"
					class="in_add_price <?php echo esc_attr($prod_inven_id); ?>in_add_price"
					data-current_inventory_id="<?php echo esc_attr($prod_inven_id); ?>" checked>
				<?php echo wp_kses(wc_help_tip('Check if you want to add seperate price for erach inventory'), wp_kses_allowed_html('post')); ?>
			</p>

			<p
				class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_inven_price_p af_mli_prd_lvl_inven_price_p">

				<label><?php echo esc_html__('Inventory Price', 'addify-multi-inventory-management'); ?></label>

				<input type="number" name="in_price[<?php echo esc_attr($prod_inven_id); ?>]" style="width: 50%"
					value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_price', true)); ?>" min="0">
				<?php echo wp_kses(wc_help_tip('Set price of this Inventory'), wp_kses_allowed_html('post')); ?>
			</p>

			<p
				class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_inven_sale_price_p af_mli_prd_lvl_inven_sale_price_p">

				<label><?php echo esc_html__('Inventory Sale Price', 'addify-multi-inventory-management'); ?></label>

				<input type="number" name="in_sale_price[<?php echo esc_attr($prod_inven_id); ?>]" style="width: 50%"
					value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_sale_price', true)); ?>" min="0">
				<?php echo wp_kses(wc_help_tip('Set sale price of this Inventory'), wp_kses_allowed_html('post')); ?>
			</p>

			<p
				class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_stock_quantity_p af_mli_prd_lvl_stock_quantity_p">

				<label><?php echo esc_html__('Stock Quantity', 'addify-multi-inventory-management'); ?></label>

				<input type="number" name="in_stock_quantity[<?php echo esc_attr($prod_inven_id); ?>]" style="width: 50%"
					value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_stock_quantity', true)); ?>" min="0">
				<?php echo wp_kses(wc_help_tip('Stock quantity of this inventory'), wp_kses_allowed_html('post')); ?>
			</p>

			<p
				class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_low_stock_threshold_p af_mli_prd_lvl_low_stock_threshold_p">

				<label><?php echo esc_html__('Low Stock Threshold', 'addify-multi-inventory-management'); ?></label>

				<input type="number" name="in_low_stock_threshold[<?php echo esc_attr($prod_inven_id); ?>]"
					style="width: 50%"
					value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_low_stock_threshold', true)); ?>" min="0">
				<?php echo wp_kses(wc_help_tip('Low Stock Threshold at Inventory Level'), wp_kses_allowed_html('post')); ?>
			</p>

		</div>
	</div>
	<?php
}

function af_mli_variation_inventory_fields_template( $current_prod_id, $prod_inven_id ) {

	?>
	<div class="af_mli_class" style="margin-left: 5px; margin-right: 5px; ">
		<div class="af_mli_inventory_field_div">
			<div
				class="af_mli_inventory_field_open_close_div <?php echo esc_attr($prod_inven_id); ?>af_mli_inventory_field_open_close_div">
				<div class="af_mli_title_heading_div">
					<p><b><em><?php echo esc_attr(get_the_title($prod_inven_id)); ?></em></b></p>
				</div>
				<div class="af_mli_remove_btn_div">
					<input type="hidden" name="inven_hidden_id" class="inven_hidden_id"
						value="<?php echo esc_attr($prod_inven_id); ?>">
					<button name="af_mli_remove_inventory_btn"
						class="af_mli_remove_inventory_btn <?php echo esc_attr($prod_inven_id); ?>af_mli_remove_inventory_btn"
						data-current_prod_id="<?php echo esc_attr($current_prod_id); ?>"
						data-current_inventory_id="<?php echo esc_attr($prod_inven_id); ?>"><?php echo esc_html__('Remove', 'addify-multi-inventory-management'); ?></button>

					<button class="fa fa-sort-up <?php echo esc_attr($prod_inven_id); ?>fa-sort-up"
						data-inven_id="<?php echo esc_attr($prod_inven_id); ?>"></button>

					<button class="fa fa-sort-down <?php echo esc_attr($prod_inven_id); ?>fa-sort-down"
						data-inven_id="<?php echo esc_attr($prod_inven_id); ?>"></button>

				</div>
			</div>

			<div style="padding: 10px; padding-bottom: unset;">

				<p
					class="form-field form-row form-row-full <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_loc_p af_mli_prd_lvl_loc_p">

					<label><?php echo esc_html__('Location', 'addify-multi-inventory-management'); ?></label>

					<?php

					echo wp_kses(wc_help_tip('Select Location'), wp_kses_allowed_html('post'));

					$inventory_locations = get_terms(
						array(
							'number'     => 0,
							'hide_empty' => false,
							'taxonomy'   => 'mli_location',
						)
					);
					$inven_location      = get_post_meta($prod_inven_id, 'in_location', true);
					?>
					<select name="in_location[<?php echo esc_attr($prod_inven_id); ?>]" class="height_40_width_100">
						<?php
						foreach ($inventory_locations as $inventory_location) {
							?>
							<option value="<?php echo esc_attr($inventory_location->term_id); ?>" 
														<?php
														if ($inventory_location->term_id == $inven_location) {
															echo 'selected';
														}
														?>
								>
								<?php echo esc_attr($inventory_location->name); ?>
							</option>
							<?php
						}
						?>
					</select>
				</p>

				<p
					class="form-field form-row form-row-full <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_priority_p af_mli_prd_lvl_priority_p">

					<label><?php echo esc_html__('Inventory Priority', 'addify-multi-inventory-management'); ?></label>
					<?php echo wp_kses(wc_help_tip('Set Priority of this Inventory'), wp_kses_allowed_html('post')); ?>
					<input type="number" min="0" name="in_field_priority[<?php echo esc_attr($prod_inven_id); ?>]"
						class="height_40_width_100"
						value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_field_priority', true)); ?>"
						placeholder="0" min="0">
				</p>

				<p
					class="form-field form-row form-row-full <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_sku_p af_mli_prd_lvl_sku_p">

					<label><?php echo esc_html__('Inventory SKU', 'addify-multi-inventory-management'); ?></label>
					<?php echo wp_kses(wc_help_tip('Enter SKU of this Inventory'), wp_kses_allowed_html('post')); ?>
					<input type="text" name="in_sku[<?php echo esc_attr($prod_inven_id); ?>]" class="height_40_width_100"
						value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_sku', true)); ?>">
				</p>


				<p
					class="form-field form-row form-row-full <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_inven_date_p af_mli_prd_lvl_inven_date_p">

					<label><?php echo esc_html__('Inventory Date', 'addify-multi-inventory-management'); ?></label>
					<?php echo wp_kses(wc_help_tip('Set date of this Inventory'), wp_kses_allowed_html('post')); ?>
					<input type="date" name="in_date[<?php echo esc_attr($prod_inven_id); ?>]" class="height_40_width_100"
						value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_date', true)); ?>">

				</p>

				<p
					class="form-field form-row form-row-full <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_inven_expiry_date_p af_mli_prd_lvl_inven_expiry_date_p">

					<label><?php echo esc_html__('Inventory Expiry Date', 'addify-multi-inventory-management'); ?></label>
					<?php echo wp_kses(wc_help_tip('Set expiry date of this Inventory'), wp_kses_allowed_html('post')); ?>
					<input type="date" name="in_expiry_date[<?php echo esc_attr($prod_inven_id); ?>]"
						class="height_40_width_100"
						value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_expiry_date', true)); ?>">

				</p>

				<p style="display:none;"
					class="form-field form-row form-row-full <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_inven_add_price_p af_mli_prd_lvl_inven_add_price_p">

					<label><?php echo esc_html__('Add inventory price?', 'addify-multi-inventory-management'); ?></label>
					<?php echo wp_kses(wc_help_tip('Check if you want to add seperate price for erach inventory'), wp_kses_allowed_html('post')); ?>
					<br>
					<input type="checkbox" name="in_add_price[<?php echo esc_attr($prod_inven_id); ?>]" value="yes"
						class="in_add_price <?php echo esc_attr($prod_inven_id); ?>in_add_price"
						data-current_inventory_id="<?php echo esc_attr($prod_inven_id); ?>" checked>

				</p>

				<p
					class="form-field form-row form-row-full <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_inven_price_p af_mli_prd_lvl_inven_price_p">

					<label><?php echo esc_html__('Inventory Price', 'addify-multi-inventory-management'); ?></label>
					<?php echo wp_kses(wc_help_tip('Set price of this Inventory'), wp_kses_allowed_html('post')); ?>
					<input type="number" name="in_price[<?php echo esc_attr($prod_inven_id); ?>]"
						class="height_40_width_100"
						value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_price', true)); ?>" min="0">

				</p>

				<p
					class="form-field form-row form-row-full <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_inven_sale_price_p af_mli_prd_lvl_inven_sale_price_p">

					<label><?php echo esc_html__('Inventory Sale Price', 'addify-multi-inventory-management'); ?></label>
					<?php echo wp_kses(wc_help_tip('Set sale price of this Inventory'), wp_kses_allowed_html('post')); ?>
					<input type="number" name="in_sale_price[<?php echo esc_attr($prod_inven_id); ?>]"
						class="height_40_width_100"
						value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_sale_price', true)); ?>" min="0">

				</p>

				<p
					class="form-field form-row form-row-full <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_stock_quantity_p af_mli_prd_lvl_stock_quantity_p">

					<label><?php echo esc_html__('Stock Quantity', 'addify-multi-inventory-management'); ?></label>
					<?php echo wp_kses(wc_help_tip('Stock quantity of this inventory'), wp_kses_allowed_html('post')); ?>
					<input type="number" name="in_stock_quantity[<?php echo esc_attr($prod_inven_id); ?>]"
						class="height_40_width_100"
						value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_stock_quantity', true)); ?>" min="0">

				</p>

				<p
					class="form-field form-row form-row-full <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_low_stock_threshold_p af_mli_prd_lvl_low_stock_threshold_p">

					<label><?php echo esc_html__('Low Stock Threshold', 'addify-multi-inventory-management'); ?></label>
					<?php echo wp_kses(wc_help_tip('Low Stock Threshold at Inventory Level'), wp_kses_allowed_html('post')); ?>
					<input type="number" name="in_low_stock_threshold[<?php echo esc_attr($prod_inven_id); ?>]"
						class="height_40_width_100"
						value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_low_stock_threshold', true)); ?>"
						min="0">

				</p>

			</div>

		</div>
	</div>
	<?php
}


add_action('wp_ajax_af_mli_update_location_in_cart', 'af_mli_update_location_in_cart');

add_action('wp_ajax_nopriv_af_mli_update_location_in_cart', 'af_mli_update_location_in_cart');

function af_mli_update_location_in_cart() {

	$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

	if (!wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}

	$inventories   = '';
	$cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';

	ob_start();

	$cart = wc()->cart->get_cart();
	if (isset($cart[ $cart_item_key ])) {

		$cart_item = $cart[ $cart_item_key ];

		if (isset($cart_item['data']) && is_object($cart_item['data']) && isset($cart_item['selected_location'])) {

			$product_id = isset($cart_item['variation_id']) && $cart_item['variation_id'] >= 1 ? $cart_item['variation_id'] : $cart_item['product_id'];

			$new_location = isset($cart_item['selected_location']['selected_value']) ? $cart_item['selected_location']['selected_value'] : 0;

			$product                             = $cart_item['data'];
			$af_mi_get_current_product_inventory = af_mi_get_current_product_inventory($product_id);
			$af_mi_get_current_product_inventory = af_mli_custom_array_filter($af_mi_get_current_product_inventory);
			$current_date                        = strtotime(gmdate('Y-m-d'));

			if (!empty($af_mi_get_current_product_inventory)) {
				?>
				<div class="af-mli-select-location-from-cart-main-div">
					<div class="af-mli-select-location-from-cart-contianer">
						<i class="af-mli-cross-icon">&times;</i>
						<select class="af-mli-cart-page-select-location<?php echo esc_attr($cart_item_key); ?>">
							<?php
							foreach ($af_mi_get_current_product_inventory as $location_detail) {

								// Check the 'date' index.
								if (!empty($location_detail['date']) && $current_date < strtotime($location_detail['date'])) {
									continue;
								}
								// Check the 'exp_date' index.
								if (!empty($location_detail['exp_date']) && $current_date > strtotime($location_detail['exp_date'])) {
									continue;
								}
								if (isset($location_detail['value']) && isset($location_detail['total_stock']) && $location_detail['total_stock'] >= 1) {
									?>
									<option value="<?php echo esc_attr($location_detail['value']); ?>" <?php selected($location_detail['value'], $new_location); ?>>
										<?php
										$name_and_stock = $location_detail['name'] . ' ( ' . $location_detail['total_stock'] . ' )';
										echo esc_attr($name_and_stock);
										?>
									</option>
									<?php
								}
							}
							?>
						</select>
						<button class="af-mli-set-new-location"
							data-cart_item_key="<?php echo esc_attr($cart_item_key); ?>">Submit</button>
					</div>
				</div>
				<a href="#" class="af-mli-select-location-from-cart-show-popup">Change Location</a>
				<?php
			}
		}
	}

	$inventories = ob_get_clean();

	wp_send_json($inventories);

	wp_die();
}

add_action('wp_ajax_af_mli_update_location_of_cart_item', 'af_mli_update_location_of_cart_item');

add_action('wp_ajax_nopriv_af_mli_update_location_of_cart_item', 'af_mli_update_location_of_cart_item');

function af_mli_update_location_of_cart_item() {

	$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

	if (!wp_verify_nonce($nonce, '_addify_mli_nonce')) {

		wp_die('Failed security check');
	}

	$cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';
	$new_location  = isset($_POST['new_location']) ? sanitize_text_field(wp_unslash($_POST['new_location'])) : '';

	$whole_cart = wc()->cart->get_cart();

	if (!isset($whole_cart[ $cart_item_key ])) {
		wp_die();
	}
	$cart_item = $whole_cart[ $cart_item_key ];

	if (isset($cart_item['selected_location']) && is_array($cart_item['selected_location'])) {
		// Update the 'selected_value' index with the new location
		$cart_item['selected_location']['selected_value'] = $new_location;

		// Update the cart item in the WooCommerce cart
		WC()->cart->cart_contents[ $cart_item_key ] = $cart_item;
		WC()->cart->set_session(); // Save the updated cart to the session

		$cart = wc()->cart->get_cart();

		$quantity = $cart_item['quantity'];

		if (isset($cart[ $cart_item_key ]) && isset($cart[ $cart_item_key ]['selected_location']) && isset($cart[ $cart_item_key ]['selected_location']['selected_value'])) {

			$selected_location = $cart[ $cart_item_key ]['selected_location']['selected_value'];
			$producy_id        = isset($cart[ $cart_item_key ]['variation_id']) && $cart[ $cart_item_key ]['variation_id'] >= 1 ? $cart[ $cart_item_key ]['variation_id'] : $cart[ $cart_item_key ]['product_id'];

			$af_mi_get_current_product_inventory = af_mi_get_current_product_inventory($producy_id);

			foreach ($af_mi_get_current_product_inventory as $current_array) {

				if ($current_array['value'] && $current_array['value'] == $selected_location) {
					if (isset($current_array['total_stock']) && ( (float) $current_array['total_stock'] >= 1 ) && $quantity > (float) $current_array['total_stock']) {

						WC()->cart->set_quantity($cart_item_key, $current_array['total_stock']);

					}

				}

			}

		}
		wp_send_json_success(array( 'reload' => true ));
	}

	wp_die();
}

