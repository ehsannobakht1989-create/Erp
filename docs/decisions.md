# Decisions

## 2026-11-16 — Baseline and transition

- The master plan supersedes the existing raw-PHP instructions, the old definition of done, and other historical documentation where they conflict. Keep the existing assets for review and adaptation rather than treating them as proof of readiness.
- Keep the legacy `core/`, `public/index.php`, `bin/erp`, and module backend implementations until a functioning Laravel replacement is in place. Preserve module manifests, specifications, UI, and contracts during the migration. The old CI currently exercises the legacy application, not the target stack.
- All 79 manifests originally declared `done` without the required depth gate. In Phase 1 they were downgraded to `partial` because each has legacy implementation; this is not a claim of usable functionality. The independent depth gate must be implemented before module work begins.
- Existing specifications are compact and some platform module specs are missing; expand/write them during their respective spec-first module work, without inventing coverage from absence.
- Budget counts tracked plus untracked non-ignored regular files/symlinks by logical byte size, with inclusive failure thresholds of 7,000 files and 90 MiB. This catches additions before staging; ignored build/dependency output is excluded. The check requires Python 3, already required for the AI service.
- Keep existing frontend/build dependencies during migration; any new Composer dependency outside the allowed list must be documented here before introduction. Framework and tooling transitive dependencies are not independent product-level package choices.

- Until the real depth gate and its test execution are implemented, CI rejects every `done` status using a temporary fail-closed status guard. It is explicitly not a scoring gate and must be replaced by the real one, not relaxed to allow unverified completions.

- Depth diagnostics are being added incrementally: while any category or module-test execution is missing, scores are null, pass is always false, and `--gate` rejects every `done` declaration. Marker diagnostics scan source files, not documentation. The separate status guard remains until the full gate is trusted.

- Module catalog selection is fail-closed: enabled modules must explicitly include all hard dependencies. The future builder can auto-select those dependencies; the runtime does not silently enable them. Optional dependencies do not auto-enable or constrain ordering and may be absent from a packaged factory.
