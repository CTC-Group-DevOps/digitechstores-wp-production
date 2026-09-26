<?php
/**
 * Param heading template.
 *
 * @var array $param
 * @var string $param_id
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( isset( $param['heading'] ) ) :
	?>
	<div class="wpb-param-heading">
		<label for="<?php echo esc_attr( wpbakery()->editForm()->get_value_control_id( $param_id, $param['type'] ) ); ?>" class="wpb_element_label"><?php echo wp_kses_post( $param['heading'] ); ?></label>
		<?php
		if ( isset( $param['description'] ) ) :
			vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => $param['description'] ] );
		endif;
		?>
	</div>
	<?php
else :
	if ( isset( $param['description'] ) ) :
		vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => $param['description'] ] );
	endif;
endif;
