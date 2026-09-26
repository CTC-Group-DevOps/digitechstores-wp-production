<?php

namespace WPDesk\FCF\Pro\ConditionalLogic;

use WPDesk\FCF\Pro\ConditionalLogic\RuleResolverFactory;

/**
 * Format needed conditional logic fields settings to be used on front end.
 */
class SettingsFormatter {

	/**
	 * FCF Fields with conditional logic.
	 *
	 * @var array<int, mixed>
	 */
	private $fields = [];

	/**
	 * Resolver object factory
	 *
	 * @var RuleResolverFactory
	 */
	private $resolver_factory;

	/**
	 * @param array<string, mixed> $settings
	 * @param RuleResolverFactory $resolver_factory
	 */
	public function __construct( array $settings, RuleResolverFactory $resolver_factory ) {
		$this->setup_fields_with_conditional_logic( $settings );
		$this->resolver_factory = $resolver_factory;
	}

	/**
	 * From all settings extract fields with conditional logic.
	 *
	 * @param array<string, mixed> $settings
	 */
	private function setup_fields_with_conditional_logic( array $settings ): void {
		foreach ( $settings as $section ) {
			foreach ( $section as $field ) {
				if ( $this->has_conditional_logic( $field ) ) {
					$this->fields[] = $field;
				}
			}
		}
	}

	/**
	 * Get all existing rules, from all fields, all groups.
	 * Hashing a rule will help us to compare rules with each other
	 * and prevent resolving them multiple times.
	 *
	 * @return array<string, mixed>
	 */
	public function get_all_rules(): array {
		$rules = [];
		foreach ( $this->fields as $field ) {
			$field_rules = $this->get_raw_field_rules( $field );
			foreach ( $field_rules as $rule ) {
				if ( ! $this->is_rule_valid( $rule ) ) {
					continue;
				}
				$hash = $this->hash( $rule );

				if ( isset( $rules[ $hash ] ) ) {
					continue;
				}

				$rule['result'] = $this->resolve_rule( $rule );
				$rules[ $hash ] = $rule;
			}
		}

		return $rules;
	}

	/**
	 * Get all fields and its actions information.
	 *
	 * @return array<int, mixed>
	 */
	public function get_fields_actions(): array {
		$fields = [];
		foreach ( $this->fields as $field ) {
			$field_groups = $this->get_field_groups( $field );
			foreach ( $field_groups as $group ) {
				if ( ! isset( $group['action'] ) ) {
					continue;
				}
				$rules_relations = $this->hash_group_rules( $group );

				$fields[] = [
					'name'   => $field['name'],
					'type'   => $field['type'] ?? 'text',
					'action' => $group['action'],
					'hash'   => $this->hash( $rules_relations ),
				];
			}
		}

		return $fields;
	}

	/**
	 * Get all rules relations (rules inside a group) information.
	 *
	 * @return array<string, mixed>
	 */
	public function get_rules_relations(): array {
		$fields = [];
		foreach ( $this->fields as $field ) {
			$field_groups = $this->get_field_groups( $field );
			foreach ( $field_groups as $group ) {
				if ( ! isset( $group['action'] ) ) {
					continue;
				}
				$rules_relations = $this->hash_group_rules( $group );
				$hash            = $this->hash( $rules_relations );
				if ( isset( $fields[ $hash ] ) ) {
					continue;
				}
				$fields[ $hash ] = $rules_relations;
			}
		}

		return $fields;
	}

	/**
	 * Resolve a rule on the backend side (if possible).
	 *
	 * @param array<string, mixed> $rule
	 */
	private function resolve_rule( array $rule ): bool {
		$condition_resolver = $this->resolver_factory->create( $rule['category'] );

		return $condition_resolver->resolve( $rule );
	}

	/**
	 * Get all field rules. Raw in this context means no nesting.
	 *
	 * @param array<string, mixed> $field
	 * @return array<int, mixed> Simple array - no nesting.
	 */
	private function get_raw_field_rules( array $field ): array {
		$rules        = [];
		$field_groups = $this->get_field_groups( $field );
		foreach ( $field_groups as $group ) {
			$rules = array_merge( $rules, $this->get_raw_group_rules( $group ) );
		}

		return $rules;
	}


	/**
	 * Get all group rules. Raw in this context means no nesting.
	 *
	 * @param array<string, mixed> $group
	 * @return array<int, mixed> Simple array - no nesting.
	 */
	private function get_raw_group_rules( array $group ): array {
		$raw_rules = [];
		$rules     = $this->get_group_rules( $group );
		foreach ( $rules as $or_rules ) {
			foreach ( $or_rules as $rule ) {
				if ( ! $this->is_rule_valid( $rule ) ) {
					continue;
				}
				$raw_rules[] = $rule;
			}
		}

		return $raw_rules;
	}

	/**
	 * Hashes an array and returns the MD5 hash.
	 *
	 * @param array<mixed, mixed> $rule an array to be hashed
	 * @return string the MD5 hash of the serialized array
	 */
	private function hash( array $rule ): string {
		return md5( serialize( $rule ) );
	}

	/**
	 * Replace nested rules with hashes, without touching the logic (or & and)
	 *
	 * @param array<string, mixed> $group
	 * @return array<int, mixed>
	 */
	private function hash_group_rules( array $group ): array {
		$rules_hashed = [];
		$group_rules  = $this->get_group_rules( $group );
		foreach ( $group_rules as $or_rules ) {
			$and_rules_hashes = [];
			foreach ( $or_rules as $rule ) {
				if ( ! $this->is_rule_valid( $rule ) ) {
					continue;
				}
				$and_rules_hashes[] = $this->hash( $rule );
			}

			$rules_hashed[] = $and_rules_hashes;
		}

		return $rules_hashed;
	}

	/**
	 * Check if field has conditional logic
	 *
	 * @param array<string, mixed> $field
	 */
	private function has_conditional_logic( array $field ): bool {
		return isset( $field['conditional_logic'] ) && is_array( $field['conditional_logic'] ) && count( $field['conditional_logic'] ) > 0;
	}

	/**
	 * Check if group has defined rules
	 *
	 * @param array<string, mixed> $group
	 */
	private function has_rules( array $group ): bool {
		return isset( $group['rules'] ) && is_array( $group['rules'] ) && count( $group['rules'] ) > 0;
	}

	/**
	 * Checks if the given rule is valid.
	 *
	 * @param array<string, mixed> $rule The rule to be validated
	 * @return bool
	 */
	private function is_rule_valid( array $rule ): bool {
		return isset( $rule['category'] ) &&
			isset( $rule['selection'] ) &&
			isset( $rule['comparison'] ) &&
			isset( $rule['values'] );
	}

	/**
	 * Function wrapping for better readability.
	 *
	 * @param array<string, mixed> $field
	 * @return array<int, mixed>
	 */
	private function get_field_groups( array $field ): array {
		return $field['conditional_logic'];
	}

	/**
	 * Function wrapping for better readability.
	 *
	 * @param array<string, mixed> $group
	 * @return array<int, mixed>
	 */
	private function get_group_rules( array $group ): array {
		return $this->has_rules( $group ) ? $group['rules'] : [];
	}
}
