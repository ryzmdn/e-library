<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 style="font-size:1.5rem; font-weight:700; margin:0 0 0.25rem 0;">Katalog Bibliografi</h1>
        <p style="color:var(--muted-foreground); margin:0; font-size:0.875rem;">Kelola seluruh koleksi judul buku, metadata DDC, dokumen digital, dan keterkaitan eksemplar.</p>
    </div>
    <div>
        <a href="/admin/catalog/create" class="btn btn-primary" style="font-size:0.875rem;">+ Tambah Buku Baru</a>
    </div>
</div>

<div class="card" style="padding:1.25rem; margin-bottom:1.5rem;">
    <form method="GET" action="/admin/catalog" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:flex-end;">
        <div style="flex:1; min-width:240px;">
            <label style="display:block; font-size:0.8125rem; font-weight:500; margin-bottom:0.35rem;">Pencarian Judul / Penulis / ISBN</label>
            <input type="text" name="q" value="<?= htmlspecialchars($filters['keyword'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ketik kata kunci pencarian..." class="form-control">
        </div>

        <div style="width:180px;">
            <label style="display:block; font-size:0.8125rem; font-weight:500; margin-bottom:0.35rem;">Format Media</label>
            <select name="media" class="form-control">
                <option value="">Semua Format</option>
                <option value="PRINTED_BOOK" <?= ($filters['media_type'] ?? '') === 'PRINTED_BOOK' ? 'selected' : '' ?>>Buku Cetak</option>
                <option value="DIGITAL_EBOOK" <?= ($filters['media_type'] ?? '') === 'DIGITAL_EBOOK' ? 'selected' : '' ?>>E-Book Digital</option>
                <option value="HYBRID" <?= ($filters['media_type'] ?? '') === 'HYBRID' ? 'selected' : '' ?>>Hybrid (Cetak & Digital)</option>
                <option value="THESIS" <?= ($filters['media_type'] ?? '') === 'THESIS' ? 'selected' : '' ?>>Skripsi / Tesis</option>
            </select>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="/admin/catalog" class="btn btn-outline">Reset</a>
        </div>
    </form>
</div>

<div class="card" style="padding:1.5rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <div style="font-size:0.875rem; color:var(--muted-foreground);">
            Menampilkan <?= count($items) ?> dari <?= number_format($totalItems, 0, ',', '.') ?> judul terdaftar
        </div>
    </div>

    <?php if (empty($items)): ?>
        <div style="text-align:center; padding:3rem 1rem; color:var(--muted-foreground);">
            <div style="font-weight:600; font-size:1rem; margin-bottom:0.5rem;">Tidak ada koleksi buku ditemukan</div>
            <p style="font-size:0.875rem; margin:0 0 1rem 0;">Coba sesuaikan kata kunci pencarian atau filter yang dipilih.</p>
            <a href="/admin/catalog/create" class="btn btn-primary" style="font-size:0.875rem;">Tambah Buku Sekarang</a>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="table" style="width:100%; font-size:0.875rem;">
                <thead>
                    <tr>
                        <th style="padding:0.75rem; width:50px;">Sampul</th>
                        <th style="padding:0.75rem;">Informasi Bibliografi</th>
                        <th style="padding:0.75rem;">Klasifikasi & Nomor Panggil</th>
                        <th style="padding:0.75rem; text-align:center;">Format</th>
                        <th style="padding:0.75rem; text-align:center;">Eksemplar</th>
                        <th style="padding:0.75rem; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td style="padding:0.75rem; vertical-align:middle;">
                                <?php if (!empty($item['cover_image_path'])): ?>
                                    <img src="<?= htmlspecialchars($item['cover_image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="width:40px; height:56px; object-fit:cover; border-radius:4px; border:1px solid var(--border);">
                                <?php else: ?>
                                    <div style="width:40px; height:56px; background-color:#e2e8f0; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:0.625rem; color:#64748b; font-weight:700;">BUKU</div>
                                <?php endif; ?>
                            </td>
                            <td style="padding:0.75rem; vertical-align:middle;">
                                <div style="font-weight:600; color:var(--foreground); margin-bottom:0.25rem;">
                                    <a href="/catalog/<?= htmlspecialchars($item['slug'] ?? (string)$item['id'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" style="text-decoration:none; color:inherit;">
                                        <?= htmlspecialchars($item['title'] ?? 'Tanpa Judul', ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </div>
                                <div style="font-size:0.75rem; color:var(--muted-foreground);">
                                    Penulis: <?= htmlspecialchars($item['authors_text'] ?? '-', ENT_QUOTES, 'UTF-8') ?> &bull; Penerbit: <?= htmlspecialchars($item['publisher_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string)($item['publication_year'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>)
                                </div>
                                <?php if (!empty($item['isbn_13'])): ?>
                                    <div style="font-size:0.6875rem; font-family:monospace; color:var(--muted-foreground); margin-top:0.2rem;">
                                        ISBN: <?= htmlspecialchars($item['isbn_13'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="padding:0.75rem; vertical-align:middle;">
                                <div style="font-family:monospace; font-weight:600; font-size:0.8125rem;">
                                    <?= htmlspecialchars($item['call_number_base'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <div style="font-size:0.75rem; color:var(--muted-foreground);">
                                    DDC: <?= htmlspecialchars($item['ddc_code'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </td>
                            <td style="padding:0.75rem; text-align:center; vertical-align:middle;">
                                <span style="display:inline-block; padding:0.2rem 0.5rem; border-radius:9999px; font-size:0.6875rem; font-weight:600; background-color:#f1f5f9; color:#334155;">
                                    <?= htmlspecialchars($item['media_type'] ?? 'PRINTED_BOOK', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td style="padding:0.75rem; text-align:center; vertical-align:middle;">
                                <div style="font-weight:600; font-size:0.9375rem;">
                                    <?= number_format((int)($item['total_copies'] ?? 0), 0, ',', '.') ?>
                                </div>
                                <div style="font-size:0.6875rem; color:var(--muted-foreground);">
                                    Tersedia: <?= number_format((int)($item['available_physical_copies'] ?? 0), 0, ',', '.') ?>
                                </div>
                            </td>
                            <td style="padding:0.75rem; text-align:right; vertical-align:middle; white-space:nowrap;">
                                <div style="display:inline-flex; gap:0.35rem;">
                                    <a href="/admin/copies/create?biblio_id=<?= (int)$item['id'] ?>" class="btn btn-outline" style="padding:0.25rem 0.5rem; font-size:0.75rem;" title="Tambah Eksemplar Fisik">+ Eksemplar</a>
                                    <a href="/admin/catalog/<?= (int)$item['id'] ?>/edit" class="btn btn-outline" style="padding:0.25rem 0.5rem; font-size:0.75rem;">Edit</a>
                                    <form method="POST" action="/admin/catalog/<?= (int)$item['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini beserta seluruh eksemplar dan aset terkait?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" class="btn btn-danger" style="padding:0.25rem 0.5rem; font-size:0.75rem;">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div style="display:flex; justify-content:center; gap:0.5rem; margin-top:1.5rem;">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="/admin/catalog?page=<?= $p ?>&q=<?= urlencode($filters['keyword'] ?? '') ?>&media=<?= urlencode($filters['media_type'] ?? '') ?>"
                       class="btn <?= $p === $currentPage ? 'btn-primary' : 'btn-outline' ?>"
                       style="padding:0.35rem 0.75rem; font-size:0.8125rem;">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
