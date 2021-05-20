<?php

namespace DpdUKVendor\WPDesk\View\Resolver;

use DpdUKVendor\WPDesk\View\Renderer\Renderer;
use DpdUKVendor\WPDesk\View\Resolver\Exception\CanNotResolve;
/**
 * This resolver never finds the file
 *
 * @package WPDesk\View\Resolver
 */
class NullResolver implements \DpdUKVendor\WPDesk\View\Resolver\Resolver
{
    public function resolve($name, \DpdUKVendor\WPDesk\View\Renderer\Renderer $renderer = null)
    {
        throw new \DpdUKVendor\WPDesk\View\Resolver\Exception\CanNotResolve("Null Cannot resolve");
    }
}
