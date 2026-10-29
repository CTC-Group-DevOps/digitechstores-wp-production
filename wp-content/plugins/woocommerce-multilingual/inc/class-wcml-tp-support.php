<?php

use WCML\TranslationJob\Hooks;
use WCML\Utilities\CustomFieldPreferences;
use function WCML\functions\flushProductCachePrefixById;

class WCML_TP_Support {

	const CUSTOM_FIELD_NAME = 'wc_variation_field:';

	const DOWNLOADABLE_FILES_TRANSLATABLE_FIELDS = [ 'name', 'file' ];

	const PRIORITY_SAVE_VARIATION_CUSTOM_FIELDS_TRANSLATION = 20;

	const PACKAGE_IMAGE_KEY_PREFIX = 'image-id-';

	private $woocommerce_wpml;
	private $wpdb;
	private $tp;

	private $variations_custom_fields_values_cache = [];

	public function __construct( woocommerce_wpml $woocommerce_wpml, wpdb $wpdb, WPML_Element_Translation_Package $tp ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->wpdb             = $wpdb;
		$this->tp               = $tp;
	}

	public function add_hooks() {
		add_filter( 'wpml_tm_translation_job_data', [
			$this,
			'append_custom_attributes_to_translation_package'
		], 10, 2 );
		add_action( 'wpml_translation_job_saved', [ $this, 'save_custom_attribute_translations' ], 10, 3 );

		add_filter( 'wpml_tm_translation_job_data', [
			$this,
			'append_variation_custom_fields_to_translation_package'
		], 10, 2 );

		add_filter( 'wpml_tm_translation_job_data', [
			$this,
			'append_variation_downloadable_fields_to_translation_package'
		], 10, 2 );

		add_filter( 'wpml_tm_translation_job_data', [
			$this,
			'append_simple_downloadable_fields_to_translation_package'
		], 10, 2 );

		add_action( 'wpml_pro_translation_completed', [
			$this,
			'save_variation_custom_fields_translations'
		], self::PRIORITY_SAVE_VARIATION_CUSTOM_FIELDS_TRANSLATION, 3 );

		add_action(
			'wpml_pro_translation_completed',
			[
				$this,
				'flush_variable_product_cache_prefix',
			],
			self::PRIORITY_SAVE_VARIATION_CUSTOM_FIELDS_TRANSLATION + 10,
			3
		);

		add_filter( 'wpml_tm_translation_job_data', [ $this, 'append_images_to_translation_package' ], 10, 2 );
		add_action( 'wpml_translation_job_saved', [ $this, 'save_images_translations' ], 10, 3 );

		add_filter( 'wpml_custom_field_settings_override_lock_render', [ $this, 'set_wpml_term_custom_field_thumbnail_id_as_read_only' ], 10, 2 );
	}

	public function add_content_signature_hooks() {
		add_filter(
			'wpml_custom_field_values_for_post_signature',
			[ $this, 'append_variation_custom_fields_to_post_signature' ],
			10,
			2
		);

		add_filter(
			'wpml_custom_field_values_for_post_signature',
			[ $this, 'append_variation_downloadable_fields_to_post_signature' ],
			10,
			2
		);

		add_action( 'added_post_meta', [ $this, 'flush_variations_custom_fields_values_cache' ], 10, 2 );
		add_action( 'updated_post_meta', [ $this, 'flush_variations_custom_fields_values_cache' ], 10, 2 );
		add_action( 'deleted_post_meta', [ $this, 'flush_variations_custom_fields_values_cache' ], 10, 2 );
	}

	public function flush_variations_custom_fields_values_cache( $meta_id, $object_id ) {
		if ( 'product_variation' === get_post_type( $object_id ) ) {
			unset( $this->variations_custom_fields_values_cache[ wp_get_post_parent_id( $object_id ) ] );
		}
	}

	public function set_wpml_term_custom_field_thumbnail_id_as_read_only( $override, $setting ) {
		if ( 'thumbnail_id' === $setting->get_index() && ( $setting instanceof WPML_Term_Custom_Field_Setting ) ) {
			if( WPML_COPY_CUSTOM_FIELD === $setting->status() ) {
				$setting->make_read_only();
			}
		}

		return $override;
	}

	public function append_custom_attributes_to_translation_package( $package, $post ) {
		if ( $this->isWpPostAWcProduct( $post ) ) {

			$product      = wc_get_product( $post->ID );

			if ( ! empty( $product ) ) {

				$attributes = $product->get_attributes();

				foreach ( $attributes as $attribute_key => $attribute ) {

					if ( $this->woocommerce_wpml->attributes->is_a_taxonomy( $attribute ) ) {
						continue;
					}

					$package['contents'][ 'wc_attribute_name:' . $attribute_key ] = [
						'translate' => 1,
						'data'      => $this->tp->encode_field_data( $attribute['name'] ),
						'format'    => 'base64'
					];
					$values                                                       = explode( '|', $attribute['value'] );
					$values                                                       = array_map( 'trim', $values );

					foreach ( $values as $value_key => $value ) {
						$package['contents'][ 'wc_attribute_value:' . $value_key . ':' . $attribute_key ] = [
							'translate' => 1,
							'data'      => $this->tp->encode_field_data( $value ),
							'format'    => 'base64'
						];
					}
				}
			}
		}

		return $package;
	}

	public function save_custom_attribute_translations( $post_id, $data, $job ) {

		$translated_attributes = [];
		$translated_labels     = $this->woocommerce_wpml->attributes->get_attr_label_translations( $post_id );

		foreach ( $data as $value ) {

			if ( $value['finished'] && isset( $value['field_type'] ) && strpos( $value['field_type'], 'wc_attribute_' ) === 0 ) {

				if ( strpos( $value['field_type'], 'wc_attribute_name:' ) === 0 ) {

					$exp           = explode( ':', $value['field_type'], 2 );
					$attribute_key = $exp[1];

					$translated_attributes[ $attribute_key ]['name'] = $value['data'];

				} else if ( strpos( $value['field_type'], 'wc_attribute_value:' ) === 0 ) {

					$exp           = explode( ':', $value['field_type'], 3 );
					$value_key     = $exp[1];
					$attribute_key = $exp[2];

					$translated_attributes[ $attribute_key ]['values'][ $value_key ] = $value['data'];

				}

			}

		}

		if ( $translated_attributes ) {

			$product_attributes = get_post_meta( $post_id, '_product_attributes', true ) ?: [];

			if( isset( $job->original_doc_id ) ){
				$original_post_id = $job->original_doc_id;
			}else{
				$original_post_id = $this->woocommerce_wpml->products->get_original_product_id( $post_id );
			}

			$original_attributes = get_post_meta( $original_post_id, '_product_attributes', true ) ?: [];

			foreach ( $translated_attributes as $attribute_key => $attribute ) {
				if( isset( $original_attributes[ $attribute_key ] ) ){
					$product_attributes[ $attribute_key ]          = $original_attributes[ $attribute_key ];
					$product_attributes[ $attribute_key ]['name']  = $attribute['name'];
					$product_attributes[ $attribute_key ]['value'] = isset( $attribute['values'] )
						? join( ' | ', $attribute['values'] )
						: $product_attributes[ $attribute_key ]['value'];

					$translated_labels[ $job->language_code ][ $attribute_key ] = $attribute['name'];
				}
			}

			update_post_meta( $post_id, '_product_attributes', $product_attributes );
			update_post_meta( $post_id, 'attr_label_translations', $translated_labels );
		}

	}

	private function get_variation_custom_fields_to_translate( $variation_id ) {
		$meta_keys = (array) get_post_custom_keys( $variation_id );
		$modes     = CustomFieldPreferences::modes( $meta_keys );

		return wpml_collect( $meta_keys )
			->filter(
				function ( $meta_key ) use ( $modes ) {
					return WPML_TRANSLATE_CUSTOM_FIELD === $modes[ $meta_key ];
				}
			)
			->toArray();
	}

	private function get_translatable_product_variations( $product_id ) {
		$product = wc_get_product( $product_id );

		$allowed_variations_types = apply_filters( 'wcml_xliff_allowed_variations_types', [ 'variable' ] );

		if ( ! $product instanceof WC_Product || ! in_array( $product->get_type(), $allowed_variations_types, true ) ) {
			return [];
		}

		return $this->woocommerce_wpml->sync_variations_data->get_product_variations( $product_id );
	}

	private function get_variations_custom_fields_values( $product_id ) {
		if ( isset( $this->variations_custom_fields_values_cache[ $product_id ] ) ) {
			return $this->variations_custom_fields_values_cache[ $product_id ];
		}

		$values     = [];
		$variations = $this->get_translatable_product_variations( $product_id );

		if ( $variations ) {
			$variation_ids = wp_list_pluck( $variations, 'ID' );
			update_meta_cache( 'post', $variation_ids );

			$meta_per_variation = [];
			$meta_keys          = [];
			foreach ( $variation_ids as $variation_id ) {
				$meta                                = (array) wp_cache_get( $variation_id, 'post_meta' );
				$meta_per_variation[ $variation_id ] = $meta;
				$meta_keys                           = array_merge( $meta_keys, array_keys( $meta ) );
			}
			$modes = CustomFieldPreferences::modes( array_unique( $meta_keys ) );

			foreach ( $meta_per_variation as $variation_id => $meta ) {
				foreach ( $meta as $meta_key => $meta_values ) {
					if (
						WPML_TRANSLATE_CUSTOM_FIELD !== $modes[ $meta_key ]
						|| ! isset( $meta_values[0] )
					) {
						continue;
					}

					$meta_value = maybe_unserialize( $meta_values[0] );

					if ( $meta_value && is_scalar( $meta_value ) ) {
						$values[ self::CUSTOM_FIELD_NAME . $meta_key . ':' . $variation_id ] = $meta_value;
					}
				}
			}
		}

		$this->variations_custom_fields_values_cache[ $product_id ] = $values;

		return $values;
	}

	public function append_variation_custom_fields_to_translation_package( $package, $post ) {
		if ( $this->isWpPostAWcProduct( $post ) ) {
			foreach ( $this->get_variations_custom_fields_values( $post->ID ) as $field_key => $meta_value ) {
				$this->add_to_package( $package, $field_key, $meta_value );
			}
		}

		return $package;
	}

	public function append_variation_custom_fields_to_post_signature( $custom_fields_values, $post_id ) {
		if ( 'product' !== get_post_type( $post_id ) ) {
			return $custom_fields_values;
		}

		foreach ( $this->get_variations_custom_fields_values( $post_id ) as $field_key => $meta_value ) {
			$custom_fields_values[ $field_key ] = $meta_value;
		}

		return $custom_fields_values;
	}

	public function append_variation_downloadable_fields_to_translation_package( $package, $post ) {
		if ( $this->isWpPostAWcProduct( $post ) ) {
			foreach ( $this->get_variations_downloadable_fields_values( $post->ID ) as $field_key => $value ) {
				$this->add_to_package( $package, $field_key, $value );
			}
		}

		return $package;
	}

	public function append_simple_downloadable_fields_to_translation_package( $package, $post ) {
		if ( $this->isWpPostAWcProduct( $post ) ) {
			foreach ( $this->get_simple_downloadable_fields_values( $post->ID ) as $field_key => $value ) {
				$this->add_to_package( $package, $field_key, $value );
			}
		}

		return $package;
	}

	public function append_variation_downloadable_fields_to_post_signature( $custom_fields_values, $post_id ) {
		if ( 'product' !== get_post_type( $post_id ) ) {
			return $custom_fields_values;
		}

		foreach ( $this->get_variations_downloadable_fields_values( $post_id ) as $field_key => $value ) {
			$custom_fields_values[ $field_key ] = $value;
		}

		return $custom_fields_values;
	}

	private function get_variations_downloadable_fields_values( $product_id ) {
		$values = [];

		foreach ( $this->get_translatable_product_variations( $product_id ) as $variation ) {
			$meta_keys_to_translate = $this->get_variation_custom_fields_to_translate( $variation->ID );

			if ( ! in_array( WCML_Downloadable_Products::DOWNLOADABLE_FILES_META, $meta_keys_to_translate, true ) ) {
				continue;
			}

			$meta_value = get_post_meta( $variation->ID, WCML_Downloadable_Products::DOWNLOADABLE_FILES_META, true );

			foreach ( $this->extract_downloadable_fields_values( $meta_value ) as $field_key => $value ) {
				$values[ sprintf( '%s:%d', $field_key, $variation->ID ) ] = $value;
			}
		}

		return $values;
	}

	private function get_simple_downloadable_fields_values( $product_id ) {
		if ( WCML_Downloadable_Products::isDownloadableFilesSetToUseSame( $product_id ) ) {
			return [];
		}

		$product = wc_get_product( $product_id );

		if ( $product instanceof WC_Product && 'simple' === $product->get_type() ) {
			return $this->extract_downloadable_fields_values(
				get_post_meta( $product_id, WCML_Downloadable_Products::DOWNLOADABLE_FILES_META, true )
			);
		}

		return [];
	}

	private function extract_downloadable_fields_values( $meta_value ) {
		$values = [];

		if ( ! is_array( $meta_value ) ) {
			return $values;
		}

		$fileNo = 0;
		foreach ( $meta_value as $downloadableId => $downloadableFile ) {
			foreach ( $downloadableFile as $name => $value ) {
				if ( ! in_array( $name, self::DOWNLOADABLE_FILES_TRANSLATABLE_FIELDS, true ) ) {
					continue;
				}

				$values[ WCML_Downloadable_Products::buildDownloadableFileField( $fileNo, $downloadableId, $name ) ] = $value;
			}
			++$fileNo;
		}

		return $values;
	}

	private function get_original_product_variation_ids( $original_product_id ) {
		$product = function_exists( 'wc_get_product' ) ? wc_get_product( (int) $original_product_id ) : null;

		return $product ? array_map( 'intval', (array) $product->get_children() ) : [];
	}

	public function save_variation_custom_fields_translations( $post_id, $data, $job ) {
		if ( ! Hooks::isProduct( $job ) ) {
			return null;
		}

		$language = $job->language_code;

		$allowed_variation_ids = $this->get_original_product_variation_ids( (int) $job->original_doc_id );

		foreach ( $data as $value ) {

			if ( $value['finished'] && isset( $value['field_type'] ) && strpos( $value['field_type'], self::CUSTOM_FIELD_NAME ) === 0 ) {

				$exp          = explode( ':', $value['field_type'], 3 );
				$meta_key     = $exp[1];
				$variation_id = $exp[2];

				if ( ! in_array( $variation_id, $allowed_variation_ids ) ) {
					continue;
				}

				if ( is_post_type_translated( 'product_variation' ) ) {
					$translated_variation_id = apply_filters( 'wpml_object_id', $variation_id, 'product_variation', false, $language );
				} else {
					global $wpml_post_translations;
					$translations            = $wpml_post_translations->get_element_translations( $variation_id );
					$translated_variation_id = $translations[ $language ] ?? false;
				}

				if ( $translated_variation_id ) {
					update_post_meta( $translated_variation_id, $meta_key, $value['data'] );
				}
			}
		}

	}

	public function append_images_to_translation_package( $package, $post ) {
		if ( $this->isWpPostAWcProduct( $post ) ) {

			$product_images = $this->woocommerce_wpml->media->product_images_ids( $post->ID );
			foreach ( $product_images as $image_id ) {
				$attachment_data = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT post_title,post_excerpt,post_content FROM {$this->wpdb->posts} WHERE ID = %d", $image_id ) );
				if ( ! is_object( $attachment_data ) ) {
					continue;
				}
				$alt_text = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				$alt_text = $alt_text ?: '';
				$this->add_to_package( $package, self::PACKAGE_IMAGE_KEY_PREFIX . $image_id . '-title', $attachment_data->post_title );
				$this->add_to_package( $package, self::PACKAGE_IMAGE_KEY_PREFIX . $image_id . '-caption', $attachment_data->post_excerpt );
				$this->add_to_package( $package, self::PACKAGE_IMAGE_KEY_PREFIX . $image_id . '-description', $attachment_data->post_content );
				$this->add_to_package( $package, self::PACKAGE_IMAGE_KEY_PREFIX . $image_id . '-alt-text', $alt_text );

			}
		}

		return $package;
	}

	public function save_images_translations( $post_id, $data, $job ) {

		$language = $job->language_code;

		$product_images = $this->woocommerce_wpml->media->product_images_ids( $job->original_doc_id );
		foreach ( $product_images as $image_id ) {
			$translated_prod_image = apply_filters( 'wpml_object_id', $image_id, 'attachment', false, $language );
			$image_data            = $this->get_image_data( $image_id, $data );
			if ( ! empty( $image_data ) ) {

				$translation = [];
				if ( isset( $image_data['title'] ) ) {
					$translation['post_title'] = $image_data['title'];
				}
				if ( isset( $image_data['description'] ) ) {
					$translation['post_content'] = $image_data['description'];
				}
				if ( isset( $image_data['caption'] ) ) {
					$translation['post_excerpt'] = $image_data['caption'];
				}

				if ( $translation ) {
					$this->wpdb->update( $this->wpdb->posts, $translation, [ 'id' => $translated_prod_image ] );
				}

				if ( isset( $image_data['alt-text'] ) ) {
					update_post_meta( $translated_prod_image, '_wp_attachment_image_alt', $image_data['alt-text'] );
				}
			}
		}
	}

	private function isWpPostAWcProduct( $post ): bool {
		return 'product' === $post->post_type;
	}

	private function get_image_data( $image_id, $data ) {
		$image_data = [];

		foreach ( $data as $value ) {
			if ( $value['finished'] && isset( $value['field_type'] ) ) {
				if ( strpos( $value['field_type'], self::PACKAGE_IMAGE_KEY_PREFIX . $image_id ) === 0 ) {
					if ( $value['field_type'] === self::PACKAGE_IMAGE_KEY_PREFIX . $image_id . '-title' ) {
						$image_data['title'] = $value['data'];
					}
					if ( $value['field_type'] === self::PACKAGE_IMAGE_KEY_PREFIX . $image_id . '-caption' ) {
						$image_data['caption'] = $value['data'];
					}
					if ( $value['field_type'] === self::PACKAGE_IMAGE_KEY_PREFIX . $image_id . '-description' ) {
						$image_data['description'] = $value['data'];
					}
					if ( $value['field_type'] === self::PACKAGE_IMAGE_KEY_PREFIX . $image_id . '-alt-text' ) {
						$image_data['alt-text'] = $value['data'];
					}
				}
			}
		}

		return $image_data;
	}

	private function add_to_package( &$package, $key, $data ) {
		$package['contents'][ $key ] = [
			'translate' => 1,
			'data'      => $this->tp->encode_field_data( $data ),
			'format'    => 'base64'
		];

	}

	public function flush_variable_product_cache_prefix( $post_id, $data, $job ) {
		if ( Hooks::isProduct( $job ) ) {
			flushProductCachePrefixById( $post_id );
		}
	}
}
