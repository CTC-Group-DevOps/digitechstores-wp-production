<?php

defined('ABSPATH') || exit();

$af_mli_manage_stock_tab = (array) get_option('af_mli_ms_checkbox');

$all_af_mli_ms_checkbox = array(

	'prd_sku'                   => 'SKU',
	'prd_name'                  => 'Name',
	'prd_thumbnail'             => 'Thumbnail',
	'prd_tax_status'            => 'Tax Status',
	'prd_tax_class'             => 'Tax Class',
	'prd_shipping_class'        => 'Shipping class',
	'prd_price'                 => 'Price',
	'prd_sale_price'            => 'Sale Price',
	'prd_weight'                => 'Weight',
	'inven_name'                => 'Inventory Name',
	'inven_manage_stock'        => 'Manage Stock',
	'inven_stock_quan'          => 'Stock Quantity',
	'inven_low_stock_threshold' => 'Low Stock Threshold',
	'inven_allow_back_order'    => 'backorder',
	'inven_stock_status'        => 'Stock Status',
	'inven_sold_individually'   => 'Sold individually',
	'inven_location'            => 'Location',
	'stock_update_history'      => 'History',

);
$name_search = filter_input(INPUT_GET, 'af_mli_pro_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$name_search = ( null !== $name_search ) ? $name_search : '';

$min_stock_quan = filter_input(INPUT_GET, 'af_low_quatity_range', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$min_stock_quan = ( null !== $min_stock_quan ) ? $min_stock_quan : '';

$max_stock_quan = filter_input(INPUT_GET, 'af_high_quatity_range', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$max_stock_quan = ( null !== $max_stock_quan ) ? $max_stock_quan : '';

$prod_cat = filter_input(INPUT_GET, 'af_selected_cat', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$prod_cat = ( null !== $prod_cat ) ? $prod_cat : '';

$prod_type = filter_input(INPUT_GET, 'af_mli_pro_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$prod_type = ( null !== $prod_type ) ? $prod_type : '';

$prod_stock_status = filter_input(INPUT_GET, 'af_pro_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$prod_stock_status = ( null !== $prod_stock_status ) ? $prod_stock_status : 'all';

$prod_manage = filter_input(INPUT_GET, 'af_pro_managment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$prod_manage = ( null !== $prod_manage ) ? $prod_manage : '';

$inven_location = filter_input(INPUT_GET, 'af_pro_location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$inven_location = ( null !== $inven_location ) ? $inven_location : '';

?>

<div class="af_mli_main_div">


	<div class="af_mli_inventory_texts">

		<div id="screen-meta" class="metabox-prefs" style="display: block;">

			<div id="screen-options-wrap" class="hidden" tabindex="-1" aria-label="Screen Options Tab"
				style="display: block;">

				<fieldset class="metabox-prefs">

					<?php foreach ($all_af_mli_ms_checkbox as $value => $label) : ?>

						<label>

							<input type="checkbox" name="af_mli_ms_checkbox[]"
								class="<?php echo esc_attr($value); ?> af_mli_ms_checkbox"
								value="<?php echo esc_attr($value); ?>" 
													<?php
													if (isset($af_mli_manage_stock_tab) && in_array($value, $af_mli_manage_stock_tab)) {
														echo 'checked';
													}
													?>
									><?php echo esc_attr($label); ?>

						</label>

					<?php endforeach ?>

				</fieldset>

			</div>

		</div>

		<form method="GET" class="af_ms_all__filter_form">

			<div style="padding: 8px; box-sizing:border-box; width: 100%; padding-left: 0;">

				<div class="disply_inline">

					<label><?php echo esc_html__('Filter By Stock QTY', 'addify-multi-inventory-management'); ?></label>

					<br>

					<input type="number" placeholder="from i.e. 30" class="af_low_quatity_range"
						name="af_low_quatity_range" min="0" value="<?php echo esc_attr($min_stock_quan); ?>">

					<input type="number" placeholder="to i.e. 100" class="af_high_quatity_range"
						name="af_high_quatity_range" min="0" value="<?php echo esc_attr($max_stock_quan); ?>">

					<br>


				</div>

				<div class="disply_inline af_mli_product_search">


					<input type="search" class="mli_input_size af_mli_pro_name" id="af_mli_pro_name"
						name="af_mli_pro_name" value="<?php echo esc_attr($name_search); ?>">

					<input type="submit" class="button af_mli_prod_search_btn" name="af_mli_prod_search_btn"
						value="<?php echo esc_html__('Search Products', 'addify-multi-inventory-management'); ?>">
				</div>


			</div>


			<div class='af_mli_filters' style="width: 100%; float:left">

				<?php $all_categories = get_categories(array( 'taxonomy' => 'product_cat' )); ?>

				<select id='af_cat_filter' class="af_mli_selected_cat" name="af_selected_cat">

					<option value="all"><?php echo esc_html__('All Categories', 'addify-multi-inventory-management'); ?>
					</option>

					<?php

					foreach ($all_categories as $category) {

						?>

						<option value="<?php echo esc_attr($category->slug); ?>" 
													<?php
													if (!empty($prod_cat) && $prod_cat == $category->slug) :
														?>
								selected <?php endif ?>>

							<?php echo esc_attr($category->name); ?>

						</option>

						<?php

					}

					?>

				</select>

				<select name="af_mli_pro_type" class="af_mli_product_type">

					<option value="all" 
					<?php
					if (!empty($prod_type) && 'all' == $prod_type) :
						?>
						selected <?php endif ?>>
						<?php echo esc_html__('All Products', 'addify-multi-inventory-management'); ?>
					</option>

					<option value="simple" 
					<?php
					if (!empty($prod_type) && 'simple' == $prod_type) :
						?>
						selected <?php endif ?>>
						<?php echo esc_html__('Simple product', 'addify-multi-inventory-management'); ?>
					</option>

					<option value="grouped" 
					<?php
					if (!empty($prod_type) && 'grouped' == $prod_type) :
						?>
						selected <?php endif ?>>
						<?php echo esc_html__('Grouped product', 'addify-multi-inventory-management'); ?>
					</option>

					<option value="external" 
					<?php
					if (!empty($prod_type) && 'external' == $prod_type) :
						?>
						selected <?php endif ?>>
						<?php echo esc_html__('External/Affiliate product', 'addify-multi-inventory-management'); ?>
					</option>

					<option value="variable" 
					<?php
					if (!empty($prod_type) && 'variable' == $prod_type) :
						?>
						selected <?php endif ?>>
						<?php echo esc_html__('Variable product', 'addify-multi-inventory-management'); ?>
					</option>

				</select>
				<?php
				$all_status_aray = array(
					'all'         => 'All Stock',
					'instock'     => 'In Stock',
					'outofstock'  => 'Out of Stock',
					'onbackorder' => 'On backorder',
					'low_stock'   => 'Low Stock',
					'overstock'   => 'Over Stock',
				);
				?>
				<select name="af_pro_status" class="af_mli_stock_status">

					<?php foreach ($all_status_aray as $status_key => $status_value) { ?>
						<option value="<?php echo esc_attr($status_key); ?>" <?php selected($status_key, $prod_stock_status); ?>>
							<?php echo esc_attr($status_value); ?>
						</option>
					<?php } ?>
				</select>

				<select name="af_pro_managment" class="af_mli_stock_managed">

					<option value="all" 
					<?php
					if (!empty($prod_manage) && 'all' == $prod_manage) :
						?>
						selected <?php endif ?>><?php echo esc_html__('All Products', 'addify-multi-inventory-management'); ?>
					</option>

					<option value="managed" 
					<?php
					if (!empty($prod_manage) && 'managed' == $prod_manage) :
						?>
						selected
						<?php endif ?>>
						<?php echo esc_html__('Managed', 'addify-multi-inventory-management'); ?>
					</option>

					<option value="unmanaged" 
					<?php
					if (!empty($prod_manage) && 'unmanaged' == $prod_manage) :
						?>
						selected
						<?php endif ?>>
						<?php echo esc_html__('Unmanaged', 'addify-multi-inventory-management'); ?>
					</option>

				</select>

				<select name="af_pro_location" class="af_mli_stock_managed">

					<option value="all" 
					<?php
					if (!empty($inven_location) && 'all' == $inven_location) :
						?>
						selected <?php endif ?>><?php echo esc_html__('All Locations', 'addify-multi-inventory-management'); ?>
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

						<option value="<?php echo esc_attr($inventory_location->term_id); ?>" 
													<?php
													if (!empty($inven_location) && $inventory_location->term_id == $inven_location) :
														?>
								selected <?php endif ?>>

							<?php echo esc_attr($inventory_location->name); ?>

						</option>

						<?php
					}
					?>

				</select>

				<input type="submit" name="af_mli_filter_btn" class="button af_mli_filter_btn"
					value="<?php echo esc_html__('Filter', 'addify-multi-inventory-management'); ?>"
					class="button-primary">

		</form>


		<input type="button" name="af_mli_ms_reset_all_btn" class="af_mli_reset_btn button" id='af_reset'
			value="Reset All">
	</div>

</div>

<form method="POST">
	<div class="af_mli_no_product_msg"></div>
	<?php


	wp_nonce_field('mli_manage_stock_nonce', 'mli_manage_stock_nonce_field');
	$products_obj = new AF_MLI_Product_Stock_Table();
	$products_obj->prepare_items();
	$products_obj->display();
	submit_button('Save Changes', 'primary', 'af_mli_manage_stock_save_table', true);
	?>

</form>
</div>