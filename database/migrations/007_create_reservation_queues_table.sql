CREATE TABLE IF NOT EXISTS reservation_queues (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    biblio_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    assigned_copy_id BIGINT UNSIGNED DEFAULT NULL,
    queue_status ENUM('WAITING', 'READY_FOR_PICKUP', 'FULFILLED', 'EXPIRED', 'CANCELLED') NOT NULL DEFAULT 'WAITING',
    requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ready_notified_at TIMESTAMP NULL DEFAULT NULL,
    pickup_expires_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_queue_biblio FOREIGN KEY (biblio_id) REFERENCES bibliographic_items(id) ON DELETE CASCADE,
    CONSTRAINT fk_queue_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_queue_copy FOREIGN KEY (assigned_copy_id) REFERENCES book_copies(id) ON DELETE SET NULL,
    INDEX idx_queue_fifo (biblio_id, queue_status, requested_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
