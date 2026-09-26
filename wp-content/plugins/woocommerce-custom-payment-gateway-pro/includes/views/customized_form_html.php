<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="woocommerce_auspost_debug_mode">Custom Form</label>
	</th>
	<td class="forminp">
		<fieldset>
			<div id="custom_payment_form_components">
				<ul class="form_components_col1">
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="text" id="form-element-text">Text</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="password" id="form-element-password">Password</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="checkbox" id="form-element-checkbox">Checkbox</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="signature" id="form-element-username">Customer Signature</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="select" id="form-element-select">Select</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="date" id="form-element-datepicker">Date</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="url" id="form-element-url">URL</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="number" id="form-element-digits">Number</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="ccform" id="form-element-file">CCard Form</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="textarea" id="form-element-textarea">Textarea</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="radio" id="form-element-radio">Radio</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="email" id="form-element-email">Email</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="time" id="form-element-time">Time</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="currency" id="form-element-currency">Currency</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="phone" id="form-element-phone">Phone</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="instructions" id="form-element-instructions">Instructions</a></li>
				</ul>
			</div>
			<div id="custom_payment_form_fields">
				<ul id="fields_wrap">
					<?php
					$current_field = 1;
					if(is_array($this->customized_form)){
						foreach($this->customized_form as $key => $field): ?>
							<?php
							echo $this->render_field($field, $key, $current_field);
							$current_field++;
							?>
						<?php
						endforeach;
					}
					?>
				</ul>
			</div>
		</fieldset>
	</td>
</tr>
<script type="text/javascript">
  var fields_counter = <?php echo (!$this->customized_form)?0:max((array_keys($this->customized_form))); ?>;
</script>