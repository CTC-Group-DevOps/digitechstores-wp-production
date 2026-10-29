<?php

class WCML_Setup_Attributes_UI extends WCML_Setup_Step {

	const SLUG = 'attributes';

	private $woocommerce_wpml;

	public function __construct( $woocommerce_wpml, $next_step_url, $previous_step_url ) {
		parent::__construct( $next_step_url, $previous_step_url );

		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function get_model() {

		$wc_attributes            = wc_get_attribute_taxonomies();
		$wc_attributes_translated = $this->woocommerce_wpml->attributes->get_translatable_attributes();
		$attribute_names          = [];
		foreach ( $wc_attributes_translated as $attribute ) {
			$attribute_names[] = $attribute->attribute_name;
		}

		$attributes = [];
		foreach ( $wc_attributes as $attribute ) {
			$attributes[] = [
				'name'       => $attribute->attribute_name,
				'label'      => $attribute->attribute_label,
				'translated' => in_array( $attribute->attribute_name, $attribute_names, true ),
			];
		}

		return [
			'strings'      => [
				'step_id'       => 'attributes_step',
				/* translators: Heading of the setup-wizard step where the shop owner picks the attributes to translate. */
				'heading'       => __( 'Which product attributes should be translatable?', 'woocommerce-multilingual' ),
				'description_1' => sprintf(
					/* translators: %1$s and %2$s are opening and closing HTML link tags */
					esc_html__( 'WPML allows you to %1$stranslate your product attributes%2$s. Some attributes, like the ones based on numbers and codes, can be universal and might not need to be translated.', 'woocommerce-multilingual' ),
					'<a target="blank" class="wpml-external-link" rel="noopener" href="' . WCML_Tracking_Link::getWcmlMainDoc( '#taxonomies', [ 'utm_term' => WCML_Tracking_Link::UTM_TERM_WIZARD ] ) . '">',
					'</a>'
				),
				/* translators: Line above the attribute list in the setup wizard; the list follows the colon. */
				'description_2' => __( 'Select which attributes in your store should be translatable:', 'woocommerce-multilingual' ),
				/* translators: Shown in place of the attribute list in the setup wizard when the shop has no product attributes. */
				'no_attributes' => __( 'There are no attributes defined', 'woocommerce-multilingual' ),
				/* translators: Button label that moves the setup wizard on to the next step. Verb, imperative. */
				'continue'      => __( 'Continue', 'woocommerce-multilingual' ),
				/* translators: Button label that returns the setup wizard to the previous step. Verb phrase, imperative. */
				'go_back'       => __( 'Go back', 'woocommerce-multilingual' ),
			],
			'attributes'   => $attributes,
			'continue_url' => $this->next_step_url,
			'go_back_url'  => $this->previous_step_url,
		];

	}

	public function get_template() {
		return '/setup/attributes.twig';
	}
}
