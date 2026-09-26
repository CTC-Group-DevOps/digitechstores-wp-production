<?php

namespace FCFProVendor\WPDesk\Library\PluginUpdateReminder;

interface Reminder
{
    public function create_reminder(ReminderData $reminder_data): void;
}
