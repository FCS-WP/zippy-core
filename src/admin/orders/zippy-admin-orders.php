<?php

/**
 * This class handle new layout for order page of Woocommerce
 *
 * @package MPDA_Consent
 */

namespace Zippy_Core\Src\Admin\Orders;

class Zippy_Admin_Orders
{
    protected static $_instance = null;

    /**
     * 
     * @return Zippy_Admin_Orders
     */

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Auto run & init function
     * @return void;
     */

    public function __construct()
    {
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
        <div>
            <h2>This is custom order pages</h2>
        </div>
    <?php
    }
}
