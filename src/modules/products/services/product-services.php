<?php

namespace Zippy_Core\Products\Services;

use WC_Order_Item_Product;
use WC_Tax;

class Product_Services
{
    /**
     * Get products list
     * @param mixed $infos
     * @return array{data: array, pagination: array{max_num_pages: mixed, page: int, per_page: int, total: mixed}}
     */
    public static function get_products($infos)
    {
        $is_pre_order = $infos['is_pre_order'] == 'true' ? true : false;
        $category = !empty($infos['category']);
        $args = [
            'status'   => 'publish',
        ];
        $page = max(1, intval($infos['page']));
        $per_page = 50;

        [$products, $total, $max_pages] = self::sort_and_paginate_products($args, $category, $page, $per_page);

        // Build data
        $data = [];
        foreach ($products as $product) {
            if ($is_pre_order) {
                $product_pre_order = get_field('pre_order', $product->get_id());
                if (empty($product_pre_order)) {
                    continue;
                }
            }

            $addons = self::get_product_addons($product);
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
                'addons'     => $addons,
                'grouped_addons' => [],
                'is_composite_product' => false,
            ];
        }

        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'max_num_pages' => $max_pages,
                'page'  => $page,
                'per_page' => $per_page,
            ],
        ];
    }

    private static function get_product_addons($product)
    {
        $result = [];
        $products_combo = get_field('products_combo', $product->get_id()) ?? [];

        foreach ($products_combo as $combo) {
            $prod = wc_get_product($combo['product_option']);
            if ($prod) {
                $result[] = [
                    'id'    => $prod->get_id(),
                    'sku'   => $prod->get_sku(),
                    'name'  => $prod->get_name(),
                    'stock' => $prod->get_stock_quantity(),
                    'image' => wp_get_attachment_image_url($prod->get_image_id(), 'thumbnail'),
                    'price' => $prod->get_price(),
                ];
            }
        }
        return $result;
    }

    private static function sort_and_paginate_products($args, $has_category, $page, $per_page)
    {
        if ($has_category) {
            $results = wc_get_products($args);
            if (empty($results) || empty($results->products)) {
                return [[], 0, 0];
            }

            $products  = $results->products;
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

    /**
     * Get categories
     * @param mixed $category
     * @return array
     */
    public static function get_product_categories()
    {
        $args = self::sanitize_categories();

        $results = get_categories($args);

        if (empty($results) || is_wp_error($results)) {
            return [];
        }

        $excluded_tag = [];
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

        return $data;
    }

    private static function sanitize_categories($category = null)
    {
        $excluded = [];

        return [
            'taxonomy'     => 'product_cat',
            'category' => intval($category) ?? [],
            'status'   => 'publish',
            "limit" => -1,
            'orderby' => 'name',
            'hide_empty'   => true,
            'show_count'   => 1, // 1 for yes, 0 for no
            'hierarchical' => 1,
            'exclude' => $excluded
        ];
    }

    public static function get_product_by_id($productID)
    {
        $product = wc_get_product($productID);
        if (!$product) {
            return [];
        }

        return [
            'id'    => $product->get_id(),
            'sku'   => $product->get_sku(),
            'name'  => $product->get_name(),
            'stock' => $product->get_stock_quantity(),
            'img_url' => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),
            'type'  => $product->get_type(),
            'link'  => admin_url('post.php?post=' . $product->get_id() . '&action=edit'),
            'min_addons' => 0,
            'min_order'  => 0,
            'addons'     => self::get_product_addons($product),
            'grouped_addons' => [],
            'is_composite_product' => false,
        ];
    }
}
