<?php

namespace DpdUKVendor\WPDesk\Composer\Codeception;

use DpdUKVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests;
use DpdUKVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests;
/**
 * Links plugin commands handlers to composer.
 */
class CommandProvider implements \DpdUKVendor\Composer\Plugin\Capability\CommandProvider
{
    public function getCommands()
    {
        return [new \DpdUKVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests(), new \DpdUKVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests()];
    }
}
