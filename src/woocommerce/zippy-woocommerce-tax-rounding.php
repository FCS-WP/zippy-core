<?php

/**
 * WooCommerce Tax Rounding
 *
 * Enforces IRAS (Singapore GST) compliant price rounding:
 * - Round Half Up to 2 decimal places
 * - Overrides WooCommerce default Half-Down behavior on tax-inclusive stores
 *
 * @package Zippy_Core
 */

namespace Zippy_Core\Src\Woocommerce;

defined('ABSPATH') or die();

class Zippy_Woocommerce_Tax_Rounding
{
    public function __construct()
    {
        $this->set_hooks();
    }

    protected function set_hooks()
    {
        /**
         * Force tax calculation to use Round Half Up (PHP_ROUND_HALF_UP).
         * @see woocommerce/includes/wc-formatting-functions.php :: wc_round_tax_total()
         */
        add_filter('wc_round_tax_total', [$this, 'round_tax_half_up'], 10, 3);
        add_filter('wc_get_price_decimals', [$this, 'enforce_price_decimals']);

        /**
         * Disable "Round tax at subtotal level" option.
         */
        add_filter('option_woocommerce_tax_round_at_subtotal', [$this, 'disable_round_at_subtotal']);
    }

    /**
     * Re-round tax value using PHP_ROUND_HALF_UP.
     *
     * Called via 'wc_round_tax_total' filter.
     * We use $value (pre-rounded) instead of $rounded_tax to ensure
     * the rounding mode is applied correctly from the original value.
     *
     * Examples:
     *   0.1250 → 0.13 (Half-Up, correct per IRAS)
     *   0.1240 → 0.12
     *
     * @param float    $rounded_tax WooCommerce's computed rounded tax (ignored).
     * @param float    $value       Original tax value before rounding.
     * @param int      $precision   Number of decimal places.
     * @return float
     */
    public function round_tax_half_up($rounded_tax, $value, $precision)
    {
        return round($value, $precision, PHP_ROUND_HALF_UP);
    }

    /**
     * Enforce 2 decimal places for all WooCommerce price displays.
     *
     * @return int
     */
    public function enforce_price_decimals()
    {
        return 2;
    }

    /**
     * Disable WooCommerce "round tax at subtotal" setting.
     * Returns 'no' regardless of what is stored in the database.
     *
     * @return string
     */
    public function disable_round_at_subtotal()
    {
        return 'no';
    }
}
