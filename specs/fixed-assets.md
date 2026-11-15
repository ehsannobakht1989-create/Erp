# fixed-assets — Capital assets & depreciation (L3; requires accounting-gl; optional asset-registry, purchase-order)

**Entities** FixedAsset{code,name,category_id,acquisition_date,in_service_date,cost,salvage,useful_life,method,status[draft|in_service|disposed|scrapped],cost_center_id,location,custodian,asset_registry_ref?,source_doc}; AssetCategory{method,rate/life,gl accounts keys(asset,accum_dep,expense,gain_loss)}; DepreciationRun{period,lines,status}; Disposal{type[sale|scrap|donation|loss],date,proceeds,gain_loss}; Revaluation; Transfer; PhysicalCount; Insurance.
**Rules** depreciation methods straight-line, declining balance, units-of-production (meter from asset-registry), custom tax tables via settings by year (no hard-coded rates); first/last-period conventions (monthly, daily); no depreciation after disposal date; run is idempotent per period and posts via journal request; capitalization from PO/invoice lines flagged "asset" (event), additions to existing asset (improvements) increase cost.
**Events out** fixed-assets.asset.capitalized|disposed, depreciation.posted. **In** ar-ap.invoice.posted (asset-flagged lines), accounting-gl.period.closed.
**Service contract** FixedAssetService: bookValue(asset,asOf), schedule(asset), registerFromInvoiceLine(dto), runDepreciation(period,dryRun).
**Permissions** fixed-assets.asset.view|create|update|dispose|revalue, fixed-assets.depreciation.run|approve, fixed-assets.category.manage.
**Screens** register list, asset card (value timeline), depreciation run wizard (preview→post), disposal wizard, schedule & forecast report, additions/disposals report, physical count sheet, label print.
**AI** anomaly (assets with abnormal cost, missing in count), forecast depreciation expense, autofill category from description.
**Iran** تهیه جدول استهلاک بر اساس ماده ۱۴۹ قانون مالیات‌های مستقیم as DATA tables the user can edit (rates/life by category and year), Jalali periods.
