<?php
declare(strict_types=1);

$items = $items ?? [];
$totalItems = $totalItems ?? 4820;
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$keyword = $keyword ?? '';
$activeCategory = $activeCategory ?? 'all';
$categoriesList = $categoriesList ?? [];
$onlyAvailable = $onlyAvailable ?? false;
$formatAudio = $formatAudio ?? false;
$langId = $langId ?? false;
$recommendedOnly = $recommendedOnly ?? false;
$sort = $sort ?? 'popular';
$viewMode = $viewMode ?? 'grid';
$queryParams = $queryParams ?? [];
$totalEbooks = $totalEbooks ?? 4820;
$totalAudios = $totalAudios ?? 950;

$buildUrl = function (array $newParams) use ($queryParams): string {
    $merged = array_merge($queryParams, $newParams);
    foreach ($merged as $k => $v) {
        if ($v === '' || $v === null || $v === false) {
            unset($merged[$k]);
        }
    }
    return '/catalog' . (!empty($merged) ? '?' . http_build_query($merged) : '');
};
?>

<div style="padding-top: 1.5rem; padding-bottom: 4rem;">
    <div class="hero-tag-badge" style="margin-bottom: 0.75rem;">
        <span class="status-dot status-dot-primary"></span>
        <span>Pustaka Digital Terkurasi</span>
    </div>

    <div class="catalog-header-row">
        <div>
            <h1 style="font-size: 2.375rem; font-weight: 800; letter-spacing: -0.03em; color: var(--foreground); line-height: 1.2; margin-bottom: 0.5rem;">
                Katalog Koleksi Digital
            </h1>
            <p style="font-size: 1rem; color: var(--muted-foreground); max-width: 680px; margin: 0; line-height: 1.5;">
                Jelajahi lebih dari <?= htmlspecialchars(number_format((float)$totalEbooks, 0, ',', '.'), ENT_QUOTES, 'UTF-8') ?> koleksi buku digital pilihan dengan kurasi terbaik untuk menemani perjalanan membacamu.
            </p>
        </div>

        <div class="catalog-metric-pills">
            <div class="catalog-metric-pill">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#3E49D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <span><?= htmlspecialchars(number_format((float)$totalEbooks, 0, ',', '.'), ENT_QUOTES, 'UTF-8') ?> E-Book</span>
            </div>

            <div class="catalog-metric-pill">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#3E49D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                    <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
                </svg>
                <span><?= htmlspecialchars(number_format((float)$totalAudios, 0, ',', '.'), ENT_QUOTES, 'UTF-8') ?> Audio</span>
            </div>
        </div>
    </div>

    <form method="GET" action="/catalog" id="catalog-filter-form">
        <input type="hidden" name="category" value="<?= htmlspecialchars($activeCategory, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="avail" value="<?= $onlyAvailable ? '1' : '0' ?>">
        <input type="hidden" name="format" value="<?= $formatAudio ? 'audio' : '' ?>">
        <input type="hidden" name="lang" value="<?= $langId ? 'id' : '' ?>">
        <input type="hidden" name="recommended" value="<?= $recommendedOnly ? '1' : '0' ?>">
        <input type="hidden" name="view" value="<?= htmlspecialchars($viewMode, ENT_QUOTES, 'UTF-8') ?>">

        <div class="catalog-search-toolbar">
            <div class="catalog-search-bar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--muted-foreground); flex-shrink: 0;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input
                    type="text"
                    name="q"
                    placeholder="Cari judul, penulis, ISBN, atau kata kunci..."
                    value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="off"
                >
                <?php if ($keyword !== ''): ?>
                    <a href="<?= htmlspecialchars($buildUrl(['q' => '']), ENT_QUOTES, 'UTF-8') ?>" class="catalog-search-clear" aria-label="Hapus Pencarian">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>

            <div class="catalog-controls-group">
                <div class="catalog-sort-pill">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <polyline points="19 12 12 19 5 12"></polyline>
                    </svg>
                    <select name="sort" class="catalog-sort-select" onchange="this.form.submit()">
                        <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Terpopuler</option>
                        <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>Terbaru</option>
                        <option value="rating_desc" <?= $sort === 'rating_desc' ? 'selected' : '' ?>>Rating Tertinggi</option>
                        <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Judul (A-Z)</option>
                    </select>
                </div>

                <div class="catalog-view-toggle">
                    <button type="button" class="catalog-view-btn <?= $viewMode === 'grid' ? 'active' : '' ?>" onclick="switchViewMode('grid')" aria-label="Tampilan Grid">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </button>
                    <button type="button" class="catalog-view-btn <?= $viewMode === 'list' ? 'active' : '' ?>" onclick="switchViewMode('list')" aria-label="Tampilan Daftar">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="8" y1="6" x2="21" y2="6"></line>
                            <line x1="8" y1="12" x2="21" y2="12"></line>
                            <line x1="8" y1="18" x2="21" y2="18"></line>
                            <line x1="3" y1="6" x2="3.01" y2="6"></line>
                            <line x1="3" y1="12" x2="3.01" y2="12"></line>
                            <line x1="3" y1="18" x2="3.01" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="catalog-categories-bar">
        <?php foreach ($categoriesList as $cat): ?>
            <a href="<?= htmlspecialchars($buildUrl(['category' => $cat['id'], 'page' => 1]), ENT_QUOTES, 'UTF-8') ?>" class="category-filter-pill <?= $activeCategory === $cat['id'] ? 'active' : '' ?>">
                <span><?= htmlspecialchars((string)$cat['label'], ENT_QUOTES, 'UTF-8') ?></span>
                <span style="opacity: 0.8; font-size: 0.75rem; margin-left: 0.25rem;">(<?= htmlspecialchars(number_format((float)$cat['count'], 0, ',', '.'), ENT_QUOTES, 'UTF-8') ?>)</span>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="fast-filters-row">
        <span style="color: var(--muted-foreground); font-weight: 500;">Filter Cepat:</span>

        <a href="<?= htmlspecialchars($buildUrl(['avail' => $onlyAvailable ? '0' : '1', 'page' => 1]), ENT_QUOTES, 'UTF-8') ?>" class="fast-filter-pill <?= $onlyAvailable ? 'active' : '' ?>">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>Hanya yang Tersedia</span>
        </a>

        <a href="<?= htmlspecialchars($buildUrl(['format' => $formatAudio ? '' : 'audio', 'page' => 1]), ENT_QUOTES, 'UTF-8') ?>" class="fast-filter-pill <?= $formatAudio ? 'active' : '' ?>">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
            </svg>
            <span>Buku Format Audio</span>
        </a>

        <a href="<?= htmlspecialchars($buildUrl(['lang' => $langId ? '' : 'id', 'page' => 1]), ENT_QUOTES, 'UTF-8') ?>" class="fast-filter-pill <?= $langId ? 'active' : '' ?>">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="2" y1="12" x2="22" y2="12"></line>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
            </svg>
            <span>Bahasa Indonesia</span>
        </a>

        <a href="<?= htmlspecialchars($buildUrl(['recommended' => $recommendedOnly ? '0' : '1', 'page' => 1]), ENT_QUOTES, 'UTF-8') ?>" class="fast-filter-pill <?= $recommendedOnly ? 'active' : '' ?>">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
            <span>Koleksi Rekomendasi</span>
        </a>
    </div>

    <?php if (empty($items)): ?>
        <div class="card" style="text-align: center; padding: 4rem 1.5rem; margin-top: 1rem;">
            <div style="font-size: 1.125rem; font-weight: 700; color: var(--foreground); margin-bottom: 0.5rem;">Tidak Ada Koleksi yang Cocok</div>
            <p style="font-size: 0.875rem; color: var(--muted-foreground); max-width: 480px; margin: 0 auto 1.5rem auto;">
                Cobalah memeriksa kembali kata kunci pencarian atau mengatur ulang filter kategori dan ketersediaan.
            </p>
            <a href="/catalog" class="btn btn-primary btn-pill btn-sm">Reset Seluruh Kriteria</a>
        </div>
    <?php else: ?>
        <div class="catalog-grid-4col">
            <?php foreach ($items as $book): ?>
                <?php
                $bTitle = (string)($book['title'] ?? 'Judul Buku');
                $bAuthor = (string)($book['authors_text'] ?? 'Penulis');
                $bSlug = (string)($book['slug'] ?? '');
                $bCategory = (string)($book['category'] ?? 'Pengembangan Diri');
                $bRating = number_format((float)($book['rating_avg'] ?? 4.8), 1);
                $bReviews = (string)($book['rating_count_text'] ?? '2.1k ulasan');
                $bFormat = (string)($book['media_format'] ?? 'E-Book');
                ?>
                <div class="catalog-card">
                    <button type="button" class="popular-bookmark-btn" aria-label="Favorit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </button>

                    <div class="catalog-card-cover-wrap">
                        <?php if ($bSlug === 'filosofi-teras'): ?>
                            <div style="width: 120px; height: 175px; background: #FFFFFF; border-radius: 6px; box-shadow: 0 8px 20px -2px rgba(0,0,0,0.1); border: 1px solid #CBD5E1; padding: 0.65rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center;">
                                <span style="font-size: 0.45rem; font-weight: 700; color: #475569;">HENRY MANAMPIRING</span>
                                <div>
                                    <div style="font-size: 0.75rem; font-weight: 900; color: #1E293B; line-height: 1.15;">FILOSOFI</div>
                                    <div style="font-size: 0.75rem; font-weight: 900; color: #1E293B; line-height: 1.15;">TERAS</div>
                                </div>
                                <span style="font-size: 0.4rem; color: #64748B;">Penerbit Kompas</span>
                            </div>
                        <?php elseif ($bSlug === 'atomic-habits'): ?>
                            <div style="width: 120px; height: 175px; background: #FFFFFF; border-radius: 6px; box-shadow: 0 8px 20px -2px rgba(0,0,0,0.1); border: 1px solid #E2E8F0; padding: 0.65rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center;">
                                <span style="font-size: 0.45rem; font-weight: 700; color: #64748B;">JAMES CLEAR</span>
                                <div>
                                    <div style="font-size: 0.75rem; font-weight: 900; color: #0F172A; line-height: 1.1;">ATOMIC</div>
                                    <div style="font-size: 0.75rem; font-weight: 900; color: #0F172A; line-height: 1.1;">HABITS</div>
                                    <div style="width: 20px; height: 2px; background: #3E49D7; margin: 0.25rem auto;"></div>
                                </div>
                                <span style="font-size: 0.4rem; color: #94A3B8;">An Easy &amp; Proven Way</span>
                            </div>
                        <?php elseif ($bSlug === 'laut-bercerita'): ?>
                            <div style="width: 120px; height: 175px; background: linear-gradient(180deg, #1E3A8A 0%, #0F172A 100%); border-radius: 6px; box-shadow: 0 8px 20px -2px rgba(15,23,42,0.3); padding: 0.65rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; color: #FFFFFF;">
                                <span style="font-size: 0.45rem; opacity: 0.8;">LEILA S. CHUDORI</span>
                                <div>
                                    <div style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.05em;">LAUT</div>
                                    <div style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.05em;">BERCERITA</div>
                                </div>
                                <span style="font-size: 0.4rem; opacity: 0.6;">Novel Sastra</span>
                            </div>
                        <?php elseif ($bSlug === 'sapiens'): ?>
                            <div style="width: 120px; height: 175px; background: #FAFAF9; border-radius: 6px; box-shadow: 0 8px 20px -2px rgba(0,0,0,0.1); border: 1px solid #E7E5E4; padding: 0.65rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center;">
                                <span style="font-size: 0.45rem; font-weight: 700; color: #78716C;">YUVAL NOAH HARARI</span>
                                <div>
                                    <div style="font-size: 0.75rem; font-weight: 900; color: #1C1917; letter-spacing: 0.05em;">SAPIENS</div>
                                </div>
                                <span style="font-size: 0.4rem; color: #A8A29E;">KPG Pustaka</span>
                            </div>
                        <?php elseif ($bSlug === 'the-psychology-of-money'): ?>
                            <div style="width: 120px; height: 175px; background: #0F172A; border-radius: 6px; box-shadow: 0 8px 20px -2px rgba(15,23,42,0.3); padding: 0.65rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; color: #F8FAFC;">
                                <span style="font-size: 0.45rem; font-weight: 600; color: #94A3B8;">MORGAN HOUSEL</span>
                                <div>
                                    <div style="font-size: 0.65rem; font-weight: 800; line-height: 1.15; color: #F8FAFC;">THE PSYCHOLOGY OF MONEY</div>
                                </div>
                                <span style="font-size: 0.4rem; color: #38BDF8;">Pelajaran Kekayaan</span>
                            </div>
                        <?php elseif ($bSlug === 'bumi-manusia'): ?>
                            <div style="width: 120px; height: 175px; background: #EFE8DA; border-radius: 6px; box-shadow: 0 8px 20px -2px rgba(120,80,40,0.25); border-left: 4px solid #8C6A43; padding: 0.65rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center;">
                                <span style="font-size: 0.45rem; font-weight: 700; color: #5C4033;">PRAMOEDYA ANANTA TOER</span>
                                <div>
                                    <div style="font-size: 0.75rem; font-weight: 900; color: #3E2723; font-family: Georgia, serif;">BUMI MANUSIA</div>
                                </div>
                                <span style="font-size: 0.4rem; color: #8D6E63;">Tetralogi Buru</span>
                            </div>
                        <?php elseif ($bSlug === 'sebuah-seni-untuk-bersikap-bodo-amat'): ?>
                            <div style="width: 120px; height: 175px; background: #EA580C; border-radius: 6px; box-shadow: 0 8px 20px -2px rgba(234,88,12,0.3); padding: 0.65rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; color: #FFFFFF;">
                                <span style="font-size: 0.45rem; font-weight: 700; opacity: 0.9;">MARK MANSON</span>
                                <div>
                                    <div style="font-size: 0.65rem; font-weight: 900; line-height: 1.15;">SEBUAH SENI BODO AMAT</div>
                                </div>
                                <span style="font-size: 0.4rem; opacity: 0.8;">Grasindo</span>
                            </div>
                        <?php else: ?>
                            <div style="width: 120px; height: 175px; background: #3730A3; border-radius: 6px; box-shadow: 0 8px 20px -2px rgba(55,48,163,0.3); padding: 0.65rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; color: #EEF2FF;">
                                <span style="font-size: 0.45rem; font-weight: 700; color: #C7D2FE;">EKA KURNIAWAN</span>
                                <div>
                                    <div style="font-size: 0.7rem; font-weight: 900; line-height: 1.15; color: #FFFFFF;">CANTIK ITU LUKA</div>
                                </div>
                                <span style="font-size: 0.4rem; color: #A5B4FC;">Realisme Magis</span>
                            </div>
                        <?php endif; ?>

                        <div style="position: absolute; bottom: 0.85rem; left: 0.85rem; background: rgba(255, 255, 255, 0.95); border: 1px solid var(--border); border-radius: var(--radius-pill); padding: 0.15rem 0.5rem; font-size: 0.6875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                            <span class="status-dot status-dot-success"></span>
                            <span>Tersedia</span>
                        </div>
                    </div>

                    <div class="catalog-card-body">
                        <div class="catalog-card-category"><?= htmlspecialchars($bCategory, ENT_QUOTES, 'UTF-8') ?></div>
                        <h2 class="catalog-card-title">
                            <a href="/catalog/<?= urlencode($bSlug) ?>" style="color: inherit; text-decoration: none;">
                                <?= htmlspecialchars($bTitle, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h2>
                        <div class="catalog-card-author"><?= htmlspecialchars($bAuthor, ENT_QUOTES, 'UTF-8') ?></div>

                        <div class="catalog-card-stats">
                            <div class="catalog-card-rating">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="#F59E0B" stroke="#F59E0B" stroke-width="1">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                <span><?= $bRating ?></span>
                                <span style="color: var(--muted-foreground); font-weight: 400;"><?= htmlspecialchars($bReviews, ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <span class="badge badge-outline" style="font-size: 0.6875rem;"><?= htmlspecialchars($bFormat, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>

                        <a href="/catalog/<?= urlencode($bSlug) ?>" class="btn btn-primary btn-pill btn-block" style="padding: 0.55rem 1rem; font-size: 0.8125rem; font-weight: 600;">
                            <span>Detail &amp; Pinjam</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="catalog-pagination-row">
            <div style="font-size: 0.8125rem; color: var(--muted-foreground);">
                Menampilkan 1 - <?= count($items) ?> dari <?= htmlspecialchars(number_format((float)$totalItems, 0, ',', '.'), ENT_QUOTES, 'UTF-8') ?> buku
            </div>

            <div class="catalog-pagination-pills">
                <a href="<?= htmlspecialchars($buildUrl(['page' => max(1, $currentPage - 1)]), ENT_QUOTES, 'UTF-8') ?>" class="pagination-pill-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>" aria-label="Sebelumnya">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </a>

                <a href="<?= htmlspecialchars($buildUrl(['page' => 1]), ENT_QUOTES, 'UTF-8') ?>" class="pagination-pill-btn <?= $currentPage === 1 ? 'active' : '' ?>">1</a>
                <a href="<?= htmlspecialchars($buildUrl(['page' => 2]), ENT_QUOTES, 'UTF-8') ?>" class="pagination-pill-btn <?= $currentPage === 2 ? 'active' : '' ?>">2</a>
                <a href="<?= htmlspecialchars($buildUrl(['page' => 3]), ENT_QUOTES, 'UTF-8') ?>" class="pagination-pill-btn <?= $currentPage === 3 ? 'active' : '' ?>">3</a>
                <span style="padding: 0 0.35rem; color: var(--muted-foreground);">&hellip;</span>
                <a href="<?= htmlspecialchars($buildUrl(['page' => 48]), ENT_QUOTES, 'UTF-8') ?>" class="pagination-pill-btn <?= $currentPage === 48 ? 'active' : '' ?>">48</a>

                <a href="<?= htmlspecialchars($buildUrl(['page' => $currentPage + 1]), ENT_QUOTES, 'UTF-8') ?>" class="pagination-pill-btn" aria-label="Berikutnya">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function switchViewMode(mode) {
  var form = document.getElementById('catalog-filter-form');
  if (form) {
    var viewInput = form.querySelector('input[name="view"]');
    if (viewInput) {
      viewInput.value = mode;
      form.submit();
    }
  }
}
</script>
