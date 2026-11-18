<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class DigitalAssetModel extends Model
{
    public function findByBiblioId(int $biblioId): ?array
    {
        $sql = "
            SELECT id, biblio_id, storage_filename_hash, file_format, file_size_bytes,
                   sha256_checksum, access_level, max_concurrent_readers, uploaded_by, created_at
            FROM digital_assets
            WHERE biblio_id = :biblio_id
            LIMIT 1
        ";

        return $this->fetchOne($sql, [':biblio_id' => $biblioId]);
    }

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT id, biblio_id, storage_filename_hash, file_format, file_size_bytes,
                   sha256_checksum, access_level, max_concurrent_readers, uploaded_by, created_at
            FROM digital_assets
            WHERE id = :id
            LIMIT 1
        ";

        return $this->fetchOne($sql, [':id' => $id]);
    }

    public function canUserAccess(array $digitalAsset, ?array $user): bool
    {
        $level = $digitalAsset['access_level'] ?? 'MEMBER_STREAM_ONLY';

        if ($level === 'PUBLIC_OPEN') {
            return true;
        }

        if ($user === null) {
            return false;
        }

        $role = $user['role'] ?? 'MEMBER';
        if (in_array($role, ['SUPER_ADMIN', 'LIBRARIAN', 'CATALOGER'], true)) {
            return true;
        }

        $accountStatus = $user['account_status'] ?? 'PENDING_VERIFICATION';
        if ($accountStatus !== 'ACTIVE') {
            return false;
        }

        if ($level === 'MEMBER_STREAM_ONLY') {
            return (bool)($user['can_access_digital'] ?? true);
        }

        if ($level === 'THESIS_ABSTRACT_ONLY') {
            return (bool)($user['can_access_restricted_thesis'] ?? false);
        }

        return false;
    }

    public function formatBytes(int $bytes, int $precision = 1): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min((int)$pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function createAsset(array $data): int
    {
        $sql = "
            INSERT INTO digital_assets (
                biblio_id, storage_filename_hash, file_format, file_size_bytes,
                sha256_checksum, access_level, max_concurrent_readers, uploaded_by, created_at
            ) VALUES (
                :biblio_id, :hash, :format, :size,
                :checksum, :access, :max_readers, :uploader, CURRENT_TIMESTAMP
            )
        ";
        $this->execute($sql, [
            ':biblio_id' => (int)$data['biblio_id'],
            ':hash' => $data['storage_filename_hash'],
            ':format' => $data['file_format'] ?? 'PDF',
            ':size' => (int)$data['file_size_bytes'],
            ':checksum' => $data['sha256_checksum'],
            ':access' => $data['access_level'] ?? 'MEMBER_STREAM_ONLY',
            ':max_readers' => (int)($data['max_concurrent_readers'] ?? 0),
            ':uploader' => (int)$data['uploaded_by'],
        ]);
        return (int)$this->lastInsertId();
    }

    public function deleteByBiblioId(int $biblioId): bool
    {
        $sql = "DELETE FROM digital_assets WHERE biblio_id = :biblio_id";
        return $this->execute($sql, [':biblio_id' => $biblioId]);
    }
}
