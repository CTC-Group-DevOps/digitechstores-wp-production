<?php
/**
 * REST  controller
 *
 * Handles requests to the / endpoint.
 *
 * @package Addify\RestApi
 */

if (!defined('ABSPATH')) {
	exit;
}


/**
 * REST API controller class.
 *
 * @package Addify\RestApi
 * @extends WC_REST_CRUD_Controller
 */
class Af_Multi_Inventory_Api_Controller extends WC_REST_CRUD_Controller {



	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';
	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'addify_multi_inventory';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $taxonomy_type = 'mli_location';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $inventory_post_type = '';


	/**
	 * Cart_recovery actions.
	 */
	public function __construct() {
	}

	/**
	 * Register the routes for Cart_recovery.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'location_id'  => array(
						'description' => esc_html__('Unique identifier for the location.', 'addify-multi-inventory-management'),
						'type'        => 'integer',
						'required'    => true,
					),
					'product_id'   => array(
						'description' => esc_html__('Unique identifier for the product.', 'addify-multi-inventory-management'),
						'type'        => 'integer',
						'required'    => true,
					),
					'inventory_id' => array(
						'description' => esc_html__('Unique identifier for the inventory.', 'addify-multi-inventory-management'),
						'type'        => 'integer',
						'required'    => true,
					),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param(
							array(
								'default' => 'view',
							)
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check if a given request has access to read an item.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		$locatio_id = isset($request['locatio_id']) ? $request['locatio_id'] : 0;
		if ($locatio_id >= 1 && ( !wc_rest_check_product_term_permissions($this->taxonomy_type, 'read', $locatio_id) || !wc_rest_check_product_term_permissions($this->taxonomy_type, 'edit', $locatio_id) ) ) {

			return new WP_Error('woocommerce_rest_cannot_view', __('Sorry, you cannot view this resource.', 'addify-multi-inventory-management'), array( 'status' => rest_authorization_required_code() ));
		}

		return true;
	}

	/**
	 * Prepare a single product output for response.
	 *
	 * @param WC_Data         $object  Object data.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  3.0.0
	 * @return WP_REST_Response
	 */
	public function prepare_object_for_response( $object, $request ) {
		$context       = !empty($request['context']) ? $request['context'] : 'view';
		$this->request = $request;
		$data          = $this->get_cart_recovery_data($object, $context, $request);

		$data     = $this->filter_response_by_context($data, $context);
		$response = rest_ensure_response($data);
		/**
		 * Filter the data for a response.
		 *
		 * The dynamic portion of the hook name, $this->post_type,
		 * refers to object type being prepared for the response.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WC_Data          $object   Object data.
		 * @param WP_REST_Request  $request  Request object.
		 */
		return apply_filters("woocommerce_rest_prepare_{$this->post_type}_object", $response, $object, $request);
	}

	public function get_cart_recovery_data( $object, $context, $request ) {



		return array();
	}

	protected function get_object( $af_mi_id ) {

		return get_post($af_mi_id);
	}
	/**
	 * Get the Product's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'id'            => array(
					'description' => __('Unique identifier for the resource.', 'addify-multi-inventory-management'),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'title'         => array(
					'description' => __('Cart title.', 'addify-multi-inventory-management'),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'date_created'  => array(
					'description' => __("The date the cart was created, in the site's timezone.", 'addify-multi-inventory-management'),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'cart_status'   => array(
					'description' => __('cart status.', 'addify-multi-inventory-management'),
					'type'        => 'string',
					'default'     => 'publish',
					'enum'        => array_merge(array_keys(get_post_statuses()), array( 'future' )),
					'context'     => array( 'view', 'edit' ),
				),

				'cart_content'  => array(
					'description' => __('The contents of the recorded the cart.', 'addify-multi-inventory-management'),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'user_id'       => array(
					'description' => __('The id of the user who recorded the cart.', 'addify-multi-inventory-management'),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'user_email'    => array(
					'description' => __('The User email who recorded the cart.', 'addify-multi-inventory-management'),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'cart_subtotal' => array(
					'description' => __('The recorded cart subtotal.', 'addify-multi-inventory-management'),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'cart_total'    => array(
					'description' => __('The recorded cart total.', 'addify-multi-inventory-management'),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
			),
		);
		return $this->add_additional_fields_schema($schema);
	}

	public function get_items( $request ) {


		$cart_recovery_ids = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => $this->taxonomy_type,
				'post_status' => 'publish',
			)
		);

		$data = array();


		$response = rest_ensure_response($data);

		return $response;
	}

	public function get_item( $request ) {
		$object = $this->get_object((int) $request['location_id']);

		if ($request['id']) {
			return new WP_Error("woocommerce_rest_{$this->taxonomy_type}_invalid_id", __('Invalid ID.', 'addify-multi-inventory-management'), array( 'status' => 404 ));
		}

		$data     = $this->prepare_object_for_response($this->get_object($object->ID), $request);
		$response = rest_ensure_response($data);
		// https://woocommerce.addifypro.com/testing_demo/wp-json/wc/v3/afacr_cart_recovery/9599
		return $response;
	}
}
