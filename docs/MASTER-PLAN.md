# MASTER PROMPT — Modular manufacturing ERP (Laravel + React + Python AI)

You are a senior engineering team lead working autonomously in the connected GitHub repository.
Read this whole prompt first. Your first action is to save it verbatim as `docs/MASTER-PLAN.md` (Phase 0), so any future session can continue by reading that file.
Never ask the owner questions; when something is ambiguous choose the simplest industry-neutral behaviour, make it a configurable setting, and record the decision in `docs/decisions.md`.

---------------------------------------------------------------------------
## 1. MISSION

Build ONE reference ERP for manufacturing plants with ~80 modules. The owner will, per target factory, TICK modules in a builder UI, define the factory's processes through configuration (workflows, forms, terminology, posting rules, approval chains), and get a deployable package containing only the ticked modules. Industry is not decided yet: the core must be configuration-driven and industry-neutral; industry specifics come later as small plugins.

Quality bar: every ticked module must be genuinely usable (real business rules, workflows, events, reports, tests), not a CRUD skeleton. A previous attempt produced 79 skeleton modules marked "done" (~100 lines each). That must never happen again: **"done" is decided only by the depth-gate script (section 7), never by you.**

## 2. NON-NEGOTIABLE RULES

1. Honesty: never claim a module/feature is complete unless the gate script passes. Never weaken the gate, delete tests, or add placeholder code to satisfy a metric.
2. One module at a time, fully, in vertical slices, committing after each slice. Do not spread thin across many modules.
3. State lives in the repo, not in your memory: `docs/MASTER-PLAN.md`, `docs/progress.md` (table + `NEXT:` pointer), `docs/decisions.md`, `docs/depth-status.md`.
4. Modules are isolated: touch only `modules/<id>/**` while working on a module. Core/platform changes happen only in the phases that own them, or via `docs/core-change-requests/<topic>.md` when blocked mid-module.
5. Modules talk to each other ONLY through (a) domain events and (b) service contracts (PHP interfaces in `modules/<id>/Contracts`). Never read/write another module's tables, never import another module's models/services directly (only its Contracts, resolved from the container, nullable when the module is optional).
6. Every table is prefixed `<module_id_with_underscores>__` (core tables: `core_`), has `company_id` (tenant), timestamps, soft deletes where records can be "removed".
7. Every mutation is audited and emits the documented domain events. Every route is permission-guarded. Money is DECIMAL(20,4) (brick/math or bcmath), never float. Dates stored UTC, shown Jalali. UI text Persian (RTL), code/identifiers English.
8. AI is optional at three levels (factory profile → module → capability), default OFF. AI never writes business data: it returns PROPOSALS that a human approves. All AI features work with provider=off.
9. Statutory rates, tax/insurance tables, holidays, HS codes, AQL tables, etc. are DATA tables editable by the owner, never hard-coded numbers in logic. External systems (tax authority, SMS, banks) are ADAPTERS behind interfaces with mock implementations; never invent undocumented protocol/crypto details.
10. Repository limits of the Arena platform: ≤ 10,000 tracked files and ≤ 128 MB. Targets: < 6,000 files, < 60 MB. Never commit `vendor/`, `node_modules/`, build output, zips, large images/fonts/sample data. Use `tools/budget.sh` (fail at 7,000 files / 90 MB).
11. Context discipline: your context is finite. After each module (or ~every 2 hours of work) update `docs/progress.md` and commit. If you feel context getting long, STOP after the current commit, write `NEXT: <module or phase>` and tell the owner to start a fresh chat with: "Read docs/MASTER-PLAN.md and docs/progress.md and continue from NEXT."
12. Reports are short: after each module output exactly 3 lines (module id, gate score, one risk/gap). No long summaries.

## 3. STACK AND DEPLOYMENT CONSTRAINTS

- Backend: **Laravel 11+ (PHP 8.2+)**, MySQL/MariaDB (SQLite in-memory for fast tests), Eloquent allowed inside a module for its OWN tables only.
- Frontend: React 18 + TypeScript + Vite + Tailwind, RTL-first Persian UI, Jalali dates, mock-first (every module ships an API mock so the UI runs without backend).
- AI: Python 3.11 FastAPI service (optional, separate deployment) for numeric/ML/OCR; LLM tasks go through the Laravel AI Gateway to **AvalAI** (OpenAI-compatible HTTP API), provider-abstracted.
- Target hosting: **cPanel shared hosting must work** (no Docker, no Redis, no daemons, no SSH assumed): queue driver `database`, scheduler via one cron line (`* * * * * php artisan schedule:run`), queue worker run by the scheduler with `--stop-when-empty --max-time=50`, file cache, local file storage, a web installer for first-time setup. Docker is an optional extra.
- Composer dependency whitelist (anything else needs a line in `docs/decisions.md` with justification): `laravel/sanctum`, `morilog/jalali`, `brick/math`, `openspout/openspout` (xlsx/csv stream), `picqer/php-barcode-generator`, `bacon/qr-code` (or `endroid/qr-code`), `symfony/yaml`. Dev: `phpunit`/`pest`, `larastan`, `laravel/pint`. NPM: react, react-router, react-hook-form, zod, tailwind, vitest, a light chart lib, a virtual-list lib.
- The built package for a factory must include `vendor/` (built with `composer install --no-dev -o`) so cPanel users need no Composer.

## 4. REPOSITORY LAYOUT

```
AGENTS.md                      # short rules for any agent session (derive from sections 2,5,11)
app/ bootstrap/ config/ database/ routes/ public/ storage/ tests/   # Laravel app = CORE PLATFORM
app/Support/Modules/           # ModuleManager, ModuleServiceProvider base, manifest validator
app/Support/{Events,Tenancy,Audit,Numbering,Settings,Money,Jalali,Ai,Datasets,Workflow}/   # core services & contracts
modules/<id>/                  # self-contained module (section 5)
web/shell/                     # React shell (auto-discovers modules/*/web/routes.ts)
ai-service/                    # FastAPI service, handlers per capability
builder/                       # profile resolver + packager (artisan command) + local tick UI
profiles/ plugins/ specs/ docs/ tools/
.github/workflows/ci.yml
```
If the repository already contains parts of this (e.g. `specs/`, `web/shell/`, `ai-service/`, `builder/`, `docs/`, `modules/*/module.json` from an earlier PHP-native attempt): KEEP and adapt them. Remove the old custom PHP kernel (`core/`, `public/index.php`, `bin/erp`) and old per-module PHP backend code once the Laravel core replaces them (Phase 1), keeping each module's `module.json`, `specs/<id>.md`, `web/` UI folder and Contracts (convert namespaces).

## 5. MODULE ANATOMY AND MANIFEST

```
modules/<id>/
  module.json                 # manifest (below)
  src/                        # namespace Modules\<StudlyId>\  (Models, Services, Http/Controllers, Http/Requests, Listeners, Jobs, Policies, Datasets)
  src/Contracts/              # service interfaces + DTOs this module PROVIDES (always packaged, even if module not selected)
  Providers/ModuleServiceProvider.php   # extends the core base; registers routes, migrations, listeners, contracts, permissions, ai tools, datasets
  database/migrations/        # forward-only, idempotent, prefix <id>__
  database/seeders/
  routes/api.php              # mounted automatically at /api/<id>
  workflows/*.json  forms/*.json  print/*.json   # shipped definitions auto-imported on enable
  lang/fa.json
  tests/                      # unit + feature tests (+ architecture tests run globally)
  web/{routes.ts,pages,api,mocks,types,i18n/fa.json}
  openapi.yaml
  README.md                   # ≤ 10 lines
```
`module.json` fields: `id, name{fa,en}, version, category, level, status[stub|partial|done], requires[], optional[], provides[], consumes[], emits[], listens[], permissions[], menu[], entities[], workflows[], settings[], datasets[], regional[], industry_tags[], python(bool), ai{enabled_default, capabilities[{id, provider, tools[], proposals[], redact[], max_tokens_per_call}]}`. Provide JSON Schema `docs/module.schema.json` and a validator (`php artisan erp:modules:validate`) that also checks dependency cycles and naming conventions (events `<module>.<entity>.<verb_past>`, permissions `<module>.<entity>.<action>`).

Hard `requires` are auto-selected by the builder; `optional` links activate extra features through events/contracts when the other module is present and are silently skipped otherwise.

## 6. CORE PLATFORM (Laravel app) — requirements

1. **ModuleManager**: scans `modules/*/module.json`, topologically orders enabled modules (enabled set from generated `config/erp-modules.php`, overridable by table `core_modules`), registers each ModuleServiceProvider; disabled modules contribute nothing (no routes, migrations, listeners, menu). Commands: `erp:modules:list|graph|enable|disable|validate`.
2. **Tenancy**: `BelongsToCompany` trait + global scope using `company_id` from the authenticated context; company/branch/fiscal-year context middleware; tests proving cross-tenant isolation.
3. **Event bus**: `DomainEvent` envelope `{event, version, company_id, entity_type, entity_id, payload, occurred_at, actor_id, correlation_id}` on top of Laravel events; dispatch after commit; async listeners via the database queue with an **inbox table** for idempotency (`core_event_inbox(listener, event_id)`), retries with backoff, dead-letter status, event catalog generated from manifests (`docs/event-catalog.md`).
4. **Service contracts** in `app/Support/Contracts` (Auth, Permission, Company, Audit, Notifier, AiGateway, Workflow, DynamicField, Print, Numbering, Dataset) with null/stub bindings so any module runs alone in tests.
5. **Auth**: Sanctum tokens + session for the SPA; permission checks via Laravel Gate/Policies using a permission registry collected from manifests; data-scope rules (own/department/branch/all) available to repositories through a scope contract.
6. **Audit**: `Auditable` trait (before/after diff, hash chain handled by the audit-log module); audit hook fires automatically on model events.
7. **Numbering service** (per company/branch/fiscal year, Jalali-aware, gap-safe with locking), **Settings service** (typed per company per module from manifest `settings`), **Money** (brick/math), **Jalali** helpers (morilog), response envelope `{ok,data,error,meta}`, exception handler, validation messages in Persian.
8. **Datasets**: `DatasetProvider` contract + registry; modules declare read-only datasets in manifests; report-bi/AI read other modules' data ONLY through datasets with permission and row-scope enforcement.
9. **Workflow/forms/print/notification engines** are PLATFORM MODULES (workflow-engine, form-builder, print-barcode-qr, notifications); the core only defines their contracts.
10. **AI Gateway** (core): providers `off | avalai | python`; capability lookup from manifests; three-level enable check; tool registry from manifests, executed with the CURRENT user's permissions; proposals table (`core_ai_proposals`), usage/cost table with budgets; field redaction before external calls; audit of every call; HMAC-signed requests to the Python service; clean "AI disabled" results when off.
11. **Architecture tests** (run in CI): no module imports another module's classes except `Contracts`; no query/model touches a table outside its prefix; every route has a permission middleware; every mutation path emits an event/audit; manifests valid; no `DB::raw`/string-built SQL with user input; no banned markers.
12. **Scheduler/queue**: schedule definitions (event queue drain, outbox/inbox maintenance, module-registered scheduled jobs) so a single cron line runs everything.
13. **Web installer** (`/install`, locked after use): checks requirements, DB connection, runs migrations for the enabled modules, seeds roles/super-admin, writes `.env`, shows cron instructions.

## 7. DEPTH GATE (build this in Phase 0/1, before any module work)

Create `tools/depth-check.php [<module-id>|--all] [--gate] [--json]` (standalone PHP, no app boot). For each module compute from `specs/<id>.md` and the code:
- entity_cov (20): spec entities ↔ migration tables/columns.
- event_cov (15): spec "Events out" emitted in code; "Events in" registered AND handled non-trivially (idempotent).
- contract_cov (20): every Contract method implemented non-trivially (not empty/throw/`return null|[]|0` only).
- rule_cov (20): test assertions vs spec "Rules" (≥ 2 assertions per rule; named test per rule keyword group).
- perm_cov (10): manifest permissions enforced by routes AND used by UI routes.
- ui_cov (10): list + form + detail pages per main entity, routes registered, fa.json keys.
- ai_cov (5): each declared AI capability has tools + tests (provider=off and fake provider).
Anti-gaming: banned markers (`TODO|FIXME|NotImplemented|not implemented|placeholder|stub|lorem|dummy`) in module code; trivial/empty methods; tests asserting nothing; ≥ 80 % generic-CRUD routes when the spec has business rules; mock data in backend paths.
**A module may carry `"status":"done"` only if score ≥ 80, every category ≥ 60, anti-gaming clean, and its tests pass.** CI job `depth-gate` fails the build if any module says `done` without passing. Output `docs/depth-status.md` with, for every module, the exact list of what is still missing. Status `partial` = some implementation, `stub` = manifest only.

## 8. PHASES (follow in order; each ends with passing CI and an updated `docs/progress.md`)

**Phase 0 — Orientation (≤ 30 min):** save this prompt as `docs/MASTER-PLAN.md`; inspect the repo; create `docs/progress.md`, `docs/decisions.md`, `AGENTS.md`, `.gitignore`, `tools/budget.sh`; record what exists and what will be kept/removed.

**Phase 1 — Laravel core + gate (1 session):** scaffold Laravel; implement section 6 items 1–8, 10–13 (contracts and bindings; engines remain modules); module loader; manifest schema/validator; architecture tests; depth-check tool and CI (`budget`, `validate manifests`, `pint`, `phpstan`, `phpunit`, `depth-gate`, shell `typecheck/build`); remove the old PHP-native kernel; adapt `modules/*/module.json` and Contracts to Laravel namespaces; mark all modules honestly `stub`/`partial` per the gate.

**Phase 2 — React shell (1 session):** `web/shell` (reuse if present, adapt to Laravel API/Sanctum): module auto-discovery with profile filtering (`ERP_MODULES`), `@erp/ui` kit (DataTable server-side with saved views/export/virtualization, FormKit, DetailLayout, Jalali DatePicker, Money/Number inputs, Modal/Drawer, Toast, Stepper, FileUpload, ReportViewer, charts), hooks, i18n, mock mode, dark/light, command palette, AI UI primitives (assistant panel, proposal card, AI inbox). Document in `docs/ui-kit.md`.

**Phase 3 — Builder + tick UI + packaging (1 session):** profile YAML/JSON schema; resolver (auto-add hard deps, report soft links, cycle checks, Python-needed detection); packager (`php artisan erp:build --profile=...`): Laravel app + only selected modules + all modules' `Contracts/`, React bundle built for the selected modules, generated `config/erp-modules.php`, `.env.example`, `vendor/` included, web installer, `install.sql` when a MySQL is available at build time, Persian `INSTALL-FA.md`, `BUILD-REPORT.md` (modules, auto-added deps, non-`done` modules WARNING, AI matrix, file count/size); local tick UI (list by category with auto-ticked dependencies and reasons, AI toggles per module/capability, presets minimal/standard/full, save/build/download); example profiles; industry plugin skeleton (`plugins/generic`).

**Phase 4 — Platform modules, Tier 0 (deep):** audit-log, workflow-engine, dynamic-fields, form-builder, multi-company, auth-rbac, notifications, document-mgmt, print-barcode-qr, api-webhooks, report-bi. These implement the platform contracts. Order matters: audit-log, multi-company, auth-rbac first.

**Phase 5 — Business spine, Tier 1 (deep):** org-structure, party-master, item-master, inventory, accounting-gl, purchase-request, rfq, purchase-order, sales, pricing, ar-ap, treasury, bom, routing, work-order, mrp, mps, costing, quality-inspection. After this tier run **S1/S2 integration scenarios** (section 10) before continuing.

**Phase 6 — Tier 2 (deep):** hr-core, attendance, payroll, asset-registry, cmms, mes, scheduling, oee, reorder-point, fixed-assets, budgeting, scrap-byproduct, lot-traceability, ncr-capa, tooling, batch-formulation, calibration, lims, eco-plm, tax-moadian (official docs only; mock adapter otherwise), supplier-evaluation, contracts, crm, project-mgmt.

**Phase 7 — Tier 3:** remaining modules (light/low-code ones: ticketing, warranty-after-sales, correspondence, weighbridge, canteen, parking-vehicles, gate-visitor, energy-utilities, waste-environment, contractor-mgmt, shipping-distribution, import-customs, forex-lc, hse, training-lms, recruitment, competency-appraisal, management-consolidation, audit-iso, condition-monitoring-iot) using form-builder/workflow-engine definitions first, custom code only where required.

**Phase 8 — AI modules + Python service:** FastAPI service (HMAC auth, handler registry, JSON schemas, health/capabilities, job endpoints, tests with synthetic data) and modules demand-forecast, failure-prediction, anomaly-detection (deterministic PHP rules work without Python), nl-report-assistant (LLM outputs a validated QuerySpec over the dataset catalog, never SQL), doc-ocr (Tesseract fa+en pipeline, review UI, proposals only), schedule-optimizer (OR-Tools CP-SAT with calendars/setups/locks).

**Phase 9 — Hardening:** S3 security review + static linters; S4 build every profile, install on empty DB, seed-load benchmarks, EXPLAIN heavy queries, chunked/resumable long jobs; S5 Persian docs (`INSTALL-FA`, `ADMIN-GUIDE-FA`, `docs/process-definition-guide.md` = how to onboard a factory with configuration only, generated per-module quick references, glossary); S6 AI end-to-end verification; S7 i18n/RTL/UX sweep.

Phases 4–8 are executed with the per-module procedure below. Work tier by tier; do not start a module of the next tier while a module of the current tier is below the gate unless the owner says so. If time is limited the owner may stop after Tier 1; remaining modules stay honestly `stub`/`partial` and are deepened on demand when a real factory ticks them.

## 9. PER-MODULE PROCEDURE (apply to every module in phases 4–8)

0. **Spec first.** If `specs/<id>.md` exists, it is the source of truth. If not, write it (≥ 80 lines) using the standard sections: Purpose · Entities (fields, types) · States/Workflows · Rules · Events out/in · Service contract (method signatures) · Permissions · Screens/Reports · AI (capabilities, tools, proposals, redact) · Datasets · Integration & edge cases · Iran notes. Use the catalog (section 11) and conventions (section 12). Generate PHP interfaces for the Service contract in `src/Contracts` and update the manifest.
1. **Slice 1 – Schema:** all entities as migrations (keys, indexes, constraints, company_id, soft deletes), models/repositories.
2. **Slice 2 – Domain rules:** implement EVERY rule in services; one named test per rule with a happy path and at least one failure path.
3. **Slice 3 – Workflows & events:** ship workflow/form definitions, emit all documented events with payloads, real idempotent handlers for every consumed event.
4. **Slice 4 – Contracts:** implement the full provided service contracts with tests; consumers use optional providers through nullable resolution.
5. **Slice 5 – API:** per-entity and per-action endpoints (not just generic CRUD), filtering/pagination/bulk/import/export per spec, permission guards, audit, OpenAPI.
6. **Slice 6 – UI:** list + form + detail per main entity, workflow action buttons/status chips, reports/screens from the spec, Persian i18n, RTL, mocks matching OpenAPI, permissions on routes.
7. **Slice 7 – AI block & datasets:** tools (read-only), proposals, redaction, datasets; tests with provider=off and a fake provider.
After EVERY slice: run tests, `php tools/depth-check.php <id>`, commit (`<id>: slice N …`). Loop on the gate's "missing" list until it passes; then set `done`, update `docs/progress.md`, print the 3-line report, continue with the next module. Budget per module: ≤ 80 files.

## 10. INTEGRATION SCENARIOS (create `tools/integration` harness; run at the indicated points; failures → fix in the owning module or log in `docs/integration-issues.md`)

- After Tier 0+1 (S1/S2 part 1): procure-to-pay (PR → RFQ → PO → receipt with lot → supplier invoice 3-way match → payment/cheque → balanced journals); order-to-cash (quote → order with credit check/reservation → delivery → issue → invoice → receipt incl. bounced cheque → journals); stock control (count, adjustment, two-step transfer, FEFO); permission matrix (role without permission is denied).
- After Tier 1/2 production modules: plan-to-produce (forecast → MPS → MRP with multi-level BOM → planned orders → work orders → backflush/manual issue → MES reports → output/by-product/scrap → close → costing run with hand-computed expected values → journals); quality loop (IQC → reject → NCR → SCAR → CAPA; calibration overdue blocks use; recall via lot genealogy); maintenance loop (meter → PM → WO with parts → downtime → availability).
- Hire-to-pay (hire → shifts → attendance → leave → period close → payroll with sample tables → payment file → journals).
Harness rules: temp DB, seed only through module services/APIs, run the queue until idle, assert invariants (balanced journals, stock = ledger, no orphan events).

## 11. MODULE CATALOG (~80). Format: id (level) — purpose; requires | optional

PLATFORM L0: auth-rbac — users, login/2FA, sessions/tokens, roles, permission matrix, data scopes, field-level hints; | — · multi-company — companies, branches, fiscal years, currencies/rates, UoM conversions, numbering sequences, settings screen | auth-rbac · audit-log — append-only trail with hash chain, history component, search | — · dynamic-fields — custom fields per entity (EAV + snapshot), designer, form hooks | — · workflow-engine — states/transitions/guards/actions/SLA/approvals/delegation, task inbox, auto-import of module workflow files | auth-rbac · form-builder — declarative form/page/list definitions, renderer, validator, versions | dynamic-fields · notifications — in-app/email/SMS adapters (Kavenegar, Melipayamak, Ghasedak)/Telegram, templates, rules, delivery log | — · document-mgmt — attachments, versions, ACL, expiry, secure download, OCR hook | — · print-barcode-qr — print templates (Jalali, RTL, A4/A5/labels), Code128/EAN13/QR | — · api-webhooks — API keys/scopes, outgoing/incoming webhooks, OpenAPI aggregation | — · report-bi — dataset-based report designer, dashboards, KPIs, schedules, snapshots, embeds | all datasets.
MASTER DATA L1: org-structure — org tree, positions, cost centers, calendars (Jalali holidays as data), shifts | — · party-master — customers/suppliers/contractors, Iranian ID/IBAN validation, addresses/banks/contacts, groups, payment terms, duplicate detection | — · item-master — items, categories with attribute sets, UoM conversions, barcodes, alternates, coding schemes, duplicate detection (Persian normalization), activation workflow | dynamic-fields.
SUPPLY CHAIN L2: inventory — warehouses/locations, immutable stock ledger, balances, lots/serials/expiry, receipt/issue/transfer/adjustment/count docs, reservations, FEFO, reversal | item-master, org-structure · purchase-request — PR with approvals, consolidation, auto-created from MRP/reorder · rfq — inquiries, quote comparison matrix, award · purchase-order — POs, approval thresholds, revisions, receipt tolerance, blanket orders, charges · sales — quotation, order, delivery, invoice, returns, credit check, reservations · pricing — price lists, rules engine, campaigns, margin guard · reorder-point — min/max/ROP, ABC/XYZ, dead stock · supplier-evaluation — scorecards, qualification, approved supplier list · contracts — contract registry, clauses/templates, milestones, renewals · shipping-distribution — shipments, vehicles, waybills, proof of delivery, freight cost · import-customs — import files, customs, landed cost · forex-lc — LCs, guarantees, FX deals, exposure · contractor-mgmt — contractors, work packages, progress statements, retention · weighbridge — truck scale tickets, tolerance, local agent · gate-visitor — visitors, vehicles, material passes · parking-vehicles — fleet, trips, fuel, documents.
PRODUCTION L3/L4: bom — multi-level BOM, revisions, substitutes, formulas, by-products, where-used | item-master · routing — operations, work centers, times/rates, alternates | org-structure · work-order — production orders, snapshots, issue/backflush, outputs, close, rework | bom, routing, inventory · mps — master schedule, ATP, rough-cut | bom · mrp — netting, lot sizing, pegging, planned orders (chunked/resumable) | bom, inventory · scheduling — finite-capacity scheduling, Gantt | work-order, routing · mes — operator terminals, shop-floor reports, downtime, andon, machine tags | work-order · oee — OEE & loss analysis | mes · batch-formulation — formulas, scaling, batch records, signatures | work-order · tooling — tools/dies life tracking · scrap-byproduct — scrap reasons, recycling loops · eco-plm — engineering changes with effectivity | bom, routing · lot-traceability — genealogy graph, recall | inventory · calibration — instruments & calibration with impact assessment.
QUALITY L3/L4: quality-inspection — inspection plans (AQL), IQC/IPQC/FQC lots, usage decisions, certificates, SPC | item-master · ncr-capa — NCR/8D/CAPA/SCAR · lims — samples, methods, specs, results, OOS, COA.
MAINTENANCE L2/L3: asset-registry — equipment tree, meters, spares | org-structure · cmms — requests, work orders, PM (calendar/meter), downtime, MTBF/MTTR | asset-registry · condition-monitoring-iot — sensors, batched ingestion, rollups, alarms (retention policies) | asset-registry.
FINANCE L3/L5: accounting-gl — chart (کل/معین/تفصیلی), journals, posting-rule engine, periods, closing, trial balance, ledgers | multi-company · ar-ap — invoices, 3-way match, payments, allocation, aging | accounting-gl, party-master · treasury — banks, cash, petty cash, cheque lifecycle (Sayad), reconciliation | accounting-gl · fixed-assets — register, depreciation (rates as data), disposal | accounting-gl · costing — standard/actual costing, overhead, WIP, variances, landed cost | work-order, inventory, accounting-gl · budgeting — budgets, commitments, checks | accounting-gl · tax-moadian — Iranian e-invoicing adapter (official docs only; mock adapter) | sales, accounting-gl · management-consolidation — management P&L, profitability, packs | report-bi.
HR L2/L3: hr-core — employees, employment history, events | org-structure · attendance — shifts, time logs, leave, overtime, period close | hr-core · payroll — structures, runs, tax/insurance tables as data, loans, payslips | hr-core, attendance, accounting-gl · recruitment — requisitions, pipeline, hire handoff · training-lms — courses, sessions, compliance matrix, quizzes · competency-appraisal — competency models, assessments, gaps.
SUPPORT L2/L5: crm — leads, opportunities, activities | party-master · project-mgmt — WBS, Gantt, timesheets, EVM · ticketing — helpdesk with SLA · hse — incidents, risk assessments, inspections, permits to work, PPE · warranty-after-sales — warranty registration, claims/RMA | sales · correspondence — secretariat letters, referrals (Jalali numbering) | document-mgmt · canteen — menus, reservations, kiosk, charges · energy-utilities — meters, tariffs, bills, allocation, SEC · waste-environment — waste streams, manifests, measurements, licenses · audit-iso — ISO clauses, audits, findings, management review, risk register.
AI L6 (Python/LLM, all optional): demand-forecast — time-series forecasting as proposals | report-bi · failure-prediction — predictive maintenance risk scores | cmms · anomaly-detection — rule+ML anomaly engine over datasets | report-bi · nl-report-assistant — NL → validated QuerySpec → reports | report-bi · doc-ocr — document extraction with review | document-mgmt · schedule-optimizer — OR-Tools optimization of schedules | scheduling.

## 12. CROSS-MODULE CONVENTIONS

- Event chain examples: `purchase-order.po.approved` → inventory expected receipts; `inventory.receipt.posted` → purchase-order received qty, costing, accounting journal request; `sales.delivery.requested` → inventory issue draft; `sales.invoice.issued` → ar-ap, accounting, tax-moadian; `work-order.output.received` → inventory, costing, lot-traceability; `quality-inspection.lot.decided` → inventory stock bucket; `attendance.period.closed` → payroll; `payroll.run.approved` → accounting/treasury.
- All accounting postings go through `AccountingService::requestJournal()` + a default, editable posting-rule pack; journals always balance; period locks respected.
- Inventory is the only writer of stock; other modules call `InventoryService::postMovement()` (idempotency key) or emit events.
- Numbering, approvals, notifications, attachments, print, audit and dynamic fields come from platform modules, never re-implemented.
- Persian text normalization (ی/ي, ک/ك, digits, ZWNJ) is shared (service in item-master or core support) for search/duplicate detection.
- Iran notes: Jalali calendar/fiscal year, Saturday-first week, ریال/تومان display setting, کد ملی/شناسه ملی/کد اقتصادی/شبا/صیاد validation, سند موقت/دائمی, چک lifecycle, صورت‌وضعیت پیمانکار, VAT/withholding/insurance/tax as data tables, Moadian fields stored in sales but submitted only by tax-moadian.
- Heavy jobs (MRP, cost runs, imports, reports, ingestion) are chunked and resumable through the database queue; long requests are forbidden.

## 13. DEFINITION OF DONE FOR THE WHOLE PROJECT (what the owner can verify)

1. CI green: budget, manifests, pint, phpstan, phpunit (incl. architecture tests), shell typecheck/build, depth-gate.
2. `docs/depth-status.md` shows which modules PASS; modules not passing are honestly `partial`/`stub` and appear as WARNING in BUILD-REPORT.
3. The tick UI builds the `minimal` and `standard` profiles into zips that install on an empty cPanel/XAMPP via the web installer, boot, and show only ticked modules.
4. Integration scenarios (section 10) pass for the Tier 0–1 spine.
5. With AI off everything works; with a fake provider the AI proposal flows pass tests.

Start now with Phase 0. Report in the 3-line format and keep going until you must stop for context, writing `NEXT:` in `docs/progress.md` when you do.
