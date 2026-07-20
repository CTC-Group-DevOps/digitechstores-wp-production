<?php

declare (strict_types=1);
namespace FCFProVendor\WPDesk\License\LicenseServer;

use FCFProVendor\Psr\Log\LoggerInterface;
use FCFProVendor\Psr\Log\LoggerTrait;
/**
 * Dummy implementation of LoggerInterface.
 */
class DummyLogger implements LoggerInterface
{
    use LoggerTrait;
    public function log($level, $message, array $context = [])
    {
        error_log('wpdesk.license ' . $level . ': ' . $message);
    }
}
