<?php

defined('ABSPATH') || exit;

class Af_Mli_Shop_Manager_Email extends WC_Email {



	public function __construct() {

		$this->id = 'af_mli_shop_m_email'; // Unique ID to Store Emails Settings.

		$this->title = esc_html__('Shop Manager Order Email', 'addify-multi-inventory-management'); // Title of email to show in Settings.

		$this->customer_email = true; // Set true for customer email and false for admin email.

		$this->description = esc_html__('This will help shop manager to let know when order is placed from his/her location', 'addify-multi-inventory-management'); // description of email.

		$this->template_base = AFMLI_PLUGIN_DIR; // Base directory of template.

		$this->template_html = 'includes/admin/email/af-mli-shop-manager-email-html.php'; // HTML template path.

		$this->template_plain = 'includes/admin/email/af-mli-shop-manager-email-plain.php'; // Plain template path.

		$this->placeholders = array(

			'{inventory_additional_content}' => '',

			'{order_user_email}'             => '',

			'{product_name}'                 => '',

			'{product_quantity}'             => '',

			'{product_location}'             => '',

		);

		// Call to the  parent constructor.

		parent::__construct(); // Must call constructor of parent class.

		// Trigger function  for this customer email cancelled order.

		add_action('addify_mli_email_notification', array( $this, 'trigger' ), 10, 5); // action hook(s) to trigger email.
	}

	/** Step 3: change default subject of email by overriding the parent class method. */
	public function get_default_subject() {

		return esc_html__('Subject for Email', 'addify-multi-inventory-management');
	}

	/** Change default heading of email by overriding the parent class method */
	public function get_default_heading() {

		return esc_html__('Heading for Email', 'addify-multi-inventory-management');
	}

	/** Must over ride trigger method to replace your placeholders and send email */
	public function trigger( $client, $order_user_email, $p_name, $p_quantity, $p_location ) {

		if (empty($client)) {
			return;
		}
		$this->setup_locale();

		$this->placeholders['{order_user_email}'] = $order_user_email;

		$this->placeholders['{product_name}'] = $p_name;

		$this->placeholders['{product_quantity}'] = $p_quantity;

		$this->placeholders['{product_location}'] = $p_location;

		if ($this->is_enabled()) {

			$this->send($client, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
		}

		$this->restore_locale();
	}

	/** Override the get_content_html method to add your template of html email */
	public function get_content_html() {

		return wc_get_template_html(
			$this->template_html,
			array(

				'member'             => $this->object,

				'email_heading'      => $this->get_heading(),

				'additional_content' => $this->get_additional_content(),

				'sent_to_admin'      => false,

				'plain_text'         => false,

				'email'              => $this,
			),
			$this->template_base,
			$this->template_base
		);
	}

	// Note: Path and default path in wc_get_template_html() can be defined as, path is defined path to over-ride email template and default path is path to your plugin template.
	// Read more about wc_get_template and wc_locate_template() to understand the over-riding templates in WooCommerce.

	/** Override the get_content_plain method to add your template of plain email */
	public function get_content_plain() {

		return wc_get_template_html(
			$this->template_html,
			array(

				'member'             => $this->object,

				'email_heading'      => $this->get_heading(),

				'additional_content' => $this->get_additional_content(),

				'sent_to_admin'      => false,

				'plain_text'         => false,

				'email'              => $this,
			),
			$this->template_base,
			$this->template_base
		);
	}
}
