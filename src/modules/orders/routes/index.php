<?php

namespace Zippy_Core\Orders\Routes;

use Zippy_Core\Core_Middleware;
use Zippy_Core\Core_Route;
use Zippy_Core\Orders\Controllers\Order_Controllers;

class Order_Route extends Core_Route
{

    public function init_module_api()
    {
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/orders', [
            'methods'  => 'GET',
            'callback' => [Order_Controllers::class, 'get_all_orders_with_pagination'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => [
                'page' => [
                    'default' => 1,
                    'sanitize_callback' => 'absint',
                ],
                'per_page' => [
                    'default' => 10,
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/update-order-status', [
            'methods'  => 'POST',
            'callback' => [Order_Controllers::class, 'update_order_status'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => [
                'order_ids' => [
                    'required' => true,
                    'type' => 'array',
                    'items' => [
                        'type' => 'integer',
                    ],
                ],
                'status' => [
                    'required' => true,
                    'type' => 'string',
                ],
            ],
        ]);
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/move-to-trash', [
            'methods'  => 'POST',
            'callback' => [Order_Controllers::class, 'move_to_trash'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => [
                'order_ids' => [
                    'required' => true,
                    'type' => 'array',
                    'items' => [
                        'type' => 'integer',
                    ],
                ],
            ],
        ]);
    }
}
