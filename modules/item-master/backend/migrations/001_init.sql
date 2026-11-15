CREATE TABLE IF NOT EXISTS item_master__categories (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    parent_id INTEGER NULL,
    code VARCHAR(32) NOT NULL,
    name_fa VARCHAR(128) NOT NULL,
    default_item_type VARCHAR(32) NULL,
    default_uom_id VARCHAR(32) NULL,
    attribute_set_json TEXT NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);

CREATE TABLE IF NOT EXISTS item_master__items (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    code VARCHAR(64) NOT NULL,
    name_fa VARCHAR(255) NOT NULL,
    name_en VARCHAR(255) NULL,
    short_name VARCHAR(128) NULL,
    normalized_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    item_type VARCHAR(32) NOT NULL,
    category_id INTEGER NULL,
    base_uom_id VARCHAR(32) NOT NULL,
    purchase_uom_id VARCHAR(32) NULL,
    sales_uom_id VARCHAR(32) NULL,
    issue_uom_id VARCHAR(32) NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'draft',
    revision INTEGER NOT NULL DEFAULT 1,
    is_purchasable TINYINT(1) NOT NULL DEFAULT 1,
    is_sellable TINYINT(1) NOT NULL DEFAULT 0,
    is_producible TINYINT(1) NOT NULL DEFAULT 0,
    is_stockable TINYINT(1) NOT NULL DEFAULT 1,
    is_batch_tracked TINYINT(1) NOT NULL DEFAULT 0,
    is_serial_tracked TINYINT(1) NOT NULL DEFAULT 0,
    has_expiry TINYINT(1) NOT NULL DEFAULT 0,
    shelf_life_days INTEGER NULL,
    retest_days INTEGER NULL,
    costing_method_hint VARCHAR(32) NOT NULL DEFAULT 'average',
    abc_class VARCHAR(8) NULL,
    is_used TINYINT(1) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    deleted_at VARCHAR(32) NULL,
    UNIQUE(company_id, code)
);

CREATE INDEX idx_item_master_cat_status ON item_master__items (company_id, category_id, status);
CREATE INDEX idx_item_master_norm_name ON item_master__items (company_id, normalized_name);

CREATE TABLE IF NOT EXISTS item_master__uom_conversions (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    from_uom VARCHAR(32) NOT NULL,
    to_uom VARCHAR(32) NOT NULL,
    factor DECIMAL(20,6) NOT NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, item_id, from_uom, to_uom)
);

CREATE TABLE IF NOT EXISTS item_master__barcodes (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    uom VARCHAR(32) NOT NULL,
    barcode VARCHAR(128) NOT NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    created_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, barcode)
);

CREATE TABLE IF NOT EXISTS item_master__alternates (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    alternate_item_id INTEGER NOT NULL,
    relation_type VARCHAR(32) NOT NULL DEFAULT 'interchangeable',
    notes TEXT NULL,
    created_at VARCHAR(32) NOT NULL
);
