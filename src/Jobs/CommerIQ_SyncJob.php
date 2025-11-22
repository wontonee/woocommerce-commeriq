<?php
namespace CommerIQ\Jobs;

defined('ABSPATH') || exit;

use CommerIQ\API\ApiClient;
use CommerIQ\Models\CommerIQ_Result;
use CommerIQ\Helpers\LicenseManager;

class CommerIQ_SyncJob
{
    /**
     * Run sync job for specified products or recent products
     * 
     * @param array $product_ids Array of product IDs to sync. If empty, syncs recent products.
     * @return void
     */
    public static function run($product_ids = [])
    {
        // Check if license is active
        if (!class_exists('CommerIQ\\Helpers\\LicenseManager') || !LicenseManager::is_license_active()) {
// error_log('CommerIQ Sync Job: License not active. Aborting sync.');
            return;
        }

        // If no product IDs provided, get recently modified products
        if (empty($product_ids)) {
            $product_ids = self::get_recent_products();
            
            if (empty($product_ids)) {
// error_log('CommerIQ Sync Job: No products to sync.');
                return;
            }
            
// error_log('CommerIQ Sync Job: Auto-selected ' . count($product_ids) . ' recent products for sync.');
        }

        // Get API settings from old option (for backward compatibility)
        $settings = get_option('commeriq_settings', []);
        $endpoint = isset($settings['api_endpoint']) ? $settings['api_endpoint'] : '';
        $apiKey = isset($settings['api_key']) ? $settings['api_key'] : '';
        $apiSecret = isset($settings['api_secret']) ? $settings['api_secret'] : '';

        if (empty($endpoint) || empty($apiKey) || empty($apiSecret)) {
// error_log('CommerIQ Sync Job: Missing API credentials. Please configure API settings.');
            return;
        }

        $success_count = 0;
        $error_count = 0;

        foreach ($product_ids as $post_id) {
            try {
                if (!function_exists('wc_get_product')) {
// error_log('CommerIQ Sync Job: WooCommerce not available.');
                    continue;
                }
                
                $product = wc_get_product($post_id);
                if (!$product) {
// error_log('CommerIQ Sync Job: Product ID ' . $post_id . ' not found.');
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
                
                $success_count++;
            } catch (\Exception $e) {
                $error_count++;
// error_log('CommerIQ Sync Job: Error syncing product ID ' . $post_id . ': ' . $e->getMessage());
            }
        }

// error_log('CommerIQ Sync Job: Completed. Success: ' . $success_count . ', Errors: ' . $error_count);
    }

    /**
     * Get recently modified products (last 7 days, max 10 products)
     * 
     * @return array Array of product IDs
     */
    private static function get_recent_products()
    {
        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            'orderby' => 'modified',
            'order' => 'DESC',
            'date_query' => [
                [
                    'column' => 'post_modified',
                    'after' => '7 days ago',
                ],
            ],
            'fields' => 'ids',
        ];

        $query = new \WP_Query($args);
        return $query->posts;
    }
}
