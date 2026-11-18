<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 style="font-size:1.5rem; font-weight:700; margin:0 0 0.25rem 0;">Ringkasan Sistem Perpustakaan</h1>
        <p style="color:var(--muted-foreground); margin:0; font-size:0.875rem;">Metrik operasional, sirkulasi fisik, inventaris, dan jejak audit sistem.</p>
    </div>
    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
        <a href="/admin/circulation" class="btn btn-primary" style="font-size:0.875rem;">Meja Sirkulasi</a>
        <a href="/admin/catalog/create" class="btn btn-outline" style="font-size:0.875rem;">Tambah Buku</a>
        <a href="/admin/copies/create" class="btn btn-outline" style="font-size:0.875rem;">Tambah Eksemplar</a>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:2rem;">
    <div class="card" style="padding:1.25rem;">
        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--muted-foreground); margin-bottom:0.35rem;">Total Judul Buku</div>
        <div style="font-size:1.75rem; font-weight:700;"><?= number_format((int)($stats['totalTitles'] ?? 0), 0, ',', '.') ?></div>
        <div style="font-size:0.75rem; color:var(--muted-foreground); margin-top:0.25rem;">Koleksi terdaftar di OPAC</div>
    </div>

    <div class="card" style="padding:1.25rem;">
        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--muted-foreground); margin-bottom:0.35rem;">Eksemplar Fisik</div>
        <div style="font-size:1.75rem; font-weight:700;"><?= number_format((int)($stats['totalCopies'] ?? 0), 0, ',', '.') ?></div>
        <div style="font-size:0.75rem; color:var(--muted-foreground); margin-top:0.25rem;">Buku bertiket barcode di rak</div>
    </div>

    <div class="card" style="padding:1.25rem;">
        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--muted-foreground); margin-bottom:0.35rem;">Peminjaman Aktif</div>
        <div style="font-size:1.75rem; font-weight:700; color:var(--primary);"><?= number_format((int)($stats['activeLoans'] ?? 0), 0, ',', '.') ?></div>
        <div style="font-size:0.75rem; color:var(--muted-foreground); margin-top:0.25rem;">Sedang dibawa pembaca</div>
    </div>

    <div class="card" style="padding:1.25rem;">
        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--muted-foreground); margin-bottom:0.35rem;">Keterlambatan (Overdue)</div>
        <div style="font-size:1.75rem; font-weight:700; color:<?= ($stats['overdueLoans'] ?? 0) > 0 ? '#b91c1c' : 'inherit' ?>;"><?= number_format((int)($stats['overdueLoans'] ?? 0), 0, ',', '.') ?></div>
        <div style="font-size:0.75rem; color:var(--muted-foreground); margin-top:0.25rem;">Perlu penagihan atau denda</div>
    </div>

    <div class="card" style="padding:1.25rem;">
        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--muted-foreground); margin-bottom:0.35rem;">Checkout Hari Ini</div>
        <div style="font-size:1.75rem; font-weight:700;"><?= number_format((int)($stats['todayCheckouts'] ?? 0), 0, ',', '.') ?></div>
        <div style="font-size:0.75rem; color:var(--muted-foreground); margin-top:0.25rem;">Transaksi pinjam hari ini</div>
    </div>

    <div class="card" style="padding:1.25rem;">
        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--muted-foreground); margin-bottom:0.35rem;">Pengembalian Hari Ini</div>
        <div style="font-size:1.75rem; font-weight:700;"><?= number_format((int)($stats['todayCheckins'] ?? 0), 0, ',', '.') ?></div>
        <div style="font-size:0.75rem; color:var(--muted-foreground); margin-top:0.25rem;">Buku kembali ke meja sirkulasi</div>
    </div>

    <div class="card" style="padding:1.25rem;">
        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--muted-foreground); margin-bottom:0.35rem;">Anggota Aktif</div>
        <div style="font-size:1.75rem; font-weight:700;"><?= number_format((int)($stats['totalMembers'] ?? 0), 0, ',', '.') ?></div>
        <div style="font-size:0.75rem; color:var(--muted-foreground); margin-top:0.25rem;">Siswa / Sivitas terverifikasi</div>
    </div>

    <div class="card" style="padding:1.25rem;">
        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--muted-foreground); margin-bottom:0.35rem;">Tunggakan Denda</div>
        <div style="font-size:1.75rem; font-weight:700; color:<?= ($stats['unpaidUsers'] ?? 0) > 0 ? '#b91c1c' : 'inherit' ?>;"><?= number_format((int)($stats['unpaidUsers'] ?? 0), 0, ',', '.') ?></div>
        <div style="font-size:0.75rem; color:var(--muted-foreground); margin-top:0.25rem;">Anggota dengan saldo denda</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(450px, 1fr)); gap:1.5rem;">
    <div class="card" style="padding:1.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h2 style="font-size:1.125rem; font-weight:600; margin:0;">Aktivitas Peminjaman Terbaru</h2>
            <a href="/admin/circulation/history" style="font-size:0.8125rem; color:var(--primary); text-decoration:none; font-weight:500;">Lihat Semua &rarr;</a>
        </div>

        <?php if (empty($recentLoans)): ?>
            <div style="text-align:center; padding:2rem; color:var(--muted-foreground); font-size:0.875rem;">
                Belum ada transaksi peminjaman tercatat.
            </div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table class="table" style="width:100%; font-size:0.8125rem;">
                    <thead>
                        <tr>
                            <th style="padding:0.5rem;">Kode</th>
                            <th style="padding:0.5rem;">Peminjam</th>
                            <th style="padding:0.5rem;">Judul Buku</th>
                            <th style="padding:0.5rem;">Jatuh Tempo</th>
                            <th style="padding:0.5rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLoans as $l): ?>
                            <tr>
                                <td style="padding:0.5rem; font-family:monospace;"><?= htmlspecialchars($l['transaction_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="padding:0.5rem;">
                                    <div style="font-weight:500;"><?= htmlspecialchars($l['member_name'] ?? 'Anggota', ENT_QUOTES, 'UTF-8') ?></div>
                                    <div style="font-size:0.75rem; color:var(--muted-foreground);"><?= htmlspecialchars($l['member_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td style="padding:0.5rem; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    <?= htmlspecialchars($l['title'] ?? 'Judul Tidak Diketahui', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding:0.5rem; white-space:nowrap;">
                                    <?= htmlspecialchars($l['due_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding:0.5rem;">
                                    <?php
                                    $st = $l['loan_status'] ?? 'ACTIVE';
                                    $badgeStyle = 'background-color:#e2e8f0; color:#1e293b;';
                                    if ($st === 'ACTIVE') {
                                        $badgeStyle = 'background-color:#dbeafe; color:#1e40af;';
                                    } elseif ($st === 'RETURNED') {
                                        $badgeStyle = 'background-color:#dcfce7; color:#166534;';
                                    } elseif ($st === 'OVERDUE') {
                                        $badgeStyle = 'background-color:#fee2e2; color:#991b1b;';
                                    }
                                    ?>
                                    <span style="display:inline-block; padding:0.125rem 0.5rem; border-radius:9999px; font-size:0.6875rem; font-weight:600; <?= $badgeStyle ?>">
                                        <?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="card" style="padding:1.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h2 style="font-size:1.125rem; font-weight:600; margin:0;">Log Audit & Keamanan Sistem</h2>
            <span style="font-size:0.8125rem; color:var(--muted-foreground);">Aktivitas Terakhir</span>
        </div>

        <?php if (empty($recentLogs)): ?>
            <div style="text-align:center; padding:2rem; color:var(--muted-foreground); font-size:0.875rem;">
                Belum ada rekaman audit log.
            </div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                <?php foreach ($recentLogs as $log): ?>
                    <div style="border:1px solid var(--border); border-radius:var(--radius); padding:0.75rem; font-size:0.8125rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.25rem;">
                            <span style="font-weight:600; font-family:monospace;"><?= htmlspecialchars($log['action_event'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            <span style="font-size:0.75rem; color:var(--muted-foreground);"><?= htmlspecialchars($log['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div style="color:var(--muted-foreground);">
                            Oleh: <strong style="color:var(--foreground);"><?= htmlspecialchars($log['actor_name'] ?? 'Sistem/Cron', ENT_QUOTES, 'UTF-8') ?></strong> &bull;
                            Entitas: <?= htmlspecialchars(($log['target_entity'] ?? '') . ' #' . ($log['target_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
