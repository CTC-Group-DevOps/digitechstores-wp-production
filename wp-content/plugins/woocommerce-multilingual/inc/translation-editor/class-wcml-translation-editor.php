<?php

use WCML\TranslationJob\Hooks;
use WCML\Utilities\DB;
use WCML\Utilities\SyncHash;
use WPML\TM\PostEditScreen\Endpoints\SetEditorMode;
use WPML\TM\Settings\RequestSettings;

use function WCML\functions\getSetting;
use function WPML\Container\make;

class WCML_Translation_Editor {

	const AUTO_SLUG_NONCE = 'wcml_editor_auto_slug';

	const EDITOR_ANSWER_PRIORITY = 100;

	private $woocommerce_wpml;
	private $sitepress;
	private $wpdb;

	public function __construct( woocommerce_wpml $woocommerce_wpml, $sitepress, wpdb $wpdb ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
		$this->wpdb             = $wpdb;
	}

	public function add_hooks() {

		add_filter( 'wpml-translation-editor-fetch-job', [ $this, 'fetch_translation_job_for_editor' ], 10, 2 );
		add_action( 'admin_print_scripts', [ $this, 'preselect_product_type_in_admin_screen' ], 11 );

		add_filter( 'icl_post_alternative_languages', [ $this, 'hide_post_translation_links' ] );

		if ( getSetting( 'set_up_wizard_run' ) ) {
			add_filter( 'manage_product_posts_columns', [ $this, 'add_languages_column' ], 100 );
		}
		add_action( 'woocommerce_product_after_variable_attributes', [ $this, 'lock_variable_fields' ], 10 );

		add_filter( 'wpml_use_tm_editor', [ $this, 'useWpmlEditorForProducts' ], self::EDITOR_ANSWER_PRIORITY, 2 );

		\WCML\Request\Ajax::register( 'wcml_editor_auto_slug', [ 'capability' => [ 'translate', 'manage_translations' ], 'nonce' => [ 'wcml_editor_auto_slug', 'wcml_nonce' ] ], [ $this, 'auto_generate_slug' ] );

		add_filter( 'wpml_tm_show_page_builders_translation_editor_warning', [ $this, 'show_page_builders_translation_editor_warning' ], 10, 2 );
	}

	public function fetch_translation_job_for_editor( $job, $job_details ) {

		if ( 'post_product' === $job_details['job_type'] ) {
			$job = new WCML_Editor_UI_Product_Job( $job_details, $this->woocommerce_wpml, $this->sitepress, $this->wpdb );
		}

		return $job;
	}

	public function preselect_product_type_in_admin_screen() {
		global $pagenow;

		if ( 'post-new.php' === $pagenow && isset( $_GET['post_type'], $_GET['trid'] ) && $_GET['post_type'] === 'product' ) {
			$translations = $this->sitepress->get_element_translations( (int) $_GET['trid'], 'post_product_type' );
			foreach ( $translations as $translation ) {
				if ( $translation->original ) {
					$source_lang = $translation->language_code;
					break;
				}
			}

			if ( isset( $source_lang ) ) {
				$terms = get_the_terms( $translations[ $source_lang ]->element_id, 'product_type' );
				echo '<script type="text/javascript">';
				echo PHP_EOL . '// <![CDATA[' . PHP_EOL;
				echo 'addLoadEvent(function(){' . PHP_EOL;
				echo "jQuery('#product-type option').prop('selected', false);" . PHP_EOL;
				echo "jQuery('#product-type option[value=\"" . esc_js( $terms[0]->slug ) . "\"]').prop('selected', true);" . PHP_EOL;
				echo '});' . PHP_EOL;
				echo PHP_EOL . '// ]]>' . PHP_EOL;
				echo '</script>';
			}
		}
	}

	public function hide_post_translation_links( $output ) {
		global $post;

		if ( null === $post ) {
			return $output;
		}

		$post_type        = get_post_type( $post->ID );
		$checkout_page_id = wc_get_page_id( 'checkout' );

		if ( $post_type === 'product' || is_page( $checkout_page_id ) ) {
			$output = '';
		}

		return $output;
	}

	public function create_product_translation_package( $product_id, $trid, $language, $status ) {
		global $iclTranslationManagement;
		$translation_id = $this->wpdb->get_var(
			$this->wpdb->prepare(
				"
                                SELECT translation_id FROM {$this->wpdb->prefix}icl_translations WHERE trid=%d AND language_code='%s'
                            ",
				$trid,
				$language
			)
		);

		$md5 = $iclTranslationManagement->post_md5( get_post( $product_id ) );

		if ( $translation_id && $this->isTranslationPackageUpToDate( (int) $translation_id, (int) $status, $md5 ) ) {
			return;
		}

		wpml_tm_ensure_local_translation_job(
			get_post( $product_id ),
			(int) $translation_id,
			(int) $status,
			[ 'md5' => $md5 ]
		);

	}

	private function isTranslationPackageUpToDate( $translationId, $status, $md5 ) {
		$records      = make( \WPML_TM_Records::class );
		$statusRecord = $records->icl_translation_status_by_translation_id( $translationId );

		if ( $statusRecord->md5() !== $md5 || (int) $statusRecord->status() !== $status ) {
			return false;
		}

		$translatedPostId = $records->icl_translations_by_translation_id( $translationId )->element_id();

		return 0 === (int) wpml_get_post_status_helper()->needs_update( $translatedPostId );
	}

	public function add_languages_column( $columns ) {

		if ( array_key_exists( 'icl_translations', $columns ) ) {
			return $columns;
		}
		$active_languages = $this->sitepress->get_active_languages();

		if ( count( $active_languages ) <= 1 || get_query_var( 'post_status' ) === 'trash' ) {
			return $columns;
		}
		$languages = [];

		foreach ( $active_languages as $v ) {
			if ( $v['code'] === $this->sitepress->get_current_language() ) {
				continue;
			}
			$languages[] = $v['code'];
		}
		$res = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT f.lang_code, f.flag, f.from_template, l.name
					FROM {$this->wpdb->prefix}icl_flags f
					JOIN {$this->wpdb->prefix}icl_languages_translations l ON f.lang_code = l.language_code
					WHERE l.display_language_code = %s AND f.lang_code IN (" . DB::prepareIn( $languages ) . ')',
				$this->sitepress->get_admin_language()
			)
		);

		foreach ( $res as $r ) {
			if ( $r->from_template ) {
				$wp_upload_dir = wp_upload_dir();
				$flag_path     = $wp_upload_dir['baseurl'] . '/flags/';
			} else {
				$flag_path = ICL_PLUGIN_URL . '/res/flags/';
			}
			$flags[ $r->lang_code ] = '<img src="' . $flag_path . $r->flag . '" width="18" height="12" alt="' . $r->name . '" title="' . $r->name . '" />';
		}

		$flags_column = '';
		foreach ( $active_languages as $v ) {
			if ( isset( $flags[ $v['code'] ] ) ) {
				$flags_column .= $flags[ $v['code'] ];
			}
		}

		$new_columns = [];
		$added       = false;
		foreach ( $columns as $k => $v ) {
			$new_columns[ $k ] = $v;
			if ( $k === 'name' ) {
				$new_columns['icl_translations'] = $flags_column;
				$added                           = true;
			}
		}

		if ( ! $added ) {
			$new_columns['icl_translations'] = $flags_column;
		}

		return $new_columns;
	}

	public function lock_variable_fields( $loop ) {
		$product_id = false;

		if ( isset( $_GET['post'] ) && 'product' === get_post_type( $_GET['post'] ) ) {
			$product_id = $_GET['post'];
		} elseif ( isset( $_POST['action'], $_POST['product_id'] ) && 'woocommerce_load_variations' === $_POST['action'] ) {
			$product_id = $_POST['product_id'];
		}

		if ( ! $product_id ) {
			return;
		} elseif (
				! $this->woocommerce_wpml->products->is_original_product( $product_id ) &&
				'auto-draft' !== get_post_status( $product_id )
		) {
			$args = [
				'post_type'      => 'product_variation',
				'post_status'    => [ 'private', 'publish' ],
				'posts_per_page' => ! empty( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 10,
				'paged'          => ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1,
				'orderby'        => [
					'menu_order' => 'ASC',
					'ID'         => 'DESC',
				],
				'post_parent'    => $product_id,
			];

			$variations     = get_posts( $args );
			$file_path_sync = [];

			if ( $variations ) {
				foreach ( $variations as $variation ) {
					$variation_id        = absint( $variation->ID );
					$original_id         = $this->woocommerce_wpml->products->get_original_product_id( $variation_id );

					$file_path_sync[ $variation_id ] = WCML_Downloadable_Products::isDownloadableFilesSetToUseSame( $original_id );
				}
			}

			?>
			<script type="text/javascript">
				jQuery(function() {
					wcml_lock_variation_fields( <?php echo json_encode( $file_path_sync ); ?> );
				});
			</script>
			<?php
		}
	}

	public function useWpmlEditorForProducts( $use_tm_editor, $post_id = null ) {
		if ( ! $post_id || 'product' !== get_post_type( $post_id ) ) {
			return $use_tm_editor;
		}

		if ( ! $this->productsAreSetToTheWpmlEditor() || $this->nativeEditorWasChosenSiteWide() ) {
			return $use_tm_editor;
		}

		return $this->answerCameFromTheLegacyFallThrough( $post_id ) ? true : $use_tm_editor;
	}

	private function productsAreSetToTheWpmlEditor() {
		$per_post_type = RequestSettings::tmSubSetting( WPML_TM_Post_Edit_TM_Editor_Mode::TM_KEY_FOR_POST_TYPE_USE_NATIVE, [] );

		return false === ( $per_post_type['product'] ?? null );
	}

	private function nativeEditorWasChosenSiteWide() {
		return WPML_TM_Post_Edit_TM_Editor_Mode::EDITOR_NATIVE
			=== RequestSettings::tmSubSetting( WPML_TM_Post_Edit_TM_Editor_Mode::TM_KEY_GLOBAL_EDITOR, null );
	}

	private function answerCameFromTheLegacyFallThrough( $post_id ) {
		list( , $scope ) = WPML_TM_Post_Edit_TM_Editor_Mode::get_translation_editor_with_scope( null, $post_id );

		return SetEditorMode::MODE_FOR_GLOBAL === $scope;
	}

	public function auto_generate_slug() {
		global $wpdb;

		if ( ! check_ajax_referer( self::AUTO_SLUG_NONCE, 'wcml_nonce', false ) ) {
			wp_send_json_error( 'Invalid nonce' );
		}

		$job_id = isset( $_POST['job_id'] ) ? (int) $_POST['job_id'] : 0;
		$authorized = false;
		if ( class_exists( '\WPML\TM\Jobs\Authorization\AuthorizedJobResolver' ) ) {
			$authorized = (bool) \WPML\TM\Jobs\Authorization\AuthorizedJobResolver::make()->byLocalId(
				\WPML\Core\Security\ExecutionContext\ExecutionContextHolder::current(),
				$job_id,
				\WPML\TM\Jobs\Authorization\AuthorizedJobResolver::ACCESS_TRANSLATE,
				'post'
			);
		} elseif ( class_exists( '\WPML\TM\Jobs\Authorization\JobAuthorization' ) ) {
			$authorized = \WPML\TM\Jobs\Authorization\JobAuthorization::currentUserCanTranslateJob( $job_id, 'post' );
		} elseif ( function_exists( 'wpml_tm_load_job_factory' ) && function_exists( 'wp_get_current_user' ) ) {
			$job = $job_id ? wpml_tm_load_job_factory()->get_translation_job_as_active_record( $job_id ) : false;
			$authorized = $job && $job->user_can_translate( wp_get_current_user() );
		}
		if ( ! $authorized ) {
			wp_send_json_error( 'Unauthorized job' );
		}

		$title = filter_input( INPUT_POST, 'title' );

		$post_name = urldecode( sanitize_title( $title ) );

		$post_id_sql = "
            SELECT t.element_id
            FROM {$wpdb->prefix}icl_translations t
                LEFT JOIN {$wpdb->prefix}icl_translation_status s ON t.translation_id = s.translation_id
                LEFT JOIN {$wpdb->prefix}icl_translate_job j ON s.rid = j.rid
            WHERE j.job_id = %d
        ";
		$post_id     = $wpdb->get_var( $wpdb->prepare( $post_id_sql, $job_id ) );

		if ( $post_id ) {
			$slug = wp_unique_post_slug( $post_name, $post_id, 'publish', 'product', 0 );
		} else {
			$lang_sql = "
                SELECT t.language_code
                FROM {$wpdb->prefix}icl_translations t
                    LEFT JOIN {$wpdb->prefix}icl_translation_status s ON t.translation_id = s.translation_id
                    LEFT JOIN {$wpdb->prefix}icl_translate_job j ON s.rid = j.rid
                WHERE j.job_id = %d
            ";
			$lang     = $wpdb->get_var( $wpdb->prepare( $lang_sql, $job_id ) );

			$check_sql       = "
                SELECT p.post_name 
                FROM {$wpdb->posts} p 
                    LEFT JOIN {$wpdb->prefix}icl_translations t 
                        ON t.element_id = p.ID AND t.element_type='post_product'   
                WHERE p.post_name = %s AND t.language_code = %s LIMIT 1
            ";
			$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $post_name, $lang ) );

			if ( $post_name_check ) {
				$suffix = 2;
				do {

					$alt_post_name   = _truncate_post_slug( $post_name, 200 - ( strlen( (string) $suffix ) + 1 ) ) . "-$suffix";
					$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_post_name, $lang ) );
					$suffix++;

				} while ( $post_name_check );
				$slug = $alt_post_name;
			} else {
				$slug = $post_name;
			}
		}

		wp_send_json( [ 'slug' => $slug ] );

	}

	public function show_page_builders_translation_editor_warning( $display, $post_id ) {

		if ( 'product' === get_post_type( $post_id ) ) {
			$display = false;
		}

		return $display;
	}

}
