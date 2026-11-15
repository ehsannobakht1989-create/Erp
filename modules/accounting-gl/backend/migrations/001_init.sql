CREATE TABLE IF NOT EXISTS accounting_gl__accounts (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    code VARCHAR(32) NOT NULL,
    name_fa VARCHAR(128) NOT NULL,
    acc_type VARCHAR(16) NOT NULL,
    is_postable TINYINT(1) NOT NULL DEFAULT 1,
    created_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);

CREATE TABLE IF NOT EXISTS accounting_gl__journals (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    journal_no VARCHAR(32) NOT NULL,
    journal_date VARCHAR(16) NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'temporary',
    source_ref VARCHAR(128) NULL,
    description TEXT NULL,
    created_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, journal_no)
);

CREATE TABLE IF NOT EXISTS accounting_gl__journal_lines (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    journal_id INTEGER NOT NULL,
    account_id INTEGER NOT NULL,
    debit DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
    credit DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
    description VARCHAR(255) NULL
);
