<?php
defined('ABSPATH') || exit;
?>
<div class="wrap">
    <h1><?php esc_html_e('CommerIQ Reports', 'woocommerce-commeriq'); ?></h1>

    <?php if (empty($rows)): ?>
        <p><?php esc_html_e('No comparison results found.', 'woocommerce-commeriq'); ?></p>
    <?php else: ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('commeriq_reports_export'); ?>
            <input type="hidden" name="action" value="commeriq_export_csv" />
            <table class="widefat fixed">
                <thead>
                    <tr>
                        <th style="width:40px;"></th>
                        <th><?php esc_html_e('ID', 'woocommerce-commeriq'); ?></th>
                        <th><?php esc_html_e('Product ID', 'woocommerce-commeriq'); ?></th>
                        <th><?php esc_html_e('Created At', 'woocommerce-commeriq'); ?></th>
                        <th><?php esc_html_e('Confidence', 'woocommerce-commeriq'); ?></th>
                        <th><?php esc_html_e('Summary', 'woocommerce-commeriq'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $commeriq_r): ?>
                        <tr>
                            <td><input type="checkbox" name="comparison_ids[]" value="<?php echo esc_attr($commeriq_r['id']); ?>" /></td>
                            <td><?php echo esc_html($commeriq_r['id']); ?></td>
                            <td><?php echo esc_html($commeriq_r['product_id']); ?></td>
                            <td><?php echo esc_html($commeriq_r['created_at']); ?></td>
                            <td><?php echo esc_html($commeriq_r['confidence_score']); ?></td>
                            <td>
                                <?php
                                $commeriq_p = isset($commeriq_r['payload_decoded']) ? $commeriq_r['payload_decoded'] : null;
                                if (is_array($commeriq_p) && isset($commeriq_p['suggested_price'])) {
                                    echo esc_html(json_encode($commeriq_p['suggested_price']));
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
                <button class="button button-primary" type="submit"><?php esc_html_e('Export Selected as CSV', 'woocommerce-commeriq'); ?></button>
            </p>
        </form>
    <?php endif; ?>
</div>
