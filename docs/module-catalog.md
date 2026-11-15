# Module Catalog

Total modules: **79**

| ID | Persian Name | Level | Category | Requires | Optional | Python | Regional |
|---|---|---|---|---|---|---|---|
| `auth-rbac` | احراز هویت و دسترسی | L0 | platform | - | - | no | - |
| `multi-company` | چندشرکتی | L0 | platform | - | - | no | - |
| `audit-log` | گزارش ممیزی | L0 | platform | - | - | no | - |
| `dynamic-fields` | فیلدهای پویا | L0 | platform | - | - | no | - |
| `workflow-engine` | موتور گردش کار | L0 | platform | - | - | no | - |
| `form-builder` | فرم‌ساز | L0 | platform | - | - | no | - |
| `notifications` | اعلان‌ها | L0 | platform | - | - | no | - |
| `document-mgmt` | مدیریت اسناد | L0 | platform | - | - | no | - |
| `print-barcode-qr` | چاپ و بارکد | L0 | platform | - | - | no | - |
| `api-webhooks` | وب‌هوک و API | L0 | platform | - | - | no | - |
| `report-bi` | گزارش‌ساز و هوش تجاری | L0 | platform | - | - | no | - |
| `item-master` | اطلاعات پایه کالا | L1 | master-data | - | - | no | - |
| `party-master` | اشخاص و طرف‌حساب‌ها | L1 | master-data | - | - | no | - |
| `org-structure` | ساختار سازمانی | L1 | master-data | - | - | no | - |
| `inventory` | انبار | L2 | supply | item-master | accounting-gl | no | - |
| `purchase-request` | درخواست خرید | L2 | supply | item-master | org-structure | no | - |
| `rfq` | استعلام بها | L2 | supply | item-master, party-master | purchase-request | no | - |
| `purchase-order` | سفارش خرید | L2 | supply | item-master, party-master, inventory | rfq, accounting-gl | no | - |
| `sales` | فروش | L2 | commercial | item-master, party-master, inventory | pricing, accounting-gl | no | - |
| `pricing` | قیمت‌گذاری | L2 | commercial | item-master, party-master | - | no | - |
| `hr-core` | منابع انسانی | L2 | hr | org-structure | - | no | - |
| `attendance` | حضور و غیاب | L2 | hr | org-structure | hr-core | no | - |
| `asset-registry` | ثبت دارایی‌ها و تجهیزات | L2 | maintenance | - | org-structure | no | - |
| `hse` | ایمنی و بهداشت | L2 | operations | - | hr-core, asset-registry | no | - |
| `crm` | مدیریت ارتباط با مشتری | L2 | commercial | party-master | sales | no | - |
| `project-mgmt` | مدیریت پروژه | L2 | operations | - | org-structure | no | - |
| `ticketing` | تیکتینگ و پشتیبانی | L2 | operations | - | party-master | no | - |
| `weighbridge` | باسکول | L2 | support | form-builder, workflow-engine | inventory | no | ir |
| `canteen` | رستوران و تغذیه | L2 | support | form-builder, workflow-engine | hr-core | no | - |
| `parking-vehicles` | پارکینگ و خودروها | L2 | support | form-builder, workflow-engine | hr-core | no | - |
| `gate-visitor` | نگهبانی و مراجعین | L2 | support | form-builder, workflow-engine | - | no | - |
| `correspondence` | دبیرخانه و مکاتبات | L2 | support | form-builder, workflow-engine, document-mgmt | org-structure | no | ir |
| `warranty-after-sales` | گارانتی و خدمات پس از فروش | L2 | support | form-builder, workflow-engine | sales, ticketing | no | - |
| `energy-utilities` | انرژی و تاسیسات | L2 | support | form-builder, workflow-engine | asset-registry | no | - |
| `waste-environment` | پسماند و محیط زیست | L2 | support | form-builder, workflow-engine | hse | no | - |
| `management-consolidation` | تلفیق مدیریتی | L2 | support | multi-company, report-bi | accounting-gl | no | - |
| `contractor-mgmt` | مدیریت پیمانکاران | L2 | support | form-builder, workflow-engine, party-master | hse | no | - |
| `supplier-evaluation` | ارزیابی تامین‌کنندگان | L2 | support | form-builder, workflow-engine, party-master | purchase-order | no | - |
| `contracts` | مدیریت قراردادها | L2 | support | form-builder, workflow-engine, document-mgmt | party-master | no | - |
| `import-customs` | واردات و گمرک | L2 | support | form-builder, workflow-engine | purchase-order | no | ir |
| `forex-lc` | ارز و اعتبار اسنادی | L2 | support | form-builder, workflow-engine | purchase-order, treasury | no | ir |
| `shipping-distribution` | حمل و توزیع | L2 | support | form-builder, workflow-engine | sales, inventory | no | - |
| `audit-iso` | ممیزی ایزو | L2 | support | form-builder, workflow-engine, document-mgmt | - | no | - |
| `recruitment` | استخدام | L2 | support | form-builder, workflow-engine | hr-core | no | - |
| `training-lms` | آموزش | L2 | support | form-builder, workflow-engine | hr-core | no | - |
| `competency-appraisal` | ارزیابی شایستگی | L2 | support | form-builder, workflow-engine | hr-core | no | - |
| `bom` | ساختار محصول | L3 | engineering | item-master | - | no | - |
| `routing` | مسیر تولید | L3 | engineering | item-master | asset-registry | no | - |
| `accounting-gl` | حسابداری کل | L3 | finance | - | multi-company | no | - |
| `ar-ap` | حساب‌های دریافتنی و پرداختنی | L3 | finance | party-master, accounting-gl | - | no | - |
| `treasury` | خزانه‌داری | L3 | finance | accounting-gl | ar-ap | no | ir |
| `fixed-assets` | دارایی‌های ثابت | L3 | finance | asset-registry, accounting-gl | - | no | - |
| `payroll` | حقوق و دستمزد | L3 | hr | hr-core, attendance, accounting-gl | - | no | ir |
| `cmms` | نگهداری و تعمیرات | L3 | maintenance | asset-registry, inventory | - | no | - |
| `quality-inspection` | کنترل کیفیت | L3 | quality | inventory | item-master | no | - |
| `work-order` | دستور کار تولید | L4 | manufacturing | bom, routing, inventory | - | no | - |
| `mrp` | برنامه‌ریزی نیازمندی مواد | L4 | manufacturing | bom, inventory, purchase-order, sales | - | no | - |
| `mps` | برنامه‌ریزی جامع تولید | L4 | manufacturing | bom, inventory, sales | - | no | - |
| `scheduling` | زمان‌بندی تولید | L4 | manufacturing | work-order, routing, asset-registry | - | no | - |
| `mes` | اجرای تولید | L4 | manufacturing | work-order, asset-registry | - | no | - |
| `oee` | اثربخشی کلی تجهیزات | L4 | manufacturing | asset-registry, mes | - | no | - |
| `batch-formulation` | فرمولاسیون و بچ | L4 | manufacturing | bom, inventory | - | no | - |
| `tooling` | ابزار و قالب | L4 | manufacturing | asset-registry, inventory | - | no | - |
| `scrap-byproduct` | ضایعات و محصولات فرعی | L4 | manufacturing | work-order, inventory | - | no | - |
| `ncr-capa` | عدم انطباق و اقدام اصلاحی | L4 | quality | quality-inspection | - | no | - |
| `lims` | مدیریت آزمایشگاه | L4 | quality | quality-inspection | - | no | - |
| `lot-traceability` | ردیابی بچ و لات | L4 | quality | inventory, work-order | - | no | - |
| `reorder-point` | نقطه سفارش | L4 | supply | inventory | purchase-request | no | - |
| `costing` | بهای تمام‌شده | L5 | finance | work-order, inventory, payroll, fixed-assets, accounting-gl | - | no | - |
| `budgeting` | بودجه‌بندی | L5 | finance | accounting-gl | - | no | - |
| `tax-moadian` | مالیات و سامانه مودیان | L5 | finance | sales, accounting-gl | - | no | ir |
| `eco-plm` | تغییرات مهندسی | L5 | engineering | bom, routing | - | no | - |
| `condition-monitoring-iot` | پایش وضعیت و IoT | L5 | maintenance | asset-registry, mes | - | no | - |
| `demand-forecast` | پیش‌بینی تقاضا | L6 | ai | sales, inventory | - | yes | - |
| `failure-prediction` | پیش‌بینی خرابی | L6 | ai | cmms, asset-registry | - | yes | - |
| `anomaly-detection` | تشخیص ناهنجاری | L6 | ai | accounting-gl, inventory | - | yes | - |
| `nl-report-assistant` | دستیار گزارش‌گیری | L6 | ai | report-bi | - | yes | - |
| `doc-ocr` | نویسه‌خوان اسناد | L6 | ai | document-mgmt | - | yes | - |
| `schedule-optimizer` | بهینه‌ساز زمان‌بندی | L6 | ai | scheduling | - | yes | - |
