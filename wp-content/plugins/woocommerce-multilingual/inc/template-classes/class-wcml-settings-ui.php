<?php

use WCML\Reviews\AllLanguagesSetting;
use WCML\Reviews\Translations\AutomaticTranslation;
use WPML\Core\Twig_SimpleFunction;

class WCML_Settings_UI extends WCML_Templates_Factory {

	private $woocommerce_wpml;
	private $sitepress;

	public function __construct( woocommerce_wpml $woocommerce_wpml, \WPML\Core\ISitePress $sitepress ) {

		$functions = [
			new Twig_SimpleFunction( 'wp_do_action', [ $this, 'wp_do_action' ] ),
		];

		parent::__construct( $functions );

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
	}

	public function wp_do_action( $hook ) {
		do_action( $hook );
	}

	public function get_model() {

		$model = [
			'form'               => [
				'action'                => admin_url( 'admin-post.php' ),
				'command_action'        => WCML_Requests::SAVE_ACTION,
				'redirect_to'           => isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '',

				'synchronization'       => [
					/* translators: Heading of the section of the plugin's settings that copies product properties to the translations. */
					'heading'    => __( 'Products Synchronization', 'woocommerce-multilingual' ),
					/* translators: Tooltip of the "Products Synchronization" section heading. */
					'tip'        => __( 'Configure specific product properties that should be synced to translations.', 'woocommerce-multilingual' ),
					'sync_date'  => [
						'value' => $this->woocommerce_wpml->settings['products_sync_date'],
						/* translators: Label of the checkbox that copies a product's publishing date to its translations. */
						'label' => __( 'Sync publishing date for translated products.', 'woocommerce-multilingual' ),
					],
					'sync_order' => [
						'value' => $this->woocommerce_wpml->settings['products_sync_order'],
						/* translators: Label of the checkbox that copies the manual sort order of products and their categories to the translations; "order" here is the sequence they are listed in, not a purchase. */
						'label' => __( 'Sync products and product taxonomies order.', 'woocommerce-multilingual' ),
					],
				],

				'media_synchronization' => [
					/* translators: Heading of the section of the plugin's settings that copies product images to the translations. */
					'heading'    => __( 'Products Media Synchronization', 'woocommerce-multilingual' ),
					/* translators: Tooltip of the "Products Media Synchronization" section heading. */
					'tip'        => __( 'Configure thumbnail and gallery synchronization to translations.', 'woocommerce-multilingual' ),
					'sync_media' => [
						'value' => $this->woocommerce_wpml->get_setting( 'sync_media', true ),
						/* translators: Label of the checkbox that falls back to the original product's images when a translation has none. */
						'label' => __( 'Display original images on the translated product front page if images not specified', 'woocommerce-multilingual' ),
					],
				],

				'file_sync'             => [
					/* translators: Heading of the section of the plugin's settings that controls the files a downloadable product's translations offer. */
					'heading'    => __( 'Products Download Files', 'woocommerce-multilingual' ),
					/* translators: Tooltip of the "Products Download Files" section heading. The line break and spacing are only how the text is laid out in the file; translate it as one sentence. */
					'tip'        => __(
						'If you are using downloadable products, you can choose to have their paths
                                            synchronized, or seperate for each language.',
						'woocommerce-multilingual'
					),
					'value'      => $this->woocommerce_wpml->settings[ \WCML_Downloadable_Products::SYNC_MODE_SETTING_KEY ],
					/* translators: Label of the option that gives every translation of a product the same download files. */
					'label_same' => __( 'Use the same files for translations', 'woocommerce-multilingual' ),
					/* translators: Label of the option that gives each translated product its own download files, in the plugin's settings. */
					'label_diff' => __( 'Add separate download files for translations when translating products', 'woocommerce-multilingual' ),
				],

				'product_reviews'       => [
					/* translators: Heading of the section of the plugin's settings about how reviews are shown. */
					'heading'                  => __( 'Product reviews', 'woocommerce-multilingual' ),
					/* translators: Tooltip of the "Product reviews" section heading. */
					'tip'                      => __( 'Define how to display product reviews on the product page by default. The customer will be still able to filter reviews by language.', 'woocommerce-multilingual' ),
					'reviews_in_all_languages' => [
						'value' => AllLanguagesSetting::resolve(
							$this->woocommerce_wpml->get_setting( AllLanguagesSetting::SETTING, null ),
							new AutomaticTranslation()
						),
						/* translators: Label of the checkbox that shows reviews written in every language on a product page. */
						'label' => __( 'By default, show reviews in all languages', 'woocommerce-multilingual' ),
					],
				],

				'cart_sync'             => [
					/* translators: Tooltip of the "Cart" section heading in the plugin's settings. */
					'tip'                        => __( 'You can choose to clear the cart contents when you change language or currency in case you have problems in cart or checkout page', 'woocommerce-multilingual' ),
					/* translators: Name of the WooCommerce cart page, and the heading of the cart section of the plugin's settings. Noun. */
					'heading'                    => __( 'Cart', 'woocommerce-multilingual' ),
					'lang_switch'                => [
						/* translators: Heading of the two options that decide what happens to a full cart when the shopper changes language. */
						'heading'     => __( 'Switching languages when there are items in the cart', 'woocommerce-multilingual' ),
						/* translators: Label of the first option for a full cart on a language switch: keep the cart and swap the products for their translations. */
						'sync_label'  => __( 'Synchronize cart content when switching languages', 'woocommerce-multilingual' ),
						/* translators: Label of the second option for a full cart on a language or currency switch: ask the shopper first, then empty the cart. */
						'clear_label' => __( 'Prompt for a confirmation and reset the cart', 'woocommerce-multilingual' ),
						'value'       => $this->woocommerce_wpml->settings['cart_sync']['lang_switch'],
					],
					'currency_switch'            => [
						/* translators: Heading of the two options that decide what happens to a full cart when the shopper changes currency. */
						'heading'     => __( 'Switching currencies when there are items in the cart', 'woocommerce-multilingual' ),
						/* translators: Label of the first option for a full cart on a currency switch: keep the cart and reprice it. */
						'sync_label'  => __( 'Synchronize cart content when switching currencies', 'woocommerce-multilingual' ),
						/* translators: Label of the second option for a full cart on a language or currency switch: ask the shopper first, then empty the cart. */
						'clear_label' => __( 'Prompt for a confirmation and reset the cart', 'woocommerce-multilingual' ),
						'value'       => $this->woocommerce_wpml->settings['cart_sync']['currency_switch'],
					],
					'wpml_cookie_enabled'        => $this->sitepress->get_setting( WPML_Cookie_Setting::COOKIE_SETTING_FIELD ),
					'cookie_not_enabled_message' => sprintf(
						/* translators: %1$s and %2$s are opening and closing HTML link tags */
						__(
							'This feature was disabled. Please enable %1$sWPML cookies%2$s to continue.',
							'woocommerce-multilingual'
						),
						// A blog license does not load Translation Management.
						'<a href="' . admin_url( 'admin.php?page=' . ( defined( 'WPML_TM_FOLDER' ) ? WPML_TM_FOLDER : 'tm' ) . '/menu/settings&section=compatibility#cookie' ) . '" target="_blank">',
						'</a>'
					),
					'doc_link'                   => sprintf(
						/* translators: %1$s and %2$s are opening and closing HTML link tags */
						__(
							'Not sure which option to choose? Read about %1$spotential issues when switching languages and currencies while the cart has items%2$s.',
							'woocommerce-multilingual'
						),
						'<a href="' . WCML_Tracking_Link::getWcmlClearCartDoc() . '" target="_blank">',
						'</a>'
					),
				],

				'nonce'                 => wp_nonce_field( 'wcml_save_settings_nonce', 'wcml_nonce', true, false ),
				/* translators: Button label that stores the settings of the screen. Verb phrase, imperative. */
				'save_label'            => __( 'Save changes', 'woocommerce-multilingual' ),

			],

			'native_translation' => WCML_TRANSLATION_METHOD_MANUAL,
			'wpml_translation'   => WCML_TRANSLATION_METHOD_EDITOR,

			'wcml_cart_sync'     => WCML_CART_SYNC,
			'wcml_cart_clear'    => WCML_CART_CLEAR,

			'troubleshooting'    => [
				'url'   => \WCML\Utilities\AdminUrl::getTroubleshootingTab(),
				/* translators: Tab title and heading of the screen with the plugin's repair tools. */
				'label' => __( 'Troubleshooting', 'woocommerce-multilingual' ),
			],
		];

		if ( $this->woocommerce_wpml->is_wpml_prior_4_2() ) {
			$model['form']['translation_interface'] = [
				/* translators: Heading of the setting that picks which editor is used to translate products. */
				'heading'                  => __( 'Product Translation Interface', 'woocommerce-multilingual' ),
				/* translators: Tooltip of the "Product Translation Interface" heading; "It" is the WPML Translation Editor. */
				'tip'                      => __(
					'The recommended way is using the WPML Translation Editor. It is streamlined for making the translation process much easier while also providing a much better integration with various WooCommerce extensions.',
					'woocommerce-multilingual'
				),
				'wcml'                     => [
					/* translators: First option of the product-translation setting; it is the name of WPML's own editing screen. */
					'label' => __( 'WPML Translation Editor', 'woocommerce-multilingual' ),

				],
				'native'                   => [
					/* translators: Second option of the product-translation setting: WooCommerce's own product editor is used instead. */
					'label' => __( 'Native WooCommerce product editing screen', 'woocommerce-multilingual' ),

				],
				'controls_value'           => $this->woocommerce_wpml->settings['trnsl_interface'],
				/* translators: Warning in the dialog shown when the shop owner switches away from the WPML Translation Editor. */
				'pb_warning'               => __( "If you are using a page builder to design WooCommerce products, you should only use WPML's Translation Editor.", 'woocommerce-multilingual' ),
				/* translators: Button that confirms the switch away from the WPML Translation Editor, in the page-builder warning dialog. */
				'pb_warning_ok_button'     => __( 'OK (translate with the WordPress editor)', 'woocommerce-multilingual' ),
				/* translators: Button that keeps the WPML Translation Editor, in the page-builder warning dialog. "Cancel" is the verb: undo the switch. */
				'pb_warning_cancel_button' => __( 'Cancel (stay with the Translation Editor)', 'woocommerce-multilingual' ),
			];
		}

		return $model;

	}

	protected function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/',
		];
	}

	public function get_template() {
		return 'settings-ui.twig';
	}


}
