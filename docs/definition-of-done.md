# Definition of Done (Per Module)

A module may only change `"status": "done"` in `modules/<id>/module.json` when ALL of the following are complete:

1. **Backend**:
   - Forward-only, idempotent SQL migrations in `backend/migrations/NNN_name.sql` using `<module_id>__<table>` naming with `company_id`, `created_at`, `updated_at`.
   - Models, full CRUD, status/workflow integration, business rules, and input validation.
   - Prepared statements only, CSRF protection, output escaping, audit log on every mutation.
2. **Events & Permissions**:
   - All `emits` and `listens` events declared in `module.json` are implemented.
   - Permissions (`<module>.<entity>.<action>`) enforced on every backend route and mirrored in UI.
3. **Frontend (React + TS + RTL + Jalali)**:
   - List page (filter, sort, paginate, export), Create/Edit form, Detail view, Key reports, Print layout.
   - Persian UI strings in `web/i18n/fa.json`.
4. **Mocks & OpenAPI**:
   - `modules/<id>/openapi.yaml` (concise) and matching frontend API mocks in `web/mocks/` so UI works without backend.
5. **AI Gateway**:
   - `ai` block implemented via core AI gateway (tools + `ai__proposals`), default OFF, works cleanly with `provider=off`.
6. **Smoke Tests**:
   - Migrations run cleanly, routes respond, and permissions deny unauthorized requests.
7. **Budget & Validation**:
   - `<= 50` files in `modules/<id>/`.
   - `bash tools/budget.sh` and `php tools/validate-manifests.php` pass with zero errors.
   - `"status": "done"` set in `module.json`.
