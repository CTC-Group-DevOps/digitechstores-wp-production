<?php

class WC_Conditional_Content_Input_Chosen_Select extends WC_Conditional_Content_Input_Base {

    public function __construct() {
        // vars
        $this->type = 'Chosen_Select';

        $this->defaults = array(
            'multiple' => 0,
            'allow_null' => 0,
            'choices' => array(),
            'default_value' => array(),
            'class' => ''
        );
    }

    public function render($field, $value = null): void {

        $field = array_merge($this->defaults, $field);
        if (!isset($field['id'])) {
            $field['id'] = sanitize_title($field['id']);
        }

        $current = $value ?: array();
        $choices = $field['choices'];
        ?>

        <select aria-label="<?php esc_attr_e('Select Product', 'wc_conditional_content'); ?>" id="<?php echo esc_attr( $field['id']); ?>" name="<?php  echo esc_attr( $field['name']); ?>[]" class="wc-enhanced-select <?php echo esc_attr($field['class']); ?>" multiple="multiple" data-placeholder="<?php esc_attr_e( $field['placeholder'] ? 'Search...' : '', 'wc_conditional_content' ); ?>">
            <?php
            foreach ($choices as $choice => $title) {
                $selected = in_array($choice, $current);
                echo '<option value="' . esc_attr($choice) . '" ' . selected($selected, true, false) . '>' . esc_html($title) . '</option>';
            }
            ?>
        </select>

        <?php
    }

}
?>
