<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

final class ReservationQueueModel extends Model
{
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT rq.*,
                   bi.title, bi.slug, bi.authors_text, bi.call_number_base, bi.cover_image_path,
                   u.full_name AS member_name, u.member_number, u.email AS member_email,
                   bc.inventory_barcode, bc.shelf_building, bc.shelf_floor, bc.shelf_aisle
            FROM reservation_queues rq
            INNER JOIN bibliographic_items bi ON rq.biblio_id = bi.id
            INNER JOIN users u ON rq.user_id = u.id
            LEFT JOIN book_copies bc ON rq.assigned_copy_id = bc.id
            WHERE rq.id = :id
            LIMIT 1
        ";
        return $this->fetchOne($sql, [':id' => $id]);
    }

    public function getActiveReservationsByUserId(int $userId): array
    {
        $sql = "
            SELECT rq.*,
                   bi.title, bi.slug, bi.authors_text, bi.call_number_base, bi.cover_image_path,
                   bc.inventory_barcode, bc.shelf_building, bc.shelf_floor, bc.shelf_aisle
            FROM reservation_queues rq
            INNER JOIN bibliographic_items bi ON rq.biblio_id = bi.id
            LEFT JOIN book_copies bc ON rq.assigned_copy_id = bc.id
            WHERE rq.user_id = :uid AND rq.queue_status IN ('WAITING', 'READY_FOR_PICKUP')
            ORDER BY rq.requested_at ASC
        ";
        return $this->fetchAll($sql, [':uid' => $userId]);
    }

    public function countActiveReservationsByUserId(int $userId): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM reservation_queues
            WHERE user_id = :uid AND queue_status IN ('WAITING', 'READY_FOR_PICKUP')
        ";
        return $this->countRows($sql, [':uid' => $userId]);
    }

    public function getQueuePosition(int $biblioId, int $userId): int
    {
        $sql = "
            SELECT COUNT(*) + 1 AS position
            FROM reservation_queues
            WHERE biblio_id = :biblio_id
              AND queue_status = 'WAITING'
              AND requested_at < (
                  SELECT requested_at
                  FROM reservation_queues
                  WHERE biblio_id = :biblio_id AND user_id = :uid AND queue_status = 'WAITING'
                  LIMIT 1
              )
        ";
        return (int)$this->query($sql, [':biblio_id' => $biblioId, ':uid' => $userId])->fetchColumn();
    }

    public function isUserAlreadyQueued(int $biblioId, int $userId): bool
    {
        $sql = "
            SELECT COUNT(*)
            FROM reservation_queues
            WHERE biblio_id = :biblio_id AND user_id = :uid AND queue_status IN ('WAITING', 'READY_FOR_PICKUP')
        ";
        return $this->countRows($sql, [':biblio_id' => $biblioId, ':uid' => $userId]) > 0;
    }

    public function createReservation(int $biblioId, int $userId): int
    {
        $sql = "
            INSERT INTO reservation_queues (
                biblio_id, user_id, queue_status, requested_at
            ) VALUES (
                :biblio_id, :uid, 'WAITING', CURRENT_TIMESTAMP
            )
        ";
        $this->execute($sql, [
            ':biblio_id' => $biblioId,
            ':uid' => $userId,
        ]);
        return (int)$this->lastInsertId();
    }

    public function getNextWaitingReservation(int $biblioId): ?array
    {
        $sql = "
            SELECT rq.*, u.full_name AS member_name, u.member_number, u.email AS member_email
            FROM reservation_queues rq
            INNER JOIN users u ON rq.user_id = u.id
            WHERE rq.biblio_id = :biblio_id AND rq.queue_status = 'WAITING'
            ORDER BY rq.requested_at ASC
            LIMIT 1
        ";
        return $this->fetchOne($sql, [':biblio_id' => $biblioId]);
    }

    public function assignCopyToReservation(int $reservationId, int $copyId, int $pickupExpiryDays = 2): bool
    {
        $expiryTimestamp = date('Y-m-d H:i:s', strtotime("+{$pickupExpiryDays} days"));
        $sql = "
            UPDATE reservation_queues
            SET assigned_copy_id = :copy_id,
                queue_status = 'READY_FOR_PICKUP',
                ready_notified_at = CURRENT_TIMESTAMP,
                pickup_expires_at = :expires_at
            WHERE id = :id AND queue_status = 'WAITING'
        ";
        return $this->execute($sql, [
            ':copy_id' => $copyId,
            ':expires_at' => $expiryTimestamp,
            ':id' => $reservationId,
        ]);
    }

    public function markAsFulfilled(int $reservationId): bool
    {
        $sql = "UPDATE reservation_queues SET queue_status = 'FULFILLED' WHERE id = :id";
        return $this->execute($sql, [':id' => $reservationId]);
    }

    public function markAsCancelled(int $reservationId, int $userId): bool
    {
        $sql = "
            UPDATE reservation_queues
            SET queue_status = 'CANCELLED'
            WHERE id = :id AND user_id = :uid AND queue_status IN ('WAITING', 'READY_FOR_PICKUP')
        ";
        return $this->execute($sql, [
            ':id' => $reservationId,
            ':uid' => $userId,
        ]);
    }

    public function getExpiredPickups(): array
    {
        $sql = "
            SELECT rq.*, bc.inventory_barcode
            FROM reservation_queues rq
            LEFT JOIN book_copies bc ON rq.assigned_copy_id = bc.id
            WHERE rq.queue_status = 'READY_FOR_PICKUP' AND rq.pickup_expires_at < CURRENT_TIMESTAMP
        ";
        return $this->fetchAll($sql);
    }

    public function markAsExpired(int $reservationId): bool
    {
        $sql = "UPDATE reservation_queues SET queue_status = 'EXPIRED' WHERE id = :id";
        return $this->execute($sql, [':id' => $reservationId]);
    }

    public function getAllActiveReservations(int $limit = 20, int $offset = 0): array
    {
        $sql = "
            SELECT rq.*,
                   bi.title, bi.slug, bi.call_number_base,
                   u.full_name AS member_name, u.member_number,
                   bc.inventory_barcode
            FROM reservation_queues rq
            INNER JOIN bibliographic_items bi ON rq.biblio_id = bi.id
            INNER JOIN users u ON rq.user_id = u.id
            LEFT JOIN book_copies bc ON rq.assigned_copy_id = bc.id
            WHERE rq.queue_status IN ('WAITING', 'READY_FOR_PICKUP')
            ORDER BY rq.requested_at ASC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAllActiveReservations(): int
    {
        $sql = "SELECT COUNT(*) FROM reservation_queues WHERE queue_status IN ('WAITING', 'READY_FOR_PICKUP')";
        return $this->countRows($sql);
    }
}
