<?php

declare (strict_types=1);
namespace FCFProVendor\WPDesk\License\LicenseServer;

/**
 * Renders license server notices as WordPress admin notices.
 */
final class AdminNotice
{
    private const NOTICE_WRAPPER = '<div class="notice notice-warning"><p>%s</p></div>';
    private NoticeCache $cache;
    public function __construct(NoticeCache $cache)
    {
        $this->cache = $cache;
    }
    /**
     * @internal
     */
    public function display(): void
    {
        $notice_data = $this->cache->get();
        if (null === $notice_data || empty($notice_data['message'])) {
            return;
        }
        $message = wp_kses_post($notice_data['message']);
        printf(self::NOTICE_WRAPPER, $message);
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    public function hooks(): void
    {
        add_action('admin_notices', [$this, 'display']);
    }
}
