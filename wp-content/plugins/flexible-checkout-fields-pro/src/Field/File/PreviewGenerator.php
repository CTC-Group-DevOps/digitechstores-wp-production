<?php

namespace WPDesk\FCF\Pro\Field\File;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * .
 */
class PreviewGenerator implements Hookable {

	const REWRITE_ENDPOINT_PREFIX = 'flexible-checkout-fields';
	const REWRITE_NONCE_SEPARATOR = '_';
	const REWRITE_NONCE_ACTION    = 'fcf_attachment_%s';

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
		add_filter( 'query_vars', [ $this, 'set_rewrite_query_var' ] );
		add_action( 'init', [ $this, 'register_rewrite_endpoint' ] );
		add_action( 'template_redirect', [ $this, 'load_file_preview' ] );
	}

	public function get_file_url( string $request_id ): string {
		$attachment  = $this->get_attachment_data( $request_id );
		$nonce_value = '';
		if ( $attachment !== null ) {
			$nonce_value .= sprintf(
				'%1$s%2$s',
				self::REWRITE_NONCE_SEPARATOR,
				wp_create_nonce( sprintf( self::REWRITE_NONCE_ACTION, $attachment->ID ) )
			);
		}

		$permalink = get_option( 'permalink_structure' );
		return sprintf(
			( $permalink ) ? '%1$s/%2$s/%3$s' : '%1$s/?%2$s=%3$s',
			get_home_url(),
			self::REWRITE_ENDPOINT_PREFIX,
			$request_id . $nonce_value
		);
	}

	/**
	 * @return \WP_Post|null
	 */
	public function get_attachment_data( string $request_id ) {
		if ( ! is_numeric( $request_id ) ) {
			return null;
		}

		$post = get_post( $request_id );
		if ( is_object( $post ) && ( $post->post_type === 'attachment' ) ) {
			return $post;
		}
		return null;
	}

	/**
	 * @internal
	 */
	public function set_rewrite_query_var( array $vars ): array {
		$vars[] = self::REWRITE_ENDPOINT_PREFIX;
		return $vars;
	}

	/**
	 * @return void
	 *
	 * @internal
	 */
	public function register_rewrite_endpoint() {
		add_rewrite_endpoint( self::REWRITE_ENDPOINT_PREFIX, EP_ROOT );

		$rules = get_option( 'rewrite_rules' );
		if ( ! $rules || ! isset( $rules[ self::REWRITE_ENDPOINT_PREFIX . '(/(.*))?/?$' ] ) ) {
			flush_rewrite_rules( false );
		}
	}

	/**
	 * @return void
	 */
	public function load_file_preview() {
		$request_id = get_query_var( self::REWRITE_ENDPOINT_PREFIX );
		if ( ! $request_id ) {
			return;
		}

		$request_data  = explode( self::REWRITE_NONCE_SEPARATOR, $request_id );
		$attachment_id = $request_data[0];
		$nonce_value   = $request_data[1] ?? null;

		$attachment = ( ( $nonce_value !== null )
			&& wp_verify_nonce( $nonce_value, sprintf( self::REWRITE_NONCE_ACTION, $attachment_id ) ) )
			? $this->get_attachment_data( $attachment_id )
			: null;

		if ( $attachment !== null ) {
			$file_path = get_attached_file( $attachment->ID ) ?: null;
		} else {
			$file_path = $this->file_uploader->get_saved_file_path( $attachment_id );
		}

		if ( $file_path === null ) {
			wp_die(
				wp_kses_post(
					__( 'Sorry, you are not allowed to access this page.', 'flexible-checkout-fields-pro' )
				),
				404
			);
		}

		header( 'Content-type: ' . mime_content_type( $file_path ), true, 200 );
		header( 'Content-Disposition: filename=' . basename( $file_path ) );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
		readfile( $file_path );

		die();
	}
}
