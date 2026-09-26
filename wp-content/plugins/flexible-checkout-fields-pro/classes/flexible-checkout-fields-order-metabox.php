<?php

use WPDesk\FCF\Pro\Field\Type\FileType;
use WPDesk\FCF\Pro\Field\Type\MultiCheckboxType;
use WPDesk\FCF\Pro\Field\Type\MultiSelectType;
use WPDesk\FCF\Pro\Field\Type\TextareaType;

/**
 * Handles order metabox for custom checkout fields.
 *
 * Class Flexible_Checkout_Fields_Order_Metabox
 */
class Flexible_Checkout_Fields_Order_Metabox implements \FCFProVendor\WPDesk\PluginBuilder\Plugin\HookablePluginDependant {

	use \FCFProVendor\WPDesk\PluginBuilder\Plugin\PluginAccess;

	const NONCE_ACTION = 'fcf_pro_metabox';
	const NONCE_NAME   = 'fcf_pro_metabox';

	/**
	 * Checkout fields PRO.
	 *
	 * @var Flexible_Checkout_Fields_Pro
	 */
	private $checkout_fields_pro;

	/**
	 * Flexible checkout fields PRO plugin.
	 *
	 * @var Flexible_Checkout_Fields_Pro_Plugin
	 */
	private $flexible_checkout_fields_pro_plugin;

	/**
	 * Flexible_Checkout_Fields_Order_Metabox constructor.
	 *
	 * @param Flexible_Checkout_Fields_Pro        $checkout_fields_pro Checkout fields PRO.
	 * @param Flexible_Checkout_Fields_Pro_Plugin $flexible_checkout_fields_pro_plugin Flexible checkout fields_pro plugin.
	 */
	public function __construct( Flexible_Checkout_Fields_Pro $checkout_fields_pro, Flexible_Checkout_Fields_Pro_Plugin $flexible_checkout_fields_pro_plugin ) {
		$this->checkout_fields_pro                 = $checkout_fields_pro;
		$this->flexible_checkout_fields_pro_plugin = $flexible_checkout_fields_pro_plugin;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'add_meta_boxes', [ $this, 'add_order_metabox' ] );
		add_action( 'save_post', [ $this, 'save_metabox_data' ], 10, 2 );
		add_action( 'woocommerce_process_shop_order_meta', [ $this, 'save_metabox_data' ], 50, 2 );
	}

	/**
	 * Add order metabox.
	 */
	public function add_order_metabox() {
		$screens = [ 'shop_order', 'shop_subscription' ];
		if ( function_exists( 'wc_get_page_screen_id' ) ) {
			$screens[] = wc_get_page_screen_id( 'shop-order' );
		}

		add_meta_box(
			'checkout_fields_fields_editor',
			__( 'Flexible Checkout Fields', 'flexible-checkout-fields-pro' ),
			[ $this, 'metabox_content' ],
			$screens,
			'advanced'
		);
	}

	/**
	 * Is custom field?
	 *
	 * @param array $field Field.
	 *
	 * @return bool
	 */
	private function is_custom_field( array $field ) {
		if ( isset( $field['custom_field'] ) && 1 === intval( $field['custom_field'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Has sections custom fields?
	 *
	 * @param string $section Section.
	 * @param array  $fields Fields.
	 *
	 * @return bool
	 */
	private function has_section_custom_fields( $section, array $fields ) {
		if ( isset( $fields[ $section ] ) ) {
			foreach ( $fields[ $section ] as $field ) {
				if ( $this->is_custom_field( $field ) ) {
					return true;
				}
			}
		}
		return false;
	}


	/**
	 * Display metabox content.
	 *
	 * @param WP_Post|Automattic\WooCommerce\Admin\Overrides\Order $post Current post object.
	 */
	public function metabox_content( $post ) {
		if ( $post instanceof \WP_Post ) {
			$order = \wc_get_order( $post->ID );
		} else {
			$order = $post;
		}

		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
		$flexible_checkout_fields_plugin = $this->flexible_checkout_fields_pro_plugin->get_flexible_checkout_fields_plugin();
		$sections                        = $flexible_checkout_fields_plugin->sections;
		$fields                          = $flexible_checkout_fields_plugin->getCheckoutFields( [] );
		foreach ( $sections as $section => $section_data ) {
			$fields_section = $section_data['section'];
			if ( $this->has_section_custom_fields( $fields_section, $fields ) ) {
				$this->section_content( $section_data, $fields[ $fields_section ], $order );
			}
		}
		include 'views/metabox-script.php';
	}

	/**
	 * Section content.
	 *
	 * @param array    $section_data   Section data.
	 * @param array    $section_fields Fields.
	 * @param WC_Order $order Order.
	 */
	private function section_content( array $section_data, array $section_fields, WC_Order $order ) {
		$section_title = $section_data['tab_title'];
		include 'views/metabox-section-header.php';
		foreach ( $section_fields as $field_id => $field ) {
			$this->field_content( $field_id, $field, $order );
		}
		include 'views/metabox-section-footer.php';
	}

	/**
	 * Field content.
	 *
	 * @param string   $field_id Field ID.
	 * @param array    $field Field.
	 * @param WC_Order $order Order.
	 */
	private function field_content( $field_id, array $field, WC_Order $order ) {
		if ( $this->is_custom_field( $field ) ) {
			$field['id']   = '_' . $field_id;
			$field['name'] = '_' . $field_id;
			$value         = $order->get_meta( $field['id'] );

			echo apply_filters( 'flexible_checkout_fields_form_field', '', $field_id, $field, $value );
		}
	}

	/**
	 * Is valid request with metabox data.
	 *
	 * @param int    $post_id Post ID.
	 * @param object $post Post.
	 * @return bool
	 */
	private function is_valid_request( $post_id, $post ) {
		if ( ! isset( $_POST[ self::NONCE_NAME ] ) // input var okay.
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ), self::NONCE_ACTION ) ) { // input var okay.
			return false;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		return true;
	}


	/**
	 * Save order field data.
	 *
	 * @param WC_Order $order Order.
	 * @param string   $field_id $field id.
	 * @param array    $field Field.
	 */
	private function update_order_field_data( WC_Order $order, $field_id, array $field ) {
		$field_key   = '_' . $field_id;
		$value       = '';
		$field_value = $_POST[ $field_id ] ?? null; //phpcs:ignore WordPress.Security

		if ( $field_value !== null ) {
			if ( in_array( $field['type'], [ TextareaType::FIELD_TYPE ] ) ) {
				$value = sanitize_textarea_field( wp_unslash( $field_value ) );
			} elseif ( in_array( $field['type'], [ MultiCheckboxType::FIELD_TYPE, MultiSelectType::FIELD_TYPE, FileType::FIELD_TYPE ] ) ) {
				$value = json_encode( wp_unslash( $field_value ), JSON_UNESCAPED_UNICODE );
			} else {
				$value = sanitize_text_field( wp_unslash( $field_value ) );
			}
		}

		$order->update_meta_data( $field_key, $value );
	}

	/**
	 * Save section data.
	 *
	 * @param string   $section Section.
	 * @param array    $fields Fields.
	 * @param WC_Order $order Order.
	 */
	private function save_section_data( $section, array $fields, WC_Order $order ) {
		$section_fields = $fields[ $section ];
		foreach ( $section_fields as $field_id => $field ) {
			if ( $this->is_custom_field( $field ) ) {
				$this->update_order_field_data( $order, $field_id, $field );
			}
		}
	}

	/**
	 * Save metabox post data.
	 *
	 * @param int    $post_id Post ID.
	 * @param object $post Post.
	 * @return bool
	 */
	public function save_metabox_data( $post_id, $post ) {
		if ( $this->is_valid_request( $post_id, $post ) ) {
			remove_action( 'save_post', [ $this, 'save_metabox_data' ], 10 );
			remove_action( 'woocommerce_process_shop_order_meta', [ $this, 'save_metabox_data' ], 50 );

			$order = wc_get_order( $post_id );
			if ( ! $order instanceof \WC_Order ) {
				return;
			}

			$flexible_checkout_fields_plugin = $this->flexible_checkout_fields_pro_plugin->get_flexible_checkout_fields_plugin();
			$sections                        = apply_filters( 'flexible_checkout_fields_sections', $flexible_checkout_fields_plugin->sections );
			$fields                          = $flexible_checkout_fields_plugin->getCheckoutFields( [] );
			foreach ( $sections as $section => $section_data ) {
				$fields_section = $section_data['section'];
				if ( isset( $fields[ $fields_section ] ) ) {
					$this->save_section_data( $fields_section, $fields, $order );
				}
			}
			$order->save();
			return true;
		}
		return false;
	}

}
