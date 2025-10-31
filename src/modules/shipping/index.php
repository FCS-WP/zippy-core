<?php

namespace Zippy_Core;

use Zippy_Core\Core_Module;
use Zippy_Core\Shipping\Services\Shipping_Services;
use Zippy_Core\Utils\Zippy_Wc_Calculate_Helper;

class Core_Shipping extends Core_Module
{
    public function __construct()
    {
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            return;
        }

        parent::__construct();
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

    public function init_module()
    {
        add_filter('woocommerce_package_rates', array($this, 'recalculate_shipping_rates'), 9999, 2);
    }

    public function recalculate_shipping_rates($rates, $package)
    {
        foreach ($rates as $rate_id => $rate) {
            $cost   = $rate->get_cost();
            $costExclTax = Zippy_Wc_Calculate_Helper::get_total_price_exclude_tax($cost);
            $taxes  = Zippy_Wc_Calculate_Helper::get_tax($cost);
            $rate->set_cost($costExclTax);
            $rate->set_taxes([1 => $taxes]);
        }
        return $rates;
    }
}
