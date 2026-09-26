<?php
/**
 * Renders navigation bar for Editors.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Navbar
 */
class Vc_Navbar {
	/**
	 *  List of controls to be displayed in the navigation bar.
	 *
	 * @var array
	 */
	protected $controls = [
		'add_element',
		'templates',
		'save_backend',
		'frontend',
		'post_settings',
		'custom_code',
		'fullscreen',
		'windowed',
		'more',
		'undo',
		'redo',
	];

	/**
	 * List of controls to be displayed in the "More" dropdown menu.
	 *
	 * @since 9.0
	 * @var array
	 */
	public $more_button_controls = [
		'undo',
		'redo',
		'post_settings',
		'custom_code',
		'save_backend_mobile',
	];

	/**
	 * URL for the brand logo.
	 *
	 * @var string
	 */
	protected $brand_url = 'https://wpbakery.com?utm_source=wpb-plugin&utm_medium=backend-editor&utm_campaign=info&utm_content=logo';
	/**
	 * CSS class for the navigation bar.
	 *
	 * @var string
	 */
	protected $css_class = 'vc_navbar';
	/**
	 * Filter name for the controls.
	 *
	 * @var string
	 */
	protected $controls_filter_name = 'vc_nav_controls';
	/**
	 * The current post object.
	 *
	 * @var bool|WP_Post
	 */
	protected $post = false;

	/**
	 * Vc_Navbar constructor.
	 *
	 * @param WP_Post $post
	 */
	public function __construct( WP_Post $post ) {
		$this->post = $post;
	}

	/**
	 * Generate array of controls by iterating property $controls list.
	 * vc_filter: vc_nav_controls - hook to override list of controls
	 *
	 * @return array - list of arrays witch contains key name and html output for button.
	 */
	public function getControls() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$control_output_list = $this->get_control_output_list( $this->getControlList() );

		return apply_filters( $this->controls_filter_name, $control_output_list );
	}

	/**
	 * Get control output list.
	 *
	 * @since 9.0
	 * @param array $control_list list of controls slugs.
	 * @return array
	 */
	public function get_control_output_list( $control_list ) {
		$control_output_list = [];
		foreach ( $control_list as $control ) {
			$control_output = $this->get_single_control_output( $control );
			if ( ! $control_output ) {
				continue;
			}

			$control_output_list[] = [
				$control,
				$control_output,
			];
		}

		return $control_output_list;
	}

	/**
	 * Get single control output.
	 *
	 * @since 9.0
	 * @param string $control Control slug.
	 * @return string
	 */
	public function get_single_control_output( $control ) {
		$method = vc_camel_case( 'get_control_' . $control );
		if ( method_exists( $this, $method ) ) {
			return $this->$method();
		}
		return '';
	}

	/**
	 * Get navbar control list.
	 *
	 * @since 7.7
	 * @return array
	 */
	public function getControlList() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		/**
		 * Filters list of navbar controls.
		 *
		 * @param array $this->controls
		 * @param Vc_Navbar $this
		 *
		 * @since 7.7
		 */
		return apply_filters( 'vc_nav_control_list', $this->controls, $this );
	}

	/**
	 * Get current post.
	 *
	 * @return null|WP_Post
	 */
	public function post() {
		if ( $this->post ) {
			return $this->post;
		} else {
			$this->post = get_post();
		}

		return $this->post;
	}

	/**
	 * Render template.
	 */
	public function render() {
		vc_include_template( 'editors/navbar/navbar.tpl.php', [
			'css_class' => $this->css_class,
			'controls' => $this->getControls(),
			'nav_bar' => $this,
			'post' => $this->post(),
		] );
	}

	/**
	 * Gets the HTML for the WPBakery Page Builder logo.
	 *
	 * @see vc_filter: vc_nav_front_logo - hook to override WPBakery Page Builder logo
	 * @return string
	 */
	public function getLogo() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid

		$output = vc_get_template(
			'editors/navbar/vc_control-brand-button.tpl.php',
			[
				'title' => esc_attr__( 'WPBakery Page Builder', 'js_composer' ),
				'link' => $this->brand_url,
			],
		);

		return apply_filters( 'vc_nav_front_logo', $output );
	}

	/**
	 * Renders the post settings control if the user has the necessary access.
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getControlPostSettings() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$has_post_settings_access = vc_user_access()->part( 'post_settings' )->can()->get();
		if ( ! $has_post_settings_access ) {
			return '';
		}

		$has_layout_module = vc_modules_manager()->is_module_on( [ 'vc-post-custom-layout' ] );

		// Backend editor: only show if layout module is enabled.
		// Frontend editor: show if post settings access OR layout module is enabled.
		$should_show = vc_is_frontend_editor() ? ( $has_post_settings_access || $has_layout_module ) : $has_layout_module;

		if ( ! $should_show ) {
			return '';
		}

		$title = sprintf( __( '%s settings', 'js_composer' ), wpb_get_post_type_noun( $this->post ) );

		return vc_get_template(
			'editors/navbar/vc_control-post-settings.php',
			[ 'title' => wpb_get_title_with_shortcut( $title ) ]
		);
	}

	/**
	 * Renders undo control.
	 *
	 * @return string
	 */
	public function getControlUndo() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return vc_get_template(
			'editors/navbar/vc_control-undo-button.tpl.php',
			[ 'title' => wpb_get_title_with_shortcut( 'Undo' ) ],
		);
	}

	/**
	 * Renders redo control.
	 *
	 * @return string
	 */
	public function getControlRedo() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return vc_get_template(
			'editors/navbar/vc_control-redo-button.tpl.php',
			[ 'title' => wpb_get_title_with_shortcut( 'Redo' ) ],
		);
	}

	/**
	 * Renders the custom CSS/JS control if at least one custom code module is enabled.
	 *
	 * @return string
	 */
	public function getControlCustomCode() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		// Only show if at least one of the custom code modules is enabled.
		if ( ! vc_modules_manager()->is_module_on( [ 'vc-custom-js', 'vc-custom-css' ] ) ) {
			return '';
		}

		return vc_get_template(
			'editors/navbar/vc_control-custom-code.php',
			[ 'title' => wpb_get_title_with_shortcut( 'Custom CSS/JS' ) ]
		);
	}

	/**
	 * Renders the fullscreen control.
	 *
	 * @return string
	 */
	public function getControlFullscreen() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return vc_get_template(
			'editors/navbar/vc_control-full-screen-button.tpl.php',
			[ 'title' => __( 'Full screen', 'js_composer' ) ],
		);
	}

	/**
	 * Renders the windowed control.
	 *
	 * @return string
	 */
	public function getControlWindowed() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return vc_get_template(
			'editors/navbar/vc_control-exit-full-screen-button.tpl.php',
			[ 'title' => __( 'Exit full screen', 'js_composer' ) ],
		);
	}

	/**
	 * Renders the add element control if the user has the necessary access.
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getControlAddElement() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( vc_user_access()->part( 'shortcodes' )->checkStateAny( true, 'custom', null )->get() && vc_user_access_check_shortcode_all( 'vc_row' ) && vc_user_access_check_shortcode_all( 'vc_column' ) ) {
			return vc_get_template(
				'editors/navbar/vc_control-add-new-element-button.tpl.php',
				[ 'title' => wpb_get_title_with_shortcut( 'Add new element' ) ],
			);
		}

		return '';
	}

	/**
	 *  Renders the templates control if the user has the necessary access.
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getControlTemplates() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( ! vc_user_access()->part( 'templates' )->can()->get() ) {
			return '';
		}

		return vc_get_template(
			'editors/navbar/vc_control-templates-button.php',
			[ 'title' => wpb_get_title_with_shortcut( 'Templates' ) ]
		);
	}

	/**
	 * Renders the frontend control if the frontend editor is enabled.
	 *
	 * @note we hide this button but keep it for click action on another 'Frontend Editor' button.
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getControlFrontend() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( ! vc_enabled_frontend() ) {
			return '';
		}

		return vc_get_template(
			'editors/navbar/vc_control-frontend-editor-button.tpl.php',
			[ 'title' => __( 'Frontend', 'js_composer' ) ],
		);
	}

	/**
	 * Renders the preview control.
	 *
	 * @return string
	 */
	public function getControlPreview() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		_deprecated_function( __METHOD__, '9.0' );

		return '';
	}

	/**
	 * Renders the save backend control with appropriate label based on post status and user capabilities.
	 *
	 * @since 8.0
	 * @param string $template
	 * @return string
	 */
	public function getControlSaveBackend( $template = '' ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$post_type = $this->post()->post_type;
		$post_type_object = get_post_type_object( $post_type );
		$can_publish = current_user_can( $post_type_object->cap->publish_posts );

		$post_type_list = [ 'publish', 'future', 'private' ];

		if ( in_array( get_post_status( $this->post() ), $post_type_list ) ) {
			$save_text = esc_html__( 'Update', 'js_composer' );
		} elseif ( $can_publish ) {
			$save_text = esc_html__( 'Publish', 'js_composer' );
		} else {
			$save_text = esc_html__( 'Submit for Review', 'js_composer' );
		}

		if ( ! $template ) {
			$template = 'editors/navbar/vc_control-buttons-desktop.tpl.php';
		}

		return vc_get_template(
			$template,
			[ 'save_text' => $save_text ]
		);
	}

	/**
	 * Renders the save backend control for mobile.
	 *
	 * @since 9.0
	 * @return string
	 */
	public function getControlSaveBackendMobile() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return $this->getControlSaveBackend( 'editors/navbar/vc_control-buttons-mobile.tpl.php' );
	}

	/**
	 * Renders the more control.
	 *
	 * @since 8.0
	 * @return array
	 */
	public function getControlMore() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return $this->get_control_output_list( $this->more_button_controls );
	}
}
