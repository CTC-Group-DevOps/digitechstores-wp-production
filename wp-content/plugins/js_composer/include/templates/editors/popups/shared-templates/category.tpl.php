<?php
/**
 * Category template for shared templates.
 *
 * @var Vc_Shared_Templates $controller
 * @var array $templates
 *
 * phpcs:ignoreFile:Generic.PHP.DisallowAlternativePHPTags.MaybeASPShortOpenTagFound
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$is_license_active = vc_license()->isActivated();
$has_downloaded = ! empty( $templates );
$custom_tag = 'script';
?>

<<?php echo esc_attr( $custom_tag ); ?>>
	window.vcTemplatesLibraryData = {
		templates: <?php echo wp_json_encode( $templates ); ?>,
		licenseActive: <?php echo wp_json_encode( $is_license_active ); ?>
	};
</<?php echo esc_attr( $custom_tag ); ?>>

<div class="vc_ui-panel-loading vc_ui-hidden">
	<div class="vc_preloader-box"></div>
</div>

<div class="vc_ui-panel-download vc_ui-hidden">
	<div class="vc_ui-panel-loading-content">
		<div class="vc_preloader-box"></div>
		<h3 class="vc_ui-panel-title"><?php esc_html_e( 'Downloading template ... please wait!', 'js_composer' ); ?></h3>
		<p class="vc_description">
			<?php esc_html_e( 'Don\'t close this window until download is complete - you will be redirected back to Template Library automatically.', 'js_composer' ); ?>
		</p>
	</div>
</div>

<div class="vc_ui-templates-content">
	<?php if ( ! $is_license_active ) : ?>
		<div class="vc_ui-template-library-cta">
			<h3 class="vc_ui-panel-title"><?php esc_html_e( 'Template library', 'js_composer' ); ?></h3>
			<p class="vc_description">
				<?php esc_html_e( 'Template library is our curated collection of templates you can download from the WPBakery cloud. This module is available only to the direct WPBakery license owners with valid support period.', 'js_composer' ); ?>
			</p>
			<a href="<?php echo esc_url( 'https://wpbakery.com/?utm_source=wpb-plugin&utm_medium=template-window&utm_campaign=info&utm_content=button' ); ?>"
				target="_blank" rel="noopener noreferrer"
				title="<?php esc_attr_e( 'Get WPBakery', 'js_composer' ); ?>"
				class="vc_general vc_ui-button vc_ui-button-size-md vc_ui-button-shape-rounded vc_ui-template-library-cta-btn">
				<?php esc_html_e( 'Get WPBakery', 'js_composer' ); ?>
			</a>
		</div>
	<?php endif; ?>

	<?php
	$show_downloaded_section = $is_license_active || $has_downloaded;
	if ( $show_downloaded_section ) :
		?>
		<section class="vc_ui-template-section" data-section="downloaded">
			<h3 class="vc_ui-template-section-title"><?php esc_html_e( 'Downloaded', 'js_composer' ); ?></h3>
			<div class="vc_ui-template-card-grid vc_templates-list-shared_templates"
				id="vc_template-library-downloaded"
				data-vc-grid="downloaded"></div>
			<p class="vc_ui-template-grid-empty" data-vc-grid-empty="downloaded">
				<?php esc_html_e( 'You don\'t have any templates downloaded yet.', 'js_composer' ); ?>
			</p>
		</section>
	<?php endif; ?>

	<section class="vc_ui-template-section" data-section="library">
		<h3 class="vc_ui-template-section-title"><?php esc_html_e( 'Template library', 'js_composer' ); ?></h3>
		<div class="vc_ui-template-card-grid"
			id="vc_template-library-grid"
			data-vc-grid="library"
			role="listbox"></div>
		<p class="vc_ui-template-grid-empty vc_ui-hidden"
			data-vc-grid-empty="library">
			<?php esc_html_e( 'No templates found.', 'js_composer' ); ?>
		</p>
	</section>
</div>

<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_template-downloaded-card">
	<div class="vc_ui-template-card vc_templates-template-type-shared_templates"
			data-template_id="<%- post_id %>"
			data-template_unique_id="<%- post_id %>"
			data-template_id_hash="<%- post_id %>"
			data-template_name="<%- _.escape(vc_slugify(title)) %>"
			data-template_type="shared_templates"
			data-category="shared_templates"
			data-template_action="vc_delete_template">
		<span class="vc_ui-template-card-thumb">
			<% if (thumbnailUrl) { %><img src="<%- thumbnailUrl %>" alt=""><% } %>
			<span class="vc_ui-template-card-overlay">
				<button type="button"
					class="vc_ui-template-card-action"
					data-template-handler
					title="<?php esc_attr_e( 'Add template', 'js_composer' ); ?>"
					aria-label="<?php esc_attr_e( 'Add template', 'js_composer' ); ?>">
					<i class="vc-composer-icon vc-c-add-circle" aria-hidden="true"></i>
				</button>
				<?php if ( vc_user_access()->part( 'templates' )->checkStateAny( true, null )->get() ) : ?>
					<button type="button"
						class="vc_ui-template-card-action"
						data-vc-ui-delete="template-title"
						title="<?php esc_attr_e( 'Delete template', 'js_composer' ); ?>"
						aria-label="<?php esc_attr_e( 'Delete template', 'js_composer' ); ?>">
						<i class="vc-composer-icon vc-c-trash" aria-hidden="true"></i>
					</button>
				<?php endif; ?>
			</span>
		</span>
		<span class="vc_ui-template-card-name" data-vc-ui-element="template-title" title="<%- title %>"><%- title %></span>
	</div>
</<?php echo esc_attr( $custom_tag ); ?>>

<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_template-library-card">
	<div class="vc_ui-template-card vc_ui-template-card-library<%- locked ? ' vc_ui-template-card--locked' : '' %>"
			data-template-id="<%- id %>"
			data-template-version="<%- version %>"
			data-template-downloaded="<%- downloaded ? 'true' : 'false' %>"
			data-template-update-available="<%- updateAvailable ? 'true' : 'false' %>"
			data-template_name="<%- _.escape(vc_slugify(title)) %>">
		<span class="vc_ui-template-card-thumb">
			<% if (thumbnailUrl) { %><img src="<%- thumbnailUrl %>" alt="" loading="lazy"><% } %>
			<% if (locked) { %>
				<span class="vc_ui-template-card-overlay">
					<span class="vc_ui-template-card-action vc_ui-template-card-action--static"
						title="<?php esc_attr_e( 'License required', 'js_composer' ); ?>"
						aria-label="<?php esc_attr_e( 'License required', 'js_composer' ); ?>">
						<i class="vc-composer-icon vc-c-locked" aria-hidden="true"></i>
					</span>
				</span>
			<% } else if (updateAvailable) { %>
				<span class="vc_ui-template-card-overlay">
					<a href="javascript:" role="option"
						class="vc_ui-template-card-action vc_ui-template-card-update-btn"
						title="<?php esc_attr_e( 'Update template', 'js_composer' ); ?>"
						aria-label="<?php esc_attr_e( 'Update template', 'js_composer' ); ?>">
						<i class="vc-composer-icon vc-c-icon-sync" aria-hidden="true"></i>
					</a>
				</span>
			<% } else if (!downloaded) { %>
				<span class="vc_ui-template-card-overlay">
					<a href="javascript:" role="option"
						class="vc_ui-template-card-action vc_ui-template-card-download-btn"
						title="<?php esc_attr_e( 'Download template', 'js_composer' ); ?>"
						aria-label="<?php esc_attr_e( 'Download template', 'js_composer' ); ?>">
						<i class="vc-composer-icon vc-c-download" aria-hidden="true"></i>
					</a>
				</span>
			<% } %>
		</span>
		<span class="vc_ui-template-card-name" title="<%- title %>"><%- title %></span>
	</div>
</<?php echo esc_attr( $custom_tag ); ?>>
