<?php

namespace Zippy_Core\Src\Modules\Orders\Services;

defined('ABSPATH') or die();

use Automattic\WooCommerce\Admin\Overrides\OrderRefund;
use WC_Order_Item_Product;


class Batch_Export_Service
{
    /**
     * Start the export process: calculate total record count and generate a unique export ID.
     * The client will use this ID to identify subsequent chunk requests.
     */
    public static function start_export($params)
    {
        $filter = $params['filter'] ?? [];
        $format = $params['format'] ?? 'csv';
        
        $args = [
            'limit' => -1,
            'return' => 'ids',
            'status' => $filter['order_status'] ?? null,
            'type'   => 'shop_order',
        ];
        
        if (!empty($filter['date_from']) && !empty($filter['date_to'])) {
            $args['date_created'] = $filter['date_from'] . '...' . $filter['date_to'];
        } elseif (!empty($filter['date_from'])) {
            $args['date_created'] = '>' . $filter['date_from'] . ' 00:00:00';
        } elseif (!empty($filter['date_to'])) {
            $args['date_created'] = '<' . $filter['date_to'] . ' 23:59:59';
        }

        $order_ids = wc_get_orders($args);
        $total_items = count($order_ids);

        if ($total_items === 0) {
            return new \WP_Error('no_orders', 'No orders found for the selected range.');
        }

        $export_id = uniqid('export_');
        
        return [
            'total_items' => $total_items,
            'export_id'   => $export_id,
            'chunk_size'  => 100,
            'format'      => $format
        ];
    }

    /**
     * Process a single chunk: Fetch orders and APPEND them to the temporary CSV or HTML file.
     * Automatically handles UTF-8 BOM and Headers on the first chunk (offset = 0).
     */
    public static function process_chunk($params)
    {
        $export_id = $params['export_id'] ?? '';
        $offset = isset($params['offset']) ? intval($params['offset']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 100;
        $filter = $params['filter'] ?? [];
        $format = $params['format'] ?? 'csv';

        if (empty($export_id)) {
            return new \WP_Error('invalid_id', 'Invalid export ID.');
        }

        $order_args = [
            'limit'  => $limit,
            'offset' => $offset,
            'return' => 'objects',
            'status' => $filter['order_status'] ?? null,
            'type'   => 'shop_order',
            'orderby' => 'ID',
            'order'   => 'ASC',
        ];
        
        if (!empty($filter['date_from']) && !empty($filter['date_to'])) {
            $order_args['date_created'] = $filter['date_from'] . '...' . $filter['date_to'];
        } elseif (!empty($filter['date_from'])) {
            $order_args['date_created'] = '>' . $filter['date_from'] . ' 00:00:00';
        } elseif (!empty($filter['date_to'])) {
            $order_args['date_created'] = '<' . $filter['date_to'] . ' 23:59:59';
        }

        $orders = wc_get_orders($order_args);

        // File preparation
        $upload_dir = wp_upload_dir();
        $zippy_dir = $upload_dir['basedir'] . '/zippy-exports';
        if (!is_dir($zippy_dir)) {
            wp_mkdir_p($zippy_dir);
        }

        $ext = ($format === 'pdf') ? 'html' : 'csv';
        $file_name = "orders_{$export_id}.{$ext}";
        $file_path = $zippy_dir . '/' . $file_name;
        
        $mode = ($offset == 0) ? 'w' : 'a';
        $f_handle = fopen($file_path, $mode);

        if ($offset == 0) {
            if ($format === 'csv') {
                fwrite($f_handle, "\xEF\xBB\xBF"); // UTF-8 BOM
                fputcsv($f_handle, [
                    'Name',
                    'Phone',
                    'Email',
                    'Company',
                    'Street',
                    'City',
                    'Postcode',
                    'State',
                    'Order ID',
                    "Products",
                    "Notes",
                    'Status',
                    'Total',
                    'Date Created',
                    'Source',
                ]);
            }
        }

        foreach ($orders as $order) {
            if ($order instanceof OrderRefund) {
                continue;
            }

            $country = $order->get_billing_country();
            $state   = $order->get_billing_state();
            $states  = WC()->countries->get_states($country);
            $state_name = $states[$state] ?? $state;

            $items_data = self::get_items_data($order);
            $total_formatted = html_entity_decode(wp_strip_all_tags(wc_price($order->get_total())));
            $date_created = $order->get_date_created() ? $order->get_date_created()->date_i18n('Y-m-d H:i:s') : '';
            $source = $order->get_meta('_wc_order_attribution_utm_source') ?: 'website';

            if ($format === 'csv') {
                $row = [
                    $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                    $order->get_billing_phone(),
                    $order->get_billing_email(),
                    $order->get_billing_company(),
                    trim($order->get_billing_address_1() . ' ' . $order->get_billing_address_2()),
                    $order->get_billing_city(),
                    $order->get_billing_postcode(),
                    $state_name,
                    $order->get_id(),
                    $items_data,
                    $order->get_customer_note(),
                    wc_get_order_status_name($order->get_status()),
                    $total_formatted,
                    $date_created,
                    $source,
                ];
                fputcsv($f_handle, $row);
            } else {
                // PDF mode: 10 columns as per snippet (space constraints)
                $html_row = '<tr>
                    <td>' . esc_html($order->get_id()) . '</td>
                    <td>' . esc_html($order->get_billing_phone()) . '</td>
                    <td>' . esc_html($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()) . '</td>
                    <td>' . esc_html($order->get_billing_email()) . '</td>
                    <td>' . esc_html($order->get_billing_company()) . '</td>
                    <td>' . esc_html($state_name) . '</td>
                    <td>' . esc_html(wc_get_order_status_name($order->get_status())) . '</td>
                    <td>' . esc_html($total_formatted) . '</td>
                    <td>' . esc_html($date_created) . '</td>
                    <td>' . esc_html($source) . '</td>
                </tr>' . PHP_EOL;
                fwrite($f_handle, $html_row);
            }
        }


        fclose($f_handle);

        return [
            'processed' => count($orders),
            'offset'    => $offset + count($orders)
        ];
    }

    /**
     * Finalize the export process: Verify the final file and return the download URL.
     */
    public static function finalize_export($export_id)
    {
        $upload_dir = wp_upload_dir();
        $base_name = "orders_{$export_id}";
        $base_path = $upload_dir['basedir'] . '/zippy-exports/' . $base_name;
        
        // Check if PDF or CSV
        if (file_exists($base_path . '.csv')) {
            $file_url = $upload_dir['baseurl'] . '/zippy-exports/' . $base_name . '.csv';
            return ['file_url' => $file_url, 'format' => 'csv'];
        } elseif (file_exists($base_path . '.html')) {
            // Convert accumulated HTML to PDF
            $html_rows = file_get_contents($base_path . '.html');
            $full_html = self::wrap_pdf_html($html_rows);
            
            $pdf_content = self::generate_pdf_content($full_html);
            
            $pdf_path = $base_path . '.pdf';
            file_put_contents($pdf_path, $pdf_content);
            
            // Clean up temp HTML
            unlink($base_path . '.html');
            
            $file_url = $upload_dir['baseurl'] . '/zippy-exports/' . $base_name . '.pdf';
            return ['file_url' => $file_url, 'format' => 'pdf'];
        }

        return new \WP_Error('file_not_found', 'Export file missing.');
    }

    private static function wrap_pdf_html($rows_html)
    {
        return '
        <style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
            h2 { text-align:center; margin-bottom: 10px; }
            table { width:100%; border-collapse: collapse; table-layout: fixed; }
            th, td { border:1px solid #ccc; padding:4px; text-align:left; word-wrap: break-word; }
            th { background-color:#f2f2f2; }
        </style>
        <h2>Orders Export</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%">Order ID</th>
                    <th style="width: 12%">Phone</th>
                    <th style="width: 12%">Name</th>
                    <th style="width: 12%">Email</th>
                    <th style="width: 10%">Company</th>
                    <th style="width: 10%">State</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 8%">Total</th>
                    <th style="width: 10%">Date Created</th>
                    <th style="width: 8%">Source</th>
                </tr>
            </thead>
            <tbody>' . $rows_html . '</tbody></table>';
    }

    private static function get_items_data($order)
    {
        $items = [];

        foreach ($order->get_items() as $item) {
            if ($item instanceof WC_Order_Item_Product) {
                $items[] = sprintf(
                    "%s (x%d) - %s",
                    $item->get_name(),
                    $item->get_quantity(),
                    html_entity_decode(wp_strip_all_tags(wc_price($item->get_total())))
                );
            }
        }

        return implode("\n", $items);
    }


    private static function generate_pdf_content($html)
    {
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }
}
