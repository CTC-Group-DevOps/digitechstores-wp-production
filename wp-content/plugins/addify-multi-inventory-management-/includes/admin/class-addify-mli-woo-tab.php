<?php
/**
 * Define Class.
 *
 * @package : Addify Product Multi Location Inventory
 */

defined('ABSPATH') || exit();

class Addify_Mli_Woo_Tab {



	public function __construct() {

		add_filter('woocommerce_settings_tabs_array', array( $this, 'filter_woocommerce_settings_tabs_array' ), 99);

		add_action('woocommerce_sections_af_mli_tab', array( $this, 'action_woocommerce_sections_my_custom_tab' ), 10);

		add_action('woocommerce_settings_af_mli_tab', array( $this, 'action_woocommerce_settings_my_custom_tab' ), 10);

		add_action('woocommerce_settings_save_af_mli_tab', array( $this, 'action_woocommerce_settings_save_my_custom_tab' ), 10);

		add_filter('woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_custom_settings_option' ), 10, 3);
	}

	public function sanitize_custom_settings_option( $value, $option, $raw_value ) {

		if ('mli_prod_page_loc_diff_msg' === $option['id'] && empty($value)) {

			$value = esc_html__('Products from 2 different locations are not allowed.', 'addify-multi-inventory-management');
		}

		return $value;
	}

	public function filter_woocommerce_settings_tabs_array( $settings_tabs ) {

		$settings_tabs['af_mli_tab'] = esc_html__('Multi Inventory', 'addify-multi-inventory-management');

		return $settings_tabs;
	}

	public function action_woocommerce_sections_my_custom_tab() {

		global $current_section;

		$tab_id = 'af_mli_tab';

		$sections = array(
			'general-setting'            => esc_html__('General', 'addify-multi-inventory-management'),
			'shop-page'                  => esc_html__('Shop Page', 'addify-multi-inventory-management'),
			'shipping-method'            => esc_html__('Shipping & Payment method', 'addify-multi-inventory-management'),
			'manage-stock-configuration' => esc_html__('Manage Stock Configuration', 'addify-multi-inventory-management'),
			'popup-setting'              => esc_html__('Popup Setting', 'addify-multi-inventory-management'),
		);

		$array_keys = array_keys($sections);

		$current_section = empty($current_section) ? 'general-setting' : $current_section;

		?>

		<ul class="subsubsub">

			<?php

			foreach ($sections as $id => $label) {

				echo wp_kses_post('<li><a href="' . admin_url('admin.php?page=wc-settings&tab=' . $tab_id . '&section=' . sanitize_title($id)) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a>' . ( end($array_keys) == $id ? '' : '|' ) . ' </li>');
			}

			?>

		</ul>

		<br class="clear" />

		<?php
	}

	public function get_custom_settings() {

		global $current_section;

		$settings = array();

		$inventory_locations = array();

		$mli_loc = get_terms(
			array(
				'number'     => 0,

				'hide_empty' => false,

				'taxonomy'   => 'mli_location',
			)
		);

		foreach ($mli_loc as $inventory_location) {

			$inventory_locations[ $inventory_location->term_id ] = $inventory_location->name;

		}

		if ('general-setting' == $current_section) {

			$settings = array(

				array(
					'title' => esc_html__('Front End', 'addify-multi-inventory-management'),

					'type'  => 'title',
				),

				array(
					'title'    => esc_html__('Show Locations', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc_tip' => true,

					'desc'     => esc_html__('Check if you want to show locations to user on single product page.', 'addify-multi-inventory-management'),

					'id'       => 'af_mli_prod_page_inven',
				),
				array(
					'title'    => esc_html__('Show Locations in', 'addify-multi-inventory-management'),
					'type'     => 'select',
					'desc_tip' => true,
					'desc'     => esc_html__('Select in which form you want to show locations', 'addify-multi-inventory-management'),
					'id'       => 'af_mli_prod_page_loc_show_type',
					'options'  => array(
						'radio'     => 'Radio',
						'drop_down' => 'Drop Down',
					),
				),
				array(
					'title'    => esc_html__('Select Multiple Locations', 'addify-multi-inventory-management'),

					'type'     => 'select',

					'desc_tip' => true,

					'desc'     => esc_html__('Allow Customers to select multiple locations to view catalogue.', 'addify-multi-inventory-management'),

					'id'       => 'af_mli_show_select_location_dropdown_as',

					'options'  => array(
						'multiple' => 'Multiple',
						'single'   => 'Single',
					),
				),
				array(
					'title'    => esc_html__('Locations Catalogue Visibility', 'addify-multi-inventory-management'),
					'type'     => 'select',
					'desc_tip' => true,
					'desc'     => esc_html__('Select a location to hide products that have not been assigned a location, or hide the "Add to Cart" button.', 'addify-multi-inventory-management'),
					'id'       => 'af_mli_on_lcation_selection',
					'options'  => array(
						'hide_product'             => 'Hide Entire Product',
						'hide_disable_add_to_cart' => 'Hide Add to Cart',
					),
				),
				array(
					'title'    => esc_html__('Auto Select Most Stock', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc_tip' => true,

					'desc'     => esc_html__('Show most stock inventory as auto selected in dropdown.', 'addify-multi-inventory-management'),

					'id'       => 'af_mli_auto_select_inventory_with_highest_stock',
				),
				array(
					'title'    => esc_html__('Force Location Selection', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc_tip' => true,

					'desc'     => esc_html__('Force customers to select location.If the don not, the nearest location will be enforced.', 'addify-multi-inventory-management'),

					'id'       => 'af_mli_auto_select_nearest_location',

				),
				array(
					'title'    => esc_html__('Show Locations Based On', 'addify-multi-inventory-management'),
					'type'     => 'select',
					'desc_tip' => true,
					'desc'     => esc_html__('Select how you want to show locations.On manual selection , must enter longitude and latitude in location creating setting. On Auto select must enter API Key so it will automatically show the most neareast location in top options ', 'addify-multi-inventory-management'),
					'id'       => 'af_mli_show_location_based_on',
					'options'  => array(
						'country' => 'User Country',
						'manual'  => 'Manual',
						'auto'    => 'Auto',
					),
				),
				array(
					'title'    => esc_html__('Google API Key ', 'addify-multi-inventory-management'),

					'type'     => 'text',

					'desc_tip' => false,

					'desc'     => esc_html__('Enter api key to get auto longitude and latitude and show the most nearest location on top of options.', 'addify-multi-inventory-management'),

					'id'       => 'mli_gen_google_api_key',
				),
				array(
					'title'    => esc_html__('Restrict Users to Specific Locations', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc_tip' => true,

					'desc'     => esc_html__('Enable to restrict users to the selected location.', 'addify-multi-inventory-management'),

					'id'       => 'mli_gen_restrict_user',
				),

				array(
					'title'    => esc_html__('Locations', 'addify-multi-inventory-management'),

					'type'     => 'multiselect',

					'desc_tip' => true,

					'desc'     => esc_html__('select location.', 'addify-multi-inventory-management'),

					'id'       => 'mli_gen_exclude_locations',

					'class'    => 'mli_gen_exclude_locations',

					'options'  => $inventory_locations,

				),

				array(
					'title'    => esc_html__('Heading Display', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc_tip' => true,

					'desc'     => esc_html__('Check if you want to show heading.', 'addify-multi-inventory-management'),

					'id'       => 'display_heading_display',
				),

				array(
					'title'    => esc_html__('Heading Title', 'addify-multi-inventory-management'),

					'type'     => 'text',

					'desc_tip' => true,

					'desc'     => esc_html__('Enter Heading.', 'addify-multi-inventory-management'),

					'id'       => 'display_heading',
				),

				array(
					'title'    => esc_html__('Heading tag', 'addify-multi-inventory-management'),

					'type'     => 'select',

					'desc_tip' => true,

					'desc'     => esc_html__('Select in which you want to show heading of multi inventory selection', 'addify-multi-inventory-management'),

					'id'       => 'af_mli_loc_ptn_heading_title',

					'options'  => array(
						'h1' => 'H1',
						'h2' => 'H2',
						'h3' => 'H3',
						'h4' => 'H4',
						'h5' => 'H5',
					),
				),

				array(
					'title'    => esc_html__('Heading Color', 'addify-multi-inventory-management'),

					'type'     => 'color',

					'desc_tip' => true,

					'desc'     => esc_html__('Select Heading Color.', 'addify-multi-inventory-management'),

					'id'       => 'display_heading_color',
				),

				array(
					'title'    => esc_html__('Field Border', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc_tip' => true,

					'desc'     => esc_html__('Enable to set field border.', 'addify-multi-inventory-management'),

					'id'       => 'display_field_border',
				),

				array(
					'title'    => esc_html__('Field Border Color', 'addify-multi-inventory-management'),

					'type'     => 'color',

					'desc_tip' => true,

					'desc'     => esc_html__('Select Field Border Color.', 'addify-multi-inventory-management'),

					'id'       => 'display_field_border_color',
				),

				array(
					'title'    => esc_html__('Field Border Radius', 'addify-multi-inventory-management'),

					'type'     => 'text',

					'desc_tip' => true,

					'desc'     => esc_html__('Enter Field Border Radius.', 'addify-multi-inventory-management'),

					'id'       => 'display_field_border_radius',
				),

				array(
					'title'    => esc_html__('Field Padding', 'addify-multi-inventory-management'),

					'type'     => 'text',

					'desc_tip' => true,

					'desc'     => esc_html__('Enter Field Padding.', 'addify-multi-inventory-management'),

					'id'       => 'display_field_padding',
				),

				array(
					'title'    => esc_html__('Field Background Color', 'addify-multi-inventory-management'),

					'type'     => 'color',

					'desc_tip' => true,

					'desc'     => esc_html__('Select Field Background Color.', 'addify-multi-inventory-management'),

					'id'       => 'display_field_background_color',
				),

				array(
					'title'    => esc_html__('Drop Down Option/Radio Button Color', 'addify-multi-inventory-management'),

					'type'     => 'color',

					'desc_tip' => true,

					'desc'     => esc_html__('Select Drop down/Radio option color.', 'addify-multi-inventory-management'),

					'id'       => 'display_field_option_color',
				),

				array(
					'title'             => esc_html__('Drop down Option/Radio Button Font Size', 'addify-multi-inventory-management'),

					'type'              => 'number',

					'desc_tip'          => true,

					'desc'              => esc_html__('Select Drop down option/Radio button label font size. Size will be in pixels', 'addify-multi-inventory-management'),

					'id'                => 'display_field_option_font_size',

					'custom_attributes' => array( 'min' => 1 ),

				),
				array(
					'title'    => esc_html__('Hide Out Of Stock Locations', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc_tip' => true,

					'desc'     => esc_html__('Enable if you want to hide the location which has out of stock status on the front-end.', 'addify-multi-inventory-management'),

					'id'       => 'mli_gen_hide_loc_from_drop_down',
				),
				array(
					'title'    => esc_html__('Out of stock Error Message', 'addify-multi-inventory-management'),

					'type'     => 'textarea',

					'desc'     => esc_html__('This notification will be displayed when the inventory is selected and is out of stock. Thank you for your understanding. Use variable {inventory_name} to print inventory name in error message ', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'value'    => !empty(get_option('af_mli_out_of_stock_error_msg')) ? get_option('af_mli_out_of_stock_error_msg') : 'Out of stock in {inventory_name}.',
					'id'       => 'af_mli_out_of_stock_error_msg',
				),
				array(
					'title'    => esc_html__('Product Page Error Message', 'addify-multi-inventory-management'),

					'type'     => 'textarea',

					'desc'     => esc_html__('This message will appear when product from 2 different locations will be added.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'mli_prod_page_loc_diff_msg',
				),

				array(
					'type' => 'sectionend',

					'id'   => 'general-setting',
				),

				// array(
				//  'title' => esc_html__('Default Product Location', 'addify-multi-inventory-management'),

				//  'type' => 'title',
				// ),

				// array(
				//  'title' => esc_html__('Locations', 'addify-multi-inventory-management'),

				//  'type' => 'select',

				//  'desc_tip' => true,

				//  'desc' => esc_html__('select product default location which every product will have. This location will be used as default of main inventory.', 'addify-multi-inventory-management'),

				//  'id' => 'mli_gen_default_location',

				//  'class' => 'mli_gen_default_location',

				//  'options' => $inventory_locations,

				// ),

				array(
					'type' => 'sectionend',

					'id'   => 'general-setting',
				),

				array(
					'title' => esc_html__('Backend Mode Only', 'addify-multi-inventory-management'),

					'type'  => 'title',
				),

				array(
					'title'    => esc_html__('Allow to Work Plugin from Backend Only', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc_tip' => true,

					'desc'     => esc_html__('Turning on this switch will disable the front-end location selection functionality. You can modify the order from the backend. eg. User can order without selecting location but still, you can set location to order from backend. which will also update the selected location stock.', 'addify-multi-inventory-management'),

					'id'       => 'mli_gen_backend_mode_only',
				),

				array(
					'title'    => esc_html__('Allow to Work Plugin from Backend Only', 'addify-multi-inventory-management'),

					'type'     => 'text',

					'desc_tip' => true,

					'desc'     => esc_html__('Select Rule.', 'addify-multi-inventory-management'),

					'id'       => 'mli_gen_backend_mode_only_rules',
				),

				array(
					'type' => 'sectionend',

					'id'   => 'general-setting',
				),

			);

		} elseif ('shop-page' == $current_section) {

			$settings = array(

				array(
					'title' => esc_html__('Shop Page Settings', 'addify-multi-inventory-management'),

					'type'  => 'title',
				),

				array(
					'title'    => esc_html__('Inventory name and stock', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc'     => esc_html__('Enable this to show locations name and available stock at selected location.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'mli_shop_loc_name_stock',
				),

				array(
					'title'    => esc_html__('Inventory Price', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc'     => esc_html__('Enable this to Show location price on shop page.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'mli_shop_loc_price',
				),

				array(
					'title'    => esc_html__('Hide out of stock products', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc'     => esc_html__('Enable this to hide out of stock products.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'mli_shop_hide_out_of_stock_prod',
				),

				array(
					'title'    => esc_html__('Location Filter Widget', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc'     => esc_html__('Enable this to show location filter on the shop page along with WooCommerce filter.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'mli_shop_loc_filter_widget',
				),

				array(
					'type' => 'sectionend',

					'id'   => 'shop-page',
				),

			);

		} elseif ('shipping-method' == $current_section) {

			$settings = array(

				array(
					'title' => esc_html__('Shipping and Payment Method Settings', 'addify-multi-inventory-management'),

					'type'  => 'title',
				),

				array(
					'title'    => esc_html__('Shipping zones to each location', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc'     => esc_html__('Enable this to set shipping zone to each location. You can set shipping zone from location edit screen after enabling this.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'mli_shipp_zones_to_loc',
				),

				array(
					'title'    => esc_html__('Shipping Methods to each location', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc'     => esc_html__('Enable this to set shipping method to each location. You can set shipping methods from the location edit screen after enabling this <br> Note - Shipping Zone Setting Must be enabled for this.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'mli_shipp_methods_to_loc',
				),

				array(
					'title'    => esc_html__('Payment methods to location', 'addify-multi-inventory-management'),

					'type'     => 'checkbox',

					'desc'     => esc_html__('Enable this to set payment method to each location. You can set the payment method from the location edit screen after enabling this on the checkout page, the common payment methods from selected product locations will be shown.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'mli_shipp_payment_method_of_loc',
				),

				array(
					'type' => 'sectionend',

					'id'   => 'shipping-method',
				),


			);

		} elseif ('popup-setting' == $current_section) {
			$settings = array(

				array(
					'title' => esc_html__('Popup Settings', 'addify-multi-inventory-management'),
					'type'  => 'title',
				),
				array(
					'title' => esc_html__('Disable Select Location Button from Menu', 'addify-multi-inventory-management'),
					'type'  => 'checkbox',
					'id'    => 'af_mi_disable_select_location_button',
				),
				array(
					'title' => esc_html__('Forcefully Show Popup If Location Not Selected', 'addify-multi-inventory-management'),
					'type'  => 'checkbox',
					'id'    => 'af_mi_always_show_popup_if_location_is_not_selected',
				),
				array(
					'title' => esc_html__('Select Location Button Text', 'addify-multi-inventory-management'),
					'type'  => 'text',
					'id'    => 'af_mi_select_location_button_text',
					'value' => get_option('af_mi_select_location_button_text') ? get_option('af_mi_select_location_button_text') : 'Please choose a store',
				),

				array(
					'title'             => esc_html__('Width', 'addify-multi-inventory-management'),
					'type'              => 'number',
					'custom_attributes' => array(
						'min' => 1,
						'max' => 100,
					),
					'id'                => 'af_mi_popup_width',
					'value'             => !empty(get_option('af_mi_popup_width')) ? get_option('af_mi_popup_width') : 50,
					'default'           => 50,
					'desc'              => esc_html__('Please enter popup width in %.', 'addify-multi-inventory-management'),
					'desc_tip'          => true,
				),
				array(
					'title'             => esc_html__('Height', 'addify-multi-inventory-management'),
					'type'              => 'number',
					'custom_attributes' => array(
						'min' => 1,
						'max' => 100,
					),
					'id'                => 'af_mi_popup_height',
					'value'             => !empty(get_option('af_mi_popup_height')) ? get_option('af_mi_popup_height') : 50,
					'default'           => 50,
					'desc'              => esc_html__('Please enter popup height in %.', 'addify-multi-inventory-management'),
					'desc_tip'          => true,
				),
				array(
					'title' => esc_html__('Heading Text', 'addify-multi-inventory-management'),
					'type'  => 'text',
					'id'    => 'af_mi_popup_heading',
				),
				array(
					'title'   => esc_html__('Heading Text Level', 'addify-multi-inventory-management'),
					'type'    => 'select',
					'options' => array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ),
					'id'      => 'af_mi_popup_heading_level',
				),
				array(
					'title' => esc_html__('Description', 'addify-multi-inventory-management'),
					'type'  => 'textarea',
					'id'    => 'af_mi_popup_description',
					'value' => get_option('af_mi_popup_description') ? get_option('af_mi_popup_description') : 'Welcome to our selection of multiple stores, where you can choose the best one to suit your needs.',
				),
				// array(
				//  'title' => esc_html__('Popup Animation', 'addify-multi-inventory-management'),
				//  'type' => 'select',
				//  'options' => array(
				//      'left_to_right' => 'Left to Right',
				//      'right_to_left' => 'Right to Left',
				//      'top_to_bottom' => 'Top to Bottom',
				//      'bottom_to_top' => 'Bottom to Top',
				//  ),
				//  'id' => 'af_mi_popup_animation',
				// ),
				array(
					'title' => esc_html__('Popup Background Color', 'addify-multi-inventory-management'),
					'type'  => 'color',
					'id'    => 'af_mi_popup_bg_color',
				),
				array(
					'title' => esc_html__('Popup Text Color', 'addify-multi-inventory-management'),
					'type'  => 'color',
					'id'    => 'af_mi_popup_text_color',
					'value' => !empty(get_option('af_mi_popup_text_color')) ? get_option('af_mi_popup_text_color') : '#ffff',
				),
				array(
					'title' => esc_html__('Heading Color', 'addify-multi-inventory-management'),
					'type'  => 'color',
					'id'    => 'af_mi_heading_color',
					'value' => !empty(get_option('af_mi_heading_color')) ? get_option('af_mi_heading_color') : '#0c0d0d',

				),
				array(
					'title' => esc_html__('Menu Button Text Color', 'addify-multi-inventory-management'),
					'type'  => 'color',
					'id'    => 'af_mi_menu_button_text_color',
					'value' => !empty(get_option('af_mi_menu_button_text_color')) ? get_option('af_mi_menu_button_text_color') : '#ffffff',
				),
				array(
					'title' => esc_html__('Menu Button Background Color', 'addify-multi-inventory-management'),
					'type'  => 'color',
					'id'    => 'af_mi_menu_button_bg_color',
					'value' => !empty(get_option('af_mi_menu_button_bg_color')) ? get_option('af_mi_menu_button_bg_color') : '#0c0d0d',

				),
				array(
					'type' => 'sectionend',
					'id'   => 'shipping-method',
				),
			);


		} else {

			$settings = array(
				array(
					'title' => esc_html__('Configuration', 'addify-multi-inventory-management'),

					'type'  => 'title',
				),

				array(
					'title'    => esc_html__('In Stock Color', 'addify-multi-inventory-management'),

					'type'     => 'color',

					'desc'     => esc_html__('Custom color for products In Stock.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'af_mli_in_stock_color',
				),

				array(
					'title'    => esc_html__('Out of Stock Color', 'addify-multi-inventory-management'),

					'type'     => 'color',

					'desc'     => esc_html__('Custom color for products Out Of Stock.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'af_mli_out_of_stock_color',
				),

				array(
					'title'    => esc_html__('On Backorder', 'addify-multi-inventory-management'),

					'type'     => 'color',

					'desc'     => esc_html__('Custom color for products on Back Order. It will apply if stock status is on backorder', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'af_mli_back_order_color',
				),

				array(
					'title'    => esc_html__('Low Stock', 'addify-multi-inventory-management'),

					'type'     => 'color',

					'desc'     => esc_html__('Custom color for products on Low Stock.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'af_mli_low_stock_color',
				),

				array(
					'title'    => esc_html__('Over Stock', 'addify-multi-inventory-management'),

					'type'     => 'color',

					'desc'     => esc_html__('Custom color for products on Over Stock.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'af_mli_over_stock_color',
				),

				array(
					'title'    => esc_html__('Over Stock Threshold', 'addify-multi-inventory-management'),

					'type'     => 'number',

					'desc'     => esc_html__('Enter Over Stock Threshold. Default Value is 200.', 'addify-multi-inventory-management'),

					'desc_tip' => true,

					'id'       => 'af_mli_over_stock_quantity',
				),

				array(
					'title'             => esc_html__('Number of Products Per Page', 'addify-multi-inventory-management'),

					'type'              => 'number',

					'desc'              => esc_html__('Enter the number of products to be displayed on each page.Default value is 10.', 'addify-multi-inventory-management'),

					'desc_tip'          => true,

					'id'                => 'af_mli_number_of_products_per_page',
					'custom_attributes' => array(
						'min' => '1',
					),
				),

				array(
					'type' => 'sectionend',

					'id'   => 'manage-stock-configuration',
				),

			);
		}
		return $settings;
	}

	public function action_woocommerce_settings_my_custom_tab() {

		$settings = $this->get_custom_settings();

		WC_Admin_Settings::output_fields($settings);
	}

	public function action_woocommerce_settings_save_my_custom_tab() {

		global $current_section;

		$tab_id = 'af_mli_tab';

		$current_section = $current_section ? $current_section : 'general-setting';

		$settings = $this->get_custom_settings();

		if ($current_section) {

			WC_Admin_Settings::save_fields($settings);

			/**
			 * Saving options values
			 *
			 * @since 1.0.0
			 */
			do_action('woocommerce_update_options_' . $tab_id . '_' . $current_section);

		}
	}
}

new Addify_Mli_Woo_Tab();