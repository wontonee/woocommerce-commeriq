<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Remove options
$option_key = 'commeriq_settings';
delete_option($option_key);
delete_site_option($option_key);

// Drop custom table
$table_name = $wpdb->prefix . 'commeriq_price_comparisons';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Necessary for plugin uninstall, table name is prefixed
$wpdb->query("DROP TABLE IF EXISTS {$table_name}");
