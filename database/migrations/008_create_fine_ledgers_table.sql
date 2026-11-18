CREATE TABLE IF NOT EXISTS fine_ledgers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    loan_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    entry_type ENUM('FINE_ASSESSMENT', 'DAMAGE_FEE', 'PAYMENT_CASH', 'PAYMENT_QRIS', 'WAIVER_ADMIN') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    receipt_reference VARCHAR(60) DEFAULT NULL,
    processed_by_staff_id BIGINT UNSIGNED DEFAULT NULL,
    notes VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ledger_loan FOREIGN KEY (loan_id) REFERENCES loan_transactions(id),
    CONSTRAINT fk_ledger_user FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_ledger_user (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
