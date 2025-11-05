<?php

namespace Zippy_Core\Orders\Routes;

use Zippy_Core\Core_Middleware;
use Zippy_Core\Core_Route;
use Zippy_Core\Orders\Controllers\Order_Controllers;
use Zippy_Core\Orders\Models\Order_Arguments;

class Order_Route extends Core_Route
{

    public function init_module_api()
    {
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/orders', [
            'methods'  => 'GET',
            'callback' => [Order_Controllers::class, 'get_all_orders_with_pagination'],
            // 'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => Order_Arguments::get_orders_args(),
        ]);
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/bulk-action-update-order-status', [
            'methods'  => 'POST',
            'callback' => [Order_Controllers::class, 'bulk_action_update_order_status'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => Order_Arguments::get_bulk_action_update_order_status_args(),
        ]);
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/move-to-trash', [
            'methods'  => 'POST',
            'callback' => [Order_Controllers::class, 'move_to_trash'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => Order_Arguments::get_move_to_trash_args(),
        ]);
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/export-orders', array(
            'methods' => 'POST',
            'callback' => [Order_Controllers::class, 'export_orders'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => Order_Arguments::get_export_orders_args(),
        ));

        //Order Detail
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/get-order-info', array(
            'methods' => 'GET',
            'callback' => [Order_Controllers::class, 'get_order_info'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => Order_Arguments::get_order_info_args(),
        ));
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/remove-item-order', array(
            'methods' => 'POST',
            'callback' => [Order_Controllers::class, 'remove_order_item'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => Order_Arguments::get_remove_item_order_args(),
        ));
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/update-quantity-order-item', array(
            'methods' => 'POST',
            'callback' => [Order_Controllers::class, 'update_quantity_order_item'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => Order_Arguments::get_update_quantity_order_item_args(),
        ));
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/apply_coupon_to_order', array(
            'methods' => 'POST',
            'callback' => [Order_Controllers::class, 'apply_coupon_to_order'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => Order_Arguments::get_apply_coupon_to_order_args(),
        ));
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/add-items-order', array(
            'methods' => 'POST',
            'callback' => [Order_Controllers::class, 'add_product_to_order'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => Order_Arguments::get_add_items_order_args(),
        ));
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/export-orders', array(
            'methods' => 'POST',
            'callback' => [Order_Controllers::class, 'export_orders'],
        ));
    }
}
