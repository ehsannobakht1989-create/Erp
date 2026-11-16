# oee-downtime — OEE & loss analysis (L4; requires mes, org-structure; optional cmms)

**Entities** OeeSnapshot{work_center,period,planned_min,run_min,ideal_cycle,good,total,availability,performance,quality,oee}; LossCategory tree (six big losses mapping); Target{work_center,oee_target,availability_target,…}; Pareto views; Alert{rule,work_center,state}.
**Rules** planned time from calendar/shift minus planned downtime categories; availability = run/planned; performance = ideal_cycle×total/run (ideal cycle from routing, override per product/work center); quality = good/total; shift/day/week/month/plant rollups; micro-stop threshold setting; recalc on corrections; alerts to notifications; breakdown starting auto-creates cmms.workrequest (setting).
**Events out** oee.snapshot.computed, oee.alert.raised. **In** mes.* events, work-order.operation.completed, cmms.workorder.completed.
**Service contract** OeeService: oee(scope,range), losses(scope,range,groupBy), trend(scope,metric,range), benchmark(plant).
**Permissions** oee-downtime.view|target.manage|lossmap.manage.
**Screens** OEE dashboard (gauges, trends, by shift/line), downtime Pareto + reason drill-down, six-losses waterfall, shift comparison, targets, TV mode.
**AI** anomaly/forecast (python: degradation trends), summarize (weekly loss narrative), next_action (top 3 improvement areas), nl_report.
