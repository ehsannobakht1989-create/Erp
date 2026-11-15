# AGENTS.md — Modular Manufacturing ERP Rules

Read this file first at the start of every session.

## 1. Product Goal & Architecture
- One reference ERP containing ~80 self-contained modules (`modules/<id>/`).
- For each factory, a YAML profile (`profiles/*.yaml`) selects modules; `builder/` outputs a deployable package containing only selected modules.
- Industry-agnostic core: never hard-code one industry's assumptions; industry specifics belong in plugins/modules or configuration.
- Adding a module must NEVER require editing any central file. `core/` scans `modules/*/module.json`, `web/shell/` globs `../../modules/*/web/routes.ts`, and `builder/` reads manifests.

## 2. Strict Stack
- **Backend**: Raw PHP 8.2+ native MVC, PDO, NO heavy frameworks (no Laravel/Symfony). Composer only for tiny libs; `vendor/` is git-ignored.
- **Frontend**: React + TypeScript + Vite + Tailwind, RTL-first, Persian UI, Jalali dates. Mock-first: every module ships an API mock so UI works before backend.
- **AI Service**: Python 3.11 + FastAPI (optional). LLM via AvalAI (OpenAI-compatible HTTP API). Offline/off mode must always work.
- **Database**: MySQL/MariaDB. Dev on XAMPP, deploy on cPanel shared hosting (no Docker required).

## 3. Session Scope & Isolation
- **One session = one module.** Touch ONLY `modules/<id>/**` (and `specs/<id>.md` if applicable).
- NEVER edit `core/`, `web/shell/`, `builder/`, or another module.
- If a core change is needed: STOP and write `docs/core-change-requests/<id>.md` instead.

## 4. Module Communication & Data Ownership
- Modules communicate ONLY via:
  1. Events through the core event bus (`emits` / `listens` in `module.json`).
  2. Service interfaces declared in `module.json` (`provides` / `consumes`).
- NEVER join or query another module's database tables.
- Every table name MUST be prefixed with module id: `<module_id>__<table>` (e.g. `inventory__warehouse`).
- Every table MUST include `company_id` (multi-tenant), `created_at`, `updated_at`, and `deleted_at` (soft-delete) where relevant.
- Migrations: forward-only, idempotent SQL in `modules/<id>/backend/migrations/NNN_name.sql`.

## 5. Security, Permissions, Money & Dates
- Permissions format: `<module>.<entity>.<action>` (e.g. `inventory.receipt.approve`). Enforced in backend on every route and mirrored in UI.
- Security: PDO prepared statements only, CSRF protection, strict input validation, output escaping, and audit log on every mutation.
- Money: integer minor units or `DECIMAL(20,4)`; NEVER `float`.
- Dates: stored in UTC, displayed in Jalali.
- Localization: Persian UI strings in `modules/<id>/web/i18n/fa.json`; all code, identifiers, and DB columns in English.

## 6. Budget & Definition of Done
- Repo limits: `< 6,000` files (hard fail `> 7,000`), `< 60 MB` (hard fail `> 90 MB`).
- Per-module budget: `<= 50` files per module (backend ~30, web ~20). No dead code, no giant generated files, no assets `> 100 KB`, no sample data `> 200 KB`.
- Every module must satisfy `docs/definition-of-done.md` before setting `"status": "done"` in `module.json`.

## 7. Ending a Session
1. Run `bash tools/budget.sh` and `php tools/validate-manifests.php`.
2. Ensure both pass.
3. In the PR description, list what is done, what is not done, and any open questions.
