<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

final class AuditLogModel extends Model
{
    public function log(
        ?int $actorUserId,
        string $actionEvent,
        string $targetEntity,
        ?int $targetId,
        ?array $payloadDiff = null,
        ?string $ip = null,
        ?string $ua = null
    ): bool {
        $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $ua = $ua ?? substr($_SERVER['HTTP_USER_AGENT'] ?? 'CLI/System', 0, 255);
        $payloadJson = $payloadDiff !== null ? json_encode($payloadDiff, JSON_UNESCAPED_UNICODE) : null;

        $sql = "
            INSERT INTO audit_logs (
                actor_user_id, action_event, target_entity, target_id,
                ip_address, user_agent, payload_diff_json, created_at
            ) VALUES (
                :actor, :event, :entity, :target_id,
                :ip, :ua, :payload, CURRENT_TIMESTAMP
            )
        ";

        return $this->execute($sql, [
            ':actor' => $actorUserId,
            ':event' => $actionEvent,
            ':entity' => $targetEntity,
            ':target_id' => $targetId,
            ':ip' => $ip,
            ':ua' => $ua,
            ':payload' => $payloadJson,
        ]);
    }

    public function getRecentLogs(int $limit = 20, int $offset = 0): array
    {
        $sql = "
            SELECT al.*, u.full_name AS actor_name, u.member_number AS actor_member_number
            FROM audit_logs al
            LEFT JOIN users u ON al.actor_user_id = u.id
            ORDER BY al.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
