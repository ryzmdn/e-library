<?php
declare(strict_types=1);

namespace App\Core;

use App\Config\Database;
use PDO;
use PDOStatement;

abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    protected function query(string $sql, array $params = []): PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result !== false ? $result : null;
    }

    protected function execute(string $sql, array $params = []): bool
    {
        $statement = $this->db->prepare($sql);
        return $statement->execute($params);
    }

    protected function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }

    protected function countRows(string $sql, array $params = []): int
    {
        return (int)$this->query($sql, $params)->fetchColumn();
    }
}
