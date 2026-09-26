<?php
/**
 * Module toggle template
 *
 * @var array $module_data
 * @var string $module_slug
 * @var bool $is_module_active
 */

?>

<div class="wpb-module-wrapper">
	<?php
	WPB_Form_Field_Toggle::render( [
		'id'      => $module_slug,
		'classes' => 'wpb_toggle-input module-toggle',
		'is_checked' => $is_module_active,
		'title'    => $module_data['name'],
	] );
	?>
</div>
