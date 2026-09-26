<?php
/**
 * Admin General Settings.
 *
 * @package  Addify Product Multi Location Inventory
 * @version  1.0.0
 */

defined('ABSPATH') || exit();

class Af_Mli_Admin_Gen_Settings {




	public function __construct() {

		add_action('admin_menu', array( $this, 'af_multiple_inventory_submenu' ));

		add_action('wp_loaded', array( $this, 'af_mli_manage_stock_update_values' ));

		add_action('all_admin_notices', array( $this, 'af_mli_add_cpt_in_submenu' ));
	}

	public function af_multiple_inventory_submenu() {

		global $pagenow, $typenow, $submenu;

		add_submenu_page(
			'woocommerce',
			esc_html__('Multi Locations', 'addify-multi-inventory-management'),
			esc_html__('Multi Locations', 'addify-multi-inventory-management'),
			'manage_options',
			'af_i_m_l_gen_settings',
			array( $this, 'af_mli_m_l_settings' )
		);
	}

	public function af_mli_m_l_settings() {

		global $active_tab;

		$active_tab = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

		if (!isset($active_tab)) {

			$active_tab = 'Dashboard';

		}



		if ('Dashboard' == $active_tab) {


			$low_stock_product_ids = array();

			$prod_threshold = (int) get_option('af_mli_over_stock_quantity');
			$prod_detail    = filter_input(INPUT_GET, 'prod_detail', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			$af_mli_main_product_detail = (array) get_option('af_mli_main_product_detail', true);

			if (isset($prod_detail)) {


				if ('low_stock' == $prod_detail) {
					$low_stock_product_ids = isset($af_mli_main_product_detail['low_stock_product_ids']) ? (array) $af_mli_main_product_detail['low_stock_product_ids'] : array();
					$tab_title             = 'Low Stock';

					$this->stock_page($tab_title, $low_stock_product_ids);

				} elseif ('overstock' == $prod_detail) {

					$over_stock_product_ids = isset($af_mli_main_product_detail['over_stock_product_ids']) ? (array) $af_mli_main_product_detail['over_stock_product_ids'] : array();

					// Get the products

					$tab_title = 'Over Stock';

					$this->stock_page($tab_title, $low_stock_product_ids);

				} elseif ('outofstock' == $prod_detail) {

					$outstock_product_ids = isset($af_mli_main_product_detail['outstock_product_ids']) ? (array) $af_mli_main_product_detail['outstock_product_ids'] : array();

					$tab_title = 'Out of Stock';

					$this->stock_page($tab_title, $outstock_product_ids);
				}
			} else {

				?>
				<h1 style="margin: 1em 0; color: #1d2327; font-size: 1.3em;">
					<?php echo esc_html__('Dashboard', 'addify-multi-inventory-management'); ?>
				</h1>
				<?php

				include AFMLI_PLUGIN_DIR . 'includes/admin/dashboard/af-mli-dashboard-design.php';

			}

			$this->custom_style();

		} elseif ('Manage_Stock' == $active_tab) {

			$this->custom_style();
			$history = filter_input(INPUT_GET, 'history', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			if (isset($history)) {

				?>
				<h1><?php echo esc_html__('History', 'addify-multi-inventory-management'); ?></h1>
				<?php

				$product_id = filter_input(INPUT_GET, 'history', FILTER_VALIDATE_INT);
				$inven      = filter_input(INPUT_GET, 'inven', FILTER_VALIDATE_INT);

				$this->get_product_history($product_id, $inven);

			} else {

				?>
				<h1 style="margin: 1em 0; color: #1d2327; font-size: 1.3em;">
					<?php echo esc_html__('Manage Stock', 'addify-multi-inventory-management'); ?>
				</h1>
				<?php

				include AFMLI_PLUGIN_DIR . 'includes/admin/manage_stock_tab/af-mli-manage-stock.php';
			}

		} elseif ('Import_Export_CSV' == $active_tab) {

			?>
			<h1 style="margin: 1em 0; color: #1d2327; font-size: 1.3em;">
				<?php echo esc_html__('Import Export CSV', 'addify-multi-inventory-management'); ?>
			</h1>

			<form method="post">

				<?php

				include AFMLI_PLUGIN_DIR . 'includes/admin/inven_csv/af-mli-csv-tab-design.php';

				$this->custom_style();

				?>

			</form>

			<?php
		}
	}

	public function af_mli_add_cpt_in_submenu() {

		global $post, $typenow;

		$screen = get_current_screen();

		if ($screen && in_array($screen->id, $this->get_tab_screen_ids(), true)) {

			$tabs = array(

				'Dashboard'         => array(

					'title' => esc_html__('Dashboard', 'addify-multi-inventory-management'),

					'url'   => admin_url('admin.php?page=af_i_m_l_gen_settings&tab=Dashboard'),
				),

				'mli_location'      => array(

					'title' => esc_html__('Locations', 'addify-multi-inventory-management'),

					'url'   => admin_url('edit-tags.php?taxonomy=mli_location&post_type=product'),
				),

				'Manage_Stock'      => array(

					'title' => esc_html__('Manage Stock', 'addify-multi-inventory-management'),

					'url'   => admin_url('admin.php?page=af_i_m_l_gen_settings&tab=Manage_Stock'),
				),

				'Import_Export_CSV' => array(

					'title' => esc_html__('CSV Import/Export', 'addify-multi-inventory-management'),

					'url'   => admin_url('admin.php?page=af_i_m_l_gen_settings&tab=Import_Export_CSV'),
				),

			);

			if (is_array($tabs)) {

				?>

				<div class="wrap woocommerce">

					<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
						<?php

						$current_tab = $this->get_current_tab();

						foreach ($tabs as $id => $tab_data) {

							$class = $id === $current_tab ? array( 'nav-tab', 'nav-tab-active' ) : array( 'nav-tab' );

							printf('<a href="%1$s" class="%2$s">%3$s</a>', esc_url($tab_data['url']), implode(' ', array_map('sanitize_html_class', $class)), esc_html($tab_data['title']));
						}

						?>

					</h2>

				</div>

				<?php
			}
		}
	}

	public function get_current_tab() {

		$screen = get_current_screen();

		$cstm_page = $screen->id;


		$cstm_page = !empty(filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) ? filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS) : $screen->id;


		switch ($cstm_page) {

			case 'Dashboard':
				return 'Dashboard';

			case 'woocommerce_page_af_i_m_l_gen_settings':
				return 'Dashboard';

			case 'edit-mli_location':
				return 'mli_location';

			case 'Manage_Stock':
				return 'Manage_Stock';

			case 'Import_Export_CSV':
				return 'Import_Export_CSV';
		}
	}

	public function get_tab_screen_ids() {

		$tabs_screens = array(

			'woocommerce_page_af_i_m_l_gen_settings',

			'edit-mli_location',

			'Dashboard',

			'Manage_Stock',

			'Import_Export_CSV',
		);

		return $tabs_screens;
	}

	public function stock_page( $tab_title, $low_stock_product_ids ) {

		include AFMLI_PLUGIN_DIR . 'includes/admin/dashboard/af-mli-stock-page.php';
	}

	public function custom_style() {

		?>

		<style>
			.form-table td,

			.form-table th {

				width: unset;

			}
		</style>

		<?php
	}

	public function get_product_history( $product_id, $inven ) {

		$prod_obj = wc_get_product($product_id);

		$prod_or_inven_id = !empty($inven) ? (int) sanitize_text_field($inven) : $product_id;

		$prod_stock_log = (array) get_post_meta($prod_or_inven_id, 'af_prod_and_inven_stock_log', true);

		?>

		<div class="af_history_table_div_main_div">

			<div class="af_history_table_div">


				<?php

				if (!empty(array_filter($prod_stock_log))) {

					?>

					<table style="width: 100%; text-align: center;">

						<thead style="background-color: #3858e9; color: white;">

							<tr>

								<th> <?php echo esc_html__('Time', 'addify-multi-inventory-management'); ?> </th>

								<th> <?php echo esc_html__('Date ', 'addify-multi-inventory-management'); ?> </th>

								<th> <?php echo esc_html__('User Name', 'addify-multi-inventory-management'); ?> </th>

								<th> <?php echo esc_html__('Old Stock ', 'addify-multi-inventory-management'); ?> </th>

								<th> <?php echo esc_html__('New Stock ', 'addify-multi-inventory-management'); ?> </th>

								<th> <?php echo esc_html__('Location ', 'addify-multi-inventory-management'); ?> </th>

							</tr>

						</thead>

						<tbody>

							<?php

							foreach ($prod_stock_log as $time => $log_data) {

								if (!is_array($log_data)) {

									continue;
								}

								$user_detail = get_user_by('ID', $log_data['user_id']);

								$loc_name = '';

								if (!empty($log_data['location']) && 'default' != $log_data['location']) {

									$loc_name = get_term($log_data['location']) ? get_term($log_data['location'])->name : '';

								} else {

									$loc_name = 'Default';

								}

								?>

								<tr>

									<td> <?php echo esc_attr(gmdate('h:m:s', $time)); ?> </td>

									<td> <?php echo esc_attr($log_data['date']); ?> </td>

									<td> <?php echo esc_attr($user_detail ? $user_detail->display_name : ''); ?> </td>

									<td> <?php echo esc_attr($log_data['old_stock']); ?> </td>

									<td> <?php echo esc_attr($log_data['new_stock']); ?> </td>

									<td> <?php echo esc_attr($loc_name); ?> </td>

								</tr>

								<?php

							}

							?>

						</tbody>

					</table>

					<?php

				} else {

					?>
					<h1> <?php echo esc_html__('No Records Found', 'addify-multi-inventory-management'); ?> </h1>
					<?php
				}

				?>

			</div>

		</div>

		<?php
	}
	public function af_mli_manage_stock_update_values() {

		if (isset($_POST['af_mli_manage_stock_save_table'])) {

			$nonce = isset($_POST['mli_manage_stock_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['mli_manage_stock_nonce_field'])) : '';

			if (!wp_verify_nonce($nonce, 'mli_manage_stock_nonce')) {

				wp_die('Failed security check');
			}

			$products = isset($_POST['prod_ids']) ? sanitize_meta('', wp_unslash($_POST['prod_ids']), '') : array();

			foreach ($products as $prod_id) {

				$prod_obj = wc_get_product($prod_id);

				$i_loc_array = array();

				$ms_prd_price = isset($_POST['ms_prd_price'][ $prod_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_prd_price'][ $prod_id ])) : 0;

				update_post_meta($prod_id, '_regular_price', $ms_prd_price);

				$ms_prd_sale_price = isset($_POST['ms_prd_sale_price'][ $prod_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_prd_sale_price'][ $prod_id ])) : 0;

				update_post_meta($prod_id, '_sale_price', $ms_prd_sale_price);

				if ($prod_obj) {

					$prod_obj->set_regular_price($ms_prd_price);

					$prod_obj->set_sale_price($ms_prd_sale_price);

					update_post_meta($prod_id, '_price', $ms_prd_price);

					if ($ms_prd_sale_price >= 0.1) {

						update_post_meta($prod_id, '_price', $ms_prd_sale_price);

					}


				}

				$ms_prd_manage_stock = isset($_POST['ms_prd_manage_stock'][ $prod_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_prd_manage_stock'][ $prod_id ])) : 'no';

				update_post_meta($prod_id, '_manage_stock', $ms_prd_manage_stock);



				$ms_prd_stock = isset($_POST['ms_prd_stock'][ $prod_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_prd_stock'][ $prod_id ])) : '';

				update_post_meta($prod_id, '_stock', $ms_prd_stock);

				$ms_prd_low_threshold = isset($_POST['ms_prd_low_threshold'][ $prod_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_prd_low_threshold'][ $prod_id ])) : '';

				update_post_meta($prod_id, '_low_stock_amount', $ms_prd_low_threshold);

				$ms_prd_allow_back_orders = isset($_POST['ms_prd_allow_back_orders'][ $prod_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_prd_allow_back_orders'][ $prod_id ])) : '';

				update_post_meta($prod_id, '_backorders', $ms_prd_allow_back_orders);

				$ms_i_location = isset($_POST['ms_i_location'][ $prod_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_i_location'][ $prod_id ])) : '';

				update_post_meta($prod_id, 'in_location', $ms_i_location);

				// $i_loc_array[] = $ms_i_location;

				$ms_i_sold_invidually = isset($_POST['ms_i_sold_invidually'][ $prod_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_i_sold_invidually'][ $prod_id ])) : '';
				update_post_meta($prod_id, '_sold_individually', $ms_i_sold_invidually);


				$prod_inven = get_posts(
					array(

						'post_type'   => 'af_prod_lvl_invent',

						'post_status' => 'publish',

						'numberposts' => -1,

						'fields'      => 'ids',

						'post_parent' => $prod_id,
					)
				);

				$all_old_inventory_sum_quantity = 0;
				$all_inventory_sum_quantity     = 0;
				if (count($prod_inven) >= 1) {
					foreach ($prod_inven as $prod_inven_id) {

						if (empty($prod_inven_id)) {
							continue;
						}

						$inv_old_stock = (float) get_post_meta($prod_inven_id, 'in_stock_quantity', true);

						$ms_i_prod_sku = isset($_POST['ms_i_prod_sku'][ $prod_inven_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_i_prod_sku'][ $prod_inven_id ])) : '';

						update_post_meta($prod_inven_id, 'in_sku', $ms_i_prod_sku);

						$ms_i_price = isset($_POST['ms_i_price'][ $prod_inven_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_i_price'][ $prod_inven_id ])) : 0;

						update_post_meta($prod_inven_id, 'in_price', $ms_i_price);

						$ms_i_sale_price = isset($_POST['ms_i_sale_price'][ $prod_inven_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_i_sale_price'][ $prod_inven_id ])) : 0;

						update_post_meta($prod_inven_id, 'in_sale_price', $ms_i_sale_price);

						$ms_i_stock                  = (float) ( isset($_POST['ms_i_stock'][ $prod_inven_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_i_stock'][ $prod_inven_id ])) : 0 );
						$all_inventory_sum_quantity += $ms_i_stock;

						update_post_meta($prod_inven_id, 'in_stock_quantity', $ms_i_stock);

						$ms_prd_low_threshold = isset($_POST['ms_prd_low_threshold'][ $prod_inven_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_prd_low_threshold'][ $prod_inven_id ])) : '';

						update_post_meta($prod_inven_id, 'in_low_stock_threshold', $ms_prd_low_threshold);

						$ms_i_location = isset($_POST['ms_i_location'][ $prod_inven_id ]) ? sanitize_text_field(wp_unslash($_POST['ms_i_location'][ $prod_inven_id ])) : '';

						update_post_meta($prod_inven_id, 'in_location', $ms_i_location);

						$i_loc_array[] = $ms_i_location;

						$all_old_inventory_sum_quantity += $inv_old_stock;

						$main_prod_stock_log = (array) get_post_meta($prod_inven_id, 'af_prod_and_inven_stock_log', true);

						if ($ms_i_stock != $inv_old_stock) {

							$main_prod_stock_log[ time() ] = array(

								'date'      => gmdate('Y-m-d'),

								'new_stock' => $ms_i_stock,

								'user_id'   => get_current_user_id(),

								'old_stock' => $inv_old_stock,

								'location'  => $ms_i_location,

							);

							update_post_meta($prod_inven_id, 'af_prod_and_inven_stock_log', $main_prod_stock_log);


						}

					}
				} else {
					$all_inventory_sum_quantity = isset($_POST['ms_prd_stock']) && isset($_POST['ms_prd_stock'][ $prod_id ]) && sanitize_text_field(wp_unslash($_POST['ms_prd_stock'][ $prod_id ])) >= 1 ? sanitize_text_field(wp_unslash($_POST['ms_prd_stock'][ $prod_id ])) : 0;

				}


				if ($all_inventory_sum_quantity > 1) {
					$prod_obj->set_stock_status('instock');
				} else {
					$prod_obj->set_stock_status('outofstock');
				}

				if ($prod_obj->get_manage_stock() || 'yes' == $ms_prd_manage_stock) {

					$prod_obj->set_stock_quantity($all_inventory_sum_quantity);

					update_post_meta($prod_obj->get_id(), '_stock', $all_inventory_sum_quantity);
					$prod_obj->save();


				}

				$inv_old_stock = (float) get_post_meta($prod_id, 'in_stock_quantity', true);

				$all_old_inventory_sum_quantity += $inv_old_stock;

				$main_prod_stock_log = (array) get_post_meta($prod_id, 'af_prod_and_inven_stock_log', true);

				if ($all_inventory_sum_quantity != $inv_old_stock) {

					$main_prod_stock_log[ time() ] = array(
						'date'      => gmdate('Y-m-d'),
						'new_stock' => $all_inventory_sum_quantity,
						'user_id'   => get_current_user_id(),
						'old_stock' => $inv_old_stock,
						'location'  => 'default',
					);

					update_post_meta($prod_id, 'af_prod_and_inven_stock_log', $main_prod_stock_log);

					update_post_meta($prod_id, 'in_stock_quantity', $all_inventory_sum_quantity);
				}

				af_mli_update_product_location_taxonomy($prod_obj);

				wp_set_post_terms($prod_id, (array) $i_loc_array, 'mli_location');
				$prod_obj->save();

			}
		}
	}
}

new Af_Mli_Admin_Gen_Settings();