<?php
declare(strict_types=1);

$items = $items ?? [];
$statusFilter = $statusFilter ?? 'all';
$sort = $sort ?? 'latest';
$metrics = $metrics ?? ['total' => 8, 'available' => 5, 'waiting' => 3];
$counts = $counts ?? ['all' => 8, 'unread' => 5, 'reading' => 1, 'completed' => 2];
$readingGoal = $readingGoal ?? [
    'year' => 2024,
    'target' => 20,
    'current' => 12,
    'percent' => 60,
    'streak_text' => 'Konsisten membaca 4 hari minggu ini',
    'remaining_text' => 'Tinggal 8 buku lagi untuk memenuhi resolusi tahunanmu!',
];
$topicStats = $topicStats ?? [
    ['name' => 'Pengembangan Diri', 'percent' => 45, 'color' => '#3E49D7'],
    ['name' => 'Fiksi Sejarah & Sastra', 'percent' => 35, 'color' => '#6366F1'],
    ['name' => 'Finansial & Bisnis', 'percent' => 20, 'color' => '#A5B4FC'],
];
$librarianPick = $librarianPick ?? [
    'title' => 'Kupikir-pikir Senja di Jakarta',
    'author' => 'Mochtar Lubis',
    'quote' => '"Kupikir-pikir Senja di Jakarta" memiliki kemiripan suasana dengan buku fiksi sejarah favoritmu.',
    'slug' => 'bumi-manusia',
];
?>

<div style="padding-top: 1.5rem; padding-bottom: 4rem;">
    <div class="hero-tag-badge" style="margin-bottom: 0.75rem;">
        <span class="status-dot status-dot-primary"></span>
        <span>Ruang Pribadi</span>
    </div>

    <div class="fav-header-row">
        <div>
            <h1 style="font-size: 2.375rem; font-weight: 800; letter-spacing: -0.03em; color: var(--foreground); line-height: 1.2; margin-bottom: 0.5rem;">
                Koleksi Favorit Saya
            </h1>
            <p style="font-size: 1rem; color: var(--muted-foreground); max-width: 680px; margin: 0; line-height: 1.5;">
                Buku-buku istimewa yang kamu simpan untuk dibaca, dipelajari, atau dinikmati kembali kapan pun suasana hatimu butuh inspirasi.
            </p>
        </div>

        <div class="fav-metrics-bar">
            <div class="fav-metric-badge">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#3E49D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <span>Koleksi: <strong><?= (int)$metrics['total'] ?> Buku</strong></span>
            </div>

            <div class="fav-metric-badge">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Tersedia: <strong><?= (int)$metrics['available'] ?> Siap Baca</strong></span>
            </div>

            <div class="fav-metric-badge">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>Antrean: <strong><?= (int)$metrics['waiting'] ?> Menunggu</strong></span>
            </div>
        </div>
    </div>

    <div class="fav-toolbar">
        <div class="fav-tabs-group">
            <a href="/member/favorites?status=all&sort=<?= urlencode($sort) ?>" class="fav-tab-pill <?= $statusFilter === 'all' ? 'active' : '' ?>">
                <span>Semua Favorit (<?= (int)$counts['all'] ?>)</span>
            </a>
            <a href="/member/favorites?status=unread&sort=<?= urlencode($sort) ?>" class="fav-tab-pill <?= $statusFilter === 'unread' ? 'active' : '' ?>">
                <span>Belum Dibaca (<?= (int)$counts['unread'] ?>)</span>
            </a>
            <a href="/member/favorites?status=reading&sort=<?= urlencode($sort) ?>" class="fav-tab-pill <?= $statusFilter === 'reading' ? 'active' : '' ?>">
                <span>Sedang Dibaca (<?= (int)$counts['reading'] ?>)</span>
            </a>
            <a href="/member/favorites?status=completed&sort=<?= urlencode($sort) ?>" class="fav-tab-pill <?= $statusFilter === 'completed' ? 'active' : '' ?>">
                <span>Selesai Dibaca (<?= (int)$counts['completed'] ?>)</span>
            </a>
        </div>

        <div class="fav-actions-group">
            <form method="GET" action="/member/favorites" style="display: inline-flex; align-items: center;">
                <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter, ENT_QUOTES, 'UTF-8') ?>">
                <div class="catalog-sort-pill">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <polyline points="19 12 12 19 5 12"></polyline>
                    </svg>
                    <select name="sort" class="catalog-sort-select" onchange="this.form.submit()">
                        <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>Terakhir Ditambahkan</option>
                        <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Paling Lama Disimpan</option>
                        <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Judul (A-Z)</option>
                    </select>
                </div>
            </form>

            <a href="/catalog" class="btn btn-secondary btn-pill" style="font-weight: 600;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Tambah Buku</span>
            </a>
        </div>
    </div>

    <?php if (empty($items)): ?>
        <div class="card" style="text-align: center; padding: 4rem 1.5rem; margin-bottom: 3.5rem;">
            <div style="font-size: 1.125rem; font-weight: 700; color: var(--foreground); margin-bottom: 0.5rem;">Belum Ada Buku di Kategori Ini</div>
            <p style="font-size: 0.875rem; color: var(--muted-foreground); max-width: 480px; margin: 0 auto 1.5rem auto;">
                Temukan buku-buku menarik di katalog kami dan klik ikon hati untuk menyimpannya ke koleksi favoritmu.
            </p>
            <a href="/catalog" class="btn btn-primary btn-pill btn-sm">Jelajahi Katalog Buku</a>
        </div>
    <?php else: ?>
        <div class="fav-grid-3col">
            <?php foreach ($items as $item): ?>
                <?php
                $fTitle = (string)($item['title'] ?? 'Judul Buku');
                $fAuthor = (string)($item['authors_text'] ?? 'Penulis');
                $fSlug = (string)($item['slug'] ?? '');
                $fRating = number_format((float)($item['rating_avg'] ?? 4.8), 1);
                $fRatingCount = (string)($item['rating_count_text'] ?? '1.4k ulasan');
                $fBadgeStatus = (string)($item['badge_status'] ?? 'Tersedia');
                $fBadgeColor = (string)($item['badge_color'] ?? 'success');
                $fNote = (string)($item['personal_note'] ?? 'Catatan pribadi');
                $fFooterInfo = (string)($item['footer_info'] ?? 'E-Book');
                $fActionLabel = (string)($item['action_label'] ?? 'Baca Sekarang');
                $fActionUrl = (string)($item['action_url'] ?? ('/catalog/' . urlencode($fSlug)));
                $fActionStyle = (string)($item['action_style'] ?? 'primary');
                ?>
                <div class="fav-card">
                    <div class="fav-card-top">
                        <div class="fav-card-cover">
                            <span class="fav-cover-badge fav-badge-<?= htmlspecialchars($fBadgeColor, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($fBadgeStatus, ENT_QUOTES, 'UTF-8') ?>
                            </span>

                            <?php if ($fSlug === 'laut-bercerita'): ?>
                                <div style="width: 70px; height: 105px; background: linear-gradient(180deg, #1E3A8A 0%, #0F172A 100%); border-radius: 4px; box-shadow: 0 4px 12px rgba(15,23,42,0.3); padding: 0.4rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; color: #FFFFFF;">
                                    <span style="font-size: 0.35rem; opacity: 0.8;">LEILA S. CHUDORI</span>
                                    <span style="font-size: 0.55rem; font-weight: 800; letter-spacing: 0.05em;">LAUT BERCERITA</span>
                                    <span style="font-size: 0.35rem; opacity: 0.6;">Novel</span>
                                </div>
                            <?php elseif ($fSlug === 'atomic-habits'): ?>
                                <div style="width: 70px; height: 105px; background: #FFFFFF; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid #E2E8F0; padding: 0.4rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center;">
                                    <span style="font-size: 0.35rem; font-weight: 700; color: #64748B;">JAMES CLEAR</span>
                                    <span style="font-size: 0.55rem; font-weight: 900; color: #0F172A; line-height: 1.1;">ATOMIC HABITS</span>
                                    <span style="font-size: 0.35rem; color: #94A3B8;">Tiny Changes</span>
                                </div>
                            <?php elseif ($fSlug === 'filosofi-teras'): ?>
                                <div style="width: 70px; height: 105px; background: #FFFFFF; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid #CBD5E1; padding: 0.4rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center;">
                                    <span style="font-size: 0.35rem; font-weight: 700; color: #475569;">HENRY M.</span>
                                    <span style="font-size: 0.55rem; font-weight: 900; color: #1E293B; line-height: 1.15;">FILOSOFI TERAS</span>
                                    <span style="font-size: 0.35rem; color: #64748B;">Stoa</span>
                                </div>
                            <?php elseif ($fSlug === 'sapiens'): ?>
                                <div style="width: 70px; height: 105px; background: #FAFAF9; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid #E7E5E4; padding: 0.4rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center;">
                                    <span style="font-size: 0.35rem; font-weight: 700; color: #78716C;">YUVAL NOAH HARARI</span>
                                    <span style="font-size: 0.55rem; font-weight: 900; color: #1C1917; letter-spacing: 0.05em;">SAPIENS</span>
                                    <span style="font-size: 0.35rem; color: #A8A29E;">KPG</span>
                                </div>
                            <?php elseif ($fSlug === 'gadis-kretek'): ?>
                                <div style="width: 70px; height: 105px; background: #FEF9C3; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 0.4rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; border: 1px solid #FEF08A;">
                                    <span style="font-size: 0.35rem; font-weight: 700; color: #713F12;">RATIH KUMALA</span>
                                    <span style="font-size: 0.55rem; font-weight: 900; color: #451A03; line-height: 1.15;">GADIS KRETEK</span>
                                    <span style="font-size: 0.35rem; color: #854D0E;">Novel</span>
                                </div>
                            <?php else: ?>
                                <div style="width: 70px; height: 105px; background: #0F172A; border-radius: 4px; box-shadow: 0 4px 12px rgba(15,23,42,0.3); padding: 0.4rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; color: #F8FAFC;">
                                    <span style="font-size: 0.35rem; font-weight: 600; color: #94A3B8;">MORGAN HOUSEL</span>
                                    <span style="font-size: 0.5rem; font-weight: 800; line-height: 1.15; color: #F8FAFC;">THE PSYCHOLOGY OF MONEY</span>
                                    <span style="font-size: 0.35rem; color: #38BDF8;">Pelajaran</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="fav-card-info">
                            <div class="fav-card-title-row">
                                <h2 class="fav-card-title">
                                    <a href="/catalog/<?= urlencode($fSlug) ?>" style="color: inherit; text-decoration: none;">
                                        <?= htmlspecialchars($fTitle, ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </h2>

                                <form method="POST" action="/member/favorites/<?= (int)($item['biblio_id'] ?? $item['id']) ?>/remove" style="margin: 0;">
                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(\App\Core\Session::getCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="fav-delete-btn" aria-label="Hapus dari Favorit" onclick="return confirm('Hapus buku ini dari daftar favorit?');">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <div class="fav-card-author"><?= htmlspecialchars($fAuthor, ENT_QUOTES, 'UTF-8') ?></div>

                            <div class="fav-card-rating-row">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="#F59E0B" stroke="#F59E0B" stroke-width="1">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                <span><?= $fRating ?></span>
                                <span style="color: var(--muted-foreground); font-weight: 400;">(<?= htmlspecialchars($fRatingCount, ENT_QUOTES, 'UTF-8') ?>)</span>
                            </div>

                            <?php if ($fNote !== ''): ?>
                                <div class="fav-note-box">
                                    <div class="fav-note-label">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                        <span>Catatanmu:</span>
                                    </div>
                                    <div>&ldquo;<?= htmlspecialchars($fNote, ENT_QUOTES, 'UTF-8') ?>&rdquo;</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="fav-card-bottom">
                        <div class="fav-footer-info">
                            <?php if ($item['slug'] === 'atomic-habits'): ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            <?php elseif ($item['slug'] === 'filosofi-teras'): ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                </svg>
                            <?php elseif ($item['slug'] === 'gadis-kretek'): ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            <?php else: ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                            <?php endif; ?>
                            <span><?= htmlspecialchars($fFooterInfo, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>

                        <div>
                            <?php if ($fActionStyle === 'primary'): ?>
                                <a href="<?= htmlspecialchars($fActionUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary btn-pill btn-sm" style="padding: 0.35rem 0.95rem; font-weight: 600;">
                                    <?= htmlspecialchars($fActionLabel, ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php elseif ($fActionStyle === 'secondary'): ?>
                                <a href="<?= htmlspecialchars($fActionUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary btn-pill btn-sm" style="padding: 0.35rem 0.95rem; font-weight: 600;">
                                    <?= htmlspecialchars($fActionLabel, ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php else: ?>
                                <a href="<?= htmlspecialchars($fActionUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline btn-pill btn-sm" style="padding: 0.35rem 0.95rem; font-weight: 600;">
                                    <?= htmlspecialchars($fActionLabel, ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <section class="reading-inspiration-banner">
        <div class="inspiration-left">
            <div class="inspiration-icon-wrap">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--foreground); margin: 0 0 0.25rem 0;">
                    Ingin menyimpan lebih banyak inspirasi?
                </h3>
                <p style="font-size: 0.8125rem; color: var(--muted-foreground); margin: 0;">
                    Klik ikon hati di setiap lembar katalog buku untuk mengelompokkannya di ruang koleksi pribadimu ini.
                </p>
            </div>
        </div>

        <div>
            <a href="/catalog" class="btn btn-primary btn-pill" style="padding: 0.6rem 1.35rem; font-weight: 600;">
                <span>Jelajahi Katalog</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </section>

    <div class="reading-stats-grid">
        <div class="reading-stat-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--foreground);">Target Baca <?= (int)$readingGoal['year'] ?></span>
                <span class="badge" style="background-color: var(--status-available-bg); color: var(--status-available-text); font-weight: 600; font-size: 0.6875rem;">
                    Tercapai <?= (int)$readingGoal['percent'] ?>%
                </span>
            </div>

            <div class="radial-progress-box">
                <div style="position: relative; width: 72px; height: 72px; display: flex; align-items: center; justify-content: center;">
                    <svg class="radial-circle-svg" viewBox="0 0 36 36">
                        <path
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none"
                            stroke="#E2E8F0"
                            stroke-width="3.5"
                        />
                        <path
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none"
                            stroke="#3E49D7"
                            stroke-width="3.5"
                            stroke-dasharray="60, 100"
                            stroke-linecap="round"
                        />
                    </svg>
                    <div style="position: absolute; font-size: 0.8125rem; font-weight: 800; color: var(--foreground);">
                        <?= (int)$readingGoal['current'] ?>/<?= (int)$readingGoal['target'] ?>
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.9375rem; font-weight: 700; color: var(--foreground);">
                        <?= (int)$readingGoal['current'] ?> dari <?= (int)$readingGoal['target'] ?> Buku
                    </div>
                    <div style="font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.15rem; line-height: 1.4;">
                        <?= htmlspecialchars((string)$readingGoal['remaining_text'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
            </div>

            <div style="margin-top: auto; display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: var(--muted-foreground); border-top: 1px solid var(--border-subtle); padding-top: 0.75rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3E49D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
                <span><?= htmlspecialchars((string)$readingGoal['streak_text'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>

        <div class="reading-stat-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--foreground);">Topik Favorit Terbanyak</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--muted-foreground);">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
            </div>

            <div>
                <?php foreach ($topicStats as $top): ?>
                    <div class="topic-bar-row">
                        <div class="topic-bar-header">
                            <span><?= htmlspecialchars((string)$top['name'], ENT_QUOTES, 'UTF-8') ?></span>
                            <strong><?= (int)$top['percent'] ?>%</strong>
                        </div>
                        <div class="topic-bar-track">
                            <div class="topic-bar-fill" style="width: <?= (int)$top['percent'] ?>%; background-color: <?= htmlspecialchars((string)$top['color'], ENT_QUOTES, 'UTF-8') ?>;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: auto; font-size: 0.75rem; color: var(--muted-foreground); border-top: 1px solid var(--border-subtle); padding-top: 0.75rem;">
                Berdasarkan 8 buku dalam koleksi aktifmu.
            </div>
        </div>

        <div class="reading-stat-card" style="background-color: var(--secondary);">
            <div style="font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.05em; color: var(--primary); text-transform: uppercase; margin-bottom: 0.35rem;">
                Rekomendasi Pustakawan
            </div>
            <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--foreground); margin: 0 0 0.75rem 0;">
                Cocok dengan Koleksimu
            </h3>

            <p style="font-size: 0.8125rem; font-style: italic; color: var(--foreground); line-height: 1.5; margin: 0 0 1rem 0;">
                <?= htmlspecialchars((string)$librarianPick['quote'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <div style="margin-top: auto; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 0.75rem;">
                <span style="font-size: 0.75rem; color: var(--muted-foreground);">Oleh <?= htmlspecialchars((string)$librarianPick['author'], ENT_QUOTES, 'UTF-8') ?></span>
                <a href="/catalog/<?= urlencode((string)$librarianPick['slug']) ?>" style="font-size: 0.75rem; font-weight: 600; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.2rem;">
                    <span>Lihat Detail</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
