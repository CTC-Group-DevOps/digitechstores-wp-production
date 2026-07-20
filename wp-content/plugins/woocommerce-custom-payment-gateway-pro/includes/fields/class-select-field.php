<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Select_Field extends Custom_Payment_Field {

	public function get_name() {
		return 'Select';
	}

	public function get_html() {
		// $this->get_options() was $field['elements']['options']['value']
		$html = '<select id="'.$this->get_id().'" name="'.$this->get_id().'">';

		foreach($this->get_options() as $option){
			foreach ($this->get_expressions_values() as $expression => $value){
				try {
					$value = str_replace($expression, $value, $option);
					eval('$content = ('. $value .');');
				} catch (Throwable $t) {
					$content = $option;
				}
			}
			$html .= '<option '. selected($this->get_default_value(), $option, false) .' value="'.$content.'">'.$content.'</option>';
		}
		$html .= '</select>';
		return $html;
	}
}