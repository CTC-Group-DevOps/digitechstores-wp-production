<?php
/**
 * WordPress settings section in page settings panel template.
 *
 * @var array $page_settings_data
 * @var bool $is_layout_blank
 * @var array $permalink
 * @since 8.2
 */

global $post;
$post_type = get_post_type();
?>
<div class="wpb-modal-section">
<?php
vc_include_template( 'editors/partials/post-settings/post_status.tpl.php' );

vc_include_template(
	'editors/partials/post-settings/post_permalink.tpl.php',
	[ 'permalink' => $permalink ] // @phpstan-ignore-line
);

if ( ! $is_layout_blank ) { // @phpstan-ignore-line
	vc_include_template(
		'editors/partials/post-settings/post_template.tpl.php'
	);
}

if ( current_theme_supports( 'menus' ) ) {
	vc_include_template(
		'editors/partials/post-settings/post_menu.tpl.php'
	);
}
?>
</div>

<?php if ( get_post_type() === 'post' || post_type_supports( $post_type, 'thumbnail' ) || post_type_supports( $post_type, 'excerpt' ) ) : ?>
<div class="wpb-modal-section">
	<?php
	if ( get_post_type() === 'post' ) {
		vc_include_template( 'editors/partials/post-settings/post_categories.tpl.php' );
		vc_include_template( 'editors/partials/post-settings/post_tags.tpl.php' );
	}

	if ( post_type_supports( get_post_type(), 'thumbnail' ) ) {
		vc_include_template( 'editors/partials/post-settings/post_featured_image.tpl.php' );
	}

	if ( post_type_supports( $post_type, 'excerpt' ) ) {
		vc_include_template( 'editors/partials/post-settings/post_excerpt.tpl.php' );
	}
	?>
</div>
<?php endif; ?>

<div class="wpb-modal-section">
<?php
vc_include_template( 'editors/partials/post-settings/post_author.tpl.php' );

if ( comments_open( $post ) || pings_open( $post ) || post_type_supports( $post_type, 'comments' ) ) {
	vc_include_template( 'editors/partials/post-settings/post_comments.tpl.php', [ 'post' => $post ] );
	vc_include_template( 'editors/partials/post-settings/post_pingbacks.tpl.php', [ 'post' => $post ] );
}
?>
</div>
