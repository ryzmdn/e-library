<?php
declare(strict_types=1);

$todayCheckouts = $todayCheckouts ?? 0;
$todayCheckins = $todayCheckins ?? 0;
$overdueLoans = $overdueLoans ?? [];
$recentLoans = $recentLoans ?? [];
?>
<section style="margin-bottom:2rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border);">
        <div>
            <h1 style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-bottom:0.25rem;">
                Meja Layanan Sirkulasi Fisik
            </h1>
            <p style="font-size:0.875rem; color:var(--muted-foreground);">
                Pusat transaksi peminjaman kilat, penerimaan pengembalian buku, dan verifikasi ketersediaan eksemplar.
            </p>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <a href="/admin/circulation/history" class="btn btn-outline btn-sm">Log Seluruh Sirkulasi</a>
            <a href="/admin/fines" class="btn btn-outline btn-sm">Buku Besar Denda</a>
        </div>
    </div>
</section>

<section style="margin-bottom:2rem;">
    <div class="grid grid-cols-3">
        <div class="card">
            <div class="card-header" style="padding-bottom:0.25rem;">
                <span class="card-description">Peminjaman Hari Ini</span>
                <div style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-top:0.25rem;">
                    <?= $todayCheckouts ?> <span style="font-size:0.875rem; font-weight:400; color:var(--muted-foreground);">Buku</span>
                </div>
            </div>
            <div class="card-content" style="padding-top:0; font-size:0.8125rem; color:var(--muted-foreground);">
                Buku berhasil dikeluarkan ke anggota
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="padding-bottom:0.25rem;">
                <span class="card-description">Pengembalian Hari Ini</span>
                <div style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-top:0.25rem;">
                    <?= $todayCheckins ?> <span style="font-size:0.875rem; font-weight:400; color:var(--muted-foreground);">Buku</span>
                </div>
            </div>
            <div class="card-content" style="padding-top:0; font-size:0.8125rem; color:var(--muted-foreground);">
                Buku diterima kembali di meja sirkulasi
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="padding-bottom:0.25rem;">
                <span class="card-description">Pinjaman Lewat Jatuh Tempo</span>
                <div style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-top:0.25rem; color:<?= count($overdueLoans) > 0 ? 'var(--destructive)' : 'inherit' ?>;">
                    <?= count($overdueLoans) ?> <span style="font-size:0.875rem; font-weight:400; color:var(--muted-foreground);">Buku</span>
                </div>
            </div>
            <div class="card-content" style="padding-top:0; font-size:0.8125rem; color:var(--muted-foreground);">
                <?= count($overdueLoans) > 0 ? 'Perlu tindakan penagihan atau kontak anggota' : 'Tidak ada keterlambatan aktif saat ini' ?>
            </div>
        </div>
    </div>
</section>

<section style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; align-items:start; margin-bottom:2.5rem;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title" style="font-size:1.15rem;">Peminjaman Cepat (Checkout)</h2>
            <p class="card-description">Keluarkan eksemplar buku kepada anggota perpustakaan yang terdaftar.</p>
        </div>
        <form method="POST" action="/admin/circulation/checkout" class="card-content" style="display:flex; flex-direction:column; gap:1.25rem;">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(\App\Core\Session::getCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="member_identifier" class="label">Nomor Anggota atau Alamat Email</label>
                <input
                    type="text"
                    id="member_identifier"
                    name="member_identifier"
                    class="input"
                    placeholder="Contoh: M-2024-0001 atau nama@email.com"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="copy_barcode_out" class="label">Nomor Barcode Eksemplar Buku</label>
                <input
                    type="text"
                    id="copy_barcode_out"
                    name="copy_barcode"
                    class="input"
                    placeholder="Pindai barcode atau ketik BC-XXXX-XXXXX"
                    required
                >
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    Proses Peminjaman
                </button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title" style="font-size:1.15rem;">Pengembalian Cepat (Check-in)</h2>
            <p class="card-description">Terima buku dari peminjam, periksa kondisi fisik, dan hitung denda.</p>
        </div>
        <form method="POST" action="/admin/circulation/checkin" class="card-content" style="display:flex; flex-direction:column; gap:1.25rem;">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(\App\Core\Session::getCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="copy_barcode_in" class="label">Nomor Barcode Eksemplar Buku</label>
                <input
                    type="text"
                    id="copy_barcode_in"
                    name="copy_barcode"
                    class="input"
                    placeholder="Pindai barcode buku yang dikembalikan"
                    required
                >
            </div>

            <div class="form-group">
                <label for="condition" class="label">Kondisi Fisik Saat Kembali</label>
                <select id="condition" name="condition" class="input">
                    <option value="EXCELLENT">Sangat Baik (EXCELLENT)</option>
                    <option value="GOOD" selected>Baik / Wajar (GOOD)</option>
                    <option value="WORN">Aus Ringan (WORN)</option>
                    <option value="DAMAGED">Rusak / Sobek (DAMAGED)</option>
                    <option value="LOST">Hilang (LOST)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="notes" class="label">Catatan Tambahan (Opsional)</label>
                <input
                    type="text"
                    id="notes"
                    name="notes"
                    class="input"
                    placeholder="Contoh: Halaman belakang sedikit terlipat"
                >
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="btn btn-outline" style="width:100%;">
                    Proses Pengembalian
                </button>
            </div>
        </form>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 class="card-title" style="font-size:1.15rem;">Transaksi Sirkulasi Terbaru</h2>
                <p class="card-description">Aktivitas peminjaman dan pengembalian yang baru saja terjadi di sistem.</p>
            </div>
            <a href="/admin/circulation/history" class="btn btn-ghost btn-sm">Lihat Semua</a>
        </div>
        <div class="card-content" style="padding:0; overflow-x:auto;">
            <?php if (empty($recentLoans)): ?>
                <div style="padding:2.5rem 1.5rem; text-align:center; color:var(--muted-foreground); font-size:0.875rem;">
                    Belum ada transaksi sirkulasi yang tercatat hari ini.
                </div>
            <?php else: ?>
                <table class="table" style="width:100%; border-collapse:collapse; font-size:0.8125rem;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border); background-color:var(--muted); text-align:left;">
                            <th style="padding:0.75rem 1rem;">Kode Transaksi</th>
                            <th style="padding:0.75rem 1rem;">Nama Anggota</th>
                            <th style="padding:0.75rem 1rem;">Judul Buku & Barcode</th>
                            <th style="padding:0.75rem 1rem;">Tanggal Pinjam</th>
                            <th style="padding:0.75rem 1rem;">Jatuh Tempo</th>
                            <th style="padding:0.75rem 1rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLoans as $row): ?>
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
                                    <?php if ($row['loan_status'] === 'RETURNED'): ?>
                                        <span class="badge badge-outline">Selesai (Kembali)</span>
                                    <?php elseif ($isOverdue): ?>
                                        <span class="badge badge-borrowed">Terlambat</span>
                                    <?php else: ?>
                                        <span class="badge badge-available">Aktif Dipinjam</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>
