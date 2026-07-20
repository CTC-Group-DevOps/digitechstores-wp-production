<?php
/**
 * Plugin Name:             Multi Inventory Management
 * Plugin URI:              https://woocommerce.com/products/addify-multi-inventory-management/
 * Description:             Manage multi-location stock using our advanced inventory management system.
 * Version:                 2.0.3
 * Author:                  Addify
 * Developed By:            Addify
 * Author URI:              https://woocommerce.com/vendor/addify/
 * Support:                 https://woocommerce.com/vendor/addify/
 * License:                 GPL-3.0+
 * License URI:             http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path:             /languages
 * Text Domain :            addify-multi-inventory-management
 * Requires Plugins: WooCommerce
 * WC requires at least: 4.0
 * WC tested up to: 10.*.*
 * Requires at least: 6.5
 * Tested up to: 6.*.*
 * Requires PHP: 7.4
 * Woo: 18734003497462:a132827b80c734af89d9483bb9509a6c

 */

defined('ABSPATH') || exit();


class Addify_Multi_Location_Inventory_Main {




	public function __construct() {

		$this->af_mli_global_constents_vars();

		register_activation_hook(__FILE__, array( $this, 'af_mli_update_option' ));

		add_action('plugins_loaded', array( $this, 'af_mli_init' ));

		add_action('before_woocommerce_init', array( $this, 'af_mli_h_o_p_s_compatibility' ));

		add_action('init', array( $this, 'af_mli_admin_init' ));
		include AFMLI_PLUGIN_DIR . 'includes/af-mli-cron-jobs.php';
	}

	public function af_mli_init() {

		if (!is_multisite() && !in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')), true)) {

			add_action('admin_notices', array( $this, 'af_mli_check_wocommerce' ));

			return;
		}

		if ('yes' != get_option('mli_gen_backend_mode_only') && 'yes' == get_option('mli_shop_loc_filter_widget')) {

			include AFMLI_PLUGIN_DIR . 'includes/front/class-addify-loc-widget.php';

		}
	}

	public function af_mli_check_wocommerce() {

		deactivate_plugins(__FILE__);

		?>

		<div id="message" class="error">

			<p>

				<strong>

					<?php echo esc_html__('Multi Inventory Management plugin is inactive. WooCommerce plugin must be active in order to activate it.', 'addify-multi-inventory-management'); ?>

				</strong>

			</p>

		</div>

		<?php
	}

	public function af_mli_admin_init() {


		if (!defined('WC_PLUGIN_FILE')) {
			return;
		}


		add_action('wp_loaded', array( $this, 'af_mli_load_text_domain' ));

		$this->af_mli_custom_post_types();

		$this->af_mli_custom_taxonomy();

		$this->af_mli_schedule_crone_job();

		add_action('addify_update_auto_location_stock', array( $this, 'addify_update_auto_location_stock' ), 10);
		//      add_action('wp_footer', array($this, 'addify_update_auto_location_stock'), 10);

		add_filter('woocommerce_email_classes', array( $this, 'af_mli_shop_manager_emails' ), 90, 1);

		add_action('woocommerce_order_item_meta_start', array( $this, 'af_show_option_name' ), 10, 2);

		add_action('woocommerce_after_order_itemmeta', array( $this, 'af_show_option_name' ), 10, 2);

		include AFMLI_PLUGIN_DIR . 'includes/admin/manage_stock_tab/class-mli-product-stock-table.php';
		include AFMLI_PLUGIN_DIR . 'includes/admin/class-addify-mli-ajax.php';
		include AFMLI_PLUGIN_DIR . 'includes/af-mi-general-functions.php';

		if (!is_admin()) {
			if ('yes' != get_option('mli_gen_backend_mode_only')) {
				include AFMLI_PLUGIN_DIR . 'includes/front/class-addify-multi-location-inventory-front.php';
				include AFMLI_PLUGIN_DIR . 'includes/front/class-addify-shipping.php';
				include AFMLI_PLUGIN_DIR . 'includes/front/class-addify-multi-location-inventory-cart.php';

			}
		} else {

			include AFMLI_PLUGIN_DIR . 'includes/admin/class-addify-mli-woo-tab.php';
			include AFMLI_PLUGIN_DIR . 'includes/admin/product/class-addify-mli-product.php';
			include AFMLI_PLUGIN_DIR . 'includes/admin/variation/class-addify-mli-variation.php';
			include AFMLI_PLUGIN_DIR . 'includes/admin/class-af-mli-admin-gen-settings.php';
			include AFMLI_PLUGIN_DIR . 'includes/admin/product/class-addify-mli-prod-custom-column.php';
			include AFMLI_PLUGIN_DIR . 'includes/admin/taxonomy/class-addify-mli-taxonomy.php';
			include AFMLI_PLUGIN_DIR . 'includes/admin/class-addify-multi-location-inventory-admin.php';

		}
	}

	public function af_mli_h_o_p_s_compatibility() {

		if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {

			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);

		}
	}

	public function af_mli_shop_manager_emails( $emails ) {

		include_once AFMLI_PLUGIN_DIR . 'includes/admin/email/class-af-mli-shop-manager-email.php';

		$emails['af_mli_shop_manager_email'] = new Af_Mli_Shop_Manager_Email();

		return $emails;
	}

	public function af_mli_update_option() {

		update_option('af_mli_loc_ptn_heading_title', 'h1');

		update_option('af_mli_over_stock_quantity', 200);

		update_option('af_mli_in_stock_color', '#d21919');

		update_option('af_mli_out_of_stock_color', '#eeff00');

		update_option('af_mli_back_order_color', '#3500f5');

		update_option('af_mli_low_stock_color', '#03fcf8');

		update_option('af_mli_over_stock_color', '#11fd0d');

		update_option('display_heading', 'Heading');

		update_option('display_heading_color', '#ff0000');

		update_option('display_title_display', '1');

		update_option('display_title', 'Title');

		update_option('display_title_color', '#00ff91');

		update_option('display_field_border', '1');

		update_option('display_field_border_color', '#000000');

		update_option('display_field_background_color', '#ffffff');

		update_option('display_field_option_color', '#ff0000');

		update_option('display_field_option_font_size', '20');

		update_option('mli_shop_loc_name_stock', '1');

		update_option('mli_shop_loc_price', '1');

		update_option('mli_shipp_zones_to_loc', '1');

		update_option('mli_shipp_methods_to_loc', '1');

		update_option('mli_shipp_payment_method_of_loc', '1');

		update_option('mli_prod_page_loc_diff_msg', 'Product from 2 different locations are not allowed.');

		$manage_stock_checkbox_array = array( 'inven_stock_quan', 'prd_name', 'inven_stock_status', 'prd_price' );

		update_option('af_mli_ms_checkbox', $manage_stock_checkbox_array);
	}

	public function af_mli_global_constents_vars() {

		if (!defined('AFMLI_URL')) {

			define('AFMLI_URL', plugin_dir_url(__FILE__));

		}

		if (!defined('AFMLI_BASENAME')) {

			define('AFMLI_BASENAME', plugin_basename(__FILE__));

		}

		if (!defined('AFMLI_PLUGIN_DIR')) {

			define('AFMLI_PLUGIN_DIR', plugin_dir_path(__FILE__));

		}
	}

	public function af_mli_load_text_domain() {

		if (function_exists('load_plugin_textdomain')) {

			load_plugin_textdomain('addify-multi-inventory-management', false, dirname(plugin_basename(__FILE__)) . '/languages/');

		}
	}

	public function af_mli_custom_post_types() { 



		$i_labels = array(

			'name'           => esc_html__('Inventory', 'addify-multi-inventory-management'),

			'singular_name'  => esc_html__('Inventory', 'addify-multi-inventory-management'),

			'menu_name'      => esc_html__('Inventories', 'addify-multi-inventory-management'),

			'add_new'        => esc_html__('Add New', 'addify-multi-inventory-management'),

			'name_admin_bar' => esc_html__('Inventories', 'addify-multi-inventory-management'),

			'edit_item'      => esc_html__('Edit Inventory', 'addify-multi-inventory-management'),

			'view_item'      => esc_html__('View Inventory', 'addify-multi-inventory-management'),

			'all_items'      => esc_html__('Inventories', 'addify-multi-inventory-management'),

			'search_items'   => esc_html__('Search Inventories', 'addify-multi-inventory-management'),

			'not_found'      => esc_html__('No Inventory found', 'addify-multi-inventory-management'),
		);

		$i_args = array(

			'supports'            => array( 'title' ),

			'labels'              => $i_labels,

			'description'         => esc_html__('Custom Inventory', 'addify-multi-inventory-management'),

			'public'              => false,

			'show_ui'             => false,

			'show_in_menu'        => false,

			'exclude_from_search' => true,

			'capability_type'     => 'post',

			'capabilities'        => array( 'create_posts' => false ),

			'query_var'           => true,

			'menu_icon'           => plugins_url('/assets/addify-logo.png', __FILE__),

			'rewrite'             => array( 'slug' => 'af_prod_lvl_invent' ),

			'hierarchical'        => true,
		);

		register_post_type('af_prod_lvl_invent', $i_args);

		$s_l_labels = array(

			'name'           => esc_html__('Stock Log', 'addify-multi-inventory-management'),

			'singular_name'  => esc_html__('Stock Logs', 'addify-multi-inventory-management'),

			'menu_name'      => esc_html__('Stock Logs', 'addify-multi-inventory-management'),

			'add_new'        => esc_html__('Add New Log', 'addify-multi-inventory-management'),

			'name_admin_bar' => esc_html__('Stock Logs', 'addify-multi-inventory-management'),

			'edit_item'      => esc_html__('Edit Log', 'addify-multi-inventory-management'),

			'view_item'      => esc_html__('View Log', 'addify-multi-inventory-management'),

			'all_items'      => esc_html__('All Logs', 'addify-multi-inventory-management'),

			'search_items'   => esc_html__('Search Log', 'addify-multi-inventory-management'),

			'not_found'      => esc_html__('No Log found', 'addify-multi-inventory-management'),
		);

		$s_l_args = array(

			'supports'            => array( 'title' ),

			'labels'              => $s_l_labels,

			'description'         => esc_html__('Stock Logs', 'addify-multi-inventory-management'),

			'public'              => false,

			'show_ui'             => false,

			'show_in_menu'        => false,

			'exclude_from_search' => true,

			'capability_type'     => 'post',

			'query_var'           => true,

			'menu_icon'           => plugins_url('/assets/addify-logo.png', __FILE__),

			'rewrite'             => array( 'slug' => 'af_stock_log' ),

			'hierarchical'        => true,
		);

		register_post_type('af_stock_log', $s_l_args);
	}

	public function af_mli_custom_taxonomy() {

		$l_labels = array(

			'name'              => esc_html__('Locations', 'addify-multi-inventory-management'),

			'singular_name'     => esc_html__('Location', 'addify-multi-inventory-management'),

			'search_items'      => esc_html__('Search Locations', 'addify-multi-inventory-management'),

			'all_items'         => esc_html__('Manage Locations', 'addify-multi-inventory-management'),

			'parent_item'       => esc_html__('Parent Location', 'addify-multi-inventory-management'),

			'parent_item_colon' => esc_html__('Parent Location:', 'addify-multi-inventory-management'),

			'edit_item'         => esc_html__('Edit Location', 'addify-multi-inventory-management'),

			'update_item'       => esc_html__('Update Location', 'addify-multi-inventory-management'),

			'add_new_item'      => esc_html__('Add New Location', 'addify-multi-inventory-management'),

			'new_item_name'     => esc_html__('New Location Name', 'addify-multi-inventory-management'),

			'menu_name'         => esc_html__('Locations', 'addify-multi-inventory-management'),

		);

		$l_args = array(

			'hierarchical' => true,

			'labels'       => $l_labels,

			'show_ui'      => true,

			'show_in_rest' => true,

			'show_in_menu' => true,

			'query_var'    => true,

			'rewrite'      => array( 'slug' => 'mli_location' ),

		);

		register_taxonomy('mli_location', 'product', $l_args);
	}

	public function af_mli_schedule_crone_job() {

		if (!wp_next_scheduled('addify_update_auto_location_stock')) {

			wp_schedule_event(time(), 'every_60_seconds', 'addify_update_auto_location_stock');


		}
	}

	public function addify_update_auto_location_stock() {

		$inventory_locations = get_terms(
			array(
				'hide_empty' => false,
				'taxonomy'   => 'mli_location',
			)
		);


		$args = array(
			'post_type'  => 'af_stock_log',
			'date_query' => array(
				array(
					'year'  => gmdate('Y'),
					'month' => gmdate('m'),
					'day'   => gmdate('d'),
				),
			),
			'fields'     => 'ids',
		);

		$post_ids = get_posts($args);

		foreach ($post_ids as $post_id) {
			wp_delete_post($post_id);
		}

		foreach ($inventory_locations as $curren_location_object) {
			if (is_object($curren_location_object) && $curren_location_object->term_id) {
				af_mli_get_lat_lng_from_address_nominatim($curren_location_object->term_id);

				$product_args_array = array(
					'numberposts' => -1,
					'return'      => 'ids',
					'post_status' => 'publish',
					'tax_query'   => array(
						array(
							'taxonomy' => 'mli_location', // Replace with your taxonomy name
							'field'    => 'term_id',
							'terms'    => array( $curren_location_object->term_id ),
						),
					),

				);

				$all_prod_ids                 = wc_get_products($product_args_array);
				$current_location_total_stock = 0;

				foreach ($all_prod_ids as $current_product_id) {
					$current_product = wc_get_product($current_product_id);

					$all_product_ids = array( $current_product_id );
					if ('variable' == $current_product->get_type()) {
						$all_product_ids = $current_product->get_children();

					}

					foreach ($all_product_ids as $variation_id) {

						$variation_obj = wc_get_product($variation_id);

						if (is_object($variation_obj) && ( !$variation_obj->get_manage_stock() || 'yes' != get_post_meta($variation_id, 'prod_level_inven', true) )) {
							continue;
						}

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



						foreach ($inven_posts as $current_invent_id) {
							if (get_post_meta($current_invent_id, 'in_location', true) == $curren_location_object->term_id) {

								$current_location_total_stock += (float) get_post_meta($current_invent_id, 'in_stock_quantity', true);

							}
						}
					}


				}

				$insert_log = wp_insert_post(
					array(

						'post_type'   => 'af_stock_log',

						'post_status' => 'publish',

						'post_title'  => $curren_location_object->name,
					)
				);

				update_post_meta($insert_log, 'stock_details', $current_location_total_stock);

				update_post_meta($insert_log, 'term_id', $curren_location_object->term_id);



			}
		}



		return;
	}
	public function af_show_option_name( $item_id, $item ) {
		wp_nonce_field('order_detail_nonce', 'order_detail_nonce_field');

		if (!method_exists($item, 'get_product_id')) {
			return;
		}

		$order_id = $item->get_order_id();
		$order    = wc_get_order($order_id);

		$quantity = $item->get_quantity();

		$product_price = $item->get_subtotal();

		$prod_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
		$product = $item->get_product();

		$item_meta = (array) wc_get_order_item_meta($item_id, 'selected_location');

		$location_name = '';

		$inven_posts = get_posts(
			array(
				'post_type'   => 'af_prod_lvl_invent',
				'post_status' => 'publish',
				'numberposts' => -1,
				'post_parent' => $prod_id,
				'fields'      => 'ids',
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
			)
		);

		$prod_obj = wc_get_product($prod_id);

		$loc_id = get_post_meta($prod_id, 'in_location', true);

		$final_inven = array();
		if (is_object($item) && method_exists($item, 'get_product_id')) {

			// $final_inven[$loc_id] = $prod_obj->get_stock_quantity();

			foreach ($inven_posts as $inventry_id) {

				$loc_id = get_post_meta($inventry_id, 'in_location', true);

				$final_inven[ $loc_id ] = get_post_meta($inventry_id, 'in_stock_quantity', true);
			}
		}
		$final_inven = af_mli_custom_array_filter($final_inven);

		$_loc_id = 0;
		if ('yes' != get_option('mli_gen_backend_mode_only')) {

			$location_name = '';

			if (!empty($item_meta) && !empty($item_meta['selected_value'])) {

				$_loc_id = $prod_id == $item_meta['selected_value'] ? get_post_meta($prod_id, 'in_location', true) : $item_meta['selected_value'];

				$term = get_term($_loc_id);


				if (!is_wp_error($term)) {

					?>

					<br><b> <?php echo esc_attr(isset($item_meta['heading']) ? $item_meta['heading'] : 'Location'); ?> : </b><br>

					<?php

					$location_name = !empty($term) ? $term->name : '';
					echo esc_attr($location_name);
				}

			}
			echo '<br>';

		}




		if (count($final_inven) < 1) {
			?>
			<p><?php echo esc_html__('No location/inventory created yet.', 'addify-multi-inventory-management'); ?></p>

			<?php
			return;
		}

		$_loc_id = isset($item_meta['selected_value']) ? $item_meta['selected_value'] : 0;

		$af_mli_inventory_detail = (array) wc_get_order_item_meta($item_id, 'af_mli_inventory_detail', true);

		$af_mli_inventory_detail = af_mli_custom_array_filter($af_mli_inventory_detail);

		if ('yes' == get_option('mli_gen_backend_mode_only') && 'most_inven' == get_option('mli_gen_backend_mode_only_rules')) {

			arsort($final_inven);

		}

		if (count($af_mli_inventory_detail) < 1 && !in_array($order->get_status(), array( 'refund', 'cancelled', 'failed', 'checkout-draft' ))) {

			$term = get_term($_loc_id);

			$location_name = !empty($term) && is_object($term) && isset($term->name) ? $term->name : '';

			$af_i_loc_select = $_loc_id;

			$prod_name = $product->get_name();

			// deduction quantity first time.

			foreach ($inven_posts as $inventory_id) {

				if (get_post_meta($inventory_id, 'in_location', true) == $_loc_id) {

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

					if (isset($final_inven[ $inventory_id ])) {

						$final_inven[ $inventory_id ] = get_post_meta($inventry_id, 'in_stock_quantity', true);

					}
				}

			}

			wc_update_order_item_meta($item_id, 'af_mli_inventory_detail', $final_inven);
		}

		$af_mli_last_qty_detail = (array) wc_get_order_item_meta($item_id, 'af_mli_last_qty_detail', true);

		$af_mli_last_qty_detail = af_mli_custom_array_filter($af_mli_last_qty_detail);

		if (count($af_mli_last_qty_detail) < 1) {
			$af_mli_last_qty_detail['last_deducted_qty'] = $item->get_quantity();
		}
		wc_update_order_item_meta($item_id, 'af_mli_last_qty_detail', $af_mli_last_qty_detail);
		if (!is_admin()) {
			return;
		}
		$current_date = strtotime(gmdate('Y-m-d'));

		?>
		<h3><?php echo esc_html__('Location', 'addify-multi-inventory-management'); ?></h3>
		<select name="af_i_loc_select[<?php echo esc_attr($item_id); ?>]" style="height: 40px;" class="af_i_loc_select">
			<?php
			foreach ($final_inven as $loc_term_id => $stock) {

				$loc_date = get_post_meta($loc_term_id, 'in_date', true);

				$loc_exp_date = get_post_meta($loc_term_id, 'in_expiry_date', true);
				// Check the 'date' index.
				if (!empty($loc_date) && $current_date < strtotime($loc_date)) {
					continue;
				}
				// Check the 'exp_date' index.
				if (!empty($loc_exp_date) && $current_date > strtotime($loc_exp_date)) {
					continue;
				}

				$loc       = get_term($loc_term_id);
				$loc_name  = $loc->name;
				$optn_text = null != $stock && $stock >= 0 ? $loc_name . ' (' . $stock . ')' : $loc_name;

				$addition_text = '';

				if ((int) $stock < $quantity || ( (int) $stock <= 0 )) {
					$addition_text .= ' disabled ';
				}
				?>
				<option <?php echo esc_attr($addition_text); ?> value="<?php echo esc_attr($loc_term_id); ?>"
					data-stock="<?php echo esc_attr($stock); ?>" <?php selected($_loc_id, $loc_term_id); ?>>
					<?php echo esc_attr($optn_text); ?>
				</option>
				<?php
			}
			?>
		</select>

		<p class="out_of_stock_msg_p" style="display: none; color: Red; font-style: italic;">
			<?php echo esc_html__('Out of Stock on this location', 'addify-multi-inventory-management'); ?>
		</p>

		<?php
	}
}
new Addify_Multi_Location_Inventory_Main();
