CREATE TABLE IF NOT EXISTS user_favorite_books (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    biblio_id INT UNSIGNED NOT NULL,
    reading_status ENUM('UNREAD', 'READING', 'COMPLETED') NOT NULL DEFAULT 'UNREAD',
    personal_note TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_biblio_fav (user_id, biblio_id),
    INDEX idx_fav_user (user_id),
    INDEX idx_fav_biblio (biblio_id),
    CONSTRAINT fk_fav_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_fav_biblio FOREIGN KEY (biblio_id) REFERENCES bibliographic_items (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
