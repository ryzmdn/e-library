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

    public function createItem(array $data): int
    {
        $slug = $this->generateSlug($data['title']);

        $sql = "
            INSERT INTO bibliographic_items (
                slug, isbn_13, issn, ddc_code, call_number_base, title, subtitle,
                authors_text, publisher_name, publication_year, edition_statement,
                language_code, physical_pages, synopsis, subject_tags, media_type,
                cover_image_path, total_physical_copies, available_physical_copies
            ) VALUES (
                :slug, :isbn_13, :issn, :ddc_code, :call_number_base, :title, :subtitle,
                :authors_text, :publisher_name, :publication_year, :edition_statement,
                :language_code, :physical_pages, :synopsis, :subject_tags, :media_type,
                :cover_image_path, 0, 0
            )
        ";

        $this->execute($sql, [
            ':slug' => $slug,
            ':isbn_13' => !empty($data['isbn_13']) ? trim((string)$data['isbn_13']) : null,
            ':issn' => !empty($data['issn']) ? trim((string)$data['issn']) : null,
            ':ddc_code' => !empty($data['ddc_code']) ? trim((string)$data['ddc_code']) : null,
            ':call_number_base' => !empty($data['call_number_base']) ? trim((string)$data['call_number_base']) : null,
            ':title' => trim((string)$data['title']),
            ':subtitle' => !empty($data['subtitle']) ? trim((string)$data['subtitle']) : null,
            ':authors_text' => !empty($data['authors_text']) ? trim((string)$data['authors_text']) : null,
            ':publisher_name' => !empty($data['publisher_name']) ? trim((string)$data['publisher_name']) : null,
            ':publication_year' => !empty($data['publication_year']) ? (int)$data['publication_year'] : null,
            ':edition_statement' => !empty($data['edition_statement']) ? trim((string)$data['edition_statement']) : null,
            ':language_code' => !empty($data['language_code']) ? trim((string)$data['language_code']) : 'id',
            ':physical_pages' => !empty($data['physical_pages']) ? (int)$data['physical_pages'] : null,
            ':synopsis' => !empty($data['synopsis']) ? trim((string)$data['synopsis']) : null,
            ':subject_tags' => !empty($data['subject_tags']) ? trim((string)$data['subject_tags']) : null,
            ':media_type' => $data['media_type'] ?? 'PRINTED_BOOK',
            ':cover_image_path' => $data['cover_image_path'] ?? null,
        ]);

        return (int)$this->lastInsertId();
    }

    public function updateItem(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowedKeys = [
            'isbn_13', 'issn', 'ddc_code', 'call_number_base', 'title', 'subtitle',
            'authors_text', 'publisher_name', 'publication_year', 'edition_statement',
            'language_code', 'physical_pages', 'synopsis', 'subject_tags', 'media_type',
            'cover_image_path'
        ];

        foreach ($allowedKeys as $key) {
            if (array_key_exists($key, $data)) {
                $fields[] = "{$key} = :{$key}";
                $val = $data[$key];
                if (in_array($key, ['publication_year', 'physical_pages'], true)) {
                    $params[":{$key}"] = ($val !== null && $val !== '') ? (int)$val : null;
                } else {
                    $params[":{$key}"] = ($val !== null && $val !== '') ? trim((string)$val) : null;
                }
            }
        }

        if (isset($data['title'])) {
            $fields[] = "slug = :slug";
            $params[':slug'] = $this->generateSlug($data['title'], $id);
        }

        if (empty($fields)) {
            return true;
        }

        $sql = "UPDATE bibliographic_items SET " . implode(', ', $fields) . " WHERE id = :id";
        return $this->execute($sql, $params);
    }

    public function deleteItem(int $id): bool
    {
        $borrowedCount = $this->countRows("
            SELECT COUNT(*) FROM book_copies
            WHERE biblio_id = :id AND availability_status = 'BORROWED'
        ", [':id' => $id]);

        if ($borrowedCount > 0) {
            return false;
        }

        $this->execute("DELETE FROM book_copies WHERE biblio_id = :id", [':id' => $id]);
        $this->execute("DELETE FROM digital_assets WHERE biblio_id = :id", [':id' => $id]);
        $this->execute("DELETE FROM reservation_queues WHERE biblio_id = :id", [':id' => $id]);

        return $this->execute("DELETE FROM bibliographic_items WHERE id = :id", [':id' => $id]);
    }

    public function generateSlug(string $title, ?int $excludeId = null): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'item-' . time();
        }

        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $sql = "SELECT COUNT(*) FROM bibliographic_items WHERE slug = :slug";
            $params = [':slug' => $slug];
            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }

            if ($this->countRows($sql, $params) === 0) {
                break;
            }

            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    public function getAllItemsAdmin(array $filters = [], int $limit = 15, int $offset = 0): array
    {
        [$whereSql, $params] = $this->buildSearchCriteria($filters['keyword'] ?? '', $filters);

        $sql = "
            SELECT id, slug, isbn_13, issn, ddc_code, call_number_base, title, subtitle,
                   authors_text, publisher_name, publication_year, media_type,
                   cover_image_path, total_physical_copies, available_physical_copies,
                   created_at
            FROM bibliographic_items
            {$whereSql}
            ORDER BY id DESC
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

    public function countAllItemsAdmin(array $filters = []): int
    {
        [$whereSql, $params] = $this->buildSearchCriteria($filters['keyword'] ?? '', $filters);
        $sql = "SELECT COUNT(*) FROM bibliographic_items {$whereSql}";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return (int)$stmt->fetchColumn();
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
