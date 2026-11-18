<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

final class LoanTransactionModel extends Model
{
    public function getActiveLoansByUserId(int $userId): array
    {
        $sql = "
            SELECT lt.*, bc.inventory_barcode, bc.shelf_building, bc.shelf_floor, bc.shelf_aisle,
                   bi.title, bi.slug, bi.authors_text, bi.call_number_base, bi.cover_image_path
            FROM loan_transactions lt
            INNER JOIN book_copies bc ON lt.book_copy_id = bc.id
            INNER JOIN bibliographic_items bi ON bc.biblio_id = bi.id
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
        $sql = "
            SELECT COUNT(*) FROM loan_transactions
            WHERE user_id = :uid AND (loan_status = 'OVERDUE' OR (loan_status = 'ACTIVE' AND due_date < CURDATE()))
        ";
        return $this->countRows($sql, [':uid' => $userId]);
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
}
