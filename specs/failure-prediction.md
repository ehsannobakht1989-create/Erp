# failure-prediction — Predictive maintenance (L6; python; requires cmms, asset-registry; optional condition-monitoring-iot, mes)

**Entities** HealthModel{scope[asset|asset_class],features_def,method[baseline_threshold|weibull_survival|gbm_classifier|isolation_based],trained_at,metrics}; RiskScore{asset,date,risk_0_100,rul_days?,drivers[],confidence}; Insight{asset,type[degradation|imminent_failure|abnormal_usage],evidence,status[new|accepted|dismissed],cmms_proposal_ref}; FeatureSnapshot{asset,date,features(json)}.
**Rules** features from cmms history (MTBF trend, failure codes, downtime), meters/utilization, sensor rollups (monitoring), operating context (mes load); **cold start**: classes with little history use transparent baselines (age vs life, MTBF-based hazard); survival analysis for classes with failure counts; risk explanations (top drivers) mandatory; thresholds produce PROPOSED cmms work requests/PM adjustments (never auto-created); feedback loop: accepted/dismissed + actual outcomes retrain/evaluate; model quality shown (precision/recall on backtest), disabled below minimum quality (setting).
**Python capability** `POST /v1/failure-risk` {assets[{id,class,features...}], history[]} → {scores[], drivers[], model_info}.
**Events out** failure-prediction.risk.high (→ notifications/cmms proposal), failure-prediction.insight.created. **In** cmms.workorder.closed (outcome), condition-monitoring-iot.alarm.*, asset-registry.meter.updated.
**Service contract** FailureRiskService: scores(assetIds), explain(assetId), topRisks(limit), feedback(insightId,outcome).
**Permissions** failure-prediction.score.view, failure-prediction.insight.act, failure-prediction.model.manage.
**Screens** fleet health overview (ranked risks), asset risk detail with drivers/timeline, insights inbox (accept→create CMMS proposal), model quality page.
**Datasets consumed** work_orders_history, downtime, meters, sensor_rollups. **Exposed** risk_scores.
**Eval** synthetic degradation simulator; baseline vs model comparison tests.
