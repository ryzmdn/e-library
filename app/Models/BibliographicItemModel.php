<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

final class BibliographicItemModel extends Model
{
    public function getLatestItems(int $limit = 6): array
    {
        $sql = "
            SELECT id, slug, isbn_13, issn, ddc_code, call_number_base, title, subtitle,
                   authors_text, publisher_name, publication_year, media_type,
                   cover_image_path, total_physical_copies, available_physical_copies,
                   rating_avg, rating_count, created_at
            FROM bibliographic_items
            ORDER BY id DESC
            LIMIT :limit
        ";

        $statement = $this->db->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $sql = "
            SELECT id, slug, isbn_13, issn, ddc_code, call_number_base, title, subtitle,
                   authors_text, publisher_name, publication_year, edition_statement,
                   language_code, physical_pages, synopsis, subject_tags, media_type,
                   cover_image_path, total_physical_copies, available_physical_copies,
                   rating_avg, rating_count, created_at
            FROM bibliographic_items
            WHERE slug = :slug
            LIMIT 1
        ";

        return $this->fetchOne($sql, [':slug' => $slug]);
    }

    public function countTotal(): int
    {
        return $this->countRows("SELECT COUNT(*) FROM bibliographic_items");
    }

    public function countAvailableCopies(): int
    {
        return (int)$this->query("SELECT IFNULL(SUM(available_physical_copies), 0) FROM bibliographic_items")->fetchColumn();
    }

    public function search(string $keyword = '', array $filters = [], int $limit = 12, int $offset = 0): array
    {
        $conditions = [];
        $params = [];

        if (trim($keyword) !== '') {
            $conditions[] = "(title LIKE :kw OR authors_text LIKE :kw OR subject_tags LIKE :kw OR synopsis LIKE :kw)";
            $params[':kw'] = '%' . trim($keyword) . '%';
        }

        if (!empty($filters['media_type'])) {
            $conditions[] = "media_type = :media_type";
            $params[':media_type'] = $filters['media_type'];
        }

        if (!empty($filters['ddc_code'])) {
            $conditions[] = "ddc_code LIKE :ddc";
            $params[':ddc'] = $filters['ddc_code'] . '%';
        }

        if (!empty($filters['only_available'])) {
            $conditions[] = "available_physical_copies > 0";
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "
            SELECT id, slug, isbn_13, issn, ddc_code, call_number_base, title, subtitle,
                   authors_text, publisher_name, publication_year, media_type,
                   cover_image_path, total_physical_copies, available_physical_copies,
                   rating_avg, rating_count
            FROM bibliographic_items
            {$whereClause}
            ORDER BY id DESC
            LIMIT :limit OFFSET :offset
        ";

        $statement = $this->db->prepare($sql);
        foreach ($params as $param => $val) {
            $statement->bindValue($param, $val);
        }
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function countSearch(string $keyword = '', array $filters = []): int
    {
        $conditions = [];
        $params = [];

        if (trim($keyword) !== '') {
            $conditions[] = "(title LIKE :kw OR authors_text LIKE :kw OR subject_tags LIKE :kw OR synopsis LIKE :kw)";
            $params[':kw'] = '%' . trim($keyword) . '%';
        }

        if (!empty($filters['media_type'])) {
            $conditions[] = "media_type = :media_type";
            $params[':media_type'] = $filters['media_type'];
        }

        if (!empty($filters['ddc_code'])) {
            $conditions[] = "ddc_code LIKE :ddc";
            $params[':ddc'] = $filters['ddc_code'] . '%';
        }

        if (!empty($filters['only_available'])) {
            $conditions[] = "available_physical_copies > 0";
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "SELECT COUNT(*) FROM bibliographic_items {$whereClause}";
        $statement = $this->db->prepare($sql);
        foreach ($params as $param => $val) {
            $statement->bindValue($param, $val);
        }
        $statement->execute();

        return (int)$statement->fetchColumn();
    }
}
