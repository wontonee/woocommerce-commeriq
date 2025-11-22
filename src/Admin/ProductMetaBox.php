<?php
namespace CommerIQ\Admin;

defined('ABSPATH') || exit;

use CommerIQ\API\ApiClient;
use CommerIQ\Models\CommerIQ_Result;

class ProductMetaBox
{
    public static function ajax_run_comparison()
    {
        check_ajax_referer('commeriq_product_nonce', '_nonce');
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        if (!$post_id) {
            wp_send_json_error(['message' => 'Missing product id'], 400);
        }

        if (!function_exists('wc_get_product')) {
            wp_send_json_error(['message' => 'WooCommerce not available'], 500);
        }

        $product = wc_get_product($post_id);
        if (!$product) {
            wp_send_json_error(['message' => 'Product not found'], 404);
        }

        $title = $product->get_name();
        $sku = $product->get_sku();
        $price = floatval($product->get_price());

        // Attempt to read cost price from common meta keys
        $cost = 0;
        $cost_meta_keys = ['_cost', '_purchase_price', 'cost'];
        foreach ($cost_meta_keys as $k) {
            $v = get_post_meta($post_id, $k, true);
            if ($v !== '') {
                $cost = floatval($v);
                break;
            }
        }

        $tags = wp_get_post_terms($post_id, 'product_tag', ['fields' => 'names']);

        // derive store values from WooCommerce (country, currency, state)
        if (class_exists('CommerIQ\\Helpers\\Utils')) {
            $store_values = \CommerIQ\Helpers\Utils::derive_from_woocommerce();
        } else {
            $store_values = ['country' => '', 'currency' => '', 'state' => ''];
        }

        $payload = [
            'source' => 'wordpress-commeriq-plugin',
            'store' => [
                'country' => isset($store_values['country']) ? $store_values['country'] : '',
                'currency' => isset($store_values['currency']) ? $store_values['currency'] : '',
                'state' => isset($store_values['state']) ? $store_values['state'] : '',
            ],
            'product' => [
                'id' => $post_id,
                'title' => $title,
                'sku' => $sku,
                'price' => $price,
                'cost_price' => $cost,
                'tags' => $tags,
            ],
        ];

        // For v1: if licence is demo, return a demo response and save it. API integration will be added later.
        $licence = get_option('commeriq_license', []);
        $licence_key = isset($licence['licence_key']) ? $licence['licence_key'] : 'demo';

        if ($licence_key === 'demo') {
            $now = gmdate('c');
            $demo_resp = [
                'product_id' => $post_id,
                'timestamp' => $now,
                'comparisons' => [
                    ['source' => 'DemoMarket', 'price' => $price, 'confidence' => 0.5],
                ],
                'suggested_price' => ['min' => max(0, $price * 0.9), 'max' => $price * 1.1],
                'margin' => ['gross_margin' => round((($price - $cost) / max(1, $price)) * 100, 2)],
                'confidence_score' => 0.5,
            ];

            CommerIQ_Result::save($post_id, $demo_resp);
            wp_send_json_success(['message' => 'Demo comparison saved', 'response' => $demo_resp]);
        }

        wp_send_json_error(['message' => 'API not configured'], 400);
    }
}
