<?php

namespace DpdUKVendor\WPDesk\Helper\Integration;

use DpdUKVendor\WPDesk\Helper\Page\SettingsPage;
use DpdUKVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use DpdUKVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use DpdUKVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
/**
 * Integrates WP Desk main settings page with WordPress
 *
 * @package WPDesk\Helper
 */
class SettingsIntegration implements \DpdUKVendor\WPDesk\PluginBuilder\Plugin\Hookable, \DpdUKVendor\WPDesk\PluginBuilder\Plugin\HookableCollection
{
    use HookableParent;
    /** @var SettingsPage */
    private $settings_page;
    public function __construct(\DpdUKVendor\WPDesk\Helper\Page\SettingsPage $settingsPage)
    {
        $this->add_hookable($settingsPage);
    }
    /**
     * @return void
     */
    public function hooks()
    {
        $this->hooks_on_hookable_objects();
    }
}
