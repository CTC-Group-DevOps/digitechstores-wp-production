<?php
global $post;

$settings = get_post_meta($post->ID, '_wccc_settings', true);
if (!$settings) {
	$settings = array('location' => 'single-product', 'hook' => 'woocommerce_template_single_excerpt');
}

$locations = apply_filters('wc_conditional_content_get_locations', array());

if (!isset($settings['type'])) {
	$settings['type'] = 'single';
}

if (!isset($settings['accepted_args'])) {
    $settings['accepted_args'] = 1;
}

?>

<h4><?php esc_html_e('Show Content', 'wc_conditional_content'); ?></h4>

<label for="wccc_settings_type"><?php esc_html_e('Type:', 'wc_conditional_content'); ?></label><br />
<select name="wccc_settings_type" id="wccc_settings_type">
	<option value="single" <?php selected($settings['type'], 'single'); ?>><?php esc_html_e('Once', 'wc_conditional_content'); ?></option>
	<option value="loop" <?php selected($settings['type'], 'loop'); ?>><?php esc_html_e('In Loop', 'wc_conditional_content'); ?></option>
</select>
<p class="description"><?php esc_html_e('Is this content displayed in a loop?.', 'wc_conditional_content'); ?></p>

<label for="wccc_settings_location"><?php esc_html_e('Location:', 'wc_conditional_content'); ?></label><br />
<select name="wccc_settings_location" id="wccc_settings_location">

	<?php foreach ($locations as $location => $data) : ?>
		<optgroup label="<?php echo esc_attr($data['title']); ?>">
			<?php foreach ($data['hooks'] as $hook_id => $hook) : ?>
				<option <?php selected($location . ':' . $hook_id, $settings['location'] . ':' . $settings['hook']) ?> value="<?php echo esc_attr($location . ':' . $hook_id); ?>"><?php echo esc_html($hook['title']); ?></option>
			<?php endforeach; ?>
		</optgroup>
	<?php endforeach; ?>

	<optgroup label="<?php esc_attr_e('Custom', 'wc_conditional_content'); ?>">
		<option <?php selected('custom', $settings['hook']); ?> value="custom:custom"><?php esc_html_e('Custom Action', 'wc_conditional_content'); ?></option>
	</optgroup>
</select>


<p class="description"><?php esc_html_e('Choose where you would like this content to be displayed.', 'wc_conditional_content'); ?></p>




<div class="wccc-settings-custom">

    <label for="wccc_settings_location_custom_hook"><?php esc_html_e( 'Action Hook Name', 'wc_conditional_content' ); ?></label>
    <br />
    <input type="text" name="wccc_settings_location_custom_hook" value="<?php echo esc_attr( $settings['hook'] == 'custom' ? $settings['custom_hook'] : '' ); ?>" />

    <br />
    <label for="wccc_settings_location_custom_priority"><?php esc_html_e( 'Priority', 'wc_conditional_content' ); ?></label>
    <br />
    <input type="text" name="wccc_settings_location_custom_priority" value="<?php echo esc_attr( $settings['hook'] == 'custom' ? $settings['custom_priority'] : '' ); ?>" />
    <p class="description">
        <?php
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        printf(
        /* translators: %s: documentation URL */
                wp_kses_post( __( 'Enter the name and priority of an action where this content should be output. See the <a href="%s">woocommerce hooks and filter reference</a> for a full list of all template actions and filters.', 'wc_conditional_content' ) ),
                esc_url( 'https://docs.woocommerce.com/document/introduction-to-hooks-actions-and-filters/' )
        );
        ?>
    </p>

    <br />
    <label for="wccc_settings_location_custom_accepted_args"><?php esc_html_e( 'Accepted Function Args', 'wc_conditional_content' ); ?></label>
    <br />
    <input type="number" name="wccc_settings_location_custom_accepted_args" value="<?php echo esc_attr( $settings['hook'] == 'custom' ? ( empty( $settings['custom_accepted_args'] ) ? '1' : $settings['custom_accepted_args'] ) : '0' ); ?>" />
    <p class="description"><?php esc_html_e( 'Optional. The number of arguments the function accepts. Default 0.', 'wc_conditional_content' ); ?></p>
</div>
