# Progress

| Phase | Status | Evidence / remaining work |
|---|---|---|
| 0 — Orientation | In progress | Master plan saved; inventory and transition decisions recorded. Budget and legacy smoke checks pass; full legacy CI not verified on native PHP. |
| 1 — Laravel core + depth gate | In progress | Downgraded 79 legacy `done` claims to `partial`, inventoried known gaps, and added a tested temporary fail-closed CI status guard. Existing backend is custom PHP, not Laravel; the new depth gate does not exist. |
| 2 — React shell | Not started | Existing React shell retained for adaptation. |
| 3 — Builder | Not started | Existing builder and profiles retained for adaptation. |
| 4–9 — Modules, AI and hardening | Not started | No module has passed the required new depth gate. |

## Inventory

At baseline: 1,172 tracked files, 79 module manifests, 70 module specs plus a template, custom `core/` kernel and `bin/erp`, existing React shell, builder, profiles, Python AI service, schema and CI. Some platform specs (including audit-log, multi-company, auth-rbac) are missing. All 79 manifests originally reported `done`, an unverified legacy assertion; they now report `partial`. The current CI runs the old PHP test runner and builder; it is not the requested Laravel CI.

Local environment: Node 22 and Python 3 are present; native PHP and Composer are absent from PATH. A temporary npm PHP-WASM CLI (PHP 8.5, warns that Node 24 is required and does not propagate PHP exit codes) ran the old manifest validator, 89 legacy tests and minimal builder successfully; this is not native PHP or proof of Laravel readiness. Existing shell typecheck, one test and build pass. `npm ci` reported 8 vulnerabilities (5 moderate, 1 high, 2 critical). Budget boundary tests pass. Full CI remains unverified.

NEXT: Phase 1 — establish native PHP/Composer tooling, then build and test the independent depth gate and Laravel core incrementally; preserve old kernel until replaced.
