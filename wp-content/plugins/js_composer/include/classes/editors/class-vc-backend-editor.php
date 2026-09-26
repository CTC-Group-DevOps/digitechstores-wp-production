<?php
/**
 * WPBakery Page Builder admin editor
 *
 * @package WPBakeryPageBuilder
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Base functionality for VC editors
 *
 * @package WPBakeryPageBuilder
 * @since 7.4
 */
require_once vc_path_dir( 'EDITORS_DIR', 'class-vc-editor.php' );

/**
 * VC backend editor.
 *
 * This editor is available on default Wp post/page admin edit page. ON admin_init callback adds meta box to
 * edit page.
 *
 * @since 4.2
 */
class Vc_Backend_Editor extends Vc_Editor {

	/**
	 * Stores data about the current post.
	 *
	 * @var mixed
	 */
	public $post = false;

	/**
	 * This method is called by Vc_Manager to register required action hooks for VC backend editor.
	 *
	 * @since  4.2
	 * @access public
	 */
	public function addHooksSettings() {
		if ( ! vc_user_access()->part( 'backend_editor' )->can()->get() ) {
			return;
		}

		// load backend editor.
		if ( function_exists( 'add_theme_support' ) ) {
			add_theme_support( 'post-thumbnails' ); // @todo check is it needed?
		}
		add_action( 'add_meta_boxes', [
			$this,
			'render',
		], 5 );
		add_action(
			'current_screen',
			[
				$this,
				'prevent_meta_box_order_in_custom_context',
			]
		);
		add_action( 'admin_print_scripts-post.php', [
			$this,
			'registerScripts',
		] );
		add_action( 'admin_print_scripts-post-new.php', [
			$this,
			'registerScripts',
		] );
		add_action( 'admin_print_scripts-post.php', [
			$this,
			'printScriptsMessages',
		] );
		add_action( 'admin_print_scripts-post-new.php', [
			$this,
			'printScriptsMessages',
		] );

		add_action( 'wp_ajax_wpb_backend_editor_params', [
			$this,
			'process_params',
		] );

		add_action( 'wp_ajax_wpb_single_image_data', [
			$this,
			'process_single_image_data',
		] );

		add_action( 'wp_ajax_wpb_get_gallery_html', [
			$this,
			'process_gallery_html',
		] );

		add_action( 'wp_ajax_wpb_load_backend_editor_shortcode', [
			$this,
			'load_shortcode_css',
		] );
	}

	/**
	 * Registers required JavaScript and CSS files.
	 */
	public function registerScripts() {
		$this->registerBackendJavascript();
		$this->registerBackendCss();
		// B.C.
		wpbakery()->registerAdminCss();
		wpbakery()->registerAdminJavascript();
	}

	/**
	 * Renders the meta box for the backend editor.
	 *
	 * @param string $post_type
	 * @throws \Exception
	 * @since  4.2
	 * @access public
	 */
	public function render( $post_type ) {
		if ( $this->isValidPostType( $post_type ) ) {
			// meta box to render.
			add_meta_box( 'wpb_wpbakery', esc_html__( 'WPBakery Page Builder', 'js_composer' ), [
				$this,
				'renderEditor',
			], $post_type, 'normal', 'high' );
		}
	}

	/**
	 * We allow ordering our meta box only in default WordPress contexts.
	 *
	 * @since 9.0
	 */
	public function prevent_meta_box_order_in_custom_context() {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		$screen = $screen->id;
		if ( ! $this->isValidPostType( $screen ) ) {
			return;
		}

		add_filter( 'get_user_option_meta-box-order_' . $screen, [ $this, 'update_user_meta_box_order' ] );
	}

	/**
	 * Move WPBakery meta box to 'normal' context if it is in custom context.
	 *
	 * @since 9.0
	 * @param mixed $order
	 * @return mixed
	 */
	public function update_user_meta_box_order( $order ) {
		if ( ! is_array( $order ) ) {
			return $order;
		}

		$allowed_context_list = [ 'normal', 'advanced', 'side' ];

		foreach ( $order as $context_name => $order_data ) {
			if ( in_array( $context_name, $allowed_context_list ) ) {
				continue;
			}

			$order_data = explode( ',', $order_data );
			$is_wpb_meta_box_in_custom_context = in_array( 'wpb_wpbakery', $order_data, true );
			if ( ! $is_wpb_meta_box_in_custom_context ) {
				continue;
			}

			$order_data_wpb_key = array_search( 'wpb_wpbakery', $order_data, true );
			unset( $order_data[ $order_data_wpb_key ] );

			if ( ! isset( $order['normal'] ) ) {
				continue;
			}

			$order[ $context_name ] = implode( ',', $order_data );
			$order['normal'] .= ',wpb_wpbakery';
		}

		return $order;
	}

	/**
	 * Output html for backend editor meta box.
	 *
	 * @param null|Wp_Post $post
	 *
	 * @return bool
	 */
	public function renderEditor( $post = null ) {
		/**
		 * TODO: setter/getter for $post
		 */
		if ( ! is_object( $post ) || 'WP_Post' !== get_class( $post ) || ! isset( $post->ID ) ) {
			return false;
		}
		$this->post = $post;
		$this->set_post_meta( $post );

		vc_include_template( 'editors/backend_editor.tpl.php', [
			'editor' => $this,
			'post' => $this->post,
			'wpb_vc_status' => $this->getEditorPostStatus(),
			'wpb_vc_editor_type' => $this->get_editor_post_type(),
		] );
		add_action( 'admin_footer', [
			$this,
			'renderEditorFooter',
		] );
		do_action( 'vc_backend_editor_render' );

		return true;
	}

	/**
	 * Check if current post is edited lastly by our editor.
	 *
	 * @since 7.8
	 * @return mixed
	 */
	public function getEditorPostStatus() {
		$post_editor_status = wpb_get_post_editor_status( $this->post->ID );
		$get_param_status = vc_get_param( 'wpb_vc_js_status', $post_editor_status );
		$wpb_vc_status = apply_filters( 'wpb_vc_js_status_filter', $get_param_status );

		if ( '' === $wpb_vc_status || ! isset( $wpb_vc_status ) ) {
			$wpb_vc_status = vc_user_access()->part( 'backend_editor' )->checkState( 'default' )->get() ? 'true' : 'false';
		}

		return $wpb_vc_status;
	}

	/**
	 * Get the current post ID from various sources.
	 *
	 * @since 9.0
	 * @return int|null The post ID or null if not found.
	 */
	protected function get_post_id() {
		global $post, $pagenow;

		if ( $this->post && isset( $this->post->ID ) ) {
			return $this->post->ID;
		}

		if ( $post && isset( $post->ID ) ) {
			return $post->ID;
		}

		$post_id = get_the_ID();
		if ( $post_id ) {
			return $post_id;
		}

		if ( is_admin() && in_array( $pagenow, [ 'post.php', 'post-new.php' ], true ) ) {
			$get_post_id = filter_input( INPUT_GET, 'post', FILTER_VALIDATE_INT );
			if ( $get_post_id ) {
				return $get_post_id;
			}

			$post_post_id = filter_input( INPUT_POST, 'post_ID', FILTER_VALIDATE_INT );
			if ( $post_post_id ) {
				return $post_post_id;
			}
		}

		return null;
	}

	/**
	 * Get backend editor type (classic or backend).
	 *
	 * @since 8.5
	 * @param int|null $post_id
	 * @return string
	 */
	public function get_editor_post_type( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return '';
		}

		$editor_type = get_post_meta( $post_id, '_wpb_vc_editor_type', true );

		return $editor_type ? $editor_type : 'backend';
	}


	/**
	 * Output required html and js content for VC editor.
	 *
	 * Here comes panels, modals and js objects with data for mapped shortcodes.
	 */
	public function renderEditorFooter() {
		if ( vc_is_gutenberg_editor() ) {
			return;
		}
		vc_include_template( 'editors/partials/backend_editor_footer.tpl.php', [
			'editor' => $this,
			'post' => $this->post,
		] );
		do_action( 'vc_backend_editor_footer_render' );
	}

	/**
	 * Check is post type is valid for rendering VC backend editor.
	 *
	 * @param string $type
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function isValidPostType( $type = '' ) {
		$type = ! empty( $type ) ? $type : get_post_type();
		if ( 'vc_grid_item' === $type ) {
			return false;
		}

		return apply_filters( 'vc_is_valid_post_type_be', vc_check_post_type( $type ), $type );
	}

	/**
	 * Enqueue required javascript libraries and css files.
	 *
	 * This method also setups reminder about license activation.
	 *
	 * @since  4.2
	 * @access public
	 */
	public function printScriptsMessages() {
		if ( ! vc_is_frontend_editor() && $this->isValidPostType( get_post_type() ) ) {
			$this->enqueueEditorScripts();
		}
	}

	/**
	 * Enqueue required javascript libraries and css files.
	 *
	 * @since  4.8
	 * @access public
	 */
	public function enqueueEditorScripts() {
		if ( $this->editorEnabled() ) {
			$this->enqueueJs();
			$this->enqueueCss();
			WPBakeryShortCodeFishBones::enqueueCss();
			WPBakeryShortCodeFishBones::enqueueJs();
		} else {
			wp_enqueue_script( 'vc-backend-actions-js' );
			$this->enqueueCss(); // needed for navbar @todo split.
		}
		do_action( 'vc_backend_editor_enqueue_js_css' );
	}

	/**
	 * Registers JavaScript files needed for the backend editor.
	 */
	public function registerBackendJavascript() {
		// editor can be disabled but fe can be enabled. so we currently need this file. @todo maybe make backend-disabled.min.js.
		wp_register_script( 'vc-backend-actions-js', vc_asset_url( 'js/dist/backend-actions.min.js' ), [
			'jquery-core',
			'backbone',
			'underscore',
		], WPB_VC_VERSION, true );
		// used in tta shortcodes, and panels.
		wp_register_script( 'vc_accordion_script', vc_asset_url( 'lib/vc/vc_accordion/vc-accordion.min.js' ), [ 'jquery-core' ], WPB_VC_VERSION, true );
		wp_register_script( 'vc-image-drop', vc_asset_url( 'js/dist/image-drop.min.js' ), [ 'jquery-core' ], WPB_VC_VERSION, true );
		wp_register_script( 'vc-backend-min-js', vc_asset_url( 'js/dist/backend.min.js' ), [
			'vc-backend-actions-js',
			'vc_accordion_script',
			'wp-color-picker',
		], WPB_VC_VERSION, true );
		wp_register_script( 'wpb_php_js', vc_asset_url( 'lib/vendor/php.default/php.default.min.js' ), [ 'jquery-core' ], WPB_VC_VERSION, true );
		// used as polyfill for JSON.stringify and etc.
		wp_register_script( 'wpb_json-js', vc_asset_url( 'lib/vendor/dist/json-js/json2.min.js' ), [], WPB_VC_VERSION, true );
		// used in post settings editor.
		wp_register_script( 'ace-editor', vc_asset_url( 'lib/vendor/dist/ace-builds/src-min-noconflict/ace.min.js' ), [ 'jquery-core' ], WPB_VC_VERSION, true );
		wp_register_script( 'wpb-code-editor', vc_asset_url( 'js/dist/post-code-editor.min.js' ), [ 'jquery-core' ], WPB_VC_VERSION, true );
		wp_register_script( 'webfont', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', [], WPB_VC_VERSION, true ); // Google Web Font CDN.
		wp_register_script( 'wpb-popper', vc_asset_url( 'lib/vendor/dist/@popperjs/core/dist/umd/popper.min.js' ), [], WPB_VC_VERSION, true );
		wp_register_script( 'pickr', vc_asset_url( 'lib/vendor/dist/@simonwep/pickr/dist/pickr.es5.min.js' ), [], WPB_VC_VERSION, true );
		wp_register_script( 'wpb-select2', vc_asset_url( 'lib/vc/wpb-select2/select2.min.js' ), [ 'jquery-core' ], WPB_VC_VERSION, true );
		// Conditionally register mousetrap based on shortcuts setting.
		$shortcuts_disabled = get_option( 'wpb_js_shortcuts', false );
		if ( ! $shortcuts_disabled ) {
			wp_register_script( 'mousetrap', vc_asset_url( 'lib/vendor/dist/mousetrap/mousetrap.min.js' ), [], WPB_VC_VERSION, true );
		}
		wp_register_script( 'wpb-dompurify', vc_asset_url( 'lib/vendor/dist/dompurify/dist/purify.min.js' ), [], WPB_VC_VERSION, true );
		vc_modules_manager()->register_modules_script();

		wp_register_script( 'vc-backend-css-loader', vc_asset_url( 'js/dist/backend-css-loader.min.js' ), [ 'vc-backend-actions-js' ], WPB_VC_VERSION, true );

		wp_localize_script( 'vc-backend-actions-js', 'i18nLocale', wpbakery()->getEditorsLocale() );
		wp_localize_script( 'vc-backend-actions-js', 'wpbData', wpbakery()->getEditorsWpbData() );

		do_action( 'wpb_after_register_backend_editor_js', $this );
	}

	/**
	 * Registers CSS files needed for the backend editor.
	 */
	public function registerBackendCss() {
		wp_register_style( 'js_composer', Vc_Css_Manager::get_backend_editor_css_file(), [], WPB_VC_VERSION, false );
		wp_register_style( 'wpb_modules_css', vc_asset_url( 'css/modules.min.css' ), [], WPB_VC_VERSION, false );

		if ( $this->editorEnabled() ) {
			/**
			 * Used for accordions/tabs/tours.
			 *
			 * @deprecated
			 */
			wp_register_style( 'ui-custom-theme', vc_asset_url( 'css/jquery-ui-less.custom.min.css' ), [], WPB_VC_VERSION );

			/**
			 * Also used in vc_icon shortcode.
			 *
			 * @todo check vc_add-element-deprecated-warning for fa icon usage ( set to our font )
			 */
			wp_register_style( 'vc_font_awesome_5_shims', vc_asset_url( 'lib/vendor/dist/@fortawesome/fontawesome-free/css/v4-shims.min.css' ), [], WPB_VC_VERSION );
			wp_register_style( 'vc_font_awesome_6', vc_asset_url( 'lib/vendor/dist/@fortawesome/fontawesome-free/css/all.min.css' ), [ 'vc_font_awesome_5_shims' ], WPB_VC_VERSION );
			/**
			 * Definitely used in edit form param: css_animation, but currently vc_add_shortcode_param doesn't accept css.
			 *
			 * @todo check for usages
			 */
			wp_register_style( 'vc_animate-css', vc_asset_url( 'lib/vendor/dist/animate.css/animate.min.css' ), [], WPB_VC_VERSION );
			wp_register_style( 'pickr', vc_asset_url( 'lib/vendor/dist/@simonwep/pickr/dist/themes/classic.min.css' ), [], WPB_VC_VERSION, false );
			wp_register_style( 'wpb-select2', vc_asset_url( 'lib/vc/wpb-select2/select2.min.css' ), [], WPB_VC_VERSION, false );
			// When version is added, we can't use multiple fonts, it only loads the last font from the url.
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			wp_register_style( 'vc_google_fonts', 'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,500;1,14..32,500&family=Open+Sans:ital,wght@1,600&family=Roboto:wght@400;700&family=Roboto:ital,wght@1,500&family=Sora:wght@500;600&display=swap&ver=' . WPB_VC_VERSION, [], null );

		}

		do_action( 'wpb_after_register_backend_editor_css', $this );
	}

	/**
	 * Enqueues JavaScript files for the backend editor.
	 */
	public function enqueueJs() {
		$wp_dependencies = [
			'jquery-core',
			'underscore',
			'backbone',
			'media-views',
			'media-editor',
			'wp-pointer',
			'mce-view',
			'wp-color-picker',
			'jquery-ui-sortable',
			'jquery-ui-droppable',
			'jquery-ui-draggable',
			'jquery-ui-autocomplete',
			'jquery-ui-resizable',
			'wpb-select2',
			// used in @deprecated tabs.
			'jquery-ui-tabs',
			'jquery-ui-accordion',
		];
		$dependencies = [
			'vc_accordion_script',
			'wpb-dompurify',
			'wpb_php_js',
			// used in our files [e.g. edit form saving sprintf].
			'wpb_json-js',
			'webfont',
			'wpb-popper',
			'vc-backend-min-js',
			'wpb-modules-js',
			'pickr',
			'ace-editor',
		];

		// Enqueue image drop script only if it is allowed via Role Manager.
		if (
			vc_user_access()->part( 'shortcodes' )->getState() === true ||
			vc_user_access()->part( 'shortcodes' )->can( 'vc_single_image_all' )->get() === true
		) {
			$dependencies[] = 'vc-image-drop';
		}

		// Conditionally add mousetrap when shortcuts are enabled.
		if ( Vc_Settings::areShortcutsEnabled() ) {
			$dependencies[] = 'mousetrap';
		}

		$post_id = $this->get_post_id();
		if ( Vc_Css_Manager::should_load_optimized_css( $post_id ) ) {
			$dependencies[] = 'vc-backend-css-loader';
		}

		$common = apply_filters( 'wpb_enqueue_backend_editor_js', array_merge( $wp_dependencies, $dependencies ) );

		// This workaround will allow to disable any of dependency on-the-fly.
		foreach ( $common as $dependency ) {
			wp_enqueue_script( $dependency );
		}
	}

	/**
	 * Enqueues CSS files for the backend editor.
	 */
	public function enqueueCss() {
		$wp_dependencies = [
			'wp-color-picker',
			'farbtastic',
			// deprecated for tabs/accordion.
			'ui-custom-theme',
			// used in deprecated message and also in vc-icon shortcode.
			'vc_font_awesome_6',
			// used in css_animation edit form param.
			'vc_animate-css',
			// used in tag_input edit form param.
			'wpb-select2',
		];
		$dependencies = [
			'js_composer',
			'wpb_modules_css',
			'pickr',
			'vc_google_fonts',
		];

		$common = apply_filters( 'wpb_enqueue_backend_editor_css', array_merge( $wp_dependencies, $dependencies ) );

		// This workaround will allow to disable any of dependency on-the-fly.
		foreach ( $common as $dependency ) {
			wp_enqueue_style( $dependency );
		}
	}

	/**
	 * Checks if the backend editor is enabled.
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function editorEnabled() {
		return vc_user_access()->part( 'backend_editor' )->can()->get();
	}

	/**
	 * Process params.
	 * As we parse back editor content with our shortcode in js side
	 * We need additional ajax request to get some specific params options that are not available in editor content.
	 *
	 * @since 8.3
	 */
	public function process_params() {
		vc_user_access()->checkAdminNonce()->validateDie();

		$elements = vc_post_param( 'elements', [] );
		$response_data = [];

		foreach ( $elements as $element_id => $element_data ) {
			if ( empty( $element_data['action'] ) || ! method_exists( $this, $element_data['action'] ) ) {
				continue;
			}

			$response_data[ $element_id ] = $this->{$element_data['action']}( $element_data );
			$response_data[ $element_id ]['action'] = $element_data['action'];
			if ( ! empty( $element_data['paramName'] ) ) {
				$response_data[ $element_id ]['paramName'] = $element_data['paramName'];
			}
		}

		wp_send_json_success( $response_data );
	}

	/**
	 * Parse a raw image field value into one or more attachment IDs.
	 *
	 * @since 9.0
	 * @param string $value    Raw value — either JSON-encoded array or a plain ID / comma-separated IDs.
	 * @param bool   $multiple When true returns all IDs as an array; when false returns the first ID only.
	 *
	 * @return array|int|string
	 */
	private function parse_image_value( $value, $multiple = false ) {
		$decoded = json_decode( stripslashes( $value ), true );
		if ( is_array( $decoded ) ) {
			return $multiple ? array_keys( $decoded ) : array_key_first( $decoded );
		}
		return $multiple ? explode( ',', $value ) : $value;
	}

	/**
	 * Get attach_image param element data.
	 * We use it to get 'Single Image' element data when editing elements.
	 *
	 * @since 8.3
	 */
	public function process_single_image_data() {
		vc_user_access()->checkAdminNonce()->validateDie();

		$params = vc_post_param( 'params' );
		$image_id = $this->parse_image_value( $params['image'] );
		$post_id = (int) vc_post_param( 'postId' );

		$source = empty( $params['source'] ) ? 'media_library' : $params['source'];

		$image_data = wpb_get_image_data_by_source( $source, $post_id, $image_id, null, $params );
		wp_send_json_success( $image_data );
	}

	/**
	 * Get 'Single Image' element data.
	 * We use it to get 'Single Image' attach_image param thumbnail data
	 * when process ajax request common for all elements in backend editor.
	 *
	 * @param array $element_data
	 *
	 * @return array
	 * @see $this->process_params()
	 * @since 8.3
	 */
	public function get_attach_image( $element_data ) {
		if ( ! isset( $element_data['source'], $element_data['value'], $element_data['postId'] ) ) {
			return [];
		}

		$image_id = $this->parse_image_value( $element_data['value'] );

		return wpb_get_image_data_by_source(
			$element_data['source'],
			$element_data['postId'],
			$image_id,
			null
		);
	}

	/**
	 * Get attach_images element param data.
	 * We use it to get gallery elements data when editing elements.
	 *
	 * @since 8.3
	 */
	public function process_gallery_html() {
		vc_user_access()->checkAdminNonce()->validateDie();

		$content = vc_post_param( 'content' );
		$images = $this->parse_image_value( $content, true );

		if ( empty( $images ) ) {
			wp_send_json_error();
		}

		wp_send_json_success( vc_field_attached_images( $images ) );
	}

	/**
	 * Get gallery elements data.
	 * We use it to get gallery elements attach_images param thumbnails data
	 * when process ajax request common for all elements in backend editor.
	 *
	 * @param array $element_data
	 *
	 * @return array
	 * @see $this->process_params()
	 * @since 8.3
	 */
	public function get_gallery_images( $element_data ) {
		if ( ! isset( $element_data['source'], $element_data['value'] ) ) {
			return [];
		}

		$images = $this->parse_image_value( $element_data['value'], true );

		return [ 'html' => vc_field_attached_images( $images ) ];
	}

	/**
	 * Load CSS for specific shortcodes via AJAX.
	 *
	 * @since 9.0
	 */
	public function load_shortcode_css() {
		vc_user_access()->checkAdminNonce()->validateDie();

		$shortcode_tags = vc_post_param( 'shortcodes', [] );
		$post_id = vc_post_param( 'post_id', null );

		if ( empty( $shortcode_tags ) ) {
			wp_send_json_error( 'No shortcodes specified' );
		}

		if ( ! Vc_Css_Manager::should_load_optimized_css( $post_id ) ) {
			wp_send_json_success( [ 'css' => [] ] );
		}

		$css_files = [];
		$processed_files = [];

		foreach ( $shortcode_tags as $tag ) {
			$this->process_shortcode_tag( $tag, $css_files, $processed_files );
		}

		wp_send_json_success( [ 'css' => $css_files ] );
	}

	/**
	 * Process a single shortcode tag and add its CSS files.
	 *
	 * @since 9.0
	 * @param string $tag Shortcode tag.
	 * @param array  $css_files Reference to CSS files array.
	 * @param array  $processed_files Reference to processed files array.
	 */
	protected function process_shortcode_tag( $tag, &$css_files, &$processed_files ) {
		$shortcode_obj = wpbakery()->getShortCode( $tag );

		if ( ! $shortcode_obj ) {
			$this->add_css_file_for_tag( $tag, $css_files, $processed_files );
			return;
		}

		$css_file_names = $this->get_css_file_names_from_shortcode( $shortcode_obj, $tag );

		foreach ( $css_file_names as $css_file_name ) {
			$this->add_css_file_for_tag( $css_file_name, $css_files, $processed_files );
		}
	}

	/**
	 * Get CSS file names from shortcode object.
	 *
	 * @since 9.0
	 * @param object $shortcode_obj Shortcode object.
	 * @param string $tag Shortcode tag.
	 * @return array Array of CSS file names.
	 */
	protected function get_css_file_names_from_shortcode( $shortcode_obj, $tag ) {
		$shortcode_class = $shortcode_obj->shortcodeClass();

		if ( ! $shortcode_class ) {
			return [ $tag ];
		}

		if ( ! method_exists( $shortcode_class, 'get_shortcode_css_files' ) ) {
			return [ $tag ];
		}

		$css_file_names = $shortcode_class->get_shortcode_css_files();

		return is_array( $css_file_names ) ? $css_file_names : [ $css_file_names ];
	}

	/**
	 * Add CSS file for a tag if it exists and hasn't been processed.
	 *
	 * @since 9.0
	 * @param string $tag Tag name.
	 * @param array  $css_files Reference to CSS files array.
	 * @param array  $processed_files Reference to processed files array.
	 */
	protected function add_css_file_for_tag( $tag, &$css_files, &$processed_files ) {
		if ( isset( $processed_files[ $tag ] ) ) {
			return;
		}

		$css_file = 'css/shortcodes/backend/' . $tag . '.min.css';
		$css_path = vc_path_dir( 'ASSETS_DIR', $css_file );

		if ( ! file_exists( $css_path ) ) {
			return;
		}

		$css_files[] = [
			'tag' => $tag,
			'url' => vc_asset_url( $css_file ) . '?ver=' . WPB_VC_VERSION,
			'handle' => $tag . '_style',
		];
		$processed_files[ $tag ] = true;
	}
}
