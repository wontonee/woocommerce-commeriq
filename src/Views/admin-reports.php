<?php
defined('ABSPATH') || exit;
?>
<div class="wrap">
    <h1><?php esc_html_e('CommerIQ Reports', 'commeriq'); ?></h1>

    <?php if (empty($rows)): ?>
        <p><?php esc_html_e('No comparison results found.', 'commeriq'); ?></p>
    <?php else: ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('commeriq_reports_export'); ?>
            <input type="hidden" name="action" value="commeriq_export_csv" />
            <table class="widefat fixed">
                <thead>
                    <tr>
                        <th style="width:40px;"></th>
                        <th><?php esc_html_e('ID', 'commeriq'); ?></th>
                        <th><?php esc_html_e('Product ID', 'commeriq'); ?></th>
                        <th><?php esc_html_e('Created At', 'commeriq'); ?></th>
                        <th><?php esc_html_e('Confidence', 'commeriq'); ?></th>
                        <th><?php esc_html_e('Summary', 'commeriq'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><input type="checkbox" name="comparison_ids[]" value="<?php echo esc_attr($r['id']); ?>" /></td>
                            <td><?php echo esc_html($r['id']); ?></td>
                            <td><?php echo esc_html($r['product_id']); ?></td>
                            <td><?php echo esc_html($r['created_at']); ?></td>
                            <td><?php echo esc_html($r['confidence_score']); ?></td>
                            <td>
                                <?php
                                $p = isset($r['payload_decoded']) ? $r['payload_decoded'] : null;
                                if (is_array($p) && isset($p['suggested_price'])) {
                                    echo esc_html(json_encode($p['suggested_price']));
                                } else {
                                    echo esc_html('—');
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p>
                <button class="button button-primary" type="submit"><?php esc_html_e('Export Selected as CSV', 'commeriq'); ?></button>
            </p>
        </form>
    <?php endif; ?>
</div>
