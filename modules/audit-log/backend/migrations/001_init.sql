CREATE TABLE IF NOT EXISTS audit_log__entries (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    actor_id INTEGER NULL,
    module_id VARCHAR(64) NOT NULL,
    table_name VARCHAR(128) NOT NULL,
    action VARCHAR(16) NOT NULL,
    record_id VARCHAR(64) NOT NULL,
    before_json TEXT NULL,
    after_json TEXT NULL,
    prev_hash VARCHAR(64) NOT NULL,
    entry_hash VARCHAR(64) NOT NULL,
    created_at VARCHAR(32) NOT NULL
);
