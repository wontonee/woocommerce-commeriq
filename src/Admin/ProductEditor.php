<?php
namespace CommerIQ\Admin;

defined('ABSPATH') || exit;

class ProductEditor
{
    public static function register()
    {
        add_action('media_buttons', [__CLASS__, 'render_ai_media_button'], 15);
        // Add price comparison button after price fields in General tab
        add_action('woocommerce_product_options_pricing', [__CLASS__, 'render_price_comparison_button']);
        // Add AI image generation button to product image metabox
        add_action('admin_footer-post.php', [__CLASS__, 'render_ai_image_button']);
        add_action('admin_footer-post-new.php', [__CLASS__, 'render_ai_image_button']);
    }

    public static function render_ai_media_button($editor_id = '')
    {
        // Only show on product edit screens
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'product') {
            return;
        }

        // Determine action type based on editor ID
        // 'content' = long description (main product description)
        // 'excerpt' = short description (product short description)
        $action_type = ($editor_id === 'excerpt') ? 'short' : 'long';

        // Button HTML: placed after Add Media
        // Use a small inline robot SVG to visually represent AI content generation
            $robot_svg = '<svg class="commeriq-ai-svg" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">'
                . '<rect x="3" y="7" width="18" height="10" rx="2" ry="2" fill="#e6eef5" stroke="#2271b1" stroke-width="1.6"/>'
                . '<circle cx="9" cy="12" r="1.4" fill="#2271b1" stroke="#ffffff" stroke-width="0.6"/>'
                . '<circle cx="15" cy="12" r="1.4" fill="#2271b1" stroke="#ffffff" stroke-width="0.6"/>'
                . '<rect x="8" y="15" width="8" height="1.2" rx="0.6" fill="#2271b1" stroke="#2271b1" stroke-width="0.8"/>'
                . '<rect x="10" y="3" width="4" height="4" rx="1" fill="#2271b1" stroke="#2271b1" stroke-width="0.8"/>'
                . '</svg>';
        echo '<button type="button" class="button commeriq-icon-button commeriq-ai-content" data-action-type="' . esc_attr($action_type) . '" data-editor-id="' . esc_attr($editor_id) . '">' . wp_kses($robot_svg, ['svg' => ['xmlns' => [], 'viewBox' => [], 'width' => [], 'height' => []], 'rect' => ['x' => [], 'y' => [], 'width' => [], 'height' => [], 'rx' => [], 'fill' => [], 'stroke' => [], 'stroke-width' => []], 'circle' => ['cx' => [], 'cy' => [], 'r' => [], 'fill' => [], 'stroke' => [], 'stroke-width' => []]]) . esc_html__('AI Content', 'commeriq-ai-powered-commerce-insights-for-woocommerce') . '</button>';
    }

    public static function render_price_comparison_button()
    {
        global $post;
        if (!$post || $post->post_type !== 'product') {
            return;
        }

        // Check if license is activated
        $license = get_option('commeriq_license', []);
        $is_active = !empty($license['licence_key']) && !empty($license['status']) && $license['status'] === 'active';

        if (!$is_active) {
            return; // Don't show button if license not active
        }

        ?>
        <div class="options_group" style="border-top: 1px solid #eee; padding-top: 12px;">
            <p class="form-field">
                <label style="font-weight: 600; color: #2271b1;"><?php esc_html_e('CommerIQ Insights', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></label>
                <button type="button" id="commeriq-run-comparison" class="button button-primary" data-post-id="<?php echo esc_attr($post->ID); ?>">
                    <span class="dashicons dashicons-chart-line" style="margin-top: 3px;"></span>
                    <?php esc_html_e('Run Comparison', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                </button>
                <span class="description" style="display: block; margin-top: 8px;">
                    <?php esc_html_e('Compare your product price with competitors across multiple platforms.', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                </span>
            </p>
        </div>

        <!-- Price Comparison Modal -->
        <div id="commeriq-comparison-modal" class="commeriq-modal" style="display:none;">
            <div class="commeriq-modal-overlay"></div>
            <div class="commeriq-modal-content">
                <div class="commeriq-modal-header">
                    <h2><?php esc_html_e('Price Comparison Results', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h2>
                    <button type="button" class="commeriq-modal-close">&times;</button>
                </div>
                <div class="commeriq-modal-body">
                    <div id="commeriq-comparison-loading" style="text-align: center; padding: 40px;">
                        <span class="spinner is-active" style="float: none; margin: 0 auto;"></span>
                        <p><?php esc_html_e('Analyzing prices across platforms...', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
                    </div>
                    <div id="commeriq-comparison-results" style="display:none;"></div>
                </div>
            </div>
        </div>

        <!-- AI Content Modal -->
        <div id="commeriq-ai-modal" class="commeriq-modal" style="display:none;">
            <div class="commeriq-modal-overlay"></div>
            <div class="commeriq-modal-content" style="max-width: 500px;">
                <div class="commeriq-modal-header">
                    <h2 id="commeriq-ai-modal-title"><?php esc_html_e('AI Content', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h2>
                    <button type="button" class="commeriq-modal-close">&times;</button>
                </div>
                <div class="commeriq-modal-body">
                    <div id="commeriq-ai-modal-icon" style="text-align: center; font-size: 48px; margin-bottom: 16px;">🤖</div>
                    <p id="commeriq-ai-modal-message" style="text-align: center; margin: 0;"></p>
                    <div id="commeriq-ai-modal-actions" style="text-align: center; margin-top: 20px;">
                        <button type="button" class="button button-primary" id="commeriq-ai-modal-confirm" style="display:none;">Generate</button>
                        <button type="button" class="button" id="commeriq-ai-modal-cancel" style="display:none;">Cancel</button>
                        <button type="button" class="button button-primary commeriq-modal-close" id="commeriq-ai-modal-ok" style="display:none;">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public static function render_ai_image_button()
    {
        global $post;
        $screen = get_current_screen();
        
        // Only show on product edit screens
        if (!$screen || $screen->post_type !== 'product') {
            return;
        }

        // Check if license is activated
        $license = get_option('commeriq_license', []);
        $is_active = !empty($license['licence_key']) && !empty($license['status']) && $license['status'] === 'active';

        if (!$is_active) {
            return; // Don't show button if license not active
        }

        ?>
        <!-- AI Image Generation Modal -->
        <div id="commeriq-ai-image-modal" class="commeriq-modal" style="display:none;">
            <div class="commeriq-modal-overlay"></div>
            <div class="commeriq-modal-content" style="max-width: 600px;">
                <div class="commeriq-modal-header">
                    <h2><?php esc_html_e('Generate AI Product Image', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h2>
                    <button type="button" class="commeriq-modal-close">&times;</button>
                </div>
                <div class="commeriq-modal-body">
                    <div id="commeriq-ai-image-form" style="display:block;">
                        <p style="margin-bottom: 15px; color: #666;">
                            <?php esc_html_e('Generate a product image using AI based on your product title and optional customization.', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                        </p>
                        
                        <div class="form-field" style="margin-bottom: 15px;">
                            <label for="commeriq-image-description" style="display:block; margin-bottom:5px; font-weight:600;">
                                <?php esc_html_e('Image Description (Optional)', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                            </label>
                            <textarea id="commeriq-image-description" rows="3" style="width:100%; padding:8px;" placeholder="e.g., Professional studio lighting, white background, product centered"></textarea>
                        </div>

                        <div class="form-field" style="margin-bottom: 15px;">
                            <label for="commeriq-image-style" style="display:block; margin-bottom:5px; font-weight:600;">
                                <?php esc_html_e('Image Style (Optional)', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                            </label>
                            <select id="commeriq-image-style" style="width:100%; padding:8px;">
                                <option value=""><?php esc_html_e('Default Style', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></option>
                                <option value="realistic"><?php esc_html_e('Realistic', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></option>
                                <option value="studio"><?php esc_html_e('Studio Photography', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></option>
                                <option value="minimalist"><?php esc_html_e('Minimalist', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></option>
                                <option value="lifestyle"><?php esc_html_e('Lifestyle', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></option>
                                <option value="artistic"><?php esc_html_e('Artistic', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></option>
                            </select>
                        </div>

                        <div class="form-field" style="margin-bottom: 15px;">
                            <label for="commeriq-image-size" style="display:block; margin-bottom:5px; font-weight:600;">
                                <?php esc_html_e('Image Size', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                            </label>
                            <select id="commeriq-image-size" style="width:100%; padding:8px;">
                                <option value="1024x1024"><?php esc_html_e('Square (1024x1024)', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></option>
                                <option value="1024x1792"><?php esc_html_e('Portrait (1024x1792)', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></option>
                                <option value="1792x1024"><?php esc_html_e('Landscape (1792x1024)', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></option>
                            </select>
                        </div>

                        <div style="text-align: center; margin-top: 20px;">
                            <button type="button" class="button button-primary" id="commeriq-start-image-generation">
                                <span class="dashicons dashicons-format-image" style="vertical-align:middle;"></span>
                                <?php esc_html_e('Generate Image', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                            </button>
                            <button type="button" class="button commeriq-icon-button commeriq-modal-close" style="margin-left:10px;">
                                <?php esc_html_e('Cancel', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                            </button>
                        </div>
                    </div>

                    <div id="commeriq-ai-image-loading" style="display:none; text-align:center; padding:40px;">
                        <span class="spinner is-active" style="float:none; margin:0 auto;"></span>
                        <p style="margin-top:20px; color:#666;"><?php esc_html_e('Generating your product image...', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
                        <p style="font-size:12px; color:#999;"><?php esc_html_e('This may take up to 60 seconds', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
                    </div>

                    <div id="commeriq-ai-image-result" style="display:none;">
                        <div style="text-align:center; margin-bottom:20px;">
                            <div id="commeriq-generated-image-container"></div>
                        </div>
                        
                        <div style="text-align:center;">
                            <button type="button" class="button button-primary" id="commeriq-set-featured-image">
                                <span class="dashicons dashicons-yes" style="vertical-align:middle;"></span>
                                <?php esc_html_e('Set as Product Image', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                            </button>
                            <button type="button" class="button" id="commeriq-save-to-library">
                                <span class="dashicons dashicons-download" style="vertical-align:middle;"></span>
                                <?php esc_html_e('Save to Media Library', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                            </button>
                            <button type="button" class="button" id="commeriq-regenerate-image" style="margin-left:10px;">
                                <span class="dashicons dashicons-update" style="vertical-align:middle;"></span>
                                <?php esc_html_e('Regenerate', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                            </button>
                        </div>
                    </div>

                    <div id="commeriq-ai-image-error" style="display:none; text-align:center; padding:20px;">
                        <div style="color:#d63638; font-size:48px; margin-bottom:15px;">⚠️</div>
                        <p id="commeriq-ai-image-error-message" style="color:#d63638; font-weight:600;"></p>
                        <button type="button" class="button button-primary commeriq-modal-close" style="margin-top:15px;">
                            <?php esc_html_e('Close', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

