<?php defined('ABSPATH') || exit(); ?>

<h1 style="margin: 1em 0; color: #1d2327; font-size: 1.3em;"><?php echo esc_attr($tab_title); ?></h1>

<div class="low_stock_product_table_div">

	<?php
	if (empty($low_stock_product_ids)) {

		?>
		<h1 class="af_mli_no_prod_heading">
			<?php echo esc_html__('No Products Found', 'addify-multi-inventory-management'); ?>
		</h1>
		<?php

	} else {

		?>
		<table class="wp-list-table widefat fixed striped table-view-list posts" style="text-align: center;">

			<thead>

				<tr>

					<th style="text-align: center;"> <?php echo esc_html__('ID', 'addify-multi-inventory-management'); ?>
					</th>

					<th style="text-align: center;">
						<?php echo esc_html__('Product Type', 'addify-multi-inventory-management'); ?>
					</th>

					<th style="text-align: center;">
						<?php echo esc_html__('Product Name', 'addify-multi-inventory-management'); ?>
					</th>

					<th style="text-align: center;">
						<?php echo esc_html__('Product Thumbnail', 'addify-multi-inventory-management'); ?>
					</th>

					<th style="text-align: center;">
						<?php echo esc_html__('Inventory Name', 'addify-multi-inventory-management'); ?>
					</th>

					<th style="text-align: center;">
						<?php echo esc_html__('Over Stock Threshold', 'addify-multi-inventory-management'); ?>
					</th>

					<th style="text-align: center;">
						<?php echo esc_html__('Current Stock', 'addify-multi-inventory-management'); ?>
					</th>

				</tr>

			</thead>

			<tbody>

				<?php

				foreach ($low_stock_product_ids as $prod_id) {

					$prod_obj = wc_get_product($prod_id);

					$prod_low_stock_threshold = $prod_obj->get_low_stock_amount();

					$prod_curr_stock = $prod_obj->get_stock_quantity();

					$image = get_the_post_thumbnail_url($prod_id) ? get_the_post_thumbnail_url($prod_id) : AFMLI_URL . 'assets/broken_image.png';

					$image_id = get_post_thumbnail_id($prod_id);

					if (!$image_id) {
						// Get the WooCommerce placeholder image ID
						$image_url = wc_placeholder_img_src(); // Get the placeholder image URL
						$image_id  = attachment_url_to_postid($image_url); // Get the image ID from the URL
			
					}

					$prod_edit_url = 'variation' == $prod_obj->get_type() ? get_edit_post_link(wp_get_post_parent_id($prod_id)) : get_edit_post_link($prod_id);

					?>

					<tr>

						<td>

							<?php echo esc_attr($prod_id); ?>

						</td>

						<td>

							<?php echo esc_attr(strtoupper($prod_obj->get_type())); ?>

						</td>

						<td>

							<a target="blank" href="<?php echo esc_url($prod_edit_url); ?>" class="af_prod_name">

								<?php echo esc_attr($prod_obj->get_name()); ?>

							</a>

						</td>

						<td>

							<?php wp_get_attachment_image_url($image_id, 'small'); ?>
						</td>

						<td>

							<?php echo esc_html('Main'); ?>

						</td>

						<td>

							<p><?php echo esc_attr(get_option('af_mli_over_stock_quantity')); ?></p>

						</td>

						<td>

							<p><?php echo esc_attr($prod_obj->get_stock_quantity()); ?></p>

						</td>

					</tr>

					<?php

				}

				?>

			</tbody>

		</table>

		<?php

	}

	?>

</div>