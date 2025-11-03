<?php


namespace Zippy_Core\Orders\Controllers;

use WP_REST_Request;
use WP_Error;
use Zippy_Core\Orders\Services\Order_Services;
use Zippy_Core\Utils\Zippy_Request_Helper;
use Zippy_Core\Utils\Zippy_Response_Handler;
use Zippy_Core\Utils\Zippy_Wc_Calculate_Helper;
use WC_Coupon;

class Order_Controllers
{
    public static function get_all_orders_with_pagination(WP_REST_Request $request)
    {
        try {
            $params = $request->get_params();
            $rules = [
                'page'          => ['type' => 'int', 'required' => true],
                'per_page'      => ['type' => 'int', 'required' => true],
                'orderby'       => ['type' => 'string', 'required' => false],
                'orderval'      => ['type' => 'string', 'required' => false],
                'order_status'  => ['type' => 'string', 'required' => false],
                'date_from'     => ['type' => 'string', 'required' => false],
                'date_to'       => ['type' => 'string', 'required' => false],
            ];

            $validation = Zippy_Request_Helper::validate_request($params, $rules);
            if (is_wp_error($validation)) {
                return $validation;
            }

            $page         = absint($request->get_param('page'));
            $per_page     = absint($request->get_param('per_page'));
            $order_by     = sanitize_text_field($request->get_param('orderby') ?? 'date');
            $order_val    = sanitize_text_field($request->get_param('orderval') ?? 'DESC');
            $order_status = sanitize_text_field($request->get_param('order_status')) ?? '';
            $date_from    = sanitize_text_field($request->get_param('date_from') ?? '');
            $date_to      = sanitize_text_field($request->get_param('date_to') ?? '');

            $infos = array($page, $per_page, $order_by, $order_val, $order_status, $date_from, $date_to);
            $data = Order_Services::handle_orders($infos);

            return rest_ensure_response([
                'success' => true,
                'result' => $data,
                'message' => "Get orders successfully!",
            ]);
        } catch (\Exception $e) {
            return new \WP_Error('zippy_orders_error', $e->getMessage(), ['status' => 500]);
        }
    }

    public static function get_order_detail_by_id(WP_REST_Request $request)
    {
        try {
            $order_id = absint($request->get_param('id'));

            if (empty($order_id)) {
                return new WP_Error('missing_id', 'Order ID is required.', ['status' => 400]);
            }

            $order = wc_get_order($order_id);

            if (!$order) {
                return new WP_Error('not_found', 'Order not found.', ['status' => 404]);
            }

            $data = Order_Services::parse_order_data($order);

            return rest_ensure_response([
                'success' => true,
                'result'  => $data,
                'message' => "Get order detail successfully!",
            ]);
        } catch (\Exception $e) {
            return new WP_Error('server_error', $e->getMessage(), ['status' => 500]);
        }
    }

    public static function update_order_status(WP_REST_Request $request)
    {
        $order_ids = $request->get_param('order_ids');
        $status = $request->get_param('status');

        if (empty($order_ids) || empty($status)) {
            return Zippy_Response_Handler::error('Missing parameters.');
        }

        if (!is_array($order_ids)) {
            return Zippy_Response_Handler::error('Order IDs must be an array.');
        }

        $valid_statuses = wc_get_order_statuses();
        if (!array_key_exists($status, $valid_statuses)) {
            return Zippy_Response_Handler::error('Invalid order status.');
        }

        $updated_orders = [];
        $failed_orders = [];

        foreach ($order_ids as $order_id) {
            $order = wc_get_order($order_id);
            if ($order) {
                $order->update_status($status, 'Order status updated via API', true);
                $updated_orders[] = $order_id;
            } else {
                $failed_orders[] = $order_id;
            }
        }

        return Zippy_Response_Handler::success([
            'updated_orders' => $updated_orders,
            'failed_orders' => $failed_orders,
            'new_status' => $status,
        ], 'Order statuses updated successfully.');
    }

    public static function get_order_info(WP_REST_Request $request)
    {
        $order_id = intval($request->get_param('order_id'));
        $order    = wc_get_order($order_id);
        if (empty($order)) {
            return Zippy_Response_Handler::error('Order not found.');
        }

        $items = $order->get_items();
        $shipping_items = $order->get_items('shipping');
        $fee = $order->get_items('fee');
        $coupon_items = $order->get_items('coupon');

        $result = [];

        [$result['products'], $subtotalOrder, $taxTotalOrder] = self::get_products_info($items);
        [$result['shipping'], $totalShipping, $taxShipping] = self::get_shipping_info($shipping_items);
        [$result['fees'], $totalFee, $taxFee] = self::get_fees_info($fee);
        [$result['coupons'], $totalCoupon] = self::get_coupons_info($coupon_items);

        $taxTotal = Zippy_Wc_Calculate_Helper::round_price_wc($taxTotalOrder + $taxShipping + $taxFee);
        $totalCalculated = Zippy_Wc_Calculate_Helper::round_price_wc(
            ($subtotalOrder + $totalShipping + $totalFee - $totalCoupon)
        );

        $result['order_info'] = [
            'subtotal'   => $subtotalOrder,
            'tax_total'  => $taxTotal,
            'total'      => $totalCalculated,
        ];

        return Zippy_Response_Handler::success($result);
    }

    private static function get_products_info($items)
    {
        $products = [];
        $subtotal = 0;
        $taxTotal = 0;

        foreach ($items as $item_id => $item) {
            $product = $item->get_product();
            $price_total = Zippy_Wc_Calculate_Helper::round_price_wc($item->get_subtotal());
            $tax_total = $item->get_subtotal_tax();

            $products[$item_id] = [
                'product_id'        => $product ? $product->get_id() : 0,
                'name'              => $product ? $product->get_name() : '',
                'img_url'           => $product ? wp_get_attachment_url($product->get_image_id()) : '',
                'sku'               => $product ? $product->get_sku() : '',
                'quantity'          => $item->get_quantity(),
                'price_total'       => $price_total,
                'tax_total'         => $tax_total,
                'price_per_item'    => Zippy_Wc_Calculate_Helper::round_price_wc($item->get_subtotal() / max(1, $item->get_quantity())),
                'tax_per_item'      => Zippy_Wc_Calculate_Helper::round_price_wc($item->get_subtotal_tax() / max(1, $item->get_quantity())),
                'min_order'         => get_post_meta($product->get_id(), '_custom_minimum_order_qty', true) ?: 0,
            ];

            $subtotal += ($price_total + Zippy_Wc_Calculate_Helper::round_price_wc($tax_total));
        }

        $taxSubtotal = Zippy_Wc_Calculate_Helper::get_tax($subtotal);
        $subtotal = Zippy_Wc_Calculate_Helper::round_price_wc($subtotal);

        return [$products, $subtotal, $taxSubtotal];
    }

    private static function get_shipping_info($shipping_items)
    {
        $shipping = [];
        $total = 0;
        $tax = 0;

        foreach ($shipping_items as $ship_id => $item) {
            $amount = floatval($item->get_total());
            $taxItem = Zippy_Wc_Calculate_Helper::get_tax_by_price_exclude_tax($amount);

            $amount += $taxItem;
            $shipping[] = [
                'method'       => $item->get_name(),
                'total'        => $amount,
                'tax_shipping' => $taxItem,
            ];

            $total += $amount;
            $tax += $taxItem;
        }

        return [$shipping, $total, $tax];
    }

    private static function get_fees_info($fee_items)
    {
        $fees = [];
        $total = 0;
        $tax = 0;

        foreach ($fee_items as $fee_id => $item) {
            $amount = floatval($item->get_total());
            $taxItem = Zippy_Wc_Calculate_Helper::get_tax_by_price_exclude_tax($amount);

            $amount += $taxItem;
            $fees[] = [
                'name'     => $item->get_name(),
                'total'    => $amount,
                'tax_fee'  => $taxItem,
            ];

            $total += $amount;
            $tax += $taxItem;
        }

        return [$fees, $total, $tax];
    }

    private static function get_coupons_info($coupon_items)
    {
        $coupons = [];
        $total = 0;

        foreach ($coupon_items as $coupon_id => $item) {
            $amount = floatval($item->get_discount());
            $coupons[] = ['total' => $amount];
            $total += $amount;
        }

        return [$coupons, $total];
    }

    public static function remove_order_item(WP_REST_Request $request)
    {
        $order_id = intval($request->get_param('order_id'));
        $item_id  = intval($request->get_param('item_id'));

        $order = wc_get_order($order_id);
        if (!$order) {
            return Zippy_Response_Handler::error('Order not found.');
        }

        $deleted = wc_delete_order_item($item_id);

        if (!$deleted) {
            // $order->calculate_totals();
            return Zippy_Response_Handler::error('Failed to delete order item.');
        }

        return Zippy_Response_Handler::success([
            'order_id' => $order_id,
            'item_id'  => $item_id,
        ], 'Order item deleted successfully.');
    }

    public static function update_meta_data_order_item(WP_REST_Request $request)
    {
        $order_id = $request->get_param('order_id');
        $item_id  = $request->get_param('item_id');
        $quantity = $request->get_param('quantity');

        if (empty($order_id) || empty($item_id) || !is_numeric($quantity)) {
            return Zippy_Response_Handler::error('Missing or invalid parameters.');
        }

        $order = wc_get_order($order_id);
        if (empty($order)) {
            return Zippy_Response_Handler::error('Order not found.');
        }

        $user_id = $order->get_user_id() ?? 0;
        $item = $order->get_item($item_id);
        if (empty($item)) {
            return Zippy_Response_Handler::error('Order item not found.');
        }

        // Set quantity
        $item->set_quantity($quantity, true);

        $product = $item->get_product();
        if (empty($product)) {
            return Zippy_Response_Handler::error('Product not found.');
        }

        $product_price = $product->get_price();
        if (is_null($product_price)) {
            return Zippy_Response_Handler::error('Failed to get product pricing by user.');
        }

        if (get_option('woocommerce_prices_include_tax') === 'yes') {
            Order_Services::set_order_item_totals_with_wc_tax($item, $product_price, $quantity);
        } else {
            $item->set_total($product_price * $quantity);
            $item->calculate_taxes();
            $item->save();
        }

        return Zippy_Response_Handler::success([
            'status' => 'success',
            'message' => 'Quantity updated',
            'data' => [
                'order_id' => $order_id,
                'item_id' => $item_id,
                'quantity' => $quantity
            ]
        ], 200);
    }

    public static function apply_coupon_to_order(WP_REST_Request $request)
    {
        try {
            $order_id = $request->get_param('order_id');
            $coupon_code = $request->get_param('coupon_code');

            if (empty($order_id) || empty($coupon_code)) {
                return Zippy_Response_Handler::error('Missing parameters.');
            }

            $order = wc_get_order($order_id);
            if (!$order) {
                return Zippy_Response_Handler::error('Order not found.');
            }

            $coupon = new WC_Coupon($coupon_code);
            if (!$coupon || !$coupon->get_id()) {
                return Zippy_Response_Handler::error('Invalid coupon code.');
            }

            foreach ($order->get_items('coupon') as $item) {
                if ($item->get_code() === $coupon_code) {
                    return Zippy_Response_Handler::error('Coupon already applied to this order.');
                }
            }

            $item_id = $order->apply_coupon($coupon_code);
            if (is_wp_error($item_id)) {
                return Zippy_Response_Handler::error('Failed to apply coupon to order.');
            }

            $order = wc_get_order($order_id);
            $order->calculate_totals();

            return Zippy_Response_Handler::success([
                'order_id' => $order_id,
                'coupon_code' => $coupon_code,
                'message' => 'Coupon applied successfully.'
            ]);
        } catch (\Throwable $th) {
            return Zippy_Response_Handler::error('An error occurred while applying the coupon.');
        }
    }

    public static function move_to_trash(WP_REST_Request $request)
    {
        $order_ids = $request->get_param('order_ids');

        if (empty($order_ids)) {
            return Zippy_Response_Handler::error('Missing order_ids.');
        }

        $trashed = [];

        foreach ($order_ids as $order_id) {
            $order = wc_get_order($order_id);

            if ($order) {
                $order->delete(false);
                $trashed[] = $order_id;
            }
        }

        if (empty($trashed)) {
            return Zippy_Response_Handler::error('No orders were trashed.');
        }

        return Zippy_Response_Handler::success([
            'trashed_orders' => $trashed,
        ], 'Orders moved to trash.');
    }
}
