<?php


namespace Zippy_Core\Orders\Controllers;

use WP_REST_Request;
use WP_Error;
use Zippy_Core\Orders\Services\Order_Services;
use Zippy_Core\Utils\Zippy_Request_Helper;

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
}
