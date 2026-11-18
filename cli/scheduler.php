<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Services\ScheduledTaskService;

$startTime = microtime(true);
$action = $argv[1] ?? '--all';

echo "[E-LIBRARY CLI] Unified Task Scheduler\n";
echo "Waktu Eksekusi: " . date('Y-m-d H:i:s') . "\n";
echo "Target Tugas  : " . $action . "\n";
echo "------------------------------------------------------------\n";

try {
    $service = new ScheduledTaskService();

    if ($action === '--fines' || $action === '--all') {
        echo "Menjalankan kalkulasi denda...\n";
        $finesResult = $service->calculateDailyFines();
        echo " - Selesai: " . $finesResult['fines_assessed_count'] . " denda baru dinilai (+Rp " . number_format($finesResult['total_fine_amount'], 0, ',', '.') . ").\n";
    }

    if ($action === '--reservations' || $action === '--all') {
        echo "Menjalankan pemrosesan kedaluwarsa reservasi...\n";
        $resResult = $service->expirePendingReservations();
        echo " - Selesai: " . $resResult['total_expired'] . " reservasi kedaluwarsa diproses (" . $resResult['reassigned_count'] . " dialihkan, " . $resResult['released_to_shelf_count'] . " kembali ke rak).\n";
    }

    $elapsed = round(microtime(true) - $startTime, 4);
    echo "------------------------------------------------------------\n";
    echo "Seluruh tugas scheduler selesai dalam {$elapsed} detik.\n";
    exit(0);
} catch (Throwable $e) {
    echo "Eksekusi Scheduler GAGAL: " . $e->getMessage() . "\n";
    exit(1);
}
