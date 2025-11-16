<?php
defined('ABSPATH') || exit;
// Read saved license if present
$opts = get_option('commeriq_license', ['licence_key' => '', 'domain_name' => '', 'activated_at' => '']);
$licence_key = isset($opts['licence_key']) ? esc_attr($opts['licence_key']) : '';
$domain_name = isset($opts['domain_name']) ? esc_attr($opts['domain_name']) : '';
$activated_at = isset($opts['activated_at']) ? $opts['activated_at'] : '';

// If nothing stored, offer "demo" as a placeholder but do not pre-fill the inputs.
$show_demo_placeholder = empty($licence_key) && empty($domain_name) && empty($activated_at);
$placeholder_licence = $show_demo_placeholder ? 'demo' : '';
$placeholder_domain = $show_demo_placeholder ? 'demo' : '';

$is_active = !empty($activated_at);

// If active on the server, emit a small inline script immediately so the client
// can restore the previously active tab before other scripts run (prevents flicker on reload).
// Important: do NOT overwrite an existing `localStorage` value — prefer the user's stored selection.
if ( $is_active ) {
    $server_last_tab = esc_js( get_option( 'commeriq_last_active_tab', 'tab-licence' ) );
    ?>
    <script>
        (function(){
            try {
                var hasHash = (window.location.hash || '').length > 1;
                var stored = null;
                try { stored = localStorage.getItem('commeriq_active_tab'); } catch(e) { stored = null; }
                // Only set the URL hash from the server when there is no hash and no stored tab.
                if (!hasHash && !stored) {
                    try { history.replaceState(null, '', '#<?php echo $server_last_tab; ?>'); } catch(e) { window.location.hash = '#<?php echo $server_last_tab; ?>'; }
                }
            } catch(e) { /* ignore any early-restore errors */ }
        })();
    </script>
    <?php
}
?>
<div class="wrap">
    <style>
        /* Hide all tab contents until JS activates the proper one to avoid flicker/redirect on refresh */
        .commeriq-tab { display: none; }
    </style>
    <div id="commeriq-license-active" class="notice notice-success commeriq-license-active" style="display:none; padding:10px; margin-bottom:10px;">
        <span style="font-size:1.25em; margin-right:8px;">✅</span>
        <strong><?php esc_html_e('License Active', 'commeriq'); ?></strong>
    </div>
    
    <h1><?php esc_html_e('CommerIQ', 'commeriq'); ?></h1>
    <p class="description"><?php esc_html_e('Smart AI for Product Description, Image Create & Product Comparison', 'commeriq'); ?></p>

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab" href="#tab-licence"><?php esc_html_e('Licence', 'commeriq'); ?></a>
        <?php $store_tab_class = $is_active ? 'nav-tab' : 'nav-tab nav-tab-disabled'; ?>
        <a class="<?php echo $store_tab_class; ?>" href="#tab-store" data-disabled="<?php echo $is_active ? '0' : '1'; ?>"><?php esc_html_e('Store Analyzer', 'commeriq'); ?></a>
    </h2>

    <div id="tab-licence" class="commeriq-tab">
        <div id="commeriq-license-form">
            <?php // Ajax-only UI: do not use settings_fields() to avoid options.php POST and refresh resubmission ?>

            <div class="commeriq-license-box">
                <div class="commeriq-license-table" style="">
                    <div>
                        <div class="commeriq-box-header">
                            <?php if ( $is_active ) : ?>
                                <h2 class="commeriq-box-title"><span style="font-size:1.1em; margin-right:8px;">✅</span><?php echo esc_html__('License Active', 'commeriq'); ?></h2>
                                <p class="commeriq-box-subtext"><?php esc_html_e('Your license is active. AI features are available.', 'commeriq'); ?></p>
                            <?php else: ?>
                                <h2 class="commeriq-box-title"><span style="font-size:1.1em; margin-right:8px;">🔒</span><?php echo esc_html__('Activate Your License', 'commeriq'); ?></h2>
                                <p class="commeriq-box-subtext"><?php esc_html_e('Please activate your CommerIQ license to access AI features.', 'commeriq'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="display:flex; gap:20px; margin-top:16px;">
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:8px; color:#717171;"><?php esc_html_e('License Key', 'commeriq'); ?></label>
                            <input type="text" id="licence_key" name="<?php echo \CommerIQ\Admin\SettingsPage::OPTION_KEY; ?>[licence_key]" value="<?php echo $licence_key; ?>" placeholder="<?php echo esc_attr( $placeholder_licence ); ?>" class="regular-text" style="width:100%; box-sizing:border-box; height:46px; background:#f5f5f5;" <?php echo $is_active ? 'readonly' : ''; ?> />
                        </div>
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:8px; color:#717171;"><?php esc_html_e('Domain', 'commeriq'); ?></label>
                            <input type="text" id="domain_name" name="<?php echo \CommerIQ\Admin\SettingsPage::OPTION_KEY; ?>[domain_name]" value="<?php echo $domain_name; ?>" placeholder="<?php echo esc_attr( $placeholder_domain ); ?>" class="regular-text" style="width:100%; box-sizing:border-box; height:46px; background:#f5f5f5;" <?php echo $is_active ? 'readonly' : ''; ?> />
                        </div>
                    </div>

                    <div class="commeriq-insert-after" style="height:0; margin-top:20px; padding:0;"></div>

                    <?php if ( $is_active ) : ?>
                        <div id="commeriq-activated-row" style="margin-bottom:20px;">
                            <label style="display:block; margin-bottom:8px; color:#717171;"><?php esc_html_e('Activated Date', 'commeriq'); ?></label>
                            <input type="text" id="commeriq-activated-date" readonly class="regular-text" style="width:100%; box-sizing:border-box; height:46px; background:#f5f5f5;" value="<?php echo esc_attr( date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime( $activated_at ) ) ); ?>" />
                        </div>
                    <?php endif; ?>

                    <div style="text-align:right; margin-top:20px">
                        <button type="button" class="button button-primary" id="commeriq-activate-license" <?php echo $is_active ? 'style="display:none;"' : ''; ?>><?php esc_html_e('Activate License', 'commeriq'); ?></button>
                        <button type="button" class="button commeriq-modify-license" id="commeriq-modify-license" style="margin-right:8px; <?php echo $is_active ? '' : 'display:none;'; ?>"><?php esc_html_e('Modify License', 'commeriq'); ?></button>
                        <button type="button" class="button button-secondary" id="commeriq-remove-license" style="background:#fff;border-color:#d9534f;color:#d9534f; <?php echo $is_active ? '' : 'display:none;'; ?>"><?php esc_html_e('Remove License', 'commeriq'); ?></button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="tab-store" class="commeriq-tab" style="display:none; margin-top:20px;">
       
        <?php
        // Retrieve values from WooCommerce settings automatically
        $woocommerce_values = ['country' => '', 'currency' => '', 'state' => '', 'address_1' => '', 'address_2' => ''];
        if (class_exists('CommerIQ\\Helpers\\Utils')) {
            $woocommerce_values = \CommerIQ\Helpers\Utils::derive_from_woocommerce();
        }

        // If derive_from_woocommerce returned empty values, fall back to the stored option
        if (empty($woocommerce_values['country']) && empty($woocommerce_values['currency']) && empty($woocommerce_values['state'])) {
            $stored = get_option('commeriq_store_config', []);
            if (!empty($stored) && ( !empty($stored['country']) || !empty($stored['currency']) || !empty($stored['state']) )) {
                $woocommerce_values['country'] = isset($stored['country']) ? $stored['country'] : '';
                $woocommerce_values['currency'] = isset($stored['currency']) ? $stored['currency'] : '';
                $woocommerce_values['state'] = isset($stored['state']) ? $stored['state'] : '';
                $woocommerce_values['address_1'] = isset($stored['address_1']) ? $stored['address_1'] : '';
                $woocommerce_values['address_2'] = isset($stored['address_2']) ? $stored['address_2'] : '';
            }
        }

        // Provide a Refresh button to re-query via AJAX
        ?>
        <div class="store-analyzer-box">
            <h3 class="store-analyzer-title"><?php esc_html_e('Store Analyzer', 'commeriq'); ?></h3>
            <p class="description"><?php esc_html_e('Configuration automatically retrieved from WooCommerce settings.', 'commeriq'); ?></p>
            <p style="margin-top:8px;"><button id="commeriq-refresh-store" class="button"><?php esc_html_e('Refresh from WooCommerce', 'commeriq'); ?></button>
            <span style="margin-left:8px;color:#666;font-size:12px;">(Uses stored values if WooCommerce is unavailable)</span></p>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Country', 'commeriq'); ?></th>
                    <td><strong id="commeriq-country-display"><?php echo esc_html($woocommerce_values['country'] ?: 'Not Set'); ?></strong></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Currency', 'commeriq'); ?></th>
                    <td><strong id="commeriq-currency-display"><?php echo esc_html($woocommerce_values['currency'] ?: 'Not Set'); ?></strong></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('State/Region', 'commeriq'); ?></th>
                    <td><strong id="commeriq-state-display"><?php echo esc_html($woocommerce_values['state'] ?: 'Not Set'); ?></strong></td>
                </tr>
                <?php if (!empty($woocommerce_values['address_1']) || !empty($woocommerce_values['address_2'])): ?>
                <tr>
                    <th><?php esc_html_e('Store Address', 'commeriq'); ?></th>
                    <td>
                        <strong id="commeriq-address-display">
                            <?php 
                            $address_parts = array_filter([
                                $woocommerce_values['address_1'],
                                $woocommerce_values['address_2']
                            ]);
                            echo esc_html(implode(', ', $address_parts));
                            ?>
                        </strong>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
            <script>
            (function(){
                document.addEventListener('click', function(e){
                    var t = e.target;
                    if (t && t.id === 'commeriq-refresh-store') {
                        e.preventDefault();
                        t.disabled = true; t.innerText = 'Refreshing...';
                        var data = { action: 'commeriq_retrieve_store', _nonce: commeriqAdmin.retrieve_nonce };
                        fetch(commeriqAdmin.ajax_url, { method: 'POST', credentials: 'same-origin', body: new URLSearchParams(data) })
                            .then(function(r){ return r.json(); })
                            .then(function(resp){
                                t.disabled = false; t.innerText = 'Refresh from WooCommerce';
                                if (resp && resp.success && resp.data) {
                                    document.getElementById('commeriq-country-display').innerText = resp.data.country || 'Not Set';
                                    document.getElementById('commeriq-currency-display').innerText = resp.data.currency || 'Not Set';
                                    document.getElementById('commeriq-state-display').innerText = resp.data.state || 'Not Set';
                                } else {
                                    alert('Could not retrieve store values.');
                                }
                            }).catch(function(){ t.disabled = false; t.innerText = 'Refresh from WooCommerce'; alert('Request failed'); });
                    }
                });
            })();
            </script>
            <p class="description" style="margin-top: 15px;">
                <?php esc_html_e('To change these values, please update your WooCommerce settings at', 'commeriq'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings')); ?>"><?php esc_html_e('WooCommerce → Settings', 'commeriq'); ?></a>
            </p>
        </div>
    </div>
</div>

<script>
    (function(){
        const tabs = document.querySelectorAll('.nav-tab');
        // Server-side flags to decide tab restoration behavior
        var commeriq_server_active = <?php echo $is_active ? 'true' : 'false'; ?>;
        var commeriq_server_last_tab = '<?php echo esc_js( get_option( 'commeriq_last_active_tab', 'tab-licence' ) ); ?>';

        function activateTabById(id) {
            if (!id) return;
            const selector = '.nav-tab[href="#' + id + '"]';
            const tab = document.querySelector(selector);
            if (!tab) return;
            tabs.forEach(function(x){ x.classList.remove('nav-tab-active'); });
            tab.classList.add('nav-tab-active');
            document.querySelectorAll('.commeriq-tab').forEach(function(c){ c.style.display = 'none'; });
            const el = document.getElementById(id);
            if (el) el.style.display = 'block';
        }

        tabs.forEach(function(t){
            t.addEventListener('click', function(e){
                // Prevent interaction on disabled tabs
                if (t.classList.contains('nav-tab-disabled') || t.getAttribute('data-disabled') === '1') {
                    e.preventDefault();
                    // Provide a subtle notice encouraging activation
                    alert('<?php echo esc_js( __( "Please activate your licence to access the Store Analyzer.", 'commeriq' ) ); ?>');
                    return;
                }
                e.preventDefault();
                const id = t.getAttribute('href').substring(1);
                activateTabById(id);
                // persist in URL hash (no history entry) and localStorage as a fallback
                try { history.replaceState(null, '', '#' + id); } catch(e) { window.location.hash = '#' + id; }
                try { localStorage.setItem('commeriq_active_tab', id); } catch(e) { /* ignore */ }
            });
        });

        // Restore tab selection policy:
        // - If server reports license is NOT active, always show 'tab-licence' (Activate License)
        // - If server reports license IS active, prefer URL hash, then localStorage, then server_last_tab, then 'tab-licence'
        var initialHash = (window.location.hash || '').replace('#','');
        if (!commeriq_server_active) {
            // Ensure we land on licence tab and clear any stored tab
            try { localStorage.removeItem('commeriq_active_tab'); } catch(e) {}
            try { history.replaceState(null, '', '#tab-licence'); } catch(e) { window.location.hash = '#tab-licence'; }
            activateTabById('tab-licence');
        } else {
            if (initialHash) {
                activateTabById(initialHash);
            } else {
                try {
                    var stored = localStorage.getItem('commeriq_active_tab') || '';
                    if (stored) {
                        activateTabById(stored);
                    } else if (commeriq_server_last_tab) {
                        activateTabById(commeriq_server_last_tab);
                    } else {
                        activateTabById('tab-licence');
                    }
                } catch(e) {
                    activateTabById('tab-licence');
                }
            }
        }
    })();
</script>
    
    <!-- Activation / Processing Modal -->
    <div id="commeriq-modal" style="display:none; position:fixed; left:0; top:0; right:0; bottom:0; background:rgba(0,0,0,0.4); z-index:9999;">
        <div style="max-width:520px; margin:80px auto; background:#fff; padding:20px; border-radius:6px; box-shadow:0 6px 18px rgba(0,0,0,0.2);">
            <div style="display:flex; align-items:center; gap:12px;">
                <div id="commeriq-modal-icon" style="font-size:28px;">⌛</div>
                <div style="flex:1;">
                    <div id="commeriq-modal-title" style="font-weight:600; font-size:18px;">Processing</div>
                    <div id="commeriq-modal-message" style="color:#666; margin-top:6px;">Please wait…</div>
                </div>
                <div>
                    <button id="commeriq-modal-close" class="button" style="display:none;">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    (function(){
        try {
            // If multiple elements with the same ID exist (unexpected), remove all but the last one
            var mods = document.querySelectorAll('#commeriq-modal');
            if (mods && mods.length > 1) {
                for (var i = 0; i < mods.length - 1; i++) {
                    mods[i].parentNode.removeChild(mods[i]);
                }
            }

            // Ensure the Close button hides any remaining modal (robust against duplicates)
            document.addEventListener('click', function(e){
                if (e.target && e.target.id === 'commeriq-modal-close') {
                    var all = document.querySelectorAll('#commeriq-modal');
                    all.forEach(function(m){ m.style.display = 'none'; });
                }
            });
        } catch (err) { /* ignore */ }
    })();
    </script>
