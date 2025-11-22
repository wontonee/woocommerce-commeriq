<?php
defined('ABSPATH') || exit;
global $post;
?>
<div class="commeriq-product-panel">
    <p><?php esc_html_e('Price comparison and margin insights will appear here after running a comparison.', 'woocommerce-commeriq'); ?></p>
    <p>
        <button class="button button-primary" id="commeriq-run-comparison" data-post-id="<?php echo esc_attr($post->ID); ?>"><?php esc_html_e('Run Comparison', 'woocommerce-commeriq'); ?></button>
    </p>
</div>
