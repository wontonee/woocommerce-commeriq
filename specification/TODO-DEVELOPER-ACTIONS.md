# Action Items for WordPress.org Approval

## ⚠️ CRITICAL - Must Fix Before Resubmission

### 1. Remove License-Based UI Restrictions

**The Problem:**  
Your plugin currently hides features in the user interface based on license status. This violates WordPress.org Guideline 5 (Trialware). All code hosted on WordPress.org must be fully functional.

**What to Change:**

#### File: `src/Admin/ProductEditor.php`

**Line ~60 - render_price_comparison_button():**
```php
// REMOVE THIS BLOCK:
if (!$is_active) {
    return; // Don't show button if license not active
}
```

**Line ~125 - render_ai_image_button():**
```php
// REMOVE THIS BLOCK:
if (!$is_active) {
    return; // Don't show button if license not active
}
```

#### File: `src/Views/admin-settings.php`

**Line ~38 - Tab rendering:**
```php
// CHANGE FROM:
<?php $commeriq_store_tab_class = $commeriq_is_active ? 'nav-tab' : 'nav-tab nav-tab-disabled'; ?>
<a class="<?php echo esc_attr($commeriq_store_tab_class); ?>" href="#tab-store" data-disabled="<?php echo esc_attr($commeriq_is_active ? '0' : '1'); ?>">

// CHANGE TO:
<a class="nav-tab" href="#tab-store">
```

**Line ~420 - Tab click handler (if you add it back):**
```javascript
// REMOVE the license check from tab switching
// Let users access all tabs - they'll see helpful messages instead
```

**The Right Way:**
- ✅ Show ALL buttons and interface elements
- ✅ When user clicks a feature, check license via API call
- ✅ If API returns error due to invalid license, show friendly message:
  - "⚠️ This feature requires an active license. [Get License]"
- ✅ License validation happens at the API/service level, not in plugin UI

---

## ⚠️ REQUIRED - Before Submission

### 2. Fix Plugin URI

**Current:** `https://wontonee.com/commeriq` (returns 404)

**Choose one solution:**
- Create a page at that URL with plugin information
- Update Plugin URI in `commeriq.php` to: `https://wontonee.com/` or another working URL

---

### 3. Choose Final Plugin Name & Request Slug

**Current Issues:**
- Name similar to "CommerceIQ" trademark
- Slug too long (50+ characters)

**Recommended:**
- Name: "Wontonee CommerIQ — AI Commerce Insights for WooCommerce"
- Slug: `wontonee-commeriq`

**Steps:**
1. Decide on your final name (must include unique identifier like "Wontonee")
2. Reply to WordPress.org email requesting slug change
3. Wait for slug confirmation before proceeding
4. After confirmation, update:
   - Plugin Name in `commeriq.php`
   - Plugin name in `readme.txt`
   - Text Domain throughout code (optional but recommended)

---

## ✅ Already Fixed (No Action Needed)

These issues have been automatically resolved:

1. ✅ Script enqueuing - All JavaScript now properly enqueued
2. ✅ ABSPATH check - Added to `tests/bootstrap.php`
3. ✅ Required headers - Added `Requires Plugins: woocommerce`
4. ✅ Backup files - Verified none exist

---

## 🧪 Testing Checklist

Before resubmitting, test these scenarios:

### Without License:
- [ ] Plugin activates successfully
- [ ] All tabs are visible in settings page
- [ ] All buttons show on product edit page
- [ ] Clicking features shows "license required" message (from API)
- [ ] No PHP errors in debug.log
- [ ] No JavaScript errors in browser console

### With License:
- [ ] License can be activated
- [ ] Features work as expected
- [ ] API calls succeed
- [ ] Can remove license

### General:
- [ ] Plugin requires WooCommerce to activate
- [ ] Deactivates cleanly
- [ ] No fatal errors on activation
- [ ] Settings page loads without errors

---

## 📧 When Ready to Resubmit

1. Make all the changes above
2. Test thoroughly
3. Upload new version at https://wordpress.org/plugins/developers/add/
4. Reply to review email with:

```
Thank you for the review. Changes made:

✅ Removed license restrictions from UI - all features now visible
✅ License validation moved to API level only
✅ Fixed script enqueuing (wp_enqueue_script)
✅ Added ABSPATH security check
✅ Added Requires Plugins header
✅ Fixed Plugin URI to: [YOUR URL]

Slug change request:
New slug: [YOUR CHOSEN SLUG]
New name: [YOUR CHOSEN NAME]

Uploaded version [VERSION NUMBER] with all fixes.
```

---

## 💡 Understanding the Trialware Rule

**What WordPress.org Allows:**
- ✅ External APIs that require authentication
- ✅ Paid services that process data on your servers
- ✅ Premium features provided by external service
- ✅ Showing users what features are available

**What WordPress.org Does NOT Allow:**
- ❌ Hiding UI elements based on license
- ❌ Disabling built-in functionality in the code
- ❌ Time-limited trials of local features
- ❌ Usage quotas for built-in features

**Your Case:**
- Your plugin's UI is built-in functionality → Must always be visible
- Your API provides the actual service → Can require license there
- Solution: Show the interface, validate license when calling API

---

## 🆘 Need Help?

If you have questions about these requirements:
1. Reply to the WordPress.org review email
2. Be specific about what's unclear
3. Volunteers will guide you through it

Remember: The goal is to make sure users get value from the free code on WordPress.org, while your paid service can provide the premium functionality. Both can coexist!

---

**Priority Order:**
1. 🔴 **Fix trialware issue** (blocks approval)
2. 🟡 Fix Plugin URI (required)
3. 🟡 Choose name & request slug (required)
4. 🟢 Test everything
5. 🟢 Resubmit

Good luck! 🚀
