# Agent instructions

Read `docs/MASTER-PLAN.md`, `docs/progress.md`, and `docs/decisions.md` before starting; continue from `NEXT:`. The master plan takes precedence over historical documentation.

- Follow phases in order. Keep existing assets (specs, UI, AI service, builder, manifests) while adapting them; the legacy PHP kernel was removed in Phase 1 (last present in `5b67c50`).
- In phases 4–8 work on one module at a time, spec first, in the prescribed vertical slices. Outside planning/progress and documented core change requests, touch only that module. Commit after each slice. Run its tests and depth check each time.
- Never mark a module `done` without a passing depth gate and tests. Do not weaken checks, remove tests, or add filler. Record gaps honestly.
- Modules communicate only through events and contracts, never other modules' implementation or tables. Enforce tenant isolation, permission guards, audit and documented events on mutations. Use decimal money and UTC dates; Persian UI is RTL and displays Jalali dates.
- AI is off by default at all three levels and proposes only; statutory values are editable data, external integrations use adapters.
- Keep `vendor/`, `node_modules/`, build output, archives and large generated artifacts out of Git. Run `tools/budget.sh`; target <6,000 files / <60 MiB, fail at 7,000 / 90 MiB. Per-module target is at most 80 files.
- Update `docs/progress.md` with an accurate `NEXT:` and commit at the end of a phase/module or before stopping. Document ambiguity in `docs/decisions.md`. After a module, report exactly three lines: id, gate score, risk/gap.
