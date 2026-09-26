<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_Conditional_Content_Compatibility' ) ) :
	class WC_Conditional_Content_Compatibility {

		public static function wc_attribute_label( $label ): string {
			return wc_attribute_label( $label );
		}

		public static function wc_attribute_taxonomy_name( $name ): string {
			return wc_attribute_taxonomy_name( $name );
		}

		public static function wc_get_attribute_taxonomies(): array {
			return wc_get_attribute_taxonomies();
		}

		public static function wc_placeholder_img_src( $size = 'woocommerce_thumbnail' ): string {
			return wc_placeholder_img_src( $size );
		}

		public static function woocommerce_get_formatted_product_name( $product ) {
			if ( $product->is_type( 'variation' ) ) {
				return $product->get_name();
			} else {
				return $product->get_formatted_name();
			}
		}

		/**
		 * Compatibility function to add and store a notice
		 *
		 * @param string $message The text to display in the notice.
		 * @param string $notice_type The singular name of the notice type - either error, success or notice. [optional]
		 *
		 * @since 1.0
		 */
		public static function wc_add_notice( $message, $notice_type = 'success' ): void {
			wc_add_notice( $message, $notice_type );
		}

		/**
		 * Prints messages and errors which are stored in the session, then clears them.
		 *
		 * @param bool $return true to return rather than echo. @since 3.5.0.
		 *
		 * @return string|void
		 * @since 2.1
		 */
		public static function wc_print_notices( $return_the_value = false ) {
			if ( $return_the_value ) {
				return wc_print_notices();
			} else {
				wc_print_notices();
			}
		}

		/**
		 * Compatibility function to queue some JavaScript code to be output in the footer.
		 *
		 * @param string $code javascript
		 *
		 * @since 1.0
		 */
		public static function wc_enqueue_js( string $code ): void {
			wc_enqueue_js( $code );
		}


		/**
		 * Format decimal numbers ready for DB storage
		 *
		 * Sanitize, remove locale formatting, and optionally round + trim off zeros
		 *
		 * @param float|string $number Expects either a float or a string with a decimal separator only (no thousands)
		 * @param mixed $dp number of decimal points to use, blank to use woocommerce_price_num_decimals, or false to avoid all rounding.
		 * @param boolean $trim_zeros from end of string
		 *
		 * @return string
		 * @since 1.0
		 */
		public static function wc_format_decimal( $number, $dp = false, $trim_zeros = false ): string {
			return wc_format_decimal( $number, $dp, $trim_zeros );
		}

		/**
		 * Get the count of notices added, either for all notices (default) or for one particular notice type specified
		 * by $notice_type.
		 *
		 * @param string $notice_type The name of the notice type - either error, success or notice. [optional]
		 *
		 * @return int the notice count
		 * @since 1.0
		 */
		public static function wc_notice_count( $notice_type = '' ): int {
			return wc_notice_count( $notice_type );
		}

		/**
		 * Compatibility function to get the version of the currently installed WooCommerce
		 *
		 * @return string woocommerce version number or null
		 * @since 1.0
		 */
		public static function get_wc_version(): string {

			// WOOCOMMERCE_VERSION is now WC_VERSION, though WOOCOMMERCE_VERSION is still available for backwards compatibility, we'll disregard it on 2.1+
			if ( defined( 'WC_VERSION' ) && WC_VERSION ) {
				return WC_VERSION;
			}

			if ( defined( 'WOOCOMMERCE_VERSION' ) && WOOCOMMERCE_VERSION ) {
				return WOOCOMMERCE_VERSION;
			}

			return '0';
		}

		/**
		 * Returns the WooCommerce instance
		 *
		 * @return WooCommerce woocommerce instance
		 * @since 1.0
		 */
		public static function WC(): WooCommerce {
			return WC();
		}

		/**
		 * Returns true if the installed version of WooCommerce is greater than $version
		 *
		 * @param string $version the version to compare
		 *
		 * @return boolean true if the installed version of WooCommerce is > $version
		 * @since 1.0
		 */
		public static function is_wc_version_gt( $version ): bool {
			return self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>' );
		}
	}

endif; // Class exists check
