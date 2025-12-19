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
                    <h1><?php esc_html_e('CommerIQ', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h1>
                    <p class="description"><?php esc_html_e('AI-Powered Commerce Intelligence for WooCommerce', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab nav-tab-active" href="#tab-licence"><?php esc_html_e('License', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></a>
        <a class="nav-tab" href="#tab-store"><?php esc_html_e('Store Analyzer', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></a>
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
                                    <h3><?php esc_html_e('License Active', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h3>
                                    <p><?php esc_html_e('Your license is active and all features are enabled', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
                                </div>
                            </div>
                            <div class="commeriq-license-actions">
                                <button type="button" class="button button-secondary" id="commeriq-modify-license-btn">
                                    <span class="dashicons dashicons-edit"></span>
                                    <?php esc_html_e('Modify', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                                </button>
                                <button type="button" class="button button-link-delete" id="commeriq-remove-license-btn">
                                    <span class="dashicons dashicons-trash"></span>
                                    <?php esc_html_e('Remove', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                                </button>
                            </div>
                        </div>
                        
                        <div class="commeriq-license-details">
                            <div class="commeriq-detail-item">
                                <span class="commeriq-detail-icon">🔑</span>
                                <div class="commeriq-detail-content">
                                    <span class="commeriq-detail-label"><?php esc_html_e('License Key', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                    <span class="commeriq-detail-value"><?php echo esc_html($commeriq_licence_key); ?></span>
                                </div>
                            </div>
                            <div class="commeriq-detail-item">
                                <span class="commeriq-detail-icon">🌐</span>
                                <div class="commeriq-detail-content">
                                    <span class="commeriq-detail-label"><?php esc_html_e('Domain', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                    <span class="commeriq-detail-value"><?php echo esc_html($commeriq_domain_name); ?></span>
                                </div>
                            </div>
                            <div class="commeriq-detail-item">
                                <span class="commeriq-detail-icon">📅</span>
                                <div class="commeriq-detail-content">
                                    <span class="commeriq-detail-label"><?php esc_html_e('Activated On', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                    <span class="commeriq-detail-value"><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($commeriq_activated_at))); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Features Showcase -->
                        <div class="commeriq-features-showcase">
                            <h4><?php esc_html_e('Active Features', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h4>
                            <div class="commeriq-features-list">
                                <div class="commeriq-feature-item">
                                    <span class="commeriq-feature-icon">📊</span>
                                    <span><?php esc_html_e('Price Comparison', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                </div>
                                <div class="commeriq-feature-item">
                                    <span class="commeriq-feature-icon">✍️</span>
                                    <span><?php esc_html_e('AI Descriptions', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                </div>
                                <div class="commeriq-feature-item">
                                    <span class="commeriq-feature-icon">🖼️</span>
                                    <span><?php esc_html_e('Image Generation', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                </div>
                                <div class="commeriq-feature-item">
                                    <span class="commeriq-feature-icon">💹</span>
                                    <span><?php esc_html_e('Margin Analytics', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
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
                                    <h3><?php esc_html_e('Modify License', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h3>
                                    <p><?php esc_html_e('Update your license key or domain', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <form id="commeriq-modify-form" class="commeriq-license-form">
                            <?php wp_nonce_field('commeriq_license_nonce', 'commeriq_license_nonce'); ?>
                            <div class="commeriq-form-grid">
                                <div class="commeriq-form-field">
                                    <label for="modify_licence_key"><?php esc_html_e('License Key', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></label>
                                    <input type="text" id="modify_licence_key" name="licence_key" value="<?php echo esc_attr($commeriq_licence_key); ?>" placeholder="<?php esc_attr_e('Enter your license key', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>" required>
                                </div>
                                <div class="commeriq-form-field">
                                    <label for="modify_domain_name"><?php esc_html_e('Domain', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></label>
                                    <input type="text" id="modify_domain_name" name="domain_name" value="<?php echo esc_attr($commeriq_domain_name); ?>" placeholder="<?php esc_attr_e('example.com', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>" required>
                                </div>
                            </div>
                            <div class="commeriq-form-actions">
                                <button type="submit" class="button button-primary button-large">
                                    <span class="dashicons dashicons-saved"></span>
                                    <?php esc_html_e('Update License', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                                </button>
                                <button type="button" class="button button-secondary button-large" id="commeriq-cancel-modify-btn">
                                    <?php esc_html_e('Cancel', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
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
                                    <h3><?php esc_html_e('Activate Your License', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h3>
                                    <p><?php esc_html_e('Enter your license key to unlock all AI-powered features', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
                                </div>
                            </div>
                            <a href="https://myapps.wontonee.com" target="_blank" rel="noopener noreferrer" class="button button-primary">
                                <span class="dashicons dashicons-external"></span>
                                <?php esc_html_e('Get License', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                            </a>
                        </div>
                        
                        <form id="commeriq-license-form" class="commeriq-license-form">
                            <?php wp_nonce_field('commeriq_license_nonce', 'commeriq_license_nonce'); ?>
                            <div class="commeriq-form-grid">
                                <div class="commeriq-form-field">
                                    <label for="licence_key"><?php esc_html_e('License Key', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></label>
                                    <input type="text" id="licence_key" name="licence_key" placeholder="<?php esc_attr_e('Enter your license key', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>" required>
                                    <span class="commeriq-field-hint"><?php esc_html_e('Your unique license key from myapps.wontonee.com', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                </div>
                                <div class="commeriq-form-field">
                                    <label for="domain_name"><?php esc_html_e('Domain', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></label>
                                    <input type="text" id="domain_name" name="domain_name" value="<?php echo esc_attr($commeriq_current_domain); ?>" placeholder="<?php esc_attr_e('example.com', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>" required>
                                    <span class="commeriq-field-hint"><?php esc_html_e('The domain where this license will be activated', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                </div>
                            </div>
                            <div class="commeriq-form-actions">
                                <button type="submit" class="button button-primary button-hero">
                                    <span class="dashicons dashicons-yes-alt"></span>
                                    <?php esc_html_e('Activate License', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                                </button>
                            </div>
                        </form>

                        <!-- Features Preview -->
                        <div class="commeriq-features-preview">
                            <h4><?php esc_html_e('Unlock These Features', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h4>
                            <div class="commeriq-features-grid">
                                <div class="commeriq-feature-card">
                                    <div class="commeriq-feature-icon">📊</div>
                                    <h5><?php esc_html_e('Price Comparison', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h5>
                                    <p><?php esc_html_e('Compare your prices with competitors across multiple platforms', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
                                </div>
                                <div class="commeriq-feature-card">
                                    <div class="commeriq-feature-icon">✍️</div>
                                    <h5><?php esc_html_e('AI Content Generation', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h5>
                                    <p><?php esc_html_e('Generate compelling product descriptions automatically', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
                                </div>
                                <div class="commeriq-feature-card">
                                    <div class="commeriq-feature-icon">🖼️</div>
                                    <h5><?php esc_html_e('AI Image Generation', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h5>
                                    <p><?php esc_html_e('Create stunning product images with AI technology', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
                                </div>
                                <div class="commeriq-feature-card">
                                    <div class="commeriq-feature-icon">💹</div>
                                    <h5><?php esc_html_e('Margin Analytics', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h5>
                                    <p><?php esc_html_e('Optimize your pricing strategy with smart analytics', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
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
                    <h2 class="commeriq-page-header-title"><?php esc_html_e('Store Analyzer', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h2>
                    <p><?php esc_html_e('Comprehensive analysis of your WooCommerce store configuration and statistics', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
                </div>
                
                <!-- Quick Stats -->
                <div class="commeriq-stats-grid">
                    <div class="commeriq-stat-card">
                        <div class="commeriq-stat-icon">📦</div>
                        <div class="commeriq-stat-content">
                            <div class="commeriq-stat-value"><?php echo esc_html($commeriq_store_data['products_count'] ?? 0); ?></div>
                            <div class="commeriq-stat-label"><?php esc_html_e('Products', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></div>
                        </div>
                    </div>
                    
                    <div class="commeriq-stat-card">
                        <div class="commeriq-stat-icon">💰</div>
                        <div class="commeriq-stat-content">
                            <div class="commeriq-stat-value"><?php echo esc_html($commeriq_store_data['currency'] ?? 'N/A'); ?></div>
                            <div class="commeriq-stat-label"><?php esc_html_e('Currency', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></div>
                        </div>
                    </div>
                    
                    <div class="commeriq-stat-card">
                        <div class="commeriq-stat-icon">🌍</div>
                        <div class="commeriq-stat-content">
                            <div class="commeriq-stat-value"><?php echo esc_html($commeriq_store_data['country'] ?? 'N/A'); ?></div>
                            <div class="commeriq-stat-label"><?php esc_html_e('Country', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></div>
                        </div>
                    </div>
                    
                    <div class="commeriq-stat-card">
                        <div class="commeriq-stat-icon">🛠️</div>
                        <div class="commeriq-stat-content">
                            <div class="commeriq-stat-value"><?php echo esc_html($commeriq_store_data['wc_version'] ?? 'N/A'); ?></div>
                            <div class="commeriq-stat-label"><?php esc_html_e('WooCommerce', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Detailed Information Sections -->
                <div class="commeriq-info-sections">
                    <!-- Location Information -->
                    <div class="commeriq-info-card">
                        <div class="commeriq-info-header">
                            <span class="dashicons dashicons-location"></span>
                            <h3><?php esc_html_e('Location Information', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h3>
                        </div>
                        <div class="commeriq-info-body">
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Country', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['country'] ?: __('Not Set', 'commeriq-ai-powered-commerce-insights-for-woocommerce')); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('State/Region', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['state'] ?: __('Not Set', 'commeriq-ai-powered-commerce-insights-for-woocommerce')); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('City', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['city'] ?: __('Not Set', 'commeriq-ai-powered-commerce-insights-for-woocommerce')); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Postal Code', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['postcode'] ?: __('Not Set', 'commeriq-ai-powered-commerce-insights-for-woocommerce')); ?></span>
                            </div>
                            <?php if (!empty($commeriq_store_data['address'])): ?>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Address', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['address']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Currency & Financial -->
                    <div class="commeriq-info-card">
                        <div class="commeriq-info-header">
                            <span class="dashicons dashicons-money-alt"></span>
                            <h3><?php esc_html_e('Currency & Financial', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h3>
                        </div>
                        <div class="commeriq-info-body">
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Currency Code', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['currency'] ?: 'USD'); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Currency Symbol', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($commeriq_store_data['currency_symbol'] ?: '$'); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Tax Calculation', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                <span class="commeriq-info-value">
                                    <?php if ($commeriq_store_data['tax_enabled']): ?>
                                        <span class="commeriq-badge commeriq-badge-success"><?php esc_html_e('Enabled', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                    <?php else: ?>
                                        <span class="commeriq-badge commeriq-badge-default"><?php esc_html_e('Disabled', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Measurement Units -->
                    <div class="commeriq-info-card">
                        <div class="commeriq-info-header">
                            <span class="dashicons dashicons-chart-bar"></span>
                            <h3><?php esc_html_e('Measurement Units', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h3>
                        </div>
                        <div class="commeriq-info-body">
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Weight Unit', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html(strtoupper($commeriq_store_data['weight_unit'])); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Dimension Unit', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html(strtoupper($commeriq_store_data['dimension_unit'])); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="commeriq-form-actions" style="margin-top: 16px;">
                    <button type="button" class="button button-secondary" id="commeriq-refresh-store">
                        <span class="dashicons dashicons-update" style="margin-top: 3px;"></span>
                        <?php esc_html_e('Refresh Data', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
                    </button>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings')); ?>" class="button">
                        <span class="dashicons dashicons-admin-settings" style="margin-top: 3px;"></span>
                        <?php esc_html_e('WooCommerce Settings', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?>
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
            <h3 id="commeriq-modal-title"><?php esc_html_e('Processing', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></h3>
            <p id="commeriq-modal-message"><?php esc_html_e('Please wait...', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></p>
            <div class="commeriq-modal-actions">
                <button type="button" class="button button-primary" id="commeriq-modal-close" style="display:none;"><?php esc_html_e('Close', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></button>
                <button type="button" class="button" id="commeriq-modal-cancel" style="display:none;"><?php esc_html_e('Cancel', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></button>
                <button type="button" class="button button-primary" id="commeriq-modal-confirm" style="display:none;"><?php esc_html_e('Confirm', 'commeriq-ai-powered-commerce-insights-for-woocommerce'); ?></button>
            </div>
        </div>
    </div>
</div>
