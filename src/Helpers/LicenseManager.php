<?php
namespace CommerIQ\Helpers;

defined('ABSPATH') || exit;

class LicenseManager
{
    const OPTION_KEY = 'commeriq_license';

    /**
     * Register license with API
     */
    public static function register_license($license_key, $domain_name)
    {
        $api_url = \CommerIQ\ApiConfig::get_endpoint_url('register_license');
        
        $body = [
            'license_key' => sanitize_text_field($license_key),
            'domain' => sanitize_text_field($domain_name),
        ];

        $args = [
            'method' => 'POST',
            'timeout' => \CommerIQ\ApiConfig::get_timeout(),
            'sslverify' => \CommerIQ\ApiConfig::should_verify_ssl(),
            'body' => $body,
        ];

        $response = wp_remote_post($api_url, $args);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => $response->get_error_message(),
            ];
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);

        // Handle both response formats: {status: "success"} and {success: true}
        $is_success = false;
        if (isset($data['status']) && $data['status'] === 'success') {
            $is_success = true;
        } elseif (isset($data['success']) && $data['success']) {
            $is_success = true;
        }

        if ($response_code === 200 && $is_success) {
            // Save license information
            $license_data = [
                'licence_key' => $license_key,
                'domain_name' => $domain_name,
                'activated_at' => current_time('mysql'),
                'status' => 'active',
                'plan' => isset($data['plan']) ? $data['plan'] : '',
            ];
            
            update_option(self::OPTION_KEY, $license_data);
            
            return [
                'success' => true,
                'message' => isset($data['message']) ? $data['message'] : __('License activated successfully', 'wontonee-commeriq'),
                'data' => $license_data,
            ];
        }

        // Handle error response
        $error_message = __('License activation failed', 'wontonee-commeriq');
        if (isset($data['message'])) {
            $error_message = $data['message'];
        } elseif (isset($data['status']) && $data['status'] === 'error') {
            $error_message = isset($data['message']) ? $data['message'] : $error_message;
        }

        return [
            'success' => false,
            'message' => $error_message,
            'debug_info' => [
                'url' => $api_url,
                'response_code' => $response_code,
                'response_body' => $response_body,
            ]
        ];
    }

    /**
     * Remove license registration
     */
    public static function remove_license($license_key, $domain_name)
    {
        $api_url = \CommerIQ\ApiConfig::get_endpoint_url('remove_license');
        
        $body = [
            'license_key' => sanitize_text_field($license_key),
            'domain' => sanitize_text_field($domain_name),
        ];

        $args = [
            'method' => 'POST',
            'timeout' => \CommerIQ\ApiConfig::get_timeout(),
            'sslverify' => \CommerIQ\ApiConfig::should_verify_ssl(),
            'body' => $body,
        ];

        $response = wp_remote_post($api_url, $args);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => $response->get_error_message(),
            ];
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);

        // Handle both response formats: {status: "success"} and {success: true}
        $is_success = false;
        if (isset($data['status']) && $data['status'] === 'success') {
            $is_success = true;
        } elseif (isset($data['success']) && $data['success']) {
            $is_success = true;
        }

        if ($response_code === 200 && $is_success) {
            // Clear license information
            update_option(self::OPTION_KEY, [
                'licence_key' => '',
                'domain_name' => '',
                'activated_at' => '',
                'status' => 'inactive',
            ]);
            
            return [
                'success' => true,
                'message' => isset($data['message']) ? $data['message'] : __('License removed successfully', 'wontonee-commeriq'),
            ];
        }

        // Handle error response
        $error_message = __('License removal failed', 'wontonee-commeriq');
        if (isset($data['message'])) {
            $error_message = $data['message'];
        } elseif (isset($data['status']) && $data['status'] === 'error') {
            $error_message = isset($data['message']) ? $data['message'] : $error_message;
        }

        return [
            'success' => false,
            'message' => $error_message,
        ];
    }

    /**
     * Validate license
     */
    public static function validate_license($license_key, $domain_name)
    {
        $api_url = \CommerIQ\ApiConfig::get_endpoint_url('validate_license');
        
        $payload = [
            'license_key' => sanitize_text_field($license_key),
            'domain' => sanitize_text_field($domain_name),
        ];

        $response = wp_remote_post($api_url, [
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => wp_json_encode($payload),
            'timeout' => \CommerIQ\ApiConfig::get_timeout(),
            'sslverify' => \CommerIQ\ApiConfig::should_verify_ssl(),
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);

        return $response_code === 200 && isset($data['success']) && $data['success'];
    }

    /**
     * Get stored license information
     */
    public static function get_license()
    {
        return get_option(self::OPTION_KEY, [
            'licence_key' => '',
            'domain_name' => '',
            'activated_at' => '',
            'status' => 'inactive',
        ]);
    }

    /**
     * Check if license is active
     */
    public static function is_license_active()
    {
        $license = self::get_license();
        return !empty($license['licence_key']) && !empty($license['activated_at']) && $license['status'] === 'active';
    }

    /**
     * Get current domain
     */
    public static function get_current_domain()
    {
        return isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '';
    }
}
