# mps — Master production schedule (L4; requires bom, inventory, sales; optional demand-forecast, routing)

**Entities** MpsPlan{name,version,horizon,bucket[week|month],status[draft|approved|released|archived],scenario_of?}; MpsItem{item,policy[make_to_stock|make_to_order|assemble_to_order],time_fences{demand,planning},lot_rule,safety_stock}; MpsLine{item,bucket_start,forecast,sales_orders,net_demand,projected_available,planned_production,ATP}; ForecastEntry{item,bucket,qty,source[manual|statistical|ai],version}; RoughCutCheck{work_center,bucket,load_hours,capacity_hours,pct}.
**Rules** demand = max/blend of forecast and actual orders within demand fence (consumption rules configurable); beyond planning fence suggestions free, inside fence frozen; ATP computed cumulatively; rough-cut capacity via RoutingService standard times; scenarios as copies with diffs; release creates MRP independent demand + firmed planned orders; approvals via workflow.
**Events out** mps.plan.approved|released, mps.atp.changed. **In** sales.order.confirmed|cancelled, demand-forecast.forecast.published (optional), mrp.run.completed (feedback).
**Service contract** MpsService: atp(item,qty,date), plan(item,range), forecast(item,range), releasePlan(id), scenarioCompare(a,b).
**Permissions** mps.plan.view|edit|approve|release, mps.forecast.edit.
**Screens** planning grid (items × buckets, editable planned production), forecast editor/import, ATP check, rough-cut chart, scenario compare, release log.
**AI** forecast (statistical + ML through python, proposals into forecast entries), anomaly (forecast vs orders divergence), nl_report.
**Iran** monthly Jalali buckets option, seasonality around Nowruz/Ramadan configurable calendar effects.
