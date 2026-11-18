<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

final class LoanTransactionModel extends Model
{
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT lt.*,
                   bc.inventory_barcode, bc.shelf_building, bc.shelf_floor, bc.shelf_aisle, bc.biblio_id,
                   bi.title, bi.slug, bi.authors_text, bi.call_number_base, bi.cover_image_path,
                   u.full_name AS member_name, u.member_number, u.email AS member_email, u.tier_id,
                   t.tier_name, t.daily_fine_amount, t.max_fine_cap, t.max_renewals, t.loan_duration_days
            FROM loan_transactions lt
            INNER JOIN book_copies bc ON lt.book_copy_id = bc.id
            INNER JOIN bibliographic_items bi ON bc.biblio_id = bi.id
            INNER JOIN users u ON lt.user_id = u.id
            INNER JOIN membership_tiers t ON u.tier_id = t.id
            WHERE lt.id = :id
            LIMIT 1
        ";
        return $this->fetchOne($sql, [':id' => $id]);
    }

    public function findByCode(string $code): ?array
    {
        $sql = "
            SELECT lt.*,
                   bc.inventory_barcode, bc.shelf_building, bc.shelf_floor, bc.shelf_aisle, bc.biblio_id,
                   bi.title, bi.slug, bi.authors_text, bi.call_number_base,
                   u.full_name AS member_name, u.member_number
            FROM loan_transactions lt
            INNER JOIN book_copies bc ON lt.book_copy_id = bc.id
            INNER JOIN bibliographic_items bi ON bc.biblio_id = bi.id
            INNER JOIN users u ON lt.user_id = u.id
            WHERE lt.transaction_code = :code
            LIMIT 1
        ";
        return $this->fetchOne($sql, [':code' => $code]);
    }

    public function getActiveLoanByCopyId(int $copyId): ?array
    {
        $sql = "
            SELECT lt.*,
                   bc.inventory_barcode, bc.biblio_id,
                   bi.title, bi.slug,
                   u.full_name AS member_name, u.member_number, u.tier_id,
                   t.daily_fine_amount, t.max_fine_cap, t.max_renewals
            FROM loan_transactions lt
            INNER JOIN book_copies bc ON lt.book_copy_id = bc.id
            INNER JOIN bibliographic_items bi ON bc.biblio_id = bi.id
            INNER JOIN users u ON lt.user_id = u.id
            INNER JOIN membership_tiers t ON u.tier_id = t.id
            WHERE lt.book_copy_id = :copy_id AND lt.loan_status IN ('ACTIVE', 'OVERDUE')
            ORDER BY lt.id DESC
            LIMIT 1
        ";
        return $this->fetchOne($sql, [':copy_id' => $copyId]);
    }

    public function getActiveLoansByUserId(int $userId): array
    {
        $sql = "
            SELECT lt.*,
                   bc.inventory_barcode, bc.shelf_building, bc.shelf_floor, bc.shelf_aisle, bc.biblio_id,
                   bi.title, bi.slug, bi.authors_text, bi.call_number_base, bi.cover_image_path,
                   t.max_renewals, t.daily_fine_amount
            FROM loan_transactions lt
            INNER JOIN book_copies bc ON lt.book_copy_id = bc.id
            INNER JOIN bibliographic_items bi ON bc.biblio_id = bi.id
            INNER JOIN users u ON lt.user_id = u.id
            INNER JOIN membership_tiers t ON u.tier_id = t.id
            WHERE lt.user_id = :uid AND lt.loan_status IN ('ACTIVE', 'OVERDUE')
            ORDER BY lt.due_date ASC
        ";
        return $this->fetchAll($sql, [':uid' => $userId]);
    }

    public function countActiveLoansByUserId(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM loan_transactions WHERE user_id = :uid AND loan_status IN ('ACTIVE', 'OVERDUE')";
        return $this->countRows($sql, [':uid' => $userId]);
    }

    public function countOverdueLoansByUserId(int $userId): int
    {
        $today = date('Y-m-d');
        $sql = "
            SELECT COUNT(*) FROM loan_transactions
            WHERE user_id = :uid AND (loan_status = 'OVERDUE' OR (loan_status = 'ACTIVE' AND due_date < :today))
        ";
        return $this->countRows($sql, [':uid' => $userId, ':today' => $today]);
    }

    public function getRecentHistoryByUserId(int $userId, int $limit = 5): array
    {
        $sql = "
            SELECT lt.*, bc.inventory_barcode, bi.title, bi.slug, bi.authors_text
            FROM loan_transactions lt
            INNER JOIN book_copies bc ON lt.book_copy_id = bc.id
            INNER JOIN bibliographic_items bi ON bc.biblio_id = bi.id
            WHERE lt.user_id = :uid
            ORDER BY lt.borrowed_at DESC
            LIMIT :limit
        ";

        $statement = $this->db->prepare($sql);
        $statement->bindValue(':uid', $userId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function generateTransactionCode(): string
    {
        $prefix = 'TRX-' . date('Ymd') . '-';
        $sql = "SELECT transaction_code FROM loan_transactions WHERE transaction_code LIKE :prefix ORDER BY id DESC LIMIT 1";
        $latest = $this->fetchOne($sql, [':prefix' => $prefix . '%']);

        if ($latest && preg_match('/-(\d+)$/', (string)$latest['transaction_code'], $matches)) {
            $next = (int)$matches[1] + 1;
        } else {
            $next = 1;
        }

        return $prefix . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }

    public function createLoan(array $data): int
    {
        $trxCode = $data['transaction_code'] ?? $this->generateTransactionCode();
        $idempotencyKey = $data['idempotency_key'] ?? bin2hex(random_bytes(16));

        $sql = "
            INSERT INTO loan_transactions (
                transaction_code, idempotency_key, user_id, book_copy_id,
                issued_by_staff_id, borrowed_at, due_date, renewal_count,
                loan_status, return_condition, accrued_fine, fine_settled, remarks
            ) VALUES (
                :code, :idemp, :uid, :copy_id,
                :staff_id, :borrowed_at, :due_date, 0,
                'ACTIVE', NULL, 0.00, 1, :remarks
            )
        ";

        $this->execute($sql, [
            ':code' => $trxCode,
            ':idemp' => $idempotencyKey,
            ':uid' => (int)$data['user_id'],
            ':copy_id' => (int)$data['book_copy_id'],
            ':staff_id' => (int)$data['issued_by_staff_id'],
            ':borrowed_at' => $data['borrowed_at'] ?? date('Y-m-d H:i:s'),
            ':due_date' => $data['due_date'],
            ':remarks' => $data['remarks'] ?? null,
        ]);

        return (int)$this->lastInsertId();
    }

    public function markAsReturned(int $loanId, int $staffId, string $returnCondition, float $accruedFine, bool $fineSettled, ?string $remarks = null): bool
    {
        $sql = "
            UPDATE loan_transactions
            SET returned_to_staff_id = :staff_id,
                returned_at = CURRENT_TIMESTAMP,
                loan_status = 'RETURNED',
                return_condition = :cond,
                accrued_fine = :fine,
                fine_settled = :settled,
                remarks = :remarks
            WHERE id = :id
        ";

        return $this->execute($sql, [
            ':staff_id' => $staffId,
            ':cond' => $returnCondition,
            ':fine' => $accruedFine,
            ':settled' => $fineSettled ? 1 : 0,
            ':remarks' => $remarks,
            ':id' => $loanId,
        ]);
    }

    public function recordRenewal(int $loanId, string $newDueDate): bool
    {
        $sql = "
            UPDATE loan_transactions
            SET due_date = :due_date,
                renewal_count = renewal_count + 1,
                loan_status = 'ACTIVE'
            WHERE id = :id
        ";

        return $this->execute($sql, [
            ':due_date' => $newDueDate,
            ':id' => $loanId,
        ]);
    }

    public function markFineAsSettled(int $loanId): bool
    {
        $sql = "UPDATE loan_transactions SET fine_settled = 1 WHERE id = :id";
        return $this->execute($sql, [':id' => $loanId]);
    }

    public function getOverdueLoans(): array
    {
        $today = date('Y-m-d');
        $sql = "
            SELECT lt.*,
                   bc.inventory_barcode,
                   bi.title,
                   u.full_name AS member_name, u.member_number, u.email AS member_email, u.tier_id,
                   t.daily_fine_amount, t.max_fine_cap
            FROM loan_transactions lt
            INNER JOIN book_copies bc ON lt.book_copy_id = bc.id
            INNER JOIN bibliographic_items bi ON bc.biblio_id = bi.id
            INNER JOIN users u ON lt.user_id = u.id
            INNER JOIN membership_tiers t ON u.tier_id = t.id
            WHERE lt.loan_status IN ('ACTIVE', 'OVERDUE') AND lt.due_date < :today
        ";
        return $this->fetchAll($sql, [':today' => $today]);
    }

    public function updateOverdueStatus(int $loanId, float $accruedFine): bool
    {
        $sql = "
            UPDATE loan_transactions
            SET loan_status = 'OVERDUE',
                accrued_fine = :fine,
                fine_settled = 0
            WHERE id = :id
        ";
        return $this->execute($sql, [
            ':fine' => round($accruedFine, 2),
            ':id' => $loanId,
        ]);
    }

    public function getAllLoans(array $filters = [], int $limit = 15, int $offset = 0): array
    {
        [$whereSql, $params] = $this->buildFilterCriteria($filters);

        $sql = "
            SELECT lt.*,
                   bc.inventory_barcode,
                   bi.title, bi.slug, bi.call_number_base,
                   u.full_name AS member_name, u.member_number,
                   s.full_name AS staff_name
            FROM loan_transactions lt
            INNER JOIN book_copies bc ON lt.book_copy_id = bc.id
            INNER JOIN bibliographic_items bi ON bc.biblio_id = bi.id
            INNER JOIN users u ON lt.user_id = u.id
            LEFT JOIN users s ON lt.issued_by_staff_id = s.id
            {$whereSql}
            ORDER BY lt.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAllLoans(array $filters = []): int
    {
        [$whereSql, $params] = $this->buildFilterCriteria($filters);
        $sql = "
            SELECT COUNT(*)
            FROM loan_transactions lt
            INNER JOIN book_copies bc ON lt.book_copy_id = bc.id
            INNER JOIN bibliographic_items bi ON bc.biblio_id = bi.id
            INNER JOIN users u ON lt.user_id = u.id
            {$whereSql}
        ";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function countTodayCheckouts(): int
    {
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $sql = "SELECT COUNT(*) FROM loan_transactions WHERE borrowed_at >= :start AND borrowed_at <= :end";
        return $this->countRows($sql, [':start' => $start, ':end' => $end]);
    }

    public function countTodayCheckins(): int
    {
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $sql = "SELECT COUNT(*) FROM loan_transactions WHERE returned_at >= :start AND returned_at <= :end";
        return $this->countRows($sql, [':start' => $start, ':end' => $end]);
    }

    public function countActiveLoans(): int
    {
        $sql = "SELECT COUNT(*) FROM loan_transactions WHERE loan_status IN ('ACTIVE', 'OVERDUE')";
        return $this->countRows($sql);
    }

    public function countOverdueLoans(): int
    {
        $sql = "SELECT COUNT(*) FROM loan_transactions WHERE loan_status = 'OVERDUE' OR (loan_status = 'ACTIVE' AND due_date < :today)";
        return $this->countRows($sql, [':today' => date('Y-m-d')]);
    }

    private function buildFilterCriteria(array $filters): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = "lt.loan_status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['keyword'])) {
            $conditions[] = "(lt.transaction_code LIKE :kw OR u.member_number LIKE :kw OR u.full_name LIKE :kw OR bc.inventory_barcode LIKE :kw OR bi.title LIKE :kw)";
            $params[':kw'] = '%' . trim($filters['keyword']) . '%';
        }

        $whereSql = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$whereSql, $params];
    }
}
