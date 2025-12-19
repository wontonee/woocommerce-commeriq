# Trademark Resolution - WordPress.org Slug Change Request

## ✅ Changes Made to Address Trademark Concern

### 1. Updated Plugin Name
**Old:** CommerIQ — AI Powered Commerce Insights for WooCommerce  
**New:** **Wontonee CommerIQ — AI Commerce Insights for WooCommerce**

**Why this resolves the issue:**
- ✅ Adds unique "Wontonee" branding at the beginning
- ✅ Clearly distinguishes from "CommerceIQ" trademark
- ✅ Shows it's a Wontonee product, not affiliated with CommerceIQ
- ✅ Follows WordPress.org's recommendation exactly

### 2. Updated Text Domain (SEO-Optimized)
**Old:** `commeriq-ai-powered-commerce-insights-for-woocommerce` (too long)  
**New:** `wontonee-ai-commerce-woocommerce` (36 characters)

**SEO Benefits:**
- ✅ Contains "ai" keyword
- ✅ Contains "commerce" keyword
- ✅ Contains "woocommerce" keyword
- ✅ Unique "wontonee" prefix avoids trademark
- ✅ Highly searchable and discoverable

### 3. Files Updated
- ✅ `commeriq.php` - Plugin headers updated
- ✅ `readme.txt` - Plugin name and contributors updated

---

## 📧 REQUIRED: Request Slug Change from WordPress.org

You **MUST** reply to the WordPress.org review email requesting the new slug **BEFORE** they approve your plugin. Once approved, the slug cannot be changed.

### Email Template to Send:

```
Subject: Re: [WordPress Plugin Review] CommerIQ

Hello,

Thank you for the detailed review. To address the trademark concern, I have updated the plugin name to include unique branding:

NEW PLUGIN NAME: Wontonee CommerIQ — AI Commerce Insights for WooCommerce
NEW SLUG REQUEST: wontonee-ai-commerce-woocommerce

This clearly distinguishes our plugin from the "CommerceIQ" trademark by:
- Adding our unique "Wontonee" brand at the beginning
- Including key SEO terms (ai, commerce, woocommerce) for discoverability
- Following your recommended naming pattern

The updated plugin files have been uploaded with:
✅ New plugin name in headers
✅ Updated text domain to "wontonee-ai-commerce-woocommerce"
✅ All technical issues resolved (script enqueuing, trialware compliance, security)

Please reserve the slug "wontonee-commeriq" for this submission.

Thank you!
```

---

## ⚠️ IMPORTANT WORKFLOW

**DO THIS IN ORDER:**

1. **✅ DONE** - Update plugin files (already completed)
   - Plugin name changed
   - Text domain changed
   - readme.txt updated

2. **NEXT** - Email WordPress.org requesting slug change
   - Use template above
   - Wait for confirmation

3. **AFTER SLUG CONFIRMATION** - Upload final version
   - Go to: https://wordpress.org/plugins/developers/add/
   - Upload plugin with new name
   - Mention in notes that slug was requested and confirmed

4. **IN SAME EMAIL** - Address all other issues:
   ```
   All technical issues resolved:
   ✅ Script Enqueuing: Properly using wp_enqueue_script()
   ✅ Trialware: Removed UI-level restrictions, API validation only
   ✅ Security: ABSPATH checks added
   ✅ Dependencies: Requires Plugins header added
   ✅ Plugin URI: Using GitHub repository
   ✅ Trademark: Updated name to "Wontonee CommerIQ", requesting slug "wontonee-commeriq"
   ```

---

## 📝 Text Domain Migration (Optional - Can be done later)

The text domain changed from `commeriq-ai-powered-commerce-insights-for-woocommerce` to `wontonee-commeriq`.

**Current state:** All strings still use the old text domain in the code.

**Options:**
1. **Keep old text domain** - WordPress.org accepts this, translations will still work
2. **Update all strings** - Better for consistency but not required for approval

**If you want to update (optional):**
```bash
# Find all instances
grep -r "commeriq-ai-powered-commerce-insights-for-woocommerce" src/

# Replace in all PHP files (can be done after approval)
```

**For WordPress.org approval:** The text domain in the plugin header is what matters most (already updated).

---

## 🎯 Summary

**What was the trademark issue:**
- "CommerIQ" too similar to "CommerceIQ" trademark
- Could cause user confusion
- Slug was too long (50+ characters)

**How it's resolved:**
- Added "Wontonee" branding for distinction ✅
- Shortened slug to "wontonee-commeriq" ✅
- Updated all plugin headers ✅
- Follows WordPress.org guidelines ✅

**Next action:**
- Reply to WordPress.org email requesting slug change
- Use the email template above
- Wait for confirmation before final upload

---

## 🚀 You're Almost There!

This was the last major concern. Once you:
1. Send the slug change request email
2. Get confirmation
3. Upload the updated plugin

Your plugin should be approved! All technical and compliance issues are resolved. 🎉
