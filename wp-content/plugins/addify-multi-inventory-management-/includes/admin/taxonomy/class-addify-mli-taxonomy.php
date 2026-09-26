<?php

defined('ABSPATH') || exit;

class Addify_Mli_Taxonomy {



	public function __construct() {

		add_action('mli_location_add_form_fields', array( $this, 'af_add_fields_tax' ));

		add_action('mli_location_edit_form_fields', array( $this, 'af_edit_fields_tax' ), 10);

		add_action('created_term', array( $this, 'af_save_fields_tax' ), 10, 3);

		add_action('edit_term', array( $this, 'af_save_fields_tax' ), 10, 3);
	}

	public function af_add_fields_tax() {

		$loc_term_id = get_the_ID();

		wp_nonce_field('loc_taxonomy_nonce', 'loc_taxonomy_nonce_field');

		$af_mli_tax_email = get_term_meta($loc_term_id, 'af_mli_tax_email', true);

		$af_mli_tax_loc_shop_manager = get_term_meta($loc_term_id, 'af_mli_tax_loc_shop_manager', true);

		$af_mli_tax_phone = get_term_meta($loc_term_id, 'af_mli_tax_phone', true);

		$af_mli_tax_adress = get_term_meta($loc_term_id, 'af_mli_tax_adress', true);

		$af_mli_tax_city = get_term_meta($loc_term_id, 'af_mli_tax_city', true);

		$af_mli_tax_state = get_term_meta($loc_term_id, 'af_mli_tax_state', true);

		$af_mli_tax_zip_code = get_term_meta($loc_term_id, 'af_mli_tax_zip_code', true);

		$af_mli_tax_country = get_term_meta($loc_term_id, 'af_mli_tax_country', true);

		$af_mli_tax_longitude = get_term_meta($loc_term_id, 'af_mli_tax_longitude', true);

		$af_mli_tax_except_order = get_term_meta($loc_term_id, 'af_mli_tax_except_order', true);

		$af_mli_tax_open_time = get_term_meta($loc_term_id, 'af_mli_tax_open_time', true);

		$af_mli_tax_close_time = get_term_meta($loc_term_id, 'af_mli_tax_close_time', true);

		$af_mli_tax_shipping_zones = (array) get_term_meta($loc_term_id, 'af_mli_tax_shipping_zones', true);

		$af_mli_tax_shipping_methods = (array) get_term_meta($loc_term_id, 'af_mli_tax_shipping_methods', true);

		$af_mli_tax_payment_methods = (array) get_term_meta($loc_term_id, 'af_mli_tax_payment_methods', true);

		$countries_obj = new WC_Countries();

		$countries = $countries_obj->get_countries();

		$states = !empty($af_mli_tax_country) ? $countries_obj->get_states($af_mli_tax_country) : array();

		?>

		<div class="form-field">

			<p class="af_mli_p_tag"><?php echo esc_html__('Location Shop Manager Name', 'addify-multi-inventory-management'); ?>
			</p>

			<input type="float" name="af_mli_tax_loc_shop_manager" class="af_mli_taxonomy_fields_style"
				value="<?php echo esc_attr($af_mli_tax_loc_shop_manager); ?>">

		</div>

		<div class="form-field">

			<div style="width: 50%; float: left;">

				<p class="af_mli_p_tag"><?php echo esc_html__('Email', 'addify-multi-inventory-management'); ?></p>

				<input type="email" name="af_mli_tax_email" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr($af_mli_tax_email); ?>">

			</div>

			<div style="width: 50%; float: right;">

				<p class="af_mli_p_tag"><?php echo esc_html__('Phone', 'addify-multi-inventory-management'); ?></p>

				<input type="float" name="af_mli_tax_phone" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr($af_mli_tax_phone); ?>">

			</div>

		</div>

		<div class="form-field">

			<p class="af_mli_p_tag"><?php echo esc_html__('Address', 'addify-multi-inventory-management'); ?></p>

			<input type="address" name="af_mli_tax_adress" class="af_mli_taxonomy_fields_style"
				value="<?php echo esc_attr($af_mli_tax_adress); ?>">

		</div>

		<div class="form-field">

			<div style="width: 50%; float: left;">

				<p class="af_mli_p_tag"><?php echo esc_html__('Country', 'addify-multi-inventory-management'); ?></p>

				<select name="af_mli_tax_country" style="width: 95%; height: 35px" class="af_mli_tax_country"
					data-tax_id="<?php echo esc_attr($loc_term_id); ?>">

					<option disabled selected><?php echo esc_html('Select Option'); ?></option>

					<?php foreach ($countries as $country_key => $country_name) : ?>

						<option value="<?php echo esc_attr($country_key); ?>" <?php echo selected($country_key, $af_mli_tax_country); ?>>
							<?php echo esc_attr($country_name); ?>
						</option>

					<?php endforeach ?>

				</select>

			</div>

			<div style="width: 50%; float: right;">

				<p class="af_mli_p_tag"><?php echo esc_html__('State', 'addify-multi-inventory-management'); ?></p>

				<select name="af_mli_tax_state" style="width: 95%; height: 35px"
					class="af_mli_tax_state <?php echo esc_attr($loc_term_id); ?>af_mli_tax_state">

					<option disabled selected><?php echo esc_html('Select Option'); ?></option>

					<?php foreach ($states as $state_key => $state_name) : ?>

						<option value="<?php echo esc_attr($state_key); ?>" <?php echo selected($state_key, $af_mli_tax_state); ?>>
							<?php echo esc_attr($state_name); ?>
						</option>

					<?php endforeach ?>

				</select>

			</div>

		</div>

		<div class="form-field">

			<div style="width: 50%; float: left; margin: 1em 0; padding: 0; ">

				<p class="af_mli_p_tag"><?php echo esc_html__('City', 'addify-multi-inventory-management'); ?></p>

				<input type="text" name="af_mli_tax_city" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr($af_mli_tax_city); ?>">

			</div>

			<div style="width: 50%; float: right; margin: 1em 0; padding: 0;">

				<p class="af_mli_p_tag"><?php echo esc_html__('Zip Code', 'addify-multi-inventory-management'); ?></p>

				<input type="number" name="af_mli_tax_zip_code" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr($af_mli_tax_zip_code); ?>">

			</div>

		</div>
		<div class="form-field">

			<div style="width: 50%; float: right; margin: 1em 0; padding: 0;">

				<p class="af_mli_p_tag"><?php echo esc_html__('Latitude', 'addify-multi-inventory-management'); ?></p>

				<input type="number" step="any" name="af_mli_location_latitude" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr(get_term_meta($loc_term_id, 'af_mli_location_latitude', true)); ?>">

			</div>
			<div style="width: 50%; float: left; margin: 1em 0; padding: 0; ">

				<p class="af_mli_p_tag"><?php echo esc_html__('Longitude', 'addify-multi-inventory-management'); ?></p>

				<input type="number" step="any" name="af_mli_location_longitude" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr(get_term_meta($loc_term_id, 'af_mli_location_longitude', true)); ?>">

			</div>
			<p class="af_mli_p_tag">
				<?php echo esc_html__('Please set longitude and latitude to show nearest location to user in dropdown.', 'addify-multi-inventory-management'); ?>
			</p>

		</div>

		<div class="form-field">

			<span>

				<input type="checkbox" name="af_mli_tax_except_order" value="yes" <?php checked('yes', $af_mli_tax_except_order); ?>>

				<?php echo esc_html__('Accept order in specific time', 'addify-multi-inventory-management'); ?>

			</span>

		</div>

		<div class="form-field af_mli_loc_time">

			<p class="af_mli_p_tag"><?php echo esc_html__('Location Time', 'addify-multi-inventory-management'); ?></p>

			<span>

				<?php echo esc_html('Opens at - '); ?>

				<input type="time" name="af_mli_tax_open_time" class="af_mli_taxonomy_fields_style" style="width: 30%"
					value="<?php echo esc_attr($af_mli_tax_open_time); ?>">

				<?php echo esc_html('Closes at - '); ?>

				<input type="time" name="af_mli_tax_close_time" class="af_mli_taxonomy_fields_style" style="width: 30%"
					value="<?php echo esc_attr($af_mli_tax_close_time); ?>">

			</span>

		</div>

		<?php

		if ('yes' == get_option('mli_shipp_zones_to_loc')) {

			?>

			<div class="form-field">

				<p class="af_mli_p_tag"><?php echo esc_html__('Shipping Zones', 'addify-multi-inventory-management'); ?></p>

				<select name="af_mli_tax_shipping_zones[]" class="af_mli_taxonomy_fields_style af_mli_tax_shipping_zones"
					data-tax_id="<?php echo esc_attr($loc_term_id); ?>" multiple>

					<?php

					$s_zones = WC_Data_Store::load('shipping-zone');

					$all_zones = $s_zones->get_zones();

					foreach ($all_zones as $s_zone) {

						?>
						<option value="<?php echo esc_attr($s_zone->zone_id); ?>" 
													<?php
													if (in_array($s_zone->zone_id, $af_mli_tax_shipping_zones)) :
														?>
								selected <?php endif ?>>

							<?php echo esc_attr($s_zone->zone_name); ?>

						</option>

						<?php

					}

					?>

				</select>

			</div>

			<?php
		}

		if ('yes' == get_option('mli_shipp_methods_to_loc')) {

			$shipping_methods = WC()->shipping->get_shipping_methods();

			?>

			<div class="form-field">

				<p class="af_mli_p_tag"><?php echo esc_html__('Shipping Methods', 'addify-multi-inventory-management'); ?></p>

				<select name="af_mli_tax_shipping_methods[]"
					class="af_mli_taxonomy_fields_style <?php echo esc_attr($loc_term_id); ?>af_mli_tax_shipping_methods af_mli_tax_shipping_methods"
					multiple>

					<?php
					foreach ($shipping_methods as $shipping_method) {

						?>
						<option value="<?php echo esc_attr($shipping_method->id); ?>" 
													<?php
													if (in_array($shipping_method->id, $af_mli_tax_shipping_methods)) :
														?>
								selected <?php endif ?>>

							<?php echo esc_attr($shipping_method->method_title); ?>

						</option>
						<?php
					}
					?>

				</select>

			</div>

			<?php
		}

		if ('yes' == get_option('mli_shipp_payment_method_of_loc')) {

			?>

			<div class="form-field">

				<p class="af_mli_p_tag"><?php echo esc_html__('Payment Methods', 'addify-multi-inventory-management'); ?></p>

				<select name="af_mli_tax_payment_methods[]" class="af_mli_taxonomy_fields_style af_mli_tax_payment_methods"
					multiple>

					<?php

					$af_payment_method = WC()->payment_gateways->get_available_payment_gateways();

					foreach ($af_payment_method as $af_p_method) {
						?>
						<option value="<?php echo esc_attr($af_p_method->id); ?>" 
													<?php
													if (in_array($af_p_method->id, $af_mli_tax_payment_methods)) :
														?>
								selected <?php endif ?>>

							<?php echo esc_attr($af_p_method->title); ?>

						</option>

						<?php

					}

					?>

				</select>

			</div>

			<?php
		}

		?>

		<br>

		<?php
	}

	public function af_edit_fields_tax( $term ) {

		$loc_term_id = $term->term_id;

		wp_nonce_field('loc_taxonomy_nonce', 'loc_taxonomy_nonce_field');

		$af_mli_tax_email = get_term_meta($loc_term_id, 'af_mli_tax_email', true);

		$af_mli_tax_loc_shop_manager = get_term_meta($loc_term_id, 'af_mli_tax_loc_shop_manager', true);

		$af_mli_tax_phone = get_term_meta($loc_term_id, 'af_mli_tax_phone', true);

		$af_mli_tax_adress = get_term_meta($loc_term_id, 'af_mli_tax_adress', true);

		$af_mli_tax_city = get_term_meta($loc_term_id, 'af_mli_tax_city', true);

		$af_mli_tax_state = get_term_meta($loc_term_id, 'af_mli_tax_state', true);

		$af_mli_tax_zip_code = get_term_meta($loc_term_id, 'af_mli_tax_zip_code', true);

		$af_mli_tax_country = get_term_meta($loc_term_id, 'af_mli_tax_country', true);

		$af_mli_tax_longitude = get_term_meta($loc_term_id, 'af_mli_tax_longitude', true);

		$af_mli_tax_except_order = get_term_meta($loc_term_id, 'af_mli_tax_except_order', true);

		$af_mli_tax_open_time = get_term_meta($loc_term_id, 'af_mli_tax_open_time', true);

		$af_mli_tax_close_time = get_term_meta($loc_term_id, 'af_mli_tax_close_time', true);

		$af_mli_tax_shipping_zones = (array) get_term_meta($loc_term_id, 'af_mli_tax_shipping_zones', true);

		$af_mli_tax_shipping_methods = (array) get_term_meta($loc_term_id, 'af_mli_tax_shipping_methods', true);

		$af_mli_tax_payment_methods = (array) get_term_meta($loc_term_id, 'af_mli_tax_payment_methods', true);

		$countries_obj = new WC_Countries();

		$countries = $countries_obj->get_countries();

		$states = !empty($af_mli_tax_country) ? $countries_obj->get_states($af_mli_tax_country) : array();

		?>

		<tr class="form-field">

			<th scope="row" valign="top">
				<label><?php echo esc_html__('Location Shop Manager Name', 'addify-multi-inventory-management'); ?></label>
			</th>

			<td>

				<input type="float" name="af_mli_tax_loc_shop_manager" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr($af_mli_tax_loc_shop_manager); ?>">

			</td>

		</tr>

		<tr class="form-field">

			<th scope="row" valign="top"><label><?php echo esc_html__('Email', 'addify-multi-inventory-management'); ?></label>
			</th>

			<td>

				<input type="email" name="af_mli_tax_email" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr($af_mli_tax_email); ?>">

			</td>

		</tr>

		<tr class="form-field">

			<th scope="row" valign="top"><label><?php echo esc_html__('Phone', 'addify-multi-inventory-management'); ?></label>
			</th>

			<td>

				<input type="float" name="af_mli_tax_phone" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr($af_mli_tax_phone); ?>">

			</td>

		</tr>

		<tr class="form-field">

			<th scope="row" valign="top">
				<label><?php echo esc_html__('Address', 'addify-multi-inventory-management'); ?></label>
			</th>

			<td>

				<input type="address" name="af_mli_tax_adress" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr($af_mli_tax_adress); ?>">

			</td>

		</tr>

		<tr class="form-field">

			<th scope="row" valign="top">
				<label><?php echo esc_html__('Country', 'addify-multi-inventory-management'); ?></label>
			</th>

			<td>

				<select name="af_mli_tax_country" style="width: 95%" class="af_mli_tax_country"
					data-tax_id="<?php echo esc_attr($loc_term_id); ?>">

					<option disabled selected><?php echo esc_html('Select Option'); ?></option>

					<?php foreach ($countries as $country_key => $country_name) : ?>

						<option value="<?php echo esc_attr($country_key); ?>" <?php echo selected($country_key, $af_mli_tax_country); ?>>
							<?php echo esc_attr($country_name); ?>
						</option>

					<?php endforeach ?>

				</select>

			</td>

		</tr>

		<tr class="form-field">

			<th scope="row" valign="top"><label><?php echo esc_html__('State', 'addify-multi-inventory-management'); ?></label>
			</th>

			<td>

				<select name="af_mli_tax_state" style="width: 95%"
					class="af_mli_tax_state <?php echo esc_attr($loc_term_id); ?>af_mli_tax_state">

					<option disabled selected><?php echo esc_html('Select Option'); ?></option>

					<?php foreach ($states as $state_key => $state_name) : ?>

						<option value="<?php echo esc_attr($state_key); ?>" <?php echo selected($state_key, $af_mli_tax_state); ?>>
							<?php echo esc_attr($state_name); ?>
						</option>

					<?php endforeach ?>

				</select>

			</td>

		</tr>

		<tr class="form-field">

			<th scope="row" valign="top"><label><?php echo esc_html__('City', 'addify-multi-inventory-management'); ?></label>
			</th>

			<td>

				<input type="text" name="af_mli_tax_city" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr($af_mli_tax_city); ?>">

			</td>

		</tr>
		<tr class="form-field">

			<th scope="row" valign="top">
				<label><?php echo esc_html__('Zip Code', 'addify-multi-inventory-management'); ?></label>
			</th>
			<td>
				<input type="number" name="af_mli_tax_zip_code" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr($af_mli_tax_zip_code); ?>">

			</td>

		</tr>
		<tr class="form-field">

			<th scope="row" valign="top">
				<label><?php echo esc_html__('Latitude', 'addify-multi-inventory-management'); ?></label>
			</th>
			<td>
				<input step="any" type="number" name="af_mli_location_latitude" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr(get_term_meta($loc_term_id, 'af_mli_location_latitude', true)); ?>">

			</td>

		</tr>
		<tr class="form-field">

			<th scope="row" valign="top">
				<label><?php echo esc_html__('Longitude', 'addify-multi-inventory-management'); ?></label>
			</th>

			<td>
				<input step="any" type="text" name="af_mli_location_longitude" class="af_mli_taxonomy_fields_style"
					value="<?php echo esc_attr(get_term_meta($loc_term_id, 'af_mli_location_longitude', true)); ?>">

			</td>

		</tr>



		<tr class="form-field">

			<th scope="row" valign="top">
				<label><?php echo esc_html__('Accept order in specific time', 'addify-multi-inventory-management'); ?></label>
			</th>

			<td>

				<span>

					<input type="checkbox" name="af_mli_tax_except_order" value="yes" 
					<?php
					if ('yes' == $af_mli_tax_except_order) :
						?>
						checked <?php endif ?>>

				</span>

			</td>

		</tr>

		<tr class="form-field af_mli_loc_time">

			<th scope="row" valign="top">
				<label><?php echo esc_html__('Location Time', 'addify-multi-inventory-management'); ?></label>
			</th>

			<td>

				<span>

					<?php echo esc_html('Opens at - '); ?>

					<input type="time" name="af_mli_tax_open_time" class="af_mli_taxonomy_fields_style" style="width: 30%"
						value="<?php echo esc_attr($af_mli_tax_open_time); ?>">

					<?php echo esc_html('Closes at - '); ?>

					<input type="time" name="af_mli_tax_close_time" class="af_mli_taxonomy_fields_style" style="width: 30%"
						value="<?php echo esc_attr($af_mli_tax_close_time); ?>">

				</span>

			</td>

		</tr>

		<?php

		if ('yes' == get_option('mli_shipp_zones_to_loc')) {

			?>

			<tr class="form-field">

				<th scope="row" valign="top">
					<label><?php echo esc_html__('Shipping Zones', 'addify-multi-inventory-management'); ?></label>
				</th>

				<td>

					<select name="af_mli_tax_shipping_zones[]" class="af_mli_taxonomy_fields_style af_mli_tax_shipping_zones"
						data-tax_id="<?php echo esc_attr($loc_term_id); ?>" multiple>

						<option value=""><?php echo esc_html('Select zone'); ?></option>

						<?php

						$s_zones = WC_Data_Store::load('shipping-zone');

						$all_zones = $s_zones->get_zones();

						foreach ($all_zones as $s_zone) {

							?>

							<option value="<?php echo esc_attr($s_zone->zone_id); ?>" 
														<?php
														if (in_array($s_zone->zone_id, $af_mli_tax_shipping_zones)) :
															?>
									selected <?php endif ?>>

								<?php echo esc_attr($s_zone->zone_name); ?>

							</option>

							<?php
						}

						?>

					</select>

				</td>

			</tr>

			<?php
		}

		if ('yes' == get_option('mli_shipp_methods_to_loc')) {

			$shipping_methods = WC()->shipping->get_shipping_methods();

			$zone_value = $af_mli_tax_shipping_zones;

			$zones = WC_Shipping_Zones::get_zones();


			?>

			<tr class="form-field">

				<th scope="row" valign="top">
					<label><?php echo esc_html__('Shipping Methods', 'addify-multi-inventory-management'); ?></label>
				</th>

				<td>

					<select name="af_mli_tax_shipping_methods[]"
						class="af_mli_taxonomy_fields_style <?php echo esc_attr($loc_term_id); ?>af_mli_tax_shipping_methods af_mli_tax_shipping_methods"
						multiple>

						<?php

						foreach ($zones as $value) {

							if (in_array($value['id'], $zone_value) && $value['id'] && $value['zone_name'] && $value['shipping_methods']) {

								foreach ($value['shipping_methods'] as $shipping_methods_value) {
									$instance_id_and_shipping_id = $shipping_methods_value->id . ':' . $shipping_methods_value->instance_id;

									if (!in_array($instance_id_and_shipping_id, $af_mli_tax_shipping_methods)) {

										continue;
									}

									?>

									<option value="<?php echo esc_attr($instance_id_and_shipping_id); ?>" selected>

										<?php echo esc_attr($shipping_methods_value->method_title); ?>

									</option>

									<?php

								}
							}
						}

						?>

					</select>

				</td>

			</tr>

			<?php
		}

		if ('yes' == get_option('mli_shipp_payment_method_of_loc')) {

			?>

			<tr class="form-field">

				<th scope="row" valign="top">
					<label><?php echo esc_html__('Payment Methods', 'addify-multi-inventory-management'); ?></label>
				</th>

				<td>

					<select name="af_mli_tax_payment_methods[]" class="af_mli_taxonomy_fields_style af_mli_tax_payment_methods"
						multiple>

						<?php

						$af_payment_method = WC()->payment_gateways->get_available_payment_gateways();

						foreach ($af_payment_method as $af_p_method) {

							?>

							<option value="<?php echo esc_attr($af_p_method->id); ?>" 
														<?php
														if (in_array($af_p_method->id, $af_mli_tax_payment_methods)) :
															?>
									selected <?php endif ?>>

								<?php echo esc_attr($af_p_method->title); ?>

							</option>

							<?php

						}

						?>

					</select>

				</td>

			</tr>

			<?php
		}
	}

	public function af_save_fields_tax( $term_id, $tt_id = '', $taxonomy = '' ) {

		if ('mli_location' != $taxonomy) {

			return;
		}

		$nonce = isset($_POST['loc_taxonomy_nonce_field']) ? sanitize_text_field(wp_unslash($_POST['loc_taxonomy_nonce_field'])) : 0;

		if (!wp_verify_nonce($nonce, 'loc_taxonomy_nonce')) {

			wp_die('Failed Security Check');

		}

		$af_mli_tax_email = isset($_POST['af_mli_tax_email']) ? sanitize_text_field(wp_unslash($_POST['af_mli_tax_email'])) : '';

		update_term_meta($term_id, 'af_mli_tax_email', $af_mli_tax_email);

		$af_mli_tax_loc_shop_manager = isset($_POST['af_mli_tax_loc_shop_manager']) ? sanitize_text_field(wp_unslash($_POST['af_mli_tax_loc_shop_manager'])) : '';

		update_term_meta($term_id, 'af_mli_tax_loc_shop_manager', $af_mli_tax_loc_shop_manager);

		$af_mli_tax_phone = isset($_POST['af_mli_tax_phone']) ? sanitize_text_field(wp_unslash($_POST['af_mli_tax_phone'])) : '';

		update_term_meta($term_id, 'af_mli_tax_phone', $af_mli_tax_phone);

		$af_mli_tax_adress = isset($_POST['af_mli_tax_adress']) ? sanitize_text_field(wp_unslash($_POST['af_mli_tax_adress'])) : '';

		update_term_meta($term_id, 'af_mli_tax_adress', $af_mli_tax_adress);

		if (get_term_meta($term_id, 'af_mli_tax_adress', true) != $af_mli_tax_adress) {

			update_term_meta($term_id, 'af_mli_location_latitude', '');

			af_mli_get_lat_lng_from_address_nominatim($term_id);
		}

		$af_mli_tax_city = isset($_POST['af_mli_tax_city']) ? sanitize_text_field(wp_unslash($_POST['af_mli_tax_city'])) : '';

		update_term_meta($term_id, 'af_mli_tax_city', $af_mli_tax_city);

		$af_mli_tax_state = isset($_POST['af_mli_tax_state']) ? sanitize_text_field(wp_unslash($_POST['af_mli_tax_state'])) : '';

		update_term_meta($term_id, 'af_mli_tax_state', $af_mli_tax_state);

		$af_mli_tax_zip_code = isset($_POST['af_mli_tax_zip_code']) ? sanitize_text_field(wp_unslash($_POST['af_mli_tax_zip_code'])) : '';

		update_term_meta($term_id, 'af_mli_tax_zip_code', $af_mli_tax_zip_code);

		$af_mli_tax_country = isset($_POST['af_mli_tax_country']) ? sanitize_text_field(wp_unslash($_POST['af_mli_tax_country'])) : '';

		update_term_meta($term_id, 'af_mli_tax_country', $af_mli_tax_country);

		$af_mli_tax_except_order = isset($_POST['af_mli_tax_except_order']) ? sanitize_text_field(wp_unslash($_POST['af_mli_tax_except_order'])) : '';

		update_term_meta($term_id, 'af_mli_tax_except_order', $af_mli_tax_except_order);

		$af_mli_tax_open_time = isset($_POST['af_mli_tax_open_time']) ? sanitize_text_field(wp_unslash($_POST['af_mli_tax_open_time'])) : '';

		update_term_meta($term_id, 'af_mli_tax_open_time', $af_mli_tax_open_time);

		$af_mli_tax_close_time = isset($_POST['af_mli_tax_close_time']) ? sanitize_text_field(wp_unslash($_POST['af_mli_tax_close_time'])) : '';

		update_term_meta($term_id, 'af_mli_tax_close_time', $af_mli_tax_close_time);

		$af_mli_tax_shipping_zones = isset($_POST['af_mli_tax_shipping_zones']) ? sanitize_meta('', wp_unslash($_POST['af_mli_tax_shipping_zones']), '') : array();

		update_term_meta($term_id, 'af_mli_tax_shipping_zones', $af_mli_tax_shipping_zones);

		$af_mli_tax_shipping_methods = isset($_POST['af_mli_tax_shipping_methods']) ? sanitize_meta('', wp_unslash($_POST['af_mli_tax_shipping_methods']), '') : array();

		update_term_meta($term_id, 'af_mli_tax_shipping_methods', $af_mli_tax_shipping_methods);

		$af_mli_tax_payment_methods = isset($_POST['af_mli_tax_payment_methods']) ? sanitize_meta('', wp_unslash($_POST['af_mli_tax_payment_methods']), '') : array();

		update_term_meta($term_id, 'af_mli_tax_payment_methods', $af_mli_tax_payment_methods);



		$af_mli_location_longitude = isset($_POST['af_mli_location_longitude']) ? sanitize_text_field(wp_unslash($_POST['af_mli_location_longitude'])) : '';

		update_term_meta($term_id, 'af_mli_location_longitude', $af_mli_location_longitude);


		$af_mli_location_latitude = isset($_POST['af_mli_location_latitude']) ? sanitize_text_field(wp_unslash($_POST['af_mli_location_latitude'])) : '';
		update_term_meta($term_id, 'af_mli_location_latitude', $af_mli_location_latitude);
	}
}

new Addify_Mli_Taxonomy();