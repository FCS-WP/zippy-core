<?php
namespace Zippy_Core\Orders\Routes;

use Zippy_Core\Core_Route;
use Zippy_Core\Orders\Controllers\Order_Controllers;

class Order_Route extends Core_Route {

    public function init_module_api() {
        register_rest_route( ZIPPY_CORE_API_PREFIX, '/orders', [
            'methods'  => 'GET',
            'callback' => [ Order_Controllers::class, 'get_all_orders_with_pagination' ],
            'permission_callback' => '__return_true',
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
        ] );
    }
}