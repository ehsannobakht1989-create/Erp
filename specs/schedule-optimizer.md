# schedule-optimizer — Production schedule optimization (L6; python; requires scheduling, work-order, routing; optional cmms, tooling)

**Entities** OptimizationRun{scope(work centers,horizon),objective[min_tardiness|min_makespan|min_setup|weighted],params{time_limit_sec,weights},status,created_by}; Solution{run,operations[{op_ref,work_center,start,end}],kpis{tardiness,makespan,setup_time,utilization},feasible,gap}; Constraint snapshot (calendars as availability windows, setup matrix, maintenance windows, tool availability, precedence, due dates, priorities, locked ops).
**Rules** payload built by PHP from SchedulingService data (ops, durations, precedences, alternates, setup matrix, availability windows); Python solves with OR-Tools CP-SAT (job-shop with optional intervals/alternates, sequence-dependent setups, calendars, locked ops fixed), respects time limit and returns best feasible solution + KPIs + comparison to the current schedule (and to the built-in heuristic); results are PROPOSED schedule versions in `scheduling` (never overwrite published); infeasibility explanations (which constraints conflict) returned; problem size guards with decomposition by work-center groups/time windows; deterministic with seed.
**Python capability** `POST /v1/schedule/optimize` {problem} → {solution, kpis, diagnostics}.
**Events out** schedule-optimizer.solution.ready. **In** scheduling.schedule.draft_requested.
**Service contract** OptimizerService: run(scope,objective,params): runId, status(runId), solution(runId), compare(runId, baselineVersion).
**Permissions** schedule-optimizer.run|view|apply.
**Screens** optimization console (scope/objective/time limit, progress), KPI comparison (current vs heuristic vs optimized), Gantt diff, apply-as-draft button.
**Eval** benchmark instances (small job-shop, setup-heavy, calendar-constrained) with known optimal/best KPIs; time-limit and feasibility tests.
