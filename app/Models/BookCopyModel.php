<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

final class BookCopyModel extends Model
{
    public function findByBiblioId(int $biblioId): array
    {
        $sql = "
            SELECT id, biblio_id, inventory_barcode, rfid_epc_hex, shelf_building,
                   shelf_floor, shelf_aisle, condition_status, availability_status,
                   acquisition_date, unit_replacement_cost, notes, updated_at
            FROM book_copies
            WHERE biblio_id = :biblio_id
            ORDER BY shelf_floor ASC, shelf_aisle ASC, inventory_barcode ASC
        ";

        return $this->fetchAll($sql, [':biblio_id' => $biblioId]);
    }

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT id, biblio_id, inventory_barcode, rfid_epc_hex, shelf_building,
                   shelf_floor, shelf_aisle, condition_status, availability_status,
                   acquisition_date, unit_replacement_cost, notes, updated_at
            FROM book_copies
            WHERE id = :id
            LIMIT 1
        ";

        return $this->fetchOne($sql, [':id' => $id]);
    }

    public function findByBarcode(string $barcode): ?array
    {
        $sql = "
            SELECT c.id, c.biblio_id, c.inventory_barcode, c.rfid_epc_hex, c.shelf_building,
                   c.shelf_floor, c.shelf_aisle, c.condition_status, c.availability_status,
                   c.acquisition_date, c.unit_replacement_cost, c.notes,
                   b.title, b.authors_text, b.call_number_base, b.slug
            FROM book_copies c
            JOIN bibliographic_items b ON b.id = c.biblio_id
            WHERE c.inventory_barcode = :barcode
            LIMIT 1
        ";

        return $this->fetchOne($sql, [':barcode' => trim($barcode)]);
    }

    public function countAvailableByBiblioId(int $biblioId): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM book_copies
            WHERE biblio_id = :biblio_id AND availability_status = 'AVAILABLE'
        ";

        return (int)$this->query($sql, [':biblio_id' => $biblioId])->fetchColumn();
    }

    public function countTotalByBiblioId(int $biblioId): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM book_copies
            WHERE biblio_id = :biblio_id
        ";

        return (int)$this->query($sql, [':biblio_id' => $biblioId])->fetchColumn();
    }

    public function updateAvailabilityStatus(int $copyId, string $status): bool
    {
        $sql = "UPDATE book_copies SET availability_status = :status WHERE id = :id";
        $success = $this->execute($sql, [':status' => $status, ':id' => $copyId]);

        if ($success) {
            $copy = $this->findById($copyId);
            if ($copy) {
                $this->syncBiblioAvailableCopies((int)$copy['biblio_id']);
            }
        }

        return $success;
    }

    public function syncBiblioAvailableCopies(int $biblioId): void
    {
        $totalSql = "SELECT COUNT(*) FROM book_copies WHERE biblio_id = :id";
        $availSql = "SELECT COUNT(*) FROM book_copies WHERE biblio_id = :id AND availability_status = 'AVAILABLE'";

        $total = (int)$this->query($totalSql, [':id' => $biblioId])->fetchColumn();
        $avail = (int)$this->query($availSql, [':id' => $biblioId])->fetchColumn();

        $updateSql = "
            UPDATE bibliographic_items
            SET total_physical_copies = :total, available_physical_copies = :avail
            WHERE id = :id
        ";

        $this->execute($updateSql, [
            ':total' => $total,
            ':avail' => $avail,
            ':id' => $biblioId,
        ]);
    }
}
