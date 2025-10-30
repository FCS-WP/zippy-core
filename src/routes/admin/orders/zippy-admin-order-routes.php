<?php

namespace Zippy_Core\Src\Routes\Admin\Orders;

use Zippy_Core\Src\Controllers\Zippy_Orders_Controllers;

class Zippy_Admin_Order_Routes
{
    protected static $_instance = null;

    /**
     * 
     * @return Zippy_Admin_Order_Routes
     */

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Auto run & init function
     * @return void;
     */

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'zippy_orders_init_api'));

    }

    /**
     * Init endpoint
     */
    public function zippy_orders_init_api()
    {
        register_rest_route( ZIPPY_CORE_API_PREFIX, '/orders', [
            'methods'  => 'GET',
            'callback' => [Zippy_Orders_Controllers::class, 'get_all_orders_with_pagination'],
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
        ]);
    }
}
