<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

final class ReadingProgressModel extends Model
{
    public function getProgress(int $userId, int $digitalAssetId): ?array
    {
        $sql = "
            SELECT id, user_id, digital_asset_id, last_page_or_cfi,
                   completion_percent, total_reading_seconds, bookmarks_json, updated_at
            FROM reading_progress
            WHERE user_id = :uid AND digital_asset_id = :asset_id
            LIMIT 1
        ";
        return $this->fetchOne($sql, [':uid' => $userId, ':asset_id' => $digitalAssetId]);
    }

    public function saveProgress(
        int $userId,
        int $digitalAssetId,
        string $lastPageOrCfi,
        float $completionPercent,
        int $additionalSeconds = 0
    ): bool {
        $completionPercent = min(100.0, max(0.0, round($completionPercent, 2)));
        $additionalSeconds = max(0, $additionalSeconds);

        $existing = $this->getProgress($userId, $digitalAssetId);
        if ($existing) {
            $sql = "
                UPDATE reading_progress
                SET last_page_or_cfi = :page,
                    completion_percent = :percent,
                    total_reading_seconds = total_reading_seconds + :sec,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ";
            return $this->execute($sql, [
                ':page' => $lastPageOrCfi,
                ':percent' => $completionPercent,
                ':sec' => $additionalSeconds,
                ':id' => $existing['id'],
            ]);
        }

        $sql = "
            INSERT INTO reading_progress (
                user_id, digital_asset_id, last_page_or_cfi,
                completion_percent, total_reading_seconds, updated_at
            ) VALUES (
                :uid, :asset_id, :page,
                :percent, :sec, CURRENT_TIMESTAMP
            )
        ";
        return $this->execute($sql, [
            ':uid' => $userId,
            ':asset_id' => $digitalAssetId,
            ':page' => $lastPageOrCfi,
            ':percent' => $completionPercent,
            ':sec' => $additionalSeconds,
        ]);
    }

    public function getBookmarks(int $userId, int $digitalAssetId): array
    {
        $row = $this->getProgress($userId, $digitalAssetId);
        if (!$row || empty($row['bookmarks_json'])) {
            return [];
        }

        $decoded = json_decode((string)$row['bookmarks_json'], true);
        return is_array($decoded) ? $decoded : [];
    }

    public function addBookmark(int $userId, int $digitalAssetId, string $pageOrCfi, string $title): bool
    {
        $bookmarks = $this->getBookmarks($userId, $digitalAssetId);
        $bookmarks[] = [
            'page' => $pageOrCfi,
            'title' => trim($title),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $json = json_encode($bookmarks, JSON_UNESCAPED_UNICODE);
        $sql = "
            UPDATE reading_progress
            SET bookmarks_json = :bookmarks, updated_at = CURRENT_TIMESTAMP
            WHERE user_id = :uid AND digital_asset_id = :asset_id
        ";
        return $this->execute($sql, [
            ':bookmarks' => $json,
            ':uid' => $userId,
            ':asset_id' => $digitalAssetId,
        ]);
    }

    public function getRecentReadingsByUserId(int $userId, int $limit = 5): array
    {
        $sql = "
            SELECT rp.*,
                   da.file_format, da.file_size_bytes,
                   bi.id AS biblio_id, bi.title, bi.slug, bi.authors_text, bi.call_number_base, bi.cover_image_path
            FROM reading_progress rp
            INNER JOIN digital_assets da ON rp.digital_asset_id = da.id
            INNER JOIN bibliographic_items bi ON da.biblio_id = bi.id
            WHERE rp.user_id = :uid
            ORDER BY rp.updated_at DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
