<?php
namespace CommerIQ\REST;

defined('ABSPATH') || exit;

use CommerIQ\API\ApiClient;
use CommerIQ\Models\CommerIQ_Result;

class RestEndpoints
{
    public static function register()
    {
        add_action('rest_api_init', function () {
            register_rest_route('commeriq/v1', '/run-comparison', [
                'methods' => 'POST',
                'callback' => [__CLASS__, 'rest_run_comparison'],
                'permission_callback' => function () {
                    return current_user_can('manage_woocommerce');
                },
            ]);
        });
    }

    public static function rest_run_comparison($request)
    {
        $params = $request->get_json_params();
        $product_id = isset($params['product']['id']) ? intval($params['product']['id']) : 0;
        if (!$product_id) {
            return new \WP_Error('missing_product', 'Missing product id', ['status' => 400]);
        }

        $settings = get_option('commeriq_settings', []);
        $endpoint = isset($settings['api_endpoint']) ? $settings['api_endpoint'] : '';
        $apiKey = isset($settings['api_key']) ? $settings['api_key'] : '';
        $apiSecret = isset($settings['api_secret']) ? $settings['api_secret'] : '';

        if (empty($endpoint) || empty($apiKey) || empty($apiSecret)) {
            return new \WP_Error('not_configured', 'API credentials not configured', ['status' => 400]);
        }

        try {
            $client = new ApiClient($endpoint, $apiKey, $apiSecret);
            $resp = $client->postComparison($params);
            CommerIQ_Result::save($product_id, $resp);
            return rest_ensure_response(['message' => 'ok', 'response' => $resp]);
        } catch (\Exception $e) {
            return new \WP_Error('api_error', $e->getMessage(), ['status' => 500]);
        }
    }
}
