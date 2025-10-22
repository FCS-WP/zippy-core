<?php

/**
 * Admin Setting
 *
 * @package Shin
 */

namespace Zippy_Core\Src\Woocommerce;

use WC_Tax;
use Zippy_Core\Utils\Zippy_Wc_Calculate_Helper;

defined('ABSPATH') or die();

class Zippy_Shipping
{
    protected static $_instance = null;

    /**
     * @return Zippy_Shipping
     */
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        if (!function_exists('is_plugin_active')) {

            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        if (!is_plugin_active('woocommerce/woocommerce.php')) return;

        $this->set_hooks();
    }

    protected function set_hooks()
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
