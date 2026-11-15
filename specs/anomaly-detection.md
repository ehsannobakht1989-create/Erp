# anomaly-detection — Generic anomaly engine (L6; python optional; requires report-bi)

**Purpose** one reusable engine other modules call (inventory adjustments, ledger entries, prices, scrap rates, OEE, energy, logins, etc.) via declared datasets + detector profiles.
**Entities** DetectorProfile{id,dataset_id,target_measure,dimensions[],granularity,method[robust_zscore|seasonal_residual|isolation_forest|change_point|rule_set],sensitivity,min_history,schedule,enabled}; Detection{profile,series_key,ts,value,expected,score,severity,explanation,status[new|confirmed|dismissed],linked_record_ref}; RuleSet{conditions DSL for deterministic checks (e.g. weekend postings, round amounts, duplicates)}; FeedbackLabel.
**Rules** two engines: (a) deterministic rule/stat checks in PHP (always available, no Python); (b) Python ML/statistical detectors when enabled; severity from score bands; suppression windows & dedup; feedback labels (confirm/dismiss) tune sensitivity per profile (simple thresholds adaptation, documented); each detection must carry a human-readable explanation (expected vs actual, contributing dimensions); notifications by severity; modules register their profiles through manifest `ai.capabilities[id=anomaly]` + `datasets` and get a default profile pack seeded on enable.
**Python capability** `POST /v1/anomaly` {series[], method, params} → {detections[], diagnostics}.
**Events out** anomaly-detection.detection.created|confirmed. **In** schedule ticks; module events that mark records as reviewed.
**Service contract** AnomalyService: runProfile(id), detections(filter), feedback(detectionId,label), registerProfile(dto).
**Permissions** anomaly-detection.profile.manage, anomaly-detection.detection.view|review.
**Screens** detections inbox with filters and drill-to-record, profile designer/test run, false-positive analytics, default profile packs list.
**Datasets exposed** detections. **Eval** injected-anomaly synthetic tests (precision/recall targets in CI).
