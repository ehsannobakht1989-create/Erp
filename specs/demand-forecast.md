# demand-forecast — Demand & consumption forecasting (L6; python; requires report-bi; optional sales, inventory, mps, reorder-point, budgeting)

**Purpose** statistical/ML forecasts for sales demand, material consumption, cash flows and any dataset time series.
**Entities** ForecastJob{dataset_id,item|group,granularity[day|week|month],horizon,method[auto|ets|arima|theta|croston|tsb|ml_gbm|baseline],params,status,created_by}; ForecastResult{job,series_key,period,point,lower,upper,model,metrics{wape,mape,bias},backtest_window}; SeriesProfile{series_key,classification[smooth|erratic|intermittent|lumpy],seasonality,trend,outliers}; AdoptedForecast{target[mps|reorder|budget],series_key,version,adopted_by}.
**Rules** series classification (ADI/CV²) selects method families; automatic backtesting (rolling origin) picks the best method per series; outlier handling (winsorize/flag, never silently delete); hierarchical reconciliation optional (item→category); calendar effects (Jalali months, Nowruz/Ramadan windows as configurable regressors); results are PROPOSALS: planner adopts into MPS forecast / reorder stats / budget drafts via events; minimum history thresholds with explicit "insufficient data" results; hard caps on series count per job (chunked).
**Python capability** `POST /v1/forecast` {series[], horizon, granularity, regressors?, options} → {results[], diagnostics[]}.
**Events out** demand-forecast.forecast.published, demand-forecast.forecast.adopted. **In** report-bi schedule triggers, sales.invoice.issued (optional incremental refresh).
**Service contract** ForecastService: run(dto): jobId, results(jobId), latest(seriesKey), adopt(seriesKey,target), accuracy(range).
**Permissions** demand-forecast.job.run|view, demand-forecast.result.adopt, demand-forecast.settings.manage.
**Screens** forecast workbench (pick dataset/series, run, compare methods), accuracy dashboard (forecast vs actual), series classification table, adoption review (diff vs current MPS forecast).
**Datasets consumed** sales_history, material_consumption, cash_flows. **Datasets exposed** forecast_results, forecast_accuracy.
**Eval** synthetic series generator (trend/seasonality/intermittent), backtests, WAPE thresholds in tests.
