<?php

namespace Zippy_Core\Orders\Services;

use WC_Order_Item_Product;
use WC_Tax;
use Dompdf\Dompdf;
use Dompdf\Options;

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

    public static function export_orders($date_from, $date_to, $format)
    {
        $args = [
            'limit'   => -1,
            'return'  => 'objects',
        ];

        if ($date_from && $date_to) {
            $args['date_created'] = $date_from . '...' . $date_to;
        } elseif ($date_from) {
            $args['date_created'] = '>' . $date_from . ' 00:00:00';
        } elseif ($date_to) {
            $args['date_created'] = '<' . $date_to . ' 23:59:59';
        }

        $orders = wc_get_orders($args);

        if (empty($orders)) {
            return new \WP_Error('no_orders', 'No orders found for the selected date range.');
        }

        // Chuẩn bị dữ liệu
        $order_rows = [];
        foreach ($orders as $order) {
            $order_rows[] = [
                'order_id'       => $order->get_id(),
                'phone'          => $order->get_billing_phone(),
                'firstname'      => $order->get_billing_first_name(),
                'lastname'       => $order->get_billing_last_name(),
                'user'           => $order->get_user_id() ? get_userdata($order->get_user_id())->user_login : 'Guest',
                'status'         => wc_get_order_status_name($order->get_status()),
                'total'          => strip_tags(wc_price($order->get_total())),
                'payment_method' => $order->get_payment_method_title(),
                'date_created'   => $order->get_date_created()->date_i18n('Y-m-d H:i:s'),
            ];
        }

        // Sinh nội dung file (không lưu server)
        $content = '';
        if ($format === 'csv') {
            $content = self::generate_csv($order_rows);
        } elseif ($format === 'pdf') {
            $content = self::generate_pdf($order_rows);
        } else {
            return new \WP_Error('invalid_format', 'Invalid export format.');
        }

        // Encode base64 để gửi client tải
        $file_base64 = base64_encode($content);
        $file_name = 'orders_export_' . date('Y-m-d_H-i-s') . '.' . $format;

        return [
            'file_base64' => $file_base64,
            'file_name'   => $file_name,
            'file_type'   => $format,
        ];
    }

    /**
     * Tạo CSV – trả về nội dung string
     */
    private static function generate_csv($rows)
    {
        $handle = fopen('php://memory', 'r+');
        fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

        fputcsv($handle, [
            'Order ID',
            'Phone',
            'First Name',
            'Last Name',
            'User',
            'Status',
            'Total',
            'Payment Method',
            'Date Created',
        ]);

        foreach ($rows as $r) {
            fputcsv($handle, [
                $r['order_id'],
                $r['phone'],
                $r['firstname'],
                $r['lastname'],
                $r['user'],
                $r['status'],
                $r['total'],
                $r['payment_method'],
                $r['date_created'],
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        return $content;
    }

    /**
     * Tạo PDF – trả về nội dung binary
     */
    private static function generate_pdf($rows)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $html = '
        <style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
            h2 { text-align:center; margin-bottom: 10px; }
            table { width:100%; border-collapse: collapse; }
            th, td { border:1px solid #ccc; padding:8px; text-align:left; }
            th { background-color:#f2f2f2; }
        </style>
        <h2>Orders Export</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Phone</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Payment Method</th>
                    <th>Date Created</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($rows as $r) {
            $html .= '<tr>
                <td>' . esc_html($r['order_id']) . '</td>
                <td>' . esc_html($r['phone']) . '</td>
                <td>' . esc_html($r['firstname']) . '</td>
                <td>' . esc_html($r['lastname']) . '</td>
                <td>' . esc_html($r['user']) . '</td>
                <td>' . esc_html($r['status']) . '</td>
                <td>' . esc_html($r['total']) . '</td>
                <td>' . esc_html($r['payment_method']) . '</td>
                <td>' . esc_html($r['date_created']) . '</td>
            </tr>';
        }

        $html .= '</tbody></table>';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }
}
