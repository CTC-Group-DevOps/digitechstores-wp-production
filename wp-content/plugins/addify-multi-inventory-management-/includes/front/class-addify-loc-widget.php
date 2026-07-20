<?php
defined('ABSPATH') || exit;

class Addify_Loc_Widget extends WP_Widget {



	public function __construct() {

		parent::__construct(
			'Addify_Loc_Widget', // Base ID of your widget.
			esc_html__('Location', 'addify-multi-inventory-management'), // Widget name will appear in UI.
			array( 'description' => esc_html__('Location widget based on Location filters', 'addify-multi-inventory-management') )
		);
	}

	/** Output widget data. */
	public function widget( $args, $instance ) {

		if (!is_shop() || !is_archive() || is_single()) {

			return;
		}

		$af_location_title = isset($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';

		$before_widgets = isset($args['before_widget']) ? $args['before_widget'] : '';

		$after_widgets = isset($args['after_widget']) ? $args['after_widget'] : '';

		$before_title = isset($args['before_title']) ? $args['before_title'] : '';

		$after_title = isset($args['after_title']) ? $args['after_title'] : '';

		echo wp_kses_post($before_widgets);

		if (wc()->session) {

			$af_loc_widget = (array) wc()->session->get('af_user_selected_location');

			$af_show_all_loc = !empty($instance['af_show_all_loc']) ? $instance['af_show_all_loc'] : false;

			?>

			<div class="widget-text wp_widget_plugin_box" id="mycustomlink">

				<form method="post">

					<?php

					if (1 == $af_show_all_loc) {

						if ($af_location_title) {

							echo wp_kses_post($before_title . $af_location_title . $after_title);
						}

						$af_selected_locations = af_mli_get_available_location();

						wp_nonce_field('af_loc_nonce', 'af_loc_nonce_field');
						if (!empty($af_selected_locations)) {

							wp_nonce_field('af_loc_nonce', 'af_loc_nonce_field');

							?>

							<select name="af_loc_widget[]" <?php echo esc_attr('single' != get_option('af_mli_show_select_location_dropdown_as') ? 'multiple' : ''); ?> class="af_loc_widget"
								style="width: 100%">
								<?php
								foreach ($af_selected_locations as $af_created_location) {
									if ($af_created_location) {
										?>
										<option value="<?php echo esc_attr($af_created_location->term_id); ?>" 
																	<?php
																	if (in_array($af_created_location->term_id, $af_loc_widget)) :
																		?>
												selected <?php endif ?>>
											<?php echo esc_html($af_created_location->name); ?>
										</option>
										<?php
									}
								}
								?>

							</select>
							<?php
						}
						?>
						<input type="submit" name="af_search_btn" value="Proceed" style="margin-top: 13px;">
						<input type="submit" name="af_clear_location" value="Reset" style="margin-top: 13px;">
						<?php
					}

					?>

				</form>

			</div>

			<?php
		}

		echo wp_kses_post($after_widgets);
	}

	/** Backend widget form data. */
	public function form( $instance ) {

		$af_location_title = isset($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';

		$af_show_all_loc = isset($instance['af_show_all_loc']) ? $instance['af_show_all_loc'] : 0;

		$af_loc_widget = isset($instance['af_loc_widget']) ? $instance['af_loc_widget'] : array();

		?>

		<div>

			<p>

				<label
					for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Title', 'addify-multi-inventory-management'); ?></label>

				<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
					name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
					value="<?php echo esc_attr($af_location_title); ?>" />

			</p>

			<p>

				<input name="<?php echo esc_attr($this->get_field_name('af_show_all_loc')); ?>"
					id="<?php echo esc_attr($this->get_field_id('af_show_all_loc')); ?>" type="checkbox" <?php checked('1', $instance['af_show_all_loc']); ?> />

				<label><?php echo esc_html('show all locations'); ?></label>

			</p>

		</div>

		<?php
	}

	/** Update widget data */
	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title'] = isset($new_instance['title']) ? wp_strip_all_tags($new_instance['title']) : '';

		$instance['af_show_all_loc'] = empty($new_instance['af_show_all_loc']) ? 0 : 1;

		return $instance;
	}
}

new Addify_Loc_Widget();

add_action(
	'widgets_init',
	function () {

		register_widget('Addify_Loc_Widget');
	}
);