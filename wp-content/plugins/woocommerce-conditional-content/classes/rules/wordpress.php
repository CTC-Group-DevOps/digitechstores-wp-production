<?php
/**
 * Page, post type and singular/archive rules.
 *
 * NOT DEAD CODE, and not to be deleted. These three rules are complete but have
 * never been registered - woocommerce-conditional-content-main.php does not
 * include this file and default_rule_types() does not list them - so they have
 * never appeared in the rule picker.
 *
 * Decision, Lucas, 2026-08-29: wire them up in 3.0 rather than in a point
 * release. Non-WooCommerce page targeting is a common ask and belongs with the
 * block-theme work, where it can ship with documentation and tests rather than
 * appearing unannounced in a bugfix release.
 *
 * Registering them means: include this file from the main class, add the three
 * slugs to default_rule_types(), and add scenarios to WCCC_Rule_Shapes - the
 * contract tests fail for any registered rule type with no scenario, which is
 * how you will know you have finished.
 */

class WC_Conditional_Content_Rule_Page_Select extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'page_select' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'in' => __( "is", 'wc_conditional_content' ),
			'notin' => __( "is not", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possible_rule_values(): array {
		$result = array();

		$pages  = get_posts( [
			'post_type' => 'page',
			'post_status' => 'publish',
			'nopaging' => true,
			'order' => 'ASC',
			'orderby' => 'menu_order, post_title'
		] );

		if ($pages) {
			foreach($pages as $page) {
				$result[$page->ID] = $page->post_title;
			}
		}

		return $result;
	}

	public function get_condition_input_type(): string {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$result = false;

		if ( is_page() && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$in = in_array(get_the_ID(), $rule_data['condition']);
			$result = $rule_data['operator'] == 'in' ? $in : !$in;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}


class WC_Conditional_Content_Rule_Post_Type_Select extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'post_type_select' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'in'    => __( "is one of", 'wc_conditional_content' ),
			'notin' => __( "is not one of", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possible_rule_values(): array {
		$result = array();

		$types = get_post_types([
			'public' => true,
		], 'objects');

		if ( $types && !is_wp_error($types) ) {
			foreach ( $types as $type ) {
				$result[ $type->name ] = $type->labels->name;
			}
		}

		return $result;
	}

	public function get_condition_input_type(): string {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$result = false;

		// get the post type for the current global object.
		$current_post_type = get_post_type();

		// check if it is in our list.
		$in = in_array($current_post_type, $rule_data['condition']);
		$result = $rule_data['operator'] == 'in' ? $in : !$in;

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Post_Location_Select extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'post_location_select' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'in'    => __( "is", 'wc_conditional_content' )
		);

		return $operators;
	}

	public function get_possible_rule_values(): array {
		$result = [
			'singular' => 'Singular',
			'archive' => 'Archive / Category Page',
			'both' => 'Both'
		];

		return $result;
	}

	public function get_condition_input_type(): string {
		return 'Select';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$location = $rule_data['condition'];

		switch ($location) {
			case 'singular' :
				$result = is_singular();
				break;
			case 'archive':
				$result = is_archive();
				break;
			case 'both' :
				$result = is_singular() || is_archive();
				break;
			default:
				$result = false;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}


