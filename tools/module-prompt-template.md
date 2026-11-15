Read AGENTS.md, docs/module-pattern.md, docs/ui-kit.md, docs/core-api.md and the platform contracts.
Use modules/item-master as the structural reference (do not copy its business logic).
Build module <ID> exactly per specs/<ID>.md. Replace the stub manifest; set "status":"done" only when
docs/definition-of-done.md is satisfied. Touch ONLY modules/<ID>/**. Budget ≤ 50 files.
Integrate with other modules ONLY via events and service contracts listed in the spec (guard optional ones).
Implement the `ai` block through AiGatewayContract (default OFF), including tests with provider=off.
Run tools/budget.sh, php tools/validate-manifests.php, php tools/test.php, npm run typecheck. Open a PR with done / not-done list.
If the spec is ambiguous, choose the simplest industry-neutral behavior, make it a manifest `setting`, and note it in the PR.
