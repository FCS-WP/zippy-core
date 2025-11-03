<?php

namespace Zippy_Core\Orders\Services;

use WC_Order_Item_Product;
use WC_Tax;

class Order_Services
{
    public static function handle_orders($infos)
    {
        list($page, $per_page, $order_by, $order_val, $order_status, $date_from, $date_to) = $infos;

        $args = [
            'limit'   => $per_page,
            'page'    => $page,
            'orderby' => $order_by,
            'order'   => $order_val,
            'return'  => 'objects',
        ];

        if (!empty($order_status)) {
            $statuses = array_map('trim', explode(',', $order_status));
            $args['status'] = $statuses;
        }

        if ($date_from && $date_to) {
            $args['date_created'] = $date_from . '...' . $date_to;
        } elseif ($date_from) {
            $args['date_created'] = '>' . $date_from . ' 00:00:00';
        } elseif ($date_to) {
            $args['date_created'] = '<' . $date_to . ' 23:59:59';
        }

        $orders = wc_get_orders($args);

        $data = [];

        foreach ($orders as $order) {
            $data[] = self::parse_order_data($order);
        }

        $count_args = [
            'return' => 'ids',
            'limit'  => -1,
        ];

        if (!empty($order_status)) {
            $count_args['status'] = $args['status'];
        }

        if (!empty($args['date_created'])) {
            $count_args['date_created'] = $args['date_created'];
        }

        $total_orders = count(wc_get_orders($count_args));
        $total_pages  = ceil($total_orders / $per_page);

        return array(
            'page'         => $page,
            'per_page'     => $per_page,
            'total_pages'  => $total_pages,
            'total_orders' => $total_orders,
            'orders'       => $data,
        );
    }

    public static function parse_order_data($order)
    {
        $billing  = $order->get_address('billing');
        $shipping = $order->get_address('shipping');

        $data = [
            'id'           => $order->get_id(),
            'order_number' => $order->get_order_number(),
            'date_created' => $order->get_date_created() ? $order->get_date_created()->date('Y-m-d H:i:s') : '',
            'status'       => $order->get_status(),
            'total'        => $order->get_total(),
            'currency'     => $order->get_currency(),
            'payment_method' => [
                'id'    => $order->get_payment_method(),
                'title' => $order->get_payment_method_title(),
            ],
            'billing' => [
                'first_name' => $billing['first_name'],
                'last_name'  => $billing['last_name'],
                'company'    => $billing['company'],
                'email'      => $billing['email'],
                'phone'      => $billing['phone'],
                'address_1'  => $billing['address_1'],
                'address_2'  => $billing['address_2'],
                'city'       => $billing['city'],
                'state'      => $billing['state'],
                'postcode'   => $billing['postcode'],
                'country'    => $billing['country'],
            ],
            'shipping' => [
                'first_name' => $shipping['first_name'],
                'last_name'  => $shipping['last_name'],
                'company'    => $shipping['company'],
                'address_1'  => $shipping['address_1'],
                'address_2'  => $shipping['address_2'],
                'city'       => $shipping['city'],
                'state'      => $shipping['state'],
                'postcode'   => $shipping['postcode'],
                'country'    => $shipping['country'],
            ],
            'items' => array_map(function ($item) {
                return [
                    'product_id' => $item->get_product_id(),
                    'name'       => $item->get_name(),
                    'quantity'   => $item->get_quantity(),
                    'subtotal'   => $item->get_subtotal(),
                    'total'      => $item->get_total(),
                ];
            }, $order->get_items()),
        ];

        return $data;
    }

    public static function  set_order_item_totals_with_wc_tax($item, $price_incl_tax, $quantity = 1)
    {
        if (get_option('woocommerce_prices_include_tax') !== 'yes') {
            $item->set_total($price_incl_tax * $quantity);
            $item->calculate_taxes();
            $item->save();
        }

        if (! $item instanceof WC_Order_Item_Product) {
            return false;
        }

        $product = $item->get_product();
        if (! $product) {
            return false;
        }

        // Get tax rates for this product
        $tax_rates = WC_Tax::get_rates($product->get_tax_class());

        if (empty($tax_rates)) {
            // No tax: treat as tax-free
            $subtotal = $price_incl_tax * $quantity;
            $item->set_subtotal($subtotal);
            $item->set_total($subtotal);
            $item->set_taxes(['total' => [], 'subtotal' => []]);
            $item->save();
            return [
                'subtotal_excl_tax' => $subtotal,
                'total_tax'         => 0,
                'total_incl_tax'    => $subtotal,
            ];
        }

        // Calculate inclusive tax breakdown
        $line_price = $price_incl_tax * $quantity;
        $taxes      = WC_Tax::calc_inclusive_tax($line_price, $tax_rates);

        $total_tax        = array_sum($taxes);
        $subtotal_excl_tax = $line_price - $total_tax;

        // Update item totals
        $item->set_subtotal($subtotal_excl_tax);
        $item->set_total($subtotal_excl_tax);
        $item->set_taxes([
            'total'    => $taxes,
            'subtotal' => $taxes,
        ]);

        $item->save();

        return [
            'subtotal_excl_tax' => $subtotal_excl_tax,
            'total_tax'         => $total_tax,
            'total_incl_tax'    => $line_price,
        ];
    }
}
