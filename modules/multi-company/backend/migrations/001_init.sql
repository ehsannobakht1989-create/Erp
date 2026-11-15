CREATE TABLE IF NOT EXISTS multi_company__companies (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL DEFAULT 1,
    code VARCHAR(32) NOT NULL,
    name VARCHAR(128) NOT NULL,
    base_currency VARCHAR(8) NOT NULL DEFAULT 'IRR',
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    deleted_at VARCHAR(32) NULL,
    UNIQUE(code)
);

CREATE TABLE IF NOT EXISTS multi_company__branches (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    code VARCHAR(32) NOT NULL,
    name VARCHAR(128) NOT NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);

CREATE TABLE IF NOT EXISTS multi_company__fiscal_years (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    code VARCHAR(16) NOT NULL,
    start_date VARCHAR(16) NOT NULL,
    end_date VARCHAR(16) NOT NULL,
    is_closed TINYINT(1) NOT NULL DEFAULT 0,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);

CREATE TABLE IF NOT EXISTS multi_company__sequences (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    branch_id INTEGER NOT NULL DEFAULT 0,
    fiscal_year VARCHAR(16) NOT NULL DEFAULT '',
    seq_key VARCHAR(64) NOT NULL,
    pattern VARCHAR(64) NOT NULL,
    last_value INTEGER NOT NULL DEFAULT 0,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, branch_id, fiscal_year, seq_key)
);


CREATE TABLE IF NOT EXISTS multi_company__uoms (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL DEFAULT 1,
    code VARCHAR(64) NOT NULL DEFAULT '',
    name VARCHAR(255) NOT NULL DEFAULT '',
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    payload TEXT NULL,
    created_at VARCHAR(32) NOT NULL DEFAULT '',
    updated_at VARCHAR(32) NOT NULL DEFAULT ''
);
