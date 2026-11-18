<?php
declare(strict_types=1);

$item = $item ?? [];
$copies = $copies ?? [];
$digitalAsset = $digitalAsset ?? null;
$canAccessDigital = $canAccessDigital ?? false;
$relatedItems = $relatedItems ?? [];
$user = $user ?? null;

$title = (string)($item['title'] ?? 'Detail Bahan Pustaka');
$subtitle = (string)($item['subtitle'] ?? '');
$authors = (string)($item['authors_text'] ?? 'Penulis Tidak Diketahui');
$publisher = (string)($item['publisher_name'] ?? '-');
$year = (string)($item['publication_year'] ?? '-');
$edition = (string)($item['edition_statement'] ?? '-');
$language = (string)($item['language_code'] ?? 'id-ID');
$pages = (int)($item['physical_pages'] ?? 0);
$isbn = (string)($item['isbn_13'] ?? '-');
$ddc = (string)($item['ddc_code'] ?? '-');
$callNumber = (string)($item['call_number_base'] ?? '-');
$synopsis = (string)($item['synopsis'] ?? 'Sinopsis atau anotasi belum tersedia untuk koleksi ini.');
$tags = array_filter(array_map('trim', explode(',', (string)($item['subject_tags'] ?? ''))));
$mediaType = (string)($item['media_type'] ?? 'PHYSICAL_ONLY');
$availableCopies = (int)($item['available_physical_copies'] ?? 0);
$totalCopies = (int)($item['total_physical_copies'] ?? 0);
$ratingAvg = (float)($item['rating_avg'] ?? 0.0);
$ratingCount = (int)($item['rating_count'] ?? 0);
?>
<section style="margin-bottom:1.5rem;">
    <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.8125rem; color:var(--muted-foreground); margin-bottom:1rem;">
        <a href="/" style="color:inherit; text-decoration:none;">Beranda</a>
        <span>/</span>
        <a href="/catalog" style="color:inherit; text-decoration:none;">Katalog OPAC</a>
        <span>/</span>
        <span style="color:var(--foreground); font-weight:500;"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
</section>

<section style="display:grid; grid-template-columns:300px 1fr; gap:2rem; align-items:start; margin-bottom:3rem;">
    <div>
        <div class="card" style="overflow:hidden; text-align:center;">
            <div style="background-color:var(--muted); height:360px; display:flex; flex-direction:column; justify-content:center; align-items:center; padding:1.5rem; border-bottom:1px solid var(--border);">
                <div style="font-size:0.875rem; font-weight:700; color:var(--muted-foreground); letter-spacing:0.05em; text-transform:uppercase; margin-bottom:0.75rem;">
                    <?= htmlspecialchars($callNumber, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div style="font-size:1.15rem; font-weight:700; line-height:1.3; color:var(--foreground); max-width:220px;">
                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div style="font-size:0.8125rem; color:var(--muted-foreground); margin-top:0.5rem;">
                    <?= htmlspecialchars($authors, ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>

            <div class="card-content" style="display:flex; flex-direction:column; gap:0.75rem; padding:1.25rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.8125rem; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">
                    <span style="color:var(--muted-foreground);">Format Media</span>
                    <span class="badge badge-outline"><?= htmlspecialchars($mediaType, ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.8125rem; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">
                    <span style="color:var(--muted-foreground);">Koleksi Fisik</span>
                    <span>
                        <?php if ($availableCopies > 0): ?>
                            <strong style="color:var(--primary);"><?= $availableCopies ?></strong> / <?= $totalCopies ?> Tersedia
                        <?php else: ?>
                            <strong style="color:var(--destructive);">0</strong> / <?= $totalCopies ?> Tersedia
                        <?php endif; ?>
                    </span>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.8125rem;">
                    <span style="color:var(--muted-foreground);">Evaluasi Pembaca</span>
                    <span><strong><?= number_format($ratingAvg, 2) ?></strong> (<?= $ratingCount ?> ulasan)</span>
                </div>
            </div>

            <div class="card-footer" style="display:flex; flex-direction:column; gap:0.5rem; padding-top:0;">
                <?php if ($digitalAsset !== null): ?>
                    <?php if ($canAccessDigital): ?>
                        <a href="/reader/<?= (int)$item['id'] ?>" class="btn btn-primary" style="width:100%; text-align:center;">
                            Baca E-Book Digital
                        </a>
                    <?php elseif ($user === null): ?>
                        <a href="/login" class="btn btn-outline" style="width:100%; text-align:center;">
                            Masuk untuk Membaca Digital
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline" disabled style="width:100%; opacity:0.6; cursor:not-allowed;">
                            Akses Digital Terbatas Tingkatan
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($totalCopies > 0 && $availableCopies === 0): ?>
                    <?php if ($user !== null): ?>
                        <form method="POST" action="/member/reservations" style="width:100%;">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(\App\Core\Session::getCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="biblio_id" value="<?= (int)$item['id'] ?>">
                            <button type="submit" class="btn btn-outline" style="width:100%;">
                                Antre Reservasi Peminjaman
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="/login" class="btn btn-outline" style="width:100%; text-align:center;">
                            Masuk untuk Reservasi
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

                <a href="/catalog" class="btn btn-ghost btn-sm" style="width:100%; text-align:center;">
                    Kembali ke Penelusuran
                </a>
            </div>
        </div>
    </div>

    <div>
        <div style="margin-bottom:1.75rem;">
            <div style="display:flex; gap:0.5rem; margin-bottom:0.5rem; flex-wrap:wrap;">
                <span class="badge"><?= htmlspecialchars($year, ENT_QUOTES, 'UTF-8') ?></span>
                <span class="badge badge-outline">DDC <?= htmlspecialchars($ddc, ENT_QUOTES, 'UTF-8') ?></span>
                <?php if ($availableCopies > 0): ?>
                    <span class="badge badge-available">Tersedia untuk Sirkulasi</span>
                <?php elseif ($mediaType === 'DIGITAL_ONLY'): ?>
                    <span class="badge badge-available">Akses Digital Tersedia</span>
                <?php else: ?>
                    <span class="badge badge-borrowed">Semua Eksemplar Sedang Dipinjam</span>
                <?php endif; ?>
            </div>

            <h1 style="font-size:2rem; font-weight:700; letter-spacing:-0.03em; margin-bottom:0.35rem; line-height:1.2;">
                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <?php if ($subtitle !== ''): ?>
                <div style="font-size:1.125rem; color:var(--muted-foreground); margin-bottom:0.5rem;">
                    <?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <div style="font-size:0.9375rem; color:var(--foreground); font-weight:500;">
                Penulis: <?= htmlspecialchars($authors, ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>

        <div class="card" style="margin-bottom:2rem;">
            <div class="card-header">
                <h2 class="card-title" style="font-size:1.1rem;">Deskripsi & Sinopsis</h2>
            </div>
            <div class="card-content" style="line-height:1.7; color:var(--foreground); font-size:0.9375rem;">
                <?= nl2br(htmlspecialchars($synopsis, ENT_QUOTES, 'UTF-8')) ?>
            </div>
            <?php if (!empty($tags)): ?>
                <div class="card-footer" style="display:flex; flex-wrap:wrap; gap:0.5rem; padding-top:0;">
                    <?php foreach ($tags as $tag): ?>
                        <a href="/catalog?q=<?= urlencode($tag) ?>" class="badge badge-outline" style="text-decoration:none;">
                            #<?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card" style="margin-bottom:2rem;">
            <div class="card-header">
                <h2 class="card-title" style="font-size:1.1rem;">Informasi Bibliografi Lengkap</h2>
            </div>
            <div class="card-content">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; font-size:0.875rem;">
                    <div>
                        <span style="color:var(--muted-foreground); display:block; font-size:0.75rem;">Penerbit</span>
                        <strong><?= htmlspecialchars($publisher, ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                    <div>
                        <span style="color:var(--muted-foreground); display:block; font-size:0.75rem;">Tahun Terbit</span>
                        <strong><?= htmlspecialchars($year, ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                    <div>
                        <span style="color:var(--muted-foreground); display:block; font-size:0.75rem;">Edisi / Cetakan</span>
                        <strong><?= htmlspecialchars($edition, ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                    <div>
                        <span style="color:var(--muted-foreground); display:block; font-size:0.75rem;">Bahasa Teks</span>
                        <strong><?= htmlspecialchars($language, ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                    <div>
                        <span style="color:var(--muted-foreground); display:block; font-size:0.75rem;">Jumlah Halaman</span>
                        <strong><?= $pages > 0 ? $pages . ' halaman' : '-' ?></strong>
                    </div>
                    <div>
                        <span style="color:var(--muted-foreground); display:block; font-size:0.75rem;">ISBN-13</span>
                        <strong><?= htmlspecialchars($isbn, ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                    <div>
                        <span style="color:var(--muted-foreground); display:block; font-size:0.75rem;">Klasifikasi DDC</span>
                        <strong><?= htmlspecialchars($ddc, ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                    <div>
                        <span style="color:var(--muted-foreground); display:block; font-size:0.75rem;">Nomor Panggil Induk</span>
                        <strong><?= htmlspecialchars($callNumber, ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
                <div>
                    <h2 class="card-title" style="font-size:1.1rem;">Inventaris Eksemplar Fisik & Lokasi Rak</h2>
                    <p class="card-description">Daftar salinan fisik buku yang tersimpan di perpustakaan.</p>
                </div>
                <span class="badge"><?= count($copies) ?> Salinan Terdaftar</span>
            </div>
            <div class="card-content" style="padding:0; overflow-x:auto;">
                <?php if (empty($copies)): ?>
                    <div style="padding:2rem; text-align:center; color:var(--muted-foreground); font-size:0.875rem;">
                        Tidak ada eksemplar cetak fisik yang terdaftar untuk judul ini. Koleksi ini tersedia dalam bentuk digital.
                    </div>
                <?php else: ?>
                    <table class="table" style="width:100%; border-collapse:collapse; font-size:0.8125rem;">
                        <thead>
                            <tr style="border-bottom:1px solid var(--border); background-color:var(--muted); text-align:left;">
                                <th style="padding:0.75rem 1rem;">No. Barcode</th>
                                <th style="padding:0.75rem 1rem;">Lokasi Gedung & Lantai</th>
                                <th style="padding:0.75rem 1rem;">Nomor / Kode Rak</th>
                                <th style="padding:0.75rem 1rem;">Kondisi</th>
                                <th style="padding:0.75rem 1rem;">Status Sirkulasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($copies as $copy): ?>
                                <tr style="border-bottom:1px solid var(--border);">
                                    <td style="padding:0.75rem 1rem; font-family:monospace; font-weight:600;">
                                        <?= htmlspecialchars((string)$copy['inventory_barcode'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td style="padding:0.75rem 1rem;">
                                        <?= htmlspecialchars((string)$copy['shelf_building'], ENT_QUOTES, 'UTF-8') ?>, Lt. <?= (int)$copy['shelf_floor'] ?>
                                    </td>
                                    <td style="padding:0.75rem 1rem; font-weight:500;">
                                        <?= htmlspecialchars((string)$copy['shelf_aisle'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td style="padding:0.75rem 1rem;">
                                        <span class="badge badge-outline" style="font-size:0.7rem;">
                                            <?= htmlspecialchars((string)$copy['condition_status'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td style="padding:0.75rem 1rem;">
                                        <?php $status = (string)$copy['availability_status']; ?>
                                        <?php if ($status === 'AVAILABLE'): ?>
                                            <span class="badge badge-available">Tersedia di Rak</span>
                                        <?php elseif ($status === 'BORROWED'): ?>
                                            <span class="badge badge-borrowed">Sedang Dipinjam</span>
                                        <?php elseif ($status === 'RESERVED_HOLD'): ?>
                                            <span class="badge badge-warning">Dalam Antrean Hold</span>
                                        <?php elseif ($status === 'REFERENCE_ONLY'): ?>
                                            <span class="badge badge-outline">Hanya Baca di Tempat</span>
                                        <?php else: ?>
                                            <span class="badge"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($relatedItems)): ?>
    <section style="margin-top:3rem; border-top:1px solid var(--border); padding-top:2rem;">
        <div style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.35rem; font-weight:700; letter-spacing:-0.02em;">Koleksi Pustaka Terkait</h2>
            <p style="font-size:0.875rem; color:var(--muted-foreground);">Bahan bacaan lain yang memiliki topik atau subjek yang relevan.</p>
        </div>
        <div class="grid grid-cols-4">
            <?php foreach ($relatedItems as $book): ?>
                <?php require dirname(__DIR__, 3) . '/components/book-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
