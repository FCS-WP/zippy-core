<?php

namespace Zippy_Core\Src\Controllers\Shipping;

use WP_REST_Request;
use Zippy_Core\Src\Helpers\Shipping_Helper;

defined('ABSPATH') or die();

class Zippy_Shipping_Controller
{
    public static function get_shipping_configs(WP_REST_Request $request)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'shipping_by_categories';

        $results = $wpdb->get_results("SELECT id, category_ids, name, note, shipping_fee FROM {$table_name}", ARRAY_A);


        if (empty($results)) {
            return rest_ensure_response([
                'success' => true,
                'results'    => [],
                'min_cost'  => floatval(get_option('shipping_config_min_cost', 0)),
                'is_active' => intval(get_option('shipping_config_is_active', -1)),
            ]);
        }

        $response = [];

        foreach ($results as $row) {
            $category_ids = json_decode($row['category_ids'], true);

            if (!is_array($category_ids)) {
                $category_ids = [];
            }

            $categories = [];
            foreach ($category_ids as $cat_id) {
                $term = get_term($cat_id, 'product_cat');
                if ($term && !is_wp_error($term)) {
                    $categories[] = [
                        'id'   => $term->term_id,
                        'name' => $term->name,
                    ];
                }
            }

            $response[] = [
                'id'                => intval($row['id']),
                'name'                => $row['name'],
                'note'                => $row['note'],
                'category_includes' => $categories,
                'shipping_fee'      => floatval($row['shipping_fee']),
            ];
        }

        return rest_ensure_response([
            'success' => true,
            'results'    => $response,
            'min_cost'  => floatval(get_option('shipping_config_min_cost', 0)),
            'is_active' => intval(get_option('shipping_config_is_active', -1)),
        ]);
    }

    public static function update_shipping_configs(WP_REST_Request $request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'shipping_by_categories';

        $params = $request->get_params();
        $rules = [
            'name'         => 'string',
            'category_ids' => 'array',
            'shipping_fee' => 'float',
        ];

        $validation = Shipping_Helper::validate_request_data($params, $rules);

        if (is_wp_error($validation)) {
            return $validation;
        }

        $config_id     = isset($params['id']) ? intval($params['id']) : 0;
        $name          = sanitize_text_field($params['name']);
        $note          = sanitize_text_field($params['note']);
        $shipping_fee  = floatval($params['shipping_fee']);
        $category_ids  = array_map('intval', $params['category_ids']);
        $category_ids_json = wp_json_encode($category_ids);

        self::create_shipping_by_categories_table();

        $data = [
            'name'          => $name,
            'note'          => $note,
            'category_ids'  => $category_ids_json,
            'shipping_fee'  => $shipping_fee,
            'updated_at'    => current_time('mysql'),
        ];

        if ($config_id > 0) {
            $updated = $wpdb->update(
                $table_name,
                $data,
                ['id' => $config_id],
                ['%s', '%s', '%s', '%f', '%s'],
                ['%d']
            );
        } else {

            $data['created_at'] = current_time('mysql');
            $wpdb->insert(
                $table_name,
                $data,
                ['%s', '%s', '%s', '%f', '%s', '%s']
            );
            $config_id = $wpdb->insert_id;
        }

        return rest_ensure_response([
            'success' => true,
            'message' => 'Shipping config saved.',
            'id'      => $config_id
        ]);
    }

    public static function remove_shipping_configs(WP_REST_Request $request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'shipping_by_categories';

        $ids = $request->get_param('ids');

        if (!is_array($ids) || empty($ids)) {
            return new WP_Error('invalid_ids', 'The "ids" parameter must be a non-empty array.', ['status' => 400]);
        }

        $ids = array_map('intval', $ids);

        $placeholders = implode(',', array_fill(0, count($ids), '%d'));

        $query = "DELETE FROM {$table_name} WHERE id IN ($placeholders)";
        $prepared_query = $wpdb->prepare($query, ...$ids);
        $result = $wpdb->query($prepared_query);

        if ($result === false) {
            return new WP_Error('db_error', 'Failed to delete shipping configs.', ['status' => 500]);
        }

        return rest_ensure_response([
            'success' => true,
            'message' => "Deleted {$result} shipping config(s).",
        ]);
    }

    public static function create_shipping_by_categories_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'shipping_by_categories';

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                category_ids TEXT NOT NULL,
                note TEXT NOT NULL,
                name VARCHAR(255) NOT NULL,
                shipping_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";

            dbDelta($sql);
        }
    }

    public static function save_configs(WP_REST_Request $request)
    {
        $params = $request->get_params();

        $rules = [
            'min_cost'     => 'float',
            'is_active' => 'boolean',
        ];

        $validation = Shipping_Helper::validate_request_data($params, $rules);

        if (is_wp_error($validation)) {
            return $validation;
        }
        
        $is_active = filter_var($params['is_active'], FILTER_VALIDATE_BOOLEAN);
        $save_active_value = $is_active ? 1 : 0;

        update_option('shipping_config_min_cost', floatval($params['min_cost']));
        update_option('shipping_config_is_active', $save_active_value);

        return rest_ensure_response([
            'success' => true,
            'results'    => [
                'min_cost'  => floatval(get_option('shipping_config_min_cost', 0)),
                'is_active' => intval(get_option('shipping_config_is_active', -1)),
            ],
        ]);
    }
}
