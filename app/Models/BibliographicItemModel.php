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

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT id, slug, isbn_13, issn, ddc_code, call_number_base, title, subtitle,
                   authors_text, publisher_name, publication_year, edition_statement,
                   language_code, physical_pages, synopsis, subject_tags, media_type,
                   cover_image_path, total_physical_copies, available_physical_copies,
                   rating_avg, rating_count, created_at, updated_at
            FROM bibliographic_items
            WHERE id = :id
            LIMIT 1
        ";

        return $this->fetchOne($sql, [':id' => $id]);
    }

    public function findBySlug(string $slug): ?array
    {
        $sql = "
            SELECT id, slug, isbn_13, issn, ddc_code, call_number_base, title, subtitle,
                   authors_text, publisher_name, publication_year, edition_statement,
                   language_code, physical_pages, synopsis, subject_tags, media_type,
                   cover_image_path, total_physical_copies, available_physical_copies,
                   rating_avg, rating_count, created_at, updated_at
            FROM bibliographic_items
            WHERE slug = :slug
            LIMIT 1
        ";

        return $this->fetchOne($sql, [':slug' => $slug]);
    }

    public function findBySlugOrId(string $identifier): ?array
    {
        if (ctype_digit($identifier)) {
            $item = $this->findById((int)$identifier);
            if ($item !== null) {
                return $item;
            }
        }

        return $this->findBySlug($identifier);
    }

    public function countTotal(): int
    {
        return $this->countRows("SELECT COUNT(*) FROM bibliographic_items");
    }

    public function countAvailableCopies(): int
    {
        return (int)$this->query("SELECT IFNULL(SUM(available_physical_copies), 0) FROM bibliographic_items")->fetchColumn();
    }

    public function search(string $keyword = '', array $filters = [], string $sort = 'latest', int $limit = 12, int $offset = 0): array
    {
        [$whereSql, $params] = $this->buildSearchCriteria($keyword, $filters);
        $orderBySql = $this->resolveSortColumn($sort);

        $sql = "
            SELECT id, slug, isbn_13, issn, ddc_code, call_number_base, title, subtitle,
                   authors_text, publisher_name, publication_year, media_type,
                   cover_image_path, total_physical_copies, available_physical_copies,
                   rating_avg, rating_count
            FROM bibliographic_items
            {$whereSql}
            ORDER BY {$orderBySql}
            LIMIT :limit OFFSET :offset
        ";

        $statement = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $statement->bindValue($key, $val);
        }
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function countSearch(string $keyword = '', array $filters = []): int
    {
        [$whereSql, $params] = $this->buildSearchCriteria($keyword, $filters);

        $sql = "SELECT COUNT(*) FROM bibliographic_items {$whereSql}";
        $statement = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $statement->bindValue($key, $val);
        }
        $statement->execute();

        return (int)$statement->fetchColumn();
    }

    public function getRelatedItems(int $currentItemId, string $subjectTags, int $limit = 4): array
    {
        $tags = array_filter(array_map('trim', explode(',', $subjectTags)));
        if (empty($tags)) {
            $sql = "
                SELECT id, slug, isbn_13, ddc_code, call_number_base, title, authors_text,
                       publisher_name, publication_year, media_type, cover_image_path,
                       available_physical_copies, rating_avg, rating_count
                FROM bibliographic_items
                WHERE id != :current_id
                ORDER BY id DESC
                LIMIT :limit
            ";

            $statement = $this->db->prepare($sql);
            $statement->bindValue(':current_id', $currentItemId, PDO::PARAM_INT);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->execute();

            return $statement->fetchAll();
        }

        $conditions = [];
        $params = [':current_id' => $currentItemId];

        foreach (array_slice($tags, 0, 3) as $idx => $tag) {
            $paramKey = ':tag_' . $idx;
            $conditions[] = "subject_tags LIKE {$paramKey}";
            $params[$paramKey] = '%' . $tag . '%';
        }

        $whereClause = implode(' OR ', $conditions);

        $sql = "
            SELECT id, slug, isbn_13, ddc_code, call_number_base, title, authors_text,
                   publisher_name, publication_year, media_type, cover_image_path,
                   available_physical_copies, rating_avg, rating_count
            FROM bibliographic_items
            WHERE id != :current_id AND ({$whereClause})
            ORDER BY id DESC
            LIMIT :limit
        ";

        $statement = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $statement->bindValue($key, $val);
        }
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        $results = $statement->fetchAll();

        if (count($results) < $limit) {
            $fallbackLimit = $limit - count($results);
            $excludedIds = array_merge([$currentItemId], array_column($results, 'id'));
            $placeholders = implode(',', array_fill(0, count($excludedIds), '?'));

            $fallbackSql = "
                SELECT id, slug, isbn_13, ddc_code, call_number_base, title, authors_text,
                       publisher_name, publication_year, media_type, cover_image_path,
                       available_physical_copies, rating_avg, rating_count
                FROM bibliographic_items
                WHERE id NOT IN ({$placeholders})
                ORDER BY id DESC
                LIMIT ?
            ";

            $fallbackStmt = $this->db->prepare($fallbackSql);
            $bindIdx = 1;
            foreach ($excludedIds as $exId) {
                $fallbackStmt->bindValue($bindIdx++, (int)$exId, PDO::PARAM_INT);
            }
            $fallbackStmt->bindValue($bindIdx, $fallbackLimit, PDO::PARAM_INT);
            $fallbackStmt->execute();

            $results = array_merge($results, $fallbackStmt->fetchAll());
        }

        return $results;
    }

    public function getDdcCategories(): array
    {
        $categories = [
            '000' => '000 - Karya Umum, Komputer & Informasi',
            '100' => '100 - Filsafat & Psikologi',
            '200' => '200 - Agama',
            '300' => '300 - Ilmu-Ilmu Sosial',
            '400' => '400 - Bahasa & Linguistik',
            '500' => '500 - Sains Murni & Matematika',
            '600' => '600 - Teknologi & Ilmu Terapan',
            '700' => '700 - Kesenian & Rekreasi',
            '800' => '800 - Kesusastraan',
            '900' => '900 - Sejarah & Geografi',
        ];

        $sql = "
            SELECT SUBSTRING(ddc_code, 1, 1) AS ddc_main, COUNT(*) AS total
            FROM bibliographic_items
            GROUP BY ddc_main
        ";

        $rows = $this->fetchAll($sql);
        $counts = [];
        foreach ($rows as $row) {
            $prefix = (string)$row['ddc_main'] . '00';
            $counts[$prefix] = (int)$row['total'];
        }

        $result = [];
        foreach ($categories as $code => $label) {
            $result[] = [
                'code' => $code,
                'label' => $label,
                'count' => $counts[$code] ?? 0,
            ];
        }

        return $result;
    }

    private function buildSearchCriteria(string $keyword, array $filters): array
    {
        $conditions = [];
        $params = [];

        $trimmed = trim($keyword);
        if ($trimmed !== '') {
            $conditions[] = "(title LIKE :kw OR authors_text LIKE :kw OR subject_tags LIKE :kw OR synopsis LIKE :kw OR isbn_13 LIKE :kw OR call_number_base LIKE :kw)";
            $params[':kw'] = '%' . $trimmed . '%';
        }

        if (!empty($filters['media_type'])) {
            $allowedTypes = ['PHYSICAL_ONLY', 'DIGITAL_ONLY', 'HYBRID', 'THESIS_REPOSITORY'];
            if (in_array($filters['media_type'], $allowedTypes, true)) {
                $conditions[] = "media_type = :media_type";
                $params[':media_type'] = $filters['media_type'];
            }
        }

        if (!empty($filters['ddc_code'])) {
            $ddcPrefix = substr(preg_replace('/[^0-9]/', '', (string)$filters['ddc_code']), 0, 1);
            if ($ddcPrefix !== '') {
                $conditions[] = "ddc_code LIKE :ddc";
                $params[':ddc'] = $ddcPrefix . '%';
            }
        }

        if (!empty($filters['only_available'])) {
            $conditions[] = "(available_physical_copies > 0 OR media_type = 'DIGITAL_ONLY')";
        }

        if (!empty($filters['year_from']) && ctype_digit((string)$filters['year_from'])) {
            $conditions[] = "publication_year >= :year_from";
            $params[':year_from'] = (int)$filters['year_from'];
        }

        if (!empty($filters['year_to']) && ctype_digit((string)$filters['year_to'])) {
            $conditions[] = "publication_year <= :year_to";
            $params[':year_to'] = (int)$filters['year_to'];
        }

        $whereSql = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return [$whereSql, $params];
    }

    private function resolveSortColumn(string $sort): string
    {
        return match ($sort) {
            'popular' => 'rating_count DESC, rating_avg DESC, id DESC',
            'year_desc' => 'publication_year DESC, id DESC',
            'year_asc' => 'publication_year ASC, id DESC',
            'title_asc' => 'title ASC, id DESC',
            'title_desc' => 'title DESC, id DESC',
            default => 'id DESC',
        };
    }
}
