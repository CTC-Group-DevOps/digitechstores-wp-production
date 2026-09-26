<?php
/**
 * WC_PB_Abilities class
 *
 * @package  WooCommerce Product Bundles
 * @since    8.5.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Product Bundles abilities.
 *
 * @class    WC_PB_Abilities
 * @version  8.5.9
 */
class WC_PB_Abilities {

	/**
	 * Setup ability registration hooks.
	 *
	 * @version 8.5.9
	 */
	public static function init() {
		if ( interface_exists( '\\Automattic\\WooCommerce\\Abilities\\AbilityDefinition' ) ) {
			self::load_ability_definition_classes();
			add_filter( 'woocommerce_ability_definition_classes', array( __CLASS__, 'add_ability_definition_classes' ) );
		}
	}

	/**
	 * Adds Product Bundles ability definitions to WooCommerce's ability loader.
	 *
	 * @param array $classes Ability definition class names.
	 * @version 8.5.9
	 * @return array
	 */
	public static function add_ability_definition_classes( array $classes ) {
		$classes[] = WC_PB_Query_Bundles_Ability::class;

		return $classes;
	}

	/**
	 * Loads WooCommerce ability definition classes.
	 *
	 * @return void
	 */
	private static function load_ability_definition_classes() {
		require_once WC_PB_ABSPATH . 'includes/class-wc-pb-bundled-item-formatters.php';
		require_once WC_PB_ABSPATH . 'includes/class-wc-pb-query-bundles-ability.php';
	}
}
