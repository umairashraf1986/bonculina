<?php

namespace DpdUKVendor\WPDesk\PluginBuilder\Storage;

class StorageFactory
{
    /**
     * @return PluginStorage
     */
    public function create_storage()
    {
        return new \DpdUKVendor\WPDesk\PluginBuilder\Storage\WordpressFilterStorage();
    }
}
