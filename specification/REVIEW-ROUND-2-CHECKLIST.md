# WordPress.org Review - Round 2 Action Items

**Date:** December 26, 2025  
**Status:** Slug confirmed as `wontonee-commeriq` ✅  
**Reviewer Notes:** 4 issues to fix

---

## ✅ COMPLETED
- [x] Slug changed to: `wontonee-commeriq`
- [x] Display name confirmed: "Wontonee CommerIQ — AI Commerce Insights for WooCommerce"

---

## 🔴 CRITICAL ISSUES TO FIX

### 1. Add Yourself to Contributors List
**Issue:** Your WordPress.org username "sajudeveloper18" is not in the contributors list

**Current in readme.txt:**
```
Contributors: wontonee
```

**Fix to:**
```
Contributors: sajudeveloper18, wontonee
```

**File to edit:** `readme.txt` line 2

**Priority:** LOW (optional but recommended)

---

### 2. Remove Backup File ⚠️ MUST FIX
**Issue:** Backup file found that shouldn't be in the plugin

**File to delete:**
```
19_08-59-12_Archive/src/commeriq-loader.php.bak
```

**Action:**
- [ ] Delete the entire `19_08-59-12_Archive` folder
- [ ] Add to `.distignore` file to prevent future uploads

**Priority:** HIGH (blocks approval)

---

### 3. Fix Text Domain Mismatch ⚠️ MUST FIX
**Issue:** 153 strings using wrong text domain

**Current:** `commeriq-ai-powered-commerce-insights-for-woocommerce`  
**Required:** `wontonee-commeriq`

**Examples of what needs changing:**
```php
// WRONG:
__('Hello', 'commeriq-ai-powered-commerce-insights-for-woocommerce')
esc_html__('Text', 'commeriq-ai-powered-commerce-insights-for-woocommerce')

// CORRECT:
__('Hello', 'wontonee-commeriq')
esc_html__('Text', 'wontonee-commeriq')
```

**Files affected:** All PHP files in `src/` directory (153 occurrences)

**Fix method:**
```bash
# Search and replace in all files
find src/ -type f -name "*.php" -exec sed -i '' 's/commeriq-ai-powered-commerce-insights-for-woocommerce/wontonee-commeriq/g' {} +
```

**Priority:** HIGH (blocks approval)

---

### 4. Fix Unsafe SQL Call ⚠️ MUST FIX
**Issue:** SQL query not using `wpdb::prepare()` - security vulnerability

**File:** `uninstall.php` line 16

**Current code:**
```php
$wpdb->query(sprintf('DROP TABLE IF EXISTS `%s`', esc_sql($commeriq_table_name)));
```

**Fix to:**
```php
$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %i', $commeriq_table_name));
```

**Note:** Use `%i` placeholder for table/column names (WordPress 6.2+)

**Priority:** CRITICAL (security issue)

---

## 📝 ACTION PLAN

### Step 1: Delete Backup File
```bash
cd /Volumes/Crucial/webapps/genesys/wp-content/plugins/commeriq-ai-powered-commerce-insights-for-woocommerce/
rm -rf 19_08-59-12_Archive/
```

### Step 2: Fix Text Domain (153 occurrences)
Run find/replace across all PHP files:
```bash
# From plugin root directory
find . -type f -name "*.php" -exec sed -i '' 's/commeriq-ai-powered-commerce-insights-for-woocommerce/wontonee-commeriq/g' {} +
```

### Step 3: Fix SQL in uninstall.php
Edit `uninstall.php` line 16 to use `wpdb::prepare()`

### Step 4: Add Yourself to Contributors (optional)
Edit `readme.txt` line 2 to include your username

### Step 5: Test Everything
- [ ] Enable `WP_DEBUG` in wp-config.php
- [ ] Activate plugin on clean WordPress install
- [ ] Test all features
- [ ] Check for PHP errors/warnings
- [ ] Deactivate and uninstall plugin
- [ ] Verify no errors during uninstall

### Step 6: Upload & Reply
- [ ] Upload new version at WordPress.org
- [ ] Send brief email confirming fixes

---

## 📧 EMAIL TEMPLATE TO SEND

```
Subject: Re: Wontonee CommerIQ - Issues Fixed

Hello,

Thank you for the review and confirming the slug change.

All issues have been addressed:

✅ Removed backup file: Deleted 19_08-59-12_Archive/ folder
✅ Text Domain: Updated all 153 occurrences from old domain to "wontonee-commeriq"
✅ SQL Security: Fixed uninstall.php to use wpdb::prepare() with %i placeholder
✅ Contributors: Added sajudeveloper18 to readme.txt

Tested with WP_DEBUG enabled on fresh WordPress 6.9 install - no errors.

New version uploaded.

Thank you!

Best regards,
Saju Gopal
```

---

## 🧪 TESTING CHECKLIST

Before uploading:

### Basic Tests
- [ ] Plugin activates without errors
- [ ] All admin pages load correctly
- [ ] No PHP notices/warnings in debug.log
- [ ] JavaScript console has no errors

### Feature Tests (Without License)
- [ ] All buttons/tabs visible
- [ ] Settings page loads
- [ ] Product editor loads with buttons
- [ ] Clicking features shows appropriate error

### Feature Tests (With License)
- [ ] License activates successfully
- [ ] AI content generation works
- [ ] AI image generation works
- [ ] Price comparison works
- [ ] License removal works

### Uninstall Test
- [ ] Deactivate plugin
- [ ] Delete plugin
- [ ] Check debug.log - should be no SQL errors
- [ ] Verify database table dropped

---

## 📋 FILES TO MODIFY

### Must Fix:
1. ✅ Delete: `19_08-59-12_Archive/` (entire folder)
2. ✅ Edit: `uninstall.php` - Fix SQL query
3. ✅ Edit: All PHP files in `src/` - Text domain (153 changes)
4. ✅ Edit: `readme.txt` - Add contributor

### Optional (Recommended):
- Create `.distignore` file to prevent backup files in future

---

## ⚠️ IMPORTANT NOTES

1. **Text Domain Change:** This is the biggest task (153 files). Use find/replace command carefully.

2. **SQL Security:** The `%i` placeholder is for identifiers (table/column names), not values. WordPress 6.2+ required.

3. **Testing:** Don't skip the uninstall test - this is where the SQL fix matters most.

4. **Backup Files:** Make sure no other archive/backup folders exist before uploading.

---

## 🎯 PRIORITY ORDER

1. 🔴 **CRITICAL** - Fix SQL security (uninstall.php)
2. 🔴 **HIGH** - Delete backup file
3. 🔴 **HIGH** - Fix text domain (all files)
4. 🟡 **LOW** - Add contributor (optional)

---

## ✅ READY TO SUBMIT WHEN:

- [ ] All 4 issues fixed
- [ ] Tested with WP_DEBUG
- [ ] No errors during activation/deactivation/uninstall
- [ ] New version uploaded to WordPress.org
- [ ] Email sent to review team

**Estimated time:** 30-60 minutes for all fixes

Good luck! You're very close to approval! 🚀
