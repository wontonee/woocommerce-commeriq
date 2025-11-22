<?php
namespace CommerIQ\Admin;

defined('ABSPATH') || exit;

class StoreConfiguration
{
    const OPTION_KEY = 'commeriq_store_config';

    public static function register()
    {
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('wp_ajax_commeriq_refresh_store_config', [__CLASS__, 'ajax_refresh_store_config']);
    }

    public static function register_settings()
    {
        register_setting('commeriq_store_config_group', self::OPTION_KEY, ['type' => 'array', 'sanitize_callback' => [__CLASS__, 'sanitize']]);

        add_settings_section('commeriq_store_section', __('Store Configuration', 'commeriq-ai-powered-commerce-insights-for-woocommerce'), [__CLASS__, 'section_cb'], 'commeriq-settings');

        add_settings_field('store_preview', __('Store Details', 'commeriq-ai-powered-commerce-insights-for-woocommerce'), [__CLASS__, 'field_store_preview'], 'commeriq-settings', 'commeriq_store_section');
        add_settings_field('industry', __('Industry', 'commeriq-ai-powered-commerce-insights-for-woocommerce'), [__CLASS__, 'field_industry'], 'commeriq-settings', 'commeriq_store_section');
        add_settings_field('business_type', __('Business Type', 'commeriq-ai-powered-commerce-insights-for-woocommerce'), [__CLASS__, 'field_business_type'], 'commeriq-settings', 'commeriq_store_section');
    }

    public static function sanitize($input)
    {
        $out = [];
        $out['country'] = isset($input['country']) ? sanitize_text_field($input['country']) : '';
        $out['currency'] = isset($input['currency']) ? sanitize_text_field($input['currency']) : '';
        $out['state'] = isset($input['state']) ? sanitize_text_field($input['state']) : '';
        $out['industry'] = isset($input['industry']) ? sanitize_text_field($input['industry']) : '';
        $out['business_type'] = isset($input['business_type']) ? sanitize_text_field($input['business_type']) : '';
        return $out;
    }

    public static function section_cb()
    {
        echo '<p>' . esc_html__('CommerIQ reads base WooCommerce store details and stores them here for AI context.', 'commeriq-ai-powered-commerce-insights-for-woocommerce') . '</p>';
    }

    public static function field_store_preview()
    {
        // Always show derived WooCommerce values (read-only)
        $derived = self::derive_from_woocommerce();
        $country = isset($derived['country']) ? esc_html($derived['country']) : '';
        $currency = isset($derived['currency']) ? esc_html($derived['currency']) : '';
        $state = isset($derived['state']) ? esc_html($derived['state']) : '';

        echo '<div id="commeriq-store-preview">';
        echo '<p><strong>' . esc_html__('Country', 'commeriq-ai-powered-commerce-insights-for-woocommerce') . ':</strong> ' . esc_html($country) . '</p>';
        echo '<p><strong>' . esc_html__('Currency', 'commeriq-ai-powered-commerce-insights-for-woocommerce') . ':</strong> ' . esc_html($currency) . '</p>';
        echo '<p><strong>' . esc_html__('State/Region', 'commeriq-ai-powered-commerce-insights-for-woocommerce') . ':</strong> ' . esc_html($state) . '</p>';
        echo '</div>';
    }

    public static function field_industry()
    {
        $cfg = get_option(self::OPTION_KEY, []);
        $val = isset($cfg['industry']) ? esc_attr($cfg['industry']) : '';
        $options = ['Retail','Wholesale','D2C','Manufacturer','Other'];
        echo '<select name="' . esc_attr(self::OPTION_KEY) . '[industry]">';
        foreach ($options as $opt) {
            $sel = selected($val, $opt, false);
            echo '<option value="' . esc_attr($opt) . '" ' . esc_attr($sel) . '>' . esc_html($opt) . '</option>';
        }
        echo '</select>';
    }

    public static function field_business_type()
    {
        $cfg = get_option(self::OPTION_KEY, []);
        $val = isset($cfg['business_type']) ? esc_attr($cfg['business_type']) : '';
        $options = ['Retail','Wholesale','D2C','B2B','Other'];
        echo '<select name="' . esc_attr(self::OPTION_KEY) . '[business_type]">';
        foreach ($options as $opt) {
            $sel = selected($val, $opt, false);
            echo '<option value="' . esc_attr($opt) . '" ' . esc_attr($sel) . '>' . esc_html($opt) . '</option>';
        }
        echo '</select>';
    }

    public static function derive_from_woocommerce()
    {
        $out = ['country' => '', 'currency' => '', 'state' => ''];
        if (function_exists('get_option')) {
            $default_country = get_option('woocommerce_default_country', '');
            if (!empty($default_country)) {
                // format may be 'US:CA' or just 'US'
                $parts = explode(':', $default_country);
                $out['country'] = $parts[0];
                if (isset($parts[1])) {
                    $out['state'] = $parts[1];
                }
            }
        }
        if (function_exists('get_woocommerce_currency')) {
            $out['currency'] = get_woocommerce_currency();
        } else {
            $out['currency'] = get_option('woocommerce_currency', '');
        }
        return $out;
    }

    public static function ajax_refresh_store_config()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }
        $derived = self::derive_from_woocommerce();
        if (empty($derived)) {
            wp_send_json_error(['message' => 'Unable to read WooCommerce settings']);
        }
        // merge into existing option but do not overwrite industry/business_type
        $existing = get_option(self::OPTION_KEY, []);
        $merged = array_merge($derived, $existing);
        update_option(self::OPTION_KEY, $merged);
        wp_send_json_success($merged);
    }
}
