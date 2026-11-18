<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class ReservationQueueModel extends Model
{
    public function getActiveReservationsByUserId(int $userId): array
    {
        $sql = "
            SELECT rq.*, bi.title, bi.slug, bi.authors_text, bi.call_number_base
            FROM reservation_queues rq
            INNER JOIN bibliographic_items bi ON rq.biblio_id = bi.id
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
}
