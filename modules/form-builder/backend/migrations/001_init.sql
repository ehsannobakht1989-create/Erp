CREATE TABLE IF NOT EXISTS form_builder__definitions (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    slug VARCHAR(64) NOT NULL,
    title_fa VARCHAR(128) NOT NULL,
    version INTEGER NOT NULL DEFAULT 1,
    schema_json TEXT NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'draft',
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, slug, version)
);

CREATE TABLE IF NOT EXISTS form_builder__instances (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    form_slug VARCHAR(64) NOT NULL,
    form_version INTEGER NOT NULL,
    data_json TEXT NOT NULL,
    created_by INTEGER NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL
);


CREATE TABLE IF NOT EXISTS form_builder__form_definitions (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL DEFAULT 1,
    code VARCHAR(64) NOT NULL DEFAULT '',
    name VARCHAR(255) NOT NULL DEFAULT '',
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    payload TEXT NULL,
    created_at VARCHAR(32) NOT NULL DEFAULT '',
    updated_at VARCHAR(32) NOT NULL DEFAULT ''
);

CREATE TABLE IF NOT EXISTS form_builder__form_instances (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL DEFAULT 1,
    code VARCHAR(64) NOT NULL DEFAULT '',
    name VARCHAR(255) NOT NULL DEFAULT '',
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    payload TEXT NULL,
    created_at VARCHAR(32) NOT NULL DEFAULT '',
    updated_at VARCHAR(32) NOT NULL DEFAULT ''
);
