<?php


namespace Zippy_Core\Products\Controllers;

use WP_REST_Request;
use WP_Error;
use Zippy_Core\Orders\Services\Order_Services;
use Zippy_Core\Utils\Zippy_Request_Helper;
use Zippy_Core\Utils\Zippy_Response_Handler;
use Zippy_Core\Utils\Zippy_Wc_Calculate_Helper;
use WC_Coupon;

class Product_Controllers
{
    public static function get_products(WP_REST_Request $request)
    {
        try {
            $category = !empty($request['category']);
            // $args = self::sanitize_products($request);
            $args = [
                'status'   => 'publish',
            ];
            $page = max(1, intval($request['page']));
            $per_page = 50;

            [$products, $total, $max_pages] = self::sort_and_paginate_products($args, $category, $page, $per_page);

            // Build data
            $data = [];
            foreach ($products as $product) {
                $data[] = [
                    'id'    => $product->get_id(),
                    'sku'   => $product->get_sku(),
                    'name'  => $product->get_name(),
                    'stock' => $product->get_stock_quantity(),
                    'img_url' => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),
                    'type'  => $product->get_type(),
                    'link'  => admin_url('post.php?post=' . $product->get_id() . '&action=edit'),
                    'min_addons' => 0,
                    'min_order'  => 0,
                    'addons'     => [],
                    'grouped_addons' => [],
                    'is_composite_product' => false,
                ];
            }

            $response = [
                'data' => $data,
                'pagination' => [
                    'total' => $total,
                    'max_num_pages' => $max_pages,
                    'page'  => $page,
                    'per_page' => $per_page,
                ],
            ];

            return empty($data)
                ? Zippy_Response_Handler::error($data, 500)
                : Zippy_Response_Handler::success($response, "Products retrieved successfully.");
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die;
            return Zippy_Response_Handler::error('Empty products', 500);
        }
    }

    private static function sort_and_paginate_products($args, $has_category, $page, $per_page)
    {
        if ($has_category) {
            $results = wc_get_products($args);
            if (empty($results) || empty($results->products)) {
                return [[], 0, 0];
            }

            $products  = Zippy_Booking_Helper::sort_products_by_category($results->products);
            $total     = $results->total;
            $max_pages = $results->max_num_pages;
        } else {
            $args['limit']    = -1;
            $args['paginate'] = false;

            $all_products = wc_get_products($args);
            if (empty($all_products)) {
                return [[], 0, 0];
            }

            $total     = count($all_products);
            $max_pages = ceil($total / $per_page);
            $offset    = ($page - 1) * $per_page;
            $products  = array_slice($all_products, $offset, $per_page);
        }

        return [$products, $total, $max_pages];
    }

    private static function sanitize_products($request)
    {
        $excluded = [15, 23]; // ['uncategorized', 'add-ons']
        $excluded_tag = ['add-ons'];
        $args = [
            'status'   => 'publish',
            'paginate' => true,
            'order'    => 'asc',
            'orderby'  => 'menu_order',
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $excluded,
                    'operator' => 'NOT IN',
                ],
                [
                    'taxonomy' => 'product_tag',
                    'field'    => 'slug',
                    'terms'    => $excluded_tag,
                    'operator' => 'NOT IN',
                ],
                [
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => ['exclude-from-catalog'],
                    'operator' => 'NOT IN',
                ],
            ],
        ];

        if (!empty($request['category'])) {
            $args['limit'] = 20;
            $args['paginate'] = true;
            $args['product_category_id'] = [intval($request['category'])];
            $args['page'] = max(1, intval($request['page']));
        } else {
            $args['limit'] = -1;
            $args['paginate'] = false;
        }

        if (!empty($request['search'])) {
            $args['s'] = sanitize_text_field($request['search']);
        }

        return $args;
    }

    public static function get_categories(WP_REST_Request $request)
    {
        try {
            $args = self::sanitize_categories($request);

            $results = get_categories($args);

            if (empty($results) || is_wp_error($results)) {
                return Zippy_Response_Handler::error('No categories found.', 500);
            }

            $excluded_tag = ['add-ons'];
            $data = [];

            foreach ($results as $category) {
                $products = wc_get_products([
                    'status'    => 'publish',
                    'limit'     => -1,
                    'category'  => [$category->slug],
                    'tax_query' => [
                        'relation' => 'AND',
                        [
                            'taxonomy' => 'product_tag',
                            'field'    => 'slug',
                            'terms'    => $excluded_tag,
                            'operator' => 'NOT IN',
                        ],
                        [
                            'taxonomy' => 'product_visibility',
                            'field'    => 'name',
                            'terms'    => ['exclude-from-catalog'],
                            'operator' => 'NOT IN',
                        ],
                    ],
                ]);

                $category->count = count($products);
                $data[] = $category;
            }

            return empty($data)
                ? Zippy_Response_Handler::error($data, 500)
                : Zippy_Response_Handler::success($data, "Products categories retrieved successfully.");
        } catch (\Exception $e) {
            return Zippy_Response_Handler::error('Empty categories', 500);
        }
    }

    private static function sanitize_categories($request)
    {

        $excluded = [];

        return [
            'taxonomy'     => 'product_cat',
            'category' => intval($request['category']) ?? [],
            'status'   => 'publish',
            "limit" => -1,
            'orderby' => 'name',
            'hide_empty'   => true,
            'show_count'   => 1, // 1 for yes, 0 for no
            'hierarchical' => 1,
            'exclude' => $excluded
        ];
    }
}
