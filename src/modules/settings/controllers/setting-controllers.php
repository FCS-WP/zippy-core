<?php

namespace Zippy_Core\Settings\Controllers;

use WP_REST_Request;
use Zippy_Core\Settings\Services\Setting_Services;
use Zippy_Core\Utils\Zippy_Response_Handler;

class Setting_Controllers
{
    public static function get_modules(WP_REST_Request $request)
    {
        try {
            $configs = Setting_Services::get_all_modules_option();
            $result = array(
                'result' => [
                    'modules' => $configs,
                    'total_modules' => count($configs),
                ]
            );
            return Zippy_Response_Handler::success($result, 'Get modules successfully!');
        } catch (\Exception $e) {
            return Zippy_Response_Handler::error($e->getMessage());
        }
    }

    public static function handle_update_module_settings(WP_REST_Request $request)
    {
        try {
            $data = $request->get_param('new_values');
            $newConfigs = Setting_Services::update_module_configs($data);

            $result = array(
                'result' => [
                    'modules' => $newConfigs,
                    'total_modules' => count($newConfigs),
                ]

            );
            return Zippy_Response_Handler::success($result, 'Update modules successfully!');
        } catch (\Exception $e) {
            return Zippy_Response_Handler::error($e->getMessage());
        }
    }
}
