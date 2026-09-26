var af_location = addify_multi_inventory_front.af_location;

var nonce = addify_multi_inventory_front.nonce;

var ajaxurl = addify_multi_inventory_front.admin_url;
var out_of_stock_error_msg = addify_multi_inventory_front.af_mli_out_of_stock_error_msg;

jQuery(document).ready(function ($) {

	if ($('.af-remove-add-to-cart-form').length) {
		$('form.cart').remove();
	}


	jQuery(document).on('change', 'input.variation_id', function () {

		$('form.variations_form').find('.out_of_stock_msg_p ').hide('fast');

		setTimeout(function () {

			jQuery.ajax(
				{

					url: ajaxurl,

					type: 'POST',

					data: {

						action: 'af_vari_prod_inven',

						nonce: nonce,

						form_data: $('form.variations_form').serialize(),
					},

					success: function (response) {


						let new_html = '';

						if (response.data && response.data['inventories_detail'] && undefined != response.data['inventories_detail']) {

							new_html = response.data['inventories_detail'];

						}

						$('.af_mli_show_i').html(new_html);

						if (response.data && response.data['hide_add_to_cart'] && undefined != response.data['hide_add_to_cart']) {

							$('form.variations_form').find('div.quantity , .single_add_to_cart_button').hide();

							if (response.data && response.data['error'] && undefined != response.data['error']) {

								$('form.variations_form').find('.out_of_stock_msg_p').text(response.data['error']);
								$('form.variations_form').find('.out_of_stock_msg_p').show();
							}

							return;

						} else {
							$('form.variations_form').find('div.quantity , .single_add_to_cart_button').show();

						}

						interventory_and_item_type();

						select_highest_Stock_option();
						// 						$('.af_mli_front_stock_div').show();


					}
				});

		}, 700);
	}
	);


	jQuery(document).on('change click', '.af_mli_inven_selector', interventory_and_item_type);

	function isCurrentTimeInRange(startTime, endTime) {

		var currentTime = new Date();

		var currentHours = currentTime.getHours();

		var currentMinutes = currentTime.getMinutes();

		var formattedCurrentTime = currentHours + ':' + (currentMinutes < 10 ? '0' : '') + currentMinutes;

		if (startTime > endTime) {

			return formattedCurrentTime >= startTime || formattedCurrentTime <= endTime;

		} else {

			return formattedCurrentTime >= startTime && formattedCurrentTime <= endTime;
		}
	}

	function isCurrentDateInRange(startDate, endDate) {

		var currentDate = new Date();

		var formattedCurrentDate = currentDate.toISOString().split('T')[0];

		return formattedCurrentDate >= startDate && formattedCurrentDate <= endDate;
	}
	interventory_and_item_type();

	function interventory_and_item_type() {


		let show_table_pricing = false;

		if ($('select.af_mli_inven_selector').length) {

			current_btn = $('select.af_mli_inven_selector').find('option:selected');

			if ($('select.af_mli_inven_selector option').length <= 1) {
				$('.af_mli_front_stock_div , .af_main_total_div').hide();
			} else {
				$('select.af_mli_inven_selector option').each(function () {
					if (($(this).data('regular_price') != undefined && $(this).data('regular_price') >= 1) || ($(this).data('sale_price') != undefined && $(this).data('sale_price') >= 1)) {
						show_table_pricing = true;
					}
				});
			}

		} else {

			current_btn = $('.af_mli_inven_selector:checked');

			if ($('.af_mli_inven_selector').length <= 1) {
				$('.af_mli_front_stock_div , .af_main_total_div').hide();
			} else {
				$('input.af_mli_inven_selector').each(function () {

					if (($(this).data('regular_price') != undefined && $(this).data('regular_price') >= 1) || ($(this).data('sale_price') != undefined && $(this).data('sale_price') >= 1)) {
						show_table_pricing = true;
					}
				});
			}
		}

		if (show_table_pricing) {
			$('.af_main_total_div').show();
		} else {
			$('.af_main_total_div').hide();
		}


		var total_stock = current_btn.attr('data-total_stock');
		var inventory_name = current_btn.attr('data-name');

		let out_of_stock_current_Error_msg = out_of_stock_error_msg;
		out_of_stock_current_Error_msg = out_of_stock_current_Error_msg.replace("{inventory_name}", inventory_name);

		var inventory_date = current_btn.attr('data-date');
		var expiry_date = current_btn.attr('data-exp_date');
		var open_time = current_btn.attr('data-open_time');
		var close_time = current_btn.attr('data-close_time');

		var flag = false;

		// $( '.af_prod_name_class .product_price' ).html( currency_symbol + ' ' + price );

		// $( '.af_prod_name_class .product_subtotal' ).html( currency_symbol + ' ' + price );

		// $( '.af_prod_name_class .product_name' ).html( name );

		if (total_stock == 0) {

			$('.out_of_stock_msg_p').fadeIn('fast');
			$('.out_of_stock_msg_p').html(out_of_stock_current_Error_msg);

			flag = true;

		} else {

			$('.out_of_stock_msg_p').fadeOut('fast');
		}

		if (open_time && close_time) {

			if (isCurrentTimeInRange(open_time, close_time)) {

				$('.time_msg_p').fadeOut('fast');

			} else {

				$('.time_msg_p').fadeIn('fast');

				flag = true;
			}

		} else {

			$('.time_msg_p').fadeOut('fast');
		}

		if (inventory_date && expiry_date) {

			if (isCurrentDateInRange(inventory_date, expiry_date)) {

				$('.date_msg_p').fadeOut('fast');

			} else {

				$('.date_msg_p').fadeIn('fast');

				flag = true;
			}

		} else {

			$('.date_msg_p').fadeOut('fast');
		}

		$('form.cart').find('.single_add_to_cart_button').prop('disabled', flag);

		var locationName = current_btn.data('name');
		$('.af_product_name_dynamic .product_name').text(locationName);

		var locationPrice = current_btn.data('formatted_price');
		$('.af_product_name_dynamic .product_price').html(locationPrice);

	}



	jQuery(document).on('submit', 'form.cart', function (e) {
		let valid = true; // Track validity of inputs

		// Validate dropdown
		if ($('select.af_mli_inven_selector_radio').length && !$('select.af_mli_inven_selector_radio').val()) {
			valid = false;
		}

		// Validate radio buttons
		if ($('input.af_mli_inven_selector_radio').length && !$('input.af_mli_inven_selector_radio:checked').val()) {

			valid = false;
		}

		// Optionally handle further processing if valid
		if (!valid) {
			$('select.af_mli_inven_selector_radio , input.af_mli_inven_selector_radio').first().focus();
			e.preventDefault();
		}
	});



	select_highest_Stock_option();
	function select_highest_Stock_option() {
		if (addify_multi_inventory_front.auto_select_higest_stock && 'yes' == addify_multi_inventory_front.auto_select_higest_stock) {

			let highest_value = 0
			let selected_select = null;
			if ($('select.af_mli_inven_selector').length) {

				$('select.af_mli_inven_selector option').each(function () {

					if ($(this).data('total_stock') && $(this).data('total_stock') > highest_value) {

						highest_value = $(this).data('total_stock');
						selected_select = $(this);
					}

				});


				if (selected_select) {
					selected_select.prop('selected', true);
				}
			}

			if ($('input.af_mli_inven_selector_radio').length) {

				$('input.af_mli_inven_selector_radio').each(function () {

					if ($(this).data('total_stock') && $(this).data('total_stock') > highest_value) {

						highest_value = $(this).data('total_stock');
						selected_select = $(this);

					}

				});

				if (selected_select) {
					selected_select.prop('checked', true).focus();
				}

			}

			interventory_and_item_type();
		}
	}

	// Function to open popup with animation.
	$(document).on('click', '#af-mi-select-location-btn', function (e) {
		e.preventDefault();
		$('.af-mi-popup-content').addClass('active');
		$('.af-mi-popup-overlay').fadeIn('slow');

	});

	// Function to close popup.
	$(document).on('click', '#af-mi-popup-close', function (e) {
		e.preventDefault();
		$('.af-mi-popup-content').removeClass('active');
		$('.af-mi-popup-overlay').fadeOut('slow');
	});

	let menu_html = addify_multi_inventory_front.menu_html;
	$('.af-mi-nearest-location').remove();

	$('.menu ul , ul.nav-menu').append(menu_html);
	$('.af_loc_widget').select2(
		{
			placeholder: 'select location'
		}
	);

	let ajax_call_ajainst_cart_key = [];

	for (let index = 1; index < 10; index++) {


		setTimeout(function () {
			$('.wc-block-components-notices').find('.af-mli-cart-page-change-location-div').remove();

			$('.af-mli-cart-page-change-location-div').each(function () {

				if ($(this).data('cart_item_key')) {

					let cart_item_key = $(this).data('cart_item_key');

					$('.af-mli-cart-page-change-location-div' + cart_item_key).each(function () {
						if ($('.af-mli-cart-page-change-location-div' + cart_item_key).closest('.wc-block-components-notice-banner').length) {
							$(this).remove();
						}
					});
					$('.af-mli-cart-page-change-location-div' + cart_item_key).not(':first').remove();
					$('.af-mli-cart-page-change-location-div' + cart_item_key).show();
					// console.log('af-mli-cart-page-change-location-div' + cart_item_key);
					// console.log($('.af-mli-cart-page-change-location-div' + cart_item_key).closest('.wc-block-components-notice-banner').length);

					if (!$('.af-mli-cart-page-change-location-div' + cart_item_key).closest('.wc-block-components-notice-banner').length && !ajax_call_ajainst_cart_key.includes(cart_item_key)) {

						ajax_call_ajainst_cart_key.push(cart_item_key);

						get_location_form_cart_against_current_cart_id(cart_item_key);
					}

				}

			});

		}, index * 1000);
	}


	jQuery(document).on('click', '.af-mli-cart-page-change-location , .af-mli-cart-page-change-location-div', function (e) {

		e.preventDefault();

		if ($(this).closest('.af-mli-cart-page-change-location-div').data('cart_item_key')) {

			let cart_key = $(this).closest('.af-mli-cart-page-change-location-div').data('cart_item_key');

			if (!$('.af-mli-cart-page-change-location-div' + cart_key).closest('.wc-block-components-notice-banner').length && !ajax_call_ajainst_cart_key.includes(cart_key)) {

				ajax_call_ajainst_cart_key.push(cart_item_key);

				get_location_form_cart_against_current_cart_id(cart_item_key);
			}
		}


	});

	function get_location_form_cart_against_current_cart_id(cart_key) {

		jQuery.ajax(
			{
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'af_mli_update_location_in_cart',
					nonce: nonce,
					cart_item_key: cart_key,
				},

				success: function (response) {

					$('.af-mli-cart-page-change-location-div' + cart_key).html(response);

				}
			});
	}

	jQuery(document).on('click', '.af-mli-set-new-location', function (e) {

		e.preventDefault();

		if ($(this).data('cart_item_key')) {

			let cart_item_key = $(this).data('cart_item_key');

			if (!$('.af-mli-cart-page-select-location' + cart_item_key).val()) {
				return;
			}

			jQuery.ajax(
				{
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'af_mli_update_location_of_cart_item',
						nonce: nonce,
						cart_item_key: cart_item_key,
						new_location: $('.af-mli-cart-page-select-location' + cart_item_key).val(),
					},

					success: function (response) {

						console.log(response);

						if (response.data && (response.data.reload || response.data['reload'])) {
							window.location.reload(true);
						}


					}
				});
		}


	});

	$(document).on('click', '.af-mli-select-location-from-cart-show-popup', function (e) {
		e.preventDefault();
		$(this).closest('.af-mli-cart-page-change-location-div').find('.af-mli-select-location-from-cart-contianer').show();
	});

	$(document).on('click', '.af-mli-cross-icon', function () {
		$(this).closest('.af-mli-select-location-from-cart-contianer').hide();
	});

});
