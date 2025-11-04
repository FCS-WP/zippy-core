<?php

namespace Zippy_Core;

use Zippy_Core\Core_Module;
use Zippy_Core\Orders\Routes\Order_Route;

class Core_Orders extends Core_Module
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
        Order_Route::get_instance();

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
        add_shortcode('admin_order_table', array($this, 'generate_admin_order_table_div'));
        add_action('woocommerce_admin_order_items_after_line_items', [$this, 'render_admin_order_table']);
        add_action('admin_head', [$this, 'custom_admin_order_styles']);
    }

    function custom_admin_order_styles()
    {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'woocommerce_page_wc-orders') {
            echo '<style>
            .woocommerce_order_items_wrapper .woocommerce_order_items {
                display: none !important;
            }

            .wc-order-data-row.wc-order-totals-items {
                display: none !important;
            }

            .wc-order-data-row.wc-order-bulk-actions {
                display: none !important;
            }
        </style>';
        }
    }

    function render_admin_order_table($order_id)
    {
        $order = wc_get_order($order_id);
        $enable_edit = true;

        echo do_shortcode('[admin_order_table order_id="' . esc_attr($order_id) . '" enable_edit="' . esc_attr($enable_edit) . '"]');
    }

    // Start handle setting tabs
    public function add_zippy_woo_tab($tabs)
    {
        $tabs['zippy_woo'] = __('Zippy - Woo', 'woocommerce');
        return $tabs;
    }

    function generate_admin_order_table_div($atts)
    {
        $atts = shortcode_atts([
            'order_id' => 0,
            'enable_edit' => false,
        ], $atts, 'admin_order_table');

        $order_id = intval($atts['order_id']);
        $enable_edit = filter_var($atts['enable_edit'], FILTER_VALIDATE_BOOLEAN);

        if (!$order_id) {
            return '';
        }

        return '<div id="admin-table-order" data-order-id="' . esc_attr($order_id) . '" data-enable-edit="' . esc_attr($enable_edit) . '"></div>';
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
