CREATE TABLE IF NOT EXISTS book_copies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    biblio_id BIGINT UNSIGNED NOT NULL,
    inventory_barcode VARCHAR(40) NOT NULL UNIQUE,
    rfid_epc_hex VARCHAR(64) DEFAULT NULL UNIQUE,
    shelf_building VARCHAR(50) NOT NULL DEFAULT 'Gedung Utama',
    shelf_floor TINYINT UNSIGNED NOT NULL DEFAULT 1,
    shelf_aisle VARCHAR(20) NOT NULL,
    condition_status ENUM('EXCELLENT', 'GOOD', 'WORN', 'DAMAGED', 'LOST') NOT NULL DEFAULT 'EXCELLENT',
    availability_status ENUM('AVAILABLE', 'BORROWED', 'RESERVED_HOLD', 'REFERENCE_ONLY', 'MAINTENANCE') NOT NULL DEFAULT 'AVAILABLE',
    acquisition_date DATE NOT NULL,
    unit_replacement_cost DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    notes VARCHAR(255) DEFAULT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_copies_biblio FOREIGN KEY (biblio_id) REFERENCES bibliographic_items(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_copies_biblio_status (biblio_id, availability_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
