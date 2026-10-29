<?php

class WCML_Multilingual_UI extends WCML_Templates_Factory {

	const MODE_LEGACY    = 'legacy';
	const MODE_DIRECTORY = 'directory';

	private $mode;

	public function __construct( $mode = self::MODE_LEGACY ) {
		parent::__construct();

		$this->mode = in_array( $mode, [ self::MODE_LEGACY, self::MODE_DIRECTORY ], true )
			? $mode
			: self::MODE_LEGACY;
	}

	public function get_model() {
		if ( self::MODE_DIRECTORY === $this->mode ) {
			return $this->get_directory_model();
		}

		return $this->get_legacy_model();
	}

	private function get_legacy_model() {
		return [
			'menu'               => [
				'translation' => [
					/* translators: Card title and button label that open WPML's Translation Dashboard filtered to products. Verb phrase, imperative. */
					'title'       => __( 'Translate Products', 'woocommerce-multilingual' ),
					/* translators: Description under the "Translate Products" card; the arrow separates the WPML menu item from the screen inside it. Both are WPML screen names. */
					'description' => esc_html__( 'Go to WPML &rarr; Translation Dashboard to translate Products', 'woocommerce-multilingual' ),
					'icon'        => 'otgs-ico-basket',
					'button'      => [
						'url'   => \WCML\Utilities\AdminUrl::getWPMLTMDashboardProducts(),
						/* translators: Card title and button label that open WPML's Translation Dashboard filtered to products. Verb phrase, imperative. */
						'label' => __( 'Translate Products', 'woocommerce-multilingual' ),
					],
				],
				'taxonomy'    => [
					/* translators: Card title and button label that open WPML's Taxonomy Translation screen. Verb phrase, imperative; "taxonomy" covers product categories, tags and attributes. */
					'title'       => __( 'Translate Taxonomy', 'woocommerce-multilingual' ),
					/* translators: Description under the "Translate Taxonomy" card; the arrow separates the WPML menu item from the screen inside it. Both are WPML screen names. */
					'description' => esc_html__( 'Go to WPML &rarr; Taxonomy Translation to translate Taxonomy terms manually', 'woocommerce-multilingual' ),
					'icon'        => 'otgs-ico-tag',
					'button'      => [
						'url'   => \WCML\Utilities\AdminUrl::getWPMLTaxonomyTranslation(),
						/* translators: Card title and button label that open WPML's Taxonomy Translation screen. Verb phrase, imperative; "taxonomy" covers product categories, tags and attributes. */
						'label' => __( 'Translate Taxonomy', 'woocommerce-multilingual' ),
					],
				],
			],
			'translate_manually' => [
				/* translators: Text of the toggle that opens the manual translation links on the plugin's multilingual screen. */
				'toggle_text'        => __( 'Prefer to translate manually?', 'woocommerce-multilingual' ),
				'products'           => sprintf(
					/* translators: %1$s and %2$s are opening and closing HTML link tags */
					__( 'To manually translate products, go to %1$sProducts%2$s and click on the "plus" icon.', 'woocommerce-multilingual' ),
					'<a href="' . \WCML\Utilities\AdminUrl::getWooProductAll() . '">',
					'</a>'
				),
				'product_categories' => sprintf(
					/* translators: %1$s and %2$s are opening and closing HTML link tags */
					__( 'To manually translate product categories, tags and attributes, go to %1$sWPML &rarr; Taxonomy Translation%2$s.', 'woocommerce-multilingual' ),
					'<a href="' . \WCML\Utilities\AdminUrl::getWPMLTaxonomyTranslation() . '">',
					'</a>'
				),
			],
		];
	}

	private function get_directory_model() {
		return [
			'sections' => [
				[
					/* translators: Heading of the section of cards about translating the shop. Noun, singular. */
					'title' => __( 'Translation', 'woocommerce-multilingual' ),
					'items' => [
						[
							/* translators: Tab title and card title of the screen where the shop's URL parts are translated. */
							'title'       => __( 'Store URLs', 'woocommerce-multilingual' ),
							/* translators: Description under the "Store URLs" card; the arrows separate the WPML menu items, which are screen names. */
							'description' => __( 'Translate the WooCommerce slugs (Shop, Product base, etc.) at WPML → Translations → Store URLs.', 'woocommerce-multilingual' ),
							'url'         => apply_filters( 'wcml_bonded_target_url', '', 'store-url' ),
							/* translators: button label on the legacy WCML directory page */
							'label'       => __( 'Open Store URLs ›', 'woocommerce-multilingual' ),
						],
						[
							/* translators: Card title and button label that open WPML's Translation Dashboard filtered to products. Verb phrase, imperative. */
							'title'       => __( 'Translate Products', 'woocommerce-multilingual' ),
							/* translators: Description under the "Translate Products" card; the arrows separate the WPML menu items, which are screen names. */
							'description' => __( 'Go to WPML → Translations → Dashboard to translate Products.', 'woocommerce-multilingual' ),
							'url'         => \WCML\Utilities\AdminUrl::getWPMLTMDashboardProducts(),
							/* translators: Button label on the "Translate Products" card. Verb phrase, imperative; keep the arrow at the end. */
							'label'       => __( 'Translate Products ›', 'woocommerce-multilingual' ),
						],
						[
							/* translators: Card title on the plugin's multilingual screen. Verb phrase, imperative; "taxonomies" covers product categories, tags and attributes. */
							'title'       => __( 'Translate product taxonomies', 'woocommerce-multilingual' ),
							/* translators: Description under the "Translate product taxonomies" card; the arrows separate the WPML menu items, which are screen names. */
							'description' => __( 'Go to WPML → Translations → Taxonomy to translate product categories, tags and attributes.', 'woocommerce-multilingual' ),
							'url'         => \WCML\Utilities\AdminUrl::getWPMLTaxonomyTranslation(),
							/* translators: Button label on the "Translate product taxonomies" card. Verb phrase, imperative; keep the arrow at the end. */
							'label'       => __( 'Translate Taxonomies ›', 'woocommerce-multilingual' ),
						],
					],
				],
				[
					/* translators: Tab title and link text opening the settings of the plugin or of one currency. Noun, plural. */
					'title' => __( 'Settings', 'woocommerce-multilingual' ),
					'items' => [
						[
							/* translators: Short name of this plugin, used as an admin menu item and as a card title. An abbreviation and stays in English. */
							'title'       => __( 'WCML', 'woocommerce-multilingual' ),
							/* translators: Description under the "WCML" card; the arrows separate the WPML menu items, which are screen names. */
							'description' => __( 'Products, media, downloads, reviews, cart and the variation translation sync — all in WPML → Settings → WCML.', 'woocommerce-multilingual' ),
							'url'         => apply_filters( 'wcml_bonded_target_url', '', 'settings' ),
							/* translators: Button label on the "WCML" card. Verb phrase, imperative; keep the arrow at the end. */
							'label'       => __( 'Open WCML Settings ›', 'woocommerce-multilingual' ),
						],
						[
							/* translators: Tab title and heading of the plugin's screen for currencies. One word, the name of the feature. */
							'title'       => __( 'Multicurrency', 'woocommerce-multilingual' ),
							/* translators: Description under the "Multicurrency" card; the arrows separate the WPML menu items, which are screen names. */
							'description' => __( 'Configure currencies, exchange rates and currency switchers at WPML → Settings → Multicurrency.', 'woocommerce-multilingual' ),
							'url'         => apply_filters( 'wcml_bonded_target_url', '', 'multi-currency' ),
							/* translators: Button label on the "Multicurrency" card. Verb phrase, imperative; keep the arrow at the end. */
							'label'       => __( 'Open Multicurrency ›', 'woocommerce-multilingual' ),
						],
					],
				],
				[
					/* translators: Heading of the section of cards about diagnosing and repairing the shop. Noun. */
					'title' => __( 'Support', 'woocommerce-multilingual' ),
					'items' => [
						[
							/* translators: Card title that opens the plugin's diagnostic screen. "WCML" is the plugin's short name and stays in English. */
							'title'       => __( 'WCML status', 'woocommerce-multilingual' ),
							/* translators: Description under the "WCML status" card. */
							'description' => __( 'Diagnostic checks for store pages, products, taxonomies, multicurrency and plugins.', 'woocommerce-multilingual' ),
							'url'         => apply_filters( 'wcml_bonded_target_url', '', 'status' ),
							/* translators: Button label on the "WCML status" card. Verb phrase, imperative; keep the arrow at the end. */
							'label'       => __( 'Open WCML status ›', 'woocommerce-multilingual' ),
						],
						[
							/* translators: Card title that opens the plugin's safe repair tools. Verb phrase, imperative. */
							'title'       => __( 'Fix WCML translations', 'woocommerce-multilingual' ),
							/* translators: Description under the "Fix WCML translations" card. */
							'description' => __( 'Safe bulk tools to sync product variations, galleries and stock; register missing reviews; create missing attribute translations.', 'woocommerce-multilingual' ),
							'url'         => apply_filters( 'wcml_bonded_target_url', '', 'troubleshooting-safe' ),
							/* translators: Button label on the "Fix WCML translations" card; the words after "Open" are that card's own title. Keep the arrow at the end. */
							'label'       => __( 'Open Fix WCML translations ›', 'woocommerce-multilingual' ),
						],
						[
							/* translators: Card title that opens the read-only list of the plugin's settings. */
							'title'       => __( 'WCML configuration log', 'woocommerce-multilingual' ),
							/* translators: Description under the "WCML configuration log" card. */
							'description' => __( 'Read-only snapshot of the WCML configuration. Copy when WPML Support asks.', 'woocommerce-multilingual' ),
							'url'         => apply_filters( 'wcml_bonded_target_url', '', 'troubleshooting-config' ),
							/* translators: Button label on the "WCML configuration log" card; the words after "Open" are that card's own title. Keep the arrow at the end. */
							'label'       => __( 'Open WCML configuration log ›', 'woocommerce-multilingual' ),
						],
						[
							/* translators: Card title that opens the plugin's advanced repair tools. */
							'title'       => __( 'WCML translation maintenance', 'woocommerce-multilingual' ),
							/* translators: Description under the "WCML translation maintenance" card. */
							'description' => __( 'Advanced repair tools — only run when WPML Support asks you to.', 'woocommerce-multilingual' ),
							'url'         => apply_filters( 'wcml_bonded_target_url', '', 'troubleshooting-advanced' ),
							/* translators: Button label on the "WCML translation maintenance" card; the words after "Open" are that card's own title. Keep the arrow at the end. */
							'label'       => __( 'Open WCML translation maintenance ›', 'woocommerce-multilingual' ),
						],
					],
				],
			],
			/* translators: Introduction at the top of the plugin's multilingual screen, above the cards. */
			'intro' => __( "WPML is installed on this site, so WCML's controls have moved into WPML. Pick the area you need.", 'woocommerce-multilingual' ),
		];
	}

	protected function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/multilingual/',
		];
	}

	public function get_template() {
		return self::MODE_DIRECTORY === $this->mode
			? 'multilingual-directory.twig'
			: 'multilingual.twig';
	}

}
