CHANGELOG
---------

### 1.2.2 (2026-09-28)

* Fixed logging workflow: the handler only accepted `INFO` while every call logged at `DEBUG`
* Log files are now `var/log/powerpay/debug.log` (full trace, only when Debug is enabled) and `var/log/powerpay/error.log` (errors and critical failures, always written regardless of the Debug flag). `var/log/powerpay/info.log` is no longer used
* Existing log calls now use the proper level: failed invoices and failed transaction persistence are logged as `CRITICAL`
* API requests and responses (method, URL, payload, HTTP status and body) are logged to `debug.log`; failed responses also go to `error.log`
* Added the "Mask Authorization Token in Logs" setting (enabled by default): the Authorization header is logged as its SHA-256 hash and length, or as the raw value when disabled
* `Helper\Data::log()` keeps its signature and now accepts `critical`, `error`, `warning`, `info` and `debug` as `$type`
* Removed `Improntus\PowerPay\Logger\Logger`, `Improntus\PowerPay\Logger\Handler\CustomHandler` and the unused `PowerPayLogger` virtual type. `Helper\Data` constructor now takes `$debugLogger` and `$errorLogger`

### 1.2.1 (2026-08-24)

* Compatibility with Adobe Commerce and Magento Open Source 2.4.9
* Fixed `etc/webapi.xml`: it declared the schema as a relative path to `app/code/Magento/Webapi/etc/webapi.xsd`, which does not exist on a Composer based installation. Replaced with the `urn:magento:module:Magento_Webapi:etc/webapi.xsd` URN
* Fixed unquoted HTML attributes in `head.phtml` (`href`, `src`) and `productWidget.phtml` (`product-price`, `mo-client-id`), which broke the markup as soon as the value contained a space
* Moved the neutral product widget inline `<style>` block to `SecureHtmlRenderer` for Content Security Policy compliance

### 1.2.0 (2026-07-14)

* Added the Neutral product page widget: a local widget showing "Paga en 3 cuotas de X sin intereses" with the Powerpay logo and an info icon that opens the Powerpay financing modal
* The Neutral widget takes its price from the catalog price info, so the amount is currency-converted, tax-aware and correct for each product type. It is not rendered on grouped products or when the final price is zero
* Fixed constant access through `$this::CONSTANT` instead of `self::CONSTANT`, which is unreliable in Magento's generated classes

### 1.1.11 (2025-06-24)

* Added Adobe Commerce / Magento Open Source 2.4.8 compatibility

### 1.1.10 (2024-05-02)

* Php 8.2 Compatibility

### 1.1.9 (2024-01-05)

* Fixed a bug where the module status was not considered when displaying widgets

### 1.1.8 (2023-11-27)

* Fixed bug when in some cases order item was canceled twice

### 1.1.7 (2023-10-10)

* Error catching in failure and success pages

### 1.1.6 (2023-10-10)

* Minor improvements in transaction data sent

### 1.1.5 (2023-10-03)

* Added transaction commit and rollback in invoice generation to avoid DB inconsistencies in case of exception

### 1.1.4 (2023-10-03)

* Minor retrocompatibility fixes

### 1.1.3 (2023-07-17)

* Cleanup
* Added agreement block in checkout KO template

### 1.1.0 (2023-04-21)

* Initial release
