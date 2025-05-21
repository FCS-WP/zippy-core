<?php

namespace Zippy_Core\Src\Routers\Shipping;

use Zippy_Core\Src\Controllers\Admin\Zippy_Support_Products;
use Zippy_Core\Src\Controllers\Shipping\Zippy_Shipping_Controller;

defined('ABSPATH') or die();


class Zippy_Shipping_Router
{
    protected static $_instance = null;

    /**
     * @return Zippy_Shipping_Router
     */

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'zippy_shipping_init_api'));

    }


    public function zippy_shipping_init_api()
    {
        register_rest_route(ZIPPY_SHIPPING_API_NAMESPACE, '/products', array(
            'methods' => 'GET',
            'callback' => [Zippy_Support_Products::class, 'search_products'],
            'permission_callback' => '__return_true',
        ));

        register_rest_route(ZIPPY_SHIPPING_API_NAMESPACE, '/categories', array(
            'methods' => 'GET',
            'callback' => [Zippy_Support_Products::class, 'search_categories'],
            'permission_callback' => '__return_true',
        ));

        register_rest_route(ZIPPY_SHIPPING_API_NAMESPACE, '/shipping', array(
            'methods' => 'GET',
            'callback' => [Zippy_Shipping_Controller::class, 'get_shipping_configs'],
            'permission_callback' => '__return_true',
        ));

        register_rest_route(ZIPPY_SHIPPING_API_NAMESPACE, '/shipping', array(
            'methods' => 'PUT',
            'callback' => [Zippy_Shipping_Controller::class, 'update_shipping_configs'],
            'permission_callback' => '__return_true',
        ));

        register_rest_route(ZIPPY_SHIPPING_API_NAMESPACE, '/shipping', array(
            'methods' => 'DELETE',
            'callback' => [Zippy_Shipping_Controller::class, 'remove_shipping_configs'],
            'permission_callback' => '__return_true',
        ));
    }
}