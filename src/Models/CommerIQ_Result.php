<?php
namespace CommerIQ\Models;

defined('ABSPATH') || exit;

class CommerIQ_Result
{
    public static function save($product_id, $payload)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'commeriq_price_comparisons';
        $json = maybe_serialize($payload);

        $confidence = null;
        if (is_array($payload) && isset($payload['confidence_score'])) {
            $confidence = floatval($payload['confidence_score']);
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct insert for custom table
        $wpdb->insert($table, [
            'product_id' => $product_id,
            'payload' => $json,
            'confidence_score' => $confidence,
            'created_at' => current_time('mysql', 1),
        ], ['%d', '%s', '%f', '%s']);

        return $wpdb->insert_id;
    }

    public static function get_last_for_product($product_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'commeriq_price_comparisons';
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Table name is prefixed, query is prepared
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE product_id = %d ORDER BY created_at DESC LIMIT 1", $product_id), ARRAY_A);
        if (!$row) {
            return null;
        }
        $payload = maybe_unserialize($row['payload']);
        $row['payload_decoded'] = $payload;
        return $row;
    }

    public static function get_recent($limit = 50)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'commeriq_price_comparisons';
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Table name is prefixed, query is prepared
        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d", intval($limit)), ARRAY_A);
        foreach ($rows as &$r) {
            $r['payload_decoded'] = maybe_unserialize($r['payload']);
        }
        return $rows;
    }
}
