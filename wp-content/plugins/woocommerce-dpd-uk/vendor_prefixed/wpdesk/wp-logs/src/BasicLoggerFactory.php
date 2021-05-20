<?php

namespace DpdUKVendor\WPDesk\Logger;

use DpdUKVendor\Monolog\Handler\HandlerInterface;
use DpdUKVendor\Monolog\Logger;
use DpdUKVendor\Monolog\Registry;
/**
 * Manages and facilitates creation of logger
 *
 * @package WPDesk\Logger
 */
class BasicLoggerFactory implements \DpdUKVendor\WPDesk\Logger\LoggerFactory
{
    /** @var string Last created logger name/channel */
    private static $lastLoggerChannel;
    /**
     * Creates logger for plugin
     *
     * @param string $name The logging channel/name of logger
     * @param HandlerInterface[] $handlers Optional stack of handlers, the first one in the array is called first, etc.
     * @param callable[] $processors Optional array of processors
     * @return Logger
     */
    public function createLogger($name, $handlers = array(), array $processors = array())
    {
        if (\DpdUKVendor\Monolog\Registry::hasLogger($name)) {
            return \DpdUKVendor\Monolog\Registry::getInstance($name);
        }
        self::$lastLoggerChannel = $name;
        $logger = new \DpdUKVendor\Monolog\Logger($name, $handlers, $processors);
        \DpdUKVendor\Monolog\Registry::addLogger($logger);
        return $logger;
    }
    /**
     * Returns created Logger by name or last created logger
     *
     * @param string $name Name of the logger
     *
     * @return Logger
     */
    public function getLogger($name = null)
    {
        if ($name === null) {
            $name = self::$lastLoggerChannel;
        }
        return \DpdUKVendor\Monolog\Registry::getInstance($name);
    }
}
