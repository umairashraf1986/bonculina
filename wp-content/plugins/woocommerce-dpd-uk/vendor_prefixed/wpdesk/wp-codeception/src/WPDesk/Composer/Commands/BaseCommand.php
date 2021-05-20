<?php

namespace DpdUKVendor\WPDesk\Composer\Codeception\Commands;

use DpdUKVendor\Composer\Command\BaseCommand as CodeceptionBaseCommand;
use DpdUKVendor\Symfony\Component\Console\Output\OutputInterface;
/**
 * Base for commands - declares common methods.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
abstract class BaseCommand extends \DpdUKVendor\Composer\Command\BaseCommand
{
    /**
     * @param string $command
     * @param OutputInterface $output
     */
    protected function execAndOutput($command, \DpdUKVendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        \passthru($command);
    }
}
