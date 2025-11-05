<?php

namespace Zippy_Core;

class Core_Settings extends Core_Module
{

    public function load_required_files()
    {
        // 
    }

    public function init_module()
    {
        add_action('admin_menu', [$this, 'register_settings_page']);
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
