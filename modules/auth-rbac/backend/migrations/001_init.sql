CREATE TABLE IF NOT EXISTS auth_rbac__users (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL DEFAULT 1,
    username VARCHAR(64) NOT NULL,
    email VARCHAR(128) NOT NULL,
    full_name VARCHAR(128) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    totp_secret VARCHAR(64) NULL,
    totp_enabled TINYINT(1) NOT NULL DEFAULT 0,
    must_change_password TINYINT(1) NOT NULL DEFAULT 0,
    failed_attempts INTEGER NOT NULL DEFAULT 0,
    locked_until VARCHAR(32) NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'active',
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    deleted_at VARCHAR(32) NULL,
    UNIQUE(company_id, username)
);

CREATE TABLE IF NOT EXISTS auth_rbac__roles (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL DEFAULT 1,
    slug VARCHAR(64) NOT NULL,
    name VARCHAR(128) NOT NULL,
    data_scope VARCHAR(32) NOT NULL DEFAULT 'own',
    permissions_json TEXT NOT NULL,
    field_hints_json TEXT NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, slug)
);

CREATE TABLE IF NOT EXISTS auth_rbac__user_roles (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL DEFAULT 1,
    user_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,
    created_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, user_id, role_id)
);

CREATE TABLE IF NOT EXISTS auth_rbac__sessions (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL DEFAULT 1,
    user_id INTEGER NOT NULL,
    token_hash VARCHAR(64) NOT NULL,
    ip VARCHAR(64) NOT NULL,
    expires_at VARCHAR(32) NOT NULL,
    created_at VARCHAR(32) NOT NULL,
    UNIQUE(token_hash)
);
