# calibration — Gauges & instrument calibration (L4; requires asset-registry; optional quality-inspection, lims, mes, cmms)

**Entities** Instrument{code,name,type,range,resolution,accuracy,location,owner,asset_ref?,status[in_service|due|overdue|out_of_service|retired],critical_use_flag}; CalibrationPlan{instrument,interval_days,tolerance,method_doc,vendor|internal,lead_days_alert}; CalibrationRecord{instrument,date,performed_by|vendor,standards_used[],as_found[],as_left[],result[pass|adjusted|fail],uncertainty,certificate_doc,next_due}; ReferenceStandard{code,cert_due,traceability}; ImpactAssessment{record,from_date,usage_refs[] (inspections/measurements since last good calibration),outcome}.
**Rules** next_due computed on pass; overdue instruments flagged and blocked from use in quality-inspection/MES/LIMS if setting; fail/out-of-tolerance triggers impact assessment listing results recorded with that instrument since last good calibration (via references stored on results), proposing NCR; vendor calibration order tracking; internal calibration uses reference standards with valid certificates.
**Events out** calibration.instrument.status_changed|due_soon|overdue, calibration.out_of_tolerance.impact. **In** quality-inspection.result.recorded (usage trace), lims.result.approved.
**Service contract** CalibrationService: isValid(instrument,date), dueSoon(days), usageSince(instrument,date), history(instrument).
**Permissions** calibration.instrument|plan|record|standard.view|manage, calibration.impact.manage.
**Screens** instrument register, due/overdue board, calibration record entry (as-found/as-left grid), certificate library, impact assessment wizard, calendar view, labels (QR with due date) print.
**AI** extract data from calibration certificate text (doc-ocr, proposal), anomaly (drift trends across calibrations), forecast due workload.

======================================================================
# PART B — ARCHITECTURE (write into docs/ai-service.md)
======================================================================

- Two AI planes: (a) **LLM plane** — PHP AI Gateway → AvalAI (OpenAI-compatible) for language tasks (assistant, summarize, extraction post-processing, NL→report-spec); (b) **ML plane** — PHP AI Gateway → Python FastAPI for numeric tasks (forecast, anomaly, failure risk, optimization, OCR). Both are optional; every feature degrades gracefully when off.
- Deployment: Python service is OPTIONAL and runs on a separate VPS/container or cPanel "Setup Python App" (Passenger). PHP never assumes it exists; builder includes `ai-service/` only if a ticked module needs it and AI is enabled.
- Data flow: PHP extracts a **dataset** (declared in manifests, permission-checked, redacted per `ai.capabilities[].redact`) → sends minimal JSON/CSV payload (no DB access from Python) → Python returns results + metadata (model, params, metrics, version, confidence) → PHP stores as **proposals/insights** (`ai__proposals`, module-owned insight tables) → human reviews. AI never writes business data directly.
- Auth: shared secret per deployment, HMAC signature + timestamp + nonce; per-request `company_id`, `module`, `capability`; request/response schemas versioned (`/v1/...`).
- Long jobs: `POST /v1/jobs` returns job_id; PHP polls via `events:work` slices and stores results; time/size limits; idempotency key.
- Models: stateless by default (fit on request data, small series) with optional file-based model registry (`ai-service/models/<company>/<capability>/<version>`) for retrained models; reproducible (fixed seeds, recorded params/metrics).
- Privacy: payload fields allow-list, optional pseudonymization map kept in PHP, size caps, no persistence of raw payloads (config), logs without payload content.
- Observability: request log (ids, durations, sizes), per-capability metrics, `/v1/health` & `/v1/capabilities` (so PHP can show what's available).
- Evaluation: each capability ships a backtest/eval script + synthetic data generator; metrics recorded with results (e.g. WAPE/MAPE for forecasts).
- Budgets: ai-service ≤ 300 files, dependencies limited to numpy, pandas, scipy, statsmodels, scikit-learn, ortools, pillow/opencv-headless (OCR only), pytesseract; no heavy deep-learning frameworks by default (optional extras documented separately).


======================================================================
# PART B BUNDLE
======================================================================
