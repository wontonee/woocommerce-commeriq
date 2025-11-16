<?php
/**
 * CommerIQ API Routes Configuration
 * 
 * This file defines all API endpoints for CommerIQ plugin
 * Similar to BlogIBot routing system
 */

defined('ABSPATH') || exit;

return [
    // Environment: 'local' or 'production'
    // Force 'local' for development, or use WP_DEBUG to auto-detect
    'environment' => 'local', // Change to 'production' when deploying
    
    // API Base URLs
    'api_urls' => [
        'local' => 'https://licenseapp.test/api/commeriq',
        'production' => 'https://myapps.wontonee.com/api/commeriq',
    ],
    
    // API Endpoints
    'endpoints' => [
        // License Management
        'register_license' => '/register-license',
        'remove_license' => '/remove-license-registration',
        'validate_license' => '/validate-license',
        
        // Product Features
        'compare_price' => '/compare-price',
        'generate_ai' => '/generate-ai',
        'generate_product_image' => '/generate-product-image',
    ],
    
    // API Configuration
    'api_config' => [
        'timeout' => 30,
        'retry_attempts' => 3,
        'verify_ssl' => true,
    ],
];
