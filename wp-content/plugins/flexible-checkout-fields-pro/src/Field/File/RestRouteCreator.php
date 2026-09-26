<?php

namespace WPDesk\FCF\Pro\Field\File;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FCF\Pro\Exception\PluginException;
use WPDesk\FCF\Pro\Plugin;

/**
 * .
 */
class RestRouteCreator implements Hookable {

	const ROUTE_NAME          = 'file-upload';
	const PARAM_FILE          = 'file';
	const PARAM_FIELD_NAME    = 'field_name';
	const RESPONSE_ERROR_CODE = 'fcf_file_upload_error';

	/**
	 * @var FileUploader
	 */
	private $file_uploader;

	public function __construct( ?FileUploader $file_uploader = null ) {
		$this->file_uploader = $file_uploader ?: new FileUploader();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hooks() {
		add_action( 'rest_api_init', [ $this, 'register_endpoint' ] );
	}

	public static function get_route_url(): string {
		$url = get_rest_url(
			null,
			sprintf(
				'%1$s/%2$s',
				Plugin::ROUTE_NAMESPACE,
				self::ROUTE_NAME
			)
		);

		return add_query_arg( '_wpnonce', wp_create_nonce( 'wp_rest' ), $url );
	}

	/**
	 * @return void
	 * @internal
	 */
	public function register_endpoint() {
		register_rest_route(
			Plugin::ROUTE_NAMESPACE,
			self::ROUTE_NAME,
			[
				'methods'             => \WP_REST_Server::ALLMETHODS,
				'permission_callback' => function () {
					return ( wp_verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'wp_rest' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
				},
				'callback'            => [ $this, 'handle_upload_request' ],
				'args'                => [
					self::PARAM_FILE       => [
						'description' => 'Binary file',
						'required'    => false,
					],
					self::PARAM_FIELD_NAME => [
						'description' => 'Name of FCF field',
						'required'    => false,
					],
				],
			]
		);
	}

	/**
	 * @param \WP_REST_Request $request .
	 *
	 * @return \WP_REST_Response|\WP_Error .
	 * @internal
	 */
	public function handle_upload_request( \WP_REST_Request $request ) {
		$params = $request->get_params();

		try {
			$uploaded_file = $this->file_uploader->upload_file(
				$_FILES[ self::PARAM_FILE ] ?? null, // phpcs:ignore WordPress.Security.ValidatedSanitizedInput, WordPress.Security.NonceVerification.Missing
				$params[ self::PARAM_FIELD_NAME ] ?? null
			);

			return new \WP_REST_Response(
				[
					'file_id' => $uploaded_file,
				],
				200
			);
		} catch ( PluginException $e ) {
			return new \WP_Error(
				self::RESPONSE_ERROR_CODE,
				$e->getMessage(),
				[
					'status' => 400,
				]
			);
		}
	}
}
