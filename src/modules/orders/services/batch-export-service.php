<?php

namespace Zippy_Core\Src\Modules\Orders\Services;

defined('ABSPATH') or die();

class Batch_Export_Service
{
    /**
     * Start the export process: calculate total record count and generate a unique export ID.
     * The client will use this ID to identify subsequent chunk requests.
     */
    public static function start_export($params)
    {
        $filter = $params['filter'] ?? [];
        
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
            'chunk_size'  => 100
        ];
    }

    /**
     * Process a single chunk: Fetch orders and APPEND them to the temporary CSV file.
     * Automatically handles UTF-8 BOM and Headers on the first chunk (offset = 0).
     */
    public static function process_chunk($params)
    {
        $export_id = $params['export_id'] ?? '';
        $offset = isset($params['offset']) ? intval($params['offset']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 100;
        $filter = $params['filter'] ?? [];

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

        $file_name = "orders_{$export_id}.csv";
        $file_path = $zippy_dir . '/' . $file_name;
        
        $mode = ($offset == 0) ? 'w' : 'a';
        $f_handle = fopen($file_path, $mode);

        if ($offset == 0) {
            fwrite($f_handle, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($f_handle, [
                'Order ID', 'Phone', 'First Name', 'Last Name', 'User', 'Status', 'Total', 'Payment Method', 'Date Created'
            ]);
        }

        foreach ($orders as $order) {
            $row = [
                $order->get_id(),
                $order->get_billing_phone(),
                $order->get_billing_first_name(),
                $order->get_billing_last_name(),
                $order->get_user_id() ? get_userdata($order->get_user_id())->user_login : 'Guest',
                wc_get_order_status_name($order->get_status()),
                strip_tags(wc_price($order->get_total())),
                $order->get_payment_method_title(),
                $order->get_date_created() ? $order->get_date_created()->date_i18n('Y-m-d H:i:s') : '',
            ];
            fputcsv($f_handle, $row);
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
        $file_name = "orders_{$export_id}.csv";
        $file_path = $upload_dir['basedir'] . '/zippy-exports/' . $file_name;
        $file_url = $upload_dir['baseurl'] . '/zippy-exports/' . $file_name;

        if (!file_exists($file_path)) {
            return new \WP_Error('file_not_found', 'Export file missing.');
        }

        return [
            'file_url' => $file_url,
            'file_name' => $file_name
        ];
    }
}
