<?php
namespace CommerIQ\Jobs;

defined('ABSPATH') || exit;

use CommerIQ\API\ApiClient;
use CommerIQ\Models\CommerIQ_Result;

class CommerIQ_SyncJob
{
    public static function run($product_ids = [])
    {
        if (empty($product_ids)) {
            // default: sync recent products - for now, bail to avoid heavy operations
            return;
        }

        $settings = get_option('commeriq_settings', []);
        $endpoint = isset($settings['api_endpoint']) ? $settings['api_endpoint'] : '';
        $apiKey = isset($settings['api_key']) ? $settings['api_key'] : '';
        $apiSecret = isset($settings['api_secret']) ? $settings['api_secret'] : '';

        if (empty($endpoint) || empty($apiKey) || empty($apiSecret)) {
            return;
        }

        foreach ($product_ids as $post_id) {
            try {
                if (!function_exists('wc_get_product')) {
                    continue;
                }
                $product = wc_get_product($post_id);
                if (!$product) {
                    continue;
                }

                $payload = [
                    'source' => 'wordpress-commeriq-plugin',
                    'store' => get_option('commeriq_store_config', []),
                    'product' => [
                        'id' => $post_id,
                        'title' => $product->get_name(),
                        'sku' => $product->get_sku(),
                        'price' => floatval($product->get_price()),
                    ],
                ];

                $client = new ApiClient($endpoint, $apiKey, $apiSecret);
                $resp = $client->postComparison($payload);
                CommerIQ_Result::save($post_id, $resp);
            } catch (\Exception $e) {
                // log or ignore for now
            }
        }
    }
}
