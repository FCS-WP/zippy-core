<?php

namespace Zippy_Core\Settings\Services;

class Order_Setting_Services
{
    /**
     * Get configs
     */

    public static function get_invoices_options()
    {
        $option_key = 'core_module_configs_order_invoices';
        $configs = get_option($option_key, []);

        if (! is_array($configs)) {
            $configs = [];
        }
        $formatted = [];

        foreach ($configs as $key => $value) {
            $formatted[] = [
                'key'   => $key,
                'input_value' => $value,
            ];
        }

        return $formatted;
    }

    /**
     * init core configs
     */

    public static function init_invoices_option()
    {
        $init_configs = [
            'invoice_logo'      => esc_url(wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full')[0]),
            'company_address' => '',
            'company_phone' => '',
        ];

        $option_key = 'core_module_configs_order_invoices';
        $existing = get_option($option_key);

        if (! is_array($existing)) {
            add_option($option_key, $init_configs);
            $existing = $init_configs;
        }
        return $existing;
    }

    /**
     * update new invoices settings
     */

    public static function update_invoices_options($data)
    {
        $option_key = 'core_module_configs_order_invoices';

        // Get current config
        $configs = get_option($option_key, []);

        // Normalize: if the option doesn’t exist, make it an array
        if (! is_array($configs)) {
            $configs = [];
        }

        foreach ($data as $new_item) {
            $configs[$new_item['key']] = $new_item['input_value'];
        }

        update_option($option_key, $configs);
        return $configs;
    }
}
