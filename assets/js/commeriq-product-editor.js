jQuery(document).ready(function($) {
    // Add AI Image button to product image metabox
    var $imageDiv = $('#postimagediv');
    if ($imageDiv.length > 0) {
        var $setImageLink = $imageDiv.find('#set-post-thumbnail');
        if ($setImageLink.length > 0) {
            var aiImageButton = '<a href="#" id="commeriq-generate-ai-image" class="commeriq-ai-image-button" style="display:block; margin-top:10px; text-decoration:none;">' +
                '<span class="dashicons dashicons-format-image" style="vertical-align:middle; color:#2271b1;"></span> ' +
                '<span style="color:#2271b1; font-weight:500;">Generate AI Image</span>' +
                '</a>';
            $setImageLink.after(aiImageButton);
        }
    }
});
