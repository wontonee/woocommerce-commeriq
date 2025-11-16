<?php
namespace CommerIQ\DB;

defined('ABSPATH') || exit;

class Migrations
{
    public static function activate()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table_name = $wpdb->prefix . 'commeriq_price_comparisons';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            product_id BIGINT UNSIGNED NOT NULL,
            payload LONGTEXT NOT NULL,
            confidence_score FLOAT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        dbDelta($sql);

        // schedule hourly cron if not scheduled
        if (!wp_next_scheduled('commeriq_hourly_sync')) {
            wp_schedule_event(time(), 'hourly', 'commeriq_hourly_sync');
        }
    }

    public static function deactivate()
    {
        // clear scheduled event
        $timestamp = wp_next_scheduled('commeriq_hourly_sync');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'commeriq_hourly_sync');
        }
    }
}
