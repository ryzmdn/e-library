<?php
declare(strict_types=1);

$book = $book ?? [];
$slug = (string)($book['slug'] ?? '');
$title = (string)($book['title'] ?? 'Judul Buku');
$authors = (string)($book['authors_text'] ?? 'Penulis');
$availableCopies = (int)($book['available_physical_copies'] ?? 0);
$mediaType = (string)($book['media_type'] ?? 'HYBRID');
$rating = number_format((float)($book['rating_avg'] ?? 4.8), 1);
$category = (string)($book['category'] ?? 'Pengembangan Diri');
$formatLabel = $mediaType === 'DIGITAL_ONLY' ? 'E-Book' : ($mediaType === 'HYBRID' ? 'E-Book + Fisik' : 'Buku Fisik');
?>
<div class="catalog-card">
    <button type="button" class="popular-bookmark-btn" aria-label="Favorit">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
        </svg>
    </button>

    <div class="catalog-card-cover-wrap">
        <div style="width: 120px; height: 175px; background: linear-gradient(135deg, #F8FAFC 0%, #E2E8F0 100%); border-radius: 6px; box-shadow: 0 8px 20px -2px rgba(0,0,0,0.08); border: 1px solid #CBD5E1; padding: 0.75rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center;">
            <span style="font-size: 0.5rem; font-weight: 700; color: #64748B; letter-spacing: 0.05em; text-transform: uppercase;">RUANGBACA</span>
            <div style="font-size: 0.75rem; font-weight: 900; color: #1E293B; line-height: 1.15; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <span style="font-size: 0.45rem; color: #64748B;"><?= htmlspecialchars($authors, ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <div style="position: absolute; bottom: 0.85rem; left: 0.85rem; background: rgba(255, 255, 255, 0.95); border: 1px solid var(--border); border-radius: var(--radius-pill); padding: 0.15rem 0.5rem; font-size: 0.6875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
            <?php if ($availableCopies > 0 || $mediaType === 'DIGITAL_ONLY' || $mediaType === 'HYBRID'): ?>
                <span class="status-dot status-dot-success"></span>
                <span>Tersedia</span>
            <?php else: ?>
                <span class="status-dot" style="background-color: var(--destructive);"></span>
                <span>Dipinjam</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="catalog-card-body">
        <div class="catalog-card-category"><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></div>
        <h3 class="catalog-card-title">
            <a href="/catalog/<?= urlencode($slug) ?>" style="color: inherit; text-decoration: none;">
                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            </a>
        </h3>
        <div class="catalog-card-author"><?= htmlspecialchars($authors, ENT_QUOTES, 'UTF-8') ?></div>

        <div class="catalog-card-stats">
            <div class="catalog-card-rating">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="#F59E0B" stroke="#F59E0B" stroke-width="1">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
                <span><?= $rating ?></span>
            </div>
            <span class="badge badge-outline" style="font-size: 0.6875rem;"><?= htmlspecialchars($formatLabel, ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <a href="/catalog/<?= urlencode($slug) ?>" class="btn btn-primary btn-pill btn-block" style="padding: 0.55rem 1rem; font-size: 0.8125rem; font-weight: 600;">
            <span>Detail &amp; Pinjam</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </a>
    </div>
</div>
