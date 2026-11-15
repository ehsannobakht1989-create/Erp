CREATE TABLE IF NOT EXISTS workflow_engine__definitions (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    code VARCHAR(64) NOT NULL,
    name_fa VARCHAR(128) NOT NULL,
    definition_json TEXT NOT NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);

CREATE TABLE IF NOT EXISTS workflow_engine__instances (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    workflow_code VARCHAR(64) NOT NULL,
    entity_id VARCHAR(64) NOT NULL,
    current_state VARCHAR(64) NOT NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, workflow_code, entity_id)
);

CREATE TABLE IF NOT EXISTS workflow_engine__history (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    instance_id INTEGER NOT NULL,
    from_state VARCHAR(64) NOT NULL,
    to_state VARCHAR(64) NOT NULL,
    action VARCHAR(64) NOT NULL,
    actor_id INTEGER NULL,
    comment TEXT NULL,
    created_at VARCHAR(32) NOT NULL
);
