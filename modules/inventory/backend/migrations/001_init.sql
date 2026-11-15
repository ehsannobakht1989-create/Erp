CREATE TABLE IF NOT EXISTS inventory__warehouses (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    code VARCHAR(32) NOT NULL,
    name_fa VARCHAR(128) NOT NULL,
    warehouse_type VARCHAR(32) NOT NULL DEFAULT 'raw',
    allow_negative TINYINT(1) NOT NULL DEFAULT 0,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);

CREATE TABLE IF NOT EXISTS inventory__ledger (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    doc_type VARCHAR(32) NOT NULL,
    doc_id VARCHAR(64) NOT NULL,
    item_id INTEGER NOT NULL,
    warehouse_id INTEGER NOT NULL,
    lot_no VARCHAR(64) NOT NULL DEFAULT '',
    qty_in DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
    qty_out DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
    idempotency_key VARCHAR(128) NOT NULL,
    posted_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, idempotency_key)
);

CREATE TABLE IF NOT EXISTS inventory__balances (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    warehouse_id INTEGER NOT NULL,
    lot_no VARCHAR(64) NOT NULL DEFAULT '',
    qty DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, item_id, warehouse_id, lot_no)
);


CREATE TABLE IF NOT EXISTS inventory__reservations (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL DEFAULT 1,
    code VARCHAR(64) NOT NULL DEFAULT '',
    name VARCHAR(255) NOT NULL DEFAULT '',
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    payload TEXT NULL,
    created_at VARCHAR(32) NOT NULL DEFAULT '',
    updated_at VARCHAR(32) NOT NULL DEFAULT ''
);
