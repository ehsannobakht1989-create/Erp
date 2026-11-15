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
