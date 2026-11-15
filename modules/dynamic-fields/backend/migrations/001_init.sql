CREATE TABLE IF NOT EXISTS dynamic_fields__fields (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    entity VARCHAR(128) NOT NULL,
    field_key VARCHAR(64) NOT NULL,
    label_fa VARCHAR(128) NOT NULL,
    field_type VARCHAR(32) NOT NULL,
    options_json TEXT NULL,
    validation_rules VARCHAR(255) NULL,
    sort_order INTEGER NOT NULL DEFAULT 0,
    is_searchable TINYINT(1) NOT NULL DEFAULT 0,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, entity, field_key)
);

CREATE TABLE IF NOT EXISTS dynamic_fields__values (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    entity VARCHAR(128) NOT NULL,
    entity_id VARCHAR(64) NOT NULL,
    field_key VARCHAR(64) NOT NULL,
    val_text TEXT NULL,
    val_num DECIMAL(20,4) NULL,
    val_date VARCHAR(32) NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, entity, entity_id, field_key)
);
