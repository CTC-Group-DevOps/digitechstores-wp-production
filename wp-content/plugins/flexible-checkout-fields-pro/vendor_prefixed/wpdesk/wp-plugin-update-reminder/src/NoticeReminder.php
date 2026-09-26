<?php

namespace FCFProVendor\WPDesk\Library\PluginUpdateReminder;

use FCFProVendor\WPDesk\Notice\Notice;
use FCFProVendor\WPDesk\Notice\PermanentDismissibleNotice;
use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class NoticeReminder implements Reminder, Hookable
{
    private ReminderData $reminder_data;
    public function create_reminder(ReminderData $reminder_data): void
    {
        $this->reminder_data = $reminder_data;
        $this->hooks();
    }
    public function hooks()
    {
        add_filter('wpdesk_plugin_update_reminders', [$this, 'register_reminder_data']);
        add_action('admin_notices', [$this, 'maybe_display_aggregated_notice']);
    }
    public function register_reminder_data(array $reminders): array
    {
        if ($this->reminder_data->is_major_update() || $this->reminder_data->is_minor_update()) {
            $reminders[] = $this->reminder_data;
        }
        return $reminders;
    }
    public function maybe_display_aggregated_notice(): void
    {
        if (did_action('wpdesk_plugin_update_reminder_notice_displayed')) {
            return;
        }
        if (!$this->should_display_notice()) {
            do_action('wpdesk_plugin_update_reminder_notice_displayed');
            return;
        }
        $all_reminders = apply_filters('wpdesk_plugin_update_reminders', []);
        $major_reminders = array_values(array_filter($all_reminders, fn($r) => $r->is_major_update()));
        $minor_reminders = array_values(array_filter($all_reminders, fn($r) => $r->is_minor_update()));
        if (!empty($major_reminders)) {
            new PermanentDismissibleNotice($this->get_combined_major_content($major_reminders), $this->get_combined_notice_name($major_reminders, 'major'), Notice::NOTICE_TYPE_ERROR);
        }
        if (!empty($minor_reminders)) {
            new PermanentDismissibleNotice($this->get_combined_minor_content($minor_reminders), $this->get_combined_notice_name($minor_reminders, 'minor'), Notice::NOTICE_TYPE_WARNING);
        }
        do_action('wpdesk_plugin_update_reminder_notice_displayed');
    }
    private function get_combined_major_content(array $reminders): string
    {
        if (count($reminders) === 1) {
            return $reminders[0]->get_major_reminder_content();
        }
        return sprintf(_n('You\'re using an outdated version of the following plugin: %s This may cause serious compatibility issues with WooCommerce. Update it immediately to avoid potential disruptions.', 'You\'re using outdated versions of the following plugins: %s This may cause serious compatibility issues with WooCommerce. Update them immediately to avoid potential disruptions.', count($reminders), 'flexible-checkout-fields-pro'), $this->build_plugin_list($reminders));
    }
    private function get_combined_minor_content(array $reminders): string
    {
        if (count($reminders) === 1) {
            return $reminders[0]->get_minor_reminder_content();
        }
        return sprintf(_n('You\'re using an older version of the following plugin: %s This version hasn\'t been tested with the latest WooCommerce versions. Please update it to the latest version to ensure smooth compatibility.', 'You\'re using older versions of the following plugins: %s These versions haven\'t been tested with the latest WooCommerce versions. Please update them to the latest version to ensure smooth compatibility.', count($reminders), 'flexible-checkout-fields-pro'), $this->build_plugin_list($reminders));
    }
    private function build_plugin_list(array $reminders): string
    {
        $items = array_map(fn($r) => '<li><strong>' . esc_html($r->get_plugin_name()) . '</strong></li>', $reminders);
        return '<ul>' . implode('', $items) . '</ul>';
    }
    private function get_combined_notice_name(array $reminders, string $type): string
    {
        $key = implode('-', array_map(fn($r) => basename($r->get_plugin_dir()) . $r->get_plugin_version() . $r->get_woocommerce_version(), $reminders));
        return 'wpdesk-notice-reminder-combined-' . $type . '-' . md5($key);
    }
    private function should_display_notice(): bool
    {
        $current_screen = get_current_screen();
        return $current_screen->id !== 'site-health';
    }
}
