<?php
/**
 * Product Custom Column..
 *
 * @package  Addify Product Multi Location Inventory
 * @version  1.0.0
 */

defined('ABSPATH') || exit();

class Addify_Mli_Prod_Custom_Column {




	public function __construct() {

		add_filter('manage_edit-product_columns', array( $this, 'af_mli_product_cstm_column' ));

		add_action('manage_product_posts_custom_column', array( $this, 'add_product_column_content' ), 10, 2);
	}

	public function af_mli_product_cstm_column( $columns ) {

		if (empty($columns) && !is_array($columns)) {

			$columns = array();

		}

		unset($columns['title'], $columns['comments'], $columns['date']);

		$show_columns = array();

		$show_columns['cb'] = '<input type="checkbox" />';

		$show_columns['thumb'] = '<span class="wc-image tips" data-tip="' . esc_html__('Image', 'addify-multi-inventory-management') . '">' . esc_html__('Image', 'addify-multi-inventory-management') . '</span>';

		$show_columns['name'] = esc_html__('Name', 'addify-multi-inventory-management');

		if (wc_product_sku_enabled()) {

			$show_columns['sku'] = esc_html__('SKU', 'addify-multi-inventory-management');
		}

		if ('yes' === get_option('woocommerce_manage_stock')) {

			$show_columns['is_in_stock'] = esc_html__('Stock', 'addify-multi-inventory-management');
		}

		$show_columns['loc_stock'] = esc_html__('Stock at Location', 'addify-multi-inventory-management');

		$show_columns['price'] = esc_html__('Price', 'addify-multi-inventory-management');

		$show_columns['product_cat'] = esc_html__('Categories', 'addify-multi-inventory-management');

		$show_columns['product_tag'] = esc_html__('Tags', 'addify-multi-inventory-management');

		$show_columns['featured'] = '<span class="wc-featured parent-tips" data-tip="' . esc_html__('Featured', 'addify-multi-inventory-management') . '">' . esc_html__('Featured', 'addify-multi-inventory-management') . '</span>';

		$show_columns['date'] = esc_html__('Date', 'addify-multi-inventory-management');

		return array_merge($show_columns, $columns);
	}

	public function add_product_column_content( $column, $post_id ) {

		if ('loc_stock' == $column) {

			$prod_inven = get_posts(
				array(

					'post_type'   => 'af_prod_lvl_invent',

					'post_status' => 'publish',

					'numberposts' => -1,

					'fields'      => 'ids',

					'post_parent' => $post_id,

				)
			);

			$prod = wc_get_product($post_id);



			$stock_quantity = '1' == $prod->get_manage_stock() ? $prod->get_stock_quantity() : '';

			$af_loc = get_term(get_post_meta($post_id, 'in_location', true));

			$loc_stock = '';

			// if (!empty($af_loc)) {

				//              $loc_stock = isset($af_loc->name) ? $af_loc->name . ' (' . $stock_quantity . ')' : '';
				// $loc_stock = $af_loc->name;

				// $loc_stock = $loc_stock ?  $af_loc->name . ' (' . $stock_quantity . ')' : $af_loc->name;
			// }

			if (!empty($loc_stock)) {

				echo esc_attr(strtoupper($loc_stock));

				echo '<br>';
			}
			foreach ($prod_inven as $inven_id) {

				if (empty($inven_id)) {

					continue;

				}

				$af_loc = get_post_meta($inven_id, 'in_location', true);

				if ($af_loc) {

					$loc_stock = '';

					if ($af_loc) {

						$af_loc = get_term($af_loc);

						$af_stock = (float) get_post_meta($inven_id, 'in_stock_quantity', true);


						if (!empty($af_loc->name)) {
							$loc_stock = $af_loc->name . ' (' . $af_stock . ')';
						}
					}

					echo esc_attr(strtoupper($loc_stock));

					if (count($prod_inven) > 1) {

						echo ',<br>';

					}
				}
			}
		}
	}
}

new Addify_Mli_Prod_Custom_Column();
