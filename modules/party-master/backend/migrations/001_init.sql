CREATE TABLE IF NOT EXISTS party_master__groups (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    parent_id INTEGER NULL,
    code VARCHAR(32) NOT NULL,
    name_fa VARCHAR(128) NOT NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);

CREATE TABLE IF NOT EXISTS party_master__payment_terms (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    code VARCHAR(32) NOT NULL,
    name_fa VARCHAR(128) NOT NULL,
    days INTEGER NOT NULL DEFAULT 0,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);

CREATE TABLE IF NOT EXISTS party_master__parties (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    code VARCHAR(64) NOT NULL,
    party_type VARCHAR(32) NOT NULL,
    legal_kind VARCHAR(16) NOT NULL DEFAULT 'legal',
    name_fa VARCHAR(255) NOT NULL,
    name_en VARCHAR(255) NULL,
    national_id VARCHAR(32) NULL,
    economic_code VARCHAR(32) NULL,
    group_id INTEGER NULL,
    payment_terms_id INTEGER NULL,
    credit_limit DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
    currency VARCHAR(8) NOT NULL DEFAULT 'IRR',
    status VARCHAR(32) NOT NULL DEFAULT 'draft',
    is_used TINYINT(1) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    deleted_at VARCHAR(32) NULL,
    UNIQUE(company_id, code)
);
