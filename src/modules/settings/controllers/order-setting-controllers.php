<?php

namespace Zippy_Core\Settings\Controllers;

use WP_REST_Request;
use Zippy_Core\Settings\Services\Order_Setting_Services;
use Zippy_Core\Utils\Zippy_Response_Handler;

class Order_Setting_Controllers
{

    public static function get_invoices_options(WP_REST_Request $request)
    {
        try {
            $configs = Order_Setting_Services::get_invoices_options();
            return Zippy_Response_Handler::success(["result" => $configs], 'Get invoices settings successfully!');
        } catch (\Exception $e) {
            return Zippy_Response_Handler::error($e->getMessage());
        }
    }

    public static function handle_update_invoices_options(WP_REST_Request $request)
    {
        try {
            $data = $request->get_param('new_invoices_options');
            $newConfigs = Order_Setting_Services::update_invoices_options($data);
            return Zippy_Response_Handler::success(["result" => $newConfigs], 'Update invoices successfully!');
        } catch (\Exception $e) {
            return Zippy_Response_Handler::error($e->getMessage());
        }
    }
}
