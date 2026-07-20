<?php
/**
 * Define Class.
 *
 * @package : Addify Product Multi Location Inventory
 */

if (!defined('ABSPATH')) {
	die;
}
// Include WP_List_Table class.
if (!class_exists('WP_List_Table')) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class start.
 */
if (!class_exists('AF_MLI_Product_Stock_Table')) {
	/**
	 * Define Class 
	 */
	class AF_MLI_Product_Stock_Table extends WP_List_Table {
	


		/**
		 * Stores columns
		 *
		 * @var $column cols.
		 */

		public $columns;
		/**
		 * Stores hidden cols
		 *
		 * @var $hidden to hide cols.
		 */
		public $hidden;
		/**
		 * Stores sortable cols
		 *
		 *  @var $sortable to sort data.
		 */
		public $sortable;
		/**
		 * Stores total items
		 *
		 *  @var  int $total_items to sort data.
		 */
		public $total_items;

		public $current_page_ids = array();


		/**
		 * Constructor of the class
		 */
		public function __construct() {

			add_filter('woocommerce_product_data_store_cpt_get_products_query', array( $this, 'handle_items_range_query_var' ), 10, 2);

			parent::__construct(array(
				'singular' => esc_html__('Products', 'addify-multi-inventory-management'),
				'plural'   => esc_html__('Product', 'addify-multi-inventory-management'),
				'ajax'     => true,
				'screen'   => 'af_mli_list_products_screen',

			));
		}
		public function handle_items_range_query_var( $query, $query_vars ) {

			if (!empty($query_vars['meta_query'])) {
				foreach ($query_vars['meta_query'] as $q) {
					$query['meta_query'][] = $q;
				}
			}
			return $query;
		}
		/**
		 * Retrieve product's data
		 */
		public function get_products() {

			$paged = filter_input(INPUT_GET, 'paged', FILTER_VALIDATE_INT);
			$paged = ( 0 < $paged && $paged ) ? $paged : 1;

			$number_of_products_per_page = get_option('af_mli_number_of_products_per_page');
			$number_of_products_per_page = ( '' !== $number_of_products_per_page && $number_of_products_per_page ) ? intval($number_of_products_per_page) : 10;

			$af_mli_product_name      = filter_input(INPUT_GET, 'af_mli_pro_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$af_mli_stock_low_value   = filter_input(INPUT_GET, 'af_low_quatity_range', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$af_mli_stock_high_value  = filter_input(INPUT_GET, 'af_high_quatity_range', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$af_mli_selected_category = filter_input(INPUT_GET, 'af_selected_cat', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$af_mli_selected_category = ( 'all' !== $af_mli_selected_category && $af_mli_selected_category ) ? $af_mli_selected_category : '';

			$af_mli_selected_product_type = filter_input(INPUT_GET, 'af_mli_pro_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$af_mli_selected_product_type = ( 'all' !== $af_mli_selected_product_type && $af_mli_selected_product_type ) ? $af_mli_selected_product_type : array_merge(array( 'variation' ), array_keys(wc_get_product_types()));

			$af_mli_product_stock_status = filter_input(INPUT_GET, 'af_pro_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$af_mli_product_stock_status = ( 'all' !== $af_mli_product_stock_status && $af_mli_product_stock_status ) ? $af_mli_product_stock_status : '';

			$af_mli_product_managed = filter_input(INPUT_GET, 'af_pro_managment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$af_mli_product_managed = ( 'all' !== $af_mli_product_managed && $af_mli_product_managed ) ? $af_mli_product_managed : '';

			$af_mli_inven_location = filter_input(INPUT_GET, 'af_pro_location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			$af_mli_order_by = filter_input(INPUT_GET, 'orderby', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$order           = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			$donot_includes_product = array();

			if (empty($order)) {
				$order = 'asc';

			}
			$meta_key = '';

			switch ($af_mli_order_by) {
				case 'prd_name':
					$af_mli_order_by = 'title';
					$meta_key        = '';
					break;
				case 'prd_price':
					$af_mli_order_by = 'meta_value_num';
					$meta_key        = '_regular_price';
					break;
				case 'prd_sale_price':
					$af_mli_order_by = 'meta_value_num';
					$meta_key        = '_sale_price';
					break;
				case 'prd_sku':
					$af_mli_order_by = 'meta_value_num';
					$meta_key        = '_sku';
					break;
				default:
					$af_mli_order_by = 'title';
					$meta_key        = '';
					break;

			}

			if ('' != $af_mli_product_managed) {
				$af_mli_product_managed = 'managed' == $af_mli_product_managed ? true : false;
			}


			$args_array = array(
				'limit'        => $number_of_products_per_page,
				'paginate'     => true,
				'return'       => 'ids',
				'page'         => $paged,
				'orderby'      => $af_mli_order_by,
				'order'        => $order,
				'meta_key'     => $meta_key,
				'category'     => $af_mli_selected_category,
				'manage_stock' => $af_mli_product_managed,
				'type'         => $af_mli_selected_product_type,
				'stock_status' => $af_mli_product_stock_status,
			);


			if ('all' != $af_mli_inven_location && '' != $af_mli_inven_location) {
				$args_array['tax_query'] = array(
					array(

						'taxonomy' => 'mli_location',
						'field'    => 'term_id',
						'terms'    => $af_mli_inven_location,
						'operator' => 'IN',
					),
				);
			}


			if (!empty($af_mli_product_name)) {

				if (is_numeric($af_mli_product_name)) {
					$args_array['include'] = array( $af_mli_product_name );
				} else {
					$args_array['sku'] = $af_mli_product_name;
					$product_with_sku  = wc_get_products($args_array);

					unset($args_array['sku']);
					$args_array['name'] = $af_mli_product_name;

					$product_with_name = wc_get_products($args_array);
					unset($args_array['name']);
					$args_array['include'] = count(array_merge($product_with_sku->products, $product_with_name->products)) > 0 ? array_merge($product_with_sku->products, $product_with_name->products) : array( '' );
				}

			}



			$args_array['meta_query'] = array( 'relation' => 'AND' );

			if ('' != $af_mli_stock_low_value) {
				array_push(
					$args_array['meta_query'],
					array(
						'key'     => '_stock',
						'value'   => $af_mli_stock_low_value,
						'compare' => '>=',
						'type'    => 'DECIMAL',
					)
				);
			}

			if ('' != $af_mli_stock_high_value) {
				array_push(
					$args_array['meta_query'],
					array(
						'key'     => '_stock',
						'value'   => $af_mli_stock_high_value,
						'compare' => '<=',
						'type'    => 'DECIMAL',
					)
				);
			}

			$prod_threshold = (int) get_option('af_mli_over_stock_quantity');





			if ('overstock' == $af_mli_product_stock_status) {

				$args_array['meta_query'][] =
					array(
						'key'     => '_stock',
						'value'   => $prod_threshold,
						'compare' => '>=',
						'type'    => 'DECIMAL',
					);

			}



			if (isset($args_array['meta_query']) && is_array($args_array['meta_query']) && count($args_array['meta_query']) <= 1) {
				unset($args_array['meta_query']);
			}


			if (in_array($af_mli_product_stock_status, array( 'low_stock', 'overstock', 'outofstock' ))) {


				$af_mli_main_product_detail = (array) get_option('af_mli_main_product_detail');
				$args_array['exclude']      = wc_get_products(array(
					'limit'  => -1,
					'status' => 'any',
					'return' => 'ids',
				));

				// ---------------------------- check all the products  who have not low stock ---------------------.
				if ('low_stock' == $af_mli_product_stock_status) {

					unset($args_array['stock_status']);
					$args_array['include'] = isset($af_mli_main_product_detail['low_stock_product_ids']) ? (array) $af_mli_main_product_detail['low_stock_product_ids'] : array();


				}

				if ('overstock' == $af_mli_product_stock_status) {

					unset($args_array['stock_status']);
					$args_array['include'] = isset($af_mli_main_product_detail['over_stock_product_ids']) ? (array) $af_mli_main_product_detail['over_stock_product_ids'] : array();


				}

				if ('outofstock' == $af_mli_product_stock_status) {

					$args_array['include'] = isset($af_mli_main_product_detail['outstock_product_ids']) ? (array) $af_mli_main_product_detail['outstock_product_ids'] : array();


				}

			}



			$args_array = af_mli_custom_array_filter($args_array);
			$query      = new WC_Product_Query($args_array);
			$products   = $query->get_products();

			$all_product = $products->products;

			if (!empty($af_mli_inven_location) && count($products->products) >= 1) {

				$args = array(
					'limit'   => -1, // No limit
					'status'  => 'publish', // Fetch only published products
					'type'    => 'variable', // Only variable products
					'include' => $products->products, // Filter by provided IDs
				);

				$variable_products = wc_get_products($args);

				foreach ((array) $products->products as $product_id) {
					$args        = array(
						'post_type'   => 'product_variation',
						'post_status' => array( 'all' ),
						'numberposts' => -1,
						'orderby'     => 'menu_order',
						'order'       => 'asc',
						'fields'      => 'ids',
						'post_parent' => $product_id, // get parent post-ID
					);
					$all_product = array_merge($all_product, get_posts($args));
				}

				$all_product = array_unique($all_product);

			}

			$this->current_page_ids = $products->products;

			$this->total_items = $products->total;

			$items = array();



			foreach ((array) $all_product as $product_id) {
				$product = wc_get_product($product_id);

				$prod_inven = get_posts(
					array(
						'post_type'   => 'af_prod_lvl_invent',
						'post_status' => 'publish',
						'numberposts' => -1,
						'fields'      => 'ids',
						'post_parent' => $product_id,
						'orderby'     => 'menu_order',
						'order'       => 'ASC',
					)
				);


				$product_name = $product->get_name();

				if (count($prod_inven) >= 1) {
					$product_name .= ' ( Main Product )';
				}

				if (!empty($af_mli_inven_location) && 'variable' != $product->get_type()) {

					$show = false;

					foreach ($prod_inven as $current_prd_inventory_id) {
						if (get_post_meta($current_prd_inventory_id, 'in_location', true) == $af_mli_inven_location) {
							$show = true;
						}
					}

					if (!$show) {
						continue;
					}

				}

				$item                              = array();
				$item['ID']                        = $product_id;
				$item['prd_inven_id']              = '';
				$item['prd_type']                  = $product->get_type();
				$item['product_inven']             = $prod_inven;
				$item['prd_sku']                   = $product->get_sku();
				$item['prd_name']                  = $product_name;
				$item['prd_thumbnail']             = $product->get_image('woocommerce_thumbnail', array( 10, 10 ));
				$item['prd_tax_status']            = $product->get_tax_status();
				$item['prd_tax_class']             = $product->get_tax_class();
				$item['prd_shipping_class']        = '0' != $product->get_shipping_class_id() ? $product->get_shipping_class_id() : '';
				$item['prd_price']                 = $product->get_regular_price();
				$item['prd_sale_price']            = $product->get_sale_price();
				$item['prd_weight']                = $product->get_weight();
				$item['inven_name']                = 'Main Inventory';
				$item['inven_manage_stock']        = $product->get_manage_stock();
				$item['inven_stock_quan']          = $product->get_stock_quantity();
				$item['inven_stock_status']        = $product->get_stock_status();
				$item['inven_low_stock_threshold'] = $product->get_low_stock_amount();
				$item['inven_allow_back_order']    = $product->get_backorders();
				$item['inven_sold_individually']   = $product->get_sold_individually();
				$item['inven_location']            = (int) get_post_meta($product_id, 'in_location', true);
				$item['stock_update_history']      = 'History';

				$items[] = $item;

				if (!empty($prod_inven)) {

					foreach ($prod_inven as $prod_inven_id) {
						$item             = array();
						$inven_price      = 0;
						$inven_sale_price = 0;

						// if ('yes' == get_post_meta($prod_inven_id, 'in_add_price', true)) {
						$inven_price      = get_post_meta($prod_inven_id, 'in_price', true);
						$inven_sale_price = get_post_meta($prod_inven_id, 'in_sale_price', true);
						// } else {
						//  $inven_price = $product->get_regular_price();
						//  $inven_sale_price = $product->get_sale_price();
						// }

						if ('low_stock' == $af_mli_product_stock_status) {

							if ((int) get_post_meta($prod_inven_id, 'in_stock_quantity', true) > (int) get_post_meta($prod_inven_id, 'in_low_stock_threshold', true)) {

								continue;
							}

						}
						if ('overstock' == $af_mli_product_stock_status) {
							if ((int) get_post_meta($prod_inven_id, 'in_stock_quantity', true) < $prod_threshold) {

								continue;
							}
						}

						$loc_term = get_term((int) get_post_meta($prod_inven_id, 'in_location', true));
						$name     = $product->get_name();

						if (is_object($loc_term) && $loc_term->name) {
							$name .= ' - ' . $loc_term->name;
						}

						$item['ID']                        = $product_id;
						$item['prd_inven_id']              = $prod_inven_id;
						$item['prd_type']                  = $product->get_type();
						$item['product_inven']             = array();
						$item['prd_sku']                   = get_post_meta($prod_inven_id, 'in_sku', true);
						$item['prd_name']                  = $name;
						$item['prd_thumbnail']             = $product->get_image('woocommerce_thumbnail', array( 10, 10 ));
						$item['prd_tax_status']            = $product->get_tax_status();
						$item['prd_tax_class']             = $product->get_tax_class();
						$item['prd_shipping_class']        = '0' != $product->get_shipping_class_id() ? $product->get_shipping_class_id() : '';
						$item['prd_price']                 = $inven_price;
						$item['prd_sale_price']            = $inven_sale_price;
						$item['prd_weight']                = $product->get_weight();
						$item['inven_name']                = get_the_title($prod_inven_id);
						$item['inven_manage_stock']        = $product->get_manage_stock();
						$item['inven_stock_quan']          = (int) get_post_meta($prod_inven_id, 'in_stock_quantity', true);
						$item['inven_stock_status']        = $product->get_stock_status();
						$item['inven_low_stock_threshold'] = get_post_meta($prod_inven_id, 'in_low_stock_threshold', true);
						$item['inven_allow_back_order']    = $product->get_backorders();
						$item['inven_sold_individually']   = $product->get_sold_individually();
						$item['inven_location']            = (int) get_post_meta($prod_inven_id, 'in_location', true);
						$item['stock_update_history']      = 'History';

						$items[] = $item;
					}
				}
			}

			return $items;
		}
		/** 
		 * Text displayed when no products data is available 
		 * */
		public function no_items() {
			echo esc_html__('No Products available.', 'addify-multi-inventory-management');
		}


		public function column_ID( $item ) {

			if ('variation' == $item['prd_type']) {
				$product_edit_link = get_edit_post_link(wc_get_product($item['ID'])->get_parent_id());
			} else {
				$product_edit_link = get_edit_post_link($item['ID']);
			}

			if ('' == $item['prd_inven_id']) {
				return sprintf(
					'<input type="hidden" name="af_mli_prod_id" class="af_mli_prod_id" value="%s">
				<input type="hidden" name="prod_ids[]" value="%s">
				<a href="%s">%s</a>',
					$item['ID'],
					$item['ID'],
					$product_edit_link,
					$item['ID']
				);
			}
		}

		public function column_prd_type( $item ) {

			$prd_type = isset($item['prd_inven_id']) && !empty($item['prd_inven_id']) ? 'Location' : $item['prd_type'];

			if (isset($item['product_inven']) && is_array($item['product_inven']) && count($item['product_inven']) >= 1) {
				$prd_type .= ' ▼';
			}
			return sprintf(
				'<span title="Expand Inventories" class="af_variable_type" data-prod_id="%s" style="cursor: pointer;">
				%s		
				</span>',
				$item['ID'],
				$prd_type
			);
		}


		public function column_prd_price( $item ) {
			if ('variable' == $item['prd_type']) {
				return;
			}


			if ('' != $item['prd_inven_id']) {
				return sprintf(
					'<input type="number" class="ms_input af_mli_location_of af_mli_location_of%s" name="ms_i_price[%s]" value="%s" min="0" step="any">',
					$item['ID'],
					$item['prd_inven_id'],
					$item['prd_price']
				);
			} elseif (isset($item['product_inven']) && is_array($item['product_inven']) && count($item['product_inven']) >= 1) {

				return sprintf(
					'<input readonly type="number" class="ms_input" name="ms_prd_price[%s]" value="%s" min="0" step="any">',
					$item['ID'],
					$item['prd_price']
				);
			} else {

				return sprintf(
					'<input type="number" class="ms_input" name="ms_prd_price[%s]" value="%s" min="0" step="any">',
					$item['ID'],
					$item['prd_price']
				);
			}
		}

		public function column_prd_sale_price( $item ) {
			if ('variable' == $item['prd_type']) {
				return;
			}

			if ('' != $item['prd_inven_id']) {
				return sprintf(
					'<input type="number" class="ms_input" name="ms_i_sale_price[%s]" value="%s" min="0" step="any">',
					$item['prd_inven_id'],
					$item['prd_sale_price']
				);
			} elseif (isset($item['product_inven']) && is_array($item['product_inven']) && count($item['product_inven']) >= 1) {


				return sprintf(
					'<input type="number" readonly class="ms_input" name="ms_prd_sale_price[%s]" value="%s" min="0" step="any">',
					$item['ID'],
					$item['prd_sale_price']
				);
			} else {


				return sprintf(
					'<input type="number" class="ms_input" name="ms_prd_sale_price[%s]" value="%s" min="0" step="any">',
					$item['ID'],
					$item['prd_sale_price']
				);
			}
		}

		public function column_inven_manage_stock( $item ) {

			if ('external' == $item['prd_type']) {
				return;
			}
			if ('' != $item['prd_inven_id']) {
				return;
			}
			return sprintf(
				'<input type="checkbox" name="ms_prd_manage_stock[%s]" class="af_mli_manage_stock_checkbox af_m_s_chk_js af_m_s_chk_js%s"  value="yes" data-prod_id="%s" %s />',
				$item['ID'],
				$item['ID'],
				$item['ID'],
				'yes' == $item['inven_manage_stock'] ? 'checked' : ''
			);
		}

		public function column_prd_shipping_class( $item ) {
			$wc_shipping = WC_Shipping::instance();
			$classes     = $wc_shipping->get_shipping_classes();

			if ($item['prd_shipping_class']) {
				foreach ($classes as $class) {

					if ($class->term_id == $item['prd_shipping_class']) {
						return $class->name;
					}
				}
			}

			return '';
		}

		public function column_inven_sold_individually( $item ) {

			if ('' == $item['prd_inven_id']) {
				return sprintf(
					'<input type="checkbox" name="ms_i_sold_invidually[%s]" class="af_mli_sold_individually_checkbox" value="yes" %s>',
					$item['ID'],
					'yes' == $item['inven_sold_individually'] ? 'checked' : ''
				);
			}
		}

		public function column_inven_low_stock_threshold( $item ) {

			$id = isset($item['prd_inven_id']) && !empty($item['prd_inven_id']) ? $item['prd_inven_id'] : $item['ID'];
			return sprintf(
				'<input type="number" name="ms_prd_low_threshold[%s]" class="ms_input af_mli_prod_low_threshold%s" value="%s" min="0">',
				$id,
				$id,
				$item['inven_low_stock_threshold']
			);
		}


		public function column_inven_allow_back_order( $item ) {

			$product_backorder_options = wc_get_product_backorder_options();

			$options_html = '';
			foreach ($product_backorder_options as $value => $label) {
				$selected      = selected($item['inven_allow_back_order'], $value, false);
				$options_html .= sprintf('<option value="%s" %s>%s</option>', esc_attr($value), $selected, esc_html($label));
			}

			if ('external' == $item['prd_type']) {
				return;
			}

			return sprintf(
				'<select class="ms_select" name="ms_prd_allow_back_orders[%s]">%s</select>',
				$item['ID'],
				$options_html
			);
		}

		public function column_inven_stock_quan( $item ) {



			if ('external' == $item['prd_type']) {
				return;
			}

			$stock_status = '';

			switch ($item['inven_stock_status']) {
				case 'onbackorder':
					$stock_status = 'On Back Order';
					break;

			}
			$inven_stock_quan = isset($item['inven_stock_quan']) && '' != $item['inven_stock_quan'] ? $item['inven_stock_quan'] : '0';

			if ('' != $item['prd_inven_id']) {
				return sprintf(
					'<p class="af_bo_p%s">%s</p>
					<input type="number" min="0" name="ms_i_stock[%s]" class="ms_input af_mli_prod_stock  af_mli_prod_stock%s" value="%s">',
					$item['prd_inven_id'],
					$stock_status,
					$item['prd_inven_id'],
					$item['prd_inven_id'],
					$inven_stock_quan
				);
			} elseif (isset($item['product_inven']) && is_array($item['product_inven']) && count($item['product_inven']) >= 1) {


				return sprintf(
					'<p class="af_bo_p%s">%s</p>
						<input type="number" readonly min="0" name="ms_prd_stock[%s]" class="ms_input af_mli_prod_stock  af_mli_prod_stock%s" value="%s">',
					$item['ID'],
					$stock_status,
					$item['ID'],
					$item['ID'],
					$inven_stock_quan
				);
			} else {


				return sprintf(
					'<p class="af_bo_p%s">%s</p>
						<input type="number" r min="0" name="ms_prd_stock[%s]" class="ms_input af_mli_prod_stock  af_mli_prod_stock%s" value="%s">',
					$item['ID'],
					$stock_status,
					$item['ID'],
					$item['ID'],
					$inven_stock_quan
				);
			}
		}

		public function column_inven_stock_status( $item ) {

			if ('external' == $item['prd_type']) {
				return;
			}
			$inven_stock_quan = isset($item['inven_stock_quan']) && '' != $item['inven_stock_quan'] ? $item['inven_stock_quan'] : 0;

			if ('' != $item['prd_inven_id'] && ( 0 == $inven_stock_quan )) {
				$item['inven_stock_status'] = 'outofstock';
			}

			$stock_status = isset(wc_get_product_stock_status_options()[ $item['inven_stock_status'] ]) ? wc_get_product_stock_status_options()[ $item['inven_stock_status'] ] : '';

			$id = isset($item['prd_inven_id']) && !empty($item['prd_inven_id']) ? $item['prd_inven_id'] : $item['ID'];

			return sprintf(
				'<p class="%s">%s</p><input type="hidden" name="af_mli_prod_stock_status" class="af_inven_stock_status af_mli_prod_stock_status%s" value="%s" data-inven-id=%s> ',
				$id,
				$stock_status,
				$id,
				$item['inven_stock_status'],
				$id
			);
		}


		public function column_inven_location( $item ) {

			if (isset($item['inven_name']) && 'Main Inventory' == $item['inven_name']) {

				return 'Main Inventory';
			}

			$inventory_locations = get_terms(
				array(
					'number'     => 0,
					'hide_empty' => false,
					'taxonomy'   => 'mli_location',
				)
			);
			$options_html        = '';
			foreach ($inventory_locations as $selected_location) {
				$loc_term      = get_term($selected_location);
				$selected      = selected($item['inven_location'], $loc_term->term_id, false);
				$options_html .= sprintf('<option value="%s" %s>%s</option>', $loc_term->term_id, $selected, esc_html($loc_term->name));
			}

			if ('external' == $item['prd_type']) {
				return;
			}

			if ('' != $item['prd_inven_id']) {
				return sprintf(
					'<select class="ms_select" name="ms_i_location[%s]">%s</select>',
					$item['prd_inven_id'],
					$options_html
				);
			}
			return sprintf(
				'<select class="ms_select" name="ms_i_location[%s]">%s</select>',
				$item['ID'],
				$options_html
			);
		}

		public function column_stock_update_history( $item ) {

			if ('external' == $item['prd_type']) {
				return;
			}


			if (isset($item['prd_inven_id']) && !is_wp_error(get_term($item['prd_inven_id']))) {
				return sprintf(
					'<a target="_blank" class="btn btn-success" href="%s/admin.php?page=af_i_m_l_gen_settings&tab=Manage_Stock&history=%s&inven=%s">
					<img src="%s/assets/history.png" height="30" width="30" title="History"></a>',
					admin_url(),
					$item['ID'],
					$item['prd_inven_id'],
					AFMLI_URL
				);

			} else {
				return sprintf(
					'<a target="_blank" class="btn btn-success" href="%s/admin.php?page=af_i_m_l_gen_settings&tab=Manage_Stock&history=%s">
					<img src="%s/assets/history.png" height="30" width="30" title="History">
				</a>',
					admin_url(),
					$item['ID'],
					AFMLI_URL
				);
			}
		}

		/**
		 * Associative array of columns
		 *
		 * @return array
		 */
		public function get_columns() {


			$columns = array(
				'ID'                        => esc_html__('ID', 'addify-multi-inventory-management'),
				'prd_type'                  => esc_html__('Product Type', 'addify-multi-inventory-management'),
				'prd_sku'                   => esc_html__('Product SKU', 'addify-multi-inventory-management'),
				'prd_name'                  => esc_html__('Product Name', 'addify-multi-inventory-management'),
				'prd_thumbnail'             => esc_html__('Product Thumbnail', 'addify-multi-inventory-management'),
				'prd_tax_status'            => esc_html__('Tax Status', 'addify-multi-inventory-management'),
				'prd_tax_class'             => esc_html__('Tax Class', 'addify-multi-inventory-management'),
				'prd_shipping_class'        => esc_html__('Shipping Class', 'addify-multi-inventory-management'),
				'prd_price'                 => esc_html__('Price', 'addify-multi-inventory-management'),
				'prd_sale_price'            => esc_html__('Sale Price', 'addify-multi-inventory-management'),
				'prd_weight'                => esc_html__('Weight', 'addify-multi-inventory-management'),
				'inven_manage_stock'        => esc_html__('Manage Stock', 'addify-multi-inventory-management'),
				'inven_stock_quan'          => esc_html__('Stock Quantity', 'addify-multi-inventory-management'),
				'inven_stock_status'        => esc_html__('Stock Status', 'addify-multi-inventory-management'),
				'inven_low_stock_threshold' => esc_html__('Low Stock Threshold', 'addify-multi-inventory-management'),
				'inven_allow_back_order'    => esc_html__('Allow Back Orders?', 'addify-multi-inventory-management'),
				'inven_sold_individually'   => esc_html__('Sold Individually', 'addify-multi-inventory-management'),
				'inven_name'                => esc_html__('Inventory Name', 'addify-multi-inventory-management'),
				'inven_location'            => esc_html__('Location', 'addify-multi-inventory-management'),
				'stock_update_history'      => esc_html__('History', 'addify-multi-inventory-management'),
			);
			return $columns;
		}
		/**
		 * Columns to make sortable.
		 *
		 * @return array
		 */
		public function get_sortable_columns() {
			$sortable_columns = array(
				'prd_name'       => array( 'prd_name', false ),
				'prd_price'      => array( 'prd_price', false ),
				'prd_sale_price' => array( 'prd_sale_price', false ),
				'prd_sku'        => array( 'prd_sku', false ),

			);

			return $sortable_columns;
		}
		public function get_hidden_columns() {

			$ret_val = array( 'prd_sku', 'prd_name', 'prd_thumbnail', 'prd_tax_status', 'prd_tax_class', 'prd_shipping_class', 'prd_price', 'prd_sale_price', 'prd_weight', 'inven_name', 'inven_manage_stock', 'inven_stock_quan', 'inven_stock_status', 'inven_low_stock_threshold', 'inven_allow_back_order', 'inven_sold_individually', 'inven_location', 'stock_update_history' );

			return $ret_val;
		}

		public function column_default( $item, $column_name ) {
			return $item[ $column_name ];
		}


		public function prepare_items() {



			$per_page = get_option('af_mli_number_of_products_per_page') && '' != get_option('af_mli_number_of_products_per_page') ? get_option('af_mli_number_of_products_per_page') : 10;


			$this->_column_headers = $this->get_column_info();

			$columns               = $this->get_columns();
			$hidden                = $this->get_hidden_columns();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			$get_items = !empty($items) ? $items : $this->get_products();


			$current_page = $this->get_pagenum();
			$my_items     = array_slice($get_items, ( ( $current_page - 1 ) * $per_page ), $per_page);

			$this->items = $get_items;
			// Set Products as items of table.
			$this->set_pagination_args(
				array(
					'total_items' => $this->total_items,
					'per_page'    => $per_page,
					'total_pages' => ceil($this->total_items / $per_page),
				)
			);
		}
	}
}
