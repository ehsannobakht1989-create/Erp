CREATE TABLE IF NOT EXISTS core__modules (
    id VARCHAR(64) PRIMARY KEY,
    enabled TINYINT(1) NOT NULL DEFAULT 0,
    updated_at VARCHAR(32) NOT NULL
);

CREATE TABLE IF NOT EXISTS core__outbox (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    event VARCHAR(128) NOT NULL,
    version VARCHAR(16) NOT NULL DEFAULT '1.0',
    company_id INTEGER NOT NULL DEFAULT 0,
    entity_id VARCHAR(64) NOT NULL DEFAULT '',
    payload TEXT NOT NULL,
    actor_id INTEGER NULL,
    correlation_id VARCHAR(64) NOT NULL,
    idempotency_key VARCHAR(128) NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'pending',
    attempts INTEGER NOT NULL DEFAULT 0,
    next_attempt_at VARCHAR(32) NOT NULL,
    last_error TEXT NULL,
    created_at VARCHAR(32) NOT NULL,
    UNIQUE(idempotency_key)
);

CREATE TABLE IF NOT EXISTS core__settings (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    module_id VARCHAR(64) NOT NULL,
    setting_key VARCHAR(128) NOT NULL,
    setting_value TEXT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, module_id, setting_key)
);

CREATE TABLE IF NOT EXISTS ai__proposals (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    module_id VARCHAR(64) NOT NULL,
    capability VARCHAR(64) NOT NULL,
    proposal_type VARCHAR(128) NOT NULL,
    payload TEXT NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'pending',
    reviewed_by INTEGER NULL,
    reviewed_at VARCHAR(32) NULL,
    created_at VARCHAR(32) NOT NULL
);

CREATE TABLE IF NOT EXISTS ai__usage (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    module_id VARCHAR(64) NOT NULL,
    capability VARCHAR(64) NOT NULL,
    provider VARCHAR(32) NOT NULL,
    prompt_hash VARCHAR(64) NOT NULL,
    tool_calls TEXT NOT NULL,
    tokens_in INTEGER NOT NULL DEFAULT 0,
    tokens_out INTEGER NOT NULL DEFAULT 0,
    estimated_cost DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
    created_at VARCHAR(32) NOT NULL
);
