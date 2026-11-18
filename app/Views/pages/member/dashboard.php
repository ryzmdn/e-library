<?php
declare(strict_types=1);

$member = $member ?? [];
$activeLoans = $activeLoans ?? [];
$activeLoansCount = $activeLoansCount ?? 0;
$overdueCount = $overdueCount ?? 0;
$unpaidFine = (float)($unpaidFine ?? 0.00);
$activeReservations = $activeReservations ?? [];
$activeReservationsCount = $activeReservationsCount ?? 0;
$recentHistory = $recentHistory ?? [];
?>
<section style="margin-bottom:2rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border);">
        <div>
            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.25rem;">
                <span class="badge badge-outline"><?= htmlspecialchars((string)($member['member_number'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></span>
                <span class="badge"><?= htmlspecialchars((string)($member['tier_name'] ?? 'Mahasiswa Reguler'), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <h1 style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em;">
                Selamat Datang, <?= htmlspecialchars((string)($member['full_name'] ?? 'Anggota'), ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p style="font-size:0.875rem; color:var(--muted-foreground);">
                Masa berlaku keanggotaan aktif hingga: <?= htmlspecialchars((string)($member['membership_expires_at'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <a href="/catalog" class="btn btn-primary btn-sm">Jelajahi Katalog</a>
            <a href="/member/profile" class="btn btn-outline btn-sm">Pengaturan Akun</a>
        </div>
    </div>
</section>

<section style="margin-bottom:2.5rem;">
    <div class="grid grid-cols-4">
        <div class="card">
            <div class="card-header" style="padding-bottom:0.25rem;">
                <span class="card-description">Pinjaman Aktif</span>
                <div style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-top:0.25rem;">
                    <?= $activeLoansCount ?> <span style="font-size:0.875rem; font-weight:400; color:var(--muted-foreground);">/ <?= (int)($member['max_physical_loans'] ?? 3) ?> Kuota</span>
                </div>
            </div>
            <div class="card-content" style="padding-top:0; font-size:0.8125rem; color:var(--muted-foreground);">
                Eksemplar fisik sedang dipinjam
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="padding-bottom:0.25rem;">
                <span class="card-description">Lewat Jatuh Tempo</span>
                <div style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-top:0.25rem; color:<?= $overdueCount > 0 ? 'var(--destructive)' : 'inherit' ?>;">
                    <?= $overdueCount ?> <span style="font-size:0.875rem; font-weight:400; color:var(--muted-foreground);">Buku</span>
                </div>
            </div>
            <div class="card-content" style="padding-top:0; font-size:0.8125rem; color:var(--muted-foreground);">
                <?= $overdueCount > 0 ? 'Segera kembalikan ke meja sirkulasi' : 'Tidak ada keterlambatan pengembalian' ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="padding-bottom:0.25rem;">
                <span class="card-description">Total Tagihan Denda</span>
                <div style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-top:0.25rem; color:<?= $unpaidFine > 0 ? 'var(--destructive)' : 'inherit' ?>;">
                    Rp <?= number_format($unpaidFine, 0, ',', '.') ?>
                </div>
            </div>
            <div class="card-content" style="padding-top:0; font-size:0.8125rem; color:var(--muted-foreground);">
                <?= $unpaidFine > 0 ? 'Dapat dilunasi di meja kasir sirkulasi' : 'Status keuangan bersih (Rp0)' ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="padding-bottom:0.25rem;">
                <span class="card-description">Antrean Reservasi</span>
                <div style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-top:0.25rem;">
                    <?= $activeReservationsCount ?> <span style="font-size:0.875rem; font-weight:400; color:var(--muted-foreground);">Judul</span>
                </div>
            </div>
            <div class="card-content" style="padding-top:0; font-size:0.8125rem; color:var(--muted-foreground);">
                Buku sedang menunggu ketersediaan
            </div>
        </div>
    </div>
</section>

<section style="margin-bottom:2.5rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <div>
            <h2 style="font-size:1.25rem; font-weight:600;">Daftar Peminjaman Aktif</h2>
            <p style="font-size:0.875rem; color:var(--muted-foreground);">Buku fisik yang saat ini berada dalam peminjaman Anda.</p>
        </div>
    </div>

    <?php if (!empty($activeLoans)): ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul Buku</th>
                        <th>Kode Barcode</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Posisi Rak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activeLoans as $loan): ?>
                        <?php
                        $isOverdue = ($loan['loan_status'] === 'OVERDUE') || (strtotime($loan['due_date']) < strtotime(date('Y-m-d')));
                        ?>
                        <tr>
                            <td>
                                <div style="font-weight:600;">
                                    <a href="/book/<?= urlencode((string)$loan['slug']) ?>" style="text-decoration:underline;">
                                        <?= htmlspecialchars((string)$loan['title'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </div>
                                <div style="font-size:0.75rem; color:var(--muted-foreground);"><?= htmlspecialchars((string)$loan['authors_text'], ENT_QUOTES, 'UTF-8') ?></div>
                            </td>
                            <td><span style="font-family:var(--font-mono); font-size:0.8125rem;"><?= htmlspecialchars((string)$loan['inventory_barcode'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td><?= htmlspecialchars(date('d M Y', strtotime((string)$loan['borrowed_at'])), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <span style="font-weight:<?= $isOverdue ? '700' : '500' ?>; color:<?= $isOverdue ? 'var(--destructive)' : 'inherit' ?>;">
                                    <?= htmlspecialchars(date('d M Y', strtotime((string)$loan['due_date'])), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($isOverdue): ?>
                                    <span class="badge badge-overdue">Terlambat</span>
                                <?php else: ?>
                                    <span class="badge badge-available">Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size:0.8125rem; color:var(--muted-foreground);">
                                    <?= htmlspecialchars((string)$loan['shelf_floor'], ENT_QUOTES, 'UTF-8') ?> &bull; <?= htmlspecialchars((string)$loan['shelf_aisle'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="card" style="text-align:center; padding:3rem 1.5rem;">
            <p style="font-weight:600; font-size:1rem; margin-bottom:0.25rem;">Tidak Ada Pinjaman Buku Aktif</p>
            <p style="color:var(--muted-foreground); font-size:0.875rem; margin-bottom:1.5rem;">
                Anda saat ini tidak memiliki tanggungan peminjaman buku fisik di perpustakaan.
            </p>
            <div>
                <a href="/catalog" class="btn btn-outline btn-sm">Mulai Telusuri Katalog OPAC</a>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php if (!empty($activeReservations)): ?>
<section style="margin-bottom:2.5rem;">
    <div style="margin-bottom:1rem;">
        <h2 style="font-size:1.25rem; font-weight:600;">Antrean Reservasi Aktif</h2>
        <p style="font-size:0.875rem; color:var(--muted-foreground);">Buku yang telah Anda pesan dan sedang menunggu giliran pengambilan.</p>
    </div>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Judul Buku</th>
                    <th>No. Panggil</th>
                    <th>Waktu Reservasi</th>
                    <th>Status Antrean</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activeReservations as $res): ?>
                    <tr>
                        <td style="font-weight:600;"><?= htmlspecialchars((string)$res['title'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span style="font-family:var(--font-mono); font-size:0.8125rem;"><?= htmlspecialchars((string)$res['call_number_base'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><?= htmlspecialchars(date('d M Y H:i', strtotime((string)$res['requested_at'])), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if ($res['queue_status'] === 'READY_FOR_PICKUP'): ?>
                                <span class="badge badge-available">Siap Diambil di Meja Layanan</span>
                            <?php else: ?>
                                <span class="badge badge-borrowed">Menunggu Antrean</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php endif; ?>
