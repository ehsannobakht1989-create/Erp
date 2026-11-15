CREATE TABLE IF NOT EXISTS notifications__messages (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    channel VARCHAR(32) NOT NULL,
    template VARCHAR(64) NOT NULL,
    payload_json TEXT NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'queued',
    read_at VARCHAR(32) NULL,
    created_at VARCHAR(32) NOT NULL
);


CREATE TABLE IF NOT EXISTS notifications__rules (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL DEFAULT 1,
    code VARCHAR(64) NOT NULL DEFAULT '',
    name VARCHAR(255) NOT NULL DEFAULT '',
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    payload TEXT NULL,
    created_at VARCHAR(32) NOT NULL DEFAULT '',
    updated_at VARCHAR(32) NOT NULL DEFAULT ''
);
