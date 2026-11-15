CREATE TABLE IF NOT EXISTS api_webhooks__keys (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    name VARCHAR(128) NOT NULL,
    key_hash VARCHAR(64) NOT NULL,
    scopes_json TEXT NOT NULL,
    rate_limit INTEGER NOT NULL DEFAULT 60,
    created_at VARCHAR(32) NOT NULL,
    UNIQUE(key_hash)
);

CREATE TABLE IF NOT EXISTS api_webhooks__webhooks (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    event_pattern VARCHAR(128) NOT NULL,
    target_url VARCHAR(512) NOT NULL,
    secret VARCHAR(128) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at VARCHAR(32) NOT NULL
);
