# مشخصات ماژول `item-master` (اطلاعات پایه کالا)

## ۱. موجودیت‌ها (Entities)
- **Item (`item_master__items`)**: `id`, `company_id`, `code` (یکتا در شرکت), `name_fa`, `name_en`, `short_name`, `normalized_name`, `description`, `item_type` (`raw_material|semi_finished|finished_good|consumable|spare_part|packaging|tool|service|asset|scrap`), `category_id`, `base_uom_id`, `purchase_uom_id`, `sales_uom_id`, `issue_uom_id`, `status` (`draft|pending_approval|active|on_hold|obsolete`), `revision`, `is_purchasable`, `is_sellable`, `is_producible`, `is_stockable`, `is_batch_tracked`, `is_serial_tracked`, `has_expiry`, `shelf_life_days`, `retest_days`, `weight`, `volume`, `hs_code`, `tax_category`, `origin_country`, `brand`, `manufacturer_name`, `manufacturer_part_no`, `lead_time_days`, `min_order_qty`, `order_multiple`, `costing_method_hint` (`standard|average|fifo|lot`), `default_warehouse_hint`, `abc_class`, `is_used`, `notes`, `created_at`, `updated_at`, `deleted_at`.
- **ItemCategory (`item_master__categories`)**: درختی (`parent_id`), `code`, `name_fa`, `default_item_type`, `default_uom_id`, `attribute_set_json`.
- **UoM Conversion (`item_master__uom_conversions`)**: `item_id`, `from_uom`, `to_uom`, `factor` (`DECIMAL(20,6)`).
- **ItemBarcode (`item_master__barcodes`)**: `item_id`, `uom`, `barcode` (یکتا در شرکت), `is_primary`.
- **ItemAlternate (`item_master__alternates`)**: `item_id`, `alternate_item_id`, `relation_type` (`interchangeable|one_way|supplier_specific`), `notes`.
- **ItemPartyRef (`item_master__party_refs`)**: `item_id`, `party_id`, `party_type` (`supplier|customer`), `party_part_no`.
- **ItemCodingScheme (`item_master__coding_schemes`)**: `category_id`, `pattern`, `separator`, `padding`.

## ۲. قوانین کسب‌وکار (Business Rules)
- کد کالا (`code`) پس از استفاده شدن کالا (`is_used = 1`) غیرقابل تغییر است.
- پرچم‌های رهگیری (`is_batch_tracked`, `is_serial_tracked`) در صورت وجود موجودی قابل غیرفعال شدن نیستند.
- جلوگیری از تکرار: بررسی تشابه با نرمال‌سازی فارسی (`TextNormalizer`: یکسان‌سازی ی/ي، ک/ك، ارقام، حذف نیم‌فاصله و اعراب).
- فعال‌سازی نیازمند دسته‌بندی، واحد پایه، نوع کالا و نام فارسی است و از طریق گردش کار `item_activation` انجام می‌شود.
- حذف نرم فقط برای کالاهای `draft` استفاده‌نشده مجاز است؛ در غیر این صورت `obsolete`.

## ۳. رویدادها (Events)
- **Emits**: `item-master.item.created`, `item-master.item.updated`, `item-master.item.activated`, `item-master.item.put_on_hold`, `item-master.item.obsoleted`, `item-master.item.merged`, `item-master.item.category_changed`.
- **Listens**: `*.item.used`, `inventory.stock.first_posted`.

## ۴. قرارداد سرویس (`ItemMasterService`)
- `find`, `findByCode`, `search`, `lookup`, `isActive`, `getTracking`, `getUoms`, `convert`, `getAttributes`, `getAlternates`, `markUsed`.

## ۵. دسترسی‌ها (Permissions)
- `item-master.item.view|create|update|delete|approve|import|export|merge`, `item-master.category.manage`, `item-master.coding.manage`, `item-master.alternate.manage`, `item-master.ai.use`.

## ۶. هوش مصنوعی (AI)
- `autofill`, `assistant`, `anomaly`, `summarize` (پیش‌فرض خاموش، فقط پیشنهاد).
