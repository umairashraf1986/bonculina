<?php

namespace DpdUKVendor\WPDesk\Composer\Codeception;

use DpdUKVendor\Composer\Composer;
use DpdUKVendor\Composer\IO\IOInterface;
use DpdUKVendor\Composer\Plugin\Capable;
use DpdUKVendor\Composer\Plugin\PluginInterface;
/**
 * Composer plugin.
 *
 * @package WPDesk\Composer\Codeception
 */
class Plugin implements \DpdUKVendor\Composer\Plugin\PluginInterface, \DpdUKVendor\Composer\Plugin\Capable
{
    /**
     * @var Composer
     */
    private $composer;
    /**
     * @var IOInterface
     */
    private $io;
    public function activate(\DpdUKVendor\Composer\Composer $composer, \DpdUKVendor\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    public function getCapabilities()
    {
        return [\DpdUKVendor\Composer\Plugin\Capability\CommandProvider::class => \DpdUKVendor\WPDesk\Composer\Codeception\CommandProvider::class];
    }
}
