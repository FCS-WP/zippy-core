<?php

namespace Zippy_Core\Settings\Routes;

use Zippy_Core\Core_Middleware;
use Zippy_Core\Core_Route;
use Zippy_Core\Settings\Controllers\Setting_Controllers;
use Zippy_Core\Settings\Models\Order_Arguments;
use Zippy_Core\Settings\Models\Setting_Arguments;

class Setting_Routes extends Core_Route
{

    public function init_module_api()
    {
        register_rest_route(ZIPPY_CORE_API_PREFIX, '/core-settings', [
            'methods'  => 'GET',
            'callback' => [Setting_Controllers::class, 'get_modules'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            'args' => Setting_Arguments::get_setting_args(),
        ]);
         register_rest_route(ZIPPY_CORE_API_PREFIX, '/core-settings', [
            'methods'  => 'POST',
            'callback' => [Setting_Controllers::class, 'handle_update_module_settings'],
            'permission_callback' => [Core_Middleware::class, 'admin_only'],
            // 'args' => Setting_Arguments::get_setting_args(),
        ]);
    }
}
