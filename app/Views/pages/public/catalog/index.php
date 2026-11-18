<?php
declare(strict_types=1);

$items = $items ?? [];
$totalItems = $totalItems ?? 0;
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$keyword = $keyword ?? '';
$mediaType = $mediaType ?? '';
$ddcCode = $ddcCode ?? '';
$onlyAvailable = $onlyAvailable ?? false;
$sort = $sort ?? 'latest';
$ddcCategories = $ddcCategories ?? [];
$queryParams = $queryParams ?? [];
?>
<section style="margin-bottom:2rem;">
    <div style="border-bottom:1px solid var(--border); padding-bottom:1.5rem;">
        <span class="badge" style="margin-bottom:0.5rem;">Online Public Access Catalog</span>
        <h1 style="font-size:2rem; font-weight:700; letter-spacing:-0.03em; margin-bottom:0.5rem;">
            Katalog Induk Terbuka
        </h1>
        <p style="font-size:0.9375rem; color:var(--muted-foreground); max-width:700px;">
            Pencarian terpadu buku fisik, naskah akademik, dan literatur digital dengan pemantauan lokasi rak dan ketersediaan sirkulasi secara waktu-nyata.
        </p>
    </div>
</section>

<section style="margin-bottom:2rem;">
    <form method="GET" action="/catalog" style="display:flex; flex-direction:column; gap:1rem; background-color:var(--card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:1.25rem;">
        <div style="position:relative;">
            <div style="display:flex; gap:0.5rem;">
                <input
                    type="text"
                    id="catalog-search-input"
                    name="q"
                    class="input"
                    placeholder="Cari berdasarkan judul, nama pengarang, topik subjek, nomor panggil, atau ISBN..."
                    value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="off"
                    style="flex:1;"
                >
                <button type="submit" class="btn btn-primary">
                    Cari Koleksi
                </button>
            </div>
            <div id="catalog-suggest-dropdown" style="display:none; position:absolute; top:calc(100% + 4px); left:0; right:0; background-color:var(--card); border:1px solid var(--border); border-radius:var(--radius-md); box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); z-index:50; max-height:320px; overflow-y:auto;">
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:0.75rem; align-items:center;">
            <div>
                <label for="filter-type" style="display:block; font-size:0.75rem; font-weight:600; color:var(--muted-foreground); margin-bottom:0.25rem;">Format Koleksi</label>
                <select id="filter-type" name="type" class="input" style="width:100%; font-size:0.875rem;">
                    <option value="">Semua Format Media</option>
                    <option value="PHYSICAL_ONLY" <?= $mediaType === 'PHYSICAL_ONLY' ? 'selected' : '' ?>>Buku Cetak Fisik</option>
                    <option value="DIGITAL_ONLY" <?= $mediaType === 'DIGITAL_ONLY' ? 'selected' : '' ?>>E-Book Digital</option>
                    <option value="HYBRID" <?= $mediaType === 'HYBRID' ? 'selected' : '' ?>>Hibrida (Fisik & Digital)</option>
                    <option value="THESIS_REPOSITORY" <?= $mediaType === 'THESIS_REPOSITORY' ? 'selected' : '' ?>>Repositori Skripsi & Tesis</option>
                </select>
            </div>

            <div>
                <label for="filter-ddc" style="display:block; font-size:0.75rem; font-weight:600; color:var(--muted-foreground); margin-bottom:0.25rem;">Klasifikasi DDC</label>
                <select id="filter-ddc" name="ddc" class="input" style="width:100%; font-size:0.875rem;">
                    <option value="">Semua Bidang Ilmu</option>
                    <?php foreach ($ddcCategories as $cat): ?>
                        <option value="<?= htmlspecialchars((string)$cat['code'], ENT_QUOTES, 'UTF-8') ?>" <?= $ddcCode === $cat['code'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string)$cat['label'], ENT_QUOTES, 'UTF-8') ?> (<?= (int)$cat['count'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="filter-sort" style="display:block; font-size:0.75rem; font-weight:600; color:var(--muted-foreground); margin-bottom:0.25rem;">Urutan Penampilan</label>
                <select id="filter-sort" name="sort" class="input" style="width:100%; font-size:0.875rem;">
                    <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>Entri Terbaru</option>
                    <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Paling Sering Dipinjam</option>
                    <option value="year_desc" <?= $sort === 'year_desc' ? 'selected' : '' ?>>Tahun Terbit (Terbaru)</option>
                    <option value="year_asc" <?= $sort === 'year_asc' ? 'selected' : '' ?>>Tahun Terbit (Terlama)</option>
                    <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Judul Alfabetis (A-Z)</option>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; justify-content:flex-end; height:100%;">
                <div style="display:flex; align-items:center; gap:0.5rem; margin-top:1.25rem;">
                    <input type="checkbox" id="filter-avail" name="avail" value="1" <?= $onlyAvailable ? 'checked' : '' ?> style="width:1rem; height:1rem; accent-color:var(--primary); cursor:pointer;">
                    <label for="filter-avail" style="font-size:0.8125rem; cursor:pointer;">Tersedia di Rak Saat Ini</label>
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; padding-top:0.75rem; border-top:1px solid var(--border); flex-wrap:wrap; gap:0.5rem;">
            <div style="font-size:0.8125rem; color:var(--muted-foreground);">
                Ditemukan <strong><?= $totalItems ?></strong> judul pustaka
                <?php if ($keyword !== ''): ?>
                    untuk kata kunci "<em><?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?></em>"
                <?php endif; ?>
            </div>

            <div style="display:flex; gap:0.5rem;">
                <a href="/catalog" class="btn btn-ghost btn-sm">Reset Pencarian</a>
                <button type="submit" class="btn btn-outline btn-sm">Terapkan Kriteria</button>
            </div>
        </div>
    </form>
</section>

<?php if (empty($items)): ?>
    <section>
        <div class="card" style="text-align:center; padding:3.5rem 1.5rem;">
            <div style="font-size:1.125rem; font-weight:600; margin-bottom:0.5rem;">Tidak Ada Koleksi yang Cocok</div>
            <p style="font-size:0.875rem; color:var(--muted-foreground); max-width:480px; margin:0 auto 1.5rem auto;">
                Cobalah memeriksa ejaan kata kunci, menggunakan sinonim subjek yang lebih umum, atau menghapus filter pembatasan ketersediaan.
            </p>
            <div>
                <a href="/catalog" class="btn btn-primary btn-sm">Lihat Seluruh Koleksi</a>
            </div>
        </div>
    </section>
<?php else: ?>
    <section>
        <div class="grid grid-cols-3">
            <?php foreach ($items as $book): ?>
                <?php require dirname(__DIR__, 3) . '/components/book-card.php'; ?>
            <?php endforeach; ?>
        </div>

        <?php
        $baseUrl = '/catalog';
        require dirname(__DIR__, 3) . '/components/pagination.php';
        ?>
    </section>
<?php endif; ?>
