# CommerIQ — AI Powered Commerce Insights for WooCommerce (v1.0)

> Complete specification document for development, release 1.0

---

## Table of Contents
1. Overview & Goals
2. Core Features (v1.0)
3. System Architecture
4. Directory Structure (plugin root)
5. File Descriptions
6. AI Agent Design (integration via Laravel API)
7. Data Flow & Payloads
8. Unique Naming Conventions (namespace + prefix)
9. Functions & Variables (complete list)
10. Database Schema (custom tables)
11. Hooks & Filters (WooCommerce / WP integration points)
12. Admin UI (screens, components)
13. Background Jobs & Cron
14. Security & Capability Requirements
15. Logging, Telemetry, Errors
16. Testing & QA
17. Future Roadmap (v1.1+)
18. Appendix — Example JSON payloads, sample prompts

---

## 1. Overview & Goals
CommerIQ v1.0 is a lightweight WooCommerce plugin that extracts product metadata (title, tags, SKU, price), performs AI-assisted price comparison and margin analytics via a dedicated Laravel API (hosted by you), and surfaces insights within the WordPress admin area.

Primary goals for v1.0:
- Compare product prices by title & tags across configured data sources.
- Provide margin & profit analytics for each WooCommerce product.
- Offer a simple Admin UI to run comparisons and view results.
- Securely call Laravel API to perform AI computations and store results.
- Include a **Preliminary Store Configuration Layer** that reads WooCommerce base settings (country, currency, state, industry, business type) to use as foundational context for AI operations.

Non-goals for v1.0:
- No competitor web-scraping inside plugin. (All external data fetched/managed by Laravel API.)
- No automated price-changing actions (read-only insights).

---

## 2. Core Features (v1.0)
- Manual and scheduled price comparison triggered from a product page or bulk product list.
- Product-level insights panel (Price comparison summary, Suggested price range, Margin, Notes).
- Bulk report export (CSV) for selected products.
- Light-weight custom table to store comparison results and history.
- Admin Settings page to configure Laravel API endpoint and API key.
- **Store Configuration and Memory Module:**
  - Reads essential store details from WooCommerce base configuration:
    - **Country** (WooCommerce base country)
    - **Currency** (e.g., INR or USD)
    - **State/Region**
    - **Industry** (selectable by admin if not present)
    - **Type of Business** (selectable by admin; options like Retail, Wholesale, D2C, etc.)
  - Stores details in a persistent option record (`commeriq_store_config`)
  - Displays admin UI for viewing, refreshing, or editing store configuration
  - Must be initialized before Pricing IQ or analytics modules start.

---

## 3. System Architecture
- **WordPress / WooCommerce plugin (CommerIQ)**
  - Collects product data and store configuration.
  - Sends both product and configuration data to Laravel API.
  - Displays insights and stores results in custom DB tables.
  - Provides configuration setup wizard after installation.
- **Laravel API (separate spec)**
  - Receives product payload and store context.
  - Enriches, analyzes, and returns structured insights via AI agent.
  - Authentication via API key and HMAC.

Communication: HTTPS JSON over POST to configured endpoint.

---

## 4. Directory Structure (plugin root)
```
commeriq/
├── composer.json
├── commeriq.php
├── readme.txt
├── assets/
│   ├── css/
│   │   └── commeriq-admin.css
│   └── js/
│       └── commeriq-admin.js
├── src/
│   ├── Admin/
│   │   ├── SettingsPage.php
│   │   ├── ProductMetaBox.php
│   │   ├── ReportsPage.php
│   │   └── StoreConfiguration.php
│   ├── API/
│   │   ├── ApiClient.php
│   │   └── ApiRoutes.php
│   ├── DB/
│   │   └── Migrations.php
│   ├── Helpers/
│   │   └── Utils.php
│   ├── Jobs/
│   │   └── CommerIQ_SyncJob.php
│   ├── Models/
│   │   └── CommerIQ_Result.php
│   ├── REST/
│   │   └── RestEndpoints.php
│   ├── Views/
│   │   ├── admin-settings.php
│   │   ├── admin-product-panel.php
│   │   └── admin-store-config.php
│   └── commeriq-loader.php
├── languages/
│   └── commeriq.pot
└── uninstall.php
```

---

## 5. File Descriptions
Each component’s responsibilities are clearly defined:
- **SettingsPage.php:** API key and endpoint settings.
- **ProductMetaBox.php:** Product-level price comparison.
- **StoreConfiguration.php:** Base configuration storage and UI.
- **ApiClient.php:** Secure Laravel API communication.
- **CommerIQ_Result.php:** Model for comparison results.
- **Migrations.php:** Creates database tables.
- **RestEndpoints.php:** REST routes for async actions.

---

## 6. AI Agent Design (Integration via Laravel API)
The Laravel API hosts the AI logic. CommerIQ simply sends structured data and receives processed insights.

**Responsibilities:**
- Identify similar products from external datasets.
- Compute suggested price range and profit margins.
- Return confidence levels for each recommendation.

**Response Schema Example:**
```json
{
  "product_id": 123,
  "timestamp": "2025-11-06T12:00:00+05:30",
  "comparisons": [
    {"source": "MarketA", "price": 1299, "confidence": 0.87}
  ],
  "suggested_price": {"min": 1100, "max": 1400},
  "margin": {"gross_margin": 36, "net_margin": 24},
  "confidence_score": 0.82
}
```

---

## 7. Data Flow & Payloads
**Flow:**
1. Admin clicks “Run Comparison” in Product panel.
2. Plugin gathers WooCommerce product data and store config.
3. Sends POST request to Laravel API.
4. Laravel returns AI insights.
5. Plugin stores and displays result.

**Sample Payload:**
```json
{
  "source": "wordpress-commeriq-plugin",
  "store": {
    "country": "IN",
    "currency": "INR",
    "state": "DL",
    "industry": "Retail",
    "business_type": "D2C"
  },
  "product": {
    "id": 123,
    "title": "Wireless Earbuds Pro 2",
    "sku": "WE-PRO-2",
    "price": 3499.00,
    "cost_price": 2200.00,
    "tags": ["wireless", "earbuds", "bluetooth"]
  }
}
```

---

## 8. Unique Naming Conventions
- Namespace: `CommerIQ\*`
- PHP function prefix: `commeriq_`
- DB table prefix: `wp_commeriq_`
- JS global: `window.commeriqAdmin`
- AJAX action: `commeriq_run_comparison`

---

## 9. Functions & Variables
Core bootstrap and helpers, all prefixed `commeriq_`:
- `commeriq_init()` — main initializer
- `commeriq_register_settings()` — registers options
- `commeriq_ajax_run_comparison()` — AJAX trigger
- `commeriq_store_comparison_result()` — save API response
- `commeriq_get_last_comparison()` — retrieve data

---

## 10. Database Schema
Table: `wp_commeriq_price_comparisons`
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| product_id | BIGINT | WooCommerce product ID |
| payload | LONGTEXT | JSON response |
| confidence_score | FLOAT | AI confidence |
| created_at | DATETIME | Timestamp |

---

## 11. Hooks & Filters
Hooks:
- `plugins_loaded` → `commeriq_init`
- `add_meta_boxes_product` → Product Meta Box
- `wp_ajax_commeriq_run_comparison` → AJAX call

Filters:
- `commeriq_api_payload`
- `commeriq_api_response`
- `commeriq_suggested_price_modifier`

---

## 12. Admin UI
- **Settings Page:** Laravel API setup and test connection.
- **Product Meta Box:** Run comparison, view last result.
- **Reports Page:** CSV export and filters.
- **Store Config Page:** Shows and refreshes WooCommerce details.

---

## 13. Background Jobs & Cron
- `commeriq_hourly_sync` — refresh selected products.
- `CommerIQ_SyncJob` — executes via WP-Cron.

---

## 14. Security
- Capability checks (`manage_woocommerce`).
- Nonce validation for AJAX.
- HMAC-signed API requests.
- HTTPS required.

---

## 15. Logging, Telemetry, Errors
- `commeriq_log()` — logs debug data.
- Error storage in option table with timestamps.
- Display last 10 errors in settings page.

---

## 16. Testing & QA
- Unit test: API signing, response validation.
- Integration test: API connectivity.
- Manual test checklist for admin UI and cron.

---

## 17. Future Roadmap (v1.1+)
- Competitor scraping integrations.
- Auto price suggestions.
- Analytics dashboards and charts.
- Multi-currency and store support.

---

## 18. Appendix — Example JSON Payloads, Sample Prompts
**Prompt Example:**
```
System: You are RetailPricingAgent. Analyze given WooCommerce product data and suggest optimal pricing range.
User: {Product payload JSON}
```
**Response Example:** see Section 6 schema.

---

*End of CommerIQ v1.0 Specification*
