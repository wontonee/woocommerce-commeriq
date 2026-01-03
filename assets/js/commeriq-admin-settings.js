(function($){
    $(document).ready(function(){
        // Terms of Service Modal
        $('#commeriq-show-terms').on('click', function(e){
            e.preventDefault();
            $('#commeriq-terms-modal').fadeIn(200);
        });
        
        $('#commeriq-terms-close, #commeriq-terms-close-btn, #commeriq-terms-modal .commeriq-modal-backdrop').on('click', function(){
            $('#commeriq-terms-modal').fadeOut(200);
        });
        
        // Privacy Policy Modal
        $('#commeriq-show-privacy').on('click', function(e){
            e.preventDefault();
            $('#commeriq-privacy-modal').fadeIn(200);
        });
        
        $('#commeriq-privacy-close, #commeriq-privacy-close-btn, #commeriq-privacy-modal .commeriq-modal-backdrop').on('click', function(){
            $('#commeriq-privacy-modal').fadeOut(200);
        });
        
        // Tab switching
        $('.nav-tab').on('click', function(e){
            e.preventDefault();
            const $tab = $(this);
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
        $('#commeriq-modify-api-btn').on('click', function(){
            $('#commeriq-modify-form').slideDown(200);
            $(this).prop('disabled', true);
        });
        
        // Cancel modify
        $('#commeriq-cancel-modify').on('click', function(){
            $('#commeriq-modify-form').slideUp(200);
            $('#commeriq-modify-api-btn').prop('disabled', false);
        });
        
        // Remove API credentials
        $('#commeriq-remove-license-btn').on('click', function(){
            commeriqShowConfirmModal(
                '⚠️',
                commeriqSettings.i18n.removeApi || 'Remove API Credentials?',
                commeriqSettings.i18n.removeApiMessage || 'Are you sure you want to remove your API credentials? You will need to re-enter them to use AI features.',
                function() {
                    // User confirmed, proceed with removal
                    commeriqShowModal('⌛', commeriqSettings.i18n.processing, commeriqSettings.i18n.removingLicense, false);
                    
                    $.ajax({
                        url: commeriqAdmin.ajax_url,
                        method: 'POST',
                        data: {
                            action: 'commeriq_remove_license',
                            nonce: commeriqAdmin.license_nonce
                        },
                        success: function(response){
                            if (response.success) {
                                commeriqShowModal('✅', commeriqSettings.i18n.success, commeriqSettings.i18n.apiRemovedSuccess || 'API credentials removed successfully', true);
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                commeriqShowModal('❌', commeriqSettings.i18n.error, response.data || commeriqSettings.i18n.apiRemoveFailed || 'Failed to remove credentials', true);
                            }
                        },
                        error: function(){
                            commeriqShowModal('⚠️', commeriqSettings.i18n.error, commeriqSettings.i18n.connectionFailed, true);
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
                commeriqShowModal('⚠️', commeriqSettings.i18n.validationError, commeriqSettings.i18n.fillAllFields, true);
                return;
            }
            
            $submitBtn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation: rotation 2s infinite linear;"></span> ' + commeriqSettings.i18n.processing + '...');
            commeriqShowModal('⌛', commeriqSettings.i18n.processing, commeriqSettings.i18n.savingApi || 'Saving API credentials...', false);
            
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
                        commeriqShowModal('✅', commeriqSettings.i18n.success + '!', response.data.message || commeriqSettings.i18n.apiSavedSuccess || 'API credentials saved successfully', true);
                        setTimeout(function(){ location.reload(); }, 2000);
                    } else {
                        commeriqShowModal('⚠️', commeriqSettings.i18n.saveFailed || 'Save Failed', response.data.message || commeriqSettings.i18n.apiSaveFailed || 'Failed to save credentials', true);
                        $submitBtn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function(xhr){
                    let errorMessage = commeriqSettings.i18n.connectionFailed;
                    
                    if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                        errorMessage = xhr.responseJSON.data.message;
                    }
                    
                    commeriqShowModal('⚠️', commeriqSettings.i18n.error, errorMessage, true);
                    $submitBtn.prop('disabled', false).html(originalHtml);
                }
            });
        });
        
        // Refresh store data
        $('#commeriq-refresh-store').on('click', function(e){
            e.preventDefault();
            const $btn = $(this);
            const originalHtml = $btn.html();
            
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="margin-top: 3px; animation: rotation 2s infinite linear;"></span>' + commeriqSettings.i18n.refreshing + '...');
            
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
                        commeriqShowModal('✅', commeriqSettings.i18n.success, commeriqSettings.i18n.storeDataRefreshed, true);
                        setTimeout(function(){ location.reload(); }, 1500);
                    } else {
                        commeriqShowModal('⚠️', commeriqSettings.i18n.error, commeriqSettings.i18n.refreshDataFailed, true);
                    }
                },
                error: function(){
                    $btn.prop('disabled', false).html(originalHtml);
                    commeriqShowModal('⚠️', commeriqSettings.i18n.error, commeriqSettings.i18n.connectionFailed, true);
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
