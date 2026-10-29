<?php

namespace WCML\Reviews\Translations;

use WPML\Infrastructure\WordPress\Port\Persistence\DatabaseWrite;
use WPML\Legacy\Component\Translation\Application\String\Repository\StringBatchRepository;
use WPML\TM\AutomaticTranslation\Actions\Actions;

use function WPML\Container\make;

class DemandProcessor implements \IWPML_Action {

	const BATCH_NAME_PREFIX = 'wcml-reviews|';
	const ELEMENT_TYPE      = 'st-batch';

	const PAIRS_PER_RUN = 50;

	const STRINGS_PER_BATCH = 100;

	const NAMES_PER_QUERY = 100;

	const FAILED_TRANSIENT = 'wcml_review_translation_failed';

	const FAILED_TTL = 3600;

	const STATUS_NOT_TRANSLATED = 0;

	private $wpdb;

	private $automaticTranslation;

	private $stringBatchRepository;

	private $actions;

	public function __construct(
		\wpdb $wpdb,
		AutomaticTranslation $automaticTranslation,
		$stringBatchRepository = null,
		$actions = null
	) {
		$this->wpdb                  = $wpdb;
		$this->automaticTranslation  = $automaticTranslation;
		$this->stringBatchRepository = $stringBatchRepository;
		$this->actions               = $actions;
	}

	public function add_hooks() {
		if ( apply_filters( FrontEndHooks::HOOK_ENABLE, true ) ) {
			add_action( DemandRecorder::CRON_HOOK, [ $this, 'process' ] );
		}
	}

	public function process() {
		$demand = get_option( DemandRecorder::OPTION, [] );
		delete_option( DemandRecorder::OPTION );

		$pairs = self::flattenDemand( $demand );

		if ( ! $pairs ) {
			return;
		}

		$leftover     = array_slice( $pairs, self::PAIRS_PER_RUN );
		$failed       = [];
		$failedBefore = (array) get_transient( self::FAILED_TRANSIENT );
		$failedNow    = $failedBefore;

		foreach ( array_slice( $pairs, 0, self::PAIRS_PER_RUN ) as $pair ) {
			$key = self::pairKey( $pair[0], $pair[1] );

			try {
				$this->processPair( $pair[0], $pair[1] );

				unset( $failedNow[ $key ] );
			} catch ( \Throwable $e ) {
				$isFirstFailure = ! isset( $failedBefore[ $key ] );

				self::logFailure( $pair, $e, $isFirstFailure );

				if ( $isFirstFailure ) {
					$failedNow[ $key ] = 1;
					$failed[]          = $pair;
				} else {
					unset( $failedNow[ $key ] );
				}
			}
		}

		if ( $failedNow !== $failedBefore ) {
			set_transient( self::FAILED_TRANSIENT, $failedNow, self::FAILED_TTL );
		}

		$unfinished = array_merge( $failed, $leftover );

		if ( $unfinished ) {
			$this->requeue( $unfinished );
			DemandRecorder::scheduleRun();
		}
	}

	private static function flattenDemand( $demand ) {
		$pairs = [];

		if ( ! is_array( $demand ) ) {
			return $pairs;
		}

		foreach ( $demand as $productId => $languages ) {
			foreach ( (array) $languages as $languageCode ) {
				if ( $productId && $languageCode ) {
					$pairs[] = [ (int) $productId, (string) $languageCode ];
				}
			}
		}

		return $pairs;
	}

	private static function pairKey( $productId, $languageCode ) {
		return $productId . '|' . $languageCode;
	}

	private static function logFailure( array $pair, $e, $willRetry ) {
		if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) {
			return;
		}

		error_log(
			sprintf(
				'WCML review translation failed for product %d into %s (%s): %s',
				$pair[0],
				$pair[1],
				$willRetry ? 'retrying once' : 'giving up until the next view of the product',
				$e->getMessage()
			)
		);
	}

	private function requeue( array $pairs ) {
		$stored = get_option( DemandRecorder::OPTION, [] );

		if ( ! is_array( $stored ) ) {
			$stored = [];
		}

		foreach ( $pairs as $pair ) {
			$productId = $pair[0];
			$existing  = isset( $stored[ $productId ] ) && is_array( $stored[ $productId ] )
				? $stored[ $productId ]
				: [];

			$existing[]           = $pair[1];
			$stored[ $productId ] = array_values( array_unique( $existing ) );
		}

		update_option( DemandRecorder::OPTION, $stored, false );
	}

	private function processPair( $productId, $targetLanguage ) {
		$stringIdsBySourceLanguage = $this->getUntranslatedStringIds( $productId, $targetLanguage );

		foreach ( $stringIdsBySourceLanguage as $sourceLanguage => $stringIds ) {
			if ( ! $this->automaticTranslation->isPairEligible( $sourceLanguage, $targetLanguage ) ) {
				continue;
			}

			foreach ( array_chunk( $stringIds, self::STRINGS_PER_BATCH ) as $chunk ) {
				$batchId = $this->getStringBatchRepository()->create(
					self::BATCH_NAME_PREFIX . $targetLanguage,
					$chunk,
					$sourceLanguage
				);

				$this->getActions()->createNewTranslationJobs(
					$sourceLanguage,
					[ [ $batchId, $targetLanguage ] ],
					self::ELEMENT_TYPE
				);
			}
		}
	}

	private function getUntranslatedStringIds( $productId, $targetLanguage ) {
		$names = $this->getReviewStringNames( $this->getTranslationGroupIds( $productId ) );

		if ( ! $names ) {
			return [];
		}

		$grouped = [];

		foreach ( array_chunk( $names, self::NAMES_PER_QUERY ) as $chunk ) {
			foreach ( $this->queryUntranslatedStrings( $chunk, $targetLanguage ) as $row ) {
				$language = (string) \WPML\FP\Obj::prop( 'language', $row );

				if ( '' !== $language ) {
					$grouped[ $language ][] = (int) \WPML\FP\Obj::prop( 'id', $row );
				}
			}
		}

		return $grouped;
	}

	private function queryUntranslatedStrings( array $names, $targetLanguage ) {
		$strings      = $this->wpdb->prefix . 'icl_strings';
		$translations = $this->wpdb->prefix . 'icl_string_translations';
		$placeholders = implode( ',', array_fill( 0, count( $names ), '%s' ) );

		$sql = $this->wpdb->prepare(
			"SELECT strings.id, strings.language
			FROM {$strings} AS strings
			LEFT JOIN {$translations} AS translations
				ON translations.string_id = strings.id AND translations.language = %s
			WHERE strings.context = %s
				AND strings.name IN ({$placeholders})
				AND ( translations.status IS NULL OR translations.status = %d )",
			array_merge(
				[ $targetLanguage, FrontEndHooks::CONTEXT ],
				$names,
				[ self::STATUS_NOT_TRANSLATED ]
			)
		);

		return (array) $this->wpdb->get_results( $sql );
	}

	private function getTranslationGroupIds( $productId ) {
		$translations = $this->wpdb->prefix . 'icl_translations';

		$sql = $this->wpdb->prepare(
			"SELECT DISTINCT siblings.element_id
			FROM {$translations} AS source
			INNER JOIN {$translations} AS siblings
				ON siblings.trid = source.trid AND siblings.element_type = source.element_type
			WHERE source.element_id = %d AND source.element_type = %s",
			$productId,
			DemandRecorder::PRODUCT_ELEMENT_TYPE
		);

		$ids = array_values( array_filter( array_map( 'intval', (array) $this->wpdb->get_col( $sql ) ) ) );

		return $ids ? $ids : [ (int) $productId ];
	}

	private function getReviewStringNames( array $productIds ) {
		if ( ! $productIds ) {
			return [];
		}

		$product_id_list = implode( ',', array_map( 'intval', $productIds ) );
		$type_formats    = implode( ',', array_fill( 0, count( FrontEndHooks::COMMENT_TYPES ), '%s' ) );

		$sql = $this->wpdb->prepare(
			"SELECT comment_ID, comment_post_ID
			FROM {$this->wpdb->comments}
			WHERE comment_post_ID IN ({$product_id_list})
				AND comment_type IN ({$type_formats})
				AND comment_approved = '1'
				AND comment_content <> ''",
			FrontEndHooks::COMMENT_TYPES
		);

		$names = [];

		foreach ( (array) $this->wpdb->get_results( $sql ) as $row ) {
			$names[] = FrontEndHooks::buildReviewStringName(
				\WPML\FP\Obj::prop( 'comment_post_ID', $row ),
				\WPML\FP\Obj::prop( 'comment_ID', $row )
			);
		}

		return $names;
	}

	private function getStringBatchRepository() {
		if ( ! $this->stringBatchRepository ) {
			global $sitepress;

			$this->stringBatchRepository = new StringBatchRepository( new DatabaseWrite( $this->wpdb ), $sitepress );
		}

		return $this->stringBatchRepository;
	}

	private function getActions() {
		if ( ! $this->actions ) {
			$this->actions = make( Actions::class );
		}

		return $this->actions;
	}
}
