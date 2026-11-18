<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 style="font-size:1.5rem; font-weight:700; margin:0 0 0.25rem 0;">Tugas Terjadwal (Cron & CLI Tasks)</h1>
        <p style="color:var(--muted-foreground); margin:0; font-size:0.875rem;">Otomasi latar belakang untuk kalkulasi denda harian dan siklus antrean reservasi buku.</p>
    </div>
    <div>
        <form method="POST" action="/admin/tasks/run-all" onsubmit="return confirm('Jalankan seluruh tugas terjadwal sekarang?');" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-primary" style="font-size:0.875rem;">Jalankan Seluruh Tugas (Run All)</button>
        </form>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:1.5rem; margin-bottom:2rem;">
    <div class="card" style="padding:1.5rem; display:flex; flex-direction:column; justify-content:space-between;">
        <div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                <h2 style="font-size:1.125rem; font-weight:600; margin:0;">Kalkulasi Denda Keterlambatan</h2>
                <span style="font-size:0.6875rem; font-weight:700; padding:0.2rem 0.5rem; border-radius:9999px; background-color:#dbeafe; color:#1e40af;">HARIAN</span>
            </div>
            <p style="font-size:0.8125rem; color:var(--muted-foreground); line-height:1.5; margin:0 0 1rem 0;">
                Memindai seluruh peminjaman yang melampaui tanggal jatuh tempo, mengecualikan hari libur nasional dan hari Minggu perpustakaan, lalu membukukan selisih denda harian ke buku besar keuangan.
            </p>

            <div style="background-color:#f8fafc; border:1px solid var(--border); border-radius:var(--radius); padding:0.75rem; margin-bottom:1rem; font-size:0.8125rem;">
                <div style="display:flex; justify-content:space-between; margin-bottom:0.35rem;">
                    <span style="color:var(--muted-foreground);">Pinjaman Terlambat Saat Ini:</span>
                    <strong style="color:<?= $overdueCount > 0 ? '#b91c1c' : 'inherit' ?>;"><?= number_format($overdueCount, 0, ',', '.') ?> transaksi</strong>
                </div>
                <div style="font-size:0.75rem; color:var(--muted-foreground);">
                    Perintah CLI: <code style="font-family:monospace; color:var(--primary); font-weight:600;">php cli/calculate_fines.php</code>
                </div>
            </div>
        </div>

        <form method="POST" action="/admin/tasks/fines" onsubmit="return confirm('Jalankan kalkulasi denda keterlambatan sekarang?');">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-outline" style="width:100%; font-size:0.8125rem;">Jalankan Kalkulasi Denda</button>
        </form>
    </div>

    <div class="card" style="padding:1.5rem; display:flex; flex-direction:column; justify-content:space-between;">
        <div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                <h2 style="font-size:1.125rem; font-weight:600; margin:0;">Kedaluwarsa Reservasi Buku</h2>
                <span style="font-size:0.6875rem; font-weight:700; padding:0.2rem 0.5rem; border-radius:9999px; background-color:#fef3c7; color:#92400e;">PER JAM / HARIAN</span>
            </div>
            <p style="font-size:0.8125rem; color:var(--muted-foreground); line-height:1.5; margin:0 0 1rem 0;">
                Memindai antrean reservasi yang melewati batas waktu pengambilan (pickup deadline), membatalkan tiket antrean, mengalihkan eksemplar ke pemohon berikutnya (FIFO), atau mengembalikan buku ke rak jika antrean kosong.
            </p>

            <div style="background-color:#f8fafc; border:1px solid var(--border); border-radius:var(--radius); padding:0.75rem; margin-bottom:1rem; font-size:0.8125rem;">
                <div style="display:flex; justify-content:space-between; margin-bottom:0.35rem;">
                    <span style="color:var(--muted-foreground);">Reservasi Kedaluwarsa Saat Ini:</span>
                    <strong style="color:<?= $expiredReservationCount > 0 ? '#b91c1c' : 'inherit' ?>;"><?= number_format($expiredReservationCount, 0, ',', '.') ?> antrean</strong>
                </div>
                <div style="font-size:0.75rem; color:var(--muted-foreground);">
                    Perintah CLI: <code style="font-family:monospace; color:var(--primary); font-weight:600;">php cli/expire_reservations.php</code>
                </div>
            </div>
        </div>

        <form method="POST" action="/admin/tasks/reservations" onsubmit="return confirm('Proses kedaluwarsa reservasi sekarang?');">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-outline" style="width:100%; font-size:0.8125rem;">Proses Kedaluwarsa Reservasi</button>
        </form>
    </div>
</div>

<div class="card" style="padding:1.5rem; margin-bottom:2rem;">
    <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 0.5rem 0;">Panduan Konfigurasi Penjadwalan Server (Cron / Task Scheduler)</h2>
    <p style="font-size:0.8125rem; color:var(--muted-foreground); margin:0 0 1rem 0;">Untuk menjalankan tugas secara otomatis tanpa intervensi manual, pasang baris perintah berikut pada crontab server atau Windows Task Scheduler:</p>

    <div style="background-color:#18181b; color:#fafafa; border-radius:var(--radius); padding:1rem; font-family:monospace; font-size:0.8125rem; line-height:1.6; overflow-x:auto;">
        <div>0 0 * * * php D:/xampp/htdocs/e-library/cli/calculate_fines.php >> D:/xampp/htdocs/e-library/storage/logs/fines.log 2>&1</div>
        <div>0 * * * * php D:/xampp/htdocs/e-library/cli/expire_reservations.php >> D:/xampp/htdocs/e-library/storage/logs/reservations.log 2>&1</div>
        <div>0 1 * * * php D:/xampp/htdocs/e-library/cli/scheduler.php --all >> D:/xampp/htdocs/e-library/storage/logs/cron.log 2>&1</div>
    </div>
</div>

<div class="card" style="padding:1.5rem;">
    <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0;">Riwayat Eksekusi Tugas Otomasi</h2>

    <?php if (empty($taskLogs)): ?>
        <div style="text-align:center; padding:2rem; color:var(--muted-foreground); font-size:0.875rem;">
            Belum ada riwayat tugas terjadwal yang tercatat pada log sistem.
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="table" style="width:100%; font-size:0.8125rem;">
                <thead>
                    <tr>
                        <th style="padding:0.5rem;">Waktu</th>
                        <th style="padding:0.5rem;">Eksekutor</th>
                        <th style="padding:0.5rem;">Peristiwa Aksi</th>
                        <th style="padding:0.5rem;">Target Entitas</th>
                        <th style="padding:0.5rem;">Rincian Muatan (Payload)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($taskLogs as $log): ?>
                        <tr>
                            <td style="padding:0.5rem; white-space:nowrap;"><?= htmlspecialchars($log['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td style="padding:0.5rem; white-space:nowrap;">
                                <strong style="color:var(--foreground);"><?= htmlspecialchars($log['actor_name'] ?? 'Sistem / CLI Cron', ENT_QUOTES, 'UTF-8') ?></strong>
                            </td>
                            <td style="padding:0.5rem;">
                                <span style="font-family:monospace; font-weight:600; color:var(--primary);">
                                    <?= htmlspecialchars($log['action_event'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td style="padding:0.5rem; white-space:nowrap; font-family:monospace;">
                                <?= htmlspecialchars(($log['target_entity'] ?? '') . ' #' . ($log['target_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td style="padding:0.5rem; font-family:monospace; font-size:0.75rem; color:var(--muted-foreground); max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                <?= htmlspecialchars($log['payload_diff_json'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
