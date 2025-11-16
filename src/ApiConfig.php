<?php
/**
 * API Configuration Helper
 * Provides centralized API URL management using routes.php configuration
 */

namespace CommerIQ;

defined('ABSPATH') || exit;

class ApiConfig
{
    private static $config = null;

    /**
     * Load configuration from routes.php
     */
    private static function load_config()
    {
        if (self::$config === null) {
            $config_file = COMMERIQ_PLUGIN_DIR . 'routes.php';
            if (file_exists($config_file)) {
                self::$config = require $config_file;
            } else {
                // Fallback to constants for environment and base URLs only
                // Endpoints MUST be defined in routes.php
                self::$config = [
                    'environment' => defined('COMMERIQ_API_ENV') ? COMMERIQ_API_ENV : 'local',
                    'api_urls' => [
                        'local' => defined('COMMERIQ_API_LOCAL') ? COMMERIQ_API_LOCAL : 'https://licenseapp.test/api/commeriq',
                        'production' => defined('COMMERIQ_API_PRODUCTION') ? COMMERIQ_API_PRODUCTION : 'https://myapps.wontonee.com/api/commeriq',
                    ],
                    'endpoints' => [],
                ];
            }
        }
        return self::$config;
    }

    /**
     * Get current environment (local or production)
     */
    public static function get_environment()
    {
        $config = self::load_config();
        return $config['environment'] ?? 'local';
    }

    /**
     * Get API base URL for current environment
     */
    public static function get_base_url()
    {
        $config = self::load_config();
        $env = self::get_environment();
        return $config['api_urls'][$env] ?? $config['api_urls']['local'];
    }

    /**
     * Get full API endpoint URL
     * 
     * @param string $endpoint_key Key from routes.php endpoints array
     * @return string Full API URL
     */
    public static function get_endpoint_url($endpoint_key)
    {
        $config = self::load_config();
        $base_url = rtrim(self::get_base_url(), '/');
        $endpoint = $config['endpoints'][$endpoint_key] ?? '';
        
        return $base_url . $endpoint;
    }

    /**
     * Check if SSL verification should be disabled (local environment)
     */
    public static function should_verify_ssl()
    {
        return self::get_environment() !== 'local';
    }

    /**
     * Get API timeout setting
     */
    public static function get_timeout()
    {
        $config = self::load_config();
        return $config['api_config']['timeout'] ?? 30;
    }
}
