<?php

use WCML\MultiCurrency\Settings as McSettings;
use Automattic\WooCommerce\Internal\ProductAttributesLookup\DataRegenerator;
use Automattic\WooCommerce\Internal\ProductAttributesLookup\LookupDataStore;
use WCML\Attributes\LookupTableFactory;
use WCML\Utilities\AdminNotices;
use WCML\Utilities\DB;
use WCML\Utilities\WCTaxonomies;
use function WCML\functions\getSitePress;
use function WCML\functions\isStandAlone;

class WCML_Upgrade {

	private $versions = [
		'2.9.9.1',
		'3.1',
		'3.2',
		'3.3',
		'3.5',
		'3.5.4',
		'3.6',
		'3.7',
		'3.7.3',
		'3.7.11',
		'3.8',
		'3.9',
		'3.9.1',
		'4.0',
		'4.1.0',
		'4.2.0',
		'4.2.2',
		'4.2.7',
		'4.2.10',
		'4.2.11',
		'4.3.4',
		'4.3.5',
		'4.4.1',
		'4.4.3',
		'4.6.8',
		'4.7.3',
		'4.7.6',
		'4.7.8',
		'4.10.0',
		'4.11.0',
		'4.12.0',
		'5.0.0',
		'5.3.0',
		'5.4.1',
		'5.4.4',
		'5.5.2',
		'5.6.0',
	];

	public function __construct() {
		add_action( 'init', [ $this, 'run' ] );
	}

	public function run() {
		$version_in_db = get_option( '_wcml_version' );

		if ( empty( $version_in_db ) && get_option( 'icl_is_wcml_installed' ) ) {
			$version_in_db = '2.3.2';
		}

		$normalized_version = $this->normalizeVersionInDb( $version_in_db, WCML_VERSION );
		$retired_stamp      = $normalized_version !== $version_in_db;
		$version_in_db      = $normalized_version;

		$migration_ran = false;

		if ( $version_in_db && version_compare( $version_in_db, WCML_VERSION, '<' ) ) {
			foreach ( $this->versions as $version ) {
				if ( version_compare( $version, $version_in_db, '>' ) ) {
					$upgrade_method = 'upgrade_' . str_replace( '.', '_', $version );

					if ( method_exists( $this, $upgrade_method ) ) {
						$this->$upgrade_method();
						$migration_ran = true;
					}
				}
			}
		}

		if ( $migration_ran || $retired_stamp || empty( $version_in_db ) ) {
			update_option( '_wcml_version', WCML_VERSION );
		}

		if ( get_option( '_wcml_4_1_0_migration_required' ) && class_exists( 'woocommerce' ) ) {
			$this->upgrade_4_1_0();
			delete_option( '_wcml_4_1_0_migration_required' );
		}
	}

	private function normalizeVersionInDb( $versionInDb, $currentVersion ) {
		if ( '6.0' === $versionInDb && version_compare( $versionInDb, $currentVersion, '>' ) ) {
			return '5.5.7';
		}

		return $versionInDb;
	}

	public function upgrade_2_9_9_1() {
		global $wpdb;

		$currencies = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'icl_currencies ORDER BY id DESC' );

		foreach ( $currencies as $currency ) {
			if ( isset( $currency->language_code ) ) {
				$wpdb->insert(
					$wpdb->prefix . 'icl_languages_currencies',
					[
						'language_code' => $currency->language_code,
						'currency_id'   => $currency->id,
					]
				);
			}
		}

		$cols = $wpdb->get_col( "SHOW COLUMNS FROM {$wpdb->prefix}icl_currencies" );
		if ( in_array( 'language_code', $cols ) ) {
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}icl_currencies DROP COLUMN language_code" );
		}

		$new_settings = [
			'is_term_order_synced'       => get_option( 'icl_is_wcml_term_order_synched' ),
			'file_path_sync'             => get_option( 'wcml_file_path_sync' ),
			'is_installed'               => get_option( 'icl_is_wpcml_installed' ),
			'dismiss_doc_main'           => get_option( 'wpml_dismiss_doc_main' ),
			'enable_multi_currency'      => get_option( 'icl_enable_multi_currency' ),
			'currency_converting_option' => get_option( 'currency_converting_option' ),
		];

		if ( ! get_option( '_wcml_settings' ) ) {
			add_option( '_wcml_settings', $new_settings, '', true );
		}

		delete_option( 'icl_is_wcml_term_order_synced' );
		delete_option( 'wcml_file_path_sync' );
		delete_option( 'icl_is_wpcml_installed' );
		delete_option( 'wpml_dismiss_doc_main' );
		delete_option( 'icl_enable_multi_currency' );
		delete_option( 'currency_converting_option' );

	}

	public function upgrade_3_1() {
		global $wpdb, $sitepress;
		$wcml_settings = get_option( '_wcml_settings' );

		if ( isset( $wcml_settings['enable_multi_currency'] ) && 'yes' === $wcml_settings['enable_multi_currency'] ) {
			$wcml_settings['enable_multi_currency'] = WCML_MULTI_CURRENCIES_INDEPENDENT;
		} else {
			$wcml_settings['enable_multi_currency'] = WCML_MULTI_CURRENCIES_DISABLED;
		}

		$wcml_settings['products_sync_date'] = 1;

		update_option( '_wcml_settings', $wcml_settings );

		if ( 'yes' === $wcml_settings['enable_multi_currency'] && 2 === (int) $wcml_settings['currency_converting_option'] ) {

			$results = $wpdb->get_results( "SELECT l.language_code, c.code FROM {$wpdb->prefix}icl_languages_currencies l JOIN {$wpdb->prefix}icl_currencies c ON l.currency_id = c.id" );
			foreach ( $results as $row ) {
				 $language_currencies[ $row->language_code ] = $row->code;
			}

			$results = $wpdb->get_results(
				$wpdb->prepare(
					"
                SELECT p.ID, t.trid, t.element_type 
                FROM {$wpdb->posts} p JOIN {$wpdb->prefix}icl_translations t ON t.element_id = p.ID AND t.element_type IN ('post_product', 'post_product_variation')
                WHERE 
                    p.post_type in ('product', 'product_variation') AND t.language_code = %s
                    
            ",
					$sitepress->get_default_language()
				)
			);

			foreach ( $results as $row ) {
				$translations                      = $sitepress->get_element_translations( $row->trid, $row->element_type );
				$meta                              = get_post_meta( $row->ID );
				$original_prices['_price']         = ! empty( $meta['_price'] ) ? $meta['_price'][0] : 0;
				$original_prices['_regular_price'] = ! empty( $meta['_regular_price'] ) ? $meta['_regular_price'][0] : 0;
				$original_prices['_sale_price']    = ! empty( $meta['_sale_price'] ) ? $meta['_sale_price'][0] : 0;

				$ccr = [];

				foreach ( $translations as $translation ) {
					if ( isset( $language_currencies ) && $translation->element_id != $row->ID ) {
						$meta                                = get_post_meta( $translation->element_id );
						$translated_prices['_price']         = $meta['_price'][0];
						$translated_prices['_regular_price'] = $meta['_regular_price'][0];
						$translated_prices['_sale_price']    = $meta['_sale_price'][0];

						if ( ! empty( $translated_prices['_price'] ) && ! empty( $original_prices['_price'] ) && $translated_prices['_price'] != $original_prices['_price'] ) {
							$ccr['_price'][ $language_currencies[ $translation->language_code ] ] = $translated_prices['_price'] / $original_prices['_price'];
						}

						if ( ! empty( $translated_prices['_regular_price'] ) && ! empty( $original_prices['_regular_price'] ) && $translated_prices['_regular_price'] != $original_prices['_regular_price'] ) {
							$ccr['_regular_price'][ $language_currencies[ $translation->language_code ] ] = $translated_prices['_regular_price'] / $original_prices['_regular_price'];
						}

						if ( ! empty( $translated_prices['_sale_price'] ) && ! empty( $original_prices['_sale_price'] ) && $translated_prices['_sale_price'] != $original_prices['_sale_price'] ) {
							$ccr['_sale_price'][ $language_currencies[ $translation->language_code ] ] = $translated_prices['_sale_price'] / $original_prices['_sale_price'];
						}
					}
				}

				if ( $ccr ) {
					update_post_meta( $row->ID, '_custom_conversion_rate', $ccr );
				}
			}
		}

	}

	public function upgrade_3_2() {
		WCML_Capabilities::set_up_capabilities();

		global $wpdb;
		$currencies    = $wpdb->get_results( 'SELECT id,code FROM ' . $wpdb->prefix . 'icl_currencies ORDER BY `id` DESC' );
		$wc_currencies = get_woocommerce_currencies();
		foreach ( $currencies as $currency ) {
			if ( ! array_key_exists( $currency->code, $wc_currencies ) ) {
				$wpdb->delete( $wpdb->prefix . 'icl_currencies', [ 'ID' => $currency->id ] );
			}
		}
	}

	public function upgrade_3_3() {
		global $wpdb, $woocommerce_wpml;

		WCML_Capabilities::set_up_capabilities();

		$currencies = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'icl_currencies ORDER BY `id` ASC', OBJECT );
		if ( $currencies ) {
			foreach ( $currencies as $currency ) {
				$woocommerce_wpml->settings['currency_options'][ $currency->code ]['rate']      = $currency->value;
				$woocommerce_wpml->settings['currency_options'][ $currency->code ]['updated']   = $currency->changed;
				$woocommerce_wpml->settings['currency_options'][ $currency->code ]['position']  = 'left';
				$woocommerce_wpml->settings['currency_options'][ $currency->code ]['languages'] = $woocommerce_wpml->settings['currencies_languages'];
				unset( $woocommerce_wpml->settings['currencies_languages'] );

				$woocommerce_wpml->update_settings();
			}
		}

		$wpdb->query( "DROP TABLE `{$wpdb->prefix}icl_currencies`" );
	}

	public function upgrade_3_5() {
		$wcml_settings = get_option( '_wcml_settings' );

		$wcml_settings['products_sync_order'] = 1;

		update_option( '_wcml_settings', $wcml_settings );
	}

	public function upgrade_3_5_4() {
		flush_rewrite_rules();
	}

	public function upgrade_3_6() {
		$wcml_settings = get_option( '_wcml_settings' );

		$wcml_settings['display_custom_prices']                = 0;
		$wcml_settings['currency_switcher_product_visibility'] = 1;

		update_option( '_wcml_settings', $wcml_settings );
	}

	public function upgrade_3_7() {
		global $woocommerce_wpml, $wpdb;

		$woocommerce_permalinks = maybe_unserialize( get_option( 'woocommerce_permalinks' ) );

		if ( is_array( $woocommerce_permalinks ) ) {
			foreach ( $woocommerce_permalinks as $base_key => $base ) {

				$base_key = trim( $base_key, '/' );

				if ( $base ) {
					$taxonomy = false;

					switch ( $base_key ) {
						case 'category_base':
							$taxonomy = 'product_cat';
							break;
						case 'tag_base':
							$taxonomy = 'product_tag';
							break;
						case 'attribute_base':
							$taxonomy = 'attribute';
							break;
					}

					if ( $taxonomy ) {
						$wpdb->update(
							$wpdb->prefix . 'icl_strings',
							[
								'context' => 'WordPress',
								'name'    => sprintf( 'URL %s tax slug', $taxonomy ),
							],
							[
								'context' => sprintf( 'URL %s slugs - %s', $taxonomy, $base ),
								'name'    => sprintf( 'Url %s slug: %s', $taxonomy, $base ),
							]
						);

					}
				}
			}
		}

		$endpoint_keys = [
			'order-pay',
			'order-received',
			'view-order',
			'edit-account',
			'edit-address',
			'lost-password',
			'customer-logout',
			'add-payment-method',
		];

		foreach ( $endpoint_keys as $endpoint_key ) {
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}icl_strings
                                  SET context = %s, name = %s
                                  WHERE context = 'WordPress' AND name = %s",
					WCML_Url_Translation::WC_STRING_CONTEXT,
					$endpoint_key,
					'Endpoint slug: ' . $endpoint_key
				)
			);

			$string_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}icl_strings WHERE context = %s AND name = %s", WCML_Url_Translation::WC_STRING_CONTEXT, $endpoint_key ) );

			if ( $string_id ) {
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE {$wpdb->prefix}icl_strings
                              SET domain_name_context_md5 = %s
                              WHERE id = %d",
						md5( $endpoint_key, WCML_Url_Translation::WC_STRING_CONTEXT ),
						$string_id
					)
				);
			}
		}

		if ( ! isset( $woocommerce_wpml->terms ) ) {
			global $sitepress;
			$woocommerce_wpml->terms = new WCML_Terms( $woocommerce_wpml, $sitepress, $wpdb );
		}
		$woocommerce_wpml->terms->check_if_sync_terms_needed();

		$wcml_settings = get_option( '_wcml_settings' );

		$wcml_settings['sync_taxonomies_checked'] = 1;

		update_option( '_wcml_settings', $wcml_settings );

		$bookable_resources = $wpdb->get_results( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE element_type = 'post_bookable_resource' AND source_language_code IS NOT NULL" );

		foreach ( $bookable_resources as $bookable_resource ) {
			update_post_meta( $bookable_resource->element_id, 'wcml_is_translated', true );
		}

		$bookable_persons = $wpdb->get_results( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE element_type = 'post_bookable_person' AND source_language_code IS NOT NULL" );

		foreach ( $bookable_persons as $bookable_person ) {
			update_post_meta( $bookable_person->element_id, 'wcml_is_translated', true );
		}
	}

	public function upgrade_3_7_3() {
		global $sitepress;

		$active_languages = $sitepress->get_active_languages();

		foreach ( $active_languages as $lang ) {
			$sitepress->switch_lang( $lang['code'] );

			try {
				$product_cats = get_terms(
					'product_cat',
					[
						'hide_empty' => false,
						'fields'     => 'id=>parent',
					]
				);

				_wc_term_recount( $product_cats, get_taxonomy( 'product_cat' ), true, false );

				$product_tags = get_terms(
					'product_tag',
					[
						'hide_empty' => false,
						'fields'     => 'id=>parent',
					]
				);

				_wc_term_recount( $product_tags, get_taxonomy( 'product_tag' ), true, false );
			} finally {
				$sitepress->switch_lang();
			}
		}
	}

	public function upgrade_3_7_11() {
		$wcml_settings                     = get_option( '_wcml_settings' );
		$wcml_settings['dismiss_doc_main'] = 1;
		update_option( '_wcml_settings', $wcml_settings );
	}

	public function upgrade_3_8() {
		$wcml_settings                      = get_option( '_wcml_settings' );
		$wcml_settings['set_up_wizard_run'] = 1;

		if ( isset( $wcml_settings['attributes_settings'] ) ) {
			$attributes_settings = $wcml_settings['attributes_settings'];
			foreach ( $attributes_settings as $name => $value ) {
				if ( ! WCTaxonomies::isProductAttribute( $name ) ) {
					unset( $wcml_settings['attributes_settings'] [ $name ] );
					$wcml_settings['attributes_settings'] [ WCTaxonomies::TAXONOMY_PREFIX_ATTRIBUTE . $name ] = $value;
				}
			}
		}

		update_option( '_wcml_settings', $wcml_settings );
	}

	public function upgrade_3_9() {
		global $wpdb;

		$meta_keys_to_fix = [
			'_price',
			'_regular_price',
			'_sale_price',
			'_sku',
		];

		$sql = "
            UPDATE {$wpdb->postmeta} 
            SET meta_value = '' 
            WHERE meta_key IN (" . DB::prepareIn( $meta_keys_to_fix ) . ")
                AND meta_value IS NULL";

		$wpdb->query( $sql );
	}

	public function upgrade_3_9_1() {
		global $wpdb, $sitepress;

		$results = $wpdb->get_results(
			"
                        SELECT p.ID, t.trid, t.element_type
                        FROM {$wpdb->posts} p
                        JOIN {$wpdb->prefix}icl_translations t ON t.element_id = p.ID AND t.element_type IN ('post_product', 'post_product_variation')
                        WHERE p.post_type in ('product', 'product_variation') AND t.source_language_code IS NULL
                    "
		);

		foreach ( $results as $product ) {
			if ( get_post_meta( $product->ID, '_manage_stock', true ) === 'yes' ) {
				$translations = $sitepress->get_element_translations( $product->trid, $product->element_type );

				$min_stock = false;

				foreach ( $translations as $translation ) {
					$stock = get_post_meta( $translation->element_id, '_stock', true );
					if ( ! $min_stock || $stock < $min_stock ) {
						$min_stock = $stock;
					}
				}

				foreach ( $translations as $translation ) {
					update_post_meta( $translation->element_id, '_stock', $min_stock );
				}
			}
		}
	}

	public function upgrade_4_0() {
		$wcml_settings                                 = get_option( '_wcml_settings' );
		$wcml_settings['dismiss_tm_warning']           = 0;
		$wcml_settings['cart_sync']['lang_switch']     = WCML_CART_SYNC;
		$wcml_settings['cart_sync']['currency_switch'] = WCML_CART_SYNC;

		update_option( '_wcml_settings', $wcml_settings );

	}

	public function upgrade_4_1_0() {
		global $wpdb;

		if ( ! class_exists( 'WooCommerce' ) ) {
			update_option( '_wcml_4_1_0_migration_required', true );
		} else {
			$results = $wpdb->get_results(
				"
                        SELECT *
                        FROM {$wpdb->postmeta}
                        WHERE meta_key LIKE '\\_price\\_%' OR meta_key LIKE '\\_regular_price\\_%' OR ( meta_key LIKE '\\_sale_price\\_%' AND meta_key NOT LIKE '\\_sale\\_price\\_dates%' )
                    "
			);

			foreach ( $results as $price ) {
				$formatted_price = wc_format_decimal( $price->meta_value );
				update_post_meta( $price->post_id, $price->meta_key, $formatted_price );

				if ( 'product_variation' === get_post_type( $price->post_id ) ) {
					delete_transient( 'wc_var_prices_' . wp_get_post_parent_id( $price->post_id ) );
				}
			}

			$wcml_settings = get_option( '_wcml_settings' );

			if (
				isset( $wcml_settings['currency_switcher_style'] ) &&
				'list' === $wcml_settings['currency_switcher_style']
			) {
				if ( 'horizontal' === $wcml_settings['wcml_curr_sel_orientation'] ) {
					$switcher_style = 'wcml-horizontal-list';
				} else {
					$switcher_style = 'wcml-vertical-list';
				}
			} else {
				$switcher_style = 'wcml-dropdown';
			}

			$wcml_settings['currency_switchers']['product'] = [
				'switcher_style' => $switcher_style,
				'template'       => $wcml_settings['wcml_curr_template'] ?? '',
				'widget_title'   => '',
				'color_scheme'   => [
					'font_current_normal'       => '',
					'font_current_hover'        => '',
					'background_current_normal' => '',
					'background_current_hover'  => '',
					'font_other_normal'         => '',
					'font_other_hover'          => '',
					'background_other_normal'   => '',
					'background_other_hover'    => '',
					'border_normal'             => '',
				],
			];

			$wcml_settings['currency_switcher_additional_css'] = '';
			update_option( '_wcml_settings', $wcml_settings );
		}
	}

	public function upgrade_4_2_0() {
		$wcml_settings                         = get_option( '_wcml_settings' );
		$wcml_settings['dismiss_cart_warning'] = 0;

		update_option( '_wcml_settings', $wcml_settings );
	}

	private function upgrade_4_2_2() {
		$user = new WP_User( 'admin' );
		if ( $user->exists() && ! is_super_admin( $user->ID ) ) {
			$user->remove_cap( 'wpml_manage_woocommerce_multilingual' );
			if ( ! in_array( 'shop_manager', $user->roles, true ) ) {
				$user->remove_cap( 'wpml_operate_woocommerce_multilingual' );
			}
		}
	}

	private function upgrade_4_2_7() {
		$wcml_settings = get_option( '_wcml_settings' );
		if ( 'yahoo' === $wcml_settings['multi_currency']['exchange_rates']['service'] ) {
			$wcml_settings['multi_currency']['exchange_rates']['service'] = 'fixerio';
			update_option( '_wcml_settings', $wcml_settings );
		}
	}

	private function upgrade_4_2_10() {
		global $wpdb;
		if ( defined( 'WC_BOOKINGS_VERSION' ) && version_compare( WC_BOOKINGS_VERSION, '1.10.9', '>=' ) ) {
			$results = $wpdb->get_results(
				"
                        SELECT *
                        FROM {$wpdb->postmeta}
                        WHERE meta_key LIKE '\\_wc_booking_base_cost\\_%' )
                    "
			);

			foreach ( $results as $price ) {
				$base_cost_price      = $price->meta_value;
				$block_cost_field_key = str_replace( 'base', 'block', $price->meta_key );
				update_post_meta( $price->post_id, $block_cost_field_key, $base_cost_price );
			}
		}
	}

	private function upgrade_4_2_11() {
		global $wpdb;

		$wpdb->query(
			"UPDATE {$wpdb->prefix}woocommerce_order_itemmeta
                                  SET meta_key = '_wcml_converted_subtotal'
                                  WHERE meta_key = 'wcml_converted_subtotal'"
		);

		$wpdb->query(
			"UPDATE {$wpdb->prefix}woocommerce_order_itemmeta
                                  SET meta_key = '_wcml_converted_total'
                                  WHERE meta_key = 'wcml_converted_total'"
		);

		WCML_Install::insert_default_categories();
	}

	private function upgrade_4_3_4() {
		global $wpdb;

		$wpdb->query( "DELETE FROM {$wpdb->prefix}icl_translations WHERE `element_id` IN ( SELECT ID FROM {$wpdb->prefix}posts WHERE `guid` LIKE '%attachment_id%' ) " );
		$wpdb->query( "DELETE FROM {$wpdb->prefix}postmeta WHERE `post_id` IN ( SELECT ID FROM {$wpdb->prefix}posts WHERE `guid` LIKE '%attachment_id%' ) " );
		$wpdb->query( "DELETE FROM {$wpdb->prefix}posts WHERE `guid` LIKE '%attachment_id%'" );
	}

	private function upgrade_4_3_5() {
		if ( class_exists( 'WC_Product_Bundle' ) && function_exists( 'WC_PB' ) ) {
			global $wpdb;
			$wpdb->query( "DELETE FROM {$wpdb->prefix}woocommerce_bundled_itemmeta WHERE `meta_key` LIKE 'translation_item_id_of_%' AND `meta_value` IN ( SELECT bundled_item_id FROM {$wpdb->prefix}woocommerce_bundled_items WHERE `product_id` = 0 AND `bundle_id` = 0 ) " );
			$wpdb->query( "DELETE FROM {$wpdb->prefix}woocommerce_bundled_items WHERE `product_id` = 0 AND `bundle_id` = 0 " );
			$not_existing_items = $wpdb->get_col( "SELECT m.`meta_id` FROM {$wpdb->prefix}woocommerce_bundled_itemmeta AS m LEFT JOIN {$wpdb->prefix}woocommerce_bundled_items as i ON m.meta_value = i.bundled_item_id WHERE m.`meta_key` LIKE 'translation_item_id_of_%' AND i.`bundled_item_id` IS NULL" );
			$wpdb->query( "DELETE FROM {$wpdb->prefix}woocommerce_bundled_itemmeta WHERE `meta_id` IN (" . DB::prepareIn( $not_existing_items ) . ')' );
		}
	}

	private function upgrade_4_4_1() {
		global $sitepress, $woocommerce_wpml;

		if ( ! $woocommerce_wpml->is_wpml_prior_4_2() ) {
			$wcml_settings = get_option( '_wcml_settings' );
			$tm_settings   = $sitepress->get_setting( 'translation-management', [] );

			if ( $wcml_settings['trnsl_interface'] ) {
				$tm_settings[ WPML_TM_Post_Edit_TM_Editor_Mode::TM_KEY_FOR_POST_TYPE_USE_NATIVE ]['product'] = false;
			} else {
				$tm_settings[ WPML_TM_Post_Edit_TM_Editor_Mode::TM_KEY_FOR_POST_TYPE_USE_NATIVE ]['product'] = true;
			}

			$sitepress->set_setting( 'translation-management', $tm_settings, true );
		}
	}


	private function upgrade_4_4_3() {
		if ( class_exists( 'WC_SwatchesPlugin' ) ) {
			global $wpdb, $sitepress, $wpml_post_translations, $woocommerce_wpml;

			$variation_sp      = new WCML_Variation_Swatches_And_Photos( $woocommerce_wpml );
			$original_products = $wpdb->get_results(
				"
                        SELECT element_id
                        FROM {$wpdb->prefix}icl_translations
                        WHERE element_type = 'post_product' AND source_language_code IS NULL"
			);

			foreach ( $original_products as $product ) {
				$sitepress->switch_lang( $wpml_post_translations->get_element_lang_code( $product->element_id ) );

				try {
					$translations = $wpml_post_translations->get_element_translations( $product->element_id, false, true );
					foreach ( $translations as $translation ) {
						$variation_sp->sync_variation_swatches_and_photos( $product->element_id, $translation, false );
					}
				} finally {
					$sitepress->switch_lang();
				}
			}
		}
	}

	private function upgrade_4_7_0() {
		global $wpdb;

		$wpdb->query(
			"UPDATE {$wpdb->prefix}icl_strings
                      SET `context` = 'admin_texts_woocommerce_gateways',
                          `domain_name_context_md5` = MD5( CONCAT( 'admin_texts_woocommerce_gateways', `name`, `gettext_context` ) )
                      WHERE `context` = 'woocommerce' AND `name` LIKE '%_gateway_title'"
		);
		$wpdb->query(
			"UPDATE {$wpdb->prefix}icl_strings
                      SET `context` = 'admin_texts_woocommerce_gateways',
                          `domain_name_context_md5` = MD5( CONCAT( 'admin_texts_woocommerce_gateways', `name`, `gettext_context` ) )
                      WHERE `context` = 'woocommerce' AND `name` LIKE '%_gateway_description'"
		);
		$wpdb->query(
			"UPDATE {$wpdb->prefix}icl_strings
                      SET `context` = 'admin_texts_woocommerce_gateways',
                          `domain_name_context_md5` = MD5( CONCAT( 'admin_texts_woocommerce_gateways', `name`, `gettext_context` ) )
                      WHERE `context` = 'woocommerce' AND `name` LIKE '%_gateway_instructions'"
		);
	}

	private function upgrade_4_7_3() {
		delete_option( 'wcml_currency_switcher_template_objects' );
	}

	private function upgrade_4_7_6() {
		global $wpdb;

		$wpdb->query(
			"UPDATE {$wpdb->prefix}icl_strings
                      SET `context` = 'admin_texts_woocommerce_shipping',
                          `domain_name_context_md5` = MD5( CONCAT( 'admin_texts_woocommerce_shipping', `name`, `gettext_context` ) )
                      WHERE `context` = 'woocommerce' AND `name` LIKE '%_shipping_method_title'"
		);
	}

	private function upgrade_4_7_8() {
		$emailOptions = [
			'woocommerce_new_order_settings',
			'woocommerce_cancelled_order_settings',
			'woocommerce_failed_order_settings',
			'woocommerce_customer_on_hold_order_settings',
			'woocommerce_customer_processing_order_settings',
			'woocommerce_customer_completed_order_settings',
			'woocommerce_customer_refunded_order_settings',
			'woocommerce_customer_invoice_settings',
			'woocommerce_customer_note_settings',
			'woocommerce_customer_reset_password_settings',
			'woocommerce_customer_new_account_settings'
		];


		foreach ( $emailOptions as $emailOption ) {

			$emailSettings = get_option( $emailOption );

			if ( isset( $emailSettings['additional_content'] ) && $emailSettings['additional_content'] ) {
				$domain = 'admin_texts_' . $emailOption;
				$name   = '[' . $emailOption . ']additional_content';

				icl_register_string( $domain, $name, $emailSettings['additional_content'], false, '' );
			}
		}
	}

	private function upgrade_4_10_0() {
		$wcml_settings = get_option( '_wcml_settings' );
		if ( WCML_MULTI_CURRENCIES_INDEPENDENT === $wcml_settings['enable_multi_currency'] ) {
			McSettings::setMode( McSettings::MODE_BY_LANGUAGE );
		}
	}

	private function upgrade_4_11_0() {
		global $sitepress;

		WCML_Install::set_language_to_existing_orders( $sitepress->get_default_language() );
	}

	private function upgrade_4_12_0() {
		wp_schedule_single_event( time() + 10, 'generate_category_lookup_table' );
	}

	private function upgrade_5_0_0() {
		delete_transient( WCML_Payment_Gateway_PayPal_V2::BEARER_TOKEN_TRANSIENT );
	}

	private function upgrade_5_3_0() {
		if (
			LookupTableFactory::hasFeature() &&
			! wc_get_container()->get( LookupDataStore::class )->regeneration_is_in_progress()
		) {
			wc_get_container()->get( DataRegenerator::class )->initiate_regeneration();
		}

		AdminNotices::get()->remove_notice( 'wcml-admin-notices', 'hpos-sync-disabled' );
	}

	private function upgrade_5_4_1() {
		$this->fixture_5_4_1_all_products_screen_set_languages_column_visible();
	}

	private function fixture_5_4_1_all_products_screen_set_languages_column_visible() {
		if ( isStandAlone() ) {
			return;
		}

		$hasTooManyLanguages = count( (array) getSitePress()->get_active_languages() ) > 4;

		if ( $hasTooManyLanguages ) {
			return;
		}

		$args = [
			'capability__in' => [ 'edit_products' ],
		];

		$users = get_users( $args );

		foreach ( $users as $user ) {
			$hiddenColumnsList = get_user_meta( $user->ID, \WCML\API\VendorAddon\Hooks::COLUMN_USER_OPTION, true );

			if ( is_array( $hiddenColumnsList ) ) {
				$columnKeyToRemove = array_search( 'icl_translations', $hiddenColumnsList, true );

				if ( false !== $columnKeyToRemove ) {
					unset( $hiddenColumnsList[ $columnKeyToRemove ] );

					update_user_meta( $user->ID, \WCML\API\VendorAddon\Hooks::COLUMN_USER_OPTION, $hiddenColumnsList );
				}
			}
		}
	}

	private function upgrade_5_4_4() {
		\WCML_Capabilities::set_up_capabilities();
	}

	private function upgrade_5_5_2() {
		delete_option( 'wcml_currency_switcher_template_objects' );
	}

	private function upgrade_5_6_0() {
		AdminNotices::get()->remove_notice( 'wcml-admin-notices', 'wcml-translation-control-moved' );

		$this->fixture_5_6_0_delete_language_pack_options();
		$this->fixture_5_6_0_clear_editor_on_store_page_jobs();
		$this->fixture_5_6_0_repair_attribute_general_name_source_language();
	}

	private function fixture_5_6_0_delete_language_pack_options(): void {
		delete_option( 'wcml_translations_upgrade_notice' );
		delete_option( 'hide_wcml_translations_message' );

		$sitepress = getSitePress();

		foreach ( $sitepress->get_active_languages() as $language ) {
			$locale = method_exists( $sitepress, 'get_locale' ) ? $sitepress->get_locale( $language['code'] ) : $language['default_locale'];
			delete_option( 'woocommerce_language_pack_version_' . $locale );
		}
	}

	private function fixture_5_6_0_repair_attribute_general_name_source_language() {
		global $wpdb;

		if ( isStandAlone() || ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
			return;
		}

		$defaultLanguage = getSitePress()->get_default_language();
		$pattern         = $this->getGeneralNamePattern( $defaultLanguage );

		if ( null === $pattern || WCML_WC_Strings::TAXONOMY_GENERAL_NAME_MSGID === $pattern ) {
			return;
		}

		foreach ( wc_get_attribute_taxonomies() as $taxonomy ) {
			$name = WCML_WC_Strings::TAXONOMY_GENERAL_NAME_PREFIX . sprintf( $pattern, $taxonomy->attribute_label );

			$string = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT id, language FROM {$wpdb->prefix}icl_strings WHERE context = %s AND name = %s",
					WCML_WC_Strings::DOMAIN_WORDPRESS,
					$name
				)
			);

			if ( ! $string || $defaultLanguage === $string->language ) {
				continue;
			}

			$hasTranslationInDefaultLanguage = (bool) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$wpdb->prefix}icl_string_translations WHERE string_id = %d AND language = %s",
					$string->id,
					$defaultLanguage
				)
			);

			if ( $hasTranslationInDefaultLanguage ) {
				continue;
			}

			$wpdb->update( $wpdb->prefix . 'icl_strings', [ 'language' => $defaultLanguage ], [ 'id' => $string->id ] );
		}
	}

	private function getGeneralNamePattern( $language ) {
		global $woocommerce_wpml;

		$strings = $woocommerce_wpml->strings ?? null;

		return $strings instanceof WCML_WC_Strings ? $strings->get_general_name_pattern( $language ) : null;
	}

	private function fixture_5_6_0_clear_editor_on_store_page_jobs() {
		global $wpdb;

		if ( isStandAlone() ) {
			return;
		}

		$storePageIds = $this->getStorePageIds( $wpdb );

		if ( ! $storePageIds ) {
			return;
		}

		$jobIds = $wpdb->get_col(
			$wpdb->prepare(
				"
				SELECT job.job_id
				FROM {$wpdb->prefix}icl_translate_job job
				INNER JOIN {$wpdb->prefix}icl_translation_status status
					ON status.rid = job.rid
				INNER JOIN {$wpdb->prefix}icl_translations translation
					ON translation.translation_id = status.translation_id
				INNER JOIN {$wpdb->prefix}icl_translations original
					ON original.trid = translation.trid
					AND original.source_language_code IS NULL
				WHERE original.element_id IN ( " . implode( ', ', array_fill( 0, count( $storePageIds ), '%d' ) ) . " )
					AND original.element_type = 'post_page'
					AND translation.source_language_code IS NOT NULL
					AND job.editor = %s
					AND job.translated = 1
					AND status.status = %d
					AND status.needs_update = 0
					AND status.review_status IS NULL
					AND ( SELECT COUNT(*) FROM {$wpdb->prefix}icl_translate_job WHERE rid = job.rid ) = 1
					AND NOT EXISTS (
						SELECT 1
						FROM {$wpdb->postmeta} meta
						WHERE meta.post_id = translation.element_id
							AND meta.meta_key = %s
					)
				",
				array_merge(
					$storePageIds,
					[
						WPML_TM_Editors::WP,
						ICL_TM_COMPLETE,
						'_last_translation_edit_mode',
					]
				)
			)
		);

		foreach ( $jobIds as $jobId ) {
			wpml_tm_load_old_jobs_editor()->set( (int) $jobId, WPML_TM_Editors::NONE );
		}
	}

	private function getStorePageIds( wpdb $wpdb ): array {
		$optionNames = WCML_Store_Pages::getWcPagesOptionNames();

		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_value FROM {$wpdb->options} WHERE option_name IN ( " . implode( ', ', array_fill( 0, count( $optionNames ), '%s' ) ) . ' )',
				$optionNames
			)
		);

		return array_values( array_filter( array_map( 'intval', $ids ) ) );
	}
}
