<?php
defined('ABSPATH') || exit;

// Ensure LicenseManager is available
if (!class_exists('CommerIQ\\Helpers\\LicenseManager')) {
    require_once COMMERIQ_PLUGIN_DIR . 'src/Helpers/LicenseManager.php';
}

use CommerIQ\Helpers\LicenseManager;

// Get license status
$commeriq_license = LicenseManager::get_license();
$commeriq_is_active = LicenseManager::is_license_active();
$commeriq_licence_key = isset($commeriq_license['licence_key']) ? $commeriq_license['licence_key'] : '';
$commeriq_domain_name = isset($commeriq_license['domain_name']) ? $commeriq_license['domain_name'] : '';
$commeriq_activated_at = isset($commeriq_license['activated_at']) ? $commeriq_license['activated_at'] : '';

// Get current domain for auto-fill
$commeriq_current_domain = LicenseManager::get_current_domain();
?>

<div class="wrap commeriq-admin-wrap">
    <div class="commeriq-header">
        <div class="commeriq-header-content">
            <div class="commeriq-header-logo">
                <span class="commeriq-logo-icon">🤖</span>
                <div>
                    <h1><?php esc_html_e('CommerIQ', 'woocommerce-commeriq'); ?></h1>
                    <p class="description"><?php esc_html_e('AI-Powered Commerce Intelligence for WooCommerce', 'woocommerce-commeriq'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab nav-tab-active" href="#tab-licence"><?php esc_html_e('License', 'woocommerce-commeriq'); ?></a>
        <?php $commeriq_store_tab_class = $commeriq_is_active ? 'nav-tab' : 'nav-tab nav-tab-disabled'; ?>
        <a class="<?php echo esc_attr($commeriq_store_tab_class); ?>" href="#tab-store" data-disabled="<?php echo esc_attr($commeriq_is_active ? '0' : '1'); ?>"><?php esc_html_e('Store Analyzer', 'woocommerce-commeriq'); ?></a>
    </h2>

    <div class="commeriq-tab-content">
        <!-- License Tab -->
        <div id="tab-licence" class="commeriq-tab commeriq-tab-active">
            <div class="commeriq-license-management">
                <?php if ($commeriq_is_active): ?>
                    <!-- Active License Display -->
                    <div class="commeriq-license-card commeriq-license-active">
                        <div class="commeriq-license-card-header">
                            <div class="commeriq-license-status">
                                <span class="commeriq-status-icon commeriq-status-active">
                                    <span class="dashicons dashicons-yes-alt"></span>
                                </span>
                                <div class="commeriq-status-text">
                                    <h3><?php esc_html_e('License Active', 'woocommerce-commeriq'); ?></h3>
                                    <p><?php esc_html_e('Your license is active and all features are enabled', 'woocommerce-commeriq'); ?></p>
                                </div>
                            </div>
                            <div class="commeriq-license-actions">
                                <button type="button" class="button button-secondary" id="commeriq-modify-license-btn">
                                    <span class="dashicons dashicons-edit"></span>
                                    <?php esc_html_e('Modify', 'woocommerce-commeriq'); ?>
                                </button>
                                <button type="button" class="button button-link-delete" id="commeriq-remove-license-btn">
                                    <span class="dashicons dashicons-trash"></span>
                                    <?php esc_html_e('Remove', 'woocommerce-commeriq'); ?>
                                </button>
                            </div>
                        </div>
                        
                        <div class="commeriq-license-details">
                            <div class="commeriq-detail-item">
                                <span class="commeriq-detail-icon">🔑</span>
                                <div class="commeriq-detail-content">
                                    <span class="commeriq-detail-label"><?php esc_html_e('License Key', 'woocommerce-commeriq'); ?></span>
                                    <span class="commeriq-detail-value"><?php echo esc_html($commeriq_licence_key); ?></span>
                                </div>
                            </div>
                            <div class="commeriq-detail-item">
                                <span class="commeriq-detail-icon">🌐</span>
                                <div class="commeriq-detail-content">
                                    <span class="commeriq-detail-label"><?php esc_html_e('Domain', 'woocommerce-commeriq'); ?></span>
                                    <span class="commeriq-detail-value"><?php echo esc_html($commeriq_domain_name); ?></span>
                                </div>
                            </div>
                            <div class="commeriq-detail-item">
                                <span class="commeriq-detail-icon">📅</span>
                                <div class="commeriq-detail-content">
                                    <span class="commeriq-detail-label"><?php esc_html_e('Activated On', 'woocommerce-commeriq'); ?></span>
                                    <span class="commeriq-detail-value"><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($commeriq_activated_at))); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Features Showcase -->
                        <div class="commeriq-features-showcase">
                            <h4><?php esc_html_e('Active Features', 'woocommerce-commeriq'); ?></h4>
                            <div class="commeriq-features-list">
                                <div class="commeriq-feature-item">
                                    <span class="commeriq-feature-icon">📊</span>
                                    <span><?php esc_html_e('Price Comparison', 'woocommerce-commeriq'); ?></span>
                                </div>
                                <div class="commeriq-feature-item">
                                    <span class="commeriq-feature-icon">✍️</span>
                                    <span><?php esc_html_e('AI Descriptions', 'woocommerce-commeriq'); ?></span>
                                </div>
                                <div class="commeriq-feature-item">
                                    <span class="commeriq-feature-icon">🖼️</span>
                                    <span><?php esc_html_e('Image Generation', 'woocommerce-commeriq'); ?></span>
                                </div>
                                <div class="commeriq-feature-item">
                                    <span class="commeriq-feature-icon">💹</span>
                                    <span><?php esc_html_e('Margin Analytics', 'woocommerce-commeriq'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Modify Form -->
                    <div id="commeriq-modify-form-container" class="commeriq-license-card" style="display: none;">
                        <div class="commeriq-license-card-header">
                            <div class="commeriq-license-status">
                                <span class="commeriq-status-icon commeriq-status-warning">
                                    <span class="dashicons dashicons-edit"></span>
                                </span>
                                <div class="commeriq-status-text">
                                    <h3><?php esc_html_e('Modify License', 'woocommerce-commeriq'); ?></h3>
                                    <p><?php esc_html_e('Update your license key or domain', 'woocommerce-commeriq'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <form id="commeriq-modify-form" class="commeriq-license-form">
                            <?php wp_nonce_field('commeriq_license_nonce', 'commeriq_license_nonce'); ?>
                            <div class="commeriq-form-grid">
                                <div class="commeriq-form-field">
                                    <label for="modify_licence_key"><?php esc_html_e('License Key', 'woocommerce-commeriq'); ?></label>
                                    <input type="text" id="modify_licence_key" name="licence_key" value="<?php echo esc_attr($commeriq_licence_key); ?>" placeholder="<?php esc_attr_e('Enter your license key', 'woocommerce-commeriq'); ?>" required>
                                </div>
                                <div class="commeriq-form-field">
                                    <label for="modify_domain_name"><?php esc_html_e('Domain', 'woocommerce-commeriq'); ?></label>
                                    <input type="text" id="modify_domain_name" name="domain_name" value="<?php echo esc_attr($commeriq_domain_name); ?>" placeholder="<?php esc_attr_e('example.com', 'woocommerce-commeriq'); ?>" required>
                                </div>
                            </div>
                            <div class="commeriq-form-actions">
                                <button type="submit" class="button button-primary button-large">
                                    <span class="dashicons dashicons-saved"></span>
                                    <?php esc_html_e('Update License', 'woocommerce-commeriq'); ?>
                                </button>
                                <button type="button" class="button button-secondary button-large" id="commeriq-cancel-modify-btn">
                                    <?php esc_html_e('Cancel', 'woocommerce-commeriq'); ?>
                                </button>
                            </div>
                        </form>
                    </div>

                <?php else: ?>
                    <!-- License Activation Form -->
                    <div class="commeriq-license-card commeriq-license-inactive">
                        <div class="commeriq-license-card-header">
                            <div class="commeriq-license-status">
                                <span class="commeriq-status-icon commeriq-status-inactive">
                                    <span class="dashicons dashicons-lock"></span>
                                </span>
                                <div class="commeriq-status-text">
                                    <h3><?php esc_html_e('Activate Your License', 'woocommerce-commeriq'); ?></h3>
                                    <p><?php esc_html_e('Enter your license key to unlock all AI-powered features', 'woocommerce-commeriq'); ?></p>
                                </div>
                            </div>
                            <a href="https://myapps.wontonee.com" target="_blank" rel="noopener noreferrer" class="button button-primary">
                                <span class="dashicons dashicons-external"></span>
                                <?php esc_html_e('Get License', 'woocommerce-commeriq'); ?>
                            </a>
                        </div>
                        
                        <form id="commeriq-license-form" class="commeriq-license-form">
                            <?php wp_nonce_field('commeriq_license_nonce', 'commeriq_license_nonce'); ?>
                            <div class="commeriq-form-grid">
                                <div class="commeriq-form-field">
                                    <label for="licence_key"><?php esc_html_e('License Key', 'woocommerce-commeriq'); ?></label>
                                    <input type="text" id="licence_key" name="licence_key" placeholder="<?php esc_attr_e('Enter your license key', 'woocommerce-commeriq'); ?>" required>
                                    <span class="commeriq-field-hint"><?php esc_html_e('Your unique license key from myapps.wontonee.com', 'woocommerce-commeriq'); ?></span>
                                </div>
                                <div class="commeriq-form-field">
                                    <label for="domain_name"><?php esc_html_e('Domain', 'woocommerce-commeriq'); ?></label>
                                    <input type="text" id="domain_name" name="domain_name" value="<?php echo esc_attr($commeriq_current_domain); ?>" placeholder="<?php esc_attr_e('example.com', 'woocommerce-commeriq'); ?>" required>
                                    <span class="commeriq-field-hint"><?php esc_html_e('The domain where this license will be activated', 'woocommerce-commeriq'); ?></span>
                                </div>
                            </div>
                            <div class="commeriq-form-actions">
                                <button type="submit" class="button button-primary button-hero">
                                    <span class="dashicons dashicons-yes-alt"></span>
                                    <?php esc_html_e('Activate License', 'woocommerce-commeriq'); ?>
                                </button>
                            </div>
                        </form>

                        <!-- Features Preview -->
                        <div class="commeriq-features-preview">
                            <h4><?php esc_html_e('Unlock These Features', 'woocommerce-commeriq'); ?></h4>
                            <div class="commeriq-features-grid">
                                <div class="commeriq-feature-card">
                                    <div class="commeriq-feature-icon">📊</div>
                                    <h5><?php esc_html_e('Price Comparison', 'woocommerce-commeriq'); ?></h5>
                                    <p><?php esc_html_e('Compare your prices with competitors across multiple platforms', 'woocommerce-commeriq'); ?></p>
                                </div>
                                <div class="commeriq-feature-card">
                                    <div class="commeriq-feature-icon">✍️</div>
                                    <h5><?php esc_html_e('AI Content Generation', 'woocommerce-commeriq'); ?></h5>
                                    <p><?php esc_html_e('Generate compelling product descriptions automatically', 'woocommerce-commeriq'); ?></p>
                                </div>
                                <div class="commeriq-feature-card">
                                    <div class="commeriq-feature-icon">🖼️</div>
                                    <h5><?php esc_html_e('AI Image Generation', 'woocommerce-commeriq'); ?></h5>
                                    <p><?php esc_html_e('Create stunning product images with AI technology', 'woocommerce-commeriq'); ?></p>
                                </div>
                                <div class="commeriq-feature-card">
                                    <div class="commeriq-feature-icon">💹</div>
                                    <h5><?php esc_html_e('Margin Analytics', 'woocommerce-commeriq'); ?></h5>
                                    <p><?php esc_html_e('Optimize your pricing strategy with smart analytics', 'woocommerce-commeriq'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Store Analyzer Tab -->
        <div id="tab-store" class="commeriq-tab">
            <?php
            // Get WooCommerce store data
            $commeriq_store_data = [];
            if (function_exists('WC')) {
                $commeriq_country_code = get_option('woocommerce_default_country', '');
                $commeriq_country_parts = explode(':', $commeriq_country_code);
                $commeriq_country = isset($commeriq_country_parts[0]) ? WC()->countries->countries[$commeriq_country_parts[0]] ?? $commeriq_country_parts[0] : '';
                $commeriq_state = isset($commeriq_country_parts[1]) ? WC()->countries->get_states($commeriq_country_parts[0])[$commeriq_country_parts[1]] ?? $commeriq_country_parts[1] : '';
                
                $commeriq_store_data = [
                    'wc_version' => WC()->version,
                    'country' => $commeriq_country,
                    'state' => $commeriq_state,
                    'currency' => get_woocommerce_currency(),
                    'currency_symbol' => get_woocommerce_currency_symbol(),
                    'address' => get_option('woocommerce_store_address', ''),
                    'city' => get_option('woocommerce_store_city', ''),
                    'postcode' => get_option('woocommerce_store_postcode', ''),
                    'weight_unit' => get_option('woocommerce_weight_unit', 'kg'),
                    'dimension_unit' => get_option('woocommerce_dimension_unit', 'cm'),
                    'tax_enabled' => get_option('woocommerce_calc_taxes') === 'yes',
                    'products_count' => wp_count_posts('product')->publish ?? 0,
                ];
            }
            ?>
            
            <div class="commeriq-store-analyzer">
                <div class="commeriq-page-header">
                    <h2 class="commeriq-page-header-title"><?php esc_html_e('Store Analyzer', 'woocommerce-commeriq'); ?></h2>
                    <p><?php esc_html_e('Comprehensive analysis of your WooCommerce store configuration and statistics', 'woocommerce-commeriq'); ?></p>
                </div>
                
                <!-- Quick Stats -->
                <div class="commeriq-stats-grid">
                    <div class="commeriq-stat-card">
                        <div class="commeriq-stat-icon">📦</div>
                        <div class="commeriq-stat-content">
                            <div class="commeriq-stat-value"><?php echo esc_html($commeriq_store_data['products_count'] ?? 0); ?></div>
                            <div class="commeriq-stat-label"><?php esc_html_e('Products', 'woocommerce-commeriq'); ?></div>
                        </div>
                    </div>
                    
                    <div class="commeriq-stat-card">
                        <div class="commeriq-stat-icon">💰</div>
                        <div class="commeriq-stat-content">
                            <div class="commeriq-stat-value"><?php echo esc_html($commeriq_store_data['currency'] ?? 'N/A'); ?></div>
                            <div class="commeriq-stat-label"><?php esc_html_e('Currency', 'woocommerce-commeriq'); ?></div>
                        </div>
                    </div>
                    
                    <div class="commeriq-stat-card">
                        <div class="commeriq-stat-icon">🌍</div>
                        <div class="commeriq-stat-content">
                            <div class="commeriq-stat-value"><?php echo esc_html($commeriq_store_data['country'] ?? 'N/A'); ?></div>
                            <div class="commeriq-stat-label"><?php esc_html_e('Country', 'woocommerce-commeriq'); ?></div>
                        </div>
                    </div>
                    
                    <div class="commeriq-stat-card">
                        <div class="commeriq-stat-icon">🛠️</div>
                        <div class="commeriq-stat-content">
                            <div class="commeriq-stat-value"><?php echo esc_html($commeriq_store_data['wc_version'] ?? 'N/A'); ?></div>
                            <div class="commeriq-stat-label"><?php esc_html_e('WooCommerce', 'woocommerce-commeriq'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Detailed Information Sections -->
                <div class="commeriq-info-sections">
                    <!-- Location Information -->
                    <div class="commeriq-info-card">
                        <div class="commeriq-info-header">
                            <span class="dashicons dashicons-location"></span>
                            <h3><?php esc_html_e('Location Information', 'woocommerce-commeriq'); ?></h3>
                        </div>
                        <div class="commeriq-info-body">
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Country', 'woocommerce-commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['country'] ?: __('Not Set', 'woocommerce-commeriq')); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('State/Region', 'woocommerce-commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['state'] ?: __('Not Set', 'woocommerce-commeriq')); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('City', 'woocommerce-commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['city'] ?: __('Not Set', 'woocommerce-commeriq')); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Postal Code', 'woocommerce-commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['postcode'] ?: __('Not Set', 'woocommerce-commeriq')); ?></span>
                            </div>
                            <?php if (!empty($commeriq_store_data['address'])): ?>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Address', 'woocommerce-commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['address']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Currency & Financial -->
                    <div class="commeriq-info-card">
                        <div class="commeriq-info-header">
                            <span class="dashicons dashicons-money-alt"></span>
                            <h3><?php esc_html_e('Currency & Financial', 'woocommerce-commeriq'); ?></h3>
                        </div>
                        <div class="commeriq-info-body">
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Currency Code', 'woocommerce-commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['currency'] ?: 'USD'); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Currency Symbol', 'woocommerce-commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['currency_symbol'] ?: '$'); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Tax Calculation', 'woocommerce-commeriq'); ?></span>
                                <span class="commeriq-info-value">
                                    <?php if ($commeriq_store_data['tax_enabled']): ?>
                                        <span class="commeriq-badge commeriq-badge-success"><?php esc_html_e('Enabled', 'woocommerce-commeriq'); ?></span>
                                    <?php else: ?>
                                        <span class="commeriq-badge commeriq-badge-default"><?php esc_html_e('Disabled', 'woocommerce-commeriq'); ?></span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Measurement Units -->
                    <div class="commeriq-info-card">
                        <div class="commeriq-info-header">
                            <span class="dashicons dashicons-chart-bar"></span>
                            <h3><?php esc_html_e('Measurement Units', 'woocommerce-commeriq'); ?></h3>
                        </div>
                        <div class="commeriq-info-body">
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Weight Unit', 'woocommerce-commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html(strtoupper($commeriq_store_data['weight_unit'])); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Dimension Unit', 'woocommerce-commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html(strtoupper($commeriq_store_data['dimension_unit'])); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="commeriq-form-actions" style="margin-top: 16px;">
                    <button type="button" class="button button-secondary" id="commeriq-refresh-store">
                        <span class="dashicons dashicons-update" style="margin-top: 3px;"></span>
                        <?php esc_html_e('Refresh Data', 'woocommerce-commeriq'); ?>
                    </button>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings')); ?>" class="button">
                        <span class="dashicons dashicons-admin-settings" style="margin-top: 3px;"></span>
                        <?php esc_html_e('WooCommerce Settings', 'woocommerce-commeriq'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Processing Modal -->
<div id="commeriq-modal" class="commeriq-modal" style="display:none;">
    <div class="commeriq-modal-backdrop"></div>
    <div class="commeriq-modal-dialog">
        <div class="commeriq-modal-content">
            <div class="commeriq-modal-icon" id="commeriq-modal-icon">⌛</div>
            <h3 id="commeriq-modal-title"><?php esc_html_e('Processing', 'woocommerce-commeriq'); ?></h3>
            <p id="commeriq-modal-message"><?php esc_html_e('Please wait...', 'woocommerce-commeriq'); ?></p>
            <div class="commeriq-modal-actions">
                <button type="button" class="button button-primary" id="commeriq-modal-close" style="display:none;"><?php esc_html_e('Close', 'woocommerce-commeriq'); ?></button>
                <button type="button" class="button" id="commeriq-modal-cancel" style="display:none;"><?php esc_html_e('Cancel', 'woocommerce-commeriq'); ?></button>
                <button type="button" class="button button-primary" id="commeriq-modal-confirm" style="display:none;"><?php esc_html_e('Confirm', 'woocommerce-commeriq'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
(function($){
    $(document).ready(function(){
        // Tab switching
        $('.nav-tab').on('click', function(e){
            e.preventDefault();
            const $tab = $(this);
            
            // Check if tab is disabled
            if ($tab.hasClass('nav-tab-disabled') || $tab.data('disabled') === '1') {
                commeriqShowModal('⚠️', '<?php echo esc_js(__('License Required', 'woocommerce-commeriq')); ?>', '<?php echo esc_js(__('Please activate your license to access this feature.', 'woocommerce-commeriq')); ?>', true);
                return;
            }
            
            const tabId = $tab.attr('href');
            
            // Update nav tabs
            $('.nav-tab').removeClass('nav-tab-active');
            $tab.addClass('nav-tab-active');
            
            // Update tab content
            $('.commeriq-tab').removeClass('commeriq-tab-active');
            $(tabId).addClass('commeriq-tab-active');
            
            // Update URL hash
            window.history.replaceState(null, null, tabId);
        });
        
        // Show modify form
        $('#commeriq-modify-license-btn').on('click', function(){
            $('.commeriq-license-active').fadeOut(200, function(){
                $('#commeriq-modify-form-container').fadeIn(200);
            });
        });
        
        // Cancel modify
        $('#commeriq-cancel-modify-btn').on('click', function(){
            $('#commeriq-modify-form-container').fadeOut(200, function(){
                $('.commeriq-license-active').fadeIn(200);
            });
        });
        
        // Remove license
        $('#commeriq-remove-license-btn').on('click', function(){
            commeriqShowConfirmModal(
                '⚠️',
                '<?php echo esc_js(__('Remove License?', 'woocommerce-commeriq')); ?>',
                '<?php echo esc_js(__('Are you sure you want to remove your license? This will disable all CommerIQ features.', 'woocommerce-commeriq')); ?>',
                function() {
                    // User confirmed, proceed with removal
                    commeriqShowModal('⌛', '<?php echo esc_js(__('Processing', 'woocommerce-commeriq')); ?>', '<?php echo esc_js(__('Removing license...', 'woocommerce-commeriq')); ?>', false);
                    
                    $.ajax({
                        url: commeriqAdmin.ajax_url,
                        method: 'POST',
                        data: {
                            action: 'commeriq_remove_license',
                            nonce: commeriqAdmin.license_nonce
                        },
                        success: function(response){
                            if (response.success) {
                                commeriqShowModal('✅', '<?php echo esc_js(__('Success', 'woocommerce-commeriq')); ?>', response.data.message || '<?php echo esc_js(__('License removed successfully', 'woocommerce-commeriq')); ?>', true);
                                setTimeout(function(){ location.reload(); }, 2000);
                            } else {
                                commeriqShowModal('⚠️', '<?php echo esc_js(__('Error', 'woocommerce-commeriq')); ?>', response.data.message || '<?php echo esc_js(__('Failed to remove license', 'woocommerce-commeriq')); ?>', true);
                            }
                        },
                        error: function(){
                            commeriqShowModal('⚠️', '<?php echo esc_js(__('Error', 'woocommerce-commeriq')); ?>', '<?php echo esc_js(__('Connection failed', 'woocommerce-commeriq')); ?>', true);
                        }
                    });
                }
            );
        });
        
        // License form submission (both activate and modify)
        $(document).on('submit', '#commeriq-license-form, #commeriq-modify-form', function(e){
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalHtml = $submitBtn.html();
            
            const licenceKey = $form.find('[name="licence_key"]').val().trim();
            const domainName = $form.find('[name="domain_name"]').val().trim();
            
            if (!licenceKey || !domainName) {
                commeriqShowModal('⚠️', '<?php echo esc_js(__('Validation Error', 'woocommerce-commeriq')); ?>', '<?php echo esc_js(__('Please fill in all fields', 'woocommerce-commeriq')); ?>', true);
                return;
            }
            
            $submitBtn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation: rotation 2s infinite linear;"></span> <?php echo esc_js(__('Processing...', 'woocommerce-commeriq')); ?>');
            commeriqShowModal('⌛', '<?php echo esc_js(__('Processing', 'woocommerce-commeriq')); ?>', '<?php echo esc_js(__('Activating your license...', 'woocommerce-commeriq')); ?>', false);
            
            $.ajax({
                url: commeriqAdmin.ajax_url,
                method: 'POST',
                data: {
                    action: 'commeriq_activate_license',
                    nonce: commeriqAdmin.license_nonce,
                    licence_key: licenceKey,
                    domain_name: domainName
                },
                success: function(response){
                    if (response.success) {
                        commeriqShowModal('✅', '<?php echo esc_js(__('Success!', 'woocommerce-commeriq')); ?>', response.data.message || '<?php echo esc_js(__('License activated successfully', 'woocommerce-commeriq')); ?>', true);
                        setTimeout(function(){ location.reload(); }, 2000);
                    } else {
                        commeriqShowModal('⚠️', '<?php echo esc_js(__('Activation Failed', 'woocommerce-commeriq')); ?>', response.data.message || '<?php echo esc_js(__('Failed to activate license', 'woocommerce-commeriq')); ?>', true);
                        $submitBtn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function(xhr){
                    let errorMessage = '<?php echo esc_js(__('Connection failed', 'woocommerce-commeriq')); ?>';
                    
                    if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                        errorMessage = xhr.responseJSON.data.message;
                    }
                    
                    commeriqShowModal('⚠️', '<?php echo esc_js(__('Error', 'woocommerce-commeriq')); ?>', errorMessage, true);
                    $submitBtn.prop('disabled', false).html(originalHtml);
                }
            });
        });
        
        // Refresh store data
        $('#commeriq-refresh-store').on('click', function(e){
            e.preventDefault();
            const $btn = $(this);
            const originalHtml = $btn.html();
            
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="margin-top: 3px; animation: rotation 2s infinite linear;"></span><?php echo esc_js(__('Refreshing...', 'woocommerce-commeriq')); ?>');
            
            $.ajax({
                url: commeriqAdmin.ajax_url,
                method: 'POST',
                data: {
                    action: 'commeriq_retrieve_store',
                    _nonce: commeriqAdmin.retrieve_nonce
                },
                success: function(response){
                    $btn.prop('disabled', false).html(originalHtml);
                    if (response.success) {
                        commeriqShowModal('✅', '<?php echo esc_js(__('Success', 'woocommerce-commeriq')); ?>', '<?php echo esc_js(__('Store data refreshed', 'woocommerce-commeriq')); ?>', true);
                        setTimeout(function(){ location.reload(); }, 1500);
                    } else {
                        commeriqShowModal('⚠️', '<?php echo esc_js(__('Error', 'woocommerce-commeriq')); ?>', '<?php echo esc_js(__('Failed to refresh data', 'woocommerce-commeriq')); ?>', true);
                    }
                },
                error: function(){
                    $btn.prop('disabled', false).html(originalHtml);
                    commeriqShowModal('⚠️', '<?php echo esc_js(__('Error', 'woocommerce-commeriq')); ?>', '<?php echo esc_js(__('Connection failed', 'woocommerce-commeriq')); ?>', true);
                }
            });
        });
        
        // Restore active tab from URL hash
        if (window.location.hash) {
            const hash = window.location.hash;
            const $tab = $('.nav-tab[href="' + hash + '"]');
            if ($tab.length && !$tab.hasClass('nav-tab-disabled')) {
                $tab.trigger('click');
            }
        }
    });
    
    // Modal helper functions (defined globally)
    window.commeriqShowModal = function(icon, title, message, showClose) {
        // Hide confirmation buttons
        $('#commeriq-modal-confirm, #commeriq-modal-cancel').hide();
        
        $('#commeriq-modal-icon').text(icon);
        $('#commeriq-modal-title').text(title);
        $('#commeriq-modal-message').text(message);
        $('#commeriq-modal-close').toggle(showClose);
        $('#commeriq-modal').fadeIn(200);
    };
    
    window.commeriqShowConfirmModal = function(icon, title, message, onConfirm) {
        // Hide close button, show confirmation buttons
        $('#commeriq-modal-close').hide();
        $('#commeriq-modal-confirm, #commeriq-modal-cancel').show();
        
        $('#commeriq-modal-icon').text(icon);
        $('#commeriq-modal-title').text(title);
        $('#commeriq-modal-message').text(message);
        
        // Remove any existing click handlers
        $('#commeriq-modal-confirm').off('click');
        $('#commeriq-modal-cancel').off('click');
        
        // Add new handlers
        $('#commeriq-modal-confirm').on('click', function(){
            $('#commeriq-modal').fadeOut(200);
            if (onConfirm) {
                setTimeout(onConfirm, 250); // Small delay to let modal close
            }
        });
        
        $('#commeriq-modal-cancel').on('click', function(){
            $('#commeriq-modal').fadeOut(200);
        });
        
        $('#commeriq-modal').fadeIn(200);
    };
    
    // Modal close handlers
    $(document).on('click', '#commeriq-modal-close', function(){
        $('#commeriq-modal').fadeOut(200);
    });
    
    $(document).on('click', '.commeriq-modal-backdrop', function(){
        // Only close on backdrop click if it's not a confirmation modal
        if ($('#commeriq-modal-confirm').is(':visible')) {
            return; // Don't close confirmation modals on backdrop click
        }
        $('#commeriq-modal').fadeOut(200);
    });
})(jQuery);
</script>
