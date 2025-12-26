<?php
namespace CommerIQ\Admin;

defined('ABSPATH') || exit;

class SettingsPage
{
    const OPTION_KEY = 'commeriq_license';

    public static function register()
    {
        add_action('admin_init', [__CLASS__, 'register_settings']);
        // AJAX endpoint to save license from the admin form
        add_action('wp_ajax_commeriq_save_license', [__CLASS__, 'ajax_save_license']);
    }

    public static function register_settings()
    {
        register_setting('commeriq_license_group', self::OPTION_KEY, ['type' => 'array', 'sanitize_callback' => [__CLASS__, 'sanitize']]);

        add_settings_section('commeriq_license_section', __('Licence', 'wontonee-commeriq'), [__CLASS__, 'section_cb'], 'commeriq-settings');

        add_settings_field('licence_key', __('Licence Key', 'wontonee-commeriq'), [__CLASS__, 'field_licence_key'], 'commeriq-settings', 'commeriq_license_section');
        add_settings_field('domain_name', __('Domain Name', 'wontonee-commeriq'), [__CLASS__, 'field_domain_name'], 'commeriq-settings', 'commeriq_license_section');
    }

    public static function sanitize($input)
    {
        // Preserve existing activation timestamp when sanitizing settings
        $existing = get_option(self::OPTION_KEY, []);
        $out = [];
        $out['licence_key'] = isset($input['licence_key']) ? sanitize_text_field($input['licence_key']) : '';
        $out['domain_name'] = isset($input['domain_name']) ? sanitize_text_field($input['domain_name']) : '';
        $out['activated_at'] = isset($existing['activated_at']) ? $existing['activated_at'] : '';
        return $out;
    }

    public static function section_cb()
    {
        echo '<p>' . esc_html__('Enter your licence details.', 'wontonee-commeriq') . '</p>';
    }

    public static function field_licence_key()
    {
        $opts = get_option(self::OPTION_KEY, []);
        $val = isset($opts['licence_key']) ? esc_attr($opts['licence_key']) : '';
        echo '<input type="text" name="' . esc_attr(self::OPTION_KEY) . '[licence_key]" value="' . esc_attr($val) . '" class="regular-text" />';
    }

    public static function field_domain_name()
    {
        $opts = get_option(self::OPTION_KEY, []);
        $val = isset($opts['domain_name']) ? esc_attr($opts['domain_name']) : '';
        echo '<input type="text" name="' . esc_attr(self::OPTION_KEY) . '[domain_name]" value="' . esc_attr($val) . '" class="regular-text" />';
    }

    public static function ajax_save_license()
    {
        check_ajax_referer('commeriq_license_nonce', '_nonce');
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        $licence_key = isset($_POST['licence_key']) ? sanitize_text_field(wp_unslash($_POST['licence_key'])) : '';
        $domain_name = isset($_POST['domain_name']) ? sanitize_text_field(wp_unslash($_POST['domain_name'])) : '';

        // Preserve activated_at if already set
        $existing = get_option(self::OPTION_KEY, []);
        $out = [
            'licence_key' => $licence_key,
            'domain_name' => $domain_name,
            'activated_at' => isset($existing['activated_at']) ? $existing['activated_at'] : '',
        ];

        update_option(self::OPTION_KEY, $out);

        wp_send_json_success($out);
    }
}
