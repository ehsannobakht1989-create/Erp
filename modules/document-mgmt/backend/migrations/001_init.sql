CREATE TABLE IF NOT EXISTS document_mgmt__documents (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    entity VARCHAR(128) NOT NULL,
    entity_id VARCHAR(64) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(128) NOT NULL,
    size_bytes INTEGER NOT NULL DEFAULT 0,
    storage_path VARCHAR(512) NOT NULL,
    tags_json TEXT NULL,
    version INTEGER NOT NULL DEFAULT 1,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL
);
