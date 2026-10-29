<?php

namespace WCML\Setup;

class CostEstimateContributors implements \IWPML_Action {

	private const PACKAGE_CONTRIBUTORS = [
		'append_custom_attributes_to_translation_package',
		'append_variation_custom_fields_to_translation_package',
		'append_variation_downloadable_fields_to_translation_package',
		'append_simple_downloadable_fields_to_translation_package',
		'append_images_to_translation_package',
	];

	private const CONFIG_ACTION_MODES = [
		'translate' => WPML_TRANSLATE_CUSTOM_FIELD,
		'copy'      => WPML_COPY_CUSTOM_FIELD,
		'copy-once' => WPML_COPY_ONCE_CUSTOM_FIELD,
		'ignore'    => WPML_IGNORE_CUSTOM_FIELD,
	];

	private $tpSupport;

	private $declaredPreferences;

	public function __construct( \WCML_TP_Support $tpSupport ) {
		$this->tpSupport = $tpSupport;
	}

	public function add_hooks() {
		add_filter( 'wpml_resolve_custom_field_preferences', [ $this, 'answerDeclaredPreferences' ], 10, 3 );
		add_filter( 'wpml_tm_translation_job_data', [ $this, 'appendToPackage' ], 10, 2 );
	}

	public function appendToPackage( $package, $post ) {
		foreach ( self::PACKAGE_CONTRIBUTORS as $contributor ) {
			$package = $this->tpSupport->$contributor( $package, $post );
		}

		return $package;
	}

	public function answerDeclaredPreferences( $answers, $metaKeys, $type ) {
		if ( 'post' !== $type || ! is_array( $answers ) ) {
			return $answers;
		}

		$declared = $this->declaredPreferences();

		foreach ( $metaKeys as $metaKey ) {
			if ( isset( $declared[ $metaKey ] ) ) {
				$answers[ $metaKey ] = $declared[ $metaKey ];
			}
		}

		return $answers;
	}

	private function declaredPreferences(): array {
		if ( null === $this->declaredPreferences ) {
			$this->declaredPreferences = [];

			$useInternalErrors = libxml_use_internal_errors( true );
			$config            = simplexml_load_file( WCML_PLUGIN_PATH . '/wpml-config.xml' );
			libxml_clear_errors();
			libxml_use_internal_errors( $useInternalErrors );

			if ( false !== $config && isset( $config->{'custom-fields'} ) ) {
				foreach ( $config->{'custom-fields'}->{'custom-field'} as $field ) {
					$action = (string) $field['action'];

					if ( isset( self::CONFIG_ACTION_MODES[ $action ] ) ) {
						$this->declaredPreferences[ (string) $field ] = self::CONFIG_ACTION_MODES[ $action ];
					}
				}
			}
		}

		return $this->declaredPreferences;
	}
}
