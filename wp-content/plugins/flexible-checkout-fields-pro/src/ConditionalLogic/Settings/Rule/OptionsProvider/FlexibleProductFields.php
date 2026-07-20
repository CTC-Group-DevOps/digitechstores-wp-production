<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProvider;

use Exception;
use Doctrine\Common\Collections\ArrayCollection;
use WPDesk\FCF\Free\Collections\RouteParamBag;

/**
 * Options provider for Flexible Product Fields plugin.
 */
class FlexibleProductFields extends OptionsProvider {

	/**
	 * FPF Group post type.
	 *
	 * @var string
	 */
	private const GROUP_POST_TYPE = 'fpf_fields';

	/**
	 * Group fields definition meta key.
	 */
	private const FIELDS_DEFINITION_KEY = '_fields';

	/**
	 * Supported field types.
	 *
	 * @var array<string>
	 */
	private const SUPPORTED_FIELD_TYPES = [
		'multi-checkbox',
		'checkbox',
		'select',
		'radio-colors',
		'radio-images',
		'radio',
		'multiselect',
	];

	public function get_label(): string {
		return __( 'FPF Field', 'flexible-checkout-fields-pro' );
	}

	public function get_value(): string {
		return 'fpf_field';
	}

	public function get_selections( RouteParamBag $params ): array {
		$fpf_groups = $this->get_product_fields_groups();

		$selections = [];
		foreach ( $fpf_groups as $fpf_group ) {
			$raw_fields = $fpf_group->meta_value;
			$fields     = $this->get_supported_fields( $raw_fields );
			$selections = array_merge(
				$selections,
				// We could use id as key here but when resolving the rule in the cart we only have title information there.
				array_column( $fields->toArray(), 'title', 'title' )
			);
		}
		asort( $selections );

		return $selections;
	}


	public function get_comparisons( RouteParamBag $params ): array {
		return [
			'is'     => __( 'Is', 'flexible-checkout-fields-pro' ),
			'is_not' => __( 'Is not', 'flexible-checkout-fields-pro' ),
		];
	}


	public function get_values( RouteParamBag $param ): array {
		$selection = $param->collection( 'form_values' )->getString( 'selection' );

		$field  = $this->get_field_by_title( $selection );
		$values = [];
		switch ( $field['type'] ) {
			case 'checkbox':
				$checkbox_value            = $field['value'];
				$values[ $checkbox_value ] = $checkbox_value;
				break;
			default:
				foreach ( $field['options'] as $value ) {
					$values[ $value['label'] ] = $value['label'];
				}
				break;
		}

		return $values;
	}

	/**
	 * Retrieves a field by its title.
	 *
	 * @param string $field_title The title of the field to retrieve.
	 * @throws Exception If the field is not found.
	 * @return array The field matching the given title.
	 */
	private function get_field_by_title( string $field_title ): array {
		$fpf_groups = $this->get_product_fields_groups();

		foreach ( $fpf_groups as $fpf_group ) {
			$raw_fields = $fpf_group->meta_value;
			$fields     = $this->get_supported_fields( $raw_fields );

			$field = $fields->filter(
				function ( $field ) use ( $field_title ) {
					return $field['title'] === $field_title;
				}
			)->first();

			if ( $field ) {
				return $field;
			}
		}

		throw new Exception( 'Flexible Product Fields field not found.' );
	}


	/**
	 * Retrieves the product fields groups from the database.
	 *
	 * @return ArrayCollection The collection of product fields groups.
	 */
	private function get_product_fields_groups(): ArrayCollection {
		global $wpdb;

		$fpf_groups = $wpdb->get_results(
			$wpdb->prepare(
				<<<SQL
					SELECT meta_value
					FROM {$wpdb->postmeta} m, {$wpdb->posts} p
					WHERE p.post_type = %s
					AND p.ID = m.post_id
					AND  m.meta_key = %s
				SQL,
				self::GROUP_POST_TYPE,
				self::FIELDS_DEFINITION_KEY
			)
		);

		if ( is_null( $fpf_groups ) ) {
			return new ArrayCollection();
		}

		return new ArrayCollection( $fpf_groups );
	}

	/**
	 * Retrieves the supported fields from a serialized string.
	 *
	 * @param string $fields The string of group fields to unserialize.
	 * @return ArrayCollection The collection of fields definitions..
	 */
	private function get_supported_fields( string $raw_fields ): ArrayCollection {
		$fields = maybe_unserialize( $raw_fields );
		if ( ! is_array( $fields ) ) {
			return new ArrayCollection();
		}

		$fields = new ArrayCollection( $fields );
		return $fields->filter(
			function ( $field ) {
				return isset( $field['type'] ) && in_array( $field['type'], self::SUPPORTED_FIELD_TYPES, true );
			}
		);
	}
}
