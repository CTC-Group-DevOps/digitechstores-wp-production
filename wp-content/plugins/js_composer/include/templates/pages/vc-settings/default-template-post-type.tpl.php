<?php
/**
 * Default post type settings template.
 *
 * @var string $title
 * @var array $post_types
 * @var array $templates
 * @var string $field_key
 * @var array $value
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<fieldset>
	<legend class="screen-reader-text">
		<span><?php echo esc_html( $title ); ?></span>
	</legend>
	<table class="vc_general vc_wp-form-table fixed">
		<thead>
		<tr>
			<th><?php esc_html_e( 'Post type', 'js_composer' ); ?></th>
			<th><?php esc_html_e( 'Template', 'js_composer' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $post_types as $post_type ) : ?>
			<?php $post_type_object = get_post_type_object( $post_type[0] ); ?>
			<tr>
				<td title="<?php echo esc_attr( $post_type[0] ); ?>">
					<?php echo esc_html( $post_type_object ? $post_type_object->labels->name : $post_type[0] ); ?>
				</td>
				<td>
					<?php
					$options = [];
					$options[] = [
						'label' => esc_html__( 'None', 'js_composer' ),
						'value' => '',
					];
					foreach ( $templates as $templates_category ) :
						$group_label = $templates_category['category_name'];
						$options_list = [];
						foreach ( $templates_category['templates'] as $template ) :
							$key = $template['type'] . '::' . $template['unique_id'];
							$options_list[] = [
								'value' => $key,
								'label' => $template['name'],
								'selected' => isset( $value[ $post_type[0] ] ) && $value[ $post_type[0] ] === $key,
							];
						endforeach;
						$options[] = [
							'label' => $group_label,
							'value' => $options_list,
						];
					endforeach;

					WPB_Form_Field_Dropdown::render( [
						'name' => $field_key . '[' . $post_type[0] . ']',
						'options' => $options,
					] );
					?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</fieldset>
