<?php

namespace Zippy_Core;

use Zippy_Core\Settings\Routes\Setting_Routes;
use Zippy_Core\Settings\Services\Order_Setting_Services;
use Zippy_Core\Settings\Services\Setting_Services;

class Core_Settings extends Core_Module
{

    public function load_required_files()
    {
        $paths = [
            __DIR__ . '/controllers',
            __DIR__ . '/routes',
            __DIR__ . '/services',
            __DIR__ . '/models',
        ];

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            foreach (glob($path . '/*.php') as $file) {
                require_once $file;
            }
        }
    }

    public function init_module()
    {
        Setting_Routes::get_instance();

        $this->init_required_options();
        
        add_action('admin_menu', [$this, 'register_settings_page']);
    }

    public function init_required_options () 
    {
        Setting_Services::init_modules_option();
        Order_Setting_Services::init_invoices_option();
    }

    public function register_settings_page()
    {
        add_menu_page(
            'Core Settings',          // Page title
            'Core Settings',          // Menu title
            'manage_options',          // Capability
            'core-settings',          // Menu slug
            [$this, 'render_settings_page'], // Callback function
            'dashicons-admin-generic', // Icon
            3                          // Position
        );

        add_submenu_page(
            'core-settings',          // Parent slug
            'Orders Settings',        // Page title
            'Orders',                 // Menu title
            'manage_options',          // Capability
            'core-settings-orders',  // Menu slug
            [$this, 'render_settings_orders_page']  // Callback
        );
    }

    function render_settings_page()
    {
        echo '<div id="core_settings"></div>';
    }

    function render_settings_orders_page()
    {
        echo '<div id="core_settings_orders"></div>';
    }
}
