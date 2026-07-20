<?php
/**
 * Product level inventories.
 *
 * @package  Addify Product Multi Location Inventory
 * @version  1.0.0
 */

defined('ABSPATH') || exit;

class Addify_Mli_Product {



	public function __construct() {

		add_action('woocommerce_product_options_inventory_product_data', array( $this, 'af_mli_inventory_create' ), 9, 3);

		add_action('save_post_product', array( $this, 'inven_prod_value_save' ));
	}

	public function af_mli_inventory_create() {

		$current_prod_id = get_the_ID();

		$prod_detail = wc_get_product($current_prod_id);

		$p_reg_price = !empty($prod_detail->get_regular_price()) ? $prod_detail->get_regular_price() : '';

		$p_sale_price = !empty($prod_detail->get_sale_price()) ? $prod_detail->get_sale_price() : '';

		$inven_location = get_post_meta($current_prod_id, 'in_location', true);

		if ('variable' == $prod_detail->get_type()) {

			return;
		}

		$total_stock = $prod_detail->get_stock_quantity();

		$prod_inven = get_posts(
			array(
				'post_type'   => 'af_prod_lvl_invent',

				'post_status' => 'publish',

				'numberposts' => -1,

				'fields'      => 'ids',

				'orderby'     => 'menu_order',

				'order'       => 'ASC',

				'post_parent' => $current_prod_id,
			)
		);

		foreach ($prod_inven as $inven) {

			$total_stock = $total_stock + (int) get_post_meta($inven, 'in_stock_quantity', true);
		}

		wp_nonce_field('af_mli_prod_level_nonce', 'af_mli_prod_level_nonce_field');

		?>

		<p class="form-field prod_level_inven_checkbox_class">

			<label> <?php echo esc_html('Use Multi Inventory?'); ?> </label>

			<input type="checkbox" name="prod_level_inven" value="yes" class="prod_level_inven" 
			<?php
			if ('yes' == get_post_meta($current_prod_id, 'prod_level_inven', true)) :
				?>
				checked <?php endif ?>>

			<span
				class="description"><?php echo esc_html('Check if you want to add multi-location inventory for this product'); ?>
			</span>

		</p>

		<div class="prod_level_inven_divs">

			<div style="width: 100%;">

				<p>

					<span><b><?php echo esc_html('Note: '); ?></b>
						<?php echo esc_html('Please use one Location for one inventory. Do not use same location on multiple inventories on this product.'); ?></span>

				</p>

			</div>

			<p class="form-field" style="display:none;">

				<label for="in_date"><?php echo esc_html__('Total Stock', 'addify-multi-inventory-management'); ?></label>

				<input type="number" name="total_inven" value="<?php echo esc_attr($total_stock); ?>" style="width: 50%;"
					readonly>

				<?php echo wp_kses(wc_help_tip('This is the total stock of product including all inventories created. This field is only readable'), wp_kses_allowed_html('post')); ?>

			</p>

			<p class="form-field" style="display:none;">

				<strong><label
						for="in_date"><?php echo esc_html__('Default Stock', 'addify-multi-inventory-management'); ?></label></strong>

			</p>

			<p class="form-field" style="display:none;">

				<label for="in_date"><?php echo esc_html__('Quantity', 'addify-multi-inventory-management'); ?></label>

				<input type="number" name="total_inven" value="<?php echo esc_attr($prod_detail->get_stock_quantity()); ?>"
					style="width: 50%;" readonly>

				<?php echo wp_kses(wc_help_tip('Total Stock of main quantity'), wp_kses_allowed_html('post')); ?>

			</p>

			<p class="form-field">

				<label for="in_date"><?php echo esc_html__('Date', 'addify-multi-inventory-management'); ?></label>

				<input type="date" name="in_date[<?php echo esc_attr($current_prod_id); ?>]"
					value="<?php echo esc_attr(get_post_meta($current_prod_id, 'in_date', true)); ?>" style="width: 50%;">

				<?php echo wp_kses(wc_help_tip('Set date of this Inventory'), wp_kses_allowed_html('post')); ?>

			</p>

			<p class="form-field">

				<label for="in_exp_date"><?php echo esc_html__('Expiry Date', 'addify-multi-inventory-management'); ?></label>

				<input type="date" name="in_exp_date[<?php echo esc_attr($current_prod_id); ?>]"
					value="<?php echo esc_attr(get_post_meta($current_prod_id, 'in_exp_date', true)); ?>" style="width: 50%;">

				<?php echo wp_kses(wc_help_tip('Set expiry date of this Inventory'), wp_kses_allowed_html('post')); ?>

			</p>

			<p class="form-field">

				<label for="in_price"><?php echo esc_html__('Price', 'addify-multi-inventory-management'); ?></label>

				<input type="number" name="in_price" style="width: 50%" value="<?php echo esc_attr($p_reg_price); ?>" readonly>

				<?php echo wp_kses(wc_help_tip('Shows the price of this Inventory'), wp_kses_allowed_html('post')); ?>

			</p>

			<p class="form-field">

				<label for="in_sale_price"><?php echo esc_html__('Sale Price', 'addify-multi-inventory-management'); ?></label>

				<input type="number" name="in_sale_price" style="width: 50%" value="<?php echo esc_attr($p_sale_price); ?>"
					readonly>

				<?php echo wp_kses(wc_help_tip('Shows the sale price of this Inventory'), wp_kses_allowed_html('post')); ?>

			</p>

			<p class="form-field" style="display:none;">

				<?php

				$inventory_locations = get_terms(array(
					'number'     => 0,
					'hide_empty' => false,
					'taxonomy'   => 'mli_location',
				));

				if (empty(get_post_meta($current_prod_id, 'in_location', true))) {

					update_post_meta($current_prod_id, 'in_location', get_option('mli_gen_default_location'));
				}

				$inven_location = get_post_meta($current_prod_id, 'in_location', true);

				?>

				<label for="in_location"><?php echo esc_html__('Location', 'addify-multi-inventory-management'); ?></label>

				<select name="in_location[<?php echo esc_attr($current_prod_id); ?>]" style="width: 50%;">

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

			<div id="myModal" class="new_inventory_modal" style="display: none;">

				<div class="af_model_content">

					<span class="af_model_close"><i class="fa fa-times" aria-hidden="true"></i></span>

					<br>

					<h3 class="af_title_message">
						<em><?php echo esc_html__('Enter Name of New Inventory', 'addify-multi-inventory-management'); ?></em>
					</h3>

					<p> <input type="text" name="af_new_invent_name" class="af_new_invent_name" placeholder="Inventory name">
					</p>

					<span>

						<input type="button" name="af_add_call_ajax" class="af_add_call_ajax button-primary"
							value="<?php echo esc_html__('Add', 'addify-multi-inventory-management'); ?>"
							data-current_rule_id="<?php echo esc_attr($current_prod_id); ?>" data-prod_type="simple">

						<input type="button" name="af_add_cancel" class="af_add_cancel button-primary"
							value="<?php echo esc_html__('Cancel', 'addify-multi-inventory-management'); ?>">

					</span>

				</div>

			</div>

			<br>

			<div style="width: 100%;">

				<div class="af_mli_expand_close_btn_div">

					<input type="submit" name="af_mli_expand_all_btn" class="af_mli_expand_all_btn"
						value="<?php echo esc_html__('Expand all', 'addify-multi-inventory-management'); ?>">

					<span><?php echo esc_html__('/', 'addify-multi-inventory-management'); ?></span>

					<input type="submit" name="af_mli_close_all_btn" class="af_mli_close_all_btn"
						value="<?php echo esc_html__('Close all', 'addify-multi-inventory-management'); ?>">

				</div>

			</div>

			<br>

			<div class="af_mli_reload_inventory_div">

				<?php

				$prod_inven = get_posts(
					array(

						'post_type'   => 'af_prod_lvl_invent',

						'post_status' => 'publish',

						'numberposts' => -1,

						'fields'      => 'ids',

						'orderby'     => 'menu_order',

						'order'       => 'ASC',

						'post_parent' => $current_prod_id,
					)
				);

				foreach ($prod_inven as $prod_inven_id) {

					if (empty($prod_inven_id)) {

						continue;
					}

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
									value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_sku', true)); ?>">
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

								<?php echo wp_kses(wc_help_tip('Check if you want to add seperate price for each inventory'), wp_kses_allowed_html('post')); ?>
							</p>

							<p
								class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_inven_price_p af_mli_prd_lvl_inven_price_p">

								<label><?php echo esc_html__('Inventory Price', 'addify-multi-inventory-management'); ?></label>

								<input type="number" min="0" name="in_price[<?php echo esc_attr($prod_inven_id); ?>]"
									style="width: 50%"
									value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_price', true)); ?>" min="0">

								<?php echo wp_kses(wc_help_tip('Set price of this Inventory'), wp_kses_allowed_html('post')); ?>

							</p>

							<p
								class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_inven_sale_price_p af_mli_prd_lvl_inven_sale_price_p">

								<label><?php echo esc_html__('Inventory Sale Price', 'addify-multi-inventory-management'); ?></label>

								<input type="number" min="0" name="in_sale_price[<?php echo esc_attr($prod_inven_id); ?>]"
									style="width: 50%"
									value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_sale_price', true)); ?>" min="0">

								<?php echo wp_kses(wc_help_tip('Set sale price of this Inventory'), wp_kses_allowed_html('post')); ?>

							</p>

							<p
								class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_stock_quantity_p af_mli_prd_lvl_stock_quantity_p">

								<label><?php echo esc_html__('Stock Quantity', 'addify-multi-inventory-management'); ?></label>

								<input type="number" name="in_stock_quantity[<?php echo esc_attr($prod_inven_id); ?>]"
									style="width: 50%"
									value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_stock_quantity', true)); ?>"
									min="0">

								<?php echo wp_kses(wc_help_tip('Stock quantity of this inventory'), wp_kses_allowed_html('post')); ?>

							</p>

							<p
								class="form-field <?php echo esc_attr($prod_inven_id); ?>af_mli_prd_lvl_low_stock_threshold_p af_mli_prd_lvl_low_stock_threshold_p">

								<label><?php echo esc_html__('Low Stock Threshold', 'addify-multi-inventory-management'); ?></label>

								<input type="number" name="in_low_stock_threshold[<?php echo esc_attr($prod_inven_id); ?>]"
									style="width: 50%"
									value="<?php echo esc_attr(get_post_meta($prod_inven_id, 'in_low_stock_threshold', true)); ?>"
									min="0">

								<?php echo wp_kses(wc_help_tip('Low Stock Threshold at Inventory Level'), wp_kses_allowed_html('post')); ?>

							</p>

						</div>

					</div>

					<?php
				}

				?>

			</div>

			<div style="width: 100%; margin: 10px;">

				<span>

					<input type="button" name="af_mli_prd_lvl_add_new_inventory"
						class="af_mli_prd_lvl_add_new_inventory button-primary" value="Add New Inventory">

				</span>

			</div>

		</div>

		<?php
	}

	public function inven_prod_value_save() {

		global $post, $current_screen;

		$current_prod_id = get_the_ID();

		$i_loc_array = array();

		if ('auto-draft' == get_post_status($current_prod_id) || 'trash' == get_post_status($current_prod_id)) {

			return;

		}

		if ($post && 'product' !== $post->post_type && $current_screen && 'post' !== $current_screen->base && 'product' !== $current_screen->id) {

			return;

		}
		global $wpdb;

		$prod_obj = wc_get_product($current_prod_id);

		if (!$prod_obj) {

			return;
		}
		$prod_new_stock = $prod_obj->get_stock_quantity();

		if ('variable' != $prod_obj->get_type()) {

			$nonce = isset($_POST['af_mli_prod_level_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['af_mli_prod_level_nonce_field'])) : '';

			if (!wp_verify_nonce($nonce, 'af_mli_prod_level_nonce')) {

				wp_die('Failed Security Check');
			}

			$prod_level_inven = isset($_POST['prod_level_inven']) ? sanitize_text_field(wp_unslash($_POST['prod_level_inven'])) : 'no';
			update_post_meta($current_prod_id, 'prod_level_inven', $prod_level_inven);

			$in_date = isset($_POST['in_date'][ $current_prod_id ]) ? sanitize_text_field(wp_unslash($_POST['in_date'][ $current_prod_id ])) : '';

			update_post_meta($current_prod_id, 'in_date', $in_date);

			$in_exp_date = isset($_POST['in_exp_date'][ $current_prod_id ]) ? sanitize_text_field(wp_unslash($_POST['in_exp_date'][ $current_prod_id ])) : '';

			update_post_meta($current_prod_id, 'in_exp_date', $in_exp_date);


			$in_location = isset($_POST['in_location'][ $current_prod_id ]) ? sanitize_text_field(wp_unslash($_POST['in_location'][ $current_prod_id ])) : '';

			// update_post_meta($current_prod_id, 'in_location', $in_location);

			$i_loc_array[] = $in_location;

			$inven_fields = get_posts(
				array(

					'post_type'   => 'af_prod_lvl_invent',

					'post_status' => 'publish',

					'numberposts' => -1,

					'fields'      => 'ids',

					'post_parent' => $current_prod_id,
				)
			);

			$prod_new_stock = $prod_obj->get_stock_quantity();

			$sum_of_inventory_stock = 0;

			foreach ($inven_fields as $inven_id) {

				$af_new_invent_name = isset($_POST['af_new_invent_name'][ $inven_id ]) ? sanitize_text_field(wp_unslash($_POST['af_new_invent_name'][ $inven_id ])) : '';

				update_post_meta($inven_id, 'af_new_invent_name', $af_new_invent_name);

				$in_location = isset($_POST['in_location'][ $inven_id ]) ? sanitize_text_field(wp_unslash($_POST['in_location'][ $inven_id ])) : '';

				update_post_meta($inven_id, 'in_location', $in_location);

				$i_loc_array[] = $in_location;

				$in_field_priority = isset($_POST['in_field_priority'][ $inven_id ]) ? sanitize_text_field(wp_unslash($_POST['in_field_priority'][ $inven_id ])) : '';

				update_post_meta($inven_id, 'in_field_priority', $in_field_priority);

				wp_update_post( array(
					'ID'         => $inven_id,
					'menu_order' => $in_field_priority,
				) );
				$in_sku = isset($_POST['in_sku'][ $inven_id ]) ? sanitize_text_field(wp_unslash($_POST['in_sku'][ $inven_id ])) : '';

				update_post_meta($inven_id, 'in_sku', $in_sku);

				$in_date = isset($_POST['in_date'][ $inven_id ]) ? sanitize_text_field(wp_unslash($_POST['in_date'][ $inven_id ])) : '';

				update_post_meta($inven_id, 'in_date', $in_date);

				$in_expiry_date = isset($_POST['in_expiry_date'][ $inven_id ]) ? sanitize_text_field(wp_unslash($_POST['in_expiry_date'][ $inven_id ])) : '';

				update_post_meta($inven_id, 'in_expiry_date', $in_expiry_date);

				$in_add_price = isset($_POST['in_add_price'][ $inven_id ]) ? sanitize_text_field(wp_unslash($_POST['in_add_price'][ $inven_id ])) : '';

				update_post_meta($inven_id, 'in_add_price', $in_add_price);

				$in_price = isset($_POST['in_price'][ $inven_id ]) ? sanitize_text_field(wp_unslash($_POST['in_price'][ $inven_id ])) : '';

				update_post_meta($inven_id, 'in_price', $in_price);

				$in_sale_price = isset($_POST['in_sale_price'][ $inven_id ]) ? sanitize_text_field(wp_unslash($_POST['in_sale_price'][ $inven_id ])) : '';

				update_post_meta($inven_id, 'in_sale_price', $in_sale_price);

				$in_stock_quantity = (float) ( isset($_POST['in_stock_quantity'][ $inven_id ]) && sanitize_text_field(wp_unslash($_POST['in_stock_quantity'][ $inven_id ])) >= 1 ? sanitize_text_field(wp_unslash($_POST['in_stock_quantity'][ $inven_id ])) : 0 );

				$prod_old_stock = get_post_meta($inven_id, 'in_stock_quantity', true);

				$main_prod_stock_log = (array) get_post_meta($inven_id, 'af_prod_and_inven_stock_log', true);

				$sum_of_inventory_stock += $in_stock_quantity;

				if ($prod_old_stock != $in_stock_quantity) {

					$main_prod_stock_log[ time() ] = array(

						'date'      => gmdate('Y-m-d'),

						'new_stock' => $in_stock_quantity,

						'user_id'   => get_current_user_id(),

						'old_stock' => $prod_old_stock,

						'location'  => $in_location,

					);
				}
				update_post_meta($inven_id, 'af_prod_and_inven_stock_log', $main_prod_stock_log);

				update_post_meta($inven_id, 'in_stock_quantity', $in_stock_quantity);

				$in_low_stock_threshold = isset($_POST['in_low_stock_threshold'][ $inven_id ]) ? sanitize_text_field(wp_unslash($_POST['in_low_stock_threshold'][ $inven_id ])) : '';

				update_post_meta($inven_id, 'in_low_stock_threshold', $in_low_stock_threshold);
			}


			update_post_meta($prod_obj->get_id(), 'in_stock_quantity', $sum_of_inventory_stock);

			if ($prod_obj->get_manage_stock()) {

				$prod_new_stock = $sum_of_inventory_stock;
				update_post_meta($prod_obj->get_id(), '_stock', $sum_of_inventory_stock);
				$prod_obj->set_stock_quantity($sum_of_inventory_stock);

			}

			$all_product_ids = array( $prod_obj->get_id() );

		} else {

			$main_product    = $prod_obj;
			$all_product_ids = $main_product->get_children();

		}

		$all_vartion_attach_taxonomy = array();


		$prod_old_stock = (float) get_post_meta($current_prod_id, '_old_stock_quantity', true);

		$main_prod_stock_log = (array) get_post_meta($current_prod_id, 'af_prod_and_inven_stock_log', true);

		if ($prod_old_stock != $prod_new_stock) {

			$main_prod_stock_log[ time() ] = array(

				'date'      => gmdate('Y-m-d'),

				'new_stock' => $prod_new_stock,

				'user_id'   => get_current_user_id(),

				'old_stock' => $prod_old_stock,

				'location'  => 'default',
			);
			update_post_meta($current_prod_id, '_old_stock_quantity', $prod_new_stock);

			update_post_meta($current_prod_id, 'af_prod_and_inven_stock_log', $main_prod_stock_log);

		}



		foreach ($all_product_ids as $variation_id) {

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



			foreach ($inven_posts as $current_invent_id) {
				$all_vartion_attach_taxonomy[] = get_post_meta($current_invent_id, 'in_location', true);
			}
		}



		wp_set_post_terms($current_prod_id, (array) $all_vartion_attach_taxonomy, 'mli_location');
		$prod_obj->save();
	}
}

new Addify_Mli_Product();