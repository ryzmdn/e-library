<?php
declare(strict_types=1);

$activeLoans = $activeLoans ?? [];
$recentHistory = $recentHistory ?? [];
$overdueCount = $overdueCount ?? 0;
?>
<section style="margin-bottom:2rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border);">
        <div>
            <h1 style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-bottom:0.25rem;">
                Peminjaman Buku Fisik
            </h1>
            <p style="font-size:0.875rem; color:var(--muted-foreground);">
                Pantau eksemplar buku yang sedang Anda bawa, tanggal jatuh tempo pengembalian, dan lakukan perpanjangan mandiri.
            </p>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <a href="/catalog" class="btn btn-primary btn-sm">Cari Koleksi Buku</a>
            <a href="/member/reservations" class="btn btn-outline btn-sm">Antrean Reservasi</a>
        </div>
    </div>
</section>

<?php if ($overdueCount > 0): ?>
    <div style="margin-bottom:1.5rem; padding:1rem 1.25rem; background-color:var(--muted); border-left:4px solid var(--destructive); border-radius:var(--radius-md); font-size:0.875rem;">
        <strong style="color:var(--destructive);">Perhatian:</strong>
        Anda memiliki <strong><?= $overdueCount ?></strong> buku yang telah melewati batas tanggal jatuh tempo. Harap segera kembalikan buku ke meja sirkulasi untuk menghindari penambahan denda harian.
    </div>
<?php endif; ?>

<section style="margin-bottom:3rem;">
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 class="card-title" style="font-size:1.15rem;">Buku Sedang Dipinjam</h2>
                <p class="card-description">Daftar buku fisik yang saat ini aktif dalam masa peminjaman Anda.</p>
            </div>
            <span class="badge"><?= count($activeLoans) ?> Buku Aktif</span>
        </div>

        <div class="card-content" style="padding:0; overflow-x:auto;">
            <?php if (empty($activeLoans)): ?>
                <div style="padding:3rem 1.5rem; text-align:center; color:var(--muted-foreground); font-size:0.875rem;">
                    Anda sedang tidak meminjam buku fisik apapun saat ini.
                </div>
            <?php else: ?>
                <table class="table" style="width:100%; border-collapse:collapse; font-size:0.8125rem;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border); background-color:var(--muted); text-align:left;">
                            <th style="padding:0.75rem 1rem;">Judul & No. Panggil</th>
                            <th style="padding:0.75rem 1rem;">No. Barcode</th>
                            <th style="padding:0.75rem 1rem;">Tanggal Pinjam</th>
                            <th style="padding:0.75rem 1rem;">Jatuh Tempo</th>
                            <th style="padding:0.75rem 1rem;">Status</th>
                            <th style="padding:0.75rem 1rem;">Perpanjangan</th>
                            <th style="padding:0.75rem 1rem; text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeLoans as $loan): ?>
                            <?php
                            $isOverdue = (string)$loan['due_date'] < date('Y-m-d');
                            $renewalsUsed = (int)($loan['renewal_count'] ?? 0);
                            $maxRenewals = (int)($loan['max_renewals'] ?? 1);
                            $canRenew = !$isOverdue && ($renewalsUsed < $maxRenewals);
                            ?>
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:0.75rem 1rem;">
                                    <div style="font-weight:600; font-size:0.875rem; margin-bottom:0.15rem;">
                                        <a href="/catalog/<?= urlencode((string)$loan['slug']) ?>" style="color:inherit; text-decoration:none;">
                                            <?= htmlspecialchars((string)$loan['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </div>
                                    <div style="font-size:0.75rem; color:var(--muted-foreground);">
                                        <?= htmlspecialchars((string)$loan['call_number_base'], ENT_QUOTES, 'UTF-8') ?> &bull; <?= htmlspecialchars((string)$loan['authors_text'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </td>
                                <td style="padding:0.75rem 1rem; font-family:monospace; font-weight:600;">
                                    <?= htmlspecialchars((string)$loan['inventory_barcode'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?= date('d M Y', strtotime((string)$loan['borrowed_at'])) ?>
                                </td>
                                <td style="padding:0.75rem 1rem; font-weight:600; color:<?= $isOverdue ? 'var(--destructive)' : 'inherit' ?>;">
                                    <?= date('d M Y', strtotime((string)$loan['due_date'])) ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?php if ($isOverdue): ?>
                                        <span class="badge badge-borrowed">Lewat Jatuh Tempo</span>
                                    <?php else: ?>
                                        <span class="badge badge-available">Aktif Dipinjam</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?= $renewalsUsed ?> / <?= $maxRenewals ?> Kali
                                </td>
                                <td style="padding:0.75rem 1rem; text-align:right;">
                                    <?php if ($canRenew): ?>
                                        <form method="POST" action="/member/loans/<?= (int)$loan['id'] ?>/renew" style="display:inline;">
                                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(\App\Core\Session::getCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="btn btn-outline btn-sm">
                                                Perpanjang
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="font-size:0.75rem; color:var(--muted-foreground);">Tidak dapat diperpanjang</span>
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

<section>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title" style="font-size:1.15rem;">Riwayat Peminjaman Terdahulu</h2>
            <p class="card-description">Catatan sirkulasi buku yang telah Anda kembalikan ke perpustakaan.</p>
        </div>
        <div class="card-content" style="padding:0; overflow-x:auto;">
            <?php if (empty($recentHistory)): ?>
                <div style="padding:2.5rem 1.5rem; text-align:center; color:var(--muted-foreground); font-size:0.875rem;">
                    Belum ada riwayat transaksi peminjaman selesai yang tercatat.
                </div>
            <?php else: ?>
                <table class="table" style="width:100%; border-collapse:collapse; font-size:0.8125rem;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border); background-color:var(--muted); text-align:left;">
                            <th style="padding:0.75rem 1rem;">Kode Transaksi</th>
                            <th style="padding:0.75rem 1rem;">Judul Buku</th>
                            <th style="padding:0.75rem 1rem;">Barcode</th>
                            <th style="padding:0.75rem 1rem;">Tanggal Pinjam</th>
                            <th style="padding:0.75rem 1rem;">Tanggal Kembali</th>
                            <th style="padding:0.75rem 1rem;">Kondisi Kembali</th>
                            <th style="padding:0.75rem 1rem; text-align:right;">Denda Terhitung</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentHistory as $item): ?>
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:0.75rem 1rem; font-family:monospace; font-size:0.75rem;">
                                    <?= htmlspecialchars((string)$item['transaction_code'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding:0.75rem 1rem; font-weight:500;">
                                    <?= htmlspecialchars((string)$item['title'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding:0.75rem 1rem; font-family:monospace;">
                                    <?= htmlspecialchars((string)$item['inventory_barcode'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?= date('d M Y', strtotime((string)$item['borrowed_at'])) ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?= !empty($item['returned_at']) ? date('d M Y', strtotime((string)$item['returned_at'])) : '-' ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <span class="badge badge-outline" style="font-size:0.7rem;">
                                        <?= htmlspecialchars((string)($item['return_condition'] ?? 'GOOD'), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td style="padding:0.75rem 1rem; text-align:right;">
                                    <?php $fine = (float)($item['accrued_fine'] ?? 0.0); ?>
                                    <?php if ($fine > 0.0): ?>
                                        <span style="color:var(--destructive); font-weight:600;">Rp <?= number_format($fine, 2, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span style="color:var(--muted-foreground);">Rp 0,00</span>
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
