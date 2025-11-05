<?php

namespace Zippy_Core\Settings\Controllers;

use WP_REST_Request;
use WP_Error;
use Zippy_Core\Settings\Services\Setting_Services;

class Setting_Controllers {
    public static function get_modules(WP_REST_Request $request) 
    {
        try {
            $configs = Setting_Services::get_all_modules_option();
            $result = array(
                'modules' => $configs,
                'total_modules' => count($configs),
            );
            return rest_ensure_response([
                'success' => true,
                'result'  => $result,
                'message' => "Get configs successfully!",
            ]); 
        } catch (\Exception $e) {
            return new WP_Error('get_modules_error', $e->getMessage(), ['status' => 500]);
        }
    }

    public static function handle_update_module_settings(WP_REST_Request $request) 
    {
        try {
            $data = $request->get_param('new_values');
            $newConfigs = Setting_Services::update_module_configs($data);

            $result = array(
                'modules' => $newConfigs,
                'total_modules' => count($newConfigs),
            );
            return rest_ensure_response([
                'success' => true,
                'result'  => $result,
                'message' => "Update configs successfully!",
            ]); 
        } catch (\Exception $e) {
            return new WP_Error('update_modules_error', $e->getMessage(), ['status' => 500]);
        }
    }

}