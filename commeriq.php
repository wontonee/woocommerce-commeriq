<?php
/**
 * Plugin Name: CommerIQ — AI Powered Commerce Insights for WooCommerce
 * Plugin URI: https://example.com/commeriq
 * Description: AI-assisted price comparison and margin analytics for WooCommerce (v1.0).
 * Version: 1.0.0
 * Author: CommerIQ
 * Text Domain: commeriq
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

if (!defined('COMMERIQ_PLUGIN_FILE')) {
    define('COMMERIQ_PLUGIN_FILE', __FILE__);
}
if (!defined('COMMERIQ_PLUGIN_DIR')) {
    define('COMMERIQ_PLUGIN_DIR', plugin_dir_path(COMMERIQ_PLUGIN_FILE));
}
if (!defined('COMMERIQ_PLUGIN_URL')) {
    define('COMMERIQ_PLUGIN_URL', plugin_dir_url(COMMERIQ_PLUGIN_FILE));
}
if (!defined('COMMERIQ_VERSION')) {
    define('COMMERIQ_VERSION', '1.0.0');
}

// API Configuration Constants
if (!defined('COMMERIQ_API_ENV')) {
    define('COMMERIQ_API_ENV', 'local'); // 'local' or 'production'
}
if (!defined('COMMERIQ_API_LOCAL')) {
    define('COMMERIQ_API_LOCAL', 'https://licenseapp.test/api/commeriq');
}
if (!defined('COMMERIQ_API_PRODUCTION')) {
    define('COMMERIQ_API_PRODUCTION', 'https://myapps.wontonee.com/api/commeriq');
}

// Load composer autoload if present
if (file_exists(COMMERIQ_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once COMMERIQ_PLUGIN_DIR . 'vendor/autoload.php';
}

// Load plugin loader
require_once COMMERIQ_PLUGIN_DIR . 'src/commeriq-loader.php';

// Ensure migrations class is available for activation/deactivation
if (file_exists(COMMERIQ_PLUGIN_DIR . 'src/DB/Migrations.php')) {
    require_once COMMERIQ_PLUGIN_DIR . 'src/DB/Migrations.php';
}

register_activation_hook(__FILE__, ['CommerIQ\DB\Migrations', 'activate']);
register_deactivation_hook(__FILE__, ['CommerIQ\DB\Migrations', 'deactivate']);

add_action('plugins_loaded', function () {
    \CommerIQ\Loader::init();
});
