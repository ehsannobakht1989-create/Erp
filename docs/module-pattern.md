# Module pattern (Laravel)

Follow `docs/MASTER-PLAN.md` §5 for the anatomy and `docs/core-api.md` for the provider hooks. Conventions the depth gate (`tools/depth-check.php`) measures:

- `src/Contracts/` — service interfaces this module provides (namespace `Modules\<Studly>\Contracts`); implemented by a class under `src/`.
- `database/migrations/` — one table per spec entity, named `<id_with_underscores>__<snake plural>`; columns named after spec fields (`field` or `field_id`).
- Events out are created with `DomainEvent::create('<id>.<entity>.<verb_past>', ...)` in `src/` and declared in manifest `emits`; events in are registered in `listeners()` and declared in `listens`.
- `routes/api.php` — permission-guarded routes (`->middleware('can:<id>.<entity>.<action>')`); avoid purely generic CRUD when the spec has business rules.
- `tests/` — PHPUnit tests; name rule tests `test_rule_<keywords>_...` using at least two keywords of the spec rule and assert at least twice per rule; AI capability tests `test_ai_<capability>_off_...` and `test_ai_<capability>_fake_...`.
- `web/routes.ts` — `{ path, component, permission }` entries; per main entity `web/pages/<Studly>ListPage.tsx`, `<Studly>FormPage.tsx`, `<Studly>DetailPage.tsx`; `web/i18n/fa.json` keys under `<entity>.` and for every `t('key')` used.
- Never: banned markers (`TODO`, `FIXME`, placeholder text, stubs), empty or constant-only methods, tests without assertions, mock data outside `web/`, `tests/` and `database/factories/`.
