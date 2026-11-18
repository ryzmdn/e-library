<?php
declare(strict_types=1);

$reservations = $reservations ?? [];
?>
<section style="margin-bottom:2rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border);">
        <div>
            <h1 style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-bottom:0.25rem;">
                Antrean Reservasi Buku
            </h1>
            <p style="font-size:0.875rem; color:var(--muted-foreground);">
                Kelola daftar tunggu buku fisik yang sedang dipinjam oleh anggota lain dan pantau ketersediaan pengambilan di meja sirkulasi.
            </p>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <a href="/catalog" class="btn btn-primary btn-sm">Jelajahi Katalog</a>
            <a href="/member/loans" class="btn btn-outline btn-sm">Pinjaman Aktif</a>
        </div>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 class="card-title" style="font-size:1.15rem;">Daftar Antrean Anda</h2>
                <p class="card-description">Urutan antrean dihitung secara adil berdasarkan waktu permintaan (FIFO).</p>
            </div>
            <span class="badge"><?= count($reservations) ?> Antrean Aktif</span>
        </div>

        <div class="card-content" style="padding:0; overflow-x:auto;">
            <?php if (empty($reservations)): ?>
                <div style="padding:3.5rem 1.5rem; text-align:center; color:var(--muted-foreground); font-size:0.875rem;">
                    Anda saat ini tidak memiliki antrean reservasi buku. Jika buku yang ingin Anda pinjam sedang habis di rak, Anda dapat menekan tombol "Antre Reservasi" di halaman detail buku.
                </div>
            <?php else: ?>
                <table class="table" style="width:100%; border-collapse:collapse; font-size:0.8125rem;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border); background-color:var(--muted); text-align:left;">
                            <th style="padding:0.75rem 1rem;">Judul Buku & No. Panggil</th>
                            <th style="padding:0.75rem 1rem;">Waktu Permintaan</th>
                            <th style="padding:0.75rem 1rem;">Status Antrean</th>
                            <th style="padding:0.75rem 1rem;">Eksemplar & Lokasi Ambil</th>
                            <th style="padding:0.75rem 1rem;">Batas Waktu Pengambilan</th>
                            <th style="padding:0.75rem 1rem; text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $res): ?>
                            <?php $isReady = ((string)$res['queue_status'] === 'READY_FOR_PICKUP'); ?>
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:0.75rem 1rem;">
                                    <div style="font-weight:600; font-size:0.875rem; margin-bottom:0.15rem;">
                                        <a href="/catalog/<?= urlencode((string)$res['slug']) ?>" style="color:inherit; text-decoration:none;">
                                            <?= htmlspecialchars((string)$res['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </div>
                                    <div style="font-size:0.75rem; color:var(--muted-foreground);">
                                        <?= htmlspecialchars((string)$res['call_number_base'], ENT_QUOTES, 'UTF-8') ?> &bull; <?= htmlspecialchars((string)$res['authors_text'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?= date('d M Y, H:i', strtotime((string)$res['requested_at'])) ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?php if ($isReady): ?>
                                        <span class="badge badge-available">Siap Diambil di Meja</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Menunggu Pengembalian</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?php if ($isReady && !empty($res['inventory_barcode'])): ?>
                                        <div style="font-family:monospace; font-weight:600;"><?= htmlspecialchars((string)$res['inventory_barcode'], ENT_QUOTES, 'UTF-8') ?></div>
                                        <div style="font-size:0.75rem; color:var(--muted-foreground);"><?= htmlspecialchars((string)$res['shelf_building'], ENT_QUOTES, 'UTF-8') ?>, Meja Sirkulasi</div>
                                    <?php else: ?>
                                        <span style="color:var(--muted-foreground);">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?php if ($isReady && !empty($res['pickup_expires_at'])): ?>
                                        <strong style="color:var(--destructive);"><?= date('d M Y, H:i', strtotime((string)$res['pickup_expires_at'])) ?></strong>
                                    <?php else: ?>
                                        <span style="color:var(--muted-foreground);">Menunggu ketersediaan</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:0.75rem 1rem; text-align:right;">
                                    <form method="POST" action="/member/reservations/<?= (int)$res['id'] ?>/cancel" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan antrean buku ini?');">
                                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(\App\Core\Session::getCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" class="btn btn-outline btn-sm">
                                            Batalkan Antrean
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>
