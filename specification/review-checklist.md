# WordPress.org Plugin Review Checklist

**Plugin:** CommerIQ — AI Powered Commerce Insights for WooCommerce  
**Review ID:** AUTOPREREVIEW ❗TRM-LIC  
**Date:** December 1, 2025  
**Status:** In Progress

## Critical Issues

### 1. Plugin Name and Slug
- [x] **Issue:** Name similarity with "CommerceIQ" trademark
- [x] **Issue:** Slug exceeds 50 characters (current: `commeriq-ai-powered-commerce-insights-for-woocommerce`)
- [ ] **Action:** Change display name to include unique identifier (suggested: "Wontonee CommerIQ — AI Commerce Insights for WooCommerce")
- [ ] **Action:** Request new slug reservation (suggested: `wontonee-commeriq`)
- [ ] **Action:** Update display name in readme.txt
- [ ] **Action:** Update display name in commeriq.php header
- [ ] **Action:** Update Text Domain in plugin files
- [ ] **Action:** Reply to WordPress.org requesting new slug
- [ ] **Action:** Upload new version via "Add your plugin" page

**NOTE:** This requires manual action by the developer to:
1. Choose a final plugin name with unique branding
2. Email WordPress.org to request the new slug
3. Update all references in the code after slug approval

### 2. Scripts Not Properly Enqueued ✅ **COMPLETED**
- [x] **File:** `src/Views/admin-settings.php:410` - `<script>` tag found
- [x] **File:** `src/Admin/ProductEditor.php:131` - `<script type="text/javascript">` tag found
- [x] **Action:** Replace inline scripts with `wp_enqueue_script()` or `wp_add_inline_script()`
- [x] **Action:** Use `admin_enqueue_scripts` hook for admin pages

**FIXES APPLIED:**
- Created `/assets/js/commeriq-admin-settings.js` for settings page scripts
- Created `/assets/js/commeriq-product-editor.js` for product editor scripts
- Updated `src/commeriq-loader.php` to properly enqueue scripts with `admin_enqueue_scripts` hook
- Removed all inline `<script>` tags from view files
- Added proper script localization for i18n strings

### 3. Trialware/Locked Features (Guideline 5) ⚠️ **CRITICAL**
- [x] **Review:** Check for any license-locked functionality
- [x] **Review:** Ensure no features are disabled/limited without payment
- [x] **Review:** Remove any trial periods or usage limits
- [ ] **Action:** Remove all license checks that control feature access
- [ ] **Action:** Fully enable all built-in features
- [x] **Note:** External service features are OK if properly documented

**ISSUES FOUND:**
The plugin locks built-in UI features behind license activation:
1. `ProductEditor.php` lines 60 & 125: Hides AI Image button if license not active
2. `admin-settings.php` line 38: Disables "Store Analyzer" tab without license
3. These are UI elements built into the plugin code, NOT external service features

**SOLUTION REQUIRED:**
- Remove license checks from `ProductEditor::render_price_comparison_button()` and `ProductEditor::render_ai_image_button()`
- Ex] **Issue:** `https://wontonee.com/commeriq` returns 404
- [ ] **Action:** Create the page or update URI in commeriq.php header

**NOTE:** Developer needs to either create the page at this URL or update the Plugin URI to a working page.
- License validation should happen at the API level, NOT in the WordPress plugin UI

**WordPress.org Rule:** All code in the directory must be fully functional. External services can require authentication, but the plugin interface must be available.

### 4. Invalid Plugin URI
- [ ] **Issue:** `https://wontonee.com/commeriq` returns 404
- [ ] **Action:** Crea ✅ **COMPLETED**
- [x] **File:** `22_11-28-00_Archive/src/commeriq-loader.php.bak` - backup file found
- [x] **Action:** Delete backup file from plugin directory

**STATUS:** File/folder does no ✅ **COMPLETED**
- [x] **Action:** Add `Requires Plugins: woocommerce` to commeriq.php header

**FIX APPLIED:** Added `Requires Plu ✅ **COMPLETED**
- [x] **File:** `tests/bootstrap.php:3` - missing ABSPATH check
- [x] **Action:** Add `if ( ! defined( 'ABSPATH' ) ) exit;` to all PHP files with executable code

**FIX APPLIED:** Added ABSPATH check to `tests/bootstrap.php`
### 6. Missing Required Headers
- [ ] **Action:** Add `Requires Plugins: woocommerce` to commeriq.php header

### 7. Direct File Access Protection
- [ ] **File:** `tests/bootstrap.php:3` - missing ABSPATH check
- [ ] **Action:** Add `if ( ! defined( 'ABSPATH' ) ) exit;` to all PHP files with executable code

## Additional Checks

### Guidelines Compliance
- [ ] Verify all code is GPL-compatible
- [ ] Ensure no functionality restrictions
- [ ] Document any external services (Terms of Use, Privacy Policy)

### Name/Trademark Review
- [ ] Username and display name don't infringe trademarks
- [ ] Plugin icon/banner don't use trademarked terms improperly
- [ ] URLs don't infringe trademarks

## Files to Modify

1. `commeriq.php` - Main plugin file (headers)
2. `readme.txt` - Plugin description
3. `src/Views/admin-settings.php` - Enqueue scripts
4. `src/Admin/ProductEditor.php` - Enqueue scripts
5. `tests/bootstrap.php` - Add ABSPATH check
6. `22_11-28-00_Archive/` - Delete folder/files

## Files to Review for License Checks

- [ ] Search codebase for license validation
- [ ] Search for feature restrictions
- [ ] Search for trial/quota checks

## Before Submission

- [ ] All issues resolved
- [ ] Plugin tested thoroughly
- [ ] No fatal errors on activation
- [ ] All changes documented in reply
- [ ] New version uploaded to WordPress.org

## Notes

- Cannot change slug after approval
- Keep reply brief and direct
- Volunteers will review entire plugin again
- Do not copy-paste AI responses in reply
