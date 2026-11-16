# Decisions

## 2026-11-16 — Baseline and transition

- The master plan supersedes the existing raw-PHP instructions, the old definition of done, and other historical documentation where they conflict. Keep the existing assets for review and adaptation rather than treating them as proof of readiness.
- Keep the legacy `core/`, `public/index.php`, `bin/erp`, and module backend implementations until a functioning Laravel replacement is in place. Preserve module manifests, specifications, UI, and contracts during the migration. The old CI currently exercises the legacy application, not the target stack.
- All 79 existing manifests declare `done`; none has yet passed the new gate. Treat these claims as unverified, not as completed modules. Phase 1 must downgrade them and implement the independent depth gate before module work begins.
- Existing specifications are compact and some platform module specs are missing; expand/write them during their respective spec-first module work, without inventing coverage from absence.
- Budget counts tracked plus untracked non-ignored regular files/symlinks by logical byte size, with inclusive failure thresholds of 7,000 files and 90 MiB. This catches additions before staging; ignored build/dependency output is excluded. The check requires Python 3, already required for the AI service.
- Keep existing frontend/build dependencies during migration; any new Composer dependency outside the allowed list must be documented here before introduction. Framework and tooling transitive dependencies are not independent product-level package choices.
