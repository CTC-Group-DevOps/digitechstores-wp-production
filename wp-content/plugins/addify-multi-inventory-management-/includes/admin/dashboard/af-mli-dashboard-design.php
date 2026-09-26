<?php
defined('ABSPATH') || exit();


$final_detil               = (array) get_option('af_mli_product_and_locations_get_stock_detail');
$converted_readable_number = isset($final_detil['converted_readable_number']) ? $final_detil['converted_readable_number'] : '';
$prod_total_stock_q        = isset($final_detil['prod_total_stock_q']) ? $final_detil['prod_total_stock_q'] : '';
$out_stock                 = isset($final_detil['out_stock']) ? $final_detil['out_stock'] : '';
$out_of_stocks_product     = isset($final_detil['out_of_stocks_product']) ? (array) $final_detil['out_of_stocks_product'] : array();
$over_stock_prod           = isset($final_detil['over_stock_prod']) ? (array) $final_detil['over_stock_prod'] : array();
$low_stock_prod            = isset($final_detil['low_stock_prod']) ? (array) $final_detil['low_stock_prod'] : array();


?>

<div class="main_value_div">

	<div class="af_dash_fields_divs">

		<div class="wrapper">

			<div class="value_class current_value_div">

				<p class="digit_title button-primary">

					<?php echo esc_html__('Current Value ', 'addify-multi-inventory-management'); ?>

					<i class="fa fa-question-circle item_current_value">

						<span class="tooltiptext">

							<?php echo esc_html__('Current value of stock based on manage stock products (qty * price)', 'addify-multi-inventory-management'); ?>

						</span>

					</i>

				</p>

				<h3 style="color: black;"><?php echo esc_attr($converted_readable_number); ?></h3>

			</div>

			<div class="value_class total_stock_div">

				<p class="digit_title button-primary">

					<?php echo esc_html__('Total Stock ', 'addify-multi-inventory-management'); ?>

					<i class="fa fa-question-circle items_in_stock">

						<span class="tooltiptext items_in_stock_tooltip">

							<?php echo esc_html__('Total stock of products', 'addify-multi-inventory-management'); ?>

						</span>

					</i>

				</p>

				<h3 style="color: black;"><?php echo esc_attr($prod_total_stock_q); ?></h3>

			</div>

			<div class="value_class out_of_stock_div">

				<p class="digit_title button-primary">

					<?php echo esc_html__('Out Of Stock Products ', 'addify-multi-inventory-management'); ?>

					<i class="fa fa-question-circle items_out_stock">

						<span class="tooltiptext">

							<?php echo esc_html__('Products which are out of stock', 'addify-multi-inventory-management'); ?>

						</span>

					</i>

				</p>

				<h3 style="color: black;"><?php echo esc_attr($out_stock); ?></h3>

			</div>

		</div>

	</div>

	<form class="af_dash_form">

		<div style="margin-top: 25px !important;" class="af_dash_graph_fields_divs">

			<?php

			wp_nonce_field('_addify_mli_nonce', 'nonce');

			$last_month_date = gmdate('Y-m-d', strtotime('-30 days'));

			$today_date = gmdate('Y-m-d');

			?>

			<div class="wrapper" style="display: flex; margin-top: 10px; align-items: end;">

				<div class="af_filter_date_div">

					<p><?php echo esc_html__('From', 'addify-multi-inventory-management'); ?></p>

					<input type="date" name="af_dash_from_date" class="af_dash_filter_date_from_input" required
						value="<?php echo esc_attr($last_month_date); ?>">
				</div>

				<div class="af_filter_date_div">

					<p><?php echo esc_html__('To', 'addify-multi-inventory-management'); ?></p>

					<input type="date" name="af_dash_to_date" class="af_dash_filter_date_to_input"
						value="<?php echo esc_attr($today_date); ?>">

				</div>

				<div class="af_filter_date_div">

					<p><?php echo esc_html__('Warehouse', 'addify-multi-inventory-management'); ?></p>

					<select name="af_dash_to_ware_house[]" class="af_dash_filter_warehouse" multiple>

						<option value="all">

							<?php echo esc_html__('All Locations', 'addify-multi-inventory-management'); ?>

						</option>

						<?php

						$inventory_locations = get_terms(
							array(
								'number'     => 0,
								'hide_empty' => false,
								'taxonomy'   => 'mli_location',
							)
						);

						foreach ($inventory_locations as $inventory_location) {

							?>

							<option value="<?php echo esc_attr($inventory_location->term_id); ?>">

								<?php echo esc_attr($inventory_location->name); ?>

							</option>

							<?php
						}

						?>

					</select>

				</div>

				<div style="text-align: left;" class="af_filter_date_div">

					<button name="af_filter_date_graph"
						class="af_filter_date_graph button-primary"><?php echo esc_html__('Filter', 'addify-multi-inventory-management'); ?></button>

					<button name="af_refresh_date_graph"
						class="af_refresh_date_graph button-primary"><?php echo esc_html__('Refresh', 'addify-multi-inventory-management'); ?></button>

				</div>

			</div>

			<div id="addify_i_curve_chart"></div>

		</div>

	</form>

	<div class="af_dash_fields_divs">

		<div class="wrapper">

			<?php $admin_url = admin_url('admin.php?page=af_i_m_l_gen_settings'); ?>

			<div class="value_class">

				<p class="digit_title button-primary">
					<?php echo esc_html__('Low Stock', 'addify-multi-inventory-management'); ?>
				</p>

				<?php if (!empty($low_stock_prod)) : ?>

					<table style="width: 90%; margin-left: 30px; margin-top: 15px;">

						<thead>

							<th style="float: left;"><?php echo esc_html('Product Name'); ?></th>

							<th><?php echo esc_html('Stock'); ?></th>

						</thead>

						<tbody>

							<?php

							foreach (array_slice($low_stock_prod, 0, 5) as $key => $low_stock) {

								$low_stock = explode('---', $low_stock);

								$stock = current($low_stock);

								$name = end($low_stock);

								?>
								<tr>

									<td style="float: left;"><?php echo esc_attr($name); ?></td>

									<td><?php echo esc_attr($stock); ?></td>

								</tr>
								<?php

								if (4 == $key) {
									break;
								}
							}

							?>
						</tbody>

					</table>

				<?php endif ?>

				<a class="af_dash_view_all_btns button-primary"
					href="<?php echo esc_url(admin_url('admin.php?page=af_i_m_l_gen_settings&tab=Manage_Stock&af_pro_status=low_stock')); ?>">
					<?php echo esc_html__('View More', 'addify-multi-inventory-management'); ?>
				</a>

			</div>

			<div class="value_class">

				<p class="digit_title button-primary">
					<?php echo esc_html__('Over Stock (Products)', 'addify-multi-inventory-management'); ?>
				</p>

				<?php if (!empty($over_stock_prod)) : ?>

					<table style="width: 90%; margin-left: 30px; margin-top: 15px;">

						<thead>

							<th style="float: left;"><?php echo esc_html('Product Name'); ?></th>

							<th><?php echo esc_html('Stock'); ?></th>

						</thead>

						<tbody>

							<?php

							foreach (array_slice($over_stock_prod, 0, 5) as $key => $over_stock) {

								$over_stock = explode('---', $over_stock);

								$stock = current($over_stock);

								$name = end($over_stock);

								?>
								<tr>

									<td style="float: left;"><?php echo esc_attr($name); ?></td>

									<td><?php echo esc_attr($stock); ?></td>

								</tr>
								<?php

								if (4 == $key) {
									break;
								}
							}

							?>
						</tbody>

					</table>

				<?php endif ?>

				<a class="af_dash_view_all_btns button-primary"
					href="<?php echo esc_url(admin_url('admin.php?page=af_i_m_l_gen_settings&tab=Manage_Stock&af_pro_status=overstock')); ?>">

					<?php echo esc_html__('View More', 'addify-multi-inventory-management'); ?>

				</a>

			</div>


			<div class="value_class" id="af_animate_out_of_stock">

				<p class="digit_title button-primary">
					<?php echo esc_html__('Out Of Stock (Products)', 'addify-multi-inventory-management'); ?>
				</p>

				<?php if ($out_of_stocks_product) : ?>

					<table style="width: 90%; margin-left: 30px; margin-top: 15px;">

						<thead>

							<th style="float: left;"><?php echo esc_html('Product Name'); ?></th>

							<th><?php echo esc_html('Stock'); ?></th>

						</thead>

						<tbody>

							<?php

							foreach (array_slice($out_of_stocks_product, 0, 5) as $key => $out_of_stock) {

								$out_of_stock = explode('---', $out_of_stock);

								$stock = current($out_of_stock);

								$name = end($out_of_stock);

								?>
								<tr>

									<td style="float: left;"><?php echo esc_attr($name); ?></td>

									<td><?php echo esc_attr($stock); ?></td>

								</tr>
								<?php

								if (4 == $key) {
									break;
								}
							}

							?>
						</tbody>

					</table>

				<?php endif ?>
				<a class="af_dash_view_all_btns button-primary"
					href="<?php echo esc_url(admin_url('admin.php?page=af_i_m_l_gen_settings&tab=Manage_Stock&af_pro_status=outofstock')); ?>">

					<?php echo esc_html__('View More', 'addify-multi-inventory-management'); ?>

				</a>

			</div>

		</div>

	</div>

</div>