<?php

namespace Zippy_Core;

use Zippy_Core\Orders\Routes\Order_Detail_Route;
use Zippy_Core\Orders\Routes\Order_Route;



defined('ABSPATH') || exit;

class Core_Orders
{
    public function __construct()
    {
        //  Load module
        self::load_required_files();
        add_action('plugins_loaded', [$this, 'init']);
        /**
         * Handle setting tabs
         */
        add_filter('woocommerce_settings_tabs_array', [$this, 'add_zippy_woo_tab'], 50);
        add_action('woocommerce_settings_tabs_zippy_woo', [$this, 'zippy_woo_settings_tab']);
        add_action('woocommerce_update_options_zippy_woo', [$this, 'update_zippy_woo_settings']);

        /**
         * Add cutomize orders pages
         * 
         */

        // enable this code after done
        // $enable_custom_orders_page = get_option('zippy_woo_custom_orders_enabled', 'no');
        // if ($enable_custom_orders_page === 'yes') {
        //     add_action('admin_menu', [$this, 'add_custom_orders_page']);
        // }

        // remove this code after done
        add_action('admin_menu', [$this, 'add_custom_orders_page']);
    }

    public function load_required_files()
    {
        $paths = [
            __DIR__ . '/controllers',
            __DIR__ . '/routes',
            __DIR__ . '/services',
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

    public function init()
    {
        Order_Route::get_instance();
        Order_Detail_Route::get_instance();
    }

    // Start handle setting tabs
    public function add_zippy_woo_tab($tabs)
    {
        $tabs['zippy_woo'] = __('Zippy - Woo', 'woocommerce');
        return $tabs;
    }

    function zippy_woo_settings_tab()
    {
        // Get saved promotions
        $enable_custom_orders_page = get_option('zippy_woo_custom_orders_enabled', 'no');
?>
        <div class="zippy-woo-configs">
            <label>
                <input type="checkbox" name="zippy_woo_custom_orders_enabled" value="yes" <?php checked($enable_custom_orders_page, 'yes'); ?> />
                <?php _e('Enable customize orders page', 'Zippy'); ?>
            </label>
        </div>
    <?php
    }

    function update_zippy_woo_settings()
    {
        $enabled = isset($_POST['zippy_woo_custom_orders_enabled']) ? 'yes' : 'no';
        update_option('zippy_woo_custom_orders_enabled', $enabled);
    }
    // End handle setting tabs


    /**
     * Add custom orders page
     */

    function add_custom_orders_page()
    {
        // Remove default WooCommerce Orders submenu
        // enable this code after done
        // remove_submenu_page( 'woocommerce', 'wc-orders' );

        add_submenu_page(
            'woocommerce',                 // Parent slug (WooCommerce menu)
            'Custom Orders',               // Page title
            'Custom Orders',               // Menu title
            'manage_woocommerce',          // Capability
            'custom-orders',               // Menu slug
            [$this, 'render_custom_orders_page']    // Callback function
        );
    }

    function render_custom_orders_page()
    {
    ?>
        <div id="orders-page"></div>
    <?php
    }
}
