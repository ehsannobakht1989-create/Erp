# مشخصات ماژول `<MODULE_ID>` (`<MODULE_FA>`)

## ۱. موجودیت‌ها (Entities)
- **EntityName**: `field1 (type)`, `field2 (type)`, ...

## ۲. قوانین کسب‌وکار (Business Rules)
- قانون ۱
- قانون ۲

## ۳. وضعیت‌ها و گردش کار (States & Workflows)
- `draft` → `pending_approval` → `active` → `obsolete`

## ۴. رویدادها (Events)
- **Emits**: `<module>.<entity>.<verb_past>`
- **Listens**: `<other>.<entity>.<verb_past>`

## ۵. قرارداد سرویس (Service Contract)
- `ModuleNameService`: متدهای عمومی برای سایر ماژول‌ها.

## ۶. دسترسی‌ها (Permissions)
- `<module>.<entity>.view|create|update|delete|approve`

## ۷. صفحات و گزارش‌ها (Screens & Reports)
- لیست، فرم، جزئیات، چاپ، ویجت داشبورد.

## ۸. قابلیت‌های هوش مصنوعی (AI Capabilities)
- `autofill` / `assistant` / `summarize` / `anomaly` (پیش‌فرض خاموش).

## ۹. نکات یکپارچه‌سازی و بومی‌سازی ایران
- تاریخ شمسی، ریال/تومان، نرمال‌سازی متن فارسی.
