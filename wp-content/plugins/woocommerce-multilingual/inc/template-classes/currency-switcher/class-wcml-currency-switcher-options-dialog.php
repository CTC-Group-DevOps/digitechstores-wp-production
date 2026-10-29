<?php

class WCML_Currency_Switcher_Options_Dialog extends WCML_Templates_Factory {

	private $woocommerce_wpml;
	private $args;

	public function __construct( $args, $woocommerce_wpml ) {
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->args             = $args;

		add_action( 'wcml_before_currency_switcher_options', [ $this, 'render' ] );
	}

	public function get_model() {

		$model = [

			'args'          => $this->args,
			'color_schemes' => [
				'clear_all' => [
					/* translators: Button in the currency-switcher design dialog that empties every colour field. Verb phrase, imperative. */
					'label' => __( 'Clear all colors', 'woocommerce-multilingual' ),
				],
				'gray'      => [
					/* translators: Name of a ready-made colour scheme for the currency switcher. */
					'label' => __( 'Gray', 'woocommerce-multilingual' ),
				],
				'white'     => [
					/* translators: Name of a ready-made colour scheme for the currency switcher. */
					'label' => __( 'White', 'woocommerce-multilingual' ),
				],
				'blue'      => [
					/* translators: Name of a ready-made colour scheme for the currency switcher. */
					'label' => __( 'Blue', 'woocommerce-multilingual' ),
				],
			],
			'options'       => [
				/* translators: Label of the colour field for the currency switcher's border. Noun. */
				'border'             => __( 'Border', 'woocommerce-multilingual' ),
				/* translators: Label of the colour field for the text of the currency the shopper is using. */
				'font_current'       => __( 'Current currency font color', 'woocommerce-multilingual' ),
				/* translators: Label of the colour field for the text of the currencies the shopper is not using. */
				'font_other'         => __( 'Other currency font color', 'woocommerce-multilingual' ),
				/* translators: Label of the colour field for the background of the currency the shopper is using. */
				'background_current' => __( 'Current currency background color', 'woocommerce-multilingual' ),
				/* translators: Label of the colour field for the background of the currencies the shopper is not using. */
				'background_other'   => __( 'Other currency background color', 'woocommerce-multilingual' ),
			],
			'form'          => [
				'switcher_style'      => [
					/* translators: Label of the setting that picks whether the currency switcher uses the built-in look or a custom one. */
					'label'        => __( 'Currency switcher style', 'woocommerce-multilingual' ),
					/* translators: Option of the currency-switcher style setting: the look that ships with the plugin. */
					'core'         => __( 'Core', 'woocommerce-multilingual' ),
					/* translators: Option of the currency-switcher style setting: a look the shop owner defines. Adjective. */
					'custom'       => __( 'Custom', 'woocommerce-multilingual' ),
					/* translators: Hint under the custom currency-switcher template field. Keep the tag names in English: they are HTML. */
					'allowed_tags' => __( 'Allowed HTML tags: <img> <span> <u> <strong> <em>', 'woocommerce-multilingual' ),
				],
				'template'            => [
					/* translators: Label of the field holding the layout each currency is printed with in the switcher. */
					'label'                  => __( 'Template for currency switcher', 'woocommerce-multilingual' ),
					/* translators: Heading above the list of placeholders that may be used in the currency-switcher template. */
					'parameters'             => __( 'Available parameters', 'woocommerce-multilingual' ),
					/* translators: Example shown under the currency-switcher template field. Only the word "Default" is translated: the rest is the template itself and every per cent sign and word inside one must be left exactly as it is. */
					'template_tip'           => __( 'Default: %name% (%1$symbol%) - %2$code%', 'woocommerce-multilingual' ),
					'parameters_list'        => '%code%, %symbol%, %name%',
					/* translators: Link text that opens the WPML documentation. Verb phrase, imperative. */
					'learn_more'             => __( 'Learn more', 'woocommerce-multilingual' ),
					/* translators: Link that collapses the currency-switcher template help again. Verb phrase, imperative; it is the counterpart of "Learn more". */
					'hide_more'              => __( 'Hide more', 'woocommerce-multilingual' ),
					/* translators: Line introducing the list of placeholders usable in the currency-switcher template. */
					'parameters_description' => __( 'You can customize the currency switcher template using these parameters:', 'woocommerce-multilingual' ),
					'parameter_name'         => sprintf(
						/* translators: %1$s is the bolded placeholder for the currency name, %name% */
						esc_html__( '%1$s: Full name of the currency (e.g., “Euro”).', 'woocommerce-multilingual' ),
						'<strong>%name%</strong>'
					),
					'parameter_symbol'       => sprintf(
						/* translators: %1$s is the bolded placeholder for the currency symbol, %symbol% */
						esc_html__( '%1$s: Standard symbol representing the currency (e.g., “$” or “€”).', 'woocommerce-multilingual' ),
						'<strong>%symbol%</strong>'
					),
					'parameter_code'         => sprintf(
						/* translators: %1$s is the bolded placeholder for the standard currency code, %code% */
						esc_html__( '%1$s: Standard code representing the currency (e.g., USD for United States Dollar).', 'woocommerce-multilingual' ),
						'<strong>%code%</strong>'
					),
					'HTML_tags_available'    => sprintf(
						// translators: %1$s and %2$s are <strong> tags; %3$s and %4$s are opening and closing HTML link tags.
						esc_html__(
							'Additionally, you can use HTML tags %1$s(img, span, em, strong, u)%2$s to customize the output when the switcher style is set to %1$sList of currencies%2$s, %3$sin line with official HTML standards for the select tag%4$s.',
							'woocommerce-multilingual'
						),
						'<strong>',
						'</strong>',
						'<a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/option" target="_blank" rel="noopener noreferrer">',
						'</a>'
					),
				],
				'colors'              => [
					/* translators: Heading of the colour settings of the currency switcher. */
					'label'                => __( 'Currency switcher colors', 'woocommerce-multilingual' ),
					/* translators: Label of the dropdown that picks a ready-made colour scheme for the currency switcher. */
					'theme'                => __( 'Color theme', 'woocommerce-multilingual' ),
					/* translators: Heading of the colour fields used when the shopper's pointer is not over the currency switcher. */
					'normal'               => __( 'Normal', 'woocommerce-multilingual' ),
					/* translators: Heading of the colour fields used while the shopper's pointer is over the currency switcher. */
					'hover'                => __( 'Hover', 'woocommerce-multilingual' ),
					/* translators: First entry of the colour-theme dropdown, shown before a scheme is chosen. Verb phrase, imperative. */
					'select_option_choose' => __( 'Select a preset', 'woocommerce-multilingual' ),
				],
				'widgets'             => [
					/* translators: Label of the dropdown that picks where in the theme the currency switcher is shown. */
					'widget_area'        => __( 'Widget area', 'woocommerce-multilingual' ),
					/* translators: Label of the field holding the heading shown above the currency switcher in the theme. */
					'widget_title'       => __( 'Widget title', 'woocommerce-multilingual' ),
					/* translators: First entry of the widget-area dropdown, shown before an area is picked. Keep the dashes: they mark it as an instruction, not an area name. */
					'choose_label'       => __( '-- Choose a widget area --', 'woocommerce-multilingual' ),
					'available_sidebars' => $this->woocommerce_wpml->multi_currency->currency_switcher->get_available_sidebars(),
				],
				/* translators: Column heading and label above the sample currency switcher, showing how it will look. Noun. */
				'preview'             => __( 'Preview', 'woocommerce-multilingual' ),
				'preview_nonce'       => wp_create_nonce( 'wcml_currencies_switcher_preview' ),
				'save_settings_nonce' => wp_create_nonce( 'wcml_currencies_switcher_save_settings' ),
				/* translators: Button label that closes a dialog and discards the changes. Verb, imperative: the action, not the state "cancelled". */
				'cancel'              => __( 'Cancel', 'woocommerce-multilingual' ),
				/* translators: Button label that stores the settings of a dialog. Verb, imperative. */
				'save'                => __( 'Save', 'woocommerce-multilingual' ),
			],
		];

		return $model;
	}

	public static function currency_switcher_pre_selected_colors() {

		$defaults = [];

		$defaults['clear_all'] = [
			'font_current_normal'       => '',
			'font_current_hover'        => '',
			'background_current_normal' => '',
			'background_current_hover'  => '',
			'font_other_normal'         => '',
			'font_other_hover'          => '',
			'background_other_normal'   => '',
			'background_other_hover'    => '',
			'border_normal'             => '',
		];

		$defaults['gray'] = [
			'font_current_normal'       => '#222222',
			'font_current_hover'        => '#000000',
			'background_current_normal' => '#eeeeee',
			'background_current_hover'  => '#eeeeee',
			'font_other_normal'         => '#222222',
			'font_other_hover'          => '#000000',
			'background_other_normal'   => '#e5e5e5',
			'background_other_hover'    => '#eeeeee',
			'border_normal'             => '#cdcdcd',
		];

		$defaults['white'] = [
			'font_current_normal'       => '#444444',
			'font_current_hover'        => '#000000',
			'background_current_normal' => '#ffffff',
			'background_current_hover'  => '#eeeeee',
			'font_other_normal'         => '#444444',
			'font_other_hover'          => '#000000',
			'background_other_normal'   => '#ffffff',
			'background_other_hover'    => '#eeeeee',
			'border_normal'             => '#cdcdcd',
		];

		$defaults['blue'] = [
			'font_current_normal'       => '#ffffff',
			'font_current_hover'        => '#000000',
			'background_current_normal' => '#95bedd',
			'background_current_hover'  => '#95bedd',
			'font_other_normal'         => '#000000',
			'font_other_hover'          => '#ffffff',
			'background_other_normal'   => '#cbddeb',
			'background_other_hover'    => '#95bedd',
			'border_normal'             => '#0099cc',
		];

		return $defaults;
	}

	public function render() {
		echo $this->get_view();
	}

	protected function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/multi-currency/',
		];
	}

	public function get_template() {
		return 'currency-switcher-options-dialog.twig';
	}
}
