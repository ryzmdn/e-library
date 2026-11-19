<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;
use Throwable;

final class UserFavoriteBookModel extends Model
{
    public function getUserFavorites(int $userId, ?string $status = null, string $sort = 'latest'): array
    {
        $orderBy = match ($sort) {
            'oldest' => 'f.created_at ASC',
            'title_asc' => 'b.title ASC',
            default => 'f.created_at DESC',
        };

        $whereClause = 'f.user_id = :uid';
        $params = [':uid' => $userId];

        if ($status !== null && $status !== '' && $status !== 'ALL') {
            $whereClause .= ' AND f.reading_status = :status';
            $params[':status'] = strtoupper($status);
        }

        $sql = "
            SELECT f.id, f.user_id, f.biblio_id, f.reading_status, f.personal_note, f.created_at,
                   b.slug, b.title, b.authors_text, b.cover_image_path, b.media_type,
                   b.available_physical_copies, b.total_physical_copies, b.rating_avg, b.rating_count,
                   b.subject_tags
            FROM user_favorite_books f
            JOIN bibliographic_items b ON f.biblio_id = b.id
            WHERE {$whereClause}
            ORDER BY {$orderBy}
        ";

        return $this->fetchAll($sql, $params);
    }

    public function countByUser(int $userId, ?string $status = null): int
    {
        $whereClause = 'user_id = :uid';
        $params = [':uid' => $userId];

        if ($status !== null && $status !== '' && $status !== 'ALL') {
            $whereClause .= ' AND reading_status = :status';
            $params[':status'] = strtoupper($status);
        }

        $sql = "SELECT COUNT(*) FROM user_favorite_books WHERE {$whereClause}";
        return $this->countRows($sql, $params);
    }

    public function isFavorite(int $userId, int $biblioId): bool
    {
        $sql = "SELECT id FROM user_favorite_books WHERE user_id = :uid AND biblio_id = :bid LIMIT 1";
        $row = $this->fetchOne($sql, [':uid' => $userId, ':bid' => $biblioId]);
        return $row !== null;
    }

    public function addFavorite(int $userId, int $biblioId, ?string $note = null, string $status = 'UNREAD'): bool
    {
        $status = strtoupper($status);
        if (!in_array($status, ['UNREAD', 'READING', 'COMPLETED'], true)) {
            $status = 'UNREAD';
        }

        $sql = "
            INSERT INTO user_favorite_books (user_id, biblio_id, reading_status, personal_note, created_at, updated_at)
            VALUES (:uid, :bid, :status, :note, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
            ON DUPLICATE KEY UPDATE personal_note = VALUES(personal_note), reading_status = VALUES(reading_status)
        ";

        return $this->execute($sql, [
            ':uid' => $userId,
            ':bid' => $biblioId,
            ':status' => $status,
            ':note' => $note,
        ]);
    }

    public function removeFavorite(int $userId, int $biblioId): bool
    {
        $sql = "DELETE FROM user_favorite_books WHERE user_id = :uid AND biblio_id = :bid";
        return $this->execute($sql, [':uid' => $userId, ':bid' => $biblioId]);
    }

    public function toggleFavorite(int $userId, int $biblioId): bool
    {
        if ($this->isFavorite($userId, $biblioId)) {
            $this->removeFavorite($userId, $biblioId);
            return false;
        }

        $this->addFavorite($userId, $biblioId);
        return true;
    }

    public function updateNote(int $userId, int $biblioId, string $note): bool
    {
        $sql = "UPDATE user_favorite_books SET personal_note = :note, updated_at = CURRENT_TIMESTAMP WHERE user_id = :uid AND biblio_id = :bid";
        return $this->execute($sql, [
            ':note' => $note,
            ':uid' => $userId,
            ':bid' => $biblioId,
        ]);
    }

    public function updateStatus(int $userId, int $biblioId, string $status): bool
    {
        $status = strtoupper($status);
        if (!in_array($status, ['UNREAD', 'READING', 'COMPLETED'], true)) {
            $status = 'UNREAD';
        }

        $sql = "UPDATE user_favorite_books SET reading_status = :status, updated_at = CURRENT_TIMESTAMP WHERE user_id = :uid AND biblio_id = :bid";
        return $this->execute($sql, [
            ':status' => $status,
            ':uid' => $userId,
            ':bid' => $biblioId,
        ]);
    }
}
