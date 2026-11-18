<?php
declare(strict_types=1);

namespace App\Core;

use App\Config\Database;
use PDO;
use Throwable;

final class DatabaseTransaction
{
    public static function run(callable $callback): mixed
    {
        $pdo = Database::getConnection();
        $isTopLevel = !$pdo->inTransaction();

        if ($isTopLevel) {
            $pdo->beginTransaction();
        }

        try {
            $result = $callback($pdo);

            if ($isTopLevel && $pdo->inTransaction()) {
                $pdo->commit();
            }

            return $result;
        } catch (Throwable $e) {
            if ($isTopLevel && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
