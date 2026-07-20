<?php
/**
 * CSV file of Module
 *
 * Manage all settings and actions of CSV
 *
 * @package  Addify Product Multi Location Inventory
 * @version  1.0.0
 */

defined('ABSPATH') || exit();

class Addify_Inventory_CSV_Export_Import {





	public function af_inven_import_csv() {

		$valid_file_types = array( 'csv' => 'text/csv' );

		$nonce = isset($_POST['af_mli_import_csv_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['af_mli_import_csv_nonce_field'])) : 0;

		if (!wp_verify_nonce($nonce, 'af_mli_import_csv_nonce')) {

			wp_die('Failed Security Check');

		}


		$filetype = array();
		if (isset($_FILES['uploadFile']['name'])) {

			$filetype = wp_check_filetype(sanitize_text_field(wp_unslash($_FILES['uploadFile']['name'])), $valid_file_types);

		}

		if (in_array($filetype['type'], $valid_file_types, true)) {

			$target_dir = AFMLI_PLUGIN_DIR . 'includes/admin/inven_csv/uploaded_csv/';

			if (isset($_FILES['uploadFile']['name'])) {

				$target_dir = $target_dir . basename(sanitize_text_field(isset($_FILES['uploadFile']['name'])));

			}

			if (isset($_FILES['uploadFile']['tmp_name'])) {

				$tempo_name = sanitize_text_field($_FILES['uploadFile']['tmp_name']);

			}

			if (copy($tempo_name, $target_dir)) {

				$handle = fopen($target_dir, 'r');

				if (false !== $handle) {

					$data2 = fgetcsv($handle, 1000, ',');

					$row = 1;

					while (( $data = fgetcsv($handle, 1000, ',') ) !== false) {
						if ($row >= 1) {

							$prod_id   = $this->mli_import_csv($data[0]);
							$prod_name = $this->mli_import_csv($data[1]);
							$i_ids     = $this->mli_import_csv($data[2]);
							$i_title   = $this->mli_import_csv($data[3]);

							$i_location = $this->mli_import_csv($data[4]);

							$i_priority = $this->mli_import_csv($data[5]);

							$i_sku = $this->mli_import_csv($data[6]);

							$i_date = $this->mli_import_csv($data[7]);

							$i_exp_date = $this->mli_import_csv($data[8]);

							// $i_add_price = $this->mli_import_csv($data[9]);

							$i_price = $this->mli_import_csv($data[9]);

							$i_sale_price = $this->mli_import_csv($data[10]);

							$i_stock_quan = $this->mli_import_csv($data[11]);

							$i_low_stock = $this->mli_import_csv($data[12]);

							$i_title_array = explode(',', $i_title);

							$i_location_array = explode(',', $i_location);

							$i_priority_array = explode(',', $i_priority);

							$i_sku_array = explode(',', $i_sku);

							$i_date_array = explode(',', $i_date);

							$i_exp_date_array = explode(',', $i_exp_date);

							// $i_add_price_array = explode(',', $i_add_price);

							$i_price_array = explode(',', $i_price);

							$i_sale_price_array = explode(',', $i_sale_price);

							$i_stock_quan_array = explode(',', $i_stock_quan);

							$i_low_stock_array = explode(',', $i_low_stock);

							$product = wc_get_product($prod_id);


							$i_ids = explode(',', $i_ids);

							foreach ($i_ids as $i_key => $i_id) {

								$create_new_inven_flag = false;

								if (!empty($i_id)) {

									if ('af_prod_lvl_invent' == get_post_type($i_id)) {

										if (wp_get_post_parent_id($i_id) != $prod_id) {

											$create_new_inven_flag = true;

										} else {

											$inven_id = $i_id;

										}
									} else {

										$create_new_inven_flag = true;

									}

								} else {

									$create_new_inven_flag = true;
								}

								if ($create_new_inven_flag) {

									$inven_id = wp_insert_post(

										array(

											'post_type'   => 'af_prod_lvl_invent',

											'post_status' => 'publish',

											'post_parent' => $prod_id,
										)
									);

								}


								if (!empty($i_title_array)) {

									$i_title_array_count = count($i_title_array);

									$i_loc_array = array();

									if (isset($i_title_array[ $i_key ])) {

										$name = (string) $i_title_array[ $i_key ];

										update_post_meta($inven_id, 'af_new_invent_name', (string) $name);

										wp_update_post(

											array(
												'post_type' => 'af_prod_lvl_invent',

												'post_status' => 'publish',

												'ID' => $inven_id,

												'post_title' => (string) $name,

											)
										);

										$af_taxonomy = get_term_by('slug', $i_location_array[ $i_key ], 'mli_location');

										if (( $af_taxonomy )) {

											$taxonomy_id = $af_taxonomy->term_id;

											update_post_meta($inven_id, 'in_location', $af_taxonomy->term_id);

											$i_loc_array[] = $af_taxonomy->term_id;

										}
									}

									if (isset($i_priority_array[ $i_key ]) && !empty($i_priority_array)) {

										update_post_meta($inven_id, 'in_field_priority', $i_priority_array[ $i_key ]);

										wp_update_post(

											array(
												'post_type' => 'af_prod_lvl_invent',

												'post_status' => 'publish',

												'ID' => $inven_id,

												'menu_order' => $i_priority_array[ $i_key ],
											)
										);

									}

									if (isset($i_sku_array[ $i_key ]) && !empty($i_sku_array)) {

										update_post_meta($inven_id, 'in_sku', $i_sku_array[ $i_key ]);

									}

									if (isset($i_date_array[ $i_key ]) && !empty($i_date_array)) {

										update_post_meta($inven_id, 'in_date', $i_date_array[ $i_key ]);

									}

									if (isset($i_exp_date_array[ $i_key ]) && !empty($i_exp_date_array)) {

										update_post_meta($inven_id, 'in_expiry_date', $i_exp_date_array[ $i_key ]);

									}

									// if (isset($i_dadd_price_array[$i_key]) && !empty($i_add_price_array)) {

									//  $in_add_price_value = 'yes' == $i_add_price_array[$i_key] ? 'yes' : 'no';

									//  update_post_meta($inven_id, 'in_add_price', $in_add_price_value);
									// }

									$in_price = isset($i_price_array[ $i_key ]) ? $i_price_array[ $i_key ] : '';


									update_post_meta($inven_id, 'in_price', $in_price);

									$in_sale_price = isset($i_sale_price_array[ $i_key ]) ? $i_sale_price_array[ $i_key ] : '';


									update_post_meta($inven_id, 'in_sale_price', $in_sale_price);

									$in_stock_quantity = isset($i_stock_quan_array[ $i_key ]) ? $i_stock_quan_array[ $i_key ] : '';

									update_post_meta($inven_id, 'in_stock_quantity', $in_stock_quantity);

									$in_low_stock_threshold = isset($i_low_stock_array[ $i_key ]) ? $i_low_stock_array[ $i_key ] : '';

									update_post_meta($inven_id, 'in_low_stock_threshold', $in_low_stock_threshold);

								}
							}

							af_mli_update_product_location_taxonomy($product);

							++$row;
						}
					}

					fclose($handle);
				}
			}
		}
	}

	public function af_inven_export_csv() {

		$mli_prod_id = '';

		$mli_prod_name = '';

		$mli_i_id = '';

		$mli_i_name = '';

		$mli_i_loc = '';

		$mli_i_priority = '';

		$mli_i_sku = '';

		$mli_i_date = '';

		$mli_i_exp_date = '';

		$mli_i_manage_stock = '';

		$mli_i_stock_quantity = '';

		$mli_i_low_stock = '';

		$mli_i_allow_back_order = '';

		$mli_i_stock_status = '';

		$mli_i_sold_individ = '';

		$inven_delimiterr = ',';

		$directory = AFMLI_PLUGIN_DIR . 'assets/files/';

		$filename = 'All-Inventories ' . gmdate('Y-m-d') . '.csv';

		$file_path = $directory . $filename;


		// Check if the directory exists; if not, create it
		if (!is_dir($directory)) {
			mkdir($directory, 0755, true);
		}


		$f1 = fopen($file_path, 'w+');

		$path = $file_path;

		$inven_fields = array(


			'prod_id'             => 'Product ID',

			'prod_name'           => 'Product Name',

			'i_id'                => 'Inventory ID',

			'i_name'              => 'Inventory Name',

			'location'            => 'Inventory Location Slug',

			'priority'            => 'Inventory Priority',

			'sku'                 => 'Inventory SKU',

			'date'                => 'Inventory Date',

			'exp_date'            => 'Inventory Expiry Date',

			// 'add_price' => 'Add Inventory Price?',

			'price'               => 'Inventory Price',

			'sale_price'          => 'Inventory Sale Price',

			'stock_quantity'      => 'Inventory Stock Quantity',

			'low_stock_threshold' => 'Inventory Low Stock Threshold',
		);

		fputcsv($f1, $inven_fields, $inven_delimiterr);

		$all_prod_ids = new WC_Product_Query(
			array(
				'type'        => array( 'simple', 'variation', 'variable', 'grouped', 'external' ),

				'paginate'    => true,

				'return'      => 'ids',

				'post_status' => 'publish',

				'order'       => 'ASC',

				'limit'       => -1,
			)
		);

		$af_products = $all_prod_ids->get_products();

		foreach ($af_products->products as $prod_id) {

			$prod_inven = get_posts(
				array(

					'post_type'   => 'af_prod_lvl_invent',

					'post_status' => 'publish',

					'numberposts' => -1,

					'fields'      => 'ids',

					'post_parent' => $prod_id,

					'orderby'     => 'menu_order',

					'order'       => 'ASC',
				)
			);

			if (!empty($prod_inven)) {

				$prod_data_array = $this->af_mli_export_csv_data($prod_id);

				$inven_fields['prod_id'] = $prod_data_array['prod_id'];

				$inven_fields['prod_name'] = $prod_data_array['prod_name'];

				$inven_fields['i_id'] = $prod_data_array['i_id'];

				$inven_fields['i_name'] = $prod_data_array['i_name'];

				$inven_fields['location'] = $prod_data_array['location'];

				$inven_fields['priority'] = $prod_data_array['priority'];

				$inven_fields['sku'] = $prod_data_array['sku'];

				$inven_fields['date'] = $prod_data_array['date'];

				$inven_fields['exp_date'] = $prod_data_array['exp_date'];

				// $inven_fields['add_price'] = $prod_data_array['add_price'];

				$inven_fields['price'] = $prod_data_array['price'];

				$inven_fields['sale_price'] = $prod_data_array['sale_price'];

				$inven_fields['stock_quantity'] = $prod_data_array['stock_quantity'];

				$inven_fields['low_stock_threshold'] = $prod_data_array['low_stock_threshold'];

				// echo '<pre>';
				// print_r($inven_fields);
				// echo '</pre>';

				fputcsv($f1, $inven_fields, $inven_delimiterr);
			}
		}

		fseek($f1, 0);

		header('Content-Type: text/csv');

		header('Content-Disposition: attachment; filename="' . $filename . '";');

		fpassthru($f1);

		wp_delete_file($path);

		exit;
	}

	public function af_mli_export_csv_data( $prod_id ) {

		$prod_data = array();

		$var_prod_obj = wc_get_product($prod_id);

		$prod_name = $var_prod_obj->get_name();

		$inven_ids = array();

		$inven_name = array();

		$inven_location = array();

		$inven_priority = array();

		$inven_sku = array();

		$inven_date = array();

		$inven_exp_date = array();

		$inven_add_price = array();

		$inven_price = array();

		$inven_sale_price = array();

		$inven_stock_quantity = array();

		$inven_low_stock_threshold = array();

		$prod_inven = get_posts(
			array(
				'post_type'   => 'af_prod_lvl_invent',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'      => 'ids',
				'post_parent' => $prod_id,
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
			)
		);

		if (!empty($prod_inven)) {

			foreach ($prod_inven as $inven_id) {

				$inven_ids[] = $inven_id;

				$title = get_the_title($inven_id);

				if (!empty($title)) {

					$inven_name[] = $title;
				}

				$location = get_post_meta($inven_id, 'in_location', true);

				$location_name = '';

				if ($location) {

					$term = get_term($location);

					if ($term) {

						$location_name = $term->slug;
					}
				}

				$inven_location[] = $location_name;

				$inven_priority[] = get_post_meta($inven_id, 'in_field_priority', true);

				$inven_sku[] = get_post_meta($inven_id, 'in_sku', true);

				$inven_date[] = get_post_meta($inven_id, 'in_date', true);

				$inven_exp_date[] = get_post_meta($inven_id, 'in_expiry_date', true);

				// $inven_add_price[] = 'yes' == get_post_meta($inven_id, 'in_add_price', true) ? 'yes' : 'no';

				$inven_stock_quantity[] = get_post_meta($inven_id, 'in_stock_quantity', true);

				$inven_price[] = get_post_meta($inven_id, 'in_price', true);

				$inven_sale_price[] = get_post_meta($inven_id, 'in_sale_price', true);

				$inven_low_stock_threshold[] = get_post_meta($inven_id, 'in_low_stock_threshold', true);

			}
		}


		// $prod_data = af_mli_custom_array_filter($prod_data);

		// $inven_ids = af_mli_custom_array_filter($inven_ids);

		// $inven_name = af_mli_custom_array_filter($inven_name);

		// $inven_location = af_mli_custom_array_filter($inven_location);

		// $inven_priority = af_mli_custom_array_filter($inven_priority);

		// $inven_sku = af_mli_custom_array_filter($inven_sku);

		// $inven_date = af_mli_custom_array_filter($inven_date);

		// $inven_exp_date = af_mli_custom_array_filter($inven_exp_date);

		// $inven_add_price = af_mli_custom_array_filter($inven_add_price);

		// $inven_price = af_mli_custom_array_filter($inven_price);

		// $inven_sale_price = af_mli_custom_array_filter($inven_sale_price);

		// $inven_stock_quantity = af_mli_custom_array_filter($inven_stock_quantity);

		// $inven_low_stock_threshold = af_mli_custom_array_filter($inven_low_stock_threshold);

		$prod_data['prod_id'] = $prod_id;

		$prod_data['prod_name'] = $prod_name;

		$prod_data['i_id'] = !empty($inven_ids) ? implode(',', $inven_ids) : '';

		$prod_data['i_name'] = !empty($inven_name) ? implode(',', $inven_name) : '';

		$prod_data['priority'] = !empty($inven_priority) ? implode(',', $inven_priority) : '';

		$prod_data['sku'] = !empty($inven_sku) ? implode(',', $inven_sku) : '';

		$prod_data['date'] = !empty($inven_date) ? implode(',', $inven_date) : '';

		$prod_data['exp_date'] = !empty($inven_exp_date) ? implode(',', $inven_exp_date) : '';

		// $prod_data['add_price'] = !empty($inven_add_price) ? implode(',', $inven_add_price) : '';

		$prod_data['price'] = !empty($inven_price) ? implode(',', $inven_price) : '';

		$prod_data['sale_price'] = !empty($inven_sale_price) ? implode(',', $inven_sale_price) : '';

		$prod_data['stock_quantity'] = !empty($inven_stock_quantity) ? implode(',', $inven_stock_quantity) : '';

		$prod_data['low_stock_threshold'] = !empty($inven_low_stock_threshold) ? implode(',', $inven_low_stock_threshold) : '';

		$prod_data['location'] = !empty($inven_location) ? implode(',', $inven_location) : '';

		return $prod_data;
	}

	public function mli_import_csv( $s ) {

		if (preg_match('#[\x80-\x{1FF}\x{2000}-\x{3FFF}]#u', $s)) {

			return $s;
		}

		if (preg_match('#[\x7F-\x9F\xBC]#', $s)) {

			return iconv('WINDOWS-1250', 'UTF-8', $s);
		}

		return iconv('ISO-8859-2', 'UTF-8', $s);
	}
}

new Addify_Inventory_CSV_Export_Import();
