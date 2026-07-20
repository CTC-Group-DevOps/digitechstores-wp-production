var ajaxurl = addify_multi_inventory.admin_url;

var nonce = addify_multi_inventory.nonce;

var chart_data = addify_multi_inventory.chart_array ? addify_multi_inventory.chart_array : [];

var colors = addify_multi_inventory.colors;

var over_stock = addify_multi_inventory.over_stock;

var af_field_border_radius = addify_multi_inventory.af_field_border_radius;

var af_mli_display_field_padding = addify_multi_inventory.af_mli_display_field_padding;

var af_mli_gen_excl_loc_front_end = addify_multi_inventory.af_mli_gen_excl_loc_front_end;

var af_mli_gen_backend_mode_only_rules = addify_multi_inventory.af_mli_gen_backend_mode_only_rules;

var low_stock = parseInt(addify_multi_inventory.low_stock);

var empty_stock = parseInt(addify_multi_inventory.empty_stock);


google.charts.load('current', { 'packages': ['corechart'] });

// google.charts.setOnLoadCallback(drawChart);

function _manage_stock() {

	if (jQuery('#_manage_stock').is(':checked')) {

		jQuery(' .prod_level_inven_checkbox_class ').fadeIn('fast');

		if (jQuery('.prod_level_inven').is(':checked')) {

			jQuery('.prod_level_inven_divs').fadeIn('fast');

		} else {

			jQuery('.prod_level_inven_divs').fadeOut('fast');
		}

	} else {

		jQuery(' .prod_level_inven_checkbox_class , .prod_level_inven_divs ').fadeOut('fast');
	}
}

jQuery(document).ready(

	function ($) {

		$(document).on('submit', 'form.af_ms_all__filter_form', function (e) {

			e.preventDefault();

			let base_url = addify_multi_inventory.manage_stock_tab + '&' + $(this).serialize();

			window.location.href = base_url;

		});

		$(document).on('click', '#af_reset', function () {

			window.location.href = addify_multi_inventory.manage_stock_tab;

		});

		$(document).on(

			'click',

			'#_manage_stock',

			function () {

				_manage_stock();
			}
		);

		_manage_stock();

		$(document).on(

			'click',

			'.af_mli_ms_checkbox',

			function (e) {

				let selected_checkboxes = [];

				af_mli_hide_column_on_checkbox($);

				$('.af_mli_ms_checkbox:checked').each(function () {

					selected_checkboxes.push($(this).val());

				});

				jQuery.ajax(

					{
						url: ajaxurl,

						type: 'POST',

						data: {

							action: 'af_ms_update_optn',

							nonce: nonce,

							selected_checkboxes: selected_checkboxes,

						},
					}
				);
			}
		);

		$(document).on(

			'click',

			'.prod_level_inven',

			function () {

				prod_level_inven();
			}
		);

		// prod_level_inven();

		function prod_level_inven() {

			if ($('.prod_level_inven').is(':checked')) {

				$('.prod_level_inven_divs').fadeIn('fast');

			} else {

				$('.prod_level_inven_divs').fadeOut('fast');
			}
		}

		$(document).on(

			'click',

			'.af_variable_type',

			function () {

				var prod_id = $(this).data('prod_id');


				$('.af_mli_location_of' + prod_id).each(function () {

					if ($(this).closest('tr').is(':visible')) {

						$(this).closest('tr').fadeOut('fast');

					} else {

						$(this).closest('tr').fadeIn('fast');
					}

				});
			}
		);

		$(document).ready(function () {
			$('tr:has(input[type="hidden"].af_mli_inven_id)').hide();

			// $('.af_variable_type').on('click', function () {

			// var $row = $('tr').has('input[type="hidden"].af_mli_inven_id');

			// if ($row.is(':visible')) {
			// 	$row.fadeOut('fast');
			// } else {
			// 	$row.fadeIn('fast');
			// }
			// });
		});






		$(document).on(

			'click',

			'input[name="af_mli_tax_except_order"]',

			function () {

				if ($(this).is(':checked')) {

					$('.af_mli_loc_time').fadeIn();

				} else {

					$('.af_mli_loc_time').fadeOut();
				}

			}
		);

		$(document).on(

			'click',

			'.in_add_price',

			function () {

				var current_inventory_id = $(this).data('current_inventory_id')

				if ($('.' + current_inventory_id + 'in_add_price').is(':checked')) {

					$('.' + current_inventory_id + 'af_mli_prd_lvl_inven_price_p').fadeIn();

					$('.' + current_inventory_id + 'af_mli_prd_lvl_inven_sale_price_p').fadeIn();

				} else {

					// $('.' + current_inventory_id + 'af_mli_prd_lvl_inven_price_p').fadeOut();

					// $('.' + current_inventory_id + 'af_mli_prd_lvl_inven_sale_price_p').fadeOut();
				}

				make_readonly_woocommerce_qty_box();

			}
		);

		if ($('input[name="af_mli_tax_except_order"]').is(':checked')) {

			$('.af_mli_loc_time').fadeIn();

		} else {

			$('.af_mli_loc_time').fadeOut();

		}

		jQuery('#display_field_border_radius').closest('td').html(af_field_border_radius);

		jQuery('#display_field_padding').closest('td').html(af_mli_display_field_padding);

		jQuery('#mli_gen_backend_mode_only_rules').closest('td').html(af_mli_gen_backend_mode_only_rules);

		$(".af_m_s_chk_js").each(

			function () {

				var prod_id = $(this).data('prod_id');

				field_disabled(prod_id, $);

			}
		);

		$(document).on(

			'click',

			".af_m_s_chk_js",

			function () {

				var prod_id = $(this).data('prod_id');

				field_disabled(prod_id, $);

			}
		);

		$(document).on(

			'click',

			"#display_heading_display",

			function (e) {

				if ($(this).is(':checked')) {

					$('#display_heading').closest('tr').fadeIn('fast');

					$('#display_heading_color').closest('tr').fadeIn('fast');

					$('#af_mli_loc_ptn_heading_title').closest('tr').fadeIn('fast');

				} else {

					$('#display_heading').closest('tr').fadeOut('fast');

					$('#display_heading_color').closest('tr').fadeOut('fast');

					$('#af_mli_loc_ptn_heading_title').closest('tr').fadeOut('fast');

				}
			}
		);

		$(document).on(

			'click',

			"#display_field_border",

			function (e) {

				if ($(this).is(':checked')) {

					$('#display_field_border_color').closest('tr').fadeIn('fast');

					$('.display_field_border_radius').closest('tr').fadeIn('fast');

					$('.display_field_padding').closest('tr').fadeIn('fast');

				} else {

					$('#display_field_border_color').closest('tr').fadeOut('fast');

					$('.display_field_border_radius').closest('tr').fadeOut('fast');

					$('.display_field_padding').closest('tr').fadeOut('fast');

				}
			}
		);

		$(document).on(

			'click',

			"#mli_gen_backend_mode_only",

			function (e) {

				if ($(this).is(':checked')) {

					$('#mli_gen_backend_mode_only_rules').closest('tr').fadeIn('fast');

				} else {

					$('#mli_gen_backend_mode_only_rules').closest('tr').fadeOut('fast');

				}
			}
		);

		$(document).ready(

			function () {

				if ($('#display_heading_display').is(':checked')) {

					$('#display_heading').closest('tr').fadeIn('fast');

					$('#display_heading_color').closest('tr').fadeIn('fast');

					$('#af_mli_loc_ptn_heading_title').closest('tr').fadeIn('fast');

				} else {

					$('#display_heading').closest('tr').fadeOut('fast');

					$('#display_heading_color').closest('tr').fadeOut('fast');

					$('#af_mli_loc_ptn_heading_title').closest('tr').fadeOut('fast');

				}

				if ($('#display_field_border').is(':checked')) {

					$('#display_field_border_color').closest('tr').fadeIn('fast');

					$('.display_field_border_radius').closest('tr').fadeIn('fast');

					$('.display_field_padding').closest('tr').fadeIn('fast');

				} else {

					$('#display_field_border_color').closest('tr').fadeOut('fast');

					$('.display_field_border_radius').closest('tr').fadeOut('fast');

					$('.display_field_padding').closest('tr').fadeOut('fast');

				}

				if ($('#mli_gen_backend_mode_only').is(':checked')) {

					$('#mli_gen_backend_mode_only_rules').closest('tr').fadeIn('fast');

				} else {

					$('#mli_gen_backend_mode_only_rules').closest('tr').fadeOut('fast');

				}
			}
		);

		$(document).ajaxComplete(

			function (event, xhr, settings) {

				if (settings.data && settings.data.toLowerCase().includes('woocommerce_load_variations')) {

					$('.fa-sort-up').hide();

					$('.fa-sort-down').show();

					$('.af_mli_prd_lvl_loc_p').hide();

					$('.af_mli_prd_lvl_priority_p').hide();

					$('.af_mli_prd_lvl_sku_p').hide();

					$('.af_mli_prd_lvl_inven_date_p').hide();

					$('.af_mli_prd_lvl_inven_expiry_date_p').hide();

					$('.af_mli_prd_lvl_inven_add_price_p').hide();

					$('.af_mli_prd_lvl_inven_price_p').hide();

					$('.af_mli_prd_lvl_inven_sale_price_p').hide();

					$('.af_mli_prd_lvl_stock_quantity_p').hide();

					$('.af_mli_prd_lvl_low_stock_threshold_p').hide();

					$(document).on('click', '.var_level_inven', var_level_inven);

					$(document).on(

						'click',

						'.variable_manage_stock',

						function () {

							variable_manage_stock();
						}
					);

					variable_manage_stock();

					function variable_manage_stock() {

						if (jQuery('.variable_manage_stock').is(':checked')) {

							jQuery('.var_prod_level_inven_class, .var_level_inven_divs').fadeIn('fast');

							if ($('.var_level_inven').is(':checked')) {

								$('.var_level_inven_divs').fadeIn('fast');

							} else {

								$('.var_level_inven_divs').fadeOut('fast');
							}

						} else {

							jQuery('.var_prod_level_inven_class, .var_level_inven_divs').fadeOut('fast');
						}
					}


				}

			}
		);

		var_level_inven();

		function var_level_inven() {

			jQuery('.var_level_inven').each(function () {

				if (jQuery(this).is(':checked')) {

					jQuery(this).closest('.woocommerce_variation').find('.var_level_inven_divs').fadeIn('fast');

				} else {

					jQuery(this).closest('.woocommerce_variation').find('.var_level_inven_divs').fadeOut('fast');
				}

			});
		}

		jQuery(
			function ($) {

				jQuery('.af_supp_assign_to').select2(
					{
						multiple: true,

						placeholder: 'Select User',
					}
				);

				jQuery('.af_mli_tax_country').select2();

				jQuery('.af_mli_tax_state').select2();

				jQuery('.af_supp_location').select2(
					{
						multiple: true,

						placeholder: 'Select Location',
					}
				);

				jQuery('.af_dash_filter_warehouse').select2(
					{
						multiple: true,

						placeholder: 'Warehouse',
					}
				);

				jQuery('.af_mli_tax_shipping_zones').select2(
					{

						multiple: true,

						placeholder: 'Choose Zones'
					}
				);

				jQuery('.af_mli_tax_shipping_methods').select2(
					{

						multiple: true,

						placeholder: 'Choose Methods'
					}
				);

				jQuery('.af_mli_tax_payment_methods').select2(
					{

						multiple: true,

						placeholder: 'Choose Methods'
					}
				);

				jQuery('.mli_gen_exclude_locations').select2();

				af_mli_prod_search($);
			}
		);

		$(document).on(

			'click',

			".af_mli_po_add_pro_btn",

			function (e) {

				e.preventDefault();

				$('.af_mli_po_popup').fadeIn('slow');

				$('.af_mli_po_popup_wrapper').fadeIn('slow');
			}
		);

		$(document).on(

			'click',

			".fa-times",

			function (e) {

				e.preventDefault();

				$('.af_mli_po_popup').fadeOut('slow');

				$('.af_mli_po_popup_wrapper').fadeOut('slow');
			}
		);

		$(document).on(

			'click',

			".af_mli_prd_lvl_add_new_inventory",

			function (e) {

				e.preventDefault();

				$('.af_model_content').fadeIn('fast');

				$('.new_inventory_modal').fadeIn('fast');
			}
		);

		$(document).on(

			'click',

			".af_model_close",

			function (e) {

				e.preventDefault();

				$('.af_model_content').fadeOut('fast');

				$('.new_inventory_modal').fadeOut('fast');
			}
		);

		$(document).on(

			'click',

			".af_add_cancel",

			function (e) {

				e.preventDefault();

				$('.af_model_content').fadeOut('fast');

				$('.new_inventory_modal').fadeOut('fast');
			}
		);

		$(document).on(

			'click',

			".fa-sort-up",

			function (e) {

				e.preventDefault();

				var current_inventory_id = $(this).data('inven_id');

				$('.' + current_inventory_id + 'fa-sort-up').hide();

				$('.' + current_inventory_id + 'fa-sort-down').show();

				$('.' + current_inventory_id + 'af_mli_prd_lvl_priority_p').slideUp('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_sku_p').slideUp('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_inven_date_p').slideUp('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_inven_expiry_date_p').slideUp('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_inven_price_p').slideUp('slow');

				// $('.' + current_inventory_id + 'af_mli_prd_lvl_inven_add_price_p').slideUp('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_inven_sale_price_p').slideUp('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_stock_quantity_p').slideUp('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_low_stock_threshold_p').slideUp('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_loc_p').slideUp('slow');
			}
		);

		$(document).on(

			'click',

			".fa-sort-down",

			function (e) {

				e.preventDefault();

				var current_inventory_id = $(this).data('inven_id');

				$('.' + current_inventory_id + 'fa-sort-up').show();

				$('.' + current_inventory_id + 'fa-sort-down').hide();

				$('.' + current_inventory_id + 'af_mli_prd_lvl_priority_p').slideDown('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_sku_p').slideDown('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_inven_date_p').slideDown('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_inven_expiry_date_p').slideDown('slow');

				// $('.' + current_inventory_id + 'af_mli_prd_lvl_inven_add_price_p').slideDown('slow');

				if ($('.' + current_inventory_id + 'in_add_price').is(':checked')) {

					$('.' + current_inventory_id + 'af_mli_prd_lvl_inven_price_p').show();

					$('.' + current_inventory_id + 'af_mli_prd_lvl_inven_sale_price_p').show();

				} else {

					// $('.' + current_inventory_id + 'af_mli_prd_lvl_inven_price_p').hide();

					// $('.' + current_inventory_id + 'af_mli_prd_lvl_inven_sale_price_p').hide();
				}

				$('.' + current_inventory_id + 'af_mli_prd_lvl_loc_p').slideDown('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_stock_quantity_p').slideDown('slow');

				$('.' + current_inventory_id + 'af_mli_prd_lvl_low_stock_threshold_p').slideDown('slow');
			}
		);

		$(document).on(

			'click',

			".af_mli_remove_inventory_btn",

			function (e) {

				e.preventDefault();

				var current_prod_id = $(this).data('current_prod_id');

				var remove_inventory_id = $(this).data('current_inventory_id');

				var button = $(this);

				if (confirm("Are You Sure About this?")) {

					jQuery.ajax(
						{
							url: ajaxurl,

							type: 'POST',

							data: {

								action: 'af_pord_inven_remove',

								nonce: nonce,

								current_prod_id: current_prod_id,

								remove_inventory_id: remove_inventory_id,

							},

							success: function (data) {

								button.closest('.af_mli_class').remove();

								$('.fa-sort-down').show();

								$('.fa-sort-up').hide();

							}
						}
					);
				}
			}
		);

		$(document).on(

			'click',

			".af_mli_po_remove_btn",

			function (e) {

				e.preventDefault();

				if (confirm("Are You Sure About this?")) {

					$(this).closest('tr').remove();

				}
			}
		);

		$(document).on(

			'click',

			".af_mli_expand_all_btn",

			function (e) {

				e.preventDefault();

				$('.fa-sort-up').show();

				$('.fa-sort-down').hide();

				$('.af_mli_prd_lvl_priority_p').slideDown('slow');

				$('.af_mli_prd_lvl_sku_p').slideDown('slow');

				$('.af_mli_prd_lvl_inven_date_p').slideDown('slow');

				$('.af_mli_prd_lvl_inven_expiry_date_p').slideDown('slow');

				// $('.af_mli_prd_lvl_inven_add_price_p').slideDown('slow');

				var current_inventory_id = $('.inven_hidden_id').val();

				if ($('.' + current_inventory_id + 'in_add_price').is(':checked')) {

					// $('.' + current_inventory_id + 'af_mli_prd_lvl_inven_price_p').fadeIn();

					// $('.' + current_inventory_id + 'af_mli_prd_lvl_inven_sale_price_p').fadeIn();


					$('.af_mli_prd_lvl_inven_price_p').fadeIn();

					$('.af_mli_prd_lvl_inven_sale_price_p').fadeIn();


				} else {

					// $('.' + current_inventory_id + 'af_mli_prd_lvl_inven_price_p').fadeOut();

					// $('.' + current_inventory_id + 'af_mli_prd_lvl_inven_sale_price_p').fadeOut();

				}

				$('.af_mli_prd_lvl_loc_p').slideDown('slow');

				$('.af_mli_prd_lvl_stock_quantity_p').slideDown('slow');

				$('.af_mli_prd_lvl_low_stock_threshold_p').slideDown('slow');

			}
		);

		$(document).on(

			'click',

			".af_mli_close_all_btn",

			function (e) {

				e.preventDefault();

				$('.fa-sort-up').hide();

				$('.fa-sort-down').show();

				$('.af_mli_prd_lvl_priority_p').slideUp('slow');

				$('.af_mli_prd_lvl_sku_p').slideUp('slow');

				$('.af_mli_prd_lvl_inven_date_p').slideUp('slow');

				$('.af_mli_prd_lvl_inven_expiry_date_p').slideUp('slow');

				$('.af_mli_prd_lvl_inven_price_p').slideUp('slow');

				// $('.af_mli_prd_lvl_inven_add_price_p').slideUp('slow');

				$('.af_mli_prd_lvl_inven_sale_price_p').slideUp('slow');

				$('.af_mli_prd_lvl_stock_quantity_p').slideUp('slow');

				$('.af_mli_prd_lvl_low_stock_threshold_p').slideUp('slow');

				$('.af_mli_prd_lvl_loc_p').slideUp('slow');
			}
		);

		// $( '#_regular_price' ).attr( 'disabled', 'disabled' );

		// $( '#_sale_price' ).attr( 'disabled', 'disabled' );

		$('.fa-sort-up').hide();

		$('.fa-sort-down').show();

		$('.af_mli_prd_lvl_loc_p').hide();

		$('.af_mli_prd_lvl_priority_p').hide();

		$('.af_mli_prd_lvl_sku_p').hide();

		$('.af_mli_prd_lvl_inven_date_p').hide();

		$('.af_mli_prd_lvl_inven_expiry_date_p').hide();

		$('.af_mli_prd_lvl_inven_add_price_p').hide();

		$('.af_mli_prd_lvl_inven_price_p').hide();

		$('.af_mli_prd_lvl_inven_sale_price_p').hide();

		$('.af_mli_prd_lvl_stock_quantity_p').hide();

		$('.af_mli_prd_lvl_low_stock_threshold_p').hide();

		$(document).on(

			'click',

			'.af_add_call_ajax',

			function (e) {

				e.preventDefault();

				var current_rule_id = $(this).data('current_rule_id');

				var prod_type = $(this).data('prod_type');

				var inventory_name = $(this).closest('.af_model_content').find('.af_new_invent_name').val();

				jQuery.ajax(
					{
						url: ajaxurl,

						type: 'POST',

						data: {

							current_rule_id: current_rule_id,

							action: 'af_prd_lvl_add_inventory',

							nonce: nonce,

							inventory_name: inventory_name,

							prod_type: prod_type,

						},

						success: function (data) {

							$('.af_mli_reload_inventory_div').append(data);

							$('.new_inventory_modal').fadeOut('fast');

							var inven_id = $('.prd_lvl_inven_hidden').val();

							$('.' + inven_id + 'fa-sort-down').hide();

							$('.' + inven_id + 'af_model_content').fadeOut('fast');

							$('.' + inven_id + 'fa-sort-up').show();

							$('.' + inven_id + 'af_mli_prd_lvl_loc_p').show();

							$('.' + inven_id + 'af_mli_prd_lvl_priority_p').show();

							$('.' + inven_id + 'af_mli_prd_lvl_sku_p').show();

							$('.' + inven_id + 'af_mli_prd_lvl_inven_date_p').show();

							$('.' + inven_id + 'af_mli_prd_lvl_inven_expiry_date_p').show();

							// $('.' + inven_id + 'af_mli_prd_lvl_inven_add_price_p').show();

							if ($('.' + inven_id + 'in_add_price').is(':checked')) {

								$('.' + inven_id + 'af_mli_prd_lvl_inven_price_p').fadeIn();

								$('.' + inven_id + 'af_mli_prd_lvl_inven_sale_price_p').fadeIn();

							} else {

								// $('.' + inven_id + 'af_mli_prd_lvl_inven_price_p').fadeOut();

								// $('.' + inven_id + 'af_mli_prd_lvl_inven_sale_price_p').fadeOut();
							}

							$('.' + inven_id + 'af_mli_prd_lvl_stock_quantity_p').show();

							$('.' + inven_id + 'af_mli_prd_lvl_low_stock_threshold_p').show();

							$('af_new_invent_name').val('');


							make_readonly_woocommerce_qty_box();

						}
					}
				);
			}
		);

		jQuery(document).ready(function ($) {
			$(document).on('change', ".af_mli_ms_checkbox", function () {
				var value = this.value;

				var checked;
				if (this.checked) {
					$(".column-" + value).css("display", "revert");
				} else {
					$(".column-" + value).css("display", "none");

				}
			})
		})

		jQuery(document).ready(function ($) {
			$(".af_mli_ms_checkbox").each(function () {
				var value = this.value;

				var checked;
				if (this.checked) {
					$(".column-" + value).css("display", "revert");
				} else {
					$(".column-" + value).css("display", "none");

				}
			})
		})

		$(document).on(

			'click',

			".prd_sku",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_sku_column').show();
					$('.column-p_sku').show();




				} else {

					$('.af_mli_sku_column').hide();
					$('.column-p_sku').hide();


				}
			}
		);

		$(document).on(

			'click',

			".prd_name",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_name_column').show();
					$('.column-p_name').show();



				} else {

					$('.af_mli_name_column').hide();
					$('.column-p_name').hide();


				}
			}
		);

		$(document).on(

			'click',

			".prd_thumb",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_thumb_column').show();

				} else {

					$('.af_mli_thumb_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".prd_tax_status",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_tax_status_column').show();

				} else {

					$('.af_mli_tax_status_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".prd_tax_class",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_tax_class_column').show();

				} else {

					$('.af_mli_tax_class_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".prd_shipping_class",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_ship_class_column').show();

				} else {

					$('.af_mli_ship_class_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".prd_price",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_price_column').show();

				} else {

					$('.af_mli_price_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".prd_sale_price",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_sale_price_column').show();

				} else {

					$('.af_mli_sale_price_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".prd_weight",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_weight_column').show();

				} else {

					$('.af_mli_weight_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".inven_name",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_inven_name_column').show();

				} else {

					$('.af_mli_inven_name_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".inven_manage_stock",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_manage_stock_column').show();

				} else {

					$('.af_mli_manage_stock_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".inven_stock_quan",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_stock_quan_column').show();

				} else {

					$('.af_mli_stock_quan_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".inven_low_stock_threshold",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_low_stock_thres_column').show();

				} else {

					$('.af_mli_low_stock_thres_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".inven_allow_back_order",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_backorder_column').show();

				} else {

					$('.af_mli_backorder_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".inven_stock_status",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_stock_status_column').show();

				} else {

					$('.af_mli_stock_status_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".inven_sold_individually",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_sold_individ_column').show();

				} else {

					$('.af_mli_sold_individ_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".inven_location",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_loc_column').show();

				} else {

					$('.af_mli_loc_column').hide();

				}
			}
		);

		$(document).on(

			'click',

			".stock_update_history",

			function (e) {

				if ($(this).is(':checked')) {

					$('.af_mli_history_column').show();

				} else {

					$('.af_mli_history_column').hide();

				}
			}
		);

		af_mli_hide_column_on_checkbox($);

		af_mli_prod_page_inven($);

		mli_gen_restrict_user($)

		$(document).on(

			'click',

			"#af_mli_prod_page_inven",

			function () {

				af_mli_prod_page_inven($);

			}
		);

		$(document).on(

			'click',

			"#mli_gen_restrict_user",

			function () {

				mli_gen_restrict_user($);

			}
		);

		$(document).on(

			'click',

			".af_filter_date_graph",

			function (e) {


				e.preventDefault();
				af_dash_graph_filter();

			}
		);

		af_dash_graph_filter();
		function af_dash_graph_filter() {
			var current_btn = $('.af_filter_date_graph');

			jQuery.ajax(
				{

					url: ajaxurl,

					type: 'POST',

					data: {

						action: 'af_dash_graph_filter',
						form_data: current_btn.closest('form.af_dash_form').serialize(),
						af_dash_filter_warehouse: $('.af_dash_filter_warehouse').val(),
						nonce: nonce,

					},

					success: function (data) {

						if (data['date_error']) {

							jQuery('body').append(data['new_html']);

							return;
						}

						chart_data = data.chart_data;

						google.charts.load('current', { 'packages': ['corechart'] });

						google.charts.setOnLoadCallback(drawChart);

					}
				}
			);
		}

		$(document).on(

			'click',

			".af_refresh_date_graph",

			function (e) {

				e.preventDefault();


				var today = new Date();
				// Format today's date as "MM/DD/YYYY"
				var todayFormatted = (("0" + (today.getMonth() + 1)).slice(-2)) + "/" + (("0" + today.getDate()).slice(-2)) + "/" + today.getFullYear();

				// Subtract 30 days from current date
				var thirtyDaysAgo = new Date();
				thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

				// Format date 30 days ago as "MM/DD/YYYY"
				var thirtyDaysAgoFormatted = (("0" + (thirtyDaysAgo.getMonth() + 1)).slice(-2)) + "/" + (("0" + thirtyDaysAgo.getDate()).slice(-2)) + "/" + thirtyDaysAgo.getFullYear();

				$('.af_dash_filter_date_from_input').val(todayFormatted);

				$('.af_dash_filter_date_to_input').val(thirtyDaysAgoFormatted);

				$('.af_dash_filter_warehouse').val('');

				$('.af_dash_filter_warehouse').select2();

				af_dash_graph_filter();
			}
		);

		$(document).on(

			'click',

			".af_ok_call_ajax",

			function (e) {

				$('.new_dashboard_modal').remove();
			}
		);

		$(document).on(

			'click',

			'.af_prod_name',

			function (e) {

				$(this).attr('href');

				if (!$(this).attr('href')) {

					e.preventDefault();
				}
			}
		);

		$(document).on(

			'click',

			".af_dash_fields_divs",

			function () {

				$('html, body').animate(
					{
						scrollTop: $(".af_dash_view_all_btns").offset().top
					},
					1200
				);
			}
		);

		$(document).on('change', ".af_i_loc_select", function () {

			var af_i_loc_select = $(this).children("option:selected").val();

			var stock = $(this).children("option:selected").data('stock');


			var flag = false;

			if (stock == 0) {

				$('.out_of_stock_msg_p').fadeIn('fast');

				flag = true;

			} else {

				flag = false;

				$('.out_of_stock_msg_p').fadeOut('fast');
			}

			$('.save_order').prop('disabled', flag);
		});

		$(document).on('change', ".af_mli_tax_shipping_zones", af_mli_tax_shipping_zones);
		af_mli_tax_shipping_zones();

		function af_mli_tax_shipping_zones() {
			var zone_value = $('.af_mli_tax_shipping_zones').val();

			var tax_id = $('.af_mli_tax_shipping_zones').data('tax_id');

			jQuery.ajax(
				{
					url: ajaxurl,

					type: 'POST',

					data: {

						zone_value: zone_value,

						action: 'af_dependable_shipp_methods',

						nonce: nonce,
					},

					success: function (response) {

						if (response) {

							jQuery('.' + tax_id + 'af_mli_tax_shipping_methods').html(response);

						}
					}
				}
			);
		}

		$(document).on(

			'change',

			".af_mli_tax_country",

			function () {

				var country_value = $(this).children("option:selected").val();

				var tax_id = $(this).data('tax_id');

				jQuery.ajax(
					{
						url: ajaxurl,

						type: 'POST',

						data: {

							country_value: country_value,

							action: 'af_dependable_country_states',

							nonce: nonce,
						},

						success: function (response) {

							if (response) {

								jQuery('.' + tax_id + 'af_mli_tax_state').html(response);

							}
						}
					}
				);
			}
		);
	}
);

function af_mli_prod_search($) {

	jQuery('.af_mli_prod_search_class').select2(
		{
			ajax: {

				url: ajaxurl, // AJAX URL is predefined in WordPress admin.

				dataType: 'json',

				type: 'POST',

				delay: 20, // Delay in ms while typing when to perform a AJAX search.

				data: function (params) {

					return {

						q: params.term, // search query

						action: 'af_mli_prod_ajax', // AJAX action for admin-ajax.php.//aftaxsearchUsers(is function name which isused in adminn file)

						nonce: nonce // AJAX nonce for admin-ajax.php.

					};

				},

				processResults: function (data) {

					var options = [];

					if (data) {

						$.each(
							data,  // data is the array of arrays, and each of them contains ID and the Label of the option.
							function (index, text) {

								options.push({ id: text[0], text: text[1] }); // do not forget that "index" is just auto incremented value.

							}
						);
					}

					return {

						results: options
					};

				},

				cache: true

			},

			multiple: false,

			minimumInputLength: 3, // the minimum of symbols to input before perform a search.

			placeholder: 'Choose Products'
		}
	);
}

function af_mli_prod_page_inven($) {

	if ($('#af_mli_prod_page_inven').is(':checked')) {

		$('#af_mli_prod_page_loc_show_type').closest('tr').fadeIn('Slow');

	} else {

		$('#af_mli_prod_page_loc_show_type').closest('tr').fadeOut('Slow');

	}
}

function mli_gen_restrict_user($) {

	if ($('#mli_gen_restrict_user').is(':checked')) {

		$('#mli_gen_exclude_locations').closest('tr').fadeIn('Slow');

	} else {

		$('#mli_gen_exclude_locations').closest('tr').fadeOut('Slow');
	}
}

function af_mli_hide_column_on_checkbox($) {

	if ($('.prd_sku').is(':checked')) {

		$('.af_mli_sku_column').show();

	} else {

		$('.af_mli_sku_column').hide();
	}

	if ($('.prd_name').is(':checked')) {

		$('.af_mli_name_column').show();

	} else {

		$('.af_mli_name_column').hide();
	}

	if ($('.prd_thumbnail').is(':checked')) {

		$('.af_mli_thumb_column').show();

	} else {

		$('.af_mli_thumb_column').hide();
	}

	if ($('.prd_tax_status').is(':checked')) {

		$('.af_mli_tax_status_column').show();

	} else {

		$('.af_mli_tax_status_column').hide();
	}

	if ($('.prd_tax_class').is(':checked')) {

		$('.af_mli_tax_class_column').show();

	} else {

		$('.af_mli_tax_class_column').hide();
	}

	if ($('.prd_shipping_class').is(':checked')) {

		$('.af_mli_ship_class_column').show();

	} else {

		$('.af_mli_ship_class_column').hide();
	}

	if ($('.prd_price').is(':checked')) {

		$('.af_mli_price_column').show();

	} else {

		$('.af_mli_price_column').hide();
	}

	if ($('.prd_sale_price').is(':checked')) {

		$('.af_mli_sale_price_column').show();

	} else {

		$('.af_mli_sale_price_column').hide();
	}

	if ($('.prd_weight').is(':checked')) {

		$('.af_mli_weight_column').show();

	} else {

		$('.af_mli_weight_column').hide();
	}

	if ($('.inven_name').is(':checked')) {

		$('.af_mli_inven_name_column').show();

	} else {

		$('.af_mli_inven_name_column').hide();
	}

	if ($('.inven_manage_stock').is(':checked')) {

		$('.af_mli_manage_stock_column').show();

	} else {

		$('.af_mli_manage_stock_column').hide();
	}

	if ($('.inven_stock_quan').is(':checked')) {

		$('.af_mli_stock_quan_column').show();

	} else {

		$('.af_mli_stock_quan_column').hide();
	}

	if ($('.inven_low_stock_threshold').is(':checked')) {

		$('.af_mli_low_stock_thres_column').show();

	} else {

		$('.af_mli_low_stock_thres_column').hide();
	}

	if ($('.inven_allow_back_order').is(':checked')) {

		$('.af_mli_backorder_column').show();

	} else {

		$('.af_mli_backorder_column').hide();
	}

	if ($('.inven_stock_status').is(':checked')) {

		$('.af_mli_stock_status_column').show();

	} else {

		$('.af_mli_stock_status_column').hide();
	}

	if ($('.inven_sold_individually').is(':checked')) {

		$('.af_mli_sold_individ_column').show();

	} else {

		$('.af_mli_sold_individ_column').hide();
	}

	if ($('.inven_location').is(':checked')) {

		$('.af_mli_loc_column').show();

	} else {

		$('.af_mli_loc_column').hide();
	}

	if ($('.stock_update_history').is(':checked')) {

		$('.af_mli_history_column').show();

	} else {

		$('.af_mli_history_column').hide();
	}
}

jQuery(window).on(

	'load',

	function () {

		if (jQuery('.af_mli_l_s_products_table >tbody >tr').length >= 1) {

			jQuery('.af_mli_l_s_products_table').show();

			jQuery('.af_mli_no_prod_heading').hide();

		} else {

			jQuery('.af_mli_l_s_products_table').hide();

			jQuery('.af_mli_no_prod_heading').show();
		}

		if (jQuery('.af_mli_os_products_table >tbody >tr').length >= 1) {

			jQuery('.af_mli_os_products_table').show();

			jQuery('.af_mli_o_s_no_prod_heading').hide();

		} else {

			jQuery('.af_mli_os_products_table').hide();

			jQuery('.af_mli_o_s_no_prod_heading').show();
		}

		if (jQuery('.af_mli_out_of_s_products_table >tbody >tr').length >= 1) {

			jQuery('.af_mli_out_of_s_products_table').show();

			jQuery('.af_mli_out_s_no_prod_heading').hide();

		} else {

			jQuery('.af_mli_out_of_s_products_table').hide();

			jQuery('.af_mli_out_s_no_prod_heading').show();
		}
	}
);

function drawChart() {

	if (chart_data == undefined) {
		return;
	}
	var data = google.visualization.arrayToDataTable(chart_data);

	var options = {

		title: 'Inventory Statistics',

		curveType: 'line',

		legend: {

			position: 'right',
		}
	};

	if (jQuery('#addify_i_curve_chart').length) {

		var chart = new google.visualization.LineChart(document.getElementById('addify_i_curve_chart'));

		chart.draw(data, options);
	}
}

function field_disabled(prod_id, $) {


	if (!prod_id) {
		return;
	}
	let low_stock = $('.af_mli_prod_low_threshold' + prod_id).val() && $('.af_mli_prod_low_threshold' + prod_id).val() != undefined ? parseInt($('.af_mli_prod_low_threshold' + prod_id).val()) : 0;

	if (!$('.af_m_s_chk_js' + prod_id).is(':checked')) {

		console.log(prod_id);

		var div_color = $(".af_mli_stock_quan_column" + prod_id).css("background-color", "revert");

		var backorder = $('.af_mli_prod_stock_status' + prod_id).val();

		$(".af_bo_p" + prod_id).css("display", "none");

		if (backorder == 'onbackorder') {

			var div_color = $(".af_bo_p" + prod_id).css(
				{
					"border": "3px solid " + colors[2],
					"padding": "3px 5px"

				}
			);

			$(".af_bo_p" + prod_id).css("display", "revert");

		}
		if ($('.af_m_s_chk_js' + prod_id).length) {

			$(".af_mli_prod_stock" + prod_id).hide();
		}

	} else {
		var backorder = $('.af_mli_prod_stock_status' + prod_id).val();


		if (backorder == 'onbackorder') {


			var div_color = $(".af_bo_p" + prod_id).css(
				{
					"border": "3px solid " + colors[2],
					"padding": "3px 5px"

				}
			);
			$(".af_bo_p" + prod_id).css("display", "revert");

		}

		var show_stock = $(".af_mli_prod_stock" + prod_id).css("display", "revert");

		$(".af_mli_prod_stock" + prod_id).css('display', 'revert');

		if ($(".af_mli_prod_stock" + prod_id).css('display') != "none") {
			$(".af_bo_p" + prod_id).css("display", "none");
		}

		var qty_check = parseInt(show_stock.val());

		var low_stock_threshold = $(".af_mli_prod_low_threshold" + prod_id).val();

		if (qty_check > empty_stock && qty_check > low_stock) {
			$(".af_mli_prod_stock" + prod_id).css("border", "3px solid " + colors[0])

		}

		if (qty_check > empty_stock && ((low_stock_threshold != "" && qty_check <= low_stock_threshold) || qty_check < low_stock)) {

			$(".af_mli_prod_stock" + prod_id).css("border", "3px solid " + colors[3])

		}

		if (qty_check <= 0) {

			$(".af_mli_prod_stock" + prod_id).css("border", "3px solid " + colors[1])

		}

		if (qty_check == over_stock || qty_check > over_stock) {

			$(".af_mli_prod_stock" + prod_id).css("border", "3px solid " + colors[4])
		}
	}
}

jQuery(document).ready(function ($) {
	$('.af_inven_stock_status').each(function () {
		var inventory_id = $(this).data('inven-id');
		var stock_status = $(this).val();
		let qty_check = $('.af_mli_prod_stock' + inventory_id).val() && $('.af_mli_prod_stock' + inventory_id).val() != undefined ? parseInt($('.af_mli_prod_stock' + inventory_id).val()) : 0;
		if (stock_status == 'onbackorder') {
			$(".af_bo_p" + inventory_id).css("display", "revert");
			var div_color = $(".af_bo_p" + inventory_id).css(
				{
					"border": "3px solid " + colors[2],
					"padding": "3px 5px"

				}
			); $('.af_mli_prod_stock' + inventory_id).css('display', 'none')
		}
		else {
			$(".af_bo_p" + inventory_id).css("display", "none");
			$('.af_mli_prod_stock' + inventory_id).css('display', 'revert')


		}
		let low_stock = $('.af_mli_prod_low_threshold' + inventory_id).val() && $('.af_mli_prod_low_threshold' + inventory_id).val() != undefined ? parseInt($('.af_mli_prod_low_threshold' + inventory_id).val()) : 0;

		var show_stock = $(".af_mli_prod_stock" + inventory_id);



		if (qty_check > empty_stock && qty_check > low_stock) {
			$(".af_mli_prod_stock" + inventory_id).css("border", "3px solid " + colors[0])

		}

		if (qty_check > empty_stock && qty_check <= low_stock) {

			$(".af_mli_prod_stock" + inventory_id).css("border", "3px solid " + colors[3])

		}

		console.log(qty_check + '  -- ' + empty_stock + ' <= ' + low_stock + 'class ===' + 'af_mli_prod_low_threshold' + inventory_id);

		if (qty_check <= 0 || stock_status == 'outofstock') {

			$(".af_mli_prod_stock" + inventory_id).css("border", "3px solid " + colors[1])

		}

		if (qty_check == over_stock || qty_check > over_stock) {

			$(".af_mli_prod_stock" + inventory_id).css("border", "3px solid " + colors[4])
		}
		field_disabled(inventory_id, $);
	})

	console.log(addify_multi_inventory);

	setTimeout(function () {
		$('#mli_gen_google_api_key').parent().find('.description ').append('<a target="_blank" href="https://developers.google.com/maps/documentation/distance-matrix/get-api-key">Create API Key</a>');
	}, 2000);
	af_mli_show_location_based_on();
	$(document).on('change', '#af_mli_show_location_based_on', af_mli_show_location_based_on);

	function af_mli_show_location_based_on() {

		if ('auto' == $('#af_mli_show_location_based_on').val()) {
			$('#mli_gen_google_api_key').prop('required', true);
			$('#mli_gen_google_api_key').closest('tr').show();

		} else {
			$('#mli_gen_google_api_key').prop('required', false);
			$('#mli_gen_google_api_key').closest('tr').hide();

		}

	}



	jQuery(document).on('click', ' input.var_level_inven , input.prod_level_inven , .variations_options', make_readonly_woocommerce_qty_box);
	make_readonly_woocommerce_qty_box();

});


function make_readonly_woocommerce_qty_box() {

	for (let index = 1; index <= 3; index++) {

		setTimeout(function () {

			// --------------------------- Product level setting -------------------------------------

			jQuery('input.prod_level_inven').each(function () {

				if (jQuery(this).is(':checked')) {
					jQuery(this).closest('.woocommerce_options_panel').find('.wc_input_stock').prop('readonly', true);
				} else {
					jQuery(this).closest('.woocommerce_options_panel').find('.wc_input_stock').prop('readonly', false);
				}

			});

			// --------------------------- variation level setting -------------------------------------

			jQuery('input.var_level_inven').each(function () {

				if (jQuery(this).is(':checked')) {
					jQuery(this).closest('.woocommerce_variation').find('.wc_input_stock').prop('readonly', true);
				} else {
					jQuery(this).closest('.woocommerce_variation').find('.wc_input_stock').prop('readonly', false);
				}

			});

			jQuery('.af_mli_location_of').closest('tr').hide();

		}, index * 1000);

	}
}