from fastapi import FastAPI
from pydantic import BaseModel
from typing import Any

app = FastAPI(title="ERP AI Service", version="1.0.0")


class ForecastRequest(BaseModel):
    company_id: int
    series: list[dict[str, Any]]
    horizon: int = 30
    config: dict[str, Any] = {}


class AnomalyRequest(BaseModel):
    company_id: int
    records: list[dict[str, Any]]
    config: dict[str, Any] = {}


class OptimizeRequest(BaseModel):
    company_id: int
    objective: str
    variables: dict[str, Any] = {}
    constraints: list[Any] = []


class OcrRequest(BaseModel):
    company_id: int
    document_type: str
    file_base64: str
    language: str = "fa"


@app.get("/health")
def health() -> dict[str, str]:
    return {"status": "healthy"}


@app.post("/v1/forecast")
def forecast(req: ForecastRequest) -> dict[str, Any]:
    return {"forecast": [], "horizon": req.horizon}


@app.post("/v1/anomaly")
def anomaly(req: AnomalyRequest) -> dict[str, Any]:
    return {"anomalies": []}


@app.post("/v1/optimize")
def optimize(req: OptimizeRequest) -> dict[str, Any]:
    return {"status": "optimal", "solution": {}, "objective_value": 0.0}


@app.post("/v1/ocr")
def ocr(req: OcrRequest) -> dict[str, Any]:
    return {"fields": {}, "confidence": 0.0, "raw_text": ""}
