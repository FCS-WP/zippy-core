<?php
namespace Zippy_Core;

class Core_Settings extends Core_Module {
    
    public function load_required_files()
    {
        // 
    }

    public function init_module()
    {
        add_action('admin_menu', [ $this, 'register_settings_page' ]);
    }

    public function register_settings_page() {
        add_options_page(
            __( 'Core Settings', 'zippy-core' ), // Page title
            __( 'Core Settings', 'zippy-core' ), // Menu title in Settings
            'manage_options',                     // Capability required
            'zippy-settings',                     // Slug
            [ $this, 'render_settings_page' ]     // Callback function
        );
    }

    public function render_settings_page() {
        echo '<div id="core_settings"></div>';
    }
}