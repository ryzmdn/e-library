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

    public function getCopyWithBiblio(int $id): ?array
    {
        $sql = "
            SELECT c.*,
                   b.title, b.slug, b.authors_text, b.ddc_code, b.call_number_base, b.publication_year, b.cover_image_path
            FROM book_copies c
            INNER JOIN bibliographic_items b ON c.biblio_id = b.id
            WHERE c.id = :id
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

    public function generateBarcode(): string
    {
        $year = date('Y');
        $prefix = "BC-{$year}-";

        $sql = "SELECT inventory_barcode FROM book_copies WHERE inventory_barcode LIKE :prefix ORDER BY id DESC LIMIT 1";
        $latest = $this->fetchOne($sql, [':prefix' => $prefix . '%']);

        if ($latest && preg_match('/-(\d+)$/', (string)$latest['inventory_barcode'], $matches)) {
            $next = (int)$matches[1] + 1;
        } else {
            $next = 101;
        }

        return $prefix . str_pad((string)$next, 5, '0', STR_PAD_LEFT);
    }

    public function createCopy(array $data): int
    {
        $barcode = !empty($data['inventory_barcode']) ? trim($data['inventory_barcode']) : $this->generateBarcode();

        $sql = "
            INSERT INTO book_copies (
                biblio_id, inventory_barcode, rfid_epc_hex, shelf_building,
                shelf_floor, shelf_aisle, condition_status, availability_status,
                acquisition_date, unit_replacement_cost, notes
            ) VALUES (
                :biblio_id, :barcode, :rfid, :building,
                :floor, :aisle, :condition, :status,
                :acq_date, :cost, :notes
            )
        ";

        $this->execute($sql, [
            ':biblio_id' => (int)$data['biblio_id'],
            ':barcode' => $barcode,
            ':rfid' => !empty($data['rfid_epc_hex']) ? trim($data['rfid_epc_hex']) : null,
            ':building' => !empty($data['shelf_building']) ? trim($data['shelf_building']) : 'Gedung Utama',
            ':floor' => isset($data['shelf_floor']) ? (int)$data['shelf_floor'] : 1,
            ':aisle' => trim((string)$data['shelf_aisle']),
            ':condition' => $data['condition_status'] ?? 'GOOD',
            ':status' => $data['availability_status'] ?? 'AVAILABLE',
            ':acq_date' => $data['acquisition_date'] ?? date('Y-m-d'),
            ':cost' => isset($data['unit_replacement_cost']) ? (float)$data['unit_replacement_cost'] : 0.00,
            ':notes' => !empty($data['notes']) ? trim($data['notes']) : null,
        ]);

        $copyId = (int)$this->lastInsertId();
        $this->syncBiblioAvailableCopies((int)$data['biblio_id']);

        return $copyId;
    }

    public function updateCopy(int $id, array $data): bool
    {
        $copy = $this->findById($id);
        if (!$copy) {
            return false;
        }

        $fields = [];
        $params = [':id' => $id];

        $keys = [
            'shelf_building', 'shelf_floor', 'shelf_aisle', 'condition_status',
            'availability_status', 'unit_replacement_cost', 'notes', 'rfid_epc_hex'
        ];

        foreach ($keys as $k) {
            if (array_key_exists($k, $data)) {
                $fields[] = "{$k} = :{$k}";
                if ($k === 'shelf_floor') {
                    $params[":{$k}"] = (int)$data[$k];
                } elseif ($k === 'unit_replacement_cost') {
                    $params[":{$k}"] = (float)$data[$k];
                } else {
                    $params[":{$k}"] = ($data[$k] !== null && $data[$k] !== '') ? trim((string)$data[$k]) : null;
                }
            }
        }

        if (empty($fields)) {
            return true;
        }

        $sql = "UPDATE book_copies SET " . implode(', ', $fields) . " WHERE id = :id";
        $success = $this->execute($sql, $params);

        if ($success) {
            $this->syncBiblioAvailableCopies((int)$copy['biblio_id']);
        }

        return $success;
    }

    public function deleteCopy(int $id): bool
    {
        $copy = $this->findById($id);
        if (!$copy) {
            return false;
        }

        if ($copy['availability_status'] === 'BORROWED') {
            return false;
        }

        $success = $this->execute("DELETE FROM book_copies WHERE id = :id", [':id' => $id]);
        if ($success) {
            $this->syncBiblioAvailableCopies((int)$copy['biblio_id']);
        }

        return $success;
    }

    public function getAllCopiesAdmin(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        [$whereSql, $params] = $this->buildFilterCriteria($filters);

        $sql = "
            SELECT c.*,
                   b.title, b.slug, b.authors_text, b.call_number_base
            FROM book_copies c
            INNER JOIN bibliographic_items b ON c.biblio_id = b.id
            {$whereSql}
            ORDER BY c.id DESC
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

    public function countAllCopiesAdmin(array $filters = []): int
    {
        [$whereSql, $params] = $this->buildFilterCriteria($filters);
        $sql = "
            SELECT COUNT(*)
            FROM book_copies c
            INNER JOIN bibliographic_items b ON c.biblio_id = b.id
            {$whereSql}
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    private function buildFilterCriteria(array $filters): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = "c.availability_status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['condition'])) {
            $conditions[] = "c.condition_status = :condition";
            $params[':condition'] = $filters['condition'];
        }

        if (!empty($filters['biblio_id'])) {
            $conditions[] = "c.biblio_id = :biblio_id";
            $params[':biblio_id'] = (int)$filters['biblio_id'];
        }

        if (!empty($filters['keyword'])) {
            $conditions[] = "(c.inventory_barcode LIKE :kw OR b.title LIKE :kw OR b.call_number_base LIKE :kw OR c.shelf_aisle LIKE :kw)";
            $params[':kw'] = '%' . trim($filters['keyword']) . '%';
        }

        $whereSql = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$whereSql, $params];
    }
}
