<?php

namespace Zippy_Core\Orders\Routes;

use Zippy_Core\Core_Route;
use Zippy_Core\Orders\Controllers\Order_Controllers;

class Order_Detail_Route extends Core_Route {

    public function orders_init_api() {
        register_rest_route( ZIPPY_CORE_API_PREFIX, '/get-order', [
            'methods'  => 'GET',
            'callback' => [ Order_Controllers::class, 'get_order_detail_by_id' ],
            'permission_callback' => '__return_true',
        ] );
    }
}
