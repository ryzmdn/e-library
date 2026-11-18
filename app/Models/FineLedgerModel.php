<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class FineLedgerModel extends Model
{
    public function getTotalUnpaidFine(int $userId): float
    {
        $sql = "
            SELECT IFNULL(SUM(fl.amount), 0.00)
            FROM fine_ledgers fl
            WHERE fl.user_id = :uid
        ";
        return (float)$this->query($sql, [':uid' => $userId])->fetchColumn();
    }

    public function getLedgerByUserId(int $userId, int $limit = 10): array
    {
        $sql = "
            SELECT fl.*, lt.transaction_code
            FROM fine_ledgers fl
            LEFT JOIN loan_transactions lt ON fl.loan_id = lt.id
            WHERE fl.user_id = :uid
            ORDER BY fl.created_at DESC
            LIMIT :limit
        ";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':uid', $userId);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }
}
