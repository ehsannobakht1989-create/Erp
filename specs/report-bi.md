# report-bi — Report builder, dashboards & KPIs (L0/L5; requires auth-rbac, form-builder)

**Entities** DatasetCatalog (from manifests); Report{name,dataset(s),columns,filters,grouping,sort,pivot,chart,visibility,owner,version}; Dashboard{layout,widgets[{report|kpi,params}],filters,owner,shared_with}; Kpi{code,name,formula(over dataset measures),unit,target,thresholds,period}; Schedule{report|dashboard,cron-like,recipients,channel[email|telegram|in-app],format[xlsx|csv|print]}; SavedFilter; Snapshot (cached results for heavy queries); AnalyticsFeed{event→fact table mapping} for cross-module facts (`report_bi__fact_*`, populated by listeners, bounded retention).
**Rules** data access only through DatasetProvider + user's permission and data-scope; row-level security preserved; cross-dataset joins by shared dimensions executed in-engine with limits (row caps, timeouts) — no foreign SQL; sensitive fields hidden unless permission; heavy reports served from snapshots/queued runs (chunked for shared hosting); formulas via safe expression engine; export respects column permissions; schedules via outbox/cron.
**Events out** report-bi.report.published, report-bi.kpi.breached. **In** manifests of all enabled modules (catalog refresh), any event mapped by AnalyticsFeed.
**Service contract** ReportService: run(reportId|spec, params, user), datasets(), kpiValue(code,period), runSchedule(id).
**Permissions** report-bi.report.view|create|publish, report-bi.dashboard.view|create|share, report-bi.kpi.manage, report-bi.schedule.manage, report-bi.dataset.view.
**Screens** report designer (drag fields, filters, group/pivot, chart picker, preview), dashboard builder (grid), KPI board with traffic lights, schedule manager, dataset explorer, export, embed widgets for other modules' pages (`<EmbeddedReport id/>`).
**AI** nl_report capability (question → validated report spec over the dataset catalog, never SQL), summarize dashboard, anomaly on KPI series, next_action. Provider-off still works fully.
**Datasets** meta (catalog of datasets itself).
**Iran** Jalali date grouping (week starts Saturday, Jalali months/quarters/years), Persian digits in exports option, RTL print.
