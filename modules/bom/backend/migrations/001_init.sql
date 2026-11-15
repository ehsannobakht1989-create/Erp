CREATE TABLE IF NOT EXISTS bom__boms (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    code VARCHAR(64) NOT NULL,
    revision INTEGER NOT NULL DEFAULT 1,
    base_qty DECIMAL(20,4) NOT NULL DEFAULT 1.0000,
    yield_pct DECIMAL(8,4) NOT NULL DEFAULT 100.0000,
    status VARCHAR(16) NOT NULL DEFAULT 'draft',
    created_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code, revision)
);

CREATE TABLE IF NOT EXISTS bom__lines (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    bom_id INTEGER NOT NULL,
    component_item_id INTEGER NOT NULL,
    qty_per DECIMAL(20,4) NOT NULL,
    scrap_pct DECIMAL(8,4) NOT NULL DEFAULT 0.0000
);
