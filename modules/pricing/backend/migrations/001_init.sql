CREATE TABLE IF NOT EXISTS pricing__lists (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    code VARCHAR(32) NOT NULL,
    list_type VARCHAR(16) NOT NULL DEFAULT 'sales',
    currency VARCHAR(8) NOT NULL DEFAULT 'IRR',
    party_id INTEGER NULL,
    priority INTEGER NOT NULL DEFAULT 10,
    status VARCHAR(16) NOT NULL DEFAULT 'active',
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);

CREATE TABLE IF NOT EXISTS pricing__lines (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    list_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    min_qty DECIMAL(20,4) NOT NULL DEFAULT 1.0000,
    price DECIMAL(20,4) NOT NULL,
    discount_pct DECIMAL(8,4) NOT NULL DEFAULT 0.0000,
    created_at VARCHAR(32) NOT NULL
);
