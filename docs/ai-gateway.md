# AI Gateway & Optional Intelligence

AI in this ERP is **strictly optional** and **disabled by default**. Every module must function 100% with AI turned off.

## 1. Three-Level Optionality
AI activation is controlled at three levels (all must permit it):
1. **Factory**: `ERP_AI_ENABLED=true` in `.env` (the builder derives it from the profile's `ai.enabled`); `AI_KILL_SWITCH=true` disables everything immediately.
2. **Module** (per company): `core_ai_toggles` row with an empty capability, defaulting to manifest `ai.enabled_default` (false).
3. **Capability** (per company): `core_ai_toggles` row for the capability, default off; a manifest `provider: "off"` can never be enabled.

Implementation: `App\Support\Ai\AiGateway` (`AiGatewayContract::invoke`). Enabled calls also need a positive remaining monthly company budget (`core_ai_budgets`), redact global and capability fields (including tool results), expose only manifest-declared read-only tools registered by the module's `aiTools()` and run them with the caller's permissions, store only declared proposal types as pending `core_ai_proposals`, and record tokens and exact cost in `core_ai_usage` with audit entries. Python service calls are signed with `X-ERP-Signature: sha256=HMAC(AI_SERVICE_SECRET, timestamp\nrequest_id\nbody)` plus `X-ERP-Timestamp` and `X-ERP-Request-Id`.

## 2. Providers
- **`avalai`**: OpenAI-compatible HTTP API. API key from `AVALAI_API_KEY` env var; base URL from config.
- **`python`**: HTTP calls to `ai-service` (FastAPI) for numeric ML, forecasting, anomaly detection, optimization, and OCR.
- **`off`**: No external or ML calls; AI UI entry points are hidden or return a graceful disabled state.

## 3. Tool-Calling & Permission Enforcement
- The AI Gateway exposes ONLY the read-only `tools` declared in the module's `module.json`.
- Every tool call executes with the **current authenticated user's permissions** — the AI never sees data the user cannot access.

## 4. Proposal-Only Writes (`ai__proposals`)
- AI **NEVER** mutates business data directly.
- Write intents return a proposal record stored in `ai__proposals` (with `company_id`, `module_id`, `capability`, `payload`, `status: pending`).
- A human user reviews and approves or rejects the proposal; upon approval, the standard module service executes the action with full validation and audit logging.

## 5. Audit, Redaction, Budgets & Kill Switch
- **Audit**: Every AI call logs `company_id`, `user_id`, `module_id`, `capability`, `prompt_hash`, `tool_calls`, `tokens_in`, `tokens_out`, `estimated_cost`, and `created_at`.
- **Redaction**: Fields listed in `ai.capabilities[].redact` (e.g. `unit_cost`, `national_id`) are masked before leaving the server.
- **Budgets**: Per-company and per-module token/cost limits enforced by the gateway.
- **Kill Switch**: Global config/env flag immediately disables all AI calls across all companies.

## 6. Python `ai-service` Contracts
All endpoints accept/return JSON:
- `POST /v1/forecast`
  - Request: `{ "company_id": 1, "series": [{"ds": "2026-01-01", "y": 120}], "horizon": 30, "config": {} }`
  - Response: `{ "forecast": [{"ds": "2026-02-01", "yhat": 125.4, "yhat_lower": 110.0, "yhat_upper": 140.0}] }`
- `POST /v1/anomaly`
  - Request: `{ "company_id": 1, "records": [{"id": 10, "features": [12.5, 0.3]}], "config": {} }`
  - Response: `{ "anomalies": [{"id": 10, "score": 0.94, "is_anomaly": true}] }`
- `POST /v1/optimize`
  - Request: `{ "company_id": 1, "objective": "minimize_makespan", "variables": {}, "constraints": [] }`
  - Response: `{ "status": "optimal", "solution": {}, "objective_value": 42.0 }`
- `POST /v1/ocr`
  - Request: `{ "company_id": 1, "document_type": "invoice", "file_base64": "...", "language": "fa" }`
  - Response: `{ "fields": {}, "confidence": 0.91, "raw_text": "..." }`

Modules supply only the dataset query and config; `ai-service` remains stateless.
