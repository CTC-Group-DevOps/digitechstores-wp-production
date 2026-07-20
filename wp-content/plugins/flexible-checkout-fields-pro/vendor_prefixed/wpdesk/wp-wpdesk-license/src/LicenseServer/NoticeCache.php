<?php

declare (strict_types=1);
namespace FCFProVendor\WPDesk\License\LicenseServer;

/**
 * Cache layer for license server notices.
 * Stores notice data in WordPress transients with configurable TTL.
 */
final class NoticeCache
{
    private const CACHE_KEY_PREFIX = 'wpdesk_license_notice_';
    private const DEFAULT_TTL = 43200;
    private string $plugin_slug;
    public function __construct(string $plugin_slug)
    {
        $this->plugin_slug = $plugin_slug;
    }
    public function get(): ?array
    {
        $cached = get_transient($this->get_cache_key());
        if (\false === $cached || !is_array($cached)) {
            return null;
        }
        return $cached;
    }
    public function set(array $notice_data, int $ttl = self::DEFAULT_TTL): bool
    {
        return set_transient($this->get_cache_key(), $notice_data, $ttl);
    }
    public function delete(): bool
    {
        return delete_transient($this->get_cache_key());
    }
    private function get_cache_key(): string
    {
        return self::CACHE_KEY_PREFIX . $this->plugin_slug;
    }
}
