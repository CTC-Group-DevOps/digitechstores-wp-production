<?php

class WCML_Not_Translatable_Attributes extends WCML_Templates_Factory {

	private $attr_id;
	private $woocommerce_wpml;

	public function __construct( $attr_id, $woocommerce_wpml ) {
		parent::__construct();

		$this->attr_id          = $attr_id;
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function get_model() {

		$model = [
			'checked'   => $this->is_translatable(),
			'edit_mode' => (bool) $this->attr_id,
			'strings'   => [
				/* translators: Label of the checkbox that lets a product attribute's values be translated, on the attribute screen. Adjective, asked as a question. */
				'label'       => __( 'Translatable?', 'woocommerce-multilingual' ),
				/* translators: Explanation under the "Translatable?" checkbox; "this" is the checkbox. */
				'description' => __( 'Enable this if you want to translate attribute values with WPML Multilingual & Multicurrency for WooCommerce', 'woocommerce-multilingual' ),
				/* translators: Warning shown when the "Translatable?" checkbox of a product attribute is turned off. */
				'notice'      => __( 'Existing translations and variations associated will be deleted.', 'woocommerce-multilingual' ),
			],
		];

		return $model;
	}

	public function is_translatable() {
		global $wpdb;

		$attribute_to_edit = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'woocommerce_attribute_taxonomies WHERE attribute_id = %d', $this->attr_id ) );
		if ( $attribute_to_edit ) {
			$att_name = wc_attribute_taxonomy_name( $attribute_to_edit->attribute_name );

			$wcml_settings = $this->woocommerce_wpml->get_settings();

			return $wcml_settings['attributes_settings'][ $att_name ] ?? true;
		}

		return true;
	}

	public function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/',
		];
	}

	public function get_template() {
		return 'trnsl-attributes.twig';
	}
}
