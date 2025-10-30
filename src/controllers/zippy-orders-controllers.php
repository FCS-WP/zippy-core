<?php

namespace Zippy_Core\Src\Controllers;

use Exception;
use WP_REST_Request;
use WP_Error;
use Zippy_Core\Utils\Zippy_Request_Helper;

class Zippy_Orders_Controllers
{
    // public static function example_function($request)
    // {
    //     $params = $request->get_params();
    //     $rules = [
    //         'id' =>  ['type' => 'int', 'required' => false],
    //     ];

    //     $validation = Zippy_Request_Helper::validate_request($params, $rules);

    //     if (is_wp_error($validation)) {
    //     return $validation;
    //     }
    // }

    public static function get_all_orders_with_pagination(WP_REST_Request $request)
    {
        try {
            $params = $request->get_params();
            $rules = [
                'page' =>  ['type' => 'int', 'required' => true],
                'per_page' =>  ['type' => 'int', 'required' => true],
                'orderby' =>  ['type' => 'string', 'required' => false],
                'orderval' =>  ['type' => 'string', 'required' => false],
            ];

            $validation = Zippy_Request_Helper::validate_request($params, $rules);

            if (is_wp_error($validation)) {
                return $validation;
            }

            $page     = $request->get_param('page');
            $per_page = $request->get_param('per_page');
            $order_by = $request->get_param('orderby') ?? 'date';
            $order_val = $request->get_param('orderval') ?? 'DESC';

            $args = [
                'limit'   => $per_page,
                'page'    => $page,
                'orderby' => $order_by,
                'order'   => $order_val,
                'return'  => 'objects',
            ];

            $orders = wc_get_orders($args);

            $data = [];
            foreach ($orders as $order) {
                $billing  = $order->get_address('billing');
                $shipping = $order->get_address('shipping');

                $data[] = [
                    'id'           => $order->get_id(),
                    'order_number' => $order->get_order_number(),
                    'date_created' => $order->get_date_created() ? $order->get_date_created()->date('Y-m-d H:i:s') : '',
                    'status'       => $order->get_status(),
                    'total'        => $order->get_total(),
                    'currency'     => $order->get_currency(),

                    // Payment Method
                    'payment_method' => [
                        'id'    => $order->get_payment_method(),
                        'title' => $order->get_payment_method_title(),
                    ],

                    // Billing info
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

                    // Shipping info
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

                    // Items
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
            }

            // Pagination info
            $total_orders = count( wc_get_orders( [
                'return' => 'ids',
                'limit'  => -1,
            ] ) );
            $total_pages  = ceil($total_orders / $per_page);

            return rest_ensure_response([
                'success' => true,
                'result' => [
                    'page'         => $page,
                    'per_page'     => $per_page,
                    'total_pages'  => $total_pages,
                    'total_orders' => $total_orders,
                    'orders'       => $data,
                ],
                'message' => "Get orders successfully!",
            ]);
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return new WP_Error($error_message, 500);
        }
    }
}
