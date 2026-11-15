CREATE TABLE IF NOT EXISTS org_structure__units (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    parent_id INTEGER NULL,
    unit_type VARCHAR(32) NOT NULL,
    code VARCHAR(32) NOT NULL,
    name_fa VARCHAR(128) NOT NULL,
    cost_center_id INTEGER NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);

CREATE TABLE IF NOT EXISTS org_structure__cost_centers (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    parent_id INTEGER NULL,
    code VARCHAR(32) NOT NULL,
    name_fa VARCHAR(128) NOT NULL,
    cc_type VARCHAR(32) NOT NULL DEFAULT 'production',
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);

CREATE TABLE IF NOT EXISTS org_structure__shifts (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    code VARCHAR(32) NOT NULL,
    name_fa VARCHAR(128) NOT NULL,
    start_time VARCHAR(8) NOT NULL,
    end_time VARCHAR(8) NOT NULL,
    break_minutes INTEGER NOT NULL DEFAULT 0,
    crosses_midnight TINYINT(1) NOT NULL DEFAULT 0,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);


CREATE TABLE IF NOT EXISTS org_structure__positions (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL DEFAULT 1,
    code VARCHAR(64) NOT NULL DEFAULT '',
    name VARCHAR(255) NOT NULL DEFAULT '',
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    payload TEXT NULL,
    created_at VARCHAR(32) NOT NULL DEFAULT '',
    updated_at VARCHAR(32) NOT NULL DEFAULT ''
);

CREATE TABLE IF NOT EXISTS org_structure__calendars (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL DEFAULT 1,
    code VARCHAR(64) NOT NULL DEFAULT '',
    name VARCHAR(255) NOT NULL DEFAULT '',
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    payload TEXT NULL,
    created_at VARCHAR(32) NOT NULL DEFAULT '',
    updated_at VARCHAR(32) NOT NULL DEFAULT ''
);
