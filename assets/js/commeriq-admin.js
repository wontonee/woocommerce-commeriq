(function(window, $){
    // Reusable modal notification function
    function showNotification(message, type) {
        type = type || 'info';
        var iconClass = type === 'success' ? 'dashicons-yes-alt' : 
                       type === 'error' ? 'dashicons-dismiss' : 
                       'dashicons-info';
        var iconColor = type === 'success' ? '#46b450' : 
                       type === 'error' ? '#dc3232' : 
                       '#2271b1';
        
        var modalHtml = '<div id="commeriq-notification-modal" class="commeriq-modal" style="display:block;">' +
            '<div class="commeriq-modal-overlay"></div>' +
            '<div class="commeriq-modal-content" style="max-width: 400px;">' +
                '<div class="commeriq-modal-body" style="text-align:center; padding:24px;">' +
                    '<span class="dashicons ' + iconClass + '" style="font-size:48px; width:48px; height:48px; color:' + iconColor + ';"></span>' +
                    '<p style="margin:16px 0 20px; font-size:15px;">' + message + '</p>' +
                    '<button type="button" class="button button-primary" id="commeriq-notification-ok">OK</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        $('body').append(modalHtml);
        
        // Close on button click
        $('#commeriq-notification-ok, #commeriq-notification-modal .commeriq-modal-overlay').on('click', function() {
            $('#commeriq-notification-modal').fadeOut(200, function() {
                $(this).remove();
            });
        });
        
        // Close on Escape key
        $(document).on('keydown.notification', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                $('#commeriq-notification-modal').fadeOut(200, function() {
                    $(this).remove();
                });
                $(document).off('keydown.notification');
            }
        });
    }

    $(document).ready(function(){
        // Price Comparison Handler
        $(document).on('click', '#commeriq-run-comparison', function(e){
            e.preventDefault();
            var $btn = $(this);
            
            // Prevent double clicks
            if ($btn.data('processing')) { return; }
            $btn.data('processing', true);
            $btn.prop('disabled', true);

            var postId = $btn.data('post-id') || $('#post_ID').val() || 0;
            
            // Show modal
            $('#commeriq-comparison-modal').fadeIn(200);
            $('#commeriq-comparison-loading').show();
            $('#commeriq-comparison-results').hide().html('');

            var data = {
                action: 'commeriq_run_comparison',
                _nonce: commeriqAdmin.nonce,
                post_id: postId
            };

            $.post(commeriqAdmin.ajax_url, data, function(resp){
                $btn.data('processing', false);
                $btn.prop('disabled', false);
                
                if (resp && resp.success && resp.data) {
                    displayComparisonResults(resp.data);
                } else {
                    var errorMsg = 'Unknown error';
                    if (resp && resp.data && resp.data.message) {
                        errorMsg = resp.data.message;
                    }
                    $('#commeriq-comparison-loading').hide();
                    $('#commeriq-comparison-results').html(
                        '<div class="notice notice-error"><p>' + errorMsg + '</p></div>'
                    ).show();
                }
            }).fail(function(){
                $btn.data('processing', false);
                $btn.prop('disabled', false);
                $('#commeriq-comparison-loading').hide();
                $('#commeriq-comparison-results').html(
                    '<div class="notice notice-error"><p>Request failed. Please try again.</p></div>'
                ).show();
            });
        });

        // Display comparison results
        function displayComparisonResults(data) {
            $('#commeriq-comparison-loading').hide();
            
            var html = '';
            
            // Competitor Results
            if (data.results && data.results.length > 0) {
                html += '<div class="commeriq-competitors">';
                html += '<h3 style="margin-top:0;">Competitor Prices</h3>';
                html += '<table class="widefat" style="margin-bottom: 20px;">';
                html += '<thead><tr>';
                html += '<th>Platform</th><th>Product Name</th><th>Price</th><th>Rating</th><th>Link</th>';
                html += '</tr></thead><tbody>';
                
                data.results.forEach(function(item){
                    html += '<tr>';
                    html += '<td><strong>' + escapeHtml(item.platform) + '</strong></td>';
                    html += '<td>' + escapeHtml(item.name) + '</td>';
                    html += '<td><strong>' + escapeHtml(item.currency) + ' ' + item.price + '</strong></td>';
                    html += '<td>' + (item.rating ? item.rating + ' ⭐' : 'N/A') + '</td>';
                    html += '<td><a href="' + escapeHtml(item.link) + '" target="_blank" class="button button-small">View</a></td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
            }
            
            // Suggestion Section
            if (data.suggestion) {
                var sug = data.suggestion;
                html += '<div class="commeriq-suggestion">';
                html += '<h3>Price Recommendations</h3>';
                
                // Current recommendation
                html += '<div class="notice notice-info" style="padding: 12px; margin-bottom: 15px;">';
                html += '<h4 style="margin:0 0 8px 0;">Recommended Strategy: ' + escapeHtml(sug.strategy) + '</h4>';
                html += '<p style="margin:0;"><strong>Recommended Price: ' + escapeHtml(sug.currency) + ' ' + sug.recommended_price + '</strong></p>';
                html += '<p style="margin:8px 0 0 0;">' + escapeHtml(sug.rationale) + '</p>';
                html += '</div>';
                
                // Market Stats
                if (sug.market) {
                    html += '<div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-bottom: 15px;">';
                    html += '<h4 style="margin:0 0 8px 0;">Market Analysis</h4>';
                    html += '<p style="margin:0;"><strong>Competitors:</strong> ' + sug.market.competitors + ' | ';
                    html += '<strong>Min:</strong> ' + sug.market.min + ' | ';
                    html += '<strong>Median:</strong> ' + sug.market.median + ' | ';
                    html += '<strong>Avg:</strong> ' + sug.market.avg + ' | ';
                    html += '<strong>Max:</strong> ' + sug.market.max + '</p>';
                    html += '</div>';
                }
                
                // Price Options
                if (sug.suggestions) {
                    html += '<h4>Price Strategy Options</h4>';
                    html += '<div class="commeriq-price-options" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 15px;">';
                    
                    ['aggressive', 'balanced', 'conservative'].forEach(function(strategy){
                        if (sug.suggestions[strategy]) {
                            var opt = sug.suggestions[strategy];
                            var strategyClass = 'strategy-' + strategy;
                            html += '<div class="commeriq-price-card ' + strategyClass + '" style="border: 2px solid #ddd; padding: 12px; border-radius: 4px; cursor: pointer;" data-price="' + opt.price + '">';
                            html += '<h5 style="margin:0 0 8px 0; text-transform: capitalize;">' + strategy + '</h5>';
                            html += '<div style="font-size: 20px; font-weight: bold; color: #2271b1; margin-bottom: 8px;">' + escapeHtml(opt.currency) + ' ' + opt.price + '</div>';
                            html += '<p style="margin:0; font-size: 12px;">' + escapeHtml(opt.strategy) + '</p>';
                            html += '<div style="margin-top: 8px; font-size: 11px; color: #666;">';
                            html += '<div>vs Min: ' + (opt.delta_vs_min.percent >= 0 ? '+' : '') + opt.delta_vs_min.percent + '%</div>';
                            html += '<div>vs Median: ' + (opt.delta_vs_median.percent >= 0 ? '+' : '') + opt.delta_vs_median.percent + '%</div>';
                            html += '</div>';
                            html += '<button type="button" class="button button-primary button-small commeriq-apply-price" style="margin-top: 10px; width: 100%;" data-price="' + opt.price + '">Apply Price</button>';
                            html += '</div>';
                        }
                    });
                    
                    html += '</div>';
                }
                
                html += '</div>';
            }
            
            $('#commeriq-comparison-results').html(html).show();
        }
        
        // Apply selected price
        $(document).on('click', '.commeriq-apply-price', function(e){
            e.preventDefault();
            var price = $(this).data('price');
            if (price) {
                $('#_regular_price').val(price).trigger('change');
                $('#commeriq-comparison-modal').fadeOut(200);
                
                // Show success notice
                if ($('.commeriq-price-applied-notice').length === 0) {
                    $('<div class="notice notice-success is-dismissible commeriq-price-applied-notice"><p>Price updated to ' + price + '. Don\'t forget to save the product.</p></div>')
                        .insertAfter('.wp-header-end');
                    setTimeout(function(){ $('.commeriq-price-applied-notice').fadeOut(); }, 5000);
                }
            }
        });
        
        // Close modal
        $(document).on('click', '.commeriq-modal-close, .commeriq-modal-overlay', function(e){
            e.preventDefault();
            $('#commeriq-comparison-modal').fadeOut(200);
        });
        
        // Helper function
        function escapeHtml(text) {
            if (!text) return '';
            return String(text).replace(/[&<>"']/g, function(s) {
                return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s];
            });
        }

        // AI Content Generation modal control function
        function showAIModal(type, message, callback) {
            var $modal = $('#commeriq-ai-modal');
            var $confirmBtn = $('#commeriq-ai-modal-confirm');
            var $cancelBtn = $('#commeriq-ai-modal-cancel');
            var $okBtn = $('#commeriq-ai-modal-ok');
            
            // Set icon based on type
            var icon = '🤖';
            if (type === 'error') icon = '⚠️';
            if (type === 'success') icon = '✅';
            
            $('#commeriq-ai-modal-icon').text(icon);
            $('#commeriq-ai-modal-message').text(message);
            
            // Show appropriate buttons
            if (type === 'confirm') {
                $confirmBtn.show();
                $cancelBtn.show();
                $okBtn.hide();
            } else {
                $confirmBtn.hide();
                $cancelBtn.hide();
                $okBtn.show();
            }
            
            // Show modal
            $modal.fadeIn(200);
            
            // Handle button clicks
            $confirmBtn.off('click').on('click', function() {
                $modal.fadeOut(200);
                if (callback) callback(true);
            });
            
            $cancelBtn.off('click').on('click', function() {
                $modal.fadeOut(200);
                if (callback) callback(false);
            });
            
            $okBtn.off('click').on('click', function() {
                $modal.fadeOut(200);
                if (callback) callback();
            });
        }
        
        // Close modal on X button or overlay click
        $(document).on('click', '#commeriq-ai-modal .commeriq-modal-close, #commeriq-ai-modal .commeriq-modal-overlay', function() {
            $('#commeriq-ai-modal').fadeOut(200);
        });

        // Close AI Image modal on X button or overlay click
        $(document).on('click', '#commeriq-ai-image-modal .commeriq-modal-close, #commeriq-ai-image-modal .commeriq-modal-overlay', function() {
            $('#commeriq-ai-image-modal').fadeOut(200);
        });

        // AI Content Generation Handler
        $(document).on('click', '.commeriq-ai-content', function(e){
            e.preventDefault();
            var $btn = $(this);
            
            // Prevent double clicks
            if ($btn.data('processing')) { return; }
            
            var postId = $('#post_ID').val() || 0;
            var title = $('#title').val() || '';
            var actionType = $btn.data('action-type') || 'long'; // Get action type from button data attribute
            var editorId = $btn.data('editor-id') || 'content';
            
            if (!title.trim()) {
                showAIModal('error', 'Please enter a product title first.', function() {
                    $('#title').focus();
                });
                return;
            }
            
            var descriptionType = (actionType === 'short') ? 'short description' : 'description';
            showAIModal('confirm', 'Generate AI-powered product ' + descriptionType + ' for "' + title + '"? This will replace the current ' + descriptionType + '.', function(confirmed) {
                if (!confirmed) return;
                
                $btn.data('processing', true);
                $btn.prop('disabled', true);
                var originalHtml = $btn.html();
                $btn.html('<span class="spinner is-active" style="float:none;margin:0 5px 0 0;"></span>Generating...');

                var data = {
                    action: 'commeriq_generate_ai_content',
                    _nonce: commeriqAdmin.nonce,
                    post_id: postId,
                    action_type: actionType
                };

                $.post(commeriqAdmin.ajax_url, data, function(resp){
                    console.log('AI Content Response:', resp);
                    $btn.data('processing', false);
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                    
                    if (resp && resp.success && resp.data && resp.data.description) {
                        // Insert AI content into the appropriate editor
                        var description = resp.data.description;
                        
                        // Clean up the description - remove markdown code block markers if present
                        description = description.replace(/^```html\s*\n?/i, '').replace(/\n?```\s*$/i, '').trim();
                        
                        // Determine which editor to update based on action type
                        if (actionType === 'short') {
                            // Insert into short description (excerpt) editor
                            if (typeof tinyMCE !== 'undefined' && tinyMCE.get('excerpt')) {
                                // TinyMCE editor for excerpt
                                tinyMCE.get('excerpt').setContent(description);
                            } else if ($('#excerpt').length) {
                                // Fallback to textarea
                                $('#excerpt').val(description);
                            }
                        } else {
                            // Insert into long description (content) editor
                            if (typeof wp !== 'undefined' && wp.data && wp.data.select('core/editor')) {
                                // Block Editor (Gutenberg)
                                var blocks = wp.blocks.parse(description);
                                wp.data.dispatch('core/editor').resetBlocks(blocks);
                            } else if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content')) {
                                // Classic Editor (TinyMCE)
                                tinyMCE.get('content').setContent(description);
                            } else if ($('#content').length) {
                                // Fallback to textarea
                                $('#content').val(description);
                            }
                        }
                        
                        // Show success notice
                        var successType = (actionType === 'short') ? 'short description' : 'description';
                        if ($('.commeriq-ai-success-notice').length === 0) {
                            $('<div class="notice notice-success is-dismissible commeriq-ai-success-notice"><p><strong>AI Content Generated!</strong> The product ' + successType + ' has been updated. Review and save the product.</p></div>')
                                .insertAfter('.wp-header-end');
                            setTimeout(function(){ $('.commeriq-ai-success-notice').fadeOut(); }, 7000);
                        }
                    } else {
                        var errorMsg = 'Failed to generate content';
                        if (resp && resp.data && resp.data.message) {
                            errorMsg = resp.data.message;
                        }
                        showAIModal('error', 'Error: ' + errorMsg);
                    }
                }).fail(function(jqXHR, textStatus, errorThrown){
                    console.error('AI Content Error:', {
                        status: jqXHR.status,
                        statusText: jqXHR.statusText,
                        responseText: jqXHR.responseText,
                        textStatus: textStatus,
                        errorThrown: errorThrown
                    });
                    $btn.data('processing', false);
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                    
                    var errorMsg = 'Request failed. Please check your connection and try again.';
                    if (jqXHR.status === 403) {
                        errorMsg = 'Permission denied. Please refresh the page and try again.';
                    } else if (jqXHR.status === 400 || jqXHR.status === 404) {
                        try {
                            var resp = JSON.parse(jqXHR.responseText);
                            if (resp && resp.data && resp.data.message) {
                                errorMsg = resp.data.message;
                            }
                        } catch(e) {}
                    }
                    showAIModal('error', errorMsg);
                });
            });
        });

        // Activate License button: delegated handler to work if the button is re-rendered.
        $(document).on('click', '#commeriq-activate-license', function(e){
            e.preventDefault();
            var $btn = $(this);
            // Prevent double-clicks while a request is in flight
            if ($btn.data('processing')) { return; }
            $btn.data('processing', true);

            var licence = String($('#licence_key').val() || '').trim();
            var domain = String($('#domain_name').val() || '').trim();

            // Show processing modal
            $('#commeriq-modal-icon').text('⌛');
            $('#commeriq-modal-title').text('Processing Activation');
            $('#commeriq-modal-message').text('Processing Activation...');
            $('#commeriq-modal-close').hide();
            $('#commeriq-modal').show();

            // Simple button loader feedback
            var originalText = $btn.text();
            $btn.prop('disabled', true).text('Activating…');

            var postData = {
                action: 'commeriq_activate_license',
                // include both common parameter names to be compatible with server handlers
                nonce: (commeriqAdmin && commeriqAdmin.license_nonce) ? commeriqAdmin.license_nonce : undefined,
                _nonce: (commeriqAdmin && commeriqAdmin.license_nonce) ? commeriqAdmin.license_nonce : undefined,
                license: { licence_key: licence, domain_name: domain }
            };

            $.post(commeriqAdmin.ajax_url, postData).done(function(resp){
                if (resp && resp.success) {
                    // update modal to success
                    $('#commeriq-modal-icon').text('✅');
                    $('#commeriq-modal-title').text('License Activated');
                    $('#commeriq-modal-message').text('Your license has been activated.');
                    $('#commeriq-modal-close').show();

                    // show admin notice and enable Store tab
                    $('#commeriq-license-active').show();
                    // Update title in the license box
                    $('.commeriq-box-title').text('License Active');

                    // Fill inputs, make them readonly
                    if (resp.data && resp.data.licence_key) {
                        $('#licence_key').val(resp.data.licence_key).prop('readonly', true);
                    }
                    if (resp.data && resp.data.domain_name) {
                        $('#domain_name').val(resp.data.domain_name).prop('readonly', true);
                    }

                    // If the activated date input exists update it, otherwise insert it after the HR element
                    var activatedFormatted = (resp.data && resp.data.activated_at_formatted) ? resp.data.activated_at_formatted : '';
                    if ($('#commeriq-activated-row').length) {
                        $('#commeriq-activated-date').val(activatedFormatted);
                    } else if (activatedFormatted) {
                        var row = '<div id="commeriq-activated-row" style="margin-bottom:12px;"><label style="display:block; margin-bottom:8px; color:#717171;">Activated Date</label>' +
                                  '<input type="text" id="commeriq-activated-date" readonly class="regular-text" style="width:100%; box-sizing:border-box; height:46px; background:#f5f5f5;" value="' + activatedFormatted + '" /></div>';
                        // insert after the placeholder inside the license box
                        $('.commeriq-license-box .commeriq-insert-after').first().after(row);
                    }

                    // Hide Activate button and show existing Modify/Remove buttons
                    $('#commeriq-activate-license').hide();
                    $('#commeriq-modify-license').show();
                    $('#commeriq-remove-license').show();

                    // enable Store tab (but show Licence tab so admin sees License Active screen)
                    var $storeTab = $('.nav-tab[href="#tab-store"]');
                    $storeTab.removeClass('nav-tab-disabled');
                    try {
                        $('.nav-tab').removeClass('nav-tab-active');
                        $('.nav-tab[href="#tab-licence"]').addClass('nav-tab-active');
                        $('.commeriq-tab').hide();
                        $('#tab-licence').show();
                    } catch (e) { /* ignore */ }
                    try { localStorage.setItem('commeriq_active_tab', 'tab-licence'); } catch(e) {}
                    try { history.replaceState(null, '', '#tab-licence'); } catch(e) { window.location.hash = '#tab-licence'; }
                    setTimeout(function(){ window.location.href = window.location.pathname + window.location.search + '#tab-licence'; }, 250);
                    // restore button state
                    $btn.data('processing', false).prop('disabled', false).text(originalText);
                } else {
                    $('#commeriq-modal-icon').text('⚠️');
                    $('#commeriq-modal-title').text('Activation Failed');
                    var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Activation failed';
                    $('#commeriq-modal-message').text(msg);
                    $('#commeriq-modal-close').show();
                    $btn.data('processing', false).prop('disabled', false).text(originalText);
                }
            }).fail(function(){
                $('#commeriq-modal-icon').text('⚠️');
                $('#commeriq-modal-title').text('Activation Failed');
                $('#commeriq-modal-message').text('Request failed.');
                $('#commeriq-modal-close').show();
                $btn.data('processing', false).prop('disabled', false).text(originalText);
            });
        });

        // Note: Remove License button omitted by design—removal is not exposed on this page.

        // Handle Modify (toggle edit/save) and Remove actions via delegated events
        $(document).on('click', '#commeriq-modify-license', function(e){
            e.preventDefault();
            var $btn = $(this);
            var editing = $btn.data('editing') || false;
            if (!editing) {
                // Enter edit mode
                $('#licence_key, #domain_name').prop('readonly', false).first().focus();
                $btn.data('editing', true).text('Save License');
            } else {
                // Save edited values via AJAX (reuse activation endpoint)
                var newLicence = String($('#licence_key').val() || '').trim();
                var newDomain = String($('#domain_name').val() || '').trim();

                // Show processing modal
                $('#commeriq-modal-icon').text('⌛');
                $('#commeriq-modal-title').text('Processing Activation');
                $('#commeriq-modal-message').text('Processing...');
                $('#commeriq-modal-close').hide();
                $('#commeriq-modal').show();

                $.post(commeriqAdmin.ajax_url, {
                    action: 'commeriq_activate_license',
                    nonce: commeriqAdmin.license_nonce,
                    license: { licence_key: newLicence, domain_name: newDomain }
                }).done(function(resp){
                    if (resp && resp.success) {
                        $('#commeriq-modal-icon').text('✅');
                        $('#commeriq-modal-title').text('License Activated');
                        $('#commeriq-modal-message').text('Your license has been updated and activated.');
                        $('#commeriq-modal-close').show();

                        // set readonly and update activated date
                        $('#licence_key, #domain_name').prop('readonly', true);
                        var activatedFormatted = (resp.data && resp.data.activated_at_formatted) ? resp.data.activated_at_formatted : '';
                        if ($('#commeriq-activated-row').length) {
                            $('#commeriq-activated-date').val(activatedFormatted);
                        } else if (activatedFormatted) {
                            var row = '<div id="commeriq-activated-row" style="margin-bottom:12px;"><label style="display:block; margin-bottom:8px; color:#717171;">Activated Date</label>' +
                                      '<input type="text" id="commeriq-activated-date" readonly class="regular-text" style="width:100%; box-sizing:border-box; height:46px; background:#f5f5f5;" value="' + activatedFormatted + '" /></div>';
                            $('.commeriq-license-box .commeriq-insert-after').first().after(row);
                        }

                        $btn.data('editing', false).text('Modify License');
                        $('.commeriq-box-title').text('License Active');
                        try {
                            $('.nav-tab').removeClass('nav-tab-active');
                            $('.nav-tab[href="#tab-licence"]').addClass('nav-tab-active');
                            $('.commeriq-tab').hide();
                            $('#tab-licence').show();
                        } catch (e) { /* ignore */ }
                        try { localStorage.setItem('commeriq_active_tab', 'tab-licence'); } catch(e) {}
                        try { history.replaceState(null, '', '#tab-licence'); } catch(e) { window.location.hash = '#tab-licence'; }
                        setTimeout(function(){ window.location.href = window.location.pathname + window.location.search + '#tab-licence'; }, 250);
                    } else {
                        $('#commeriq-modal-icon').text('⚠️');
                        $('#commeriq-modal-title').text('Activation Failed');
                        var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Update failed';
                        $('#commeriq-modal-message').text(msg);
                        $('#commeriq-modal-close').show();
                    }
                }).fail(function(){
                    $('#commeriq-modal-icon').text('⚠️');
                    $('#commeriq-modal-title').text('Activation Failed');
                    $('#commeriq-modal-message').text('Request failed.');
                    $('#commeriq-modal-close').show();
                });
            }
        });

        $(document).on('click', '#commeriq-remove-license', function(e){
            e.preventDefault();
            if (!confirm('Remove license and clear values?')) { return; }

            // Show processing removal modal
            $('#commeriq-modal-icon').text('⌛');
            $('#commeriq-modal-title').text('Processing Removal');
            $('#commeriq-modal-message').text('Removing license...');
            $('#commeriq-modal-close').hide();
            $('#commeriq-modal').show();

            $.post(commeriqAdmin.ajax_url, {
                action: 'commeriq_remove_license',
                nonce: commeriqAdmin.license_nonce
            }).done(function(resp){
                if (resp && resp.success) {
                    $('#commeriq-modal-icon').text('✅');
                    $('#commeriq-modal-title').text('License Removed');
                    $('#commeriq-modal-message').text('The license has been removed.');
                    $('#commeriq-modal-close').show();

                    // revert UI to not activated
                    $('.commeriq-box-title').text('Activate License');
                    // remove activated date row
                    $('#commeriq-activated-row').remove();
                    // clear and enable inputs
                    $('#licence_key').val('').prop('readonly', false);
                    $('#domain_name').val('').prop('readonly', false);
                    // hide Modify/Remove buttons and show Activate
                    $('#commeriq-modify-license, #commeriq-remove-license').hide();
                    $('#commeriq-activate-license').show();
                    // hide admin notice and disable Store tab
                    $('#commeriq-license-active').hide();
                    var $storeTab = $('.nav-tab[href="#tab-store"]');
                    $storeTab.addClass('nav-tab-disabled');
                } else {
                    $('#commeriq-modal-icon').text('⚠️');
                    $('#commeriq-modal-title').text('Removal Failed');
                    var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Removal failed';
                    $('#commeriq-modal-message').text(msg);
                    $('#commeriq-modal-close').show();
                }
            }).fail(function(){
                $('#commeriq-modal-icon').text('⚠️');
                $('#commeriq-modal-title').text('Removal Failed');
                $('#commeriq-modal-message').text('Request failed.');
                $('#commeriq-modal-close').show();
            });
        });

        // Close modal
        $(document).on('click', '#commeriq-modal-close', function(){
            $('#commeriq-modal').hide();
        });

        // Prevent click on disabled tab
        $(document).on('click', '.nav-tab.nav-tab-disabled', function(e){ e.preventDefault(); });

        // On initial load, if license is activated (server rendered), enable Store tab and show notice
        var activatedVal = $('#commeriq-activated-date').val() || '';
        if (activatedVal.trim() !== '') {
            $('#commeriq-license-active').show();
            var $storeTabInit = $('.nav-tab[href="#tab-store"]');
            $storeTabInit.removeClass('nav-tab-disabled');
            // ensure inputs are readonly on load when active
            $('#licence_key, #domain_name').prop('readonly', true);
            // hide activate button and show modify/remove if not present
            $('#commeriq-activate-license').hide();
            $('#commeriq-modify-license').show();
            $('#commeriq-remove-license').show();
        }
    });

    // ==========================================
    // AI Image Generation
    // ==========================================

    var generatedImageData = null;

    // Open AI Image modal when button is clicked
    $(document).on('click', '#commeriq-generate-ai-image', function(e) {
        e.preventDefault();
        
        // Reset modal to form state
        $('#commeriq-ai-image-form').show();
        $('#commeriq-ai-image-loading').hide();
        $('#commeriq-ai-image-result').hide();
        $('#commeriq-ai-image-error').hide();
        
        // Clear form fields
        $('#commeriq-image-description').val('');
        $('#commeriq-image-style').val('');
        $('#commeriq-image-size').val('1024x1024');
        
        // Show modal
        $('#commeriq-ai-image-modal').fadeIn(200);
    });

    // Start image generation
    $(document).on('click', '#commeriq-start-image-generation', function(e) {
        e.preventDefault();
        
        var postId = $('#post_ID').val();
        if (!postId) {
            showNotification('Please save the product first before generating an image.', 'error');
            return;
        }
        
        // Get form values
        var description = $('#commeriq-image-description').val().trim();
        var style = $('#commeriq-image-style').val();
        var size = $('#commeriq-image-size').val();
        
        // Show loading state
        $('#commeriq-ai-image-form').hide();
        $('#commeriq-ai-image-loading').show();
        $('#commeriq-ai-image-result').hide();
        $('#commeriq-ai-image-error').hide();
        
        // Make AJAX request
        var data = {
            action: 'commeriq_generate_ai_image',
            _nonce: commeriqAdmin.nonce,
            post_id: postId,
            description: description,
            style: style,
            size: size
        };
        
        $.post(commeriqAdmin.ajax_url, data, function(resp) {
            if (resp && resp.success && resp.data && resp.data.image_url) {
                // Store generated image data
                generatedImageData = resp.data;
                
                // Show the generated image
                $('#commeriq-generated-image').attr('src', resp.data.image_url);
                $('#commeriq-ai-image-loading').hide();
                $('#commeriq-ai-image-result').show();
            } else {
                // Show error
                var errorMsg = (resp && resp.data && resp.data.message) 
                    ? resp.data.message 
                    : 'Failed to generate image. Please try again.';
                $('#commeriq-ai-image-error-message').text(errorMsg);
                $('#commeriq-ai-image-loading').hide();
                $('#commeriq-ai-image-error').show();
            }
        }).fail(function(xhr, status, error) {
            var errorMsg = 'Request failed: ';
            if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                errorMsg += xhr.responseJSON.data.message;
            } else {
                errorMsg += error || 'Internal Server Error';
            }
            $('#commeriq-ai-image-error-message').text(errorMsg);
            $('#commeriq-ai-image-loading').hide();
            $('#commeriq-ai-image-error').show();
        });
    });

    // Set as featured image
    $(document).on('click', '#commeriq-set-featured-image', function(e) {
        e.preventDefault();
        
        if (!generatedImageData || !generatedImageData.attachment_id) {
            showNotification('No image data available. Please regenerate the image.', 'error');
            return;
        }
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner is-active" style="float:none;margin:0 5px 0 0;"></span>Setting...');
        
        var postId = $('#post_ID').val();
        var data = {
            action: 'commeriq_set_featured_image',
            _nonce: commeriqAdmin.nonce,
            post_id: postId,
            attachment_id: generatedImageData.attachment_id
        };
        
        $.post(commeriqAdmin.ajax_url, data, function(resp) {
            $btn.prop('disabled', false).html(originalText);
            
            if (resp && resp.success) {
                // Update the product image thumbnail
                if (generatedImageData.thumbnail_url) {
                    $('#set-post-thumbnail img').attr('src', generatedImageData.thumbnail_url);
                    $('#postimagediv .inside').html('<img src="' + generatedImageData.thumbnail_url + '" style="max-width:100%;" />');
                }
                
                // Close modal first
                $('#commeriq-ai-image-modal').fadeOut(200);
                
                // Show success message
                showNotification('Product image set successfully!', 'success');
                
                // Reload page to refresh the featured image display
                location.reload();
            } else {
                var errorMsg = (resp && resp.data && resp.data.message) 
                    ? resp.data.message 
                    : 'Failed to set product image.';
                showNotification(errorMsg, 'error');
            }
        }).fail(function() {
            $btn.prop('disabled', false).html(originalText);
            showNotification('Request failed. Please try again.', 'error');
        });
    });

    // Save to media library (already done during generation, just inform user)
    $(document).on('click', '#commeriq-save-to-library', function(e) {
        e.preventDefault();
        
        if (!generatedImageData || !generatedImageData.attachment_id) {
            showNotification('No image data available.', 'error');
            return;
        }
        
        showNotification('Image has been saved to your Media Library! (ID: ' + generatedImageData.attachment_id + ')', 'success');
    });

    // Regenerate image
    $(document).on('click', '#commeriq-regenerate-image', function(e) {
        e.preventDefault();
        
        // Go back to form
        $('#commeriq-ai-image-result').hide();
        $('#commeriq-ai-image-form').show();
    });

})(window, jQuery);
