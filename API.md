## eKomi Integration for Magento 2 — API & Integration Guide

This document describes the public integration surface of the `Ekomi_EkomiIntegration` Magento 2 module: configuration, events/observers, cron jobs, helpers, blocks/widgets, and external API calls.

- **Module name**: `Ekomi_EkomiIntegration`
- **Composer package**: `ekomiltd/ekomiintegration`
- **Version**: 2.5.14

### Contents
- [Installation](#installation)
- [Configuration](#configuration)
- [Export Triggers](#export-triggers)
  - [On Order Status Change (Observer)](#on-order-status-change-observer)
  - [Daily Export (Cron)](#daily-export-cron)
- [Helpers](#helpers)
  - [`Helper\Data`](#helperdata)
  - [`Helper\OrderData`](#helperorderdata)
  - [`Helper\Export`](#helperexport)
- [Admin Controller Rewrite](#admin-controller-rewrite)
- [Widget: Product Review Container](#widget-product-review-container)
- [ACL](#acl)
- [Module XML](#module-xml)
- [External APIs](#external-apis)

---

### Installation
Install via Composer, then enable and upgrade the module.

```bash
composer require ekomiltd/ekomiintegration
bin/magento module:enable Ekomi_EkomiIntegration
bin/magento setup:upgrade
```

---

### Configuration
Admin path: Stores → Configuration → Ekomi → Ekomi Integration (`section id = ekomiintegration`).

Group: `general`
- **Enabled** (`ekomiintegration/general/active`, Yes/No) — overall module switch. Backend model validates credentials.
- **Product Base Reviews** (`ekomiintegration/general/product_reviews`, Yes/No)
- **Shop ID** (`ekomiintegration/general/shop_id`) — provided by eKomi.
- **Shop Password** (`ekomiintegration/general/shop_password`) — interface password from eKomi.
- **Order Status** (`ekomiintegration/general/order_status`, multiselect) — statuses that trigger/send exports.
- **Review Mode** (`ekomiintegration/general/review_mod`, `email` | `sms` | `fallback`).
- **Product Identifier** (`ekomiintegration/general/product_identifier`, `id` | `sku`).
- **Exclude Products** (`ekomiintegration/general/exclude_products`, comma-separated IDs/SKUs).
- **Export Method** (`ekomiintegration/general/export_method`, `status` | `cron`).
- **TurnAround Time** (`ekomiintegration/general/turnaround_time`, days as integer).
- **Terms and Conditions** (`ekomiintegration/general/terms_and_conditions`, Yes/No). Must be accepted for exports.

Default values (`etc/config.xml`):
- `active=0`, `product_reviews=0`, `export_method=status`, `turnaround_time=10`, `terms_and_conditions=1`, `prc/show_prc=0`.

Validation on save:
- `Model\Validate::beforeSave()` verifies Shop ID/Password against eKomi and may warn if default SRR customer segment is disabled.

---

### Export Triggers

#### On Order Status Change (Observer)
- Event: `sales_order_save_after` (`etc/events.xml`).
- Observer: `Observer\SendOrderToEkomi`.
- Active when all conditions are met:
  - Module Enabled
  - `Export Method = status`
  - Order status is in configured statuses
  - Terms and Conditions accepted
- Action: Collects order payload and sends to Plugins Dashboard API.

#### Daily Export (Cron)
- Cron job id: `magestore_cron` (group `default`) every day at 00:00 (`etc/crontab.xml`).
- Class: `Cron\Export::execute()`.
- Active when all conditions are met per store:
  - Module Enabled
  - `Export Method = cron`
- Behavior: For eligible stores, fetches orders created within the last `TurnAround Time` days and sends them.

---

### Helpers

#### `Helper\Data`
Configuration accessors for all module settings. Key methods:
- `getShopId($storeId = false)`
- `getShopPw($storeId = false)`
- `getProductReview($storeId = false)`
- `getOrderStatus($storeId = false)` → comma-separated string
- `getIsActive($storeId = false)`
- `getStoreName($storeId = false)` / `getStoreEmail($storeId = false)`
- `getReviewMod($storeId = false)`
- `getProductIdentifier($storeId = false)`
- `getExcludeProducts($storeId = false)`
- `getExportMethod($storeId = false)`
- `getTurnaroundTime($storeId = false)`
- `getActivePrc($storeId = false)` / `getWidgetToken($storeId = false)`
- `isTermsAndConditionsAccepted($storeId = null)`

#### `Helper\OrderData`
Builds request payload and sends to eKomi Plugins Dashboard.
- `getRequiredFields(Order $order, int $storeId): array`
- `sendOrderData(array $orderData): ?string` — sends to `https://plugins-dashboard.ekomiapps.de/api/v1/order` using PUT/post; returns response body or null.
- Extractors used internally:
  - `getOrderData($order, $storeId)` → order, customer, sender info
  - `getAddressData($order)` → first/last name, phone, country
  - `getProductsData($order)` → id, sku, type, name, description, urls, image_url

Payload structure root fields:
- `shop_id`, `interface_password`, `order_data`, `mode`, `product_identifier`, `exclude_products`, `product_reviews`, `plugin_name` (magento)

#### `Helper\Export`
Batch export used by cron.
- `exportOrders(): array` — iterates stores, filters by `export_method=cron`, processes orders.
- `getStores(): array` — id => name
- `getOrders($storeId): array` — orders created since now - `turnaround_time` days
- `processOrders($orders, $storeId): array` — validates status filter and dispatches via `OrderData::sendOrderData`

---

### Admin Controller Rewrite
`Controller\Rewrite\Adminhtml\System\Config\Save`
- Adds a success message after saving configuration prompting merchants to review the plugin on the Marketplace, then calls the core save action.

---

### Widget: Product Review Container
`Block\Widget\Reviews` with `etc/widget.xml` and template `view/frontend/templates/ekomi_sw_prc.phtml`.

Block capabilities:
- `getCurrentProductId()` — returns product ids/skus (handles grouped, bundle, configurable) based on configured identifier.
- `getStoreId()`, `getShopId()`, `getWidgetToken()`
- `isModuleEnabled()`, `isPrcEnabled()`

Widget configuration (`widget.xml`):
- Widget ID: `ekomi_sw_prc`
- Parameter `template`: defaults to `ekomi_sw_prc.phtml`
- Parameter `title`: free text

Frontend template injects the eKomi widget script using `shopId` and `widgetToken` and exposes the product identifiers in the DOM.

---

### ACL
`etc/acl.xml` exposes resource `Ekomi_EkomiIntegration::system_config` under Stores → Configuration permissions.

---

### Module XML
`etc/module.xml` declares the module and setup version `2.5.14`.

DI override in `etc/di.xml`:
- Preferences core `Magento\Config\Controller\Adminhtml\System\Config\Save` → module rewrite controller.

---

### External APIs
The module communicates with the following endpoints:
- Plugins Dashboard Orders API: `https://plugins-dashboard.ekomiapps.de/api/v1/order` (Order export)
- eKomi Get Settings: `http://api.ekomi.de/v3/getSettings` (Credential validation)
- SRR Customer Segments: `https://srr.ekomi.com/api/v1/customer-segments` (Informational check)

Headers used for SRR checks: `shop-id`, `interface-password`. Orders API uses multipart/JSON via cURL.

---

### Notes & Best Practices
- Ensure Terms and Conditions is set to Yes; otherwise exports are skipped.
- When using `Export Method = status`, configure the specific order statuses to avoid unnecessary calls.
- `TurnAround Time` applies to the cron-based export to bound the date range.
- For the widget, enable both Module and PRC flags, and set a valid Widget Token.


