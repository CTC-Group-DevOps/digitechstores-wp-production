<?php defined('ABSPATH') || exit(); ?>

<div class="af_mli_export_portion_div" style="margin-bottom: 20px;">

	<form method="post" id="af_sm_import_form" action="" class="setting-form" enctype="multipart/form-data">

		<?php wp_nonce_field('af_mli_export_csv_nonce', 'af_mli_export_csv_nonce_field'); ?>

		<p><strong><?php echo esc_html__('You can Export csv file, with your stock data.', 'addify-multi-inventory-management'); ?></strong>
		</p>

		<p><?php echo esc_html__('Increase memory if export is not working (this may happen if there are alot of products).', 'addify-multi-inventory-management'); ?>
		</p>

		<input type="submit" name="af_export_file" value="Download CSV" class="button-primary">

		<p><?php echo esc_html__('This will only download the CSV of inventories that are created in products.', 'addify-multi-inventory-management'); ?>
		</p>

	</form>

</div>

<div class="af_mli_import_portion_div">

	<p><strong><?php echo esc_html__('You can upload csv file, with your stock data. ', 'addify-multi-inventory-management'); ?></strong>
	</p>

	<p><?php echo esc_html__('CSV file should have these columns in it.', 'addify-multi-inventory-management'); ?></p>

	<p><?php echo esc_html__('Increase memory if Import is not working (this may happen if there are alot of products).', 'addify-multi-inventory-management'); ?>
	</p>

	<h3><?php echo esc_html__('File format', 'addify-multi-inventory-management'); ?></h3>

	<table class="af_import_csv_table">

		<thead>

			<tr>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Product ID', 'addify-multi-inventory-management'); ?>
				</th>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Product Name', 'addify-multi-inventory-management'); ?>
				</th>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Inventory ID', 'addify-multi-inventory-management'); ?>
				</th>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Inventory Name', 'addify-multi-inventory-management'); ?>
				</th>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Inventory Location Slug', 'addify-multi-inventory-management'); ?>
				</th>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Inventory Priority', 'addify-multi-inventory-management'); ?>
				</th>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Inventory SKU', 'addify-multi-inventory-management'); ?>
				</th>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Inventory Date', 'addify-multi-inventory-management'); ?>
				</th>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Inventory Expiry Date', 'addify-multi-inventory-management'); ?>
				</th>

				<th style=" display:none; padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Add Inventory Price?', 'addify-multi-inventory-management'); ?>
				</th>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Inventory Price', 'addify-multi-inventory-management'); ?>
				</th>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Inventory Sale Price', 'addify-multi-inventory-management'); ?>
				</th>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Inventory Stock Quantity', 'addify-multi-inventory-management'); ?>
				</th>

				<th style="padding: 7px; vertical-align: top;">
					<?php echo esc_html__('Inventory Low Stock Threshold', 'addify-multi-inventory-management'); ?>
				</th>

			</tr>

		</thead>

		<tbody>

			<tr>

				<td style="vertical-align: top"><?php echo esc_html__('27', 'addify-multi-inventory-management'); ?>
				</td>

				<td style="vertical-align: top"><?php echo esc_html__('Album', 'addify-multi-inventory-management'); ?>
				</td>

				<td style="vertical-align: top">
					<?php echo esc_html__('200, 201', 'addify-multi-inventory-management'); ?>
				</td>

				<td style="vertical-align: top">
					<?php echo esc_html__('Album Inventory, Album Inventory 2', 'addify-multi-inventory-management'); ?>
				</td>

				<td style="vertical-align: top">
					<?php echo esc_html__('maxico, florida', 'addify-multi-inventory-management'); ?>
				</td>

				<td style="vertical-align: top"><?php echo esc_html__('1, 2', 'addify-multi-inventory-management'); ?>
				</td>

				<td style="vertical-align: top">
					<?php echo esc_html__('inv-album, inv-album-2', 'addify-multi-inventory-management'); ?>
				</td>

				<td style="vertical-align: top">
					<?php echo esc_html__('2/10/2022, 2/11/2022', 'addify-multi-inventory-management'); ?>
				</td>

				<td style="vertical-align: top">
					<?php echo esc_html__('2/27/2022, 2/28/2022', 'addify-multi-inventory-management'); ?>
				</td>

				<td style=" display: none; vertical-align: top">
					<?php echo esc_html__('yes, yes', 'addify-multi-inventory-management'); ?>
				</td>

				<td style="vertical-align: top"><?php echo esc_html__('50, 70', 'addify-multi-inventory-management'); ?>
				</td>

				<td style="vertical-align: top"><?php echo esc_html__('20, 40', 'addify-multi-inventory-management'); ?>
				</td>

				<td style="vertical-align: top">
					<?php echo esc_html__('100, 125', 'addify-multi-inventory-management'); ?>
				</td>

				<td style="vertical-align: top"><?php echo esc_html__('20, 15', 'addify-multi-inventory-management'); ?>
				</td>

			</tr>

		</tbody>

	</table>

	<ul class="af_mli_csv_import_info">

		<li>

			<strong><?php echo esc_html__('Product ID: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('product id, required. Necessary for import and export.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li>

			<strong><?php echo esc_html__('Product Name: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('product name, required. Necessary for import and export.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li>

			<strong><?php echo esc_html__('Inventory ID: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('Inventory ID, Required for identification of inventory. Write comma separated if more than 1 inventory ID.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li>

			<strong><?php echo esc_html__('Inventory Name: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('Inventory Name, Required for identification of inventory. Write comma separated if more than 1 inventory name.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li>

			<strong><?php echo esc_html__('Inventory Location Slug: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('The slug of location of inventory. Write comma separated if more than 1 inventory location slug.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li>

			<strong><?php echo esc_html__('Inventory Priority: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('For prioritize the inventory. Write comma separated if more than 1 inventory priority.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li>

			<strong><?php echo esc_html__('Inventory SKU: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('Inventory SKU (Stock Keeping Unit). Write comma separated if more than 1 inventory sku.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li>

			<strong><?php echo esc_html__('Inventory Start Date: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('The start date of inventory. Write comma separated if more than 1 inventory date.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li>

			<strong><?php echo esc_html__('Inventory Expiry Date: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('The date on which inventory will expire. Write comma separated if more than 1 inventory expiry date.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li style="display:none;">

			<strong><?php echo esc_html__('Add Inventory Price?: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('Check if seperate price for each inventory. Write comma separated if more than 1 inventory price.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li>

			<strong><?php echo esc_html__('Inventory Price: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('Price of each Inventory. Write comma separated for each inventory price.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li>

			<strong><?php echo esc_html__('Inventory Sale Price: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('Sale price of each Inventory. Write comma separated for each inventory sale price.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li>

			<strong><?php echo esc_html__('Inventory Stock Quantity: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('Stock quantity of each Inventory. Write comma separated for each inventory stock quantity.', 'addify-multi-inventory-management'); ?></i>

		</li>

		<li>

			<strong><?php echo esc_html__('Inventory Low Stock Threshold: ', 'addify-multi-inventory-management'); ?></strong>

			<i
				style="color: gray;"><?php echo esc_html__('Low stock threshold of each Inventory. Write comma separated for each inventory low stock threshold.', 'addify-multi-inventory-management'); ?></i>

		</li>

	</ul>

	<form method="post" id="af_sm_import_form" action="" class="setting-form" enctype="multipart/form-data">

		<p style="margin: 15px;">

			<span><i><b><?php echo esc_html__('Upload csv file', 'addify-multi-inventory-management'); ?></b></i></span>

			<span><input type="file" name="uploadFile" class="af_sm_file_csv"></span>

		</p>

		<?php wp_nonce_field('af_mli_import_csv_nonce', 'af_mli_import_csv_nonce_field'); ?>

		<input type="submit" name="af_mli_import_button" class="button-primary"
			value="<?php echo esc_html__('Upload CSV', 'addify-multi-inventory-management'); ?>" />

	</form>

</div>