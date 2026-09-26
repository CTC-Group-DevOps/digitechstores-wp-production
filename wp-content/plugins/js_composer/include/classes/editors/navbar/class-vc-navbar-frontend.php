<?php
/**
 * Navbar Frontend functionality.
 *
 * @package WPBakeryPageBuilder
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'EDITORS_DIR', 'navbar/class-vc-navbar.php' );

/**
 * Class Vc_Navbar_Frontend
 */
class Vc_Navbar_Frontend extends Vc_Navbar {
	/**
	 * List of controls specific to the frontend navbar.
	 *
	 * @var array
	 */
	protected $controls = [
		'add_element',
		'templates',
		'view_post',
		'more',
		'save_buttons',
		'post_settings',
		'custom_code',
		'screen_size',
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
		'save_buttons_mobile',
		'backend_editor',
		'preview',
		'view_post_mobile',
	];

	/**
	 * Filter name for the frontend controls.
	 *
	 * @var string
	 */
	protected $controls_filter_name = 'vc_nav_front_controls';
	/**
	 * URL for the frontend brand logo.
	 *
	 * @var string
	 */
	protected $brand_url = 'https://wpbakery.com/?utm_source=wpb-plugin&utm_medium=frontend-editor&utm_campaign=info&utm_content=logo';
	/**
	 * CSS class for the frontend navbar.
	 *
	 * @var string
	 */
	protected $css_class = 'vc_navbar vc_navbar-frontend';
	/**
	 * Renders the screen size controls.
	 *
	 * @return string
	 */
	public function getControlScreenSize() {
		$disable_responsive = vc_settings()->get( 'not_responsive_css' );
		if ( '1' === $disable_responsive ) {
			return '';
		}

		$screen_sizes = vc_get_shared( 'screen sizes' );
        // phpcs:ignore:WordPress.NamingConventions.ValidHookName.NotLowercase
		$screen_sizes = apply_filters( 'wpb_navbar_getControlScreenSize', $screen_sizes );
		$title = esc_html__( 'Responsive preview', 'js_composer' );

		$output = '<li class="vc_pull-right vc_hide-mobile"><div class="vc_dropdown" id="vc_screen-size-control"><a href="#" class="vc_dropdown-toggle vc_icon-btn" tabindex="7" title="' . $title . '" aria-label="' . $title . '" role="button" aria-haspopup="true"><span id="vc_screen-size-current" class="vc_screen-size-current">';
		$output .= vc_get_template( 'icons/desktop-ico.tpl.php' );
		$output .= '</span></a><ul class="vc_dropdown-list" role="menu">';

		$screen = current( $screen_sizes );
		while ( $screen ) {
			$title_attr = esc_attr( $screen['title'] );
			$active_class = isset( $screen['active'] ) && $screen['active'] ? ' active' : '';

			// Map screen key to icon template, with special case for 'default'.
			$icon_key = 'default' === $screen['key'] ? 'desktop' : $screen['key'];

			// Security: Sanitize $icon_key to prevent path traversal attacks.
			$icon_key = preg_replace( '/[^a-z0-9\-]/', '', $icon_key );

			$output .= '<li><a href="#" title="' . $title_attr . '" aria-label="' . $title_attr . '" class="vc_screen-width vc_icon-btn' . $active_class . '" data-size="' . esc_attr( $screen['size'] ) . '">';
			$output .= vc_get_template( 'icons/' . $icon_key . '-ico.tpl.php' );
			$output .= '</a></li>';

			next( $screen_sizes );
			$screen = current( $screen_sizes );
		}
		$output .= '</ul></div></li>';

		return $output;
	}


	/**
	 * Renders the save buttons control with appropriate label based on post status and user capabilities.
	 *
	 * @since 8.0
	 * @param bool $is_mobile
	 * @return string
	 */
	public function getControlSaveButtons( $is_mobile = false ) {
		return vc_get_template(
			'editors/navbar/vc_control-save-buttons.tpl.php',
			[
				'post' => $this->post(),
				'is_mobile' => $is_mobile,
			]
		);
	}

	/**
	 * Renders the save buttons mobiles control with appropriate label based on post status and user capabilities.
	 *
	 * @since 9.0
	 * @return string
	 */
	public function getControlSaveButtonsMobile() {
		return $this->getControlSaveButtons( true );
	}

	/**
	 * Controls html for view post functionality.
	 *
	 * @since 8.0
	 * @param bool $is_mobile
	 * @return string
	 */
	public function getControlViewPost( $is_mobile = false ) {
		return vc_get_template(
			'editors/navbar/vc_control-view-post.tpl.php',
			[
				'is_mobile' => $is_mobile,
				'post_id'   => $this->post(),
				'title'     => wpb_get_title_with_shortcut( 'Exit WPBakery Page Builder edit mode' ),
			]
		);
	}

	/**
	 * Controls html for save and update functionality.
	 *
	 * @return string
	 * @deprecated 8.0
	 */
	public function getControlSaveUpdate() {
		_deprecated_function( __METHOD__, '8.0', 'Vc_Navbar_Frontend::getControlMore' );

		return $this->getControlMore();
	}

	/**
	 * Controls html for backend editor button functionality.
	 *
	 * @return string
	 * @deprecated 8.0
	 */
	public function getControlBackendEditor() {
		if ( ! vc_user_access()->part( 'backend_editor' )->can()->get() ) {
			return '';
		}

		return vc_get_template(
			'editors/navbar/vc_control-backend-editor-button.tpl.php',
			[
				'title' => __( 'Backend Editor', 'js_composer' ),
				'link'  => get_edit_post_link( $this->post() ),
			],
		);
	}

	/**
	 * Controls html for preview button functionality.
	 *
	 * @return string
	 * @deprecated 8.0
	 */
	public function getControlPreview() {
		return vc_get_template(
			'editors/navbar/vc_control-preview-button.tpl.php',
			[
				'title' => __( 'View Page', 'js_composer' ),
				'link'  => get_permalink( $this->post() ),
			],
		);
	}

	/**
	 * Renders the save backend control for mobile.
	 *
	 * @since 9.0
	 * @return string
	 */
	public function getControlViewPostMobile() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return $this->getControlViewPost( true );
	}
}
