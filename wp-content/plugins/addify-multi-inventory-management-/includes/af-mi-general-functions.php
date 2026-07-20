<?php
function af_mi_get_current_product_inventory( $_prod_id ) {
	if (!empty(wc()->session)) {

		$af_loc_widget = (array) wc()->session->get('af_user_selected_location');   
	} else {

		$af_loc_widget = array();   
	}
	
	$af_loc_widget = af_mli_custom_array_filter($af_loc_widget);

	$mli_product = wc_get_product($_prod_id);

	if ('variable' == $mli_product->get_type() || 'yes' != get_post_meta($_prod_id, 'prod_level_inven', true) || !$mli_product->get_manage_stock()) {
		return array();
	}

	$inven_val_array = array();

	$all_inven_values = array();

	$indiv_stock = '';

	$loc_arr = (array) get_option('mli_gen_exclude_locations');

	$loc_id = get_post_meta($_prod_id, 'in_location', true);

	$loc_open_time = 'yes' == get_term_meta($loc_id, 'af_mli_tax_except_order', true) && !empty(get_term_meta($loc_id, 'af_mli_tax_open_time', true)) ? get_term_meta($loc_id, 'af_mli_tax_open_time', true) : '';

	$loc_close_time = 'yes' == get_term_meta($loc_id, 'af_mli_tax_except_order', true) && !empty(get_term_meta($loc_id, 'af_mli_tax_close_time', true)) ? get_term_meta($loc_id, 'af_mli_tax_close_time', true) : '';

	$loc_date = get_post_meta($_prod_id, 'in_date', true);

	$loc_exp_date = get_post_meta($_prod_id, 'in_expiry_date', true);

	$_prod_price = $mli_product->get_price();

	$loc_ = get_term($loc_id);

	$loc_name = !is_wp_error($loc_) && is_object($loc_) ? $loc_->name : '';

	$inven_posts = get_posts(
		array(
			'post_type'   => 'af_prod_lvl_invent',

			'post_status' => 'publish',

			'numberposts' => -1,

			'post_parent' => $_prod_id,

			'orderby'     => 'menu_order',

			'order'       => 'ASC',

			'fields'      => 'ids',
		)
	);
	if (count($inven_posts) < 1) {

		return array();

	}

	$optn_text = $loc_name;

	$main_stock = -1;

	if ('1' == $mli_product->get_manage_stock()) {

		if (!empty($mli_product->get_stock_quantity()) || 0 < $mli_product->get_stock_quantity()) {

			$optn_text = $loc_name;

			$main_stock = $mli_product->get_stock_quantity();
		}
	}

	$optn_text = ( -1 != $main_stock ) ? $loc_name . ' (' . $main_stock . ')' : $loc_name;

	$first_flag = false;

	$f_flag = false;


	if ('yes' == get_option('mli_gen_hide_loc_from_drop_down')) {

		if (( 'yes' == $mli_product->get_manage_stock() && 0 != $mli_product->get_stock_quantity() ) || 'outofstock' != $mli_product->get_stock_status()) {

			$first_flag = true;

		}
	} else {

		$first_flag = true;
	}

	if ($first_flag) {

		if ('yes' == get_option('mli_gen_restrict_user')) {

			if (!empty($loc_arr)) {

				if (in_array($loc_id, $loc_arr)) {

					$f_flag = true;
				}
			} else {

				$f_flag = true;

			}
		} else {

			$f_flag = true;

		}

		// if ($f_flag) {

			//          $inven_val_array = array(

			//              'value' => $loc_id,

			//              'total_stock' => $main_stock,

			//              'open_time' => $loc_open_time,

			//              'close_time' => $loc_close_time,

			//              'date' => $loc_date,

			//              'exp_date' => $loc_exp_date,

			//              'curr_price' => $_prod_price,

			//              'formatted_price' => wc_price($_prod_price),

			//              'name' => $loc_name,

			//              'text' => $optn_text,
//          );

			//          $all_inven_values[] = $inven_val_array;
		// }



		foreach ($inven_posts as $inven_id) {
			$loc_id = get_post_meta($inven_id, 'in_location', true);

			if ('yes' == get_option('mli_gen_restrict_user')) {

				if (!empty($loc_arr)) {

					if (!in_array($loc_id, $loc_arr)) {

						continue;
					}
				}
			}

			$loc_ = get_term($loc_id);

			if (!is_wp_error($loc_)) {

				$loc_name = $loc_->name;
			}

			$_stock = !empty(get_post_meta($inven_id, 'in_stock_quantity', true)) || 0 == get_post_meta($inven_id, 'in_stock_quantity', true) ? get_post_meta($inven_id, 'in_stock_quantity', true) : -1;

			$loc_open_time = 'yes' == get_term_meta($loc_id, 'af_mli_tax_except_order', true) && !empty(get_term_meta($loc_id, 'af_mli_tax_open_time', true)) ? get_term_meta($loc_id, 'af_mli_tax_open_time', true) : '';

			$loc_close_time = 'yes' == get_term_meta($loc_id, 'af_mli_tax_except_order', true) && !empty(get_term_meta($loc_id, 'af_mli_tax_close_time', true)) ? get_term_meta($loc_id, 'af_mli_tax_close_time', true) : '';

			$loc_date = get_post_meta($inven_id, 'in_date', true);

			$loc_exp_date = get_post_meta($inven_id, 'in_expiry_date', true);


			// if ('yes' == get_post_meta($inven_id, 'in_add_price', true)) {

			$_prod_price = (float) ( !empty(get_post_meta($inven_id, 'in_sale_price', true)) ? get_post_meta($inven_id, 'in_sale_price', true) : get_post_meta($inven_id, 'in_price', true) );

			if (empty($_prod_price)) {

				$_prod_price = $mli_product->get_price();
			}
			// } else {

			//  $_prod_price = $mli_product->get_price();
			// }

			if ('yes' == get_option('mli_gen_hide_loc_from_drop_down')) {

				if (0 != $_stock) {

					if (-1 != $_stock) {

						$optn_text = $loc_name . ' (' . $_stock . ')';

					} else {

						$optn_text = $loc_name;
					}

					$inven_val_array = array(

						'value'           => $loc_id,

						'total_stock'     => $_stock,

						'open_time'       => $loc_open_time,

						'close_time'      => $loc_close_time,

						'date'            => $loc_date,

						'exp_date'        => $loc_exp_date,

						'curr_price'      => $_prod_price,

						'formatted_price' => wc_price($_prod_price),

						'name'            => $loc_name,

						'text'            => $optn_text,
					);

					if (get_post_meta($inven_id, 'in_price', true)) {

						$inven_price      = get_post_meta($inven_id, 'in_price', true);
						$inven_sale_price = get_post_meta($inven_id, 'in_sale_price', true);

						if (!empty($inven_price) && !empty($inven_sale_price)) {

							$inven_val_array['formatted_price'] = '<del>' . wc_price($inven_price) . '</del>  <span>' . wc_price($inven_sale_price) . '</span>';
							$inven_val_array['regular_price']   = $inven_price;
							$inven_val_array['sale_price']      = $inven_price;

						}
					}

					$all_inven_values[] = $inven_val_array;
				}
			} else {

				if (-1 != $_stock) {

					$optn_text = $loc_name . '(' . $_stock . ')';

				} else {

					$optn_text = $loc_name;
				}

				$inven_val_array = array(

					'value'           => $loc_id,
					'term_id'         => $loc_id,
					'total_stock'     => $_stock,
					'open_time'       => $loc_open_time,
					'close_time'      => $loc_close_time,
					'date'            => $loc_date,

					'exp_date'        => $loc_exp_date,

					'curr_price'      => $_prod_price,

					'formatted_price' => wc_price($_prod_price),

					'name'            => $loc_name,

					'text'            => $optn_text,
				);

				if (get_post_meta($inven_id, 'in_price', true)) {

					$inven_price      = get_post_meta($inven_id, 'in_price', true);
					$inven_sale_price = get_post_meta($inven_id, 'in_sale_price', true);

					if (!empty($inven_price) && !empty($inven_sale_price)) {
						$inven_val_array['formatted_price'] = '<del>' . wc_price($inven_price) . '</del>  <span>' . wc_price($inven_sale_price) . '</span>';
						$inven_val_array['regular_price']   = $inven_price;
						$inven_val_array['sale_price']      = $inven_price;

					}
				}

				$all_inven_values[] = (object) $inven_val_array;
			}
		}
	}


	if (count($all_inven_values) >= 1) {

		$all_inven_values = af_mli_get_available_location($all_inven_values);

		$loc_arr = (array) get_option('mli_gen_exclude_locations');

		foreach ($all_inven_values as $current_key => $inven_value) {

			$all_inven_values[ $current_key ] = (array) $inven_value;
			$inven_value                      = (array) $inven_value;
			if (!isset($inven_value['value'])) {
				unset($all_inven_values[ $current_key ]);
				continue;
			}

			if (!isset($inven_value['total_stock'])) {
				continue;
			}
			if ('yes' == get_option('mli_gen_hide_loc_from_drop_down')) {

				if (isset($inven_value['total_stock']) && $inven_value['total_stock'] < 0) {
					unset($all_inven_values[ $current_key ]);
					continue;
				}
			}
			if (count($af_loc_widget) >= 1 && !in_array($inven_value['value'], (array) $af_loc_widget)) {
				unset($all_inven_values[ $current_key ]);
				continue;
			}
		}
	}



	return $all_inven_values;
}
function af_mi_get_product_inventory( $_prod_id ) {

	?>
	<input type="hidden" name="af_prd_page_field" value="af_mli_product_page_field">
	<?php

	$product = wc_get_product($_prod_id);

	$all_inven_values = af_mi_get_current_product_inventory($_prod_id);
	$all_inven_values = af_mli_custom_array_filter($all_inven_values);



	if (empty($all_inven_values)) {
		return;
	} elseif (count($all_inven_values) == 1 && isset(current($all_inven_values)['total_stock']) && current($all_inven_values)['total_stock'] >= 1) {
		?>
			<p class="stock in-stock"> 
			<?php
			echo esc_html('Product Available in ');
			foreach ($all_inven_values as $key => $inven_value) {

				echo esc_attr($inven_value['text']);
				break;
			}
			?>
			</p>
			<?php
	}


	?>

	<div class="af_mli_front_stock_div">

		<?php if ('yes' == get_option('display_heading_display')) : ?>

			<<?php echo esc_attr(get_option('af_mli_loc_ptn_heading_title')); ?>
				class="af_inven_heading"><?php echo esc_attr(get_option('display_heading')); ?> </<?php echo esc_attr(get_option('af_mli_loc_ptn_heading_title')); ?>>

		<?php endif ?>


		<div class="af_mli_front_inven_div">

			<?php



			if ('drop_down' == get_option('af_mli_prod_page_loc_show_type')) {

				?>

				<select class="af_mli_inven_selector" name="af_mli_inven_selector">

					<?php

					foreach ($all_inven_values as $key => $inven_value) {

						?>

						<option value="<?php echo esc_attr($inven_value['value']); ?>" 
													<?php
													foreach ($inven_value as $data_keys => $current_vale) {

														?>
								data-<?php echo esc_attr($data_keys); ?>="<?php echo esc_attr($current_vale); ?>"
								<?php

													}
													?>
							>
							<?php echo esc_attr($inven_value['text']); ?>

						</option>

						<?php
					}

					?>

				</select>

				<?php

			} else {

				foreach ($all_inven_values as $key => $inven_value) {

					?>

					<div style="width: fit-content; background: #f2f2f2; padding: 10px; border-radius: 6px; margin-bottom: 10px;">

						<input type="radio" name="af_mli_inven_selector" class="af_mli_inven_selector af_mli_inven_selector_radio"
							value="<?php echo esc_attr($inven_value['value']); ?>" 
												<?php
												foreach ($inven_value as $data_keys => $current_vale) {

													?>
									data-<?php echo esc_attr($data_keys); ?>="<?php echo esc_attr($current_vale); ?>"
								<?php

												}
												?>
								>

						<span class="af_mli_inven_selector" style="padding-left: 5px">
							<?php echo esc_attr($inven_value['name']); ?></span>

						<br>

						<?php

						if ($inven_value['total_stock'] > 0) {

							?>
							<span style="padding-left: 22px; color: green">
								<?php echo esc_attr('Total Stock: ' . $inven_value['total_stock']); ?></span>
							<?php

						} elseif (0 == $inven_value['total_stock']) {

							?>
								<span style="padding-left: 22px; color: red">
								<?php echo esc_attr('Total Stock: ' . $inven_value['total_stock']); ?></span>
							<?php
						}

						?>

					</div>

					<?php

				}
			}

			?>

		</div>

	</div>


	<p class="time_msg_p" style="display: none;"><?php echo esc_html('Shop is closed'); ?></p>
	<p class="date_msg_p" style="display: none;"> <?php echo esc_html('Inventory is expired'); ?> </p>
	<?php if ('drop_down' == get_option('af_mli_prod_page_loc_show_type')) { ?>
		<div class="af_main_total_div">
			<table>
				<thead>
					<tr class="af_prod_name_class">
						<th><?php echo esc_html('Location'); ?></th>
						<th><?php echo esc_html('Price'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr class="af_prod_name_class af_product_name_dynamic">
						<td class="product_name"></td>
						<td class="product_price"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}
}


function af_mli_display_field_border_radius() {

	$display_field_border_radius = get_option('display_field_border_radius');

	$f_b_r_top_left = isset($display_field_border_radius['top_left']) ? $display_field_border_radius['top_left'] : 0;

	$f_b_r_top_right = isset($display_field_border_radius['top_right']) ? $display_field_border_radius['top_right'] : 0;

	$f_b_r_bottom_left = isset($display_field_border_radius['bottom_left']) ? $display_field_border_radius['bottom_left'] : 0;

	$f_b_r_bottom_right = isset($display_field_border_radius['bottom_right']) ? $display_field_border_radius['bottom_right'] : 0;

	?>

	<div style="display: inline-flex; width: 40%;" class="display_field_border_radius">

		<div class="af_mli_display_field_border_radius">

			<input type="number" name="display_field_border_radius[top_left]" style="width: 100%;"
				value="<?php echo esc_attr($f_b_r_top_left); ?>" min="0" placeholder="0">

			<p><i><?php echo esc_html__('Top Left', 'addify-multi-inventory-management'); ?></i></p>

		</div>

		<div class="af_mli_display_field_border_radius">

			<input type="number" name="display_field_border_radius[top_right]" style="width: 100%;"
				value="<?php echo esc_attr($f_b_r_top_right); ?>" min="0" placeholder="0">

			<p><i><?php echo esc_html__('Top Right', 'addify-multi-inventory-management'); ?></i></p>

		</div>

		<div class="af_mli_display_field_border_radius">

			<input type="number" name="display_field_border_radius[bottom_left]" style="width: 100%;"
				value="<?php echo esc_attr($f_b_r_bottom_left); ?>" min="0" placeholder="0">

			<p><i><?php echo esc_html__('Bottom Left', 'addify-multi-inventory-management'); ?></i></p>

		</div>

		<div class="af_mli_display_field_border_radius">

			<input type="number" name="display_field_border_radius[bottom_right]" style="width: 100%;"
				value="<?php echo esc_attr($f_b_r_bottom_right); ?>" min="0" placeholder="0">

			<p><i><?php echo esc_html__('Bottom Right', 'addify-multi-inventory-management'); ?></i></p>

		</div>

		<span
			style="padding-top: 8px; padding-left: 5px;"><i><?php echo esc_html__('px', 'addify-multi-inventory-management'); ?></i></span>

	</div>

	<?php
}

function af_mli_display_field_padding() {

	$display_field_padding = get_option('display_field_padding');

	$f_p_top = isset($display_field_padding['top']) ? $display_field_padding['top'] : 0;

	$f_p_bottom = isset($display_field_padding['bottom']) ? $display_field_padding['bottom'] : 0;

	$f_p_left = isset($display_field_padding['left']) ? $display_field_padding['left'] : 0;

	$f_p_right = isset($display_field_padding['right']) ? $display_field_padding['right'] : 0;

	?>

	<div style="display: inline-flex; width: 40%;" class="display_field_padding">

		<div class="af_mli_display_field_border_radius">

			<input type="number" name="display_field_padding[top]" style="width: 100%;"
				value="<?php echo esc_attr($f_p_top); ?>" min="0" placeholder="0">

			<p><i><?php echo esc_html__('Top', 'addify-multi-inventory-management'); ?></i></p>

		</div>

		<div class="af_mli_display_field_border_radius">

			<input type="number" name="display_field_padding[bottom]" style="width: 100%;"
				value="<?php echo esc_attr($f_p_bottom); ?>" min="0" placeholder="0">

			<p><i><?php echo esc_html__('Bottom', 'addify-multi-inventory-management'); ?></i></p>

		</div>

		<div class="af_mli_display_field_border_radius">

			<input type="number" name="display_field_padding[left]" style="width: 100%;"
				value="<?php echo esc_attr($f_p_left); ?>" min="0" placeholder="0">

			<p><i><?php echo esc_html__('Left', 'addify-multi-inventory-management'); ?></i></p>

		</div>

		<div class="af_mli_display_field_border_radius">

			<input type="number" name="display_field_padding[right]" style="width: 100%;"
				value="<?php echo esc_attr($f_p_right); ?>" min="0" placeholder="0">

			<p><i><?php echo esc_html__('Right', 'addify-multi-inventory-management'); ?></i></p>

		</div>

		<span
			style="padding-top: 8px; padding-left: 5px;"><i><?php echo esc_html__('px', 'addify-multi-inventory-management'); ?></i></span>

	</div>

	<?php
}

function af_mli_gen_excl_loc_front_end() {

	$inventory_locations = get_terms(
		array(
			'number'     => 0,
			'hide_empty' => false,
			'taxonomy'   => 'mli_location',
		)
	);

	$loc = (array) get_option('mli_gen_exclude_locations');

	?>

	<select name="mli_gen_exclude_locations[]" class="mli_gen_exclude_locations" style="height: 40px; width: 50%;" multiple>

		<?php

		foreach ($inventory_locations as $inventory_location) {

			$loc_term_id = $inventory_location->term_id;
			?>

			<option value="<?php echo esc_attr($inventory_location->term_id); ?>"
				data-inven_id="<?php echo esc_attr($loc_term_id); ?>" 
											<?php

											if (in_array($inventory_location->term_id, $loc)) {

												echo 'selected';
											}

											?>
					>

				<?php echo esc_attr($inventory_location->name); ?>

			</option>

		<?php } ?>

	</select>

	<?php
}

function af_mli_gen_backend_mode_only_rules() {

	?>
	<select name="mli_gen_backend_mode_only_rules" id="mli_gen_backend_mode_only_rules">

		<option value="most_inven" 
		<?php
		if ('most_inven' == get_option('mli_gen_backend_mode_only_rules')) :
			?>
			selected <?php endif ?>>
			<?php echo esc_html('Location with most inventory in stock'); ?>

		</option>

		<option value="priority_loc" 
		<?php
		if ('priority_loc' == get_option('mli_gen_backend_mode_only_rules')) :
			?>
			selected
			<?php endif ?>>
			<?php echo esc_html('Location as per priority in stock'); ?>

		</option>

	</select>

	<p><b><i><?php echo esc_html__('Rule description :', 'addify-multi-inventory-management'); ?></i></b></p>

	<p><b><i><?php echo esc_html__('1. Location with most inventory in stock : ', 'addify-multi-inventory-management'); ?></i></b><?php echo esc_html__('The location is automatically chosen with availability of most number of stock .', 'addify-multi-inventory-management'); ?>
	</p>

	<p><b><i><?php echo esc_html__('2. Location as per priority in stock: ', 'addify-multi-inventory-management'); ?></i></b><?php echo esc_html__('Assign location as per priority set and availability stock is automatically chosen.', 'addify-multi-inventory-management'); ?>
	</p>

	<?php
}

function af_mli_get_stock_html( $quantity = '' ) {

	ob_start();

	if (!empty($quantity) && $quantity >= 1) {

		?>
		<span class="stock in-stock"> <?php echo esc_html($quantity . ' in stock'); ?> </span>
		<?php

	} elseif (0 == $quantity) {

		?>
			<span class="stock out-of-stock"><?php echo esc_html('Out of stock'); ?></span>
		<?php

	} elseif (0 != $quantity || $quantity < 0) {

		?>
				<span class="stock in-stock"><?php echo esc_html('In stock'); ?></span>
		<?php
	}

	return ob_get_clean();
}

function af_mli_get_stock_class( $quantity = '' ) {

	$stock_class = '';

	if (!empty($quantity) && $quantity >= 1) {

		$stock_class = 'stock in-stock';

	} elseif (0 == $quantity) {

		$stock_class = 'stock out-of-stock';

	} elseif (0 != $quantity || $quantity <= -1) {

		$stock_class = 'stock in-stock';

	}

	return $stock_class;
}

function af_mli_custom_array_filter( $filters ) {
	$filters = array_filter(
		(array) $filters,
		function ( $current_value, $current_key ) {
			return ( '' !== $current_value && '' !== $current_key );
		},
		ARRAY_FILTER_USE_BOTH
	);
	return $filters;
}

function af_mli_get_user_ip() {

	$country = new WC_Geolocation();
	$ip      = $country->get_ip_address();
	return $ip;
}
function af_mli_get_user_location() {
	$ip = af_mli_get_user_ip(); // Get the user's IP address

	$api_url = "http://ip-api.com/json/{$ip}";

	// Fetch IP details
	$response = wp_remote_get($api_url);

	if (!is_wp_error($response)) {

		$get_response_of_wp = wp_remote_retrieve_body($response);
		$get_response_of_wp = str_replace(array( 'lat', 'lon' ), array( 'latitude', 'longitude' ), $get_response_of_wp);

		// Decode JSON response
		$ip_details = json_decode($get_response_of_wp, true);
		if (!empty($ip_details) && 'success' == $ip_details['status']) {
			return $ip_details;
		}
	}

	$response = file_get_contents("http://ipinfo.io/$ip/geo");

	// Check for errors in the response
	if (false == $response) {
		return array(); // Handle the error as needed
	}

	// Decode the JSON response
	$data = json_decode($response, true);

	// Check if the location (latitude and longitude) is available
	if (isset($data['loc'])) {
		list($latitude, $longitude) = explode(',', $data['loc']);
		// Explicitly set latitude and longitude
		$data['latitude']  = $latitude;
		$data['longitude'] = $longitude;
	} else {
		$data['latitude']  = null; // Default value if not found
		$data['longitude'] = null; // Default value if not found
	}

	return $data;
}
// Function to get latitude and longitude from the address using Nominatim
function af_mli_get_lat_lng_from_address_nominatim( $loc_term_id ) {

	if (!empty(get_option('mli_gen_google_api_key')) && ( empty(get_term_meta($loc_term_id, 'af_mli_location_latitude', true)) || empty(get_term_meta($loc_term_id, 'af_mli_location_longitude', true)) )) {

		$af_mli_tax_adress   = get_term_meta($loc_term_id, 'af_mli_tax_adress', true);
		$af_mli_tax_country  = get_term_meta($loc_term_id, 'af_mli_tax_country', true);
		$af_mli_tax_state    = get_term_meta($loc_term_id, 'af_mli_tax_state', true);
		$af_mli_tax_city     = get_term_meta($loc_term_id, 'af_mli_tax_city', true);
		$af_mli_tax_zip_code = get_term_meta($loc_term_id, 'af_mli_tax_zip_code', true);
		$address             = trim($af_mli_tax_adress . ', ' . $af_mli_tax_city . ', ' . $af_mli_tax_state . ', ' . $af_mli_tax_country . ' ' . $af_mli_tax_zip_code);

		// Replace with your Google Maps Geocoding API key
		$apiKey = get_option('mli_gen_google_api_key');

		// Prepare the address for the API request
		$address = urlencode($address);

		// Build the API request URL
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";

		// Make the API request
		// $response = file_get_contents($url);

		$response = wp_remote_get($url);
		$response = $response['body'];


		$data = json_decode($response);

		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';

		// Check for a valid response
		if (isset($data->results) && isset($data->results[0])) {
			$lat = $data->results[0]->geometry->location->lat;
			$lng = $data->results[0]->geometry->location->lng;

			update_term_meta($loc_term_id, 'af_mli_location_latitude', $lat);
			update_term_meta($loc_term_id, 'af_mli_location_longitude', $lng);

		}
	}
}
// Haversine formula to calculate the distance between two lat/long points
function af_mli_haversine_great_circle_distance( $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371 ) {
	// Convert degrees to radians

	$latFrom = deg2rad((float) $latitudeFrom);
	$lonFrom = deg2rad((float) $longitudeFrom);
	$latTo   = deg2rad((float) $latitudeTo);
	$lonTo   = deg2rad((float) $longitudeTo);

	$latDelta = $latTo - $latFrom;
	$lonDelta = $lonTo - $lonFrom;

	$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
		cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	return $angle * $earthRadius; // Distance in kilometers
}

function af_mli_sort_locations_by_proximity( $af_mli_user_location, $af_mli_available_locations ) {
	$af_mli_user_lat = $af_mli_user_location['latitude'];
	$af_mli_user_lon = $af_mli_user_location['longitude'];



	// Calculate the distance for each location
	foreach ($af_mli_available_locations as $af_mli_location) {


		if (isset($af_mli_location->latitude) && isset($af_mli_location->longitude)) {
			$af_mli_location->distance = af_mli_haversine_great_circle_distance(
				$af_mli_user_lat,
				$af_mli_user_lon,
				$af_mli_location->latitude,
				$af_mli_location->longitude
			);
		} else {
			$af_mli_location->distance = $af_mli_location->term_id;
		}


	}

	// Sort the locations by distance
	usort($af_mli_available_locations, function ( $a, $b ) {
		if ($a->distance == $b->distance) {
			return 0;
		}
		return ( $a->distance < $b->distance ) ? -1 : 1;
	});


	return $af_mli_available_locations;
}
function af_mli_get_available_location( $af_selected_locations = array() ) {

	$af_mli_user_location = af_mli_get_user_location();


	if (!is_array($af_selected_locations) || count($af_selected_locations) < 1) {

		$af_selected_locations = get_terms(
			array(
				'number'     => 0,
				'hide_empty' => false,
				'taxonomy'   => 'mli_location',
			)
		);

	}

	$country                      = new WC_Geolocation();
	$current_user_location_detail = $country->geolocate_ip(af_mli_get_user_ip());

	$user_country_code = isset($current_user_location_detail['country']) ? $current_user_location_detail['country'] : '';


	if (isset($af_mli_user_location['country'])) {

		if (!empty($af_selected_locations)) {

			foreach ($af_selected_locations as $key => $af_created_location) {

				if (is_object($af_created_location) && $af_created_location->term_id) {

					$loc_term_id = $af_created_location->term_id;

					$location           = array();
					$af_mli_tax_country = !empty(get_term_meta($loc_term_id, 'af_mli_tax_country', true)) ? get_term_meta($loc_term_id, 'af_mli_tax_country', true) : $user_country_code;

					af_mli_get_lat_lng_from_address_nominatim($loc_term_id);

					if (( !in_array(get_option('af_mli_show_location_based_on'), array( 'auto', 'manual' )) ) && !empty($user_country_code) && $af_mli_tax_country != $user_country_code) {

						unset($af_selected_locations[ $key ]);
						continue;
					} else {
						$location = array(
							'latitude'  => get_term_meta($loc_term_id, 'af_mli_location_latitude', true),
							'longitude' => get_term_meta($loc_term_id, 'af_mli_location_longitude', true),
						);
					}

					$location_array                = array_merge((array) $af_created_location, $location);
					$location_object               = json_decode(wp_json_encode($location_array));
					$af_selected_locations[ $key ] = $location_object;

				}
			}

			if (in_array(get_option('af_mli_show_location_based_on'), array( 'auto', 'manual' ))) {

				$af_selected_locations = af_mli_sort_locations_by_proximity($af_mli_user_location, $af_selected_locations);

			}


		}
	}

	if ('yes' == get_option('mli_gen_restrict_user') && !empty(get_option('mli_gen_exclude_locations'))) {


		foreach ($af_selected_locations as $key => $currentinven_obej) {

			if (is_object($currentinven_obej) && $currentinven_obej->term_id && !in_array($currentinven_obej->term_id, (array) get_option('mli_gen_exclude_locations'))) {
				unset($af_selected_locations[ $key ]);
			}
		}

	}

	//  echo '<pre>';

	//  print_r($af_mli_user_location);
//  print_r($af_selected_locations);

	//  echo '</pre>';
	return $af_selected_locations;
}

function convert_in_readable_numbers( $number ) {

	// first strip any formatting;.
	$number = ( 0 + str_replace(',', '', $number) );

	// is this a number?.
	if (!is_numeric($number)) {
		return false;
	}

	// now filter it;.
	if ($number > 1000000000000) {

		return round(( $number / 1000000000000 ), 1) . ' T';

	} elseif ($number > 1000000000) {

		return round(( $number / 1000000000 ), 1) . ' B';

	} elseif ($number > 1000000) {

		return round(( $number / 1000000 ), 1) . ' M';

	} elseif ($number > 1000) {

		return round(( $number / 1000 ), 1) . ' K';
	}

	return number_format($number);
}
