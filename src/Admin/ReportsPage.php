<?php
namespace CommerIQ\Admin;

defined('ABSPATH') || exit;

use CommerIQ\Models\CommerIQ_Result;

class ReportsPage
{
    public static function register()
    {
        add_action('admin_menu', [__CLASS__, 'add_pages']);
        add_action('admin_post_commeriq_export_csv', [__CLASS__, 'handle_export_csv']);
    }

    public static function add_pages()
    {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }
        add_submenu_page('commeriq-settings', 'Reports', 'Reports', 'manage_woocommerce', 'commeriq-reports', [__CLASS__, 'render_page']);
    }

    public static function render_page()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('Unauthorized', 'commeriq'));
        }
        $rows = CommerIQ_Result::get_recent(50);
        require_once COMMERIQ_PLUGIN_DIR . 'src/Views/admin-reports.php';
    }

    public static function handle_export_csv()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('Unauthorized', 'commeriq'));
        }
        check_admin_referer('commeriq_reports_export');

        $ids = isset($_POST['comparison_ids']) ? array_map('intval', (array) $_POST['comparison_ids']) : [];
        if (empty($ids)) {
            wp_redirect(admin_url('admin.php?page=commeriq-reports&error=no_selection'));
            exit;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'commeriq_price_comparisons';
        $placeholders = implode(',', array_fill(0, count($ids), '%d'));
        $sql = "SELECT * FROM {$table} WHERE id IN ({$placeholders}) ORDER BY created_at DESC";
        $rows = $wpdb->get_results($wpdb->prepare($sql, $ids), ARRAY_A);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="commeriq_report.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['id', 'product_id', 'created_at', 'confidence_score', 'payload']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['id'], $r['product_id'], $r['created_at'], $r['confidence_score'], $r['payload']]);
        }
        fclose($out);
        exit;
    }
}
