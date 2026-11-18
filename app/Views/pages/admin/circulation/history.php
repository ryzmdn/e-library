<?php
declare(strict_types=1);

$loans = $loans ?? [];
$totalLoans = $totalLoans ?? 0;
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$status = $status ?? '';
$keyword = $keyword ?? '';

$queryParams = [];
if ($status !== '') {
    $queryParams['status'] = $status;
}
if ($keyword !== '') {
    $queryParams['q'] = $keyword;
}
?>
<section style="margin-bottom:2rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border);">
        <div>
            <h1 style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-bottom:0.25rem;">
                Log Transaksi Sirkulasi Lengkap
            </h1>
            <p style="font-size:0.875rem; color:var(--muted-foreground);">
                Seluruh rekam jejak peminjaman dan pengembalian buku fisik di perpustakaan.
            </p>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <a href="/admin/circulation" class="btn btn-outline btn-sm">Kembali ke Meja Sirkulasi</a>
        </div>
    </div>
</section>

<section style="margin-bottom:1.5rem;">
    <form method="GET" action="/admin/circulation/history" style="display:flex; gap:0.75rem; flex-wrap:wrap; background-color:var(--card); border:1px solid var(--border); border-radius:var(--radius-md); padding:1rem;">
        <input
            type="text"
            name="q"
            class="input"
            placeholder="Cari kode transaksi, nomor anggota, nama, barcode, atau judul..."
            value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"
            style="flex:1; min-width:240px;"
        >

        <select name="status" class="input" style="min-width:180px;">
            <option value="">Semua Status Transaksi</option>
            <option value="ACTIVE" <?= $status === 'ACTIVE' ? 'selected' : '' ?>>Aktif Dipinjam</option>
            <option value="OVERDUE" <?= $status === 'OVERDUE' ? 'selected' : '' ?>>Lewat Jatuh Tempo</option>
            <option value="RETURNED" <?= $status === 'RETURNED' ? 'selected' : '' ?>>Sudah Dikembalikan</option>
            <option value="LOST_REPORTED" <?= $status === 'LOST_REPORTED' ? 'selected' : '' ?>>Dilaporkan Hilang</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="/admin/circulation/history" class="btn btn-ghost">Reset</a>
    </form>
</section>

<section>
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 class="card-title" style="font-size:1.15rem;">Daftar Rekam Jejak Sirkulasi</h2>
                <p class="card-description">Menampilkan <?= count($loans) ?> dari total <?= $totalLoans ?> transaksi.</p>
            </div>
            <span class="badge"><?= $totalLoans ?> Total Transaksi</span>
        </div>

        <div class="card-content" style="padding:0; overflow-x:auto;">
            <?php if (empty($loans)): ?>
                <div style="padding:3rem 1.5rem; text-align:center; color:var(--muted-foreground); font-size:0.875rem;">
                    Tidak ada transaksi sirkulasi yang cocok dengan kriteria pencarian Anda.
                </div>
            <?php else: ?>
                <table class="table" style="width:100%; border-collapse:collapse; font-size:0.8125rem;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border); background-color:var(--muted); text-align:left;">
                            <th style="padding:0.75rem 1rem;">Kode Transaksi</th>
                            <th style="padding:0.75rem 1rem;">Anggota</th>
                            <th style="padding:0.75rem 1rem;">Judul Buku & Barcode</th>
                            <th style="padding:0.75rem 1rem;">Pinjam</th>
                            <th style="padding:0.75rem 1rem;">Jatuh Tempo</th>
                            <th style="padding:0.75rem 1rem;">Kembali</th>
                            <th style="padding:0.75rem 1rem;">Status</th>
                            <th style="padding:0.75rem 1rem; text-align:right;">Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $row): ?>
                            <?php $isOverdue = ((string)$row['loan_status'] === 'ACTIVE' && (string)$row['due_date'] < date('Y-m-d')); ?>
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:0.75rem 1rem; font-family:monospace; font-size:0.75rem;">
                                    <?= htmlspecialchars((string)$row['transaction_code'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <div style="font-weight:600;"><?= htmlspecialchars((string)$row['member_name'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <div style="font-size:0.75rem; color:var(--muted-foreground);"><?= htmlspecialchars((string)$row['member_number'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <div style="font-weight:500;"><?= htmlspecialchars((string)$row['title'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <div style="font-family:monospace; font-size:0.75rem; color:var(--muted-foreground);"><?= htmlspecialchars((string)$row['inventory_barcode'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?= date('d M Y', strtotime((string)$row['borrowed_at'])) ?>
                                </td>
                                <td style="padding:0.75rem 1rem; font-weight:600; color:<?= $isOverdue ? 'var(--destructive)' : 'inherit' ?>;">
                                    <?= date('d M Y', strtotime((string)$row['due_date'])) ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?= !empty($row['returned_at']) ? date('d M Y', strtotime((string)$row['returned_at'])) : '&mdash;' ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?php if ($row['loan_status'] === 'RETURNED'): ?>
                                        <span class="badge badge-outline">Dikembalikan</span>
                                    <?php elseif ($isOverdue): ?>
                                        <span class="badge badge-borrowed">Terlambat</span>
                                    <?php else: ?>
                                        <span class="badge badge-available">Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:0.75rem 1rem; text-align:right;">
                                    <?php $fine = (float)($row['accrued_fine'] ?? 0.0); ?>
                                    <?php if ($fine > 0.0): ?>
                                        <span style="color:var(--destructive); font-weight:600;">Rp <?= number_format($fine, 2, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span style="color:var(--muted-foreground);">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $baseUrl = '/admin/circulation/history';
    require dirname(__DIR__, 2) . '/components/pagination.php';
    ?>
</section>
