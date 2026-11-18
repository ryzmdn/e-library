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
echo "[E-LIBRARY CLI] Kalkulasi Denda Keterlambatan Harian\n";
echo "Waktu Eksekusi: " . date('Y-m-d H:i:s') . "\n";
echo "------------------------------------------------------------\n";

try {
    $service = new ScheduledTaskService();
    $result = $service->calculateDailyFines();

    echo "Status: SUKSES\n";
    echo "Total Peminjaman Dipindai       : " . $result['total_scanned'] . "\n";
    echo "Peminjaman Dikenakan Denda Baru : " . $result['fines_assessed_count'] . "\n";
    echo "Total Nominal Denda Baru        : Rp " . number_format($result['total_fine_amount'], 0, ',', '.') . "\n";

    if (!empty($result['details'])) {
        echo "\nRincian Transaksi Denda:\n";
        foreach ($result['details'] as $item) {
            echo " - Kode: " . $item['transaction_code'] . " | Peminjam: " . $item['member_name'] . " | Telat: " . $item['overdue_days'] . " hari | +Rp " . number_format($item['added_fine'], 0, ',', '.') . " (Total: Rp " . number_format($item['total_fine'], 0, ',', '.') . ")\n";
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
