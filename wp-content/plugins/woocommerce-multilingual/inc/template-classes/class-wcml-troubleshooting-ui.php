<?php

use WCML\Reviews\Translations\FrontEndHooks;
use WCML\Utilities\AdminUrl;

class WCML_Troubleshooting_UI extends WCML_Templates_Factory {

	const MODE_ALL      = 'all';
	const MODE_SAFE     = 'safe';
	const MODE_CONFIG   = 'config';
	const MODE_ADVANCED = 'advanced';

	private $woocommerce_wpml;

	private $mode;

	public function __construct( $woocommerce_wpml, $mode = self::MODE_ALL ) {
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->mode             = in_array( $mode, [ self::MODE_ALL, self::MODE_SAFE, self::MODE_CONFIG, self::MODE_ADVANCED ], true )
			? $mode
			: self::MODE_ALL;
	}


	public function get_model() {

		$translatedProductTypeTerms      = WCML_Install::translated_product_type_terms();
		$wcmlSettings                    = print_r( get_option( '_wcml_settings', [] ), true );
		$wpmlElementSyncSettingsFaxctory = new WPML_Element_Sync_Settings_Factory();

		$model = [
			'header' => [
				/* translators: Tab title and heading of the screen with the plugin's repair tools. */
				'title' => __( 'Troubleshooting', 'woocommerce-multilingual' ),
				/* translators: Warning at the top of the Troubleshooting screen, above the repair tools. */
				'warning' => __( 'Please make a backup of your database before using these tools', 'woocommerce-multilingual' ),
			],
			'status' => [
				/* translators: Tab title and heading of the screen with the plugin's diagnostic checks. Noun. */
				'title' => __( 'Status', 'woocommerce-multilingual' ),
				'items' => [
					/* translators: Diagnostic line on the Troubleshooting screen. %s: how many products the shop has, arriving in bold. The brackets around "s" cover both one and several. */
					'total'      => sprintf( __( 'There are <strong>%s product(s)</strong> in total.', 'woocommerce-multilingual' ), $this->woocommerce_wpml->troubleshooting->countProducts() ),
					/* translators: Diagnostic line on the Troubleshooting screen. %s: how many of the shop's products have variations, arriving in bold. The brackets around "s" cover both one and several. */
					'variable'   => sprintf( __( 'There are <strong>%s product(s)</strong> with variations.', 'woocommerce-multilingual' ), $this->woocommerce_wpml->troubleshooting->countVariableProducts() ),
					/* translators: Diagnostic line on the Troubleshooting screen. %s: how many product variations the shop has in total, arriving in bold. The brackets around "s" cover both one and several. */
					'variations' => sprintf( __( 'There are <strong>%s variation(s)</strong> in total.', 'woocommerce-multilingual' ), $this->woocommerce_wpml->troubleshooting->countVariations() ),
				],
			],
			'synchronizeData' => [
				/* translators: Heading of the group of repair tools that copy data from the original products to their translations. */
				'title'         => __( 'Synchronize data into translations', 'woocommerce-multilingual' ),
				'items'         => [
					/* translators: Label of the repair tool that recreates missing variations on translated products. Verb phrase, imperative. */
					'variations'         => __( 'Synchronize product variations', 'woocommerce-multilingual' ),
					/* translators: Label of the repair tool that copies product image galleries to the translations. Verb phrase, imperative. */
					'gallery'            => __( 'Synchronize products image galleries', 'woocommerce-multilingual' ),
					/* translators: Label of the repair tool that copies a product category's layout and image to its translations. Verb phrase, imperative. */
					'categoriesMetadata' => __( 'Synchronize metadata for product categories (display type, thumbnail)', 'woocommerce-multilingual' ),
					/* translators: Label of the repair tool that copies stock levels to the translations. Verb phrase, imperative. */
					'stock'              => __( 'Synchronize stock for products and product variations', 'woocommerce-multilingual' ),
				],
				'galleryEnable' => $wpmlElementSyncSettingsFaxctory->create( 'post' )->is_sync( 'attachment' ),
			],
			'connect' => [
				/* translators: Heading of the group of repair tools that mend the links between an original and its translations. Verb phrase, imperative. */
				'title' => __( 'Fix multilingual information', 'woocommerce-multilingual' ),
				'items' => [
					/* translators: Label of the repair tool that reconnects translated variations to their originals. Verb phrase, imperative. */
					'variations' => __( 'Fix incorrect or missing translation links for product variations', 'woocommerce-multilingual' ),
				],
			],
			'taxonomies' => [
				/* translators: Heading of the group of repair tools for product categories, tags and attributes. Verb phrase, imperative. */
				'title'             => __( 'Fix product taxonomies', 'woocommerce-multilingual' ),
				'items'             => [
					/* translators: Label of the repair tool that stops product types being translated and removes the translations already made. Verb phrase, imperative. */
					'productType' => __( 'Set product types as not translatable and delete unwanted translations', 'woocommerce-multilingual' ),
					/* translators: Label of the repair tool that creates the missing attribute translations; a dropdown of attributes follows the colon. Verb phrase, imperative. */
					'attributes'  => __( 'Create missing translations for product attributes:', 'woocommerce-multilingual' ),
				],
				'productTypeEnable' => ! empty( $translatedProductTypeTerms ),
				'link'              => sprintf(
				/* translators: %1$s and %2$s are opening and closing HTML link tags */
					esc_html__( 'To translate product taxonomies, go to the %1$sTaxonomy Translation%2$s page.', 'woocommerce-multilingual' ),
					'<a href="' . esc_url( AdminUrl::getWPMLTaxonomyTranslation() ) . '">',
					'</a>'
				),
				'data' => [
					'attributes' => $this->get_all_products_taxonomies(),
					/* translators: First entry of the attribute dropdown of a repair tool: no attribute is picked. Lower case, as it sits in a list of attribute names. */
					'none'       => __( 'none', 'woocommerce-multilingual' ),
				]
			],
			'reviews' => [
				/* translators: Heading of the group of repair tools for reviews. Verb phrase, imperative. */
				'title'         => __( 'Fix missing product reviews', 'woocommerce-multilingual' ),
				'items'         => [
					/* translators: Label of the repair tool that makes reviews WPML has not registered available for translation. */
					'register' => __( 'Allow to translate missing product reviews', 'woocommerce-multilingual' ),
				],
				'reviewsEnable' => apply_filters( FrontEndHooks::HOOK_ENABLE, true ),
				'link'          => sprintf(
				  /* translators: %1$s and %2$s are opening and closing HTML link tags */
					esc_html__( 'To translate product reviews, go to the %1$sTranslation Dashboard%2$s.', 'woocommerce-multilingual' ),
					'<a href="' . esc_url( AdminUrl::getWPMLTMDashboardStringDomain( FrontEndHooks::CONTEXT ) ) . '">',
					'</a>'
				),
			],
			'cleanup' => [
				/* translators: Heading of the group of repair tools that remove leftover data. */
				'title' => __( 'Cleanup tools', 'woocommerce-multilingual' ),
				'items' => [
					/* translators: Label of the repair tool that deletes leftover custom fields from translations. Verb phrase, imperative. */
					'orphanedMeta' => __( 'Remove unused custom fields from product and variation translations', 'woocommerce-multilingual' ),
				],
			],
			'run' => [
				/* translators: Button label that starts the ticked repair tools. Verb phrase, imperative. */
				'start'   => __( 'Run the selected tools', 'woocommerce-multilingual' ),
				/* translators: Label the run button changes to while the repair tools are working. A state, not a command. */
				'running' => __( 'Processing', 'woocommerce-multilingual' ),
			],
			'settings' => [
				/* translators: Heading above the read-only list of the plugin's settings on the Troubleshooting screen. */
				'title' => __( 'WPML Multilingual & Multicurrency for WooCommerce settings', 'woocommerce-multilingual' ),
				'data'  => esc_html( $wcmlSettings ),
			],
			'counter' => sprintf(
				/* translators: Progress line shown while the repair tools run. %s: how many items have been handled so far; the number counts up as the work goes on. */
				__( '%s items processed', 'woocommerce-multilingual' ),
				'<span class="count"></span>'
			),
			/* translators: Progress line shown while a repair tool is working. A state, not a command; keep the three dots. */
			'doing'  => __( 'Processing...', 'woocommerce-multilingual' ),
			/* translators: Progress line shown when a repair tool has finished. A state, not a command. */
			'done'   => __( 'Completed', 'woocommerce-multilingual' ),
			/* translators: Error shown when a repair tool failed. */
			'error'  => __( 'Something went wrong, please reload the page and try again', 'woocommerce-multilingual' ),
			'nonces' => [
				'trbl_sync_variations'    => wp_nonce_field( 'trbl_sync_variations', 'trbl_sync_variations_nonce', true, false ),
				'trbl_gallery_images'     => wp_nonce_field( 'trbl_gallery_images', 'trbl_gallery_images_nonce', true, false ),
				'trbl_sync_categories'    => wp_nonce_field( 'trbl_sync_categories', 'trbl_sync_categories_nonce', true, false ),
				'trbl_duplicate_terms'    => wp_nonce_field( 'trbl_duplicate_terms', 'trbl_duplicate_terms_nonce', true, false ),
				'trbl_product_type_terms' => wp_nonce_field( 'trbl_product_type_terms', 'trbl_product_type_terms_nonce', true, false ),
				'trbl_sync_stock'         => wp_nonce_field( 'trbl_sync_stock', 'trbl_sync_stock_nonce', true, false ),
				'fix_relationships'       => wp_nonce_field( 'fix_relationships', 'fix_relationships_nonce', true, false ),
				'sync_deleted_meta'       => wp_nonce_field( 'sync_deleted_meta', 'sync_deleted_meta_nonce', true, false ),
				'register_reviews_in_st'  => wp_nonce_field( 'register_reviews_in_st', 'register_reviews_in_st_nonce', true, false ),
			],
			'visible' => $this->visible_sections(),
		];

		return $model;
	}


	private function visible_sections() {
		$sections = [
			'header',
			'status',
			'synchronizeData',
			'connect',
			'taxonomies',
			'reviews',
			'cleanup',
			'run',
			'settings',
		];

		$visible_per_mode = [
			self::MODE_ALL      => $sections,
			self::MODE_SAFE     => [ 'header', 'synchronizeData', 'taxonomies', 'reviews', 'run' ],
			self::MODE_CONFIG   => [ 'settings' ],
			self::MODE_ADVANCED => [ 'header', 'connect', 'cleanup', 'run' ],
		];

		$visible = $visible_per_mode[ $this->mode ];

		return array_combine( $sections, array_map(
			function( $section ) use ( $visible ) {
				return in_array( $section, $visible, true );
			},
			$sections
		) );
	}

	public function get_all_products_taxonomies() {

		$all_products_taxonomies = get_taxonomies( [ 'object_type' => [ 'product' ] ], 'objects' );
		unset(
			$all_products_taxonomies['product_type'],
			$all_products_taxonomies['product_cat'],
			$all_products_taxonomies['product_tag']
		);

		foreach ( $all_products_taxonomies as $key => $taxonomy ) {
			if ( is_taxonomy_translated( $key ) ) {
				$all_products_taxonomies[ $key ]->tax_key     = $key;
			} else {
				unset( $all_products_taxonomies[ $key ] );
			}
		}

		return $all_products_taxonomies;

	}

	public function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/',
		];
	}

	public function get_template() {
		return 'troubleshooting.twig';
	}
}
