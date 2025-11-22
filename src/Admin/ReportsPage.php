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
            wp_die(esc_html__('Unauthorized', 'commeriq-ai-powered-commerce-insights-for-woocommerce'));
        }
        $rows = CommerIQ_Result::get_recent(50);
        require_once COMMERIQ_PLUGIN_DIR . 'src/Views/admin-reports.php';
    }

    public static function handle_export_csv()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(esc_html__('Unauthorized', 'commeriq-ai-powered-commerce-insights-for-woocommerce'));
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
        // Build query with table name first, then prepare with placeholders
        $query = "SELECT * FROM {$table} WHERE id IN ({$placeholders}) ORDER BY created_at DESC";
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Table name is prefixed, dynamic placeholders validated, direct query necessary for CSV export
        $rows = $wpdb->get_results($wpdb->prepare($query, $ids), ARRAY_A);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="commeriq_report.csv"');
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Using php://output for direct CSV streaming
        $out = fopen('php://output', 'w');
        fputcsv($out, ['id', 'product_id', 'created_at', 'confidence_score', 'payload']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['id'], $r['product_id'], $r['created_at'], $r['confidence_score'], $r['payload']]);
        }
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing php://output stream
        fclose($out);
        exit;
    }
}
