<?php

namespace Zippy_Core\Src\Controllers\Admin;

use WP_REST_Request;
use WP_Query;


defined('ABSPATH') or die();

class Zippy_Support_Products
{
    public static function search_products(WP_REST_Request $request)
    {
        $keyword = sanitize_text_field($request->get_param('keyword'));

        if (empty($keyword)) {
            return new WP_Error('no_keyword', 'Keyword parameter is required.', ['status' => 400]);
        }

        $args = [
            'post_type'      => 'product',
            'posts_per_page' => 10,
            's'              => $keyword,
            'post_status'    => 'publish',
        ];

        $query = new WP_Query($args);

        $products = [];

        foreach ($query->posts as $post) {
            $product = wc_get_product($post->ID);

            if ($product) {
                $products[] = [
                    'id'       => $product->get_id(),
                    'name'     => $product->get_name(),
                    'price'    => $product->get_price(),
                    'image'    => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),
                    'permalink' => get_permalink($product->get_id()),
                ];
            }
        }

        return rest_ensure_response([
            'success' => true,
            'results' => $products,
        ]);
    }

    public static function search_categories(WP_REST_Request $request)
    {
        $keyword = sanitize_text_field($request->get_param('keyword'));

        if (empty($keyword)) {
            return new WP_Error('no_keyword', 'Keyword parameter is required.', ['status' => 400]);
        }

        $args = [
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'number'     => 10,
            'search'     => $keyword,
        ];

        $categories = get_terms($args);

        $results = [];

        foreach ($categories as $cat) {
            $results[] = [
                'id'          => $cat->term_id,
                'name'        => $cat->name,
                'slug'        => $cat->slug,
                'count'       => $cat->count,
                'link'        => get_term_link($cat),
            ];
        }

        return rest_ensure_response([
            'success' => true,
            'results' => $results,
        ]);
    }
}
