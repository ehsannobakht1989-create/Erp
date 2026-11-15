# management-consolidation — Management reporting (L5; requires report-bi, accounting-gl; optional costing, sales, multi-company)

**Entities** ReportPack{name,period,sections[],status}; Scorecard{perspective,kpis[],targets}; ConsolidationSet{companies[],eliminations[],currency}; Commentary{section,text,author}; AllocationRule (shared cost spreading); ProfitabilityView{dimension[product|customer|plant|cost_center],measures}.
**Rules** built on datasets only: management P&L by cost center/plant, product & customer profitability (sales margin using CostingService when enabled), plant comparisons, consolidated multi-company with elimination rules and FX translation (rates from multi-company); packs frozen at publish with snapshots; commentary workflow; board-pack print/PDF via print templates.
**Events out** management-consolidation.pack.published. **In** accounting-gl.period.closed, costing.run.completed.
**Service contract** ConsolidationService: pnl(scope,period), profitability(dimension,period), pack(id).
**Permissions** management-consolidation.pack.view|create|publish, management-consolidation.rule.manage.
**Screens** pack builder, scorecards, P&L/profitability explorers, consolidation setup, commentary editor, print.
**AI** summarize pack narrative (draft commentary proposal), nl_report, anomaly. **Datasets** consumes only.
