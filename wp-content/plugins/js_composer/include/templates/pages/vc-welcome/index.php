<?php
/**
 * Welcome page template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Get welcome page tabs.
$vc_page_welcome_tabs = apply_filters( 'vc_page_welcome_slugs_list', [
	'vc-welcome' => esc_html__( 'What\'s New', 'js_composer' ),
	'vc-faq' => esc_html__( 'FAQ', 'js_composer' ),
	'vc-resources' => esc_html__( 'Resources', 'js_composer' ),
] );
$welcome_slug = key( $vc_page_welcome_tabs ) ?: 'vc-welcome';
$active_welcome_tab = sanitize_key( vc_get_param( 'tab', $welcome_slug ) );

// Validate that the requested tab exists in the allowed tabs list.
if ( ! isset( $vc_page_welcome_tabs[ $active_welcome_tab ] ) ) {
	$active_welcome_tab = $welcome_slug;
}

preg_match( '/^(\d+)(\.\d+)?/', WPB_VC_VERSION, $matches );
$custom_tag = 'script';
?>
<div class="vc-page-welcome about-wrap">
	<h1><?php printf( esc_html__( 'Welcome to WPBakery Page Builder %s', 'js_composer' ), esc_html( isset( $matches[0] ) ? $matches[0] : WPB_VC_VERSION ) ); ?></h1>

	<div class="about-text">
		<?php esc_html_e( 'The leading no-code solution for building and managing WordPress sites.', 'js_composer' ); ?>
	</div>
	<div class="wp-badge vc-page-logo">
		<?php printf( esc_html__( 'Version %s', 'js_composer' ), esc_html( WPB_VC_VERSION ) ); ?>
	</div>
	<p class="vc-page-actions">
		<?php
		if ( vc_user_access()->wpAny( 'manage_options' )->part( 'settings' )->can( 'vc-general-tab' )->get() && ( ! is_multisite() || ! is_main_site() )
		) :
			?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=vc-general' ) ); ?>"
			class="button button-primary"><?php esc_html_e( 'Settings', 'js_composer' ); ?></a><?php endif; ?>
		<a href="https://twitter.com/share" class="twitter-share-button"
			data-via="wpbakery"
			data-text="Take full control over your #WordPress site with WPBakery Page Builder page builder"
			data-url="https://wpbakery.com" data-size="large">Tweet</a>
		<<?php echo esc_attr( $custom_tag ); ?>>! function ( d, s, id ) {
				var js, fjs = d.getElementsByTagName( s )[ 0 ], p = /^http:/.test( d.location ) ? 'http' : 'https';
				if ( ! d.getElementById( id ) ) {
					js = d.createElement( s );
					js.id = id;
					js.src = p + '://platform.twitter.com/widgets.js';
					fjs.parentNode.insertBefore( js, fjs );
				}
			}( document, 'script', 'twitter-wjs' );</<?php echo esc_attr( $custom_tag ); ?>>
	</p>

	<!-- Welcome page inner tabs -->
	<h2 class="nav-tab-wrapper">
		<?php foreach ( $vc_page_welcome_tabs as $tab_slug => $title ) : ?>
			<?php
			// Inner tab URLs use page=vc-welcome (registered in settings system via getTabs())
			// with tab= parameter to load the appropriate welcome tab content.
			$url = 'admin.php?page=vc-welcome&tab=' . rawurlencode( $tab_slug );
			?>
			<a href="<?php echo esc_attr( is_network_admin() ? network_admin_url( $url ) : admin_url( $url ) ); ?>"
					class="nav-tab<?php echo $active_welcome_tab === $tab_slug ? esc_attr( ' nav-tab-active' ) : ''; ?>">
				<?php echo esc_html( $title ); ?>
			</a>
		<?php endforeach; ?>
	</h2>

	<!-- Welcome tab content -->
	<?php
	// Include the active welcome tab template.
	$tab_template = 'pages/vc-welcome/' . $active_welcome_tab . '.php';
	vc_include_template( $tab_template );
	?>
</div>
