<?php
/**
 * WC_PB_Members_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Memberships Integration: Discounts inheritance.
 *
 * @version  8.5.12
 */
class WC_PB_Members_Compatibility {

	/**
	 * Decimals kept in an inherited discount percentage.
	 *
	 * @since 8.5.12
	 *
	 * @var int
	 */
	const DISCOUNT_PRECISION = 12;

	/**
	 * Runtime cache.
	 *
	 * @var boolean
	 */
	private static $member_is_logged_in;

	/**
	 * Flag used to prevent 'wc_memberships_exclude_product_from_member_discounts' from changing the return value.
	 *
	 * @var boolean
	 */
	private static $calculating_inherited_discounts = false;

	/**
	 * Control flag used in inherit_member_discount().
	 *
	 * @var boolean
	 */
	private static $inherit_member_discount;

	/**
	 * Initialization.
	 */
	public static function init() {

		$is_ajax = wp_doing_ajax();

		// See 'WC_Memberships_Member_Discounts'.
		if ( ! ( is_admin() && ! $is_ajax ) ) {

			if ( 'filters' === WC_PB_Product_Prices::get_bundled_cart_item_discount_method() ) {

				// Bundle membership discounts are inherited by bundled items and applied here.
				add_filter( 'woocommerce_bundled_item_discount', array( __CLASS__, 'inherit_member_discount' ), 10, 4 );

				// Enable/disable discount filtering.
				add_action( 'wc_memberships_discounts_enable_price_adjustments', array( __CLASS__, 'enable_member_discount_inheritance' ) );
				add_action( 'wc_memberships_discounts_disable_price_adjustments', array( __CLASS__, 'disable_member_discount_inheritance' ) );
			}
		}

		// Prevent Memberships from applying member discounts to bundled products -- membership discounts are inherited.
		add_filter( 'wc_memberships_exclude_product_from_member_discounts', array( __CLASS__, 'exclude_bundled_product_from_member_discounts' ), 10, 2 );
	}

	/**
	 * Whether the current user has an active membership.
	 *
	 * @return bool
	 */
	private static function member_is_logged_in() {

		if ( null === self::$member_is_logged_in ) {
			self::$member_is_logged_in = wc_memberships_is_user_member( get_current_user_id() );
		}

		return self::$member_is_logged_in;
	}

	/**
	 * Inherit Memberships discounts as bundled item discounts.
	 *
	 * @param  mixed           $discount The discount applied to the bundled item.
	 * @param  WC_Bundled_Item $bundled_item The bundled item.
	 * @param  string          $context The context in which the discount is calculated.
	 * @param  WC_Product      $product The product the discount is calculated for -- the chosen variation, when the bundled product is variable.
	 * @return mixed
	 */
	public static function inherit_member_discount( $discount, $bundled_item, $context, $product = null ) {

		if ( ! in_array( $context, array( 'config', 'any' ), true ) ) {
			return $discount;
		}

		$is_memberships_version_gte_1_21_8 = version_compare( WC_Memberships::VERSION, '1.21.7', '>' );

		if ( ! $is_memberships_version_gte_1_21_8 ) {
			if ( ! self::member_is_logged_in() ) {
				return $discount;
			}
		}

		if ( ! self::$inherit_member_discount ) {
			return $discount;
		}

		// Don't recalculate discounts, avoid infinite loops.
		if ( self::$calculating_inherited_discounts ) {
			return $discount;
		}

		// Flag to prevent 'exclude_bundled_product_from_member_discounts' from kicking in.
		self::$calculating_inherited_discounts = true;

		$bundle          = $bundled_item->get_bundle();
		$bundled_product = $bundled_item->get_product();

		// If the bundle is excluded from member discounts, don't apply any discounts.
		if ( wc_memberships()->get_member_discounts_instance()->is_product_excluded_from_member_discounts( $bundle ) ) {
			self::$calculating_inherited_discounts = false;
			return $discount;
		}

		// If the product itself is excluded from member discounts, don't apply any discounts.
		if ( wc_memberships()->get_member_discounts_instance()->is_product_excluded_from_member_discounts( $bundled_product ) ) {
			self::$calculating_inherited_discounts = false;
			return $discount;
		}

		/*
		 * Memberships purchasing-discount rules can target a single variation. Rules assigned to a variation are
		 * invisible to a lookup made with the ID of its variable parent, so when the chosen variation is known,
		 * resolve rules against it instead.
		 *
		 * Resolving against the variation does not lose the rules assigned to its parent: when the queried object
		 * is a variation, 'WC_Memberships_Rules::query_rules()' falls back to matching purchasing-discount rules
		 * against its 'post_parent', and matches product category rules against the parent's terms. That fallback
		 * has been in place since Memberships v1.1.1, and both branches below reach it through the same
		 * 'WC_Memberships_Rules::get_product_purchasing_discount_rules()'.
		 *
		 * The exclusion checks above stay on the parent on purpose: the '_wc_memberships_exclude_discounts' meta
		 * is only ever stored on the parent product, and Memberships expands it to every variation itself.
		 */
		$discount_product = $bundled_product;

		if ( is_a( $product, 'WC_Product' ) && $product->is_type( 'variation' ) && $product->get_parent_id() === $bundled_product->get_id() ) {
			$discount_product = $product;
		}

		$member_id             = get_current_user_id();
		$parent_discount_rules = array();
		$child_discount_rules  = array();
		$discount_rules        = array();

		if ( wc_memberships()->get_member_discounts_instance()->user_has_member_discount( $bundle ) ) {
			if ( $is_memberships_version_gte_1_21_8 ) {
				// This function was private up to WooCommerce Memberships v1.21.8. Fallback to legacy code for previous versions to avoid fatal errors.
				$parent_discount_rules = wc_memberships()->get_member_discounts_instance()->get_user_product_purchasing_discount_rules( $member_id, $bundle->get_id() );
			} else {
				$parent_discount_rules = wc_memberships()->get_rules_instance()->get_user_product_purchasing_discount_rules( $member_id, $bundle->get_id() );
			}
		}

		if ( wc_memberships()->get_member_discounts_instance()->user_has_member_discount( $discount_product ) ) {
			if ( $is_memberships_version_gte_1_21_8 ) {
				// This function was private up to WooCommerce Memberships v1.21.8. Fallback to legacy code for previous versions to avoid fatal errors.
				$child_discount_rules = wc_memberships()->get_member_discounts_instance()->get_user_product_purchasing_discount_rules( $member_id, $discount_product->get_id() );
			} else {
				$child_discount_rules = wc_memberships()->get_rules_instance()->get_user_product_purchasing_discount_rules( $member_id, $discount_product->get_id() );
			}
		}

		$discount_rules_merged = array_merge( $parent_discount_rules, $child_discount_rules );

		// Make sure we don't apply the same membership discount twice.
		foreach ( $discount_rules_merged as $discount_rule ) {
			if ( empty( $discount_rules[ $discount_rule->get_id() ] ) ) {
				$discount_rules[ $discount_rule->get_id() ] = $discount_rule;
			}
		}

		/**
		 * 'woocommerce_bundled_item_member_discount_rules' filter.
		 *
		 * Use this filter to modify the discount rules, for example to use bundle-level or product-level discount rules only.
		 *
		 * @since 6.0.0
		 * @param  array            $discount_rules
		 * @param  array            $parent_discount_rules
		 * @param  array            $child_discount_rules
		 * @param  WC_Bundled_Item  $bundled_item
		 */
		$discount_rules = apply_filters( 'woocommerce_bundled_item_member_discount_rules', $discount_rules, $parent_discount_rules, $child_discount_rules, $bundled_item );

		if ( empty( $discount_rules ) ) {
			self::$calculating_inherited_discounts = false;
			return $discount;
		}

		/**
		 * 'wc_memberships_allow_cumulative_member_discounts' filter.
		 *
		 * @since 6.0.0
		 * @param boolean  $allow_cumulative
		 * @param int      $member_id
		 * @param WC_Product_Bundle $bundle
		 */
		$allow_cumulative = apply_filters( 'wc_memberships_allow_cumulative_member_discounts', true, $member_id, $bundle );

		$discount = is_numeric( $discount ) ? (float) $discount : 0.0;
		$discount = self::apply_discount_rules( $discount, $discount_rules, $child_discount_rules, $bundled_item, $discount_product, $allow_cumulative );

		self::$calculating_inherited_discounts = false;

		/**
		 * 'woocommerce_bundled_item_member_discount' filter.
		 *
		 * Use this filter to modify the membership discount applied on bundled products.
		 *
		 * @since 6.0.0
		 * @param  float            $discount
		 * @param  array            $discount_rules
		 * @param  WC_Bundled_Item  $bundled_item
		 */
		return apply_filters( 'woocommerce_bundled_item_member_discount', $discount, $discount_rules, $bundled_item );
	}

	/**
	 * Folds Memberships purchasing-discount rules into a single bundled item discount percentage.
	 *
	 * Bundled item discounts are percentages, while Memberships rules come in two flavours -- 'percentage' and
	 * 'amount', an absolute monetary value. To support both, the rules are applied to the price the bundled item
	 * discount would be applied to, and the resulting price is expressed back as a percentage of that same price.
	 * The cumulative handling, the zero-clamping and the skipping of rules that do not lower the price all mirror
	 * 'WC_Memberships_Member_Discounts::get_discounted_price()', so that a bundled item lands on the same price the
	 * product would have on its own.
	 *
	 * Percentage-only rule sets fold exactly as they did before absolute amounts were supported: compounding
	 * percentages and multiplying prices are the same operation.
	 *
	 * Only amounts from rules that target the bundled product are applied. A rule assigned to the bundle is
	 * inherited by every individually priced item in it, which is exact for a percentage and wrong for an amount:
	 * "20 off this bundle" would come off each item, and off each unit of it, discounting several times what the
	 * rule asks for. Such rules keep being ignored, as they were before absolute amounts were supported.
	 *
	 * @since 8.5.12
	 *
	 * @param  float           $discount         The bundled item discount, as a percentage.
	 * @param  array           $discount_rules   The Memberships purchasing-discount rules to apply.
	 * @param  array           $product_rules    Of those, the rules that resolve against the bundled product itself,
	 *                                           rather than being inherited from the bundle.
	 * @param  WC_Bundled_Item $bundled_item     The bundled item.
	 * @param  WC_Product      $discount_product The product the discount is calculated for.
	 * @param  boolean         $allow_cumulative Whether rules stack, or the cheapest one wins.
	 * @return float
	 */
	private static function apply_discount_rules( $discount, $discount_rules, $product_rules, $bundled_item, $discount_product, $allow_cumulative ) {
		/*
		 * The price a bundled item discount is applied to -- see 'WC_Bundled_Item::get_raw_price()'. Read in 'edit'
		 * context to stay clear of the price filters this runs inside.
		 */
		$base_price = false === $bundled_item->is_discount_allowed_on_sale_price() ? $discount_product->get_regular_price( 'edit' ) : $discount_product->get_price( 'edit' );
		$base_price = is_numeric( $base_price ) ? (float) $base_price : 0.0;

		/*
		 * Resolved when the first amount rule that could use it comes up, and cached for the rest of them. Reading
		 * whether a product has children costs a couple of queries on a variable product that has not read them yet,
		 * and most rule sets are percentages, which never ask.
		 */
		$can_apply_amounts = null;

		// Rules that resolve against the bundled product itself, keyed by ID. Only these contribute an amount.
		$product_rule_ids = array();

		foreach ( (array) $product_rules as $product_rule ) {
			if ( is_object( $product_rule ) && method_exists( $product_rule, 'get_id' ) ) {
				$product_rule_ids[ $product_rule->get_id() ] = true;
			}
		}

		// Percentages are ratios, so they fold on any non-zero reference price. An amount needs the real one, and is skipped without it.
		$reference_price = $base_price > 0 ? $base_price : 1.0;

		$discounted_price      = $reference_price * ( 100 - $discount ) / 100;
		$non_cumulative_prices = array();

		foreach ( $discount_rules as $rule ) {

			$rule_amount = (float) $rule->get_discount_amount();

			if ( $rule_amount <= 0 ) {
				continue;
			}

			switch ( $rule->get_discount_type() ) {
				case 'percentage':
					$rule_price = $discounted_price * ( 100 - $rule_amount ) / 100;
					break;
				case 'amount':
					if ( ! isset( $product_rule_ids[ $rule->get_id() ] ) ) {
						continue 2;
					}

					if ( null === $can_apply_amounts ) {
						/*
						 * An absolute amount only translates into a percentage when the price it is taken off is both
						 * known and non-zero. It is neither for a product with children -- a variable parent carries
						 * the lowest price of its variations, not the price of the one being purchased -- which
						 * happens when a caller looks up a discount without telling us which variation the customer
						 * chose.
						 */
						$can_apply_amounts = $base_price > 0 && ! $discount_product->has_child();
					}

					if ( ! $can_apply_amounts ) {
						continue 2;
					}

					$rule_price = $discounted_price - $rule_amount;
					break;
				default:
					continue 2;
			}

			// Memberships skips rules that do not lower the price.
			if ( $rule_price >= $discounted_price ) {
				continue;
			}

			if ( $allow_cumulative ) {
				$discounted_price = max( $rule_price, 0.0 );
			} else {
				$non_cumulative_prices[] = max( $rule_price, 0.0 );
			}
		}

		if ( ! empty( $non_cumulative_prices ) ) {
			$discounted_price = min( $non_cumulative_prices );
		}

		/*
		 * The percentage is stored in cart stamps and order item meta, so trim the floating point noise that a
		 * round trip through prices leaves behind -- a plain 20% rule must stay 20, not 19.999999999999996. Prices
		 * are rounded to 'WC_PB_Product_Prices::get_discounted_price_precision()' decimals afterwards, well short
		 * of what the digits kept here can shift.
		 */
		return round( 100 * ( 1 - $discounted_price / $reference_price ), self::DISCOUNT_PRECISION );
	}

	/**
	 * Prevent Memberships from applying member discounts to bundled products -- membership discounts are inherited.
	 *
	 * @param  boolean    $exclude Whether to exclude the product from member discounts.
	 * @param  WC_Product $product The product.
	 * @return boolean
	 */
	public static function exclude_bundled_product_from_member_discounts( $exclude, $product ) {

		if ( is_numeric( $product ) ) {
			$product = WC_PB_Helpers::cache_get( 'mb_compat_product_' . $product );

			if ( is_null( $product ) ) {
				$product = wc_get_product( $product );
				if ( is_a( $product, 'WC_Product' ) ) {
					WC_PB_Helpers::cache_set( 'mb_compat_product_' . $product->get_id(), $product );
				}
			}
		}

		if ( $product && is_a( $product, 'WC_Product' ) ) {
			if ( WC_PB_Product_Prices::is_bundled_pricing_context( $product, 'catalog' ) && ! self::$calculating_inherited_discounts ) {
				$exclude = true;
			}

			if ( WC_PB_Product_Prices::is_bundled_pricing_context( $product, 'cart' ) ) {
				$exclude = true;
			}
		}

		return $exclude;
	}

	/**
	 * Enables discount filtering.
	 */
	public static function enable_member_discount_inheritance() {
		self::$inherit_member_discount = true;
	}

	/**
	 * Disables discount filtering.
	 */
	public static function disable_member_discount_inheritance() {
		self::$inherit_member_discount = false;
	}
}

WC_PB_Members_Compatibility::init();
