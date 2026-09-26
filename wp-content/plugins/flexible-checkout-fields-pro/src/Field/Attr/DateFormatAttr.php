<?php

namespace WPDesk\FCF\Pro\Field\Attr;

use WPDesk\FCF\Pro\Field\Type\DateType;
use WPDesk\FCF\Pro\Settings\Option\DateFormatOption;

/**
 * {@inheritdoc}
 */
class DateFormatAttr implements Attr {

	const ATTR_NAME = 'data-date-format';

	/**
	 * @var string[] Characters for date format used in plugin settings.
	 */
	private static $date_format_settings = [ 'dd', 'd', 'mm', 'm', 'yy', 'y' ];

	/**
	 * @var string[] Characters for date format used in JS.
	 */
	private static $date_format_js = [ 'dd', 'd', 'mm', 'm', 'yyyy', 'yy' ];

	/**
	 * @var string[] Characters for date format used in PHP.
	 */
	private static $date_format_php = [ 'd', 'j', 'm', 'n', 'Y', 'y' ];

	/**
	 * {@inheritdoc}
	 */
	public function is_available( array $field_data ): bool {
		return ( isset( $field_data['type'] ) && ( $field_data['type'] === DateType::FIELD_TYPE ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name(): string {
		return self::ATTR_NAME;
	}

	/**
	 * @param array{date_format?: string} $field_data
	 */
	public function get_value( array $field_data ): string {
		$date_format = ( $field_data[ DateFormatOption::FIELD_NAME ] ?? '' ) ?: DateFormatOption::DEFAULT_DATE_FORMAT;

		return $this->convert_date_format_for_js( $date_format );
	}

	/**
	 * Converts date format to JS date format.
	 *
	 * @param string $date_format Original date format.
	 */
	public static function convert_date_format_for_js( string $date_format ): string {
		return self::convert_date_format( $date_format, self::$date_format_settings, self::$date_format_js );
	}

	/**
	 * Converts date format to PHP date format.
	 * Translate our simplified date format (i.e. 'd/m/Y') to PHP date format (i.e. 'j/n/Y').
	 *
	 * @param string $date_format Original date format.
	 */
	public static function convert_date_format_for_php( string $date_format ): string {
		return self::convert_date_format( $date_format, self::$date_format_settings, self::$date_format_php );
	}

	/**
	 * Converts date format to different date format.
	 *
	 * @param string   $date_format      Original date format.
	 * @param string[] $old_format_parts .
	 * @param string[] $new_format_parts .
	 *
	 * @return string Updated date format.
	 */
	private static function convert_date_format( string $date_format, array $old_format_parts, array $new_format_parts ): string {
		preg_match_all( '/([a-zA-Z]+)/', $date_format, $matches );
		$format_parts = $matches[0] ?? [];
		if ( ! $format_parts ) {
			return $date_format;
		}

		foreach ( $format_parts as $format_part ) {
			$index = array_search( $format_part, $old_format_parts, true );
			if ( $index !== false ) {
				$date_format = str_replace(
					$old_format_parts[ $index ],
					$new_format_parts[ $index ],
					$date_format
				);
			}
		}

		return $date_format;
	}
}
