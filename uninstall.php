<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Remove options
$commeriq_option_key = 'commeriq_settings';
delete_option($commeriq_option_key);
delete_site_option($commeriq_option_key);

// Drop custom table
$commeriq_table_name = $wpdb->prefix . 'commeriq_price_comparisons';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Necessary for plugin uninstall
$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %i', $commeriq_table_name));
