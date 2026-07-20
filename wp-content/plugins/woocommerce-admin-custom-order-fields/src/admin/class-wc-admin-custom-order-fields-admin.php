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
 * Admin class.
 *
 * @since 1.0
 */
#[\AllowDynamicProperties]
class WC_Admin_Custom_Order_Fields_Admin {


	/** @var string|mixed page suffix ID */
	public $page_id;

	/** @var WC_Admin_Custom_Order_Fields_Shop_Order|null instance */
	protected ?WC_Admin_Custom_Order_Fields_Shop_Order $shop_order_handler;

	/**
	 * This property should not be accessed directly. Use the getFieldTypes() method instead.
	 * @var array<string, string>
	 */
	protected array $field_types;

	/**
	 * This property should not be accessed directly. Use the getFieldAttributes() method instead.
	 * @var array<string, string>
	 */
	protected array $field_attributes;


	/**
	 * Initializes the class.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// load view order list table / edit order screen customizations
		require_once( plugin_dir_path( __FILE__ ) . 'orders/class-wc-admin-custom-order-fields-shop-order.php' );

		$this->shop_order_handler = new \WC_Admin_Custom_Order_Fields_Shop_Order();

		// load styles/scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// load WC styles / scripts on editor screen
		add_filter( 'woocommerce_screen_ids', array( $this, 'load_wc_scripts' ) );

		// add 'Custom Order Fields' link under WooCommerce menu
		add_action( 'admin_menu', [ $this, 'add_menu_link' ] );
	}

	/**
	 * Gets the field types.
	 *
	 * @since 1.17.2
	 *
	 * @return array<string, string>
	 */
	protected function getFieldTypes() : array
	{
		if ( ! isset( $this->field_types ) ) {
			/**
			 * Filters the field types.
			 *
			 * @since 1.0
			 *
			 * @param array<string, string> $field_types
			 */
			$this->field_types = (array) apply_filters( 'wc_admin_custom_order_fields_field_types', [
				'text'        => __( 'Text', 'woocommerce-admin-custom-order-fields' ),
				'textarea'    => __( 'Text Area', 'woocommerce-admin-custom-order-fields' ),
				'select'      => __( 'Select', 'woocommerce-admin-custom-order-fields' ),
				'multiselect' => __( 'Multiselect', 'woocommerce-admin-custom-order-fields' ),
				'radio'       => __( 'Radio', 'woocommerce-admin-custom-order-fields' ),
				'checkbox'    => __( 'Checkbox', 'woocommerce-admin-custom-order-fields' ),
				'date'        => __( 'Date', 'woocommerce-admin-custom-order-fields' ),
			] );
		}

		return $this->field_types;
	}

	/**
	 * Gets the field attributes.
	 *
	 * @since 1.17.2
	 *
	 * @return array<string, string>
	 */
	protected function getFieldAttributes() : array
	{
		if ( ! isset( $this->field_attributes ) ) {
			/**
			 * Filters the field attributes.
			 *
			 * @since 1.0
			 *
			 * @param array<string, string> $field_attributes
			 */
			$this->field_attributes = (array) apply_filters( 'wc_admin_custom_order_fields_field_attributes', [
				'visible'    => __( 'Show in My Orders / Emails', 'woocommerce-admin-custom-order-fields' ),
				'required'   => __( 'Required', 'woocommerce-admin-custom-order-fields' ),
				'listable'   => __( 'Display in View Orders screen', 'woocommerce-admin-custom-order-fields' ),
				'sortable'   => __( 'Allow Sorting on View Orders screen', 'woocommerce-admin-custom-order-fields' ),
				'filterable' => __( 'Allow Filtering on View Orders screen', 'woocommerce-admin-custom-order-fields' )
			] );
		}
		return $this->field_attributes;
	}


	/**
	 * Loads admin scripts and styles.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param string $hook_suffix the current URL filename, ie edit.php, post.php, etc
	 */
	public function load_styles_scripts( $hook_suffix ) {
		global $post_type, $wp_scripts;

		// load admin css only on view orders / edit order screens
		if ( ( 'shop_order' === $post_type && ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) ) || $this->page_id === $hook_suffix || Framework\SV_WC_Order_Compatibility::is_order_edit_screen() || Framework\SV_WC_Order_Compatibility::is_orders_screen() ) {

			// admin CSS
			wp_enqueue_style( 'wc-admin-custom-order-fields-admin', wc_admin_custom_order_fields()->get_plugin_url() . '/assets/css/admin/wc-admin-custom-order-fields.min.css', [ 'woocommerce_admin_styles' ], \WC_Admin_Custom_Order_Fields::VERSION );

			// load JS only on editor screen
			if ( $this->page_id === $hook_suffix || Framework\SV_WC_Order_Compatibility::is_order_edit_screen() ) {

				// admin JS
				wp_enqueue_script( 'wc-admin-custom-order-fields-admin', wc_admin_custom_order_fields()->get_plugin_url() . '/assets/js/admin/wc-admin-custom-order-fields.min.js', [ 'jquery', 'jquery-ui-sortable', 'woocommerce_admin' ], \WC_Admin_Custom_Order_Fields::VERSION );

				$params = [
					'new_row'                  => str_replace( [ "\n", "\t" ], '', $this->get_row_html() ),
					'label_required_text'      => __( 'Label is a required field', 'woocommerce-admin-custom-order-fields' ),
					'option_required_text'     => __( 'A select/multiselect/checkbox/radio field must have at least one option', 'woocommerce-admin-custom-order-fields' ),
					'default_placeholder_text' => __( 'Pipe (|) separates options', 'woocommerce-admin-custom-order-fields' ),
				];

				// add HTML for adding new fields
				wp_localize_script( 'wc-admin-custom-order-fields-admin', 'wc_admin_custom_order_fields_params', $params );

				// add jQuery DatePicker
				wp_enqueue_script( 'jquery-ui-datepicker' );

				// get jQuery UI version
				$jquery_version = $wp_scripts->registered['jquery-ui-core']->ver ?? '1.9.2';

				// enqueue UI CSS
				wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
			}
		}
	}


	/**
	 * Add settings/export screen ID to the list of pages for WC to load its JS on
	 *
	 * @since 1.0
	 * @param array $screen_ids
	 * @return array
	 */
	public function load_wc_scripts( $screen_ids ) {

		// sub-menu page screen ID
		$screen_ids[] = Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'wc_admin_custom_order_fields' );;

		return $screen_ids;
	}


	/**
	 * Add 'Order Custom Fields' sub-menu link under 'WooCommerce' top level menu
	 *
	 * @since 1.0
	 */
	public function add_menu_link() {

		$this->page_id = add_submenu_page(
			'woocommerce',
			__( 'Custom Order Fields', 'woocommerce-admin-custom-order-fields' ),
			__( 'Custom Order Fields', 'woocommerce-admin-custom-order-fields' ),
			'manage_woocommerce',
			'wc_admin_custom_order_fields',
			array( $this, 'render_editor_screen' )
		);
	}


	/**
	 * Render the custom order fields editor
	 *
	 * @since 1.0
	 */
	public function render_editor_screen() {

		?>
		<div class="wrap woocommerce">

			<form method="post" id="mainform" action="" enctype="multipart/form-data" class="wc-admin-custom-order-fields">

				<div id="icon-woocommerce" class="icon32"><br /></div>

				<h2><?php esc_html_e( 'Custom Order Fields Editor', 'woocommerce-admin-custom-order-fields' ); ?></h2>

				<?php
					// save custom fields
					if ( ! empty( $_POST ) ) {
						$this->save_custom_fields();
					}

					// show custom field editor
					$this->render_editor();
				?>

			</form>

		</div>
		<?php
	}


	/**
	 * Render the custom order fields editor table
	 *
	 * @since 1.0
	 */
	private function render_editor() {

		?>
		<div class="wc-admin-custom-order-fields-editor-content">

			<table class="widefat wc-admin-custom-order-fields-editor">

				<thead>
					<tr>
						<th class="check-column"><input type="checkbox" /></th>
						<th class="wc-custom-order-field-label"><?php esc_html_e( 'Label', 'woocommerce-admin-custom-order-fields'); ?></th>
						<th width="1%" class="wc-custom-order-field-type"><?php esc_html_e( 'Type', 'woocommerce-admin-custom-order-fields' ); ?></th>
						<th class="wc-custom-order-field-description"><?php esc_html_e( 'Description', 'woocommerce-admin-custom-order-fields' ); ?></th>
						<th class="wc-custom-order-field-default-values"><?php esc_html_e( 'Default / Values', 'woocommerce-admin-custom-order-fields' ); ?></th>
						<th class="wc-custom-order-field-attributes"><?php esc_html_e( 'Attributes', 'woocommerce-admin-custom-order-fields' ); ?></th>
						<th class="js-wc-custom-order-field-draggable"></th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th colspan="3">
							<button type="button" class="button button-secondary js-wc-admin-custom-order-fields-add-field">&nbsp;&#43; <?php esc_html_e( 'Add Field', 'woocommerce-admin-custom-order-fields' ); ?></button>
							<button type="button" class="button button-secondary js-wc-admin-custom-order-fields-remove"><?php esc_html_e( 'Remove Selected', 'woocommerce-admin-custom-order-fields' ); ?></button>
						</th>
						<th colspan="5"><input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Fields', 'woocommerce-admin-custom-order-fields' ); ?>"/></th>
					</tr>
				</tfoot>

				<tbody>
					<?php

					$index = 0;

					foreach ( wc_admin_custom_order_fields()->get_order_fields() as $custom_field_id => $custom_field ) {

						echo $this->get_row_html( $index, $custom_field_id, $custom_field ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						$index++;
					}

					?>
				</tbody>
			</table>
		</div>
		<?php

		wp_nonce_field( __FILE__ );
	}


	/**
	 * Return the HTML for a new field row
	 *
	 * @since 1.0
	 * @param int $index the row index
	 * @param int $field_id the ID of the custom field
	 * @param \WC_Custom_Order_Field|null $field the custom field data
	 * @return string the HTML
	 */
	private function get_row_html( $index = null, $field_id = null, $field = null ) {

		$field_types      = $this->getFieldTypes();
		$field_attributes = $this->getFieldAttributes();

		if ( is_object( $field ) ) {

			// convert options and defaults back into a simple string
			if ( $field->has_options() ) {

				$values = array();

				foreach( $field->get_options() as $option ) {

					// skip blank option added for non-required select/multiselect
					// check explicitly for 0, which is a valid option label
					if ( '0' !== $option['label'] && empty( $option['label'] ) ) {
						continue;
					}

					$values[] = $option['selected'] ? '**' . $option['label'] . '**' : $option['label'];
				}

				$field->default = implode( ' | ', $values );
			}

			// convert date field default from timestamp into string
			if ( 'date' === $field->type && isset( $field->default ) && 'now' !== $field->default ) {
				$field->default = date( 'Y-m-d', $field->default );
			}
		}

		ob_start();

		require( 'views/html-field-editor-table-row.php' );

		return ob_get_clean();
	}


	/**
	 * Save the custom fields
	 *
	 * @since 1.0
	 */
	private function save_custom_fields() {

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ), __FILE__ ) ) {
			wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'woocommerce-admin-custom-order-fields' ) );
		}

		$fields = array();

		if ( ! empty( $_POST['wc-custom-order-field-id'] ) ) {

			$field_ids = count( $_POST['wc-custom-order-field-id'] );

			for ( $index = 0; $index < $field_ids; $index++ ) {

				// ID - assigned if empty
				$field_id = empty( $_POST['wc-custom-order-field-id'][ $index ] ) ? $this->get_next_field_id() : absint( $_POST['wc-custom-order-field-id'][ $index ] );

				$fields[ $field_id ] = array();

				// label
				$fields[ $field_id ]['label'] = sanitize_text_field( stripslashes( $_POST['wc-custom-order-field-label'][ $index ] ?: '' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				// type
				$type = sanitize_text_field( $_POST['wc-custom-order-field-type'][ $index ] );
				$fields[ $field_id ]['type'] = ( in_array( $type, array_keys( $this->getFieldTypes() ) ) ) ? $type : 'text';

				// description
				if ( ! empty( $_POST['wc-custom-order-field-description'][ $index ] ) ) {
					$fields[ $field_id ]['description'] = sanitize_text_field( stripslashes( $_POST['wc-custom-order-field-description'][ $index ] ?: '' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				}

				// default / values
				if ( ! empty( $_POST['wc-custom-order-field-default-values'][ $index ] ) ) {

					switch ( $fields[ $field_id ]['type'] ) {

						// text/textarea fields have simple text defaults and no options
						case 'text':
						case 'textarea':
							$fields[ $field_id ]['default'] = sanitize_text_field( stripslashes( $_POST['wc-custom-order-field-default-values'][ $index ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						break;

						// select/checkbox/radio fields have multiple options and a single default, multiselect has multiple options and multiple defaults
						case 'select':
						case 'multiselect':
						case 'checkbox':
						case 'radio':

							$options = array_map( 'sanitize_text_field', explode( '|', $_POST['wc-custom-order-field-default-values'][ $index ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

							foreach ( $options as $option ) {

								$fields[ $field_id ]['options'][] = array(
									'default' => false !== strpos( $option, '**' ),
									'label'   => stripslashes( str_replace( '**', '', $option ) ),
									'value'   => $this->sanitize_option_key( $option ),
								);
							}

						break;

						// date is saved as a unix timestamp (UTC), `now` is a special default
						case 'date':
							$date_string = sanitize_text_field( $_POST['wc-custom-order-field-default-values'][ $index ] );
							$fields[ $field_id ]['default'] = ( 'now' === $date_string ) ? 'now' : strtotime( $date_string );
						break;

						// allow custom field types
						// it's up to implementors to handle their own sanitization
						default:

							$fields[ $field_id ]['default'] = apply_filters( 'wc_admin_custom_order_fields_' . $fields[ $field_id ]['type'] . '_default', '',      $_POST['wc-custom-order-field-default-values'][ $index ], $field_id ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
							$fields[ $field_id ]['options'] = apply_filters( 'wc_admin_custom_order_fields_' . $fields[ $field_id ]['type'] . '_options', array(), $_POST['wc-custom-order-field-default-values'][ $index ], $field_id ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					}

				} else {

					$fields[ $field_id ]['default'] = null;
				}

				// attributes - true/false for each
				if ( ! empty( $_POST['wc-custom-order-field-attributes'][ $index ] ) ) {

					foreach ( array( 'required', 'visible', 'listable', 'sortable', 'filterable' ) as $attribute ) {
						$fields[ $field_id ][ $attribute ] = ( in_array( $attribute, $_POST['wc-custom-order-field-attributes'][ $index ] ) );
					}

					// add the listable attribute if either sortable or filterable were added
					if ( ! $fields[ $field_id ]['listable'] && ( $fields[ $field_id ]['sortable'] || $fields[ $field_id ]['filterable'] ) ) {
						$fields[ $field_id ]['listable'] = true;
					}
				}

				// scope (not exposed in editor right now)
				$fields[ $field_id ]['scope'] = 'order';
			}
		}

		if ( true === update_option( 'wc_admin_custom_order_fields', $fields ) ) {

			echo '<div class="updated"><p>' . esc_html__( 'Custom Fields Saved', 'woocommerce-admin-custom-order-fields' ) . '</p></div>';

			/**
			 * Fires upon saving custom order fields.
			 *
			 * @since 1.11.0
			 *
			 * @param array $posted_data form data
			 * @param array $fields saved fields
			 */
			do_action( 'wc_admin_custom_order_fields_saved_fields', $_POST, $fields );
		}
	}


	/**
	 * Get the next available custom field ID
	 *
	 * @since 1.0
	 * @return int the next available field ID
	 */
	private function get_next_field_id() {

		$next_field_id = (int) get_option( 'wc_admin_custom_order_fields_next_field_id', 0 );

		update_option( 'wc_admin_custom_order_fields_next_field_id', ++$next_field_id );

		return $next_field_id;
	}


	/**
	 * Sanitize option key
	 *
	 * This was introduced to ensure that non-latin options
	 * in fields with options would be saved correctly
	 *
	 * @since 1.6.1
	 * @param mixed|string $string
	 * @return string
	 */
	private function sanitize_option_key( $string ) {

		// sanity check
		// check explicitly for 0 as a value, which is valid
		if ( '0' !== $string && ( ! is_string( $string ) || empty( $string ) ) ) {
			return '';
		}

		// detects if a string contains non-western characters and applies
		// a more flexible sanitization while retaining backwards compatibility
		// with previous uses of `sanitize_key()`
		if ( preg_match( '/[^\\p{Common}\\p{Latin}]/u', $string ) > 0 ) {
			$string = sanitize_text_field( $string );
		} else {
			$string = sanitize_key( $string );
		}

		return $string;
	}


} // end \WC_Admin_Custom_Order_Fields_Admin class
