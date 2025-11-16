<?php
defined('ABSPATH') || exit;

// Ensure LicenseManager is available
if (!class_exists('CommerIQ\\Helpers\\LicenseManager')) {
    require_once COMMERIQ_PLUGIN_DIR . 'src/Helpers/LicenseManager.php';
}

use CommerIQ\Helpers\LicenseManager;

// Get license status
$license = LicenseManager::get_license();
$is_active = LicenseManager::is_license_active();
$licence_key = isset($license['licence_key']) ? $license['licence_key'] : '';
$domain_name = isset($license['domain_name']) ? $license['domain_name'] : '';
$activated_at = isset($license['activated_at']) ? $license['activated_at'] : '';

// Get current domain for auto-fill
$current_domain = LicenseManager::get_current_domain();
?>

<div class="wrap commeriq-admin-wrap">
    <h1><?php esc_html_e('CommerIQ', 'commeriq'); ?></h1>
    <p class="description"><?php esc_html_e('Smart AI for Product Description, Image Generation & Price Comparison', 'commeriq'); ?></p>

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab nav-tab-active" href="#tab-licence"><?php esc_html_e('License', 'commeriq'); ?></a>
        <?php $store_tab_class = $is_active ? 'nav-tab' : 'nav-tab nav-tab-disabled'; ?>
        <a class="<?php echo $store_tab_class; ?>" href="#tab-store" data-disabled="<?php echo $is_active ? '0' : '1'; ?>"><?php esc_html_e('Store Analyzer', 'commeriq'); ?></a>
    </h2>

    <div class="commeriq-tab-content">
        <!-- License Tab -->
        <div id="tab-licence" class="commeriq-tab commeriq-tab-active">
            <div class="commeriq-license-management">
                <div class="commeriq-page-header">
                    <h2 class="commeriq-page-header-title"><?php esc_html_e('License Management', 'commeriq'); ?></h2>
                    <p><?php esc_html_e('Please activate your CommerIQ license to access AI features for WooCommerce.', 'commeriq'); ?></p>
                </div>

                <?php if ($is_active): ?>
                    <!-- Active License Display -->
                    <div class="commeriq-form-section commeriq-license-active-section commeriq-license-active">
                        <div class="commeriq-license-status-header">
                            <span class="dashicons dashicons-yes-alt commeriq-license-icon-success"></span>
                            <h3><?php esc_html_e('License Active', 'commeriq'); ?></h3>
                        </div>
                        <div class="commeriq-license-details">
                            <div class="commeriq-form-row">
                                <div class="commeriq-form-group">
                                    <label><?php esc_html_e('License Key', 'commeriq'); ?></label>
                                    <input type="text" value="<?php echo esc_attr(substr($licence_key, 0, 10) . '...'); ?>" readonly>
                                </div>
                                <div class="commeriq-form-group">
                                    <label><?php esc_html_e('Domain', 'commeriq'); ?></label>
                                    <input type="text" value="<?php echo esc_attr($domain_name); ?>" readonly>
                                </div>
                            </div>
                            <div class="commeriq-form-group">
                                <label><?php esc_html_e('Activated Date', 'commeriq'); ?></label>
                                <input type="text" value="<?php echo esc_attr(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($activated_at))); ?>" readonly>
                            </div>
                        </div>
                        <div class="commeriq-form-actions">
                            <button type="button" class="button button-secondary" onclick="commeriqShowModifyLicense()">
                                <?php esc_html_e('Modify License', 'commeriq'); ?>
                            </button>
                            <button type="button" class="button button-link-delete" onclick="commeriqRemoveLicense()">
                                <?php esc_html_e('Remove License', 'commeriq'); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Hidden modify form -->
                    <div id="commeriq-modify-license-form" class="commeriq-form-section" style="display: none;">
                        <h3 class="commeriq-form-section-title"><?php esc_html_e('Modify License', 'commeriq'); ?></h3>
                        <form id="commeriq-modify-form" method="post">
                            <?php wp_nonce_field('commeriq_license_nonce', 'commeriq_license_nonce'); ?>
                            <div class="commeriq-form-row">
                                <div class="commeriq-form-group">
                                    <label for="licence_key"><?php esc_html_e('License Key', 'commeriq'); ?></label>
                                    <input type="text" 
                                           id="licence_key" 
                                           name="licence_key" 
                                           value="<?php echo esc_attr($licence_key); ?>" 
                                           placeholder="<?php esc_attr_e('Enter your license key', 'commeriq'); ?>" 
                                           required>
                                </div>
                                <div class="commeriq-form-group">
                                    <label for="domain_name"><?php esc_html_e('Domain Name', 'commeriq'); ?></label>
                                    <input type="text" 
                                           id="domain_name" 
                                           name="domain_name" 
                                           value="<?php echo esc_attr($domain_name); ?>" 
                                           placeholder="<?php esc_attr_e('example.com', 'commeriq'); ?>" 
                                           required>
                                    <small><?php esc_html_e('Enter your domain name without http:// or https://', 'commeriq'); ?></small>
                                </div>
                            </div>
                            <div class="commeriq-form-actions">
                                <button type="submit" class="button button-primary"><?php esc_html_e('Update License', 'commeriq'); ?></button>
                                <button type="button" class="button button-secondary" onclick="commeriqCancelModify()"><?php esc_html_e('Cancel', 'commeriq'); ?></button>
                            </div>
                        </form>
                    </div>

                <?php else: ?>
                    <!-- License Information Note -->
                    <div class="commeriq-license-info-note">
                        <div class="commeriq-note-icon">
                            <span class="dashicons dashicons-info-outline"></span>
                        </div>
                        <div class="commeriq-note-content" style="flex: 1;">
                            <strong style="display: inline; margin-right: 8px;"><?php esc_html_e('Need a License?', 'commeriq'); ?></strong>
                            <span style="display: inline; margin-right: 8px; font-size: 12px;"><?php esc_html_e('Unlock AI features for WooCommerce', 'commeriq'); ?></span>
                            <a href="https://myapps.wontonee.com" target="_blank" rel="noopener noreferrer" class="button button-primary" style="vertical-align: middle;">
                                <span class="dashicons dashicons-external" style="margin-top: 2px; font-size: 12px;"></span>
                                <?php esc_html_e('Get License', 'commeriq'); ?>
                            </a>
                        </div>
                    </div>
                    
                    <!-- License Activation Form -->
                    <div class="commeriq-form-section">
                        <div class="commeriq-license-status-header">
                            <span class="dashicons dashicons-lock commeriq-license-icon-warning"></span>
                            <h3 class="commeriq-form-section-title"><?php esc_html_e('Activate Your License', 'commeriq'); ?></h3>
                        </div>
                        
                        <form id="commeriq-license-form" method="post" style="margin-top: 8px;">
                            <?php wp_nonce_field('commeriq_license_nonce', 'commeriq_license_nonce'); ?>
                            <div class="commeriq-form-row">
                                <div class="commeriq-form-group">
                                    <label for="licence_key"><?php esc_html_e('License Key', 'commeriq'); ?></label>
                                    <input type="text" 
                                           id="licence_key" 
                                           name="licence_key" 
                                           placeholder="<?php esc_attr_e('Enter your license key', 'commeriq'); ?>" 
                                           required>
                                </div>
                                <div class="commeriq-form-group">
                                    <label for="domain_name"><?php esc_html_e('Domain Name', 'commeriq'); ?></label>
                                    <input type="text" 
                                           id="domain_name" 
                                           name="domain_name" 
                                           value="<?php echo esc_attr($current_domain); ?>"
                                           placeholder="<?php esc_attr_e('example.com', 'commeriq'); ?>" 
                                           required>
                                    <small><?php esc_html_e('Enter your domain name without http:// or https://', 'commeriq'); ?></small>
                                </div>
                            </div>
                            <div class="commeriq-form-actions">
                                <button type="submit" class="button button-primary commeriq-activate-btn"><?php esc_html_e('Activate License', 'commeriq'); ?></button>
                            </div>
                        </form>
                    </div>

                    <!-- Features Preview -->
                    <div class="commeriq-form-section commeriq-features-section">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3 class="commeriq-form-section-title" style="margin: 0;"><?php esc_html_e('Unlock These Features', 'commeriq'); ?></h3>
                            <button type="button" class="button button-secondary commeriq-toggle-features" style="padding: 4px 10px; font-size: 12px;">
                                <span class="dashicons dashicons-arrow-down-alt2" style="font-size: 14px; margin-top: 2px;"></span>
                                <?php esc_html_e('Show Features', 'commeriq'); ?>
                            </button>
                        </div>
                        <div class="commeriq-features-grid" style="display: none; margin-top: 10px;">
                            <div class="commeriq-feature-card">
                                <div class="commeriq-feature-icon">
                                    <span class="dashicons dashicons-chart-line"></span>
                                </div>
                                <h4><?php esc_html_e('Price Comparison', 'commeriq'); ?></h4>
                                <p><?php esc_html_e('Compare your product prices with competitors', 'commeriq'); ?></p>
                            </div>
                            <div class="commeriq-feature-card">
                                <div class="commeriq-feature-icon">
                                    <span class="dashicons dashicons-edit-large"></span>
                                </div>
                                <h4><?php esc_html_e('AI Descriptions', 'commeriq'); ?></h4>
                                <p><?php esc_html_e('Generate compelling product descriptions with AI', 'commeriq'); ?></p>
                            </div>
                            <div class="commeriq-feature-card">
                                <div class="commeriq-feature-icon">
                                    <span class="dashicons dashicons-images-alt2"></span>
                                </div>
                                <h4><?php esc_html_e('Image Generation', 'commeriq'); ?></h4>
                                <p><?php esc_html_e('Create product images with AI assistance', 'commeriq'); ?></p>
                            </div>
                            <div class="commeriq-feature-card">
                                <div class="commeriq-feature-icon">
                                    <span class="dashicons dashicons-analytics"></span>
                                </div>
                                <h4><?php esc_html_e('Margin Analytics', 'commeriq'); ?></h4>
                                <p><?php esc_html_e('Track and optimize your profit margins', 'commeriq'); ?></p>
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
            $store_data = [];
            if (function_exists('WC')) {
                $country_code = get_option('woocommerce_default_country', '');
                $country_parts = explode(':', $country_code);
                $country = isset($country_parts[0]) ? WC()->countries->countries[$country_parts[0]] ?? $country_parts[0] : '';
                $state = isset($country_parts[1]) ? WC()->countries->get_states($country_parts[0])[$country_parts[1]] ?? $country_parts[1] : '';
                
                $store_data = [
                    'wc_version' => WC()->version,
                    'country' => $country,
                    'state' => $state,
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
                    <h2 class="commeriq-page-header-title"><?php esc_html_e('Store Analyzer', 'commeriq'); ?></h2>
                    <p><?php esc_html_e('Comprehensive analysis of your WooCommerce store configuration and statistics', 'commeriq'); ?></p>
                </div>
                
                <!-- Quick Stats -->
                <div class="commeriq-stats-grid">
                    <div class="commeriq-stat-card">
                        <div class="commeriq-stat-icon">📦</div>
                        <div class="commeriq-stat-content">
                            <div class="commeriq-stat-value"><?php echo esc_html($store_data['products_count'] ?? 0); ?></div>
                            <div class="commeriq-stat-label"><?php esc_html_e('Products', 'commeriq'); ?></div>
                        </div>
                    </div>
                    
                    <div class="commeriq-stat-card">
                        <div class="commeriq-stat-icon">💰</div>
                        <div class="commeriq-stat-content">
                            <div class="commeriq-stat-value"><?php echo esc_html($store_data['currency'] ?? 'N/A'); ?></div>
                            <div class="commeriq-stat-label"><?php esc_html_e('Currency', 'commeriq'); ?></div>
                        </div>
                    </div>
                    
                    <div class="commeriq-stat-card">
                        <div class="commeriq-stat-icon">🌍</div>
                        <div class="commeriq-stat-content">
                            <div class="commeriq-stat-value"><?php echo esc_html($store_data['country'] ?? 'N/A'); ?></div>
                            <div class="commeriq-stat-label"><?php esc_html_e('Country', 'commeriq'); ?></div>
                        </div>
                    </div>
                    
                    <div class="commeriq-stat-card">
                        <div class="commeriq-stat-icon">🛠️</div>
                        <div class="commeriq-stat-content">
                            <div class="commeriq-stat-value"><?php echo esc_html($store_data['wc_version'] ?? 'N/A'); ?></div>
                            <div class="commeriq-stat-label"><?php esc_html_e('WooCommerce', 'commeriq'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Detailed Information Sections -->
                <div class="commeriq-info-sections">
                    <!-- Location Information -->
                    <div class="commeriq-info-card">
                        <div class="commeriq-info-header">
                            <span class="dashicons dashicons-location"></span>
                            <h3><?php esc_html_e('Location Information', 'commeriq'); ?></h3>
                        </div>
                        <div class="commeriq-info-body">
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Country', 'commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($store_data['country'] ?: __('Not Set', 'commeriq')); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('State/Region', 'commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($store_data['state'] ?: __('Not Set', 'commeriq')); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('City', 'commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($store_data['city'] ?: __('Not Set', 'commeriq')); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Postal Code', 'commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($store_data['postcode'] ?: __('Not Set', 'commeriq')); ?></span>
                            </div>
                            <?php if (!empty($store_data['address'])): ?>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Address', 'commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($store_data['address']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Currency & Financial -->
                    <div class="commeriq-info-card">
                        <div class="commeriq-info-header">
                            <span class="dashicons dashicons-money-alt"></span>
                            <h3><?php esc_html_e('Currency & Financial', 'commeriq'); ?></h3>
                        </div>
                        <div class="commeriq-info-body">
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Currency Code', 'commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($store_data['currency'] ?: 'USD'); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Currency Symbol', 'commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html($store_data['currency_symbol'] ?: '$'); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Tax Calculation', 'commeriq'); ?></span>
                                <span class="commeriq-info-value">
                                    <?php if ($store_data['tax_enabled']): ?>
                                        <span class="commeriq-badge commeriq-badge-success"><?php esc_html_e('Enabled', 'commeriq'); ?></span>
                                    <?php else: ?>
                                        <span class="commeriq-badge commeriq-badge-default"><?php esc_html_e('Disabled', 'commeriq'); ?></span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Measurement Units -->
                    <div class="commeriq-info-card">
                        <div class="commeriq-info-header">
                            <span class="dashicons dashicons-chart-bar"></span>
                            <h3><?php esc_html_e('Measurement Units', 'commeriq'); ?></h3>
                        </div>
                        <div class="commeriq-info-body">
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Weight Unit', 'commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html(strtoupper($store_data['weight_unit'])); ?></span>
                            </div>
                            <div class="commeriq-info-row">
                                <span class="commeriq-info-label"><?php esc_html_e('Dimension Unit', 'commeriq'); ?></span>
                                <span class="commeriq-info-value"><?php echo esc_html(strtoupper($store_data['dimension_unit'])); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="commeriq-form-actions" style="margin-top: 16px;">
                    <button type="button" class="button button-secondary" id="commeriq-refresh-store">
                        <span class="dashicons dashicons-update" style="margin-top: 3px;"></span>
                        <?php esc_html_e('Refresh Data', 'commeriq'); ?>
                    </button>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings')); ?>" class="button">
                        <span class="dashicons dashicons-admin-settings" style="margin-top: 3px;"></span>
                        <?php esc_html_e('WooCommerce Settings', 'commeriq'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Processing Modal -->
<div id="commeriq-processing-modal" class="commeriq-modal" style="display:none;">
    <div class="commeriq-modal-backdrop"></div>
    <div class="commeriq-modal-dialog">
        <div class="commeriq-modal-content">
            <div class="commeriq-modal-icon" id="commeriq-modal-icon">⌛</div>
            <h3 id="commeriq-modal-title"><?php esc_html_e('Processing', 'commeriq'); ?></h3>
            <p id="commeriq-modal-message"><?php esc_html_e('Please wait...', 'commeriq'); ?></p>
            <div class="commeriq-modal-actions">
                <button type="button" class="button" id="commeriq-modal-close" style="display:none;"><?php esc_html_e('Close', 'commeriq'); ?></button>
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
                alert('<?php echo esc_js(__('Please activate your license to access this feature.', 'commeriq')); ?>');
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
        
        // License form submission (both activate and modify forms)
        $(document).on('submit', '#commeriq-license-form, #commeriq-modify-form', function(e){
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();
            
            const licenceKey = $form.find('[name="licence_key"]').val().trim();
            const domainName = $form.find('[name="domain_name"]').val().trim();
            
            if (!licenceKey || !domainName) {
                commeriqShowModal('⚠️', '<?php echo esc_js(__('Validation Error', 'commeriq')); ?>', '<?php echo esc_js(__('Please fill in all fields.', 'commeriq')); ?>', true);
                return;
            }
            
            $submitBtn.prop('disabled', true).text('<?php echo esc_js(__('Processing...', 'commeriq')); ?>');
            commeriqShowModal('⌛', '<?php echo esc_js(__('Processing Activation', 'commeriq')); ?>', '<?php echo esc_js(__('Activating your license...', 'commeriq')); ?>', false);
            
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
                        commeriqShowModal('✅', '<?php echo esc_js(__('License Activated', 'commeriq')); ?>', response.data.message || '<?php echo esc_js(__('Your license has been successfully activated!', 'commeriq')); ?>', true);
                        setTimeout(function(){ location.reload(); }, 2000);
                    } else {
                        commeriqShowModal('⚠️', '<?php echo esc_js(__('Activation Failed', 'commeriq')); ?>', response.data.message || '<?php echo esc_js(__('Failed to activate license.', 'commeriq')); ?>', true);
                        $submitBtn.prop('disabled', false).text(originalText);
                    }
                },
                error: function(xhr){
                    let errorMessage = '<?php echo esc_js(__('Failed to connect to the server. Please try again.', 'commeriq')); ?>';
                    
                    // Try to parse error response
                    if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                        errorMessage = xhr.responseJSON.data.message;
                    } else if (xhr.responseText) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (data.data && data.data.message) {
                                errorMessage = data.data.message;
                            }
                        } catch (e) {
                            // Keep default error message
                        }
                    }
                    
                    commeriqShowModal('⚠️', '<?php echo esc_js(__('Connection Error', 'commeriq')); ?>', errorMessage, true);
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });
        
        // Refresh store configuration
        $('#commeriq-refresh-store').on('click', function(e){
            e.preventDefault();
            const $btn = $(this);
            const originalHtml = $btn.html();
            
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="margin-top: 3px; animation: rotation 2s infinite linear;"></span><?php echo esc_js(__('Refreshing...', 'commeriq')); ?>');
            
            $.ajax({
                url: commeriqAdmin.ajax_url,
                method: 'POST',
                data: {
                    action: 'commeriq_retrieve_store',
                    _nonce: commeriqAdmin.retrieve_nonce
                },
                success: function(response){
                    $btn.prop('disabled', false).html(originalHtml);
                    if (response.success && response.data) {
                        // Update Location Information card values
                        var $locationCard = $('.commeriq-info-card').first();
                        $locationCard.find('.commeriq-info-value').eq(0).text(response.data.country || '<?php echo esc_js(__('Not Set', 'commeriq')); ?>');
                        $locationCard.find('.commeriq-info-value').eq(1).text(response.data.state || '<?php echo esc_js(__('Not Set', 'commeriq')); ?>');
                        
                        // Update Currency & Financial card values
                        var $currencyCard = $('.commeriq-info-card').eq(1);
                        $currencyCard.find('.commeriq-info-value').eq(0).text(response.data.currency || 'USD');
                        
                        // Show success message
                        commeriqShowModal('✅', '<?php echo esc_js(__('Store Data Refreshed', 'commeriq')); ?>', '<?php echo esc_js(__('Store configuration has been updated from WooCommerce settings.', 'commeriq')); ?>', true);
                    } else {
                        commeriqShowModal('⚠️', '<?php echo esc_js(__('Refresh Failed', 'commeriq')); ?>', '<?php echo esc_js(__('Could not retrieve store values.', 'commeriq')); ?>', true);
                    }
                },
                error: function(){
                    $btn.prop('disabled', false).html(originalHtml);
                    commeriqShowModal('⚠️', '<?php echo esc_js(__('Connection Error', 'commeriq')); ?>', '<?php echo esc_js(__('Request failed.', 'commeriq')); ?>', true);
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
        
        // Toggle features section
        $('.commeriq-toggle-features').on('click', function() {
            const $btn = $(this);
            const $grid = $('.commeriq-features-grid');
            const $icon = $btn.find('.dashicons');
            
            if ($grid.is(':visible')) {
                $grid.slideUp(200);
                $icon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
                $btn.find('span:not(.dashicons)').text('<?php echo esc_js(__('Show Features', 'commeriq')); ?>');
            } else {
                $grid.slideDown(200);
                $icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
                $btn.find('span:not(.dashicons)').text('<?php echo esc_js(__('Hide Features', 'commeriq')); ?>');
            }
        });
    });
    
    // Modal helper functions
    window.commeriqShowModal = function(icon, title, message, showClose) {
        $('#commeriq-modal-icon').text(icon);
        $('#commeriq-modal-title').text(title);
        $('#commeriq-modal-message').text(message);
        $('#commeriq-modal-close').toggle(showClose);
        $('#commeriq-processing-modal').fadeIn(200);
    };
    
    window.commeriqShowConfirmModal = function(icon, title, message, onConfirm) {
        $('#commeriq-modal-icon').text(icon);
        $('#commeriq-modal-title').text(title);
        $('#commeriq-modal-message').text(message);
        
        // Show both confirm and cancel buttons
        $('#commeriq-modal-close').hide();
        
        // Remove any existing buttons
        $('.commeriq-modal-actions .commeriq-confirm-btn, .commeriq-modal-actions .commeriq-cancel-btn').remove();
        
        // Add confirm and cancel buttons
        var $confirmBtn = $('<button type="button" class="button button-primary commeriq-confirm-btn"><?php echo esc_js(__('Confirm', 'commeriq')); ?></button>');
        var $cancelBtn = $('<button type="button" class="button commeriq-cancel-btn"><?php echo esc_js(__('Cancel', 'commeriq')); ?></button>');
        
        $('.commeriq-modal-actions').append($cancelBtn).append($confirmBtn);
        
        // Handle confirm
        $confirmBtn.off('click').on('click', function() {
            $('#commeriq-processing-modal').fadeOut(200);
            $('.commeriq-confirm-btn, .commeriq-cancel-btn').remove();
            if (onConfirm) onConfirm();
        });
        
        // Handle cancel
        $cancelBtn.off('click').on('click', function() {
            $('#commeriq-processing-modal').fadeOut(200);
            $('.commeriq-confirm-btn, .commeriq-cancel-btn').remove();
        });
        
        $('#commeriq-processing-modal').fadeIn(200);
    };
    
    $('#commeriq-modal-close').on('click', function(){
        $('#commeriq-processing-modal').fadeOut(200);
    });
    
    window.commeriqShowModifyLicense = function() {
        $('.commeriq-license-active').fadeOut(200, function(){
            $('#commeriq-modify-license-form').fadeIn(200);
        });
    };
    
    window.commeriqCancelModify = function() {
        $('#commeriq-modify-license-form').fadeOut(200, function(){
            $('.commeriq-license-active').fadeIn(200);
        });
    };
    
    window.commeriqRemoveLicense = function() {
        // Show confirmation modal
        commeriqShowConfirmModal(
            '⚠️',
            '<?php echo esc_js(__('Remove License?', 'commeriq')); ?>',
            '<?php echo esc_js(__('Are you sure you want to remove your license? This will disable all CommerIQ features.', 'commeriq')); ?>',
            function() {
                // User confirmed, proceed with removal
                commeriqShowModal('⌛', '<?php echo esc_js(__('Processing Removal', 'commeriq')); ?>', '<?php echo esc_js(__('Removing your license...', 'commeriq')); ?>', false);
                commeriqExecuteRemoveLicense();
            }
        );
    };
    
    window.commeriqExecuteRemoveLicense = function() {
        
        $.ajax({
            url: commeriqAdmin.ajax_url,
            method: 'POST',
            data: {
                action: 'commeriq_remove_license',
                nonce: commeriqAdmin.license_nonce
            },
            success: function(response){
                if (response.success) {
                    commeriqShowModal('✅', '<?php echo esc_js(__('License Removed', 'commeriq')); ?>', response.data.message || '<?php echo esc_js(__('Your license has been successfully removed.', 'commeriq')); ?>', true);
                    setTimeout(function(){ location.reload(); }, 2000);
                } else {
                    commeriqShowModal('⚠️', '<?php echo esc_js(__('Removal Failed', 'commeriq')); ?>', response.data.message || '<?php echo esc_js(__('Failed to remove license.', 'commeriq')); ?>', true);
                }
            },
            error: function(){
                commeriqShowModal('⚠️', '<?php echo esc_js(__('Connection Error', 'commeriq')); ?>', '<?php echo esc_js(__('Failed to connect to the server. Please try again.', 'commeriq')); ?>', true);
            }
        });
    };
})(jQuery);
</script>
