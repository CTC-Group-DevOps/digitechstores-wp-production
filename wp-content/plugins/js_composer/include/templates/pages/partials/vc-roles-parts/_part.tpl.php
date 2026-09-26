<?php
/**
 * User role part template.
 *
 * @var string $part
 * @var string $role
 * @var string $params_prefix
 * @var Vc_Role_Access_Controller $controller
 * @var array $options
 * @var string $main_label
 * @var string|null $description
 * @var array $capabilities
 * @var array $cap_types
 * @var string $custom_label
 * @var string|null $custom_value
 * @var string $item_header_name
 * @var array $categories
 * @var bool|null $use_table
 * @var bool|null $global_set
 * @var array|null $ignore_capabilities
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
	<tr>
		<th scope="row">
			<span><?php echo esc_html( $main_label ); ?></span>
		</th>
		<td>
			<fieldset>
				<legend class="screen-reader-text">
					<span><?php esc_html( $main_label ); ?></span>
				</legend>
				<?php
				if ( ! empty( $description ) ) {
					vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => $description ] );
				}
				$data_attributes['vc-part'] = $part;
				$data_attributes['vc-name'] = '_state';
				if ( ! empty( $capabilities ) ) {
					$data_attributes['vc-roles'] = 'part-state';
					$data_attributes['vc-role-part'] = $part . '-' . $role;
				}
				$options_list = [];
				foreach ( $options as $option_data ) {
					$options_list[] = [
						'value' => $option_data[0] ? (string) $option_data[0] : '0',
						'label' => $option_data[1],
						'selected' => $controller->getState() === $option_data[0],
						'data_attributes' => [
							'custom-selector' => $option_data[2] ?? '',
						],
					];
				}

				WPB_Form_Field_Dropdown::render( [
					'name' => $params_prefix . '[_state]',
					'data_attributes' => $data_attributes,
					'classes' => 'vc_ui-settings-roles-dropdown',
					'options' => $options_list,
				] );
				?>
			</fieldset>
		</td>
	</tr>
<?php if ( ! empty( $capabilities ) ) : ?>
	<?php if ( isset( $use_table ) && true === $use_table ) : ?>
		<?php
		require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-add-element-box.php' );
		require_once vc_path_dir( 'SHORTCODES_DIR', 'core/class-wpbakeryshortcode.php' );
		$add_box = new Vc_Add_Element_Box();
		?>
		<tr data-vc-role-related-part="<?php echo esc_attr( $part . '-' . $role ); ?>"
			data-vc-role-part-state="<?php echo esc_attr( isset( $custom_value ) ? $custom_value : '*' ); ?>"
			class="vc_role-custom-settings<?php echo ! isset( $custom_value ) || (string) $controller->getState() === (string) $custom_value ? ' vc_visible' : ''; ?>">
			<th scope="row"></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html( $custom_label ); ?></span>
					</legend>
					<?php if ( ! empty( $categories ) ) : ?>
						<?php
						vc_include_template( 'editors/partials/add_element_tabs.tpl.php', [
							'categories' => $categories,
							'box' => $add_box,
						] )
						?>
					<?php endif; ?>
					<table class="vc_general vc_wp-form-table fixed" data-vc-roles="table">
						<thead>
						<tr>
							<th><?php echo esc_html( $item_header_name ); ?></th>
							<?php foreach ( $cap_types as $type ) : ?>
								<th class="column-date">
									<?php
									WPB_Form_Field_Checkbox::render( [
										'name'            => 'all',
										'label'           => $type[1],
										'data_attributes' => [
											'vc-related-controls'       => 'tfoot [data-vc-roles-select-all-checkbox]',
											'vc-roles-select-all-checkbox' => $type[0],
										],
									] );
									?>
								</th>
							<?php endforeach; ?>
						</tr>
						</thead>
						<tfoot<?php echo isset( $global_set ) ? ' style="display: none;"' : ''; ?>>
						<tr>
							<th><?php echo esc_html( $item_header_name ); ?></th>
							<?php foreach ( $cap_types as $type ) : ?>
								<th class="column-date">
									<?php
									WPB_Form_Field_Checkbox::render( [
										'name'            => 'all',
										'label'           => $type[1],
										'data_attributes' => [
											'vc-related-controls'       => 'thead [data-vc-roles-select-all-checkbox]',
											'vc-roles-select-all-checkbox' => $type[0],
										],
									] );
									?>
								</th>
							<?php endforeach; ?>
						</tr>
						</tfoot>
						<tbody<?php echo isset( $global_set ) ? ' style="display: none;"' : ''; ?>>
						<?php foreach ( $capabilities as $cap ) : ?>
							<?php if ( ! isset( $ignore_capabilities ) || ! in_array( $cap['base'], $ignore_capabilities, true ) ) : ?>
								<?php
								$category_css_classes = '';
								if ( isset( $cap['_category_ids'] ) ) {
									foreach ( $cap['_category_ids'] as $id ) {
										$category_css_classes .= ' js-category-' . $id;
									}
								}
								?>
								<tr data-vc-capability="<?php echo esc_attr( $cap['base'] ); ?>"
									class="<?php echo esc_attr( trim( $category_css_classes ) ); ?>">
									<td title="<?php echo esc_attr( $cap['base'] ); ?>">
										<?php
										$shortcode = new class( $cap ) extends WPBakeryShortCode {};
										$shortcode->printIconStyles();
										// @codingStandardsIgnoreLine
										print $add_box->renderIcon( $cap );
										?>
										<div>
											<?php echo esc_html( $cap['name'] ); ?>
											<?php echo ! empty( $cap['description'] ) ? '<span class="vc_element-description">' . esc_html( $cap['description'] ) . '</span>' : ''; ?>
										</div>
									</td>
									<?php foreach ( $cap_types as $type ) : ?>
										<td>
											<div class="vc_wp-form-checkbox">
												<?php
												WPB_Form_Field_Checkbox::render( [
													'name' => $params_prefix . '[' . $role . '][' . $part . '][' . $cap['base'] . '_' . $type[0] . ']',
													'value' => '1',
													'label' => $type[1],
													'checked' => ! isset( $global_set ) && $controller->can( $cap['base'] . '_' . $type[0], false )->get(),
													'data_attributes' => [
														'vc-part'  => $part,
														'vc-name'  => $cap['base'] . '_' . $type[0],
														'vc-roles' => 'table-checkbox',
														'vc-cap'   => $type[0],
													],
												] );
												?>
											</div>
										</td>
									<?php endforeach; ?>
								</tr>
							<?php endif; ?>
						<?php endforeach; ?>
						</tbody>
					</table>
				</fieldset>
			</td>
		</tr>
	<?php else : ?>
		<tr data-vc-role-related-part="<?php echo esc_attr( $part . '-' . $role ); ?>" data-vc-role-part-state="<?php echo esc_attr( isset( $custom_value ) ? $custom_value : '*' ); ?>" class="vc_role-custom-settings<?php echo ! isset( $custom_value ) || $controller->getState() === $custom_value ? ' vc_visible' : ''; ?>">
			<th scope="row"></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html( $custom_label ); ?></span>
					</legend>
					<div class="vc_wp-form-row">
						<?php foreach ( $capabilities as $cap ) : ?>
							<div class="vc_wp-form-col vc_wp-form-checkbox">
								<?php
								// hard coded yes :).
								$is_admin_locked = 'administrator' === $role && 'settings' === $part && ( 'vc-roles-tab' === $cap[0] || 'vc-updater-tab' === $cap[0] );
								$data_attributes = $is_admin_locked ? [] : [
									'vc-part'  => $part,
									'vc-name'  => $cap[0],
									'vc-roles' => 'checkbox',
								];

								WPB_Form_Field_Checkbox::render( [
									'name'             => $params_prefix . '[' . $cap[0] . ']',
									'value'            => '1',
									'label'            => $cap[1],
									'checked'          => $is_admin_locked || $controller->can( $cap[0], false )->get(),
									'disabled'         => $is_admin_locked,
									'input_attr_class' => 'class="vc_roles-settings-checkbox"',
									'data_attributes'  => $data_attributes,
								] );
								?>
							</div>
						<?php endforeach; ?>
					</div>
				</fieldset>
			</td>
		</tr>
	<?php endif; ?>
<?php endif; ?>
