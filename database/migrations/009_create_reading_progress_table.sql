CREATE TABLE IF NOT EXISTS reading_progress (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    digital_asset_id BIGINT UNSIGNED NOT NULL,
    last_page_or_cfi VARCHAR(120) NOT NULL DEFAULT '1',
    completion_percent DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
    total_reading_seconds INT UNSIGNED NOT NULL DEFAULT 0,
    bookmarks_json JSON DEFAULT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_asset_progress (user_id, digital_asset_id),
    CONSTRAINT fk_progress_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_progress_asset FOREIGN KEY (digital_asset_id) REFERENCES digital_assets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
