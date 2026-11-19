<?php
declare(strict_types=1);

namespace Tests;

final class SystemVerification
{
    private int $passedCount = 0;
    private int $failedCount = 0;
    private array $failures = [];

    public function run(): int
    {
        $this->testPublicRoutes();
        $this->testProtectedMemberRoutes();
        $this->testSecurityRedirects();
        $this->testCliTasks();
        $this->printSummary();

        return $this->failedCount === 0 ? 0 : 1;
    }

    private function testPublicRoutes(): void
    {
        $cases = [
            [
                'uri' => '/',
                'name' => 'Layar Beranda RuangBaca (Gambar 1)',
                'expected' => ['RuangBaca', 'Buku Pilihan Minggu Ini', 'Rekomendasi Terpopuler', 'Atomic Habits'],
            ],
            [
                'uri' => '/catalog',
                'name' => 'Layar Katalog Koleksi Digital OPAC (Gambar 3)',
                'expected' => ['Katalog Koleksi Digital', 'Semua Buku', 'Filosofi Teras', 'Atomic Habits'],
            ],
            [
                'uri' => '/catalog/filosofi-teras',
                'name' => 'Layar Detail Koleksi Buku Publik (Gambar 2)',
                'expected' => ['Filosofi Teras', 'Henry Manampiring', 'Baca Sekarang', 'Sinopsis Lengkap'],
            ],
            [
                'uri' => '/favorites',
                'name' => 'Layar Koleksi Favorit Publik (Gambar 4)',
                'expected' => ['Koleksi Favorit Saya', 'Laut Bercerita', 'Atomic Habits', 'Target Baca 2024'],
            ],
            [
                'uri' => '/history',
                'name' => 'Layar Jurnal & Riwayat Membaca Publik (Gambar 5)',
                'expected' => ['Riwayat Membaca', 'SEDANG DIBACA', 'Buku Selesai Dibaca', 'Refleksi Bulan Ini', '6 Hari Beruntun'],
            ],
            [
                'uri' => '/login',
                'name' => 'Layar Masuk Sistem Otentikasi',
                'expected' => ['Masuk ke Sistem', '_csrf_token', 'password'],
            ],
            [
                'uri' => '/register',
                'name' => 'Layar Pendaftaran Anggota Baru',
                'expected' => ['Pendaftaran Anggota', '_csrf_token', 'full_name'],
            ],
        ];

        foreach ($cases as $c) {
            $this->runIsolatedRouteTest($c['uri'], $c['name'], $c['expected'], false);
        }
    }

    private function testProtectedMemberRoutes(): void
    {
        $cases = [
            [
                'uri' => '/catalog/filosofi-teras',
                'name' => 'Layar Detail Buku dengan Opsi Pinjam (Anggota Sarah)',
                'expected' => ['Filosofi Teras', 'Pinjam Buku (14 Hari)'],
            ],
            [
                'uri' => '/member/favorites',
                'name' => 'Layar Koleksi Favorit Anggota Terotentikasi',
                'expected' => ['Koleksi Favorit Saya', 'Laut Bercerita', 'Atomic Habits', 'Target Baca 2024'],
            ],
            [
                'uri' => '/member/history',
                'name' => 'Layar Jurnal & Riwayat Anggota Terotentikasi',
                'expected' => ['Riwayat Membaca', 'SEDANG DIBACA', 'SELESAI TAHUN INI', 'Buku Selesai Dibaca'],
            ],
            [
                'uri' => '/reader/filosofi-teras',
                'name' => 'Layar Pembaca Digital Terproteksi Anggota',
                'expected' => ['Filosofi Teras', 'reader-container', 'reader-viewport'],
            ],
        ];

        foreach ($cases as $c) {
            $this->runIsolatedRouteTest($c['uri'], $c['name'], $c['expected'], true);
        }
    }

    private function testSecurityRedirects(): void
    {
        $protectedUris = [
            '/member/dashboard',
            '/admin/dashboard',
        ];

        foreach ($protectedUris as $uri) {
            $phpCode = 'ini_set(\'session.save_path\', sys_get_temp_dir());'
                . '$_SERVER[\'REQUEST_METHOD\'] = \'GET\';'
                . '$_SERVER[\'REQUEST_URI\'] = \'' . $uri . '\';'
                . '$_SERVER[\'HTTP_HOST\'] = \'localhost\';'
                . '$_SERVER[\'SCRIPT_NAME\'] = \'/index.php\';'
                . 'chdir(\'' . addslashes(__DIR__ . '/../public') . '\');'
                . 'ob_start();'
                . 'require \'' . addslashes(__DIR__ . '/../public/index.php') . '\';'
                . 'ob_end_clean();'
                . 'echo http_response_code();';

            $output = [];
            $code = 0;
            exec('d:\\xampp\\php\\php.exe -r "' . $phpCode . '" 2>&1', $output, $code);
            $statusCode = trim(implode('', $output));

            if ($statusCode === '302') {
                $this->recordSuccess('Proteksi Akses Tamu Tanpa Sesi (' . $uri . ' -> Redirect 302)');
            } else {
                $this->recordFailure('Proteksi Akses Tamu Tanpa Sesi (' . $uri . ')', 'Expected 302 Redirect, got ' . $statusCode);
            }
        }
    }

    private function runIsolatedRouteTest(string $uri, string $name, array $expectedStrings, bool $asMember): void
    {
        $sessionMock = 'ini_set(\'session.save_path\', sys_get_temp_dir());';
        if ($asMember) {
            $sessionMock .= 'session_start();'
                . '$_SESSION[\'_user\'] = ['
                . '\'id\' => 4,'
                . '\'full_name\' => \'Sarah Anggita\','
                . '\'email\' => \'sarah@elibrary.local\','
                . '\'role\' => \'MEMBER\','
                . '\'tier_id\' => 1'
                . '];'
                . 'session_write_close();';
        }

        $phpCode = $sessionMock
            . '$_SERVER[\'REQUEST_METHOD\'] = \'GET\';'
            . '$_SERVER[\'REQUEST_URI\'] = \'' . $uri . '\';'
            . '$_SERVER[\'HTTP_HOST\'] = \'localhost\';'
            . '$_SERVER[\'SCRIPT_NAME\'] = \'/index.php\';'
            . 'chdir(\'' . addslashes(__DIR__ . '/../public') . '\');'
            . 'ob_start();'
            . 'require \'' . addslashes(__DIR__ . '/../public/index.php') . '\';'
            . '$body = ob_get_clean();'
            . '$status = http_response_code();'
            . 'echo json_encode([\'status\' => $status, \'body\' => $body]);';

        $output = [];
        $code = 0;
        exec('d:\\xampp\\php\\php.exe -r "' . $phpCode . '" 2>&1', $output, $code);
        $rawResult = implode('', $output);

        $decoded = json_decode($rawResult, true);
        if (!is_array($decoded) || !isset($decoded['status'])) {
            $this->recordFailure($name . ' (' . $uri . ')', 'Subprocess returned invalid payload: ' . substr($rawResult, 0, 150));
            return;
        }

        $status = (int)$decoded['status'];
        if ($status !== 200) {
            $this->recordFailure($name . ' (' . $uri . ')', 'Expected status 200, got ' . $status);
            return;
        }

        $body = (string)$decoded['body'];
        foreach ($expectedStrings as $exp) {
            if (stripos($body, $exp) === false) {
                $this->recordFailure($name . ' (' . $uri . ')', 'Missing expected text: ' . $exp);
                return;
            }
        }

        $this->recordSuccess($name . ' (' . $uri . ')');
    }

    private function testCliTasks(): void
    {
        $cliScripts = [
            'cli/calculate_fines.php',
            'cli/expire_reservations.php',
            'cli/migrate.php',
            'cli/scheduler.php',
        ];

        foreach ($cliScripts as $script) {
            $fullPath = __DIR__ . '/../' . $script;
            if (!file_exists($fullPath)) {
                $this->recordFailure('CLI Task: ' . $script, 'File does not exist');
                continue;
            }

            $output = [];
            $code = 0;
            exec('d:\\xampp\\php\\php.exe -l ' . escapeshellarg($fullPath) . ' 2>&1', $output, $code);

            if ($code !== 0) {
                $this->recordFailure('CLI Task Lint: ' . $script, implode(' ', $output));
            } else {
                $this->recordSuccess('CLI Task Syntax Clean: ' . $script);
            }
        }
    }

    private function recordSuccess(string $name): void
    {
        $this->passedCount++;
        echo '[PASS] ' . $name . PHP_EOL;
    }

    private function recordFailure(string $name, string $reason): void
    {
        $this->failedCount++;
        $this->failures[] = ['name' => $name, 'reason' => $reason];
        echo '[FAIL] ' . $name . ' -> ' . $reason . PHP_EOL;
    }

    private function printSummary(): void
    {
        echo PHP_EOL . '=========================================' . PHP_EOL;
        echo 'HASIL VERIFIKASI AKHIR SISTEM RUANGBACA' . PHP_EOL;
        echo '=========================================' . PHP_EOL;
        echo 'Total Pengujian : ' . ($this->passedCount + $this->failedCount) . PHP_EOL;
        echo 'Berhasil (PASS) : ' . $this->passedCount . PHP_EOL;
        echo 'Gagal (FAIL)    : ' . $this->failedCount . PHP_EOL;
        echo '=========================================' . PHP_EOL;

        if ($this->failedCount > 0) {
            echo PHP_EOL . 'Daftar Kegagalan:' . PHP_EOL;
            foreach ($this->failures as $f) {
                echo ' - ' . $f['name'] . ': ' . $f['reason'] . PHP_EOL;
            }
        } else {
            echo 'Seluruh komponen dan alur terverifikasi 100% prima dan siap digunakan.' . PHP_EOL;
        }
    }
}

$suite = new SystemVerification();
exit($suite->run());
