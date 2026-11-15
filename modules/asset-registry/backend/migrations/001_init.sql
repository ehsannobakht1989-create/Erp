CREATE TABLE IF NOT EXISTS asset_registry__assets (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    parent_id INTEGER NULL,
    code VARCHAR(64) NOT NULL,
    name_fa VARCHAR(128) NOT NULL,
    criticality VARCHAR(4) NOT NULL DEFAULT 'B',
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    qr_code VARCHAR(128) NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);

CREATE TABLE IF NOT EXISTS asset_registry__meters (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    asset_id INTEGER NOT NULL,
    meter_type VARCHAR(32) NOT NULL,
    current_value DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
    updated_at VARCHAR(32) NOT NULL
);
