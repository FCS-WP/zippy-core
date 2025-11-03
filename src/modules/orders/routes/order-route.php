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
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/get-order-info', array(
            'methods' => 'GET',
            'callback' => [Order_Controllers::class, 'get_order_info'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => [
                'order_id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
            ],
        ));
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/remove-item-order', array(
            'methods' => 'POST',
            'callback' => [Order_Controllers::class, 'remove_order_item'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => [
                'order_id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
                'item_id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
            ],
        ));
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/update-meta-data-order-item', array(
            'methods' => 'POST',
            'callback' => [Order_Controllers::class, 'update_meta_data_order_item'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => [
                'order_id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
                'item_id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
                'quantity' => [
                    'required' => true,
                    'type' => 'integer',
                ],
            ],
        ));
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/apply_coupon_to_order', array(
            'methods' => 'POST',
            'callback' => [Order_Controllers::class, 'apply_coupon_to_order'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => [
                'order_id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
                'coupon_code' => [
                    'required' => true,
                    'type' => 'string',
                ],
            ],
        ));
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/add-items-order', array(
            'methods' => 'POST',
            'callback' => [Order_Controllers::class, 'add_product_to_order'],
        ));
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/export-orders', array(
            'methods' => 'POST',
            'callback' => [Order_Controllers::class, 'export_orders'],
        ));
    }
}
