<?php

namespace WPDesk\FCF\Pro\Field\File;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FCF\Pro\Field\Type\FileType;

/**
 * .
 */
class FieldValuePrinter implements Hookable {

	/**
	 * @var FileUploader
	 */
	private $file_uploader;

	/**
	 * @var PreviewGenerator
	 */
	private $preview_generator;

	public function __construct(
		?FileUploader $file_uploader = null,
		?PreviewGenerator $preview_generator = null
	) {
		$this->file_uploader     = $file_uploader ?: new FileUploader();
		$this->preview_generator = $preview_generator ?: new PreviewGenerator();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hooks() {
		add_action( 'flexible_checkout_fields_print_value', [ $this, 'print_field_value' ], 10, 2 );
		add_action( 'flexible_checkout_fields_user_meta_display_value', [ $this, 'print_field_value' ], 10, 2 );
		add_action( 'flexible_checkout_fields_print_value_raw', [ $this, 'print_field_value_as_url' ], 10, 2 );
	}

	/**
	 * @param string $value      .
	 * @param array  $field_data .
	 *
	 * @return string
	 */
	public function print_field_value( $value, $field_data ) {
		if ( ( $field_data['type'] ?? '' ) !== FileType::FIELD_TYPE ) {
			return $value;
		}

		$value_ids = json_decode( $value, true ) ?: [];
		$values    = [];
		foreach ( (array) $value_ids as $request_id ) {
			if ( $request_id === '' ) {
				continue;
			}
			$values[] = $this->preview_generator->get_file_url( $request_id );
		}

		return implode( ', ', $values );
	}

	/**
	 * @param mixed $value      .
	 * @param array $field_data .
	 *
	 * @return mixed
	 */
	public function print_field_value_as_url( $value, $field_data ) {
		if ( ( $field_data['type'] !== FileType::FIELD_TYPE ) ) {
			return $value;
		}

		$value_ids = json_decode( $value, true ) ?: [];
		$values    = [];
		foreach ( (array) $value_ids as $request_id ) {
			if ( $request_id === '' ) {
				continue;
			}
			$attachment = $this->preview_generator->get_attachment_data( $request_id );
			$filename   = ( $attachment !== null )
				? $attachment->guid
				: $this->file_uploader->get_saved_file_path( $request_id );

			$values[] = sprintf(
				'<a href="%1$s" target="_blank">%2$s</a>',
				$this->preview_generator->get_file_url( $request_id ),
				basename( $filename )
			);
		}

		return $values;
	}
}
