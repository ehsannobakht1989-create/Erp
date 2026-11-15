CREATE TABLE IF NOT EXISTS print_barcode_qr__templates (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    company_id INTEGER NOT NULL,
    code VARCHAR(64) NOT NULL,
    name_fa VARCHAR(128) NOT NULL,
    page_size VARCHAR(16) NOT NULL DEFAULT 'A4',
    html_template TEXT NOT NULL,
    created_at VARCHAR(32) NOT NULL,
    updated_at VARCHAR(32) NOT NULL,
    UNIQUE(company_id, code)
);
