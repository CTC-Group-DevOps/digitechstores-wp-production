<?php

/**
 * Class Flexible_Checkout_Fields_Pro
 */
class Flexible_Checkout_Fields_Pro implements \FCFProVendor\WPDesk\PluginBuilder\Plugin\HookablePluginDependant {

	use \FCFProVendor\WPDesk\PluginBuilder\Plugin\PluginAccess;

    const FIELD_TYPE_NAME = 'name';
	const FIELD_TYPE_PLACEHOLDER_LABEL = 'placeholder_label';
	const FIELD_TYPE_LABEL_IS_REQUIRED = 'label_is_required';
	const FIELD_TYPE_HAS_DEFAULT_VALUE = 'has_default_value';
	const FIELD_TYPE_DISABLE_PLACEHOLDER = 'disable_placeholder';
	const FIELD_TYPE_HAS_OPTIONS = 'has_options';
	const FIELD_TYPE_HAS_REQUIRED = 'has_required';
	const FIELD_TYPE_EXCLUDE_IN_ADMIN = 'exclude_in_admin';
	const FIELD_TYPE_EXCLUDE_FOR_USER = 'exclude_for_user';

	/**
	 * Plugin.
	 *
	 * @var Flexible_Checkout_Fields_Pro_Plugin
	 */
	private $plugin;

	/**
	 * List of keys for unavailable field sections, used in actions woocommerce_{$section_name}.
	 *
	 * @var string[]
	 */
	private $unavailable_sections = [];

	/**
	 * Flexible_Checkout_Fields_Pro constructor.
	 *
	 * @param Flexible_Checkout_Fields_Pro_Plugin $plugin Plugin.
	 */
	public function __construct( Flexible_Checkout_Fields_Pro_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Fires hooks
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'flexible_checkout_fields_sections', array( $this, 'flexible_checkout_fields_sections' ), 10, 2 );
		add_filter( 'flexible_checkout_fields_all_sections', array( $this, 'flexible_checkout_fields_all_sections' ), 10, 2 );
		add_action( 'init', array( $this, 'init_sections' ) );
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'woocommerce_admin_order_data_after_shipping_address' ), 9999999 );
		add_action( 'woocommerce_checkout_process', array( $this, 'set_unavailable_sections_for_checkout_process' ) );
	}

	/**
     * Settings must be always array.
     *
	 * @param mixed $settings
	 *
	 * @return array
	 */
	private function fallback_settings_to_array( $settings ) {
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}
		return $settings;
    }

	public function get_settings() {
		return $this->fallback_settings_to_array( get_option( 'wpdesk_checkout_fields_settings', array() ) );
	}

	public function get_section_settings() {
		return $this->fallback_settings_to_array( get_option('inspire_checkout_fields_section_settings', array() ) );
	}

	public function init() {
		$settings = $this->get_settings();
		if ( get_option( 'flexible_checkout_fields_init_checkboxes', '0' ) == '0' ) {
			$checkboxes = get_option( 'inspire_checkout_fields_checkboxes', array() );
			foreach ( $checkboxes as $checkbox ) {
				if ( empty( $settings['after_customer_details'] ) ) {
					$settings['after_customer_details'] = array();
				}
				$settings['review_order_before_submit'][$checkbox['name']] = array(
					'custom_field' 	=> '1',
					'name'			=> $checkbox['name'],
					'visible'		=> '0',
					'required'		=> $checkbox['required'],
					'label'			=> $checkbox['label'],
					'placeholder'	=> __( 'Yes', 'flexible-checkout-fields-pro' ),
					'class'			=> $checkbox['class'],
					'type'			=> 'inspirecheckbox',
					'file_types'	=> '',
					'file_size'		=> '',
					'date_format'	=> 'dd.mm.yy',
					'days_before'	=> '0',
					'days_after'	=> '',
				);
				update_option( 'wpdesk_checkout_fields_settings', $settings );
				update_option( 'inspire_checkout_fields_review_order_before_submit', '1' );
			}
			update_option( 'flexible_checkout_fields_init_checkboxes', '1' );
		}
	}

	public function woocommerce_admin_order_data_after_shipping_address( $order ) {

		$additional_fields = false;

		$sections = $this->flexible_checkout_fields_sections( array(), false );
		$settings = $this->get_settings();

		$flexible_checkout_fields = flexible_checkout_fields();
		$checkout_field_type = $flexible_checkout_fields->get_fields();

		foreach ( $sections as $section => $section_data ) {
			if ( isset( $settings[$section_data['section']] ) && is_array( $settings[$section_data['section']] ) ) {
				foreach ( $settings[$section_data['section']] as $key => $field ) {
					$value = wpdesk_get_order_meta( $order, '_'.$key, true );
					$additional_fields = true;
				}
			}
		}

		if ( $additional_fields != false ) {
			include( 'views/order-additional-fields.php' );
		}
	}

	public function checkout_form_action() {

		$sections = $this->flexible_checkout_fields_sections( array(), false );

		$settings = $this->get_settings();

		$section_settings = $this->get_section_settings();

		$current_filter = current_filter();

		$section = $sections[ $current_filter ]['section'] ?? '';

		$fields = [];

		$checkout = WC()->checkout();

		if ( empty( $section_settings[ $section ] ) ) {
			$section_settings = [];
		} else {
			$section_settings = $section_settings[ $sections[ $current_filter ]['section'] ];
		}

		if ( isset( $settings[ $section ] ) ) {
			/**
			 * Filter flexible checkout fields section fields.
			 *
			 * @since 4.0.9
			 *
			 * @param array $fields The current section fields.
			 * @param string $section The current section.
			 */
			$fields = apply_filters( 'flexible_checkout_fields_section_fields', $settings[ $section ], $section );

			// old filter.
			if ( has_filter( 'flexible_chekout_fields_fields' ) ) {
				$fields = apply_filters( 'flexible_chekout_fields_fields', $settings[ $section ], $section );

				_deprecated_hook( 'flexible_chekout_fields_fields', '4.0.9', 'flexible_checkout_fields_section_fields', 'This filter is deprecated. Please use the new filter "flexible_checkout_fields_section_fields" instead.' );
			}
		}

		if ( !empty( $fields ) && is_array( $fields ) ) {
			$args = array(
				'fields' 	       => $fields,
				'checkout'	       => $checkout,
				'section_settings' => $section_settings,
				'html_tag'         => $section_settings['section_title_type'] ?? '',
				'section_id'       => $sections[$current_filter]['section'],
			);
			echo $this->plugin->load_template( $sections[$current_filter]['section'], 'checkout/flexible-checkout-fields', $args );
		}

	}

	public function init_sections( $checkout ) {
		$sections = $this->flexible_checkout_fields_sections( array(), false );

		foreach ( $sections as $section => $section_data ) {
			add_action( $section, array( $this, 'checkout_form_action' ), ( $section_data['hook_priority'] ?? false ) ? 0 : 100 );
		}
	}

	public function flexible_checkout_fields_all_sections( $sections, $get_disabled = true ) {
		return $this->flexible_checkout_fields_sections( $sections, $get_disabled );
	}

	public function flexible_checkout_fields_sections( $sections, $get_disabled = false ) {

		$sections_add = array();

		$sections_add['woocommerce_checkout_before_customer_details'] = array(
			'section'        => 'before_customer_details',
			'tab'            => 'fields_before_customer_details',
			'tab_title'      => __( 'Before Customer Details', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
				/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'Before Customer Details', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => true,
		);

		$sections_add['woocommerce_checkout_billing'] = array(
			'section'        => 'checkout_billing',
			'tab'            => 'fields_checkout_billing',
			'tab_title'      => __( 'Before Billing Heading', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'Before Billing Heading', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => true,
			'hook_priority'  => true,
		);

		$sections_add['woocommerce_before_checkout_billing_form'] = array(
			'section'        => 'before_checkout_billing_form',
			'tab'            => 'fields_before_checkout_billing_form',
			'tab_title'      => __( 'Before Billing Form', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'Before Billing Form', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => true,
		);

		$sections_add['woocommerce_after_checkout_billing_form'] = array(
			'section'        => 'after_checkout_billing_form',
			'tab'            => 'fields_after_checkout_billing_form',
			'tab_title'      => __( 'After Billing Form', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'After Billing Form', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => true,
		);

		$sections_add['woocommerce_before_checkout_registration_form'] = array(
			'section'        => 'before_checkout_registration_form',
			'tab'            => 'fields_before_checkout_registration_form',
			'tab_title'      => __( 'Before Registration Form', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'Before Registration Form', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => true,
		);

		$sections_add['woocommerce_after_checkout_registration_form'] = array(
			'section'        => 'after_checkout_registration_form',
			'tab'            => 'fields_after_checkout_registration_form',
			'tab_title'      => __( 'After Registration Form', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'After Registration Form', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => true,
		);

		$sections_add['woocommerce_checkout_shipping'] = array(
			'section'        => 'checkout_shipping',
			'tab'            => 'fields_checkout_shipping',
			'tab_title'      => __( 'Before Shipping Heading', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'Before Shipping Heading', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => true,
			'hook_priority'  => true,
		);

		$sections_add['woocommerce_before_checkout_shipping_form'] = array(
			'section'        => 'before_checkout_shipping_form',
			'tab'            => 'fields_before_checkout_shipping_form',
			'tab_title'      => __( 'Before Shipping Form', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'Before Shipping Form', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => true,
		);

		$sections_add['woocommerce_after_checkout_shipping_form'] = array(
			'section'        => 'after_checkout_shipping_form',
			'tab'            => 'fields_after_checkout_shipping_form',
			'tab_title'      => __( 'After Shipping Form', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'After Shipping Form', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => true,
		);

		$sections_add['woocommerce_before_order_notes'] = array(
			'section'        => 'before_order_notes',
			'tab'            => 'fields_before_order_notes',
			'tab_title'      => __( 'Before Order Notes', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'Before Order Notes', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => false,
		);

		$sections_add['woocommerce_checkout_after_customer_details'] = array(
			'section'        => 'after_customer_details',
			'tab'            => 'fields_after_customer_details',
			'tab_title'      => __( 'After Customer Details', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'After Customer Details', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => false,
		);

		$sections_add['woocommerce_after_order_notes'] = array(
			'section'        => 'after_order_notes',
			'tab'            => 'fields_after_order_notes',
			'tab_title'      => __( 'After Order Notes', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'After Order Notes', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => false,
		);

		$sections_add['woocommerce_review_order_before_payment'] = array(
			'section'        => 'review_order_before_payment',
			'tab'            => 'fields_review_order_before_payment',
			'tab_title'      => __( 'Before Payment', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'Before Payment', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => false,
		);

		$sections_add['woocommerce_review_order_before_submit'] = array(
			'section'        => 'review_order_before_submit',
			'tab'            => 'fields_review_order_before_submit',
			'tab_title'      => __( 'Before Submit', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'Before Submit', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => false,
		);

		$sections_add['woocommerce_review_order_after_submit'] = array(
			'section'        => 'review_order_after_submit',
			'tab'            => 'fields_review_order_after_submit',
			'tab_title'      => __( 'After Submit', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'After Submit', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => false,
		);

		$sections_add['woocommerce_review_order_after_payment'] = array(
			'section'        => 'review_order_after_payment',
			'tab'            => 'fields_review_order_after_payment',
			'tab_title'      => __( 'After Payment', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'After Payment', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => false,
		);

		$sections_add['woocommerce_checkout_after_order_review'] = array(
			'section'        => 'checkout_after_order_review',
			'tab'            => 'fields_checkout_after_order_review',
			'tab_title'      => __( 'After Order Review', 'flexible-checkout-fields-pro' ),
			'title'          => sprintf(
			/* translators: %1$s: section title */
				__( '%s Fields', 'flexible-checkout-fields-pro' ),
				__( 'After Order Review', 'flexible-checkout-fields-pro' )
			),
			'custom_section' => true,
			'user_meta'      => false,
		);

		foreach ( $sections_add as $section => $section_data ) {
			if ( in_array( $section_data['section'], $this->unavailable_sections, true ) ) {
				continue;
			}

			if ( $get_disabled || get_option( 'inspire_checkout_fields_' . $section_data['section'] , '0' ) == '1' ) {
				$sections[$section] = $section_data;
			}
		}

		return $sections;
	}

	/**
	 * Flexible Checkout Fields Sections
	 *
	 * @return array of sections [ 'billing' => [ 'section' => 'billing' , 'tab' => 'fields_billing' ...] ]
	 */
	private function fcf_settings_fields_sections() {
		$flexible_checkout_fields = flexible_checkout_fields();

		return $flexible_checkout_fields->sections;
	}

	/**
	 * Get option as array from string.
	 *
	 * @param string $options_as_string Options as string.
	 *
	 * @return array
	 */
	public function get_options_as_array_from_string( $options_as_string ) {
		$options = array();
		$rows    = explode( "\n", $options_as_string );
		foreach ( $rows as $row ) {
			$row_option                = explode( ':', $row );
			$options[ $row_option[0] ] = isset( $row_option[1] ) ? $row_option[1] : $row_option[0];
		}
		return $options;
	}

	/**
	 * Sets keys of unavailable sections for checkout process.
	 *
	 * @internal
	 */
	public function set_unavailable_sections_for_checkout_process() {
		if ( is_user_logged_in() || ! WC()->checkout()->is_registration_enabled() ) {
			$this->unavailable_sections[] = 'before_checkout_registration_form';
			$this->unavailable_sections[] = 'after_checkout_registration_form';
		}
	}
}
