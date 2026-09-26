<?php
/**
 * Frontend controls template.
 *
 * phpcs:ignoreFile:Squiz.PHP.EmbeddedPhp.ContentBeforeEnd
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$custom_tag = 'script';
?>
<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_controls-template-default">
	<div
			class="vc_controls-element" data-can-all="{{ can_all }}" data-can-edit="{{ can_edit }}">
		<div class="vc_controls-cc">
			<a class="vc_control-btn vc_element-name{# if( can_all && moveAccess ) { #} vc_element-move{# } #}"><span
				class="vc_btn-content"
				title="{# if( can_all && moveAccess ) { #}<?php printf(
					esc_attr__( 'Drag to move %s', 'js_composer' ),
					'{{ name_lower }}'
				); ?>{# } #}">{# if( can_all && moveAccess ) { #}<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>{# } #}
				{{ name }}
			</span></a>{# if( can_edit ) { #}<a
			class="vc_control-btn vc_control-btn-edit" data-control="edit" href="#"
			title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
				class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# }
					if( can_all ) { #}<a class="vc_control-btn vc_control-btn-clone"
						data-control="clone"
						href="#"
						title="<?php printf( esc_attr__( 'Clone %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
				class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-clone"></i></span></a><a
			class="vc_control-btn vc_control-btn-copy" href="#"
			title="<?php printf( esc_attr__( 'Copy %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
				class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-copy"></i></span></a><a
			class="vc_control-btn vc_control-btn-delete" data-control="delete" href="#"
			title="<?php printf( esc_attr__( 'Delete %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
				class="vc_btn-content"><i class="vc-composer-icon vc-c-trash"></i></span></a>{# } #}
		</div>
	</div>
</<?php echo esc_attr( $custom_tag ); ?>>
<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_controls-template-container">
	<div class="vc_controls-container">
		<div class="vc_controls-out-tl">
			<div class="vc_element element-{{ tag }}">
				<a class="vc_control-btn vc_element-name{# if( can_all && moveAccess ) { #} vc_element-move{# } #}"
					title="{# if( can_all && moveAccess ) { #}<?php printf(
						esc_attr__( 'Drag to move %s', 'js_composer' ),
						'{{ name_lower }}'
					); ?>{# } #}">{# if( can_all && moveAccess ) { #}<span
							class="vc_btn-content">
							<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>{{ name }}</span>{# } else { #}<span
							class="vc_btn-content">
							{{ name }}</span>{# } #}</a>{# if( 'edit' !== state ) { #}<a
						class="vc_control-btn vc_control-btn-prepend" href="#"
						title="<?php printf( esc_attr__( 'Prepend to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
							class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>{# } #}{# if( can_edit ) { #}<a class="vc_control-btn vc_control-btn-edit"
																				href="#"
																				title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
							class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( can_all ) { #}<a
						class="vc_control-btn vc_control-btn-clone" href="#"
						title="<?php printf( esc_attr__( 'Clone %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
							class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-clone"></i></span></a><span class="vc_control-btn vc_control-btn-copypaste" title="<?php esc_attr_e( 'Copy and paste', 'js_composer' ); ?>"><select
						class="vc_copypaste-select" data-placeholder="&hellip;"><option></option><option
							value="copy" title="<?php esc_attr_e( 'Copy element', 'js_composer' ); ?>"><?php esc_html_e( 'Copy element', 'js_composer' ); ?></option><option
							value="paste" title="<?php esc_attr_e( 'Paste element', 'js_composer' ); ?>"><?php esc_html_e( 'Paste element', 'js_composer' ); ?></option></select></span><a
						class="vc_control-btn vc_control-btn-delete" href="#"
						title="<?php printf( esc_attr__( 'Delete %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
							class="vc_btn-content"><i class="vc-composer-icon vc-c-trash"></i></span></a>{# } #}
			</div>
		</div>
		{# if( 'edit' !== state ) { #}
		<div class="vc_controls-bc">
			<a class="vc_control-btn vc_control-btn-append" href="#"
				title="<?php printf( esc_attr__( 'Append to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>
		</div>
		{# } #}
	</div><!-- end vc_controls-column -->
</<?php echo esc_attr( $custom_tag ); ?>>
<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_controls-template-container-width-parent">
	<div class="vc_controls-column">
		<div class="vc_controls-out-tl">
			<div class="vc_parent parent-{{ parent_tag }} vc_active"><a
				class="vc_control-btn vc_element-name{# if( parent_can_all && moveAccess ) { #} vc_move-{{ parent_tag }} vc_element-move{# } #}"
				title="{# if( can_all && moveAccess ) { #}<?php printf(
					esc_attr__( 'Drag to move %s', 'js_composer' ),
					'{{ parent_name_lower }}'
				); ?>{# } #}">{# if( can_all && moveAccess ) { #}<span
					class="vc_btn-content">
					<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>
					{{ parent_name }}</span>{# } else { #}<span
					class="vc_btn-content">
					{{ parent_name }}</span>{# } #}</a><span class="advanced">{# if( parent_can_edit ) { #}<a
					class="vc_control-btn vc_control-btn-edit vc_edit" href="#"
					title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( allowAdd ) { #}<a
					class="vc_control-btn vc_control-btn-prepend vc_edit" href="#"
					title="<?php printf( esc_attr__( 'Prepend to %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>{# } #}{# if( parent_can_all ) { #}<a
					class="vc_control-btn vc_control-btn-clone" href="#"
					title="<?php printf( esc_attr__( 'Clone %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-content_copy"></i></span></a><a
					class="vc_control-btn vc_control-btn-delete" href="#"
					title="<?php printf( esc_attr__( 'Delete %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-trash"></i></span></a>{# } #}</span>
			</div>
			<div class="vc_element element-{{ tag }} vc_active"><a
				class="vc_control-btn vc_element-name vc_move-{{ tag }} {# if( can_all && moveAccess ) { #}vc_element-move{# } #}"
				title="{# if( can_all && moveAccess ) { #}<?php printf(
					esc_attr__( 'Drag to move %s', 'js_composer' ),
					'{{ name_lower }}'
				); ?>{# } #}">{# if( can_all && moveAccess ) { #}<span
					class="vc_btn-content">
					<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>
					{{ name }}</span>{# } else { #}<span
					class="vc_btn-content">
					{{ name }}</span>{# } #}</a><span class="advanced">{# if( can_edit ) { #}<a
					class="vc_control-btn vc_control-btn-edit" href="#"
					title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( 'edit' !== state ) { #}<a
					class="vc_control-btn vc_control-btn-prepend" href="#"
					title="<?php printf( esc_attr__( 'Prepend to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a></span>{# } #}
			</div>
		</div>
		{# if( 'edit' !== state ) { #}
		<div class="vc_controls-bc">
			<a class="vc_control-btn vc_control-btn-append" href="#"
				title="<?php printf( esc_attr__( 'Append to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>
		</div>
		{# } #}
	</div><!-- end vc_controls-column -->
</<?php echo esc_attr( $custom_tag ); ?>>
<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_controls-template-vc_column">
	<div class="vc_controls-column">
		<div class="vc_controls-out-tl">
			<div class="vc_parent parent-{{ parent_tag }} vc_active"><a
				class="vc_control-btn vc_element-name{# if( parent_can_all && moveAccess ) { #} vc_element-move vc_move-{{ parent_tag }}{# } #}"
				title="{# if( parent_can_all && moveAccess ) { #}<?php printf(
					esc_attr__( 'Drag to move %s', 'js_composer' ),
					'{{ parent_name_lower }}'
				); ?>{# } #}">{# if( parent_can_all && moveAccess ) { #}<span
					class="vc_btn-content">
					<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>
					{{ parent_name }}</span>{# } else { #}<span
					class="vc_btn-content">
					{{ parent_name }}</span>{# } #}</a><span class="vc_advanced">{# if( parent_can_edit ) { #}<a
					class="vc_control-btn vc_control-btn-edit vc_edit" href="#"
					title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( parent_can_all ) { #}<a
					class="vc_control-btn vc_control-btn-layout vc_edit" href="#"
					title="<?php printf( esc_attr__( 'Change layout', 'js_composer' ) ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-layout"></i></span></a>{# } #}{# if( parent_can_all ) { #}<a
					class="vc_control-btn vc_control-btn-clone" href="#"
					title="<?php printf( esc_attr__( 'Clone %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-clone"></i></span></a><span class="vc_control-btn vc_control-btn-copypaste" title="<?php esc_attr_e( 'Copy and paste', 'js_composer' ); ?>"><select
					class="vc_copypaste-select" data-placeholder="&hellip;"><option></option><option
						value="copy" title="<?php esc_attr_e( 'Copy element', 'js_composer' ); ?>"><?php esc_html_e( 'Copy element', 'js_composer' ); ?></option><option
						value="paste" title="<?php esc_attr_e( 'Paste element', 'js_composer' ); ?>"><?php esc_html_e( 'Paste element', 'js_composer' ); ?></option></select></span><a
					class="vc_control-btn vc_control-btn-delete" href="#"
					title="<?php printf( esc_attr__( 'Delete %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-trash"></i></span></a>{# } #}</span>
			</div>
			<div class="vc_element element-{{ tag }} vc_active"><a
				class="vc_control-btn vc_element-name{# if( can_all && moveAccess ) { #} vc_element-move vc_move-vc_column{# } #}"
				title="{# if( can_all && moveAccess ) { #}<?php printf(
					esc_attr__( 'Drag to move %s', 'js_composer' ),
					'{{ name_lower }}'
				); ?>{# } #}">{# if( can_all && moveAccess ) { #}<span
					class="vc_btn-content">
					<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>
					{{ name }}</span>{# } else { #}<span
					class="vc_btn-content">
					{{ name }}</span>{# } #}</a><span class="vc_advanced">{# if( 'edit' !== state ) { #}<a
					class="vc_control-btn vc_control-btn-prepend" href="#"
					title="<?php printf( esc_attr__( 'Prepend to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>{# } #}{# if( can_edit ) { #}<a
					class="vc_control-btn vc_control-btn-edit" href="#"
					title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( can_all ) { #}<a
					class="vc_control-btn vc_control-btn-paste" href="#"
					title="<?php esc_attr_e( 'Paste', 'js_composer' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-paste"></i></span></a><a
					class="vc_control-btn vc_control-btn-delete" href="#"
					title="<?php printf( esc_attr__( 'Delete %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-trash"></i></span></a>{# } #}</span>
			</div>
		</div>
		{# if( 'edit' !== state ) { #}
		<div class="vc_controls-bc">
			<a class="vc_control-btn vc_control-btn-append" href="#"
				title="<?php printf( esc_attr__( 'Append to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>
		</div>
		{# } #}
	</div><!-- end vc_controls-column -->
</<?php echo esc_attr( $custom_tag ); ?>>
<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_controls-template-vc_tab">
	<div class="vc_controls-column">
		<div class="vc_controls-out-tr">
			<div class="vc_parent parent-{{ parent_tag }}"><a
				class="vc_control-btn vc_element-name vc_move-{{ parent_tag }}{# if( parent_can_all && moveAccess ) { #} vc_element-move{# } #}"
				title="{# if( parent_can_all && moveAccess ) { #}<?php printf(
					esc_attr__( 'Drag to move %s', 'js_composer' ),
					'{{ parent_name_lower }}'
				); ?>{# } #}">{# if( parent_can_all && moveAccess ) { #}<span
					class="vc_btn-content">
					<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>
					{{ parent_name }}</span>{# } else { #}<span
					class="vc_btn-content">
					{{ parent_name }}</span>{# } #}</a><span class="vc_advanced">{# if( parent_can_edit ) { #}<a
					class="vc_control-btn vc_control-btn-edit vc_edit" href="#"
					title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( allowAdd ) { #}<a
					class="vc_control-btn vc_control-btn-prepend vc_edit" href="#"
					title="<?php printf( esc_attr__( 'Add new %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>{# } #}{# if( parent_can_all ) { #}<a
					class="vc_control-btn vc_control-btn-clone" href="#"
					title="<?php printf( esc_attr__( 'Clone %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-clone"></i></span></a><a
					class="vc_control-btn vc_control-btn-delete" href="#"
					title="<?php printf( esc_attr__( 'Delete %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-trash"></i></span></a>{# } #}</span>
			</div>
			<div class="vc_element element-{{ tag }} vc_active"><a
				class="vc_control-btn vc_element-name vc_move-{{ tag }}{# if( can_all && moveAccess ) { #} vc_element-move{# } #}"
				title="{# if( can_all && moveAccess ) { #}<?php printf(
					esc_attr__( 'Drag to move %s', 'js_composer' ),
					'{{ name }}'
				); ?>{# } #}">{# if( can_all && moveAccess ) { #}<span
					class="vc_btn-content">
					<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>
					{{ name }}</span>{# } else { #}<span
					class="vc_btn-content">
					{{ name }}</span>{# } #}</a><span class="vc_advanced">{# if( can_edit ) { #}<a
					class="vc_control-btn vc_control-btn-edit" href="#"
					title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( 'edit' !== state ) { #}<a
					class="vc_control-btn vc_control-btn-prepend" href="#"
					title="<?php printf( esc_attr__( 'Prepend to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></a>{# } #}{# if( can_all ) { #}<a
					class="vc_control-btn vc_control-btn-clone" href="#"
					title="<?php printf( esc_attr__( 'Clone %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-content_copy"></i></span></a><a
					class="vc_control-btn vc_control-btn-delete" href="#"
					title="<?php printf( esc_attr__( 'Delete %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-trash"></i></span></a>{# } #}</span>
			</div>
		</div>
		{# if( 'edit' !== state ) { #}
		<div class="vc_controls-bc">
			<a class="vc_control-btn vc_control-btn-append" href="#"
				title="<?php printf( esc_attr__( 'Append to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
					class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>
		</div>
		{# } #}
	</div><!-- end vc_controls-column -->
</<?php echo esc_attr( $custom_tag ); ?>>
<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_controls-template-vc_tta_section">
	<div class="vc_controls-container">
		<div class="vc_controls-out-tr">
			<div class="vc_parent parent-{{ parent_tag }}"><a
				class="vc_control-btn vc_element-name vc_move-{{ parent_tag }}{# if( parent_can_all && moveAccess ) { #} vc_element-move{# } #}"
				title="{# if( parent_can_all && moveAccess ) { #}<?php printf(
					esc_attr__( 'Drag to move %s', 'js_composer' ),
					'{{ parent_name_lower }}'
				); ?>{# } #}">{# if( parent_can_all && moveAccess ) { #}<span
					class="vc_btn-content">
					<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>
					{{ parent_name }}</span>{# } else { #}<span
					class="vc_btn-content">
					{{ parent_name }}</span>{# } #}</a><span class="vc_advanced">{# if( parent_can_edit ) { #}<a
					class="vc_control-btn vc_control-btn-edit vc_edit" href="#"
					title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"
					data-vc-control="parent.edit"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( allowAdd ) { #}<a
					class="vc_control-btn vc_control-btn-prepend vc_edit" href="#"
					title="<?php printf( esc_attr__( 'Add new %s', 'js_composer' ), '{{ name_lower }}' ); ?>"
					data-vc-control="parent.append"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>{# } #}{# if( parent_can_all ) { #}<a
					class="vc_control-btn vc_control-btn-clone" href="#"
					title="<?php printf( esc_attr__( 'Clone %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"
					data-vc-control="parent.clone"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-clone"></i></span></a><span class="vc_control-btn vc_control-btn-copypaste" title="<?php esc_attr_e( 'Copy and paste', 'js_composer' ); ?>"><select
					class="vc_copypaste-select" data-placeholder="&hellip;"><option></option><option
						value="copy" title="<?php esc_attr_e( 'Copy element', 'js_composer' ); ?>"><?php esc_html_e( 'Copy element', 'js_composer' ); ?></option><option
						value="paste" title="<?php esc_attr_e( 'Paste element', 'js_composer' ); ?>"><?php esc_html_e( 'Paste element', 'js_composer' ); ?></option></select></span><a
						class="vc_control-btn vc_control-btn-delete" href="#"
						title="<?php printf( esc_attr__( 'Delete %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"
						data-vc-control="parent.destroy"><span
							class="vc_btn-content"><i class="vc-composer-icon vc-c-trash"></i></span></a>{# } #}</span>
			</div>
			<div class="vc_element element-{{ tag }} vc_active"><a
				class="vc_control-btn vc_element-name"
				title="{# if( can_all && moveAccess ) { #}<?php printf(
					esc_attr__( 'Drag to move %s', 'js_composer' ),
					'{{ name }}'
				); ?>{# } #}">{# if( can_all && moveAccess ) { #}<span
					class="vc_btn-content">
					{{ name }}</span>{# } else { #}<span
					class="vc_btn-content">
					{{ name }}</span>{# } #}</a><span class="vc_advanced">{# if( can_edit ) { #}<a
					class="vc_control-btn vc_control-btn-edit" href="#"
					title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ name_lower }}' ); ?>"
					data-vc-control="edit"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( 'edit' !== state ) { #}<a
					class="vc_control-btn vc_control-btn-prepend" href="#"
					title="<?php printf( esc_attr__( 'Prepend to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"
					data-vc-control="prepend"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>{# } #}{# if( can_all ) { #}<a
					class="vc_control-btn vc_control-btn-clone" href="#"
					title="<?php printf( esc_attr__( 'Clone %s', 'js_composer' ), '{{ name_lower }}' ); ?>"
					data-vc-control="clone"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-clone"></i></span></a><span class="vc_control-btn vc_control-btn-copypaste" title="<?php esc_attr_e( 'Copy and paste', 'js_composer' ); ?>"><select
					class="vc_copypaste-select" data-placeholder="&hellip;"><option></option><option
						value="copy" title="<?php esc_attr_e( 'Copy element', 'js_composer' ); ?>"><?php esc_html_e( 'Copy element', 'js_composer' ); ?></option><option
						value="paste" title="<?php esc_attr_e( 'Paste element', 'js_composer' ); ?>"><?php esc_html_e( 'Paste element', 'js_composer' ); ?></option></select></span><a
						class="vc_control-btn vc_control-btn-delete" href="#"
						title="<?php printf( esc_attr__( 'Delete %s', 'js_composer' ), '{{ name_lower }}' ); ?>"
						data-vc-control="destroy"><span
							class="vc_btn-content"><i class="vc-composer-icon vc-c-trash"></i></span></a>{# } #}</span>
			</div>
		</div>
		{# if( 'edit' !== state ) { #}
		<div class="vc_controls-bc">
			<a class="vc_control-btn vc_control-btn-append" href="#"
				title="<?php printf( esc_attr__( 'Append to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"
				data-vc-control="append"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>
		</div>
		{# } #}
	</div><!-- end vc_controls-vc_tta_section -->
</<?php echo esc_attr( $custom_tag ); ?>>
<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_controls-template-vc_tta_toggle_section">
	<div class="vc_controls-container">
		<div class="vc_controls-out-tr">
			<div class="vc_parent parent-{{ parent_tag }}"><a
				class="vc_control-btn vc_element-name vc_move-{{ parent_tag }}{# if( parent_can_all && moveAccess ) { #} vc_element-move{# } #}"
				title="{# if( parent_can_all && moveAccess ) { #}<?php printf(
					esc_attr__( 'Drag to move %s', 'js_composer' ),
					'{{ parent_name_lower }}'
				); ?>{# } #}">{# if( parent_can_all && moveAccess ) { #}<span
					class="vc_btn-content">
					<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>
					{{ parent_name }}</span>{# } else { #}<span
					class="vc_btn-content">
					{{ parent_name }}</span>{# } #}</a><span class="vc_advanced">{# if( parent_can_edit ) { #}<a
					class="vc_control-btn vc_control-btn-edit vc_edit" href="#"
					title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"
					data-vc-control="parent.edit"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( parent_can_all ) { #}<a
					class="vc_control-btn vc_control-btn-clone" href="#"
					title="<?php printf( esc_attr__( 'Clone %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"
					data-vc-control="parent.clone"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-clone"></i></span></a><span class="vc_control-btn vc_control-btn-copypaste" title="<?php esc_attr_e( 'Copy and paste', 'js_composer' ); ?>"><select
					class="vc_copypaste-select" data-placeholder="&hellip;"><option></option><option
						value="copy" title="<?php esc_attr_e( 'Copy element', 'js_composer' ); ?>"><?php esc_html_e( 'Copy element', 'js_composer' ); ?></option><option
						value="paste" title="<?php esc_attr_e( 'Paste element', 'js_composer' ); ?>"><?php esc_html_e( 'Paste element', 'js_composer' ); ?></option></select></span><a
					class="vc_control-btn vc_control-btn-delete" href="#"
					title="<?php printf( esc_attr__( 'Delete %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"
					data-vc-control="parent.destroy"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-trash"></i></span></a>{# } #}</span>
			</div>
			<div class="vc_element element-{{ tag }} vc_active"><a
				class="vc_control-btn vc_element-name"
				title="{# if( can_all && moveAccess ) { #}<?php printf(
					esc_attr__( 'Drag to move %s', 'js_composer' ),
					'{{ name }}'
				); ?>{# } #}">{# if( can_all && moveAccess ) { #}<span
					class="vc_btn-content">
					{{ name }}</span>{# } else { #}<span
					class="vc_btn-content">
					{{ name }}</span>{# } #}</a><span class="vc_advanced">{# if( can_edit ) { #}<a
					class="vc_control-btn vc_control-btn-edit" href="#"
					title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ name_lower }}' ); ?>"
					data-vc-control="edit"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( 'edit' !== state ) { #}<a
					class="vc_control-btn vc_control-btn-prepend" href="#"
					title="<?php printf( esc_attr__( 'Prepend to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"
					data-vc-control="prepend"><span
						class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a><span class="vc_control-btn vc_control-btn-copypaste" title="<?php esc_attr_e( 'Copy and paste', 'js_composer' ); ?>"><select
					class="vc_copypaste-select" data-placeholder="&hellip;"><option></option><option
						value="copy" title="<?php esc_attr_e( 'Copy element', 'js_composer' ); ?>"><?php esc_html_e( 'Copy element', 'js_composer' ); ?></option><option
						value="paste" title="<?php esc_attr_e( 'Paste element', 'js_composer' ); ?>"><?php esc_html_e( 'Paste element', 'js_composer' ); ?></option></select></span>
				{# } #}</span>
			</div>
		</div>
	</div><!-- end vc_controls-vc_tta_section -->
</<?php echo esc_attr( $custom_tag ); ?>>
<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_controls-template-vc_container_item">
<div class="vc_controls-column">
    <div class="vc_controls-out-tl">
        <div class="vc_parent parent-{{ parent_tag }}"><a
                    class="vc_control-btn vc_element-name{# if( parent_can_all && moveAccess ) { #} vc_element-move vc_move-{{ parent_tag }}{# } #}"
                    title="{# if( parent_can_all && moveAccess ) { #}<?php printf(
						esc_attr__( 'Drag to move %s', 'js_composer' ),
						'{{ parent_name }}'
					); ?>{# } #}">{# if( parent_can_all && moveAccess ) { #}<span
                        class="vc_btn-content">
					<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>
					{{ parent_name }}</span>{# } else { #}<span
                        class="vc_btn-content">
					{{ parent_name }}</span>{# } #}</a><span class="vc_advanced">{# if( parent_can_edit ) { #}<a
                        class="vc_control-btn vc_control-btn-edit vc_edit" href="#"
                        title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
                            class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( allowAdd ) { #}<a
                        class="vc_control-btn vc_control-btn-prepend vc_edit" href="#"
                        title="<?php printf( esc_attr__( 'Add new %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
                            class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>{# } #}{# if( parent_can_all ) { #}<a
                        class="vc_control-btn vc_control-btn-clone" href="#"
                        title="<?php printf( esc_attr__( 'Clone %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
                            class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-clone"></i></span></a><span class="vc_control-btn vc_control-btn-copypaste" title="<?php esc_attr_e( 'Copy and paste', 'js_composer' ); ?>"><select
                        class="vc_copypaste-select" data-placeholder="&hellip;"><option></option><option
                            value="copy" title="<?php esc_attr_e( 'Copy element', 'js_composer' ); ?>"><?php esc_html_e( 'Copy element', 'js_composer' ); ?></option><option
                            value="paste" title="<?php esc_attr_e( 'Paste element', 'js_composer' ); ?>"><?php esc_html_e( 'Paste element', 'js_composer' ); ?></option></select></span><a
                        class="vc_control-btn vc_control-btn-delete" href="#"
                        title="<?php printf( esc_attr__( 'Delete %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
                            class="vc_btn-content"><i class="vc-composer-icon vc-c-trash"></i></span></a>{# } #}</span>
        </div>
        <div class="vc_element element-{{ tag }} vc_active"><a
                    class="vc_control-btn vc_element-name{# if( can_all && moveAccess ) { #} vc_element-move vc_move-vc_column{# } #}"
                    title="{# if( can_all && moveAccess ) { #}<?php printf(
						esc_attr__( 'Drag to move %s', 'js_composer' ),
						'{{ name }}'
					); ?>{# } #}">{# if( can_all && moveAccess ) { #}<span
                        class="vc_btn-content">
					<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>
					{{ name }}</span>{# } else { #}<span
                        class="vc_btn-content">
					{{ name }}</span>{# } #}</a><span class="vc_advanced">{# if( can_edit ) { #}<a
                        class="vc_control-btn vc_control-btn-edit" href="#"
                        title="<?php printf( esc_attr__( 'Edit %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
                            class="vc_btn-content"><i class="vc-composer-icon vc-c-edit"></i></span></a>{# } #}{# if( 'edit' !== state ) { #}<a
                        class="vc_control-btn vc_control-btn-prepend" href="#"
                        title="<?php printf( esc_attr__( 'Prepend to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
                            class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>{# } #}{# if( can_all ) { #}<a
                        class="vc_control-btn vc_control-btn-clone" href="#"
                        title="<?php printf( esc_attr__( 'Clone %s', 'js_composer' ), '{{ parent_name_lower }}' ); ?>"><span
                            class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-clone"></i></span></a><span class="vc_control-btn vc_control-btn-copypaste" title="<?php esc_attr_e( 'Copy and paste', 'js_composer' ); ?>"><select
                        class="vc_copypaste-select" data-placeholder="&hellip;"><option></option><option
                            value="copy" title="<?php esc_attr_e( 'Copy element', 'js_composer' ); ?>"><?php esc_html_e( 'Copy element', 'js_composer' ); ?></option><option
                            value="paste" title="<?php esc_attr_e( 'Paste element', 'js_composer' ); ?>"><?php esc_html_e( 'Paste element', 'js_composer' ); ?></option></select></span><a
                        class="vc_control-btn vc_control-btn-delete" href="#"
                        title="<?php printf( esc_attr__( 'Delete %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
                            class="vc_btn-content"><i class="vc-composer-icon vc-c-trash"></i></span></a>{# } #}"></span>
        </div>
    </div>
    {# if( 'edit' !== state ) { #}
    <div class="vc_controls-bc">
        <a class="vc_control-btn vc_control-btn-append" href="#"
           title="<?php printf( esc_attr__( 'Append to %s', 'js_composer' ), '{{ name_lower }}' ); ?>"><span
                    class="vc_btn-content"><i class="vc-composer-icon vc-c-add-circle"></i></span></a>
    </div>
    {# } #}
</div><!-- end vc_controls-vc_container_item -->
</<?php echo esc_attr( $custom_tag ); ?>>
