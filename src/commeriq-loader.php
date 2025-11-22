<?php
namespace CommerIQ;

use CommerIQ\Helpers\LicenseManager;

defined('ABSPATH') || exit;

class Loader
{
    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'register_admin_pages']);
        add_action('add_meta_boxes', [__CLASS__, 'register_meta_boxes']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'admin_assets']);
        add_action('wp_ajax_commeriq_run_comparison', [__CLASS__, 'ajax_run_comparison']);
        add_action('wp_ajax_commeriq_generate_ai_content', [__CLASS__, 'ajax_generate_ai_content']);
        add_action('wp_ajax_commeriq_generate_ai_image', [__CLASS__, 'ajax_generate_ai_image']);
        add_action('wp_ajax_commeriq_set_featured_image', [__CLASS__, 'ajax_set_featured_image']);
        add_action('wp_ajax_commeriq_retrieve_store', [__CLASS__, 'ajax_retrieve_store']);
        add_action('wp_ajax_commeriq_save_store', [__CLASS__, 'ajax_save_store']);
        add_action('wp_ajax_commeriq_save_license', [__CLASS__, 'ajax_save_license']);
        add_action('wp_ajax_commeriq_activate_license', [__CLASS__, 'ajax_activate_license']);
        add_action('wp_ajax_commeriq_remove_license', [__CLASS__, 'ajax_remove_license']);

        // OLD SettingsPage DISABLED - Using new license system in admin-settings-new.php
        // The old SettingsPage::ajax_save_license() conflicts with the new ajax_activate_license()
        /*
        $settings_file = COMMERIQ_PLUGIN_DIR . 'src/Admin/SettingsPage.php';
        if (file_exists($settings_file)) {
            require_once $settings_file;
            if (class_exists('CommerIQ\\Admin\\SettingsPage')) {
                \CommerIQ\Admin\SettingsPage::register();
            }
        }
        */

        // Register product editor helpers (buttons, panels)
        $product_editor_file = COMMERIQ_PLUGIN_DIR . 'src/Admin/ProductEditor.php';
        if (file_exists($product_editor_file)) {
            require_once $product_editor_file;
            if (class_exists('CommerIQ\\Admin\\ProductEditor')) {
                \CommerIQ\Admin\ProductEditor::register();
            }
        }

        // Do not auto-create or activate a license here. License activation is explicit
        // and will be persisted via the AJAX activate/remove endpoints.

        // StoreConfiguration removed; store values are derived from WooCommerce via Utils

        // Register REST endpoints if available
        $rest_file = COMMERIQ_PLUGIN_DIR . 'src/REST/RestEndpoints.php';
        if (file_exists($rest_file)) {
            require_once $rest_file;
            if (class_exists('CommerIQ\\REST\\RestEndpoints')) {
                \CommerIQ\REST\RestEndpoints::register();
            }
        }

        // Register hourly sync handler if job exists
        $job_file = COMMERIQ_PLUGIN_DIR . 'src/Jobs/CommerIQ_SyncJob.php';
        if (file_exists($job_file)) {
            require_once $job_file;
            if (class_exists('CommerIQ\\Jobs\\CommerIQ_SyncJob')) {
                add_action('commeriq_hourly_sync', ['\\CommerIQ\\Jobs\\CommerIQ_SyncJob', 'run']);
            }
        }

        // ReportsPage registration intentionally removed (no reports submenu)
    }

    public static function admin_assets($hook)
    {
        // Ensure WP dashicons are available for icon buttons
        wp_enqueue_style('dashicons');
        
        // Always load common styles (modals, buttons, icons)
        wp_enqueue_style('commeriq-admin-common', COMMERIQ_PLUGIN_URL . 'assets/css/commeriq-admin-common.css', ['dashicons'], COMMERIQ_VERSION . '.5');
        
        // Load context-specific styles
        if (strpos($hook, 'commeriq-settings') !== false) {
            // Settings page: modern BlogIBot-inspired design
            wp_enqueue_style('commeriq-admin-settings', COMMERIQ_PLUGIN_URL . 'assets/css/commeriq-admin-settings.css', ['commeriq-admin-common'], COMMERIQ_VERSION . '.' . time());
        } else {
            // Product editor and other admin pages
            wp_enqueue_style('commeriq-admin-product', COMMERIQ_PLUGIN_URL . 'assets/css/commeriq-admin-product.css', ['commeriq-admin-common'], COMMERIQ_VERSION . '.5');
        }
        
        wp_enqueue_script('commeriq-admin', COMMERIQ_PLUGIN_URL . 'assets/js/commeriq-admin.js', ['jquery'], COMMERIQ_VERSION . '.7', true);
        wp_localize_script('commeriq-admin', 'commeriqAdmin', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('commeriq_product_nonce'),
            'retrieve_nonce' => wp_create_nonce('commeriq_retrieve_nonce'),
            'license_nonce' => wp_create_nonce('commeriq_license_nonce'),
        ]);
    }

    public static function register_admin_pages()
    {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }
        // Register as a submenu under WooCommerce
        if (function_exists('add_submenu_page')) {
            add_submenu_page('woocommerce', 'CommerIQ', 'CommerIQ', 'manage_woocommerce', 'commeriq-settings', [__CLASS__, 'render_settings_page']);
        } else {
            // fallback to top-level if WooCommerce menu not present
            add_menu_page('CommerIQ', 'CommerIQ', 'manage_woocommerce', 'commeriq-settings', [__CLASS__, 'render_settings_page'], 'dashicons-chart-line');
        }
    }

    public static function render_settings_page()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(esc_html__('Unauthorized', 'woocommerce-commeriq'));
        }
        
        // Ensure LicenseManager is loaded before view
        if (!class_exists('CommerIQ\\Helpers\\LicenseManager')) {
            require_once COMMERIQ_PLUGIN_DIR . 'src/Helpers/LicenseManager.php';
        }
        
        require_once COMMERIQ_PLUGIN_DIR . 'src/Views/admin-settings.php';
    }

    public static function register_meta_boxes()
    {
        // Meta box removed - price comparison moved to General tab
    }

    public static function render_product_panel($post)
    {
        // No longer used
    }

    public static function ajax_run_comparison()
    {
        check_ajax_referer('commeriq_product_nonce', '_nonce');
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        // Get license data
        $license = get_option('commeriq_license', []);
        if (empty($license['licence_key']) || empty($license['domain_name'])) {
            wp_send_json_error(['message' => 'License not activated'], 400);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        if (!$post_id) {
            wp_send_json_error(['message' => 'Invalid product ID'], 400);
        }

        $product = wc_get_product($post_id);
        if (!$product) {
            wp_send_json_error(['message' => 'Product not found'], 404);
        }

        // Get product data
        $title = $product->get_name();
        $price = $product->get_regular_price();
        $description = $product->get_short_description();

        if (empty($title)) {
            wp_send_json_error(['message' => 'Product title is required for price comparison'], 400);
        }

        // Get store location
        $country = WC()->countries->get_base_country();
        $state = WC()->countries->get_base_state();

        // Prepare API request
        $api_url = \CommerIQ\ApiConfig::get_endpoint_url('compare_price');

        $payload = [
            'license_key' => $license['licence_key'],
            'domain' => $license['domain_name'],
            'title' => $title,
            'price' => $price ? strval($price) : '',
            'country' => $country,
            'state' => $state,
            'product_description' => $description,
        ];

        $args = [
            'body' => wp_json_encode($payload),
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => \CommerIQ\ApiConfig::get_timeout(),
            'sslverify' => \CommerIQ\ApiConfig::should_verify_ssl(),
        ];

        $response = wp_remote_post($api_url, $args);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'API request failed: ' . $response->get_error_message()], 500);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data)) {
            wp_send_json_error(['message' => 'Invalid API response'], 500);
        }

        wp_send_json_success($data);
    }

    public static function ajax_generate_ai_content()
    {
        // Clean output buffer to prevent any stray output before JSON
        if (ob_get_level()) {
            ob_clean();
        }
        
        check_ajax_referer('commeriq_product_nonce', '_nonce');
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        // Get license data
        $license = get_option('commeriq_license', []);
        if (empty($license['licence_key']) || empty($license['domain_name'])) {
            wp_send_json_error(['message' => 'License not activated'], 400);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        if (!$post_id) {
            wp_send_json_error(['message' => 'Invalid product ID'], 400);
        }

        $product = wc_get_product($post_id);
        if (!$product) {
            wp_send_json_error(['message' => 'Product not found'], 404);
        }

        // Get action type: 'long' for product description, 'short' for product short description
        $action_type = isset($_POST['action_type']) ? sanitize_text_field(wp_unslash($_POST['action_type'])) : 'long';

        // Get product data
        $title = $product->get_name();
        $price = $product->get_regular_price();
        $current_description = ($action_type === 'short') ? $product->get_short_description() : $product->get_description();

        if (empty($title)) {
            wp_send_json_error(['message' => 'Product title is required for AI content generation'], 400);
        }

        // Prepare API request
        $api_url = \CommerIQ\ApiConfig::get_endpoint_url('generate_ai');

        // Truncate current description to prevent payload size issues
        // Strip HTML tags and limit to 150 characters for context
        $description_context = '';
        if (!empty($current_description)) {
            $description_context = wp_strip_all_tags($current_description);
            $description_context = mb_substr($description_context, 0, 150);
        }

        $payload = [
            'license_key' => $license['licence_key'],
            'domain' => $license['domain_name'],
            'title' => $title,
            'price' => $price ? strval($price) : '',
            'product_description' => $description_context, // Send truncated version
            'action' => $action_type, // Send 'long' or 'short' to API
        ];

// error_log('CommerIQ AI Content - Endpoint: ' . $api_url);
// error_log('CommerIQ AI Content - Payload: ' . wp_json_encode($payload));

        $args = [
            'body' => wp_json_encode($payload),
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => \CommerIQ\ApiConfig::get_timeout(),
            'sslverify' => \CommerIQ\ApiConfig::should_verify_ssl(),
        ];

        $response = wp_remote_post($api_url, $args);

        if (is_wp_error($response)) {
// error_log('CommerIQ AI Content - WP Error: ' . $response->get_error_message());
            wp_send_json_error(['message' => 'API request failed: ' . $response->get_error_message()], 500);
        }

        $body = wp_remote_retrieve_body($response);
        $status = wp_remote_retrieve_response_code($response);
        
// error_log('CommerIQ AI Content - Response Status: ' . $status);
// error_log('CommerIQ AI Content - Response Body: ' . substr($body, 0, 500));
        
        $data = json_decode($body, true);

        if (empty($data) || !isset($data['description'])) {
// error_log('CommerIQ AI Content - Invalid response data: ' . // print_r($data, true));
            wp_send_json_error(['message' => 'Invalid API response'], 500);
        }

// error_log('CommerIQ AI Content - Sending success response');
        
        // Clean output buffer one more time before sending JSON
        if (ob_get_level()) {
            ob_clean();
        }
        
        wp_send_json_success($data);
    }

    public static function ajax_retrieve_store()
    {
        check_ajax_referer('commeriq_retrieve_nonce', '_nonce');
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        // Prefer WooCommerce-derived values when available
        if (class_exists('CommerIQ\\Helpers\\Utils')) {
            $derived = \CommerIQ\Helpers\Utils::derive_from_woocommerce();
            $out = [
                'country' => isset($derived['country']) ? $derived['country'] : '',
                'currency' => isset($derived['currency']) ? $derived['currency'] : '',
                'state' => isset($derived['state']) ? $derived['state'] : '',
                'source' => 'woocommerce',
            ];
            wp_send_json_success($out);
        }

        // Fallback to stored option (use commeriq_store_config for consistency with settings page)
        $stored = get_option('commeriq_store_config', ['country' => '', 'currency' => '', 'state' => '', 'address_1' => '', 'address_2' => '']);
        $out = [
            'country' => isset($stored['country']) ? $stored['country'] : '',
            'currency' => isset($stored['currency']) ? $stored['currency'] : '',
            'state' => isset($stored['state']) ? $stored['state'] : '',
            'address_1' => isset($stored['address_1']) ? $stored['address_1'] : '',
            'address_2' => isset($stored['address_2']) ? $stored['address_2'] : '',
            'source' => (!empty($stored['country']) || !empty($stored['currency']) || !empty($stored['state'])) ? 'stored' : 'empty',
        ];
        wp_send_json_success($out);
    }

    public static function ajax_save_store()
    {
        check_ajax_referer('commeriq_retrieve_nonce', '_nonce');
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        $vals = isset($_POST['store_values']) && is_array($_POST['store_values']) ? array_map('sanitize_text_field', wp_unslash($_POST['store_values'])) : [];
        $out = [
            'country' => isset($vals['country']) ? sanitize_text_field($vals['country']) : '',
            'currency' => isset($vals['currency']) ? sanitize_text_field($vals['currency']) : '',
            'state' => isset($vals['state']) ? sanitize_text_field($vals['state']) : '',
            'address_1' => isset($vals['address_1']) ? sanitize_text_field($vals['address_1']) : '',
            'address_2' => isset($vals['address_2']) ? sanitize_text_field($vals['address_2']) : '',
        ];

        // Persist into commeriq_store_config so the settings view reads the same data
        update_option('commeriq_store_config', $out);
        wp_send_json_success($out);
    }

    public static function ajax_activate_license()
    {
        // Ensure LicenseManager is loaded
        if (!class_exists('CommerIQ\\Helpers\\LicenseManager')) {
            require_once COMMERIQ_PLUGIN_DIR . 'src/Helpers/LicenseManager.php';
        }
        
        check_ajax_referer('commeriq_license_nonce', 'nonce');
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }
        // Accept either a posted 'license' array or individual fields (compatibility with different serializers)
        $vals = [];
        if (isset($_POST['license']) && is_array($_POST['license'])) {
            $vals = array_map('sanitize_text_field', wp_unslash($_POST['license']));
        } else {
            // Fallback to individual post fields
            $vals = [
                'licence_key' => isset($_POST['licence_key']) ? sanitize_text_field(wp_unslash($_POST['licence_key'])) : '',
                'domain_name' => isset($_POST['domain_name']) ? sanitize_text_field(wp_unslash($_POST['domain_name'])) : '',
            ];
        }

        $licence_key = isset($vals['licence_key']) ? sanitize_text_field($vals['licence_key']) : '';
        $domain_name = isset($vals['domain_name']) ? sanitize_text_field($vals['domain_name']) : '';

        // Use LicenseManager to register license with API
        $result = LicenseManager::register_license($licence_key, $domain_name);

        if (!$result['success']) {
            $data = ['message' => $result['message']];
            if (isset($result['debug_info'])) {
                $data['debug_info'] = $result['debug_info'];
            }
            wp_send_json_error($data, 400);
        }

        // Persist the last active admin tab so a reload will restore the Licence view
        update_option('commeriq_last_active_tab', 'tab-licence');

        // Prepare a formatted date for the client
        $activated_at_formatted = date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime( $result['data']['activated_at'] ) );

        wp_send_json_success([
            'message' => $result['message'],
            'licence_key' => $licence_key,
            'domain_name' => $domain_name,
            'activated_at' => $result['data']['activated_at'],
            'activated_at_formatted' => $activated_at_formatted,
            'last_active_tab' => 'tab-licence',
        ]);
    }

    public static function ajax_remove_license()
    {
        // Ensure LicenseManager is loaded
        if (!class_exists('CommerIQ\\Helpers\\LicenseManager')) {
            require_once COMMERIQ_PLUGIN_DIR . 'src/Helpers/LicenseManager.php';
        }
        
        check_ajax_referer('commeriq_license_nonce', 'nonce');
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        // Get current license data
        $license = LicenseManager::get_license();
        $licence_key = isset($license['licence_key']) ? $license['licence_key'] : '';
        $domain_name = isset($license['domain_name']) ? $license['domain_name'] : '';

        // Use LicenseManager to remove license from API
        $result = LicenseManager::remove_license($licence_key, $domain_name);

        if (!$result['success']) {
            wp_send_json_error(['message' => $result['message']], 400);
        }

        wp_send_json_success(['message' => $result['message']]);
    }

    /**
     * AJAX handler for generating AI product images
     */
    public static function ajax_generate_ai_image()
    {
        check_ajax_referer('commeriq_product_nonce', '_nonce');
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        // Load required WordPress files
        if (!function_exists('download_url')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        if (!function_exists('media_handle_sideload')) {
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        // Get license data
        $license = get_option('commeriq_license', []);
        
        // Debug: Log license data
// error_log('CommerIQ AI Image - License Data: ' . wp_json_encode($license));
        
        if (empty($license['licence_key']) || empty($license['domain_name'])) {
            wp_send_json_error(['message' => 'License not activated'], 400);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        if (!$post_id) {
            wp_send_json_error(['message' => 'Invalid product ID'], 400);
        }

        $product = wc_get_product($post_id);
        if (!$product) {
            wp_send_json_error(['message' => 'Product not found'], 404);
        }

        // Get product title
        $title = $product->get_name();
        if (empty($title)) {
            wp_send_json_error(['message' => 'Product title is required for image generation'], 400);
        }

        // Get optional parameters
        $description = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';
        $style = isset($_POST['style']) ? sanitize_text_field(wp_unslash($_POST['style'])) : '';
        $size = isset($_POST['size']) ? sanitize_text_field(wp_unslash($_POST['size'])) : '1024x1024';

        // Prepare API request
        $endpoint = \CommerIQ\ApiConfig::get_endpoint_url('generate_product_image');

        $payload = [
            'license_key' => $license['licence_key'],
            'domain' => $license['domain_name'],
            'title' => $title,
            'description' => $description,
            'style' => $style,
            'size' => $size,
        ];

        // Debug: Log the payload being sent
// error_log('CommerIQ AI Image - Endpoint: ' . $endpoint);
// error_log('CommerIQ AI Image - Payload: ' . wp_json_encode($payload));

        // Prepare request arguments
        $args = [
            'timeout' => 60, // Increased timeout for AI image generation
            'headers' => ['Content-Type' => 'application/json'],
            'body' => wp_json_encode($payload),
            'sslverify' => \CommerIQ\ApiConfig::should_verify_ssl(),
        ];

        $response = wp_remote_post($endpoint, $args);

        if (is_wp_error($response)) {
// error_log('CommerIQ AI Image - WP Error: ' . $response->get_error_message());
            $error_msg = $response->get_error_message();
            
            // Provide user-friendly error messages
            if (strpos($error_msg, 'timed out') !== false || strpos($error_msg, 'timeout') !== false) {
                wp_send_json_error(['message' => 'The AI image generation is taking longer than expected. Please try again or contact support if the issue persists.'], 500);
            } else {
                wp_send_json_error(['message' => 'API connection failed. Please check your internet connection or contact support.'], 500);
            }
        }

        $status = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        // Debug: Log the response
// error_log('CommerIQ AI Image - Response Status: ' . $status);
// error_log('CommerIQ AI Image - Response Body: ' . substr($body, 0, 500) . '...');
        
        $data = json_decode($body, true);

        if ($status !== 200) {
            $error_message = isset($data['message']) ? $data['message'] : 'Failed to generate image';
            wp_send_json_error(['message' => $error_message], $status);
        }

        // Check for base64 image data in nested structure
        if (!isset($data['image']['base64_data'])) {
// error_log('CommerIQ AI Image - Invalid response structure: ' . // print_r($data, true));
            wp_send_json_error(['message' => 'Invalid API response: missing image data'], 500);
        }

        // Extract base64 data and remove data URI prefix if present
        $base64_data = $data['image']['base64_data'];
        if (strpos($base64_data, 'data:image/') === 0) {
            // Remove "data:image/png;base64," or similar prefix
            $base64_data = substr($base64_data, strpos($base64_data, ',') + 1);
        }
        
// error_log('CommerIQ AI Image - Base64 data length: ' . strlen($base64_data) . ' chars');
        
        // Decode base64 to binary
        $image_binary = base64_decode($base64_data);
        if ($image_binary === false) {
// error_log('CommerIQ AI Image - Failed to decode base64 data');
            wp_send_json_error(['message' => 'Failed to decode image data'], 500);
        }
        
// error_log('CommerIQ AI Image - Binary size: ' . strlen($image_binary) . ' bytes');
        
        // Create temporary file and write binary data
        $temp_file = wp_tempnam();
        if (!$temp_file) {
// error_log('CommerIQ AI Image - Failed to create temp file');
            wp_send_json_error(['message' => 'Failed to create temporary file'], 500);
        }
        
        $write_result = file_put_contents($temp_file, $image_binary);
        if ($write_result === false) {
// error_log('CommerIQ AI Image - Failed to write binary data to temp file');
            @wp_delete_file($temp_file);
            wp_send_json_error(['message' => 'Failed to save image data'], 500);
        }
        
// error_log('CommerIQ AI Image - Temp file created: ' . $temp_file . ' (' . $write_result . ' bytes written)');
        
        // Get MIME type and determine extension
        $mime_type = isset($data['image']['mime_type']) ? $data['image']['mime_type'] : 'image/png';
        $extension = ($mime_type === 'image/png') ? '.png' : '.jpg';
        
// error_log('CommerIQ AI Image - MIME type: ' . $mime_type);

        // Prepare file array for media_handle_sideload
        $file_array = [
            'name' => sanitize_file_name($title) . '-' . time() . $extension,
            'tmp_name' => $temp_file,
            'type' => $mime_type,
        ];

        // Upload to media library (files already included at function start)
        $attachment_id = media_handle_sideload($file_array, $post_id, $title);

        // Clean up temp file
        if (file_exists($temp_file)) {
            @wp_delete_file($temp_file);
        }

        if (is_wp_error($attachment_id)) {
            wp_send_json_error(['message' => 'Failed to save image to media library: ' . $attachment_id->get_error_message()], 500);
        }

        // Get attachment URLs
        $attachment_url = wp_get_attachment_url($attachment_id);
        $thumbnail_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');

        wp_send_json_success([
            'image_url' => $attachment_url,
            'thumbnail_url' => $thumbnail_url,
            'attachment_id' => $attachment_id,
            'message' => 'Image generated and saved successfully',
        ]);
    }

    /**
     * AJAX handler for setting featured image
     */
    public static function ajax_set_featured_image()
    {
        check_ajax_referer('commeriq_product_nonce', '_nonce');
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $attachment_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;

        if (!$post_id || !$attachment_id) {
            wp_send_json_error(['message' => 'Invalid parameters'], 400);
        }

        // Set the post thumbnail
        $result = set_post_thumbnail($post_id, $attachment_id);

        if (!$result) {
            wp_send_json_error(['message' => 'Failed to set product image'], 500);
        }

        wp_send_json_success([
            'message' => 'Product image set successfully',
            'attachment_id' => $attachment_id,
        ]);
    }
}
