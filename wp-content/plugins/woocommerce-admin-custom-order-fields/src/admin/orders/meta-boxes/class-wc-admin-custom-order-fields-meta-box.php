<?php
/**
 * WooCommerce Admin Custom Order Fields
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Admin Custom Order Fields to newer
 * versions in the future. If you wish to customize WooCommerce Admin Custom Order Fields for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-admin-custom-order-fields/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2026, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v6_2_0 as Framework;

/**
 * Meta-box adds, renders, and save the custom order fields displayed on the Edit Order screen
 *
 * @since 1.0
 */
class WC_Admin_Custom_Order_Fields_Meta_Box {


	/**
	 * Adds meta box actions.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// add the meta box
		add_action( 'add_meta_boxes', [ $this, 'add' ] );

		// save the meta box
		add_action( 'woocommerce_process_shop_order_meta', [ $this, 'save' ], 10, 2 );
	}


	/**
	 * Add the meta-box
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function add() {

		add_meta_box(
			'wc-order-custom-fields',
			__( 'Order Custom Fields', 'woocommerce-admin-custom-order-fields' ),
			array( $this, 'render' ),
			Framework\SV_WC_Order_Compatibility::get_order_screen_id(),
			'normal',
			'default'
		);
	}


	/**
	 * Render the custom order fields.
	 *
	 * Displays the order custom fields meta box for displaying and configuring any custom fields attached to the order.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function render() {
		global $post, $theorder;

		$order_id_or_object = Framework\SV_WC_Plugin_Compatibility::is_hpos_enabled() ? $theorder : $post->ID;
		$order_fields = wc_admin_custom_order_fields()->get_order_fields( $order_id_or_object );

		if ( ! empty( $order_fields ) ) :

			?>
			<ul>

				<?php foreach ( $order_fields as $field ) : ?>

					<li class="form-field">

						<label for="wc-admin-custom-order-fields-input-<?php echo esc_attr( $field->id ); ?>">
							<?php esc_html_e( $field->label, 'woocommerce-admin-custom-order-fields' ); ?>
							<?php if ( $field->is_required() ) : ?>
								<span class="required">*</span>
							<?php endif; ?>
							<?php if ( ! empty( $field->description ) ) : ?>
								<?php echo wc_help_tip( $field->description ); ?>
							<?php endif; ?>
						</label>

						<?php

							$name = sprintf( 'wc-admin-custom-order-fields[%s]', esc_attr( $field->id ) );
							$id   = sprintf( 'wc-admin-custom-order-fields-input-%s', esc_attr( $field->id ) );
							$type = in_array( $field->type, array( 'select', 'multiselect' ), true ) ? 'dropdown' : $field->type;
							$view = wc_admin_custom_order_fields()->get_plugin_path() . "/src/admin/orders/meta-boxes/views/html-{$type}-field.php";

							if ( is_readable( $view ) ) {
								require( $view );
							}

						?>

					</li>

				<?php endforeach; ?>

			</ul>

			<div style="clear: both;"></div>
			<?php

		endif;
	}


	/**
	 * Persists any order custom fields.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param int $order_id the WC_Order ID
	 * @param \WP_Post|\WC_Order $object the WC_Order object or post object according to HPOS
	 */
	public function save( $order_id, $object ) {

		/**
		 * Due to the mixed type, each array item will be sanitized individually when saved.
		 * @var array<int, int|string|string[]>|null $updated_custom_fields can contain integers, strings, or arrays of strings, or be null if the POST key is absent
		 */
		$updated_custom_fields = $_POST['wc-admin-custom-order-fields'] ?? null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( empty( $updated_custom_fields ) ) {
			return;
		}

		if ( $object instanceof \WC_Order ) {
			$order = $object;
		} else {
			$order = wc_get_order( $order_id );
		}

		if ( ! $order ) {
			return;
		}

		$order_fields = wc_admin_custom_order_fields()->get_order_fields();

		foreach ( $order_fields as $custom_field ) {

			$field_id       = $custom_field->get_id();
			$field_meta_key = $custom_field->get_meta_key();
			$updated_value  = $updated_custom_fields[$field_id] ?? '';

			// Update a custom field value unless it's empty...
			// A value of 0 is valid, so check for that first.
			// Empty string is also allowed to clear out custom fields completely.
			if ( '0' === $updated_value || '' === $updated_value || ! empty( $updated_value ) ) {

				// Special handling for date fields.
				if ( 'date' === $order_fields[ $field_id ]->get_type() ) {

					$updated_value = strtotime( sanitize_text_field( $updated_value ) );

					$order_fields[ $field_id ]->set_value( $updated_value );

					$order->update_meta_data( $field_meta_key, $order_fields[ $field_id ]->get_value() );

					// This column is used so that date fields can be searchable.
					$order->update_meta_data( $field_meta_key . '_formatted', $order_fields[ $field_id ]->get_value_formatted() );

				} else {
					if (is_array( $updated_value )) {
						$updated_value = array_map( 'sanitize_text_field', $updated_value );
					} elseif('textarea' === $order_fields[ $field_id ]->get_type()) {
						$updated_value = sanitize_textarea_field( $updated_value );
					} else {
						$updated_value = sanitize_text_field( $updated_value );
					}

					$order->update_meta_data( $field_meta_key, $updated_value );
				}

			// ...Or if it's empty, delete the custom field meta altogether.
			} else {

				$order->delete_meta_data( $field_meta_key );
				$order->delete_meta_data( $field_meta_key . '_formatted' );
			}

			$order->save_meta_data();
			$order->save();
		}
	}


}
