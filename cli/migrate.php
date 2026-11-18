<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/Config/Environment.php';

use App\Config\Environment;

Environment::load(dirname(__DIR__) . '/.env');

$host = Environment::get('DB_HOST', '127.0.0.1');
$port = Environment::get('DB_PORT', '3306');
$dbName = Environment::get('DB_NAME', 'elibrary_prod');
$user = Environment::get('DB_USER', 'root');
$pass = Environment::get('DB_PASS', '');

try {
    $serverPdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
} catch (PDOException $e) {
    fwrite(STDERR, "Gagal menghubungkan ke server MySQL: " . $e->getMessage() . PHP_EOL);
    exit(1);
}

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            batch INT UNSIGNED NOT NULL,
            executed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $stmtBatch = $pdo->query("SELECT IFNULL(MAX(batch), 0) + 1 AS next_batch FROM migrations");
    $nextBatch = (int)$stmtBatch->fetch()['next_batch'];

    $stmtExecuted = $pdo->query("SELECT migration FROM migrations");
    $executed = $stmtExecuted->fetchAll(PDO::FETCH_COLUMN);

    $migrationDir = dirname(__DIR__) . '/database/migrations';
    $files = scandir($migrationDir);
    $appliedCount = 0;

    foreach ($files as $file) {
        if (!str_ends_with($file, '.sql')) {
            continue;
        }

        if (in_array($file, $executed, true)) {
            continue;
        }

        $filePath = $migrationDir . '/' . $file;
        $sql = file_get_contents($filePath);
        if ($sql === false || trim($sql) === '') {
            continue;
        }

        echo "Menjalankan migrasi: {$file}... ";
        $pdo->beginTransaction();
        try {
            $pdo->exec($sql);

            $stmtRecord = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)");
            $stmtRecord->execute([
                ':migration' => $file,
                ':batch' => $nextBatch,
            ]);

            $pdo->commit();
            echo "[OK]" . PHP_EOL;
            $appliedCount++;
        } catch (Throwable $e) {
            $pdo->rollBack();
            echo "[GAGAL]" . PHP_EOL;
            fwrite(STDERR, "Galat pada berkas {$file}: " . $e->getMessage() . PHP_EOL);
            exit(1);
        }
    }

    if ($appliedCount === 0) {
        echo "Tidak ada berkas migrasi baru untuk dijalankan." . PHP_EOL;
    } else {
        echo "Total migrasi baru berhasil diterapkan: {$appliedCount} berkas." . PHP_EOL;
    }

    $shouldSeed = in_array('--seed', $argv, true);
    if ($shouldSeed) {
        $seedFile = dirname(__DIR__) . '/database/seeders/initial_seed.sql';
        if (file_exists($seedFile)) {
            echo "Memproses data awal (seeders)... ";
            $seedSql = file_get_contents($seedFile);
            if ($seedSql !== false && trim($seedSql) !== '') {
                $pdo->beginTransaction();
                try {
                    $pdo->exec($seedSql);
                    $pdo->commit();
                    echo "[OK]" . PHP_EOL;
                } catch (Throwable $e) {
                    $pdo->rollBack();
                    echo "[GAGAL]" . PHP_EOL;
                    fwrite(STDERR, "Galat saat seeding data: " . $e->getMessage() . PHP_EOL);
                    exit(1);
                }
            }
        }
    }

    echo "Proses migrasi basis data selesai sepenuhnya." . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, "Terjadi galat tak terduga: " . $e->getMessage() . PHP_EOL);
    exit(1);
}
