<?php
/**
 * Front file of Module
 *
 * Manage all settings and actions of front
 *
 * @package  Addify Product Multi Location Inventory
 * @version  1.0.0
 */

defined('ABSPATH') || exit();

add_action('wp_enqueue_scripts', 'af_mli_front_enqueue_script', 11);
add_action('wp_head', 'af_front_styling');
add_action('woocommerce_before_add_to_cart_button', 'af_before_add_to_cart_button_inven_show');
add_action('woocommerce_add_to_cart_validation', 'af_add_cart_validation', 30, 4);

function af_mli_front_enqueue_script() {

	wp_enqueue_style('mli_front_css', plugins_url('../../assets/css/af-m-l-i-front.css', __FILE__), array(), '1.0.0');

	wp_enqueue_script('mli_front_js', plugins_url('../../assets/js/af-m-l-i-front.js', __FILE__), array( 'jquery' ), '1.0.0', true);

	wp_enqueue_style('mli_frontawesome', AFMLI_URL . 'assets/font-awesome/css/font-awesome.min.css', array(), '4.7.0');

	wp_enqueue_style('select2-css', plugins_url('assets/css/select2.css', WC_PLUGIN_FILE), array(), '5.7.2');

	wp_enqueue_script('select2-js', plugins_url('assets/js/select2/select2.min.js', WC_PLUGIN_FILE), array( 'jquery' ), '4.0.3', true);

	$location = get_option('af_mli_prod_page_loc_show_type');
	// Popup HTML and settings
	ob_start();
	if ('yes' != get_option('af_mi_disable_select_location_button')) {
		$af_mi_menu_button_text_color = !empty(get_option('af_mi_menu_button_text_color')) ? get_option('af_mi_menu_button_text_color') : '#ffffff';
		$af_mi_menu_button_bg_color   = !empty(get_option('af_mi_menu_button_bg_color')) ? get_option('af_mi_menu_button_bg_color') : '#0c0d0d';
		$af_mi_heading_color          = !empty(get_option('af_mi_heading_color')) ? get_option('af_mi_heading_color') : '#0c0d0d';
		?>
		<li class="menu-item af-mi-nearest-location">
			<button
				style="padding:4px; color:<?php echo esc_attr($af_mi_menu_button_text_color); ?>; background-color:<?php echo esc_attr($af_mi_menu_button_bg_color); ?>;"
				id="af-mi-select-location-btn"><?php echo esc_attr(get_option('af_mi_select_location_button_text') ? get_option('af_mi_select_location_button_text') : 'Please choose a store'); ?></button>
			<div id="af-mi-location-popup" class="af-mi-popup-overlay"
				style="display:<?php echo esc_attr('yes' == get_option('af_mi_always_show_popup_if_location_is_not_selected') && empty(wc()->session->get('af_user_selected_location')) ? 'block !important' : 'none'); ?>">
				<div class="af-mi-popup-content">
					<span id="af-mi-popup-close" class="af-mi-popup-close">&times;</span>
					<div class="af-mi-popup-popup-before-form-div">
						<img src="<?php echo esc_url(AFMLI_URL . 'assets/location-map.jpg'); ?>" />
						<div class="af-mi-content-wrap-location">
							<?php
							$heading_level           = !empty(get_option('af_mi_popup_heading_level')) ? get_option('af_mi_popup_heading_level') : 'h1';
							$af_mi_popup_heading     = !empty(get_option('af_mi_popup_heading')) ? get_option('af_mi_popup_heading') : 'Please choose a store';
							$af_mi_popup_description = !empty(get_option('af_mi_popup_description')) ? get_option('af_mi_popup_description') : 'Welcome to our selection of multiple stores, where you can choose the best one to suit your needs.';
							echo esc_attr('<' . $heading_level . ' style="color:' . $af_mi_heading_color . '" >' . $af_mi_popup_heading . '</' . $heading_level . '>');
							$af_selected_locations = af_mli_get_available_location();
							$af_loc_widget         = (array) wc()->session->get('af_user_selected_location');
							if (!empty($af_selected_locations)) {
								?>
								<form method="post">
									<p><?php echo esc_attr($af_mi_popup_description); ?></p>
									<?php wp_nonce_field('af_loc_nonce', 'af_loc_nonce_field'); ?>
									<select name="af_loc_widget[]" <?php echo esc_attr('single' != get_option('af_mli_show_select_location_dropdown_as') ? 'multiple' : ''); ?>
										class="af_loc_widget" style="width: 100%">
										<?php
										foreach ($af_selected_locations as $af_created_location) {

											if ($af_created_location) {

												$loc_term_id = $af_created_location->term_id;

												$af_mli_tax_adress   = get_term_meta($loc_term_id, 'af_mli_tax_adress', true);
												$af_mli_tax_country  = get_term_meta($loc_term_id, 'af_mli_tax_country', true);
												$af_mli_tax_state    = get_term_meta($loc_term_id, 'af_mli_tax_state', true);
												$af_mli_tax_city     = get_term_meta($loc_term_id, 'af_mli_tax_city', true);
												$af_mli_tax_zip_code = get_term_meta($loc_term_id, 'af_mli_tax_zip_code', true);
												$full_address        = trim($af_mli_tax_adress . ', ' . $af_mli_tax_city . ', ' . $af_mli_tax_state . ', ' . $af_mli_tax_country . ' ' . $af_mli_tax_zip_code);
												$location            = af_mli_get_lat_lng_from_address_nominatim($full_address);
												?>
												<option value="<?php echo esc_attr($af_created_location->term_id); ?>" 
																			<?php
																			if (in_array($af_created_location->term_id, $af_loc_widget)) :
																				?>
														selected <?php endif ?>>

													<?php echo esc_html($af_created_location->name); ?>

												</option>
												<?php
											}
										}
										?>

									</select>
									<input type="submit" name="af_search_btn" value="Proceed" style="margin-top: 13px;">
									<input type="submit" name="af_clear_location" value="Reset" style="margin-top: 13px;">
								</form>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</li>
		<?php
	}
	$menu_html = ob_get_clean();

	$ary = array(
		'af_location'                   => $location,
		'admin_url'                     => admin_url('admin-ajax.php'),
		'nonce'                         => wp_create_nonce('_addify_mli_nonce'),
		'auto_select_higest_stock'      => get_option('af_mli_auto_select_inventory_with_highest_stock'),
		'menu_html'                     => $menu_html,
		'af_mli_out_of_stock_error_msg' => !empty(get_option('af_mli_out_of_stock_error_msg')) ? get_option('af_mli_out_of_stock_error_msg') : 'Out of stock in {inventory_name}.',
	);
	wp_localize_script('mli_front_js', 'addify_multi_inventory_front', $ary);
}

function af_add_cart_validation( $validation, $product_id, $quantity, $variation_id = 0 ) {

	if ('yes' == get_option('mli_gen_backend_mode_only') || empty($_POST['af_mli_inven_selector']) || '1' != wc_get_product($product_id)->get_manage_stock()) {

		return $validation;
	}

	$error_message = '';

	$nonce = isset($_POST['af_inven_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['af_inven_nonce_field'])) : 0;

	if (isset($_POST['af_prd_page_field']) && !wp_verify_nonce($nonce, 'af_inven_nonce')) {

		wp_die('Failed Security check validation');
	}

	$inven_selector = isset($_POST['af_mli_inven_selector']) ? sanitize_text_field(wp_unslash($_POST['af_mli_inven_selector'])) : '';

	$selected_location = '';

	foreach (WC()->cart->get_cart() as $item_key => $value_check) {

		if (isset($value_check['selected_location'])) {

			$loc_id = $value_check['selected_location'];

			$selected_location = $loc_id['selected_value'];

			if ($inven_selector != $selected_location) {

				$error_message = get_option('mli_prod_page_loc_diff_msg');

			}
		}
	}

	if (!empty($error_message)) {

		wc_add_notice($error_message, 'error');

		return false;
	}

	$pro_id = $variation_id > 0 ? $variation_id : $product_id;

	if (isset($_POST['af_mli_inven_selector'])) {

		$nonce = isset($_POST['af_inven_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['af_inven_nonce_field'])) : 0;

		if (isset($_POST['af_prd_page_field']) && !wp_verify_nonce($nonce, 'af_inven_nonce')) {

			wp_die('Failed Security Check');
		}

		$inven_selector = isset($_POST['af_mli_inven_selector']) ? sanitize_text_field(wp_unslash($_POST['af_mli_inven_selector'])) : '';

		$af_mi_get_current_product_inventory = af_mi_get_current_product_inventory($pro_id);


		foreach ($af_mi_get_current_product_inventory as $key => $current_array) {


			if (isset($current_array['value']) && $inven_selector == $current_array['value']) {
				if (( !isset($current_array['total_stock']) ) || ( (float) $current_array['total_stock'] <= 1 )) {


					$error_message = str_replace(array( '{inventory_name}' ), array( $current_array['name'] ), get_option('af_mli_out_of_stock_error_msg'));

					wc_add_notice($error_message, 'error');

					return false;

				}

				if (isset($current_array['total_stock']) && ( (float) $current_array['total_stock'] >= 1 ) && $quantity > (float) $current_array['total_stock']) {

					$error_message  = isset($current_array['name']) ? $current_array['name'] : ' ';
					$error_message .= ' have only ' . (float) $current_array['total_stock'] . ' quantity';
					wc_add_notice($error_message, 'error');

					return false;

				}
			}

		}


	}


	return $validation;
}

add_action('woocommerce_cart_item_set_quantity', 'after_cart_item_quantity_update', 10, 2);

function after_cart_item_quantity_update( $cart_item_key, $quantity ) {
	$cart = wc()->cart->get_cart();

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
}

add_filter('woocommerce_get_price_html', function ( $price_html, $product ) {

	$all_inven_values = (array) af_mi_get_current_product_inventory($product->get_id());
	$all_inven_values = af_mli_custom_array_filter($all_inven_values);


	if (1 == count($all_inven_values) && is_array(current($all_inven_values)) && isset(current($all_inven_values)['formatted_price'])) {
		$price_html = current($all_inven_values)['formatted_price'];
	}

	return $price_html;
}, 10, 2);

function af_front_styling() {
	if ( is_home() ) {
	   return;
	}
	$display_field_border_radius = get_option('display_field_border_radius');

	$display_field_padding = get_option('display_field_padding');

	$f_b_r_top_left = isset($display_field_border_radius['top_left']) ? $display_field_border_radius['top_left'] : 0;

	$f_b_r_top_right = isset($display_field_border_radius['top_right']) ? $display_field_border_radius['top_right'] : 0;

	$f_b_r_bottom_left = isset($display_field_border_radius['bottom_left']) ? $display_field_border_radius['bottom_left'] : 0;

	$f_b_r_bottom_right = isset($display_field_border_radius['bottom_right']) ? $display_field_border_radius['bottom_right'] : 0;

	$f_p_top = isset($display_field_padding['top']) ? $display_field_padding['top'] : 0;

	$f_p_bottom = isset($display_field_padding['bottom']) ? $display_field_padding['bottom'] : 0;

	$f_p_left = isset($display_field_padding['left']) ? $display_field_padding['left'] : 0;

	$f_p_right = isset($display_field_padding['right']) ? $display_field_padding['right'] : 0;

	?>

	<style>
		<?php if ('drop_down' == get_option('af_mli_prod_page_loc_show_type')) : ?>

			.af_mli_inven_selector {

				height: 45px;
				padding: 9px 12px;
				width: 100%;
				background: white;
				border: 1px solid #d3d3d352;
				-webkit-appearance: none;
				-moz-appearance: none;
				position: relative;
				appearance: none;
			}

			.af_mli_front_inven_div {
				height: 45px;
				width: 85%;
				position: relative;
			}

			.af_mli_front_inven_div:before {
				content: "\f107";
				position: absolute;
				right: 22px;
				top: 50%;
				transform: translateY(-50%);
				font-family: 'FontAwesome';
				font-size: 13px;
				color: #000;
				z-index: 99;
			}


		<?php endif ?>

		.af_mli_front_inven_div {
			margin-bottom: 20px;
		}

		.af_inven_heading {

			<?php if (!empty(get_option('display_heading_display'))) : ?>

				color:
					<?php echo esc_attr(get_option('display_heading_color')); ?>
					!important;

			<?php endif ?>
		}

		.af_mli_inven_selector_radio {

			<?php if (!empty(get_option('display_field_option_font_size'))) : ?>

				width:
					<?php echo esc_attr(get_option('display_field_option_font_size') . 'px !important'); ?>
				;

			<?php endif ?>

			<?php if (!empty(get_option('display_field_option_font_size'))) : ?>

				height:
					<?php echo esc_attr(get_option('display_field_option_font_size') . 'px !important'); ?>
				;

			<?php endif ?>
		}

		.af_mli_inven_selector {

			<?php if (!empty(get_option('display_field_option_color'))) : ?>

				color:
					<?php echo esc_attr(get_option('display_field_option_color') . ' !important'); ?>
				;

			<?php endif ?>

			<?php if (!empty(get_option('display_field_option_font_size'))) : ?>

				font-size:
					<?php echo esc_attr(get_option('display_field_option_font_size') . 'px !important'); ?>
				;

			<?php endif ?>
		}

		.af_mli_front_stock_div {

			<?php if (!empty(get_option('display_field_background_color'))) : ?>

				background-color:
					<?php echo esc_attr(get_option('display_field_background_color')); ?>
				;

			<?php endif ?>

			<?php if ('yes' == get_option('display_field_border')) : ?>

				border: 1px solid
					<?php echo esc_attr(get_option('display_field_border_color')); ?>
				;

				border-top-left-radius:
					<?php echo esc_attr($f_b_r_top_left . 'px'); ?>
				;

				border-top-right-radius:
					<?php echo esc_attr($f_b_r_top_right . 'px'); ?>
				;

				border-bottom-left-radius:
					<?php echo esc_attr($f_b_r_bottom_left . 'px'); ?>
				;

				border-bottom-right-radius:
					<?php echo esc_attr($f_b_r_bottom_right . 'px'); ?>
				;

				padding-top:
					<?php echo esc_attr($f_p_top . 'px'); ?>
				;

				padding-bottom:
					<?php echo esc_attr($f_p_bottom . 'px'); ?>
				;

				padding-left:
					<?php echo esc_attr($f_p_left . 'px'); ?>
				;

				padding-right:
					<?php echo esc_attr($f_p_right . 'px'); ?>
				;

			<?php endif ?>
		}

		.af_main_total_div {

			width: 85%;

			margin-bottom: 30px;

			background: border-box;
		}

		.af_main_total_div h2 {

			font-weight: 700;

			font-size: 26px;

			line-height: 36px;

			margin-bottom: 23px;
		}

		.af_main_total_div table {

			margin: 0;
		}

		.af_main_total_div table tr th,
		.af_main_total_div table td {

			background: none;

			padding: 14px 0px;

			color: #000;

			font-size: 15px;

			line-height: 25px;

			border-bottom: 1px solid #d3d3d352;
		}

		.af_main_total_div table tr th:first-child,
		.af_main_total_div table tr td:first-child {

			width: 60%;
		}

		.af_main_total_div table tr th:last-child,
		.af_main_total_div table tr td:last-child {

			width: 40%;
		}

		.af_main_total_div table td {

			background-color: #fdfdfd00 !important;

			color: #000;
		}

		.af_main_total_div table tbody tr:last-child td {

			border-bottom: 0 !important;
		}

		.af_mli_front_inven_div:before {
			content: "\f107";
			position: absolute;
			right: 22px;
			top: 50%;
			transform: translateY(-50%);
			font-family: 'FontAwesome';
			font-size: 13px;
			color: #000;
			z-index: 99;
		}
	</style>

	<?php
}

function af_before_add_to_cart_button_inven_show() {

	global $product;
	if ('yes' != get_option('af_mli_prod_page_inven') || 'yes' == get_option('mli_gen_backend_mode_only')) {

		// if ('yes' != get_option('af_mli_prod_page_inven') || 'yes' == get_option('mli_gen_backend_mode_only') || ('variable' != $product->get_type() && '1' != $product->get_manage_stock()) ) {
		return;
	}

	wp_nonce_field('af_inven_nonce', 'af_inven_nonce_field');
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

	?>
	<p class="out_of_stock_msg_p" style="display: none;"><?php echo esc_html('Out of Stock on this location'); ?></p>

	<div class="af_mli_show_i">
		<?php
		//  if ('variable' != $product->get_type() && count($inven_posts) < 1 && 'yes' != get_post_meta($product->get_id(), 'prod_level_inven', true)) {
	
		// if ('variable' != $product->get_type() && count($inven_posts) >= 1) {
		af_mi_get_product_inventory($product->get_id());
		// }
		?>
	</div>

	<?php

	// if ('variation' != $product->get_type() && 'yes' != get_post_meta($product->get_id(), 'prod_level_inven', true)) {

	//  return;

	// }

	// $term = get_term(get_post_meta($product->get_id(), 'in_location', true), 'mli_location');


	// Check if the term object exists
	// if ($term && !is_wp_error($term)) {
	//  $taxonomy_name = $term->name;
	// }
}
add_filter('woocommerce_loop_add_to_cart_link', function ( $html, $product ) {

	// '1' != $product->get_manage_stock()
	if ('yes' == get_option('mli_gen_backend_mode_only') || ( 'variable' == $product->get_type() )) {
		return $html;
	}

	$all_inven_values = (array) af_mi_get_current_product_inventory($product->get_id());
	$all_inven_values = af_mli_custom_array_filter($all_inven_values);

	

	if (!empty(wc()->session)) {

		$af_selected_location = (array) wc()->session->get('af_user_selected_location');
		$af_selected_location = af_mli_custom_array_filter($af_selected_location);

	} else {

		$af_selected_location = array();

	}

	// !in_array(get_option('mli_gen_default_location'), $af_selected_location) &&
	if ('variable' != $product->get_type() && 'hide_disable_add_to_cart' == get_option('af_mli_on_lcation_selection')) {

		// if (('variable' != $product->get_type() && '1' != $product->get_manage_stock())) {

		//  return $html;

		// }

		if (count($af_selected_location) >= 1 && ( count($all_inven_values) < 1 )) {

			$inventory_name = array();

			foreach ($af_selected_location as $location_id) {

				$location_object = get_term($location_id);

				if (is_object($location_object) && isset($location_object->name)) {
					$inventory_name[] = $location_object->name;
				}

			}
			$html = '<span class="button" style="color:red;" >' . str_replace('{inventory_name}', implode(' , ', $inventory_name), get_option('af_mli_out_of_stock_error_msg')) . '</span>';
		}

	}

	return $html;
}, 10, 2);

add_action(
	'woocommerce_before_add_to_cart_form',
	function () {

		global $product;
		// if ('yes' != get_option('af_mli_prod_page_inven') || 'yes' == get_option('mli_gen_backend_mode_only') || ('variable' != $product->get_type() && '1' != $product->get_manage_stock())) {
		?>
	<input type="hidden" class="af-mli-hidden-data"
		data-max_product_limit="<?php echo esc_attr($product->get_max_purchase_quantity()); ?>">
	<?php
		if ('yes' != get_option('af_mli_prod_page_inven') || 'yes' == get_option('mli_gen_backend_mode_only')) {
			return;
		}

		$all_inven_values = (array) af_mi_get_current_product_inventory($product->get_id());
		$all_inven_values = af_mli_custom_array_filter($all_inven_values);

		$af_selected_location = (array) wc()->session->get('af_user_selected_location');
		$af_selected_location = af_mli_custom_array_filter($af_selected_location);


		if ('variable' != $product->get_type() && 'hide_disable_add_to_cart' == get_option('af_mli_on_lcation_selection')) {
			if (count($af_selected_location) >= 1 && ( count($all_inven_values) < 1 )) {
				$inventory_name = array();

				foreach ($af_selected_location as $location_id) {

					$location_object = get_term($location_id);

					if (is_object($location_object) && isset($location_object->name)) {
						$inventory_name[] = $location_object->name;
					}
				}
				?>
			<p class="stock out-of-stock af-remove-add-to-cart-form ">
				<?php echo esc_attr(str_replace('{inventory_name}', implode(' , ', $inventory_name), get_option('af_mli_out_of_stock_error_msg'))); ?>
			</p>
			<?php

			}
		}
	}
);

add_filter('woocommerce_available_variation', 'remove_variation_for_customer_role', 10, 3);

function remove_variation_for_customer_role( $variation_data, $product, $variation ) {
	// Check if the user is logged in and has the 'customer' role

	if (!empty(wc()->session)) {
		$af_selected_location = (array) wc()->session->get('af_user_selected_location');
	} else {

		$af_selected_location = array();
	}
	
	$af_selected_location = af_mli_custom_array_filter($af_selected_location);
	// !in_array(get_option('mli_gen_default_location'), $af_selected_location) &&
	if ('hide_disable_add_to_cart' == get_option('af_mli_on_lcation_selection')) {

		$all_inven_values = (array) af_mi_get_current_product_inventory($variation->get_id());
		$all_inven_values = af_mli_custom_array_filter($all_inven_values);

		if (count($af_selected_location) >= 1 && count($all_inven_values) < 1) {
			return false;
		}
	}

	// Return the variation data if no exclusions were made
	return $variation_data;
}



add_action('wp_loaded', function () {

	$af_loc_widget = array();

	if (isset($_POST['af_search_btn'])) {
		$nonce = isset($_POST['af_loc_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['af_loc_nonce_field'])) : '';
		if (!wp_verify_nonce($nonce, 'af_loc_nonce')) {
			wp_die('Failed Security Check');
		}
		$af_loc_widget = isset($_POST['af_loc_widget']) ? sanitize_meta('', wp_unslash($_POST['af_loc_widget']), '') : array();
		wc()->session->set('af_user_selected_location', $af_loc_widget);

	}
	if (isset($_POST['af_clear_location'])) {
		$nonce = isset($_POST['af_loc_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['af_loc_nonce_field'])) : '';
		if (!wp_verify_nonce($nonce, 'af_loc_nonce')) {
			wp_die('Failed Security Check');
		}
		wc()->session->set('af_user_selected_location', array());

	}

	if (wc() && wc()->session) {
		$af_loc_widget = (array) wc()->session->get('af_user_selected_location');

		$af_loc_widget = af_mli_custom_array_filter($af_loc_widget);


		if (count($af_loc_widget) < 1 && 'yes' == get_option('af_mli_auto_select_nearest_location')) {

			ob_start();
			$af_selected_locations = (array) af_mli_get_available_location();

			if (!empty($af_selected_locations)) {

				$first_category = current($af_selected_locations);

				if (is_object($first_category) && isset($first_category->term_id) && !is_checkout()) {
					$af_loc_widget = array( $first_category->term_id );
					wc()->session->set('af_user_selected_location', $af_loc_widget);

				}

			}

			$result = ob_get_clean();

		}
	}
});
add_action('woocommerce_product_query', function ( $q ) {

	$af_loc_widget = (array) wc()->session->get('af_user_selected_location');
	$product_args  = array(
		'numberposts' => -1,
		'post_status' => array( 'publish' ),
		'post_type'   => array( 'product' ),
		'fields'      => 'ids',
	);

	// && !in_array(get_option('mli_gen_default_location'), $af_loc_widget)

	if (!empty($af_loc_widget) && 'hide_disable_add_to_cart' != get_option('af_mli_on_lcation_selection')) {

		if (!empty($af_loc_widget)) {

			$product_args['tax_query'] = array(

				array(
					'taxonomy' => 'mli_location',
					'field'    => 'term_id', // This is optional, as it defaults to 'term_id'.
					'terms'    => $af_loc_widget,
					'operator' => 'IN', // Possible values are 'IN', 'NOT IN', 'AND'.
				),
			);

			$products_ids = (array) get_posts($product_args);

			if (empty($products_ids)) {

				$product = wc_get_products(
					array(
						'return'         => 'ids',
						'posts_per_page' => '-1',
					)
				);

				$q->set('post__not_in', $product);

			} else {

				$q->set('post__in', $products_ids);

			}
		}
		do_action('addify_query_visibility_applied', $q);

	}
}, 200, 2);


// add_action(
//  'wp_footer',
//  function () {

//      if (is_wc_endpoint_url('order-received')) {

			// wc()->session->set('af_user_selected_location', array());
//      }
//  }
// );


function af_mi_add_popup_styles_to_head() {
	$background_color = !empty(get_option('af_mi_popup_bg_color')) ? get_option('af_mi_popup_bg_color') : '#ffffff';
	$opacity          = !empty(get_option('af_mi_popup_opacity')) ? get_option('af_mi_popup_opacity') : 0.8;
	$text_color       = !empty(get_option('af_mi_popup_text_color')) ? get_option('af_mi_popup_text_color') : '#111111';
	?>
	<style>
		/* Popup overlay styling */
		#af-mi-location-popup {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			z-index: 9999;
		}

		/* Popup content styling */
		.af-mi-popup-content {
			position: fixed;
			width:
				<?php echo esc_attr(!empty(get_option('af_mi_popup_width')) ? get_option('af_mi_popup_width') . '%' : 50 . '%'); ?>
			;
			height:
				<?php echo esc_attr(!empty(get_option('af_mi_popup_height')) ? get_option('af_mi_popup_height') . '%' : 50 . '%'); ?>
			;
			opacity: 1;
			/* padding: 20px; */
			/* background-color: #fff; */

			background-color:
				<?php echo esc_attr($background_color); ?>
			;

			color:
				<?php echo esc_attr($text_color); ?>
			;
			box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
			transition: all 0.3s ease;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
		}

		/* Close button styling */
		.af-mi-popup-close {
			position: absolute;
			top: -16px;
			font-size: 24px;
			cursor: pointer;
			right: -12px;
			border-radius: 50%;
			background: red;
			padding: 0 4px;
			color: #fff;
			line-height: 20px;
		}

		.af-mi-popup-popup-before-form-div img {
			width: 46%;
		}

		.af-mi-popup-popup-before-form-div {
			display: flex;
			justify-content: space-between;
		}

		.af-mi-content-wrap-location {
			width: 50%;
		}

		.af-mi-content-wrap-location p {
			margin-bottom: 16px;
			font-size: 15px;
			line-height: 25px;
		}

		.af-mi-popup-popup-before-form-div {
			overflow-y: auto;
			width: 100%;
			height: 100%;
			padding: 20px;
		}

		/* Heading and text color */
		.af-mi-popup-heading,
		.af-mi-popup-description {
			color:
				<?php echo esc_attr($text_color); ?>
			;
		}

		.af-mi-popup-popup-before-form-div h1 {
			color: #1d1f1f;
			font-size: 24px;
			line-height: 34px;
			font-weight: 600;
			margin-bottom: 10px;
		}

		.select2-selection.select2-selection--single {
			height: 43px;
			line-height: 53px;
			margin-bottom: 7px;
			border: 1px solid #d3d3d3c7;
		}

		.af-mi-popup-popup-before-form-div .select2-container--default .select2-selection--single .select2-selection__rendered {
			line-height: 39px;
		}

		.af-mi-popup-popup-before-form-div form input[type="submit"] {
			margin-top: 10px;
			background: black;
			color: #fff;
			padding: 8px 23px;
			font-size: 14px;
			border-radius: 3px;
			margin-right: 10px;
			line-height: 24px;
		}

		.af-mi-popup-popup-before-form-div form {
			margin-bottom: 14px;
		}

		.af-mi-popup-popup-before-form-div .select2-container--default .select2-selection--single .select2-selection__arrow {
			height: 39px;
			width: 28px;
		}
	</style>
	<?php
}
add_action('wp_head', 'af_mi_add_popup_styles_to_head');



add_action('woocommerce_before_calculate_totals', 'custom_change_cart_item_name_before_calculation', 10, 1);

function custom_change_cart_item_name_before_calculation( $cart ) {
	if (is_admin() || !did_action('woocommerce_before_calculate_totals') || 'yes' != get_option('af_mli_prod_page_inven')) {
		return;
	}
	if ($cart && $cart->get_cart()) {

		foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
			if (isset($cart_item['data']) && is_object($cart_item['data']) && isset($cart_item['selected_location'])) {

				$product_id = isset($cart_item['variation_id']) && $cart_item['variation_id'] >= 1 ? $cart_item['variation_id'] : $cart_item['product_id'];

				$product = wc_get_product($product_id);
				$product = $cart_item['data'];

				if (!empty(af_mi_get_current_product_inventory($product_id))) {
					ob_start();
					?>
					<div class="af-mli-cart-page-change-location-div af-mli-cart-page-change-location-div<?php echo esc_attr($cart_item_key); ?>"
						data-cart_item_key="<?php echo esc_attr($cart_item_key); ?>">
					</div>
					<?php
					$result = ob_get_clean();

					$new_name = $result . $product->get_description();
					// Set the new name
					$cart_item['data']->set_description($new_name);

					$new_name = $result . $product->get_short_description();
					// Set the new name
					$cart_item['data']->set_short_description($new_name);
					$product->set_description($new_name);


				}


			}
		}

	}
}
