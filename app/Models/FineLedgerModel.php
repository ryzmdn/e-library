<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

final class FineLedgerModel extends Model
{
    public function getTotalUnpaidFine(int $userId): float
    {
        $sql = "
            SELECT
                IFNULL(SUM(CASE WHEN entry_type IN ('FINE_ASSESSMENT', 'DAMAGE_FEE') THEN amount ELSE 0 END), 0.00)
                -
                IFNULL(SUM(CASE WHEN entry_type IN ('PAYMENT_CASH', 'PAYMENT_QRIS', 'WAIVER_ADMIN') THEN amount ELSE 0 END), 0.00) AS unpaid_balance
            FROM fine_ledgers
            WHERE user_id = :uid
        ";
        $balance = (float)$this->query($sql, [':uid' => $userId])->fetchColumn();
        return max(0.00, round($balance, 2));
    }

    public function getOutstandingBalanceForLoan(int $loanId): float
    {
        $sql = "
            SELECT
                IFNULL(SUM(CASE WHEN entry_type IN ('FINE_ASSESSMENT', 'DAMAGE_FEE') THEN amount ELSE 0 END), 0.00)
                -
                IFNULL(SUM(CASE WHEN entry_type IN ('PAYMENT_CASH', 'PAYMENT_QRIS', 'WAIVER_ADMIN') THEN amount ELSE 0 END), 0.00) AS unpaid_balance
            FROM fine_ledgers
            WHERE loan_id = :loan_id
        ";
        $balance = (float)$this->query($sql, [':loan_id' => $loanId])->fetchColumn();
        return max(0.00, round($balance, 2));
    }

    public function getTotalAssessedFineForLoan(int $loanId): float
    {
        $sql = "
            SELECT IFNULL(SUM(amount), 0.00)
            FROM fine_ledgers
            WHERE loan_id = :loan_id AND entry_type = 'FINE_ASSESSMENT'
        ";
        return (float)$this->query($sql, [':loan_id' => $loanId])->fetchColumn();
    }

    public function getLedgerByUserId(int $userId, int $limit = 20): array
    {
        $sql = "
            SELECT fl.*, lt.transaction_code, bi.title AS book_title
            FROM fine_ledgers fl
            LEFT JOIN loan_transactions lt ON fl.loan_id = lt.id
            LEFT JOIN book_copies bc ON lt.book_copy_id = bc.id
            LEFT JOIN bibliographic_items bi ON bc.biblio_id = bi.id
            WHERE fl.user_id = :uid
            ORDER BY fl.id DESC
            LIMIT :limit
        ";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':uid', $userId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function recordAssessment(int $loanId, int $userId, float $amount, ?string $notes = null): int
    {
        $sql = "
            INSERT INTO fine_ledgers (
                loan_id, user_id, entry_type, amount, notes, created_at
            ) VALUES (
                :loan_id, :uid, 'FINE_ASSESSMENT', :amount, :notes, CURRENT_TIMESTAMP
            )
        ";
        $this->execute($sql, [
            ':loan_id' => $loanId,
            ':uid' => $userId,
            ':amount' => round($amount, 2),
            ':notes' => $notes,
        ]);
        return (int)$this->lastInsertId();
    }

    public function recordDamageFee(int $loanId, int $userId, float $amount, int $staffId, ?string $notes = null): int
    {
        $sql = "
            INSERT INTO fine_ledgers (
                loan_id, user_id, entry_type, amount, processed_by_staff_id, notes, created_at
            ) VALUES (
                :loan_id, :uid, 'DAMAGE_FEE', :amount, :staff_id, :notes, CURRENT_TIMESTAMP
            )
        ";
        $this->execute($sql, [
            ':loan_id' => $loanId,
            ':uid' => $userId,
            ':amount' => round($amount, 2),
            ':staff_id' => $staffId,
            ':notes' => $notes,
        ]);
        return (int)$this->lastInsertId();
    }

    public function recordPayment(int $loanId, int $userId, string $type, float $amount, int $staffId, ?string $receipt = null, ?string $notes = null): int
    {
        $allowed = ['PAYMENT_CASH', 'PAYMENT_QRIS', 'WAIVER_ADMIN'];
        if (!in_array($type, $allowed, true)) {
            $type = 'PAYMENT_CASH';
        }

        $sql = "
            INSERT INTO fine_ledgers (
                loan_id, user_id, entry_type, amount, receipt_reference, processed_by_staff_id, notes, created_at
            ) VALUES (
                :loan_id, :uid, :type, :amount, :receipt, :staff_id, :notes, CURRENT_TIMESTAMP
            )
        ";
        $this->execute($sql, [
            ':loan_id' => $loanId,
            ':uid' => $userId,
            ':type' => $type,
            ':amount' => round($amount, 2),
            ':receipt' => $receipt,
            ':staff_id' => $staffId,
            ':notes' => $notes,
        ]);
        return (int)$this->lastInsertId();
    }

    public function getUnpaidUsersList(int $limit = 20, int $offset = 0): array
    {
        $sql = "
            SELECT
                u.id AS user_id, u.member_number, u.full_name, u.phone_number, u.email,
                (
                    IFNULL(SUM(CASE WHEN fl.entry_type IN ('FINE_ASSESSMENT', 'DAMAGE_FEE') THEN fl.amount ELSE 0 END), 0.00)
                    -
                    IFNULL(SUM(CASE WHEN fl.entry_type IN ('PAYMENT_CASH', 'PAYMENT_QRIS', 'WAIVER_ADMIN') THEN fl.amount ELSE 0 END), 0.00)
                ) AS unpaid_total
            FROM users u
            INNER JOIN fine_ledgers fl ON u.id = fl.user_id
            GROUP BY u.id
            HAVING unpaid_total > 0
            ORDER BY unpaid_total DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countUnpaidUsers(): int
    {
        $sql = "
            SELECT COUNT(*) FROM (
                SELECT u.id
                FROM users u
                INNER JOIN fine_ledgers fl ON u.id = fl.user_id
                GROUP BY u.id
                HAVING (
                    IFNULL(SUM(CASE WHEN fl.entry_type IN ('FINE_ASSESSMENT', 'DAMAGE_FEE') THEN fl.amount ELSE 0 END), 0.00)
                    -
                    IFNULL(SUM(CASE WHEN fl.entry_type IN ('PAYMENT_CASH', 'PAYMENT_QRIS', 'WAIVER_ADMIN') THEN fl.amount ELSE 0 END), 0.00)
                ) > 0
            ) as t
        ";
        return (int)$this->query($sql)->fetchColumn();
    }

    public function getRecentPayments(int $limit = 15): array
    {
        $sql = "
            SELECT fl.*, u.full_name AS member_name, u.member_number,
                   lt.transaction_code, s.full_name AS staff_name
            FROM fine_ledgers fl
            INNER JOIN users u ON fl.user_id = u.id
            LEFT JOIN loan_transactions lt ON fl.loan_id = lt.id
            LEFT JOIN users s ON fl.processed_by_staff_id = s.id
            WHERE fl.entry_type IN ('PAYMENT_CASH', 'PAYMENT_QRIS', 'WAIVER_ADMIN')
            ORDER BY fl.id DESC
            LIMIT :limit
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
