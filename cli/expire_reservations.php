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
echo "[E-LIBRARY CLI] Pemeriksaan Kedaluwarsa Reservasi Buku\n";
echo "Waktu Eksekusi: " . date('Y-m-d H:i:s') . "\n";
echo "------------------------------------------------------------\n";

try {
    $service = new ScheduledTaskService();
    $result = $service->expirePendingReservations();

    echo "Status: SUKSES\n";
    echo "Total Reservasi Kedaluwarsa  : " . $result['total_expired'] . "\n";
    echo "Dialihkan ke Antrean Berikut : " . $result['reassigned_count'] . "\n";
    echo "Dikembalikan ke Rak Koleksi  : " . $result['released_to_shelf_count'] . "\n";

    if (!empty($result['details'])) {
        echo "\nRincian Penanganan Reservasi:\n";
        foreach ($result['details'] as $item) {
            echo " - ID #" . $item['reservation_id'] . " | Barcode: " . ($item['barcode'] ?: '-') . " | Tindakan: " . $item['action_taken'] . "\n";
        }
    }

    $elapsed = round(microtime(true) - $startTime, 4);
    echo "\nSelesai dalam {$elapsed} detik.\n";
    exit(0);
} catch (Throwable $e) {
    echo "Status: GAGAL\n";
    echo "Pesan Kesalahan: " . $e->getMessage() . "\n";
    exit(1);
}
