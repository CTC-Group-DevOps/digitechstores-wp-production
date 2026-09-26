<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_auspost_debug_mode"><?php _e('HTTP Headers', 'woocommerce-custom-payment-gateway'); ?></label>
    </th>
    <td class="forminp">
        <fieldset>
            <table id="customized_payment_api_request_headers" class="wp-list-table widefat fixed striped posts">
                <thead>
                <tr>
                    <th class="column-key"><?php _e('Key', 'woocommerce-custom-payment-gateway'); ?></th>
                    <th><?php _e('Value', 'woocommerce-custom-payment-gateway'); ?></th>
                    <th class="column-featured"></th>
                </tr>
                </thead>
                <tbody>

				<?php if(isset($this->api_request_headers) and is_array($this->api_request_headers)): ?>
					<?php foreach($this->api_request_headers as $key => $value): ?>
                        <tr>
                            <td><input name="header_keys[]" class="widefat" value="<?php echo esc_attr($key); ?>" type="text"></td>
                            <td><input name="header_values[]" class="widefat" value="<?php echo esc_attr($value); ?>" type="text"></td>
                            <td><a class="delete_header_key" href="javascript:void(0);"><span class="dashicons  dashicons-trash"></span></a></td>
                        </tr>
					<?php endforeach; ?>
				<?php endif; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="3"><a id="add_row_api_request_headers_button" href="javascript:void(0);" class="button"><?php _e('Add Row', 'woocommerce-custom-payment-gateway'); ?></a></th>
                </tr>
                </tfoot>
            </table>
        </fieldset>
    </td>
</tr>
