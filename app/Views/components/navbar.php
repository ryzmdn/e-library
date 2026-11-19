<?php
declare(strict_types=1);

$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
$uriPath = parse_url($currentUri, PHP_URL_PATH) ?? '/';

$isHomeActive = ($uriPath === '/');
$isCatalogActive = str_starts_with($uriPath, '/catalog') || str_starts_with($uriPath, '/book/');
$isFavoritesActive = str_starts_with($uriPath, '/member/favorites');
$isHistoryActive = str_starts_with($uriPath, '/member/history') || str_starts_with($uriPath, '/member/loans');

$displayName = $currentUserName ?? 'Pengguna';
$initials = 'U';
$nameParts = explode(' ', trim($displayName));
if (count($nameParts) >= 2) {
    $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
} else {
    $initials = strtoupper(substr($displayName, 0, 1));
}
?>
<header class="site-header">
    <div class="container navbar">
        <a href="/" class="nav-brand-container">
            <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path>
                <path d="M6 6h10"></path>
                <path d="M6 10h10"></path>
            </svg>
            <span class="brand-title">RuangBaca</span>
            <span class="brand-badge">E-Perpus</span>
        </a>

        <nav class="nav-pills-bar">
            <a href="/" class="nav-pill <?= $isHomeActive ? 'active' : '' ?>">Beranda</a>
            <a href="/catalog" class="nav-pill <?= $isCatalogActive ? 'active' : '' ?>">Katalog Buku</a>
            <a href="/catalog?filter=categories" class="nav-pill">Kategori</a>
            <a href="/member/favorites" class="nav-pill <?= $isFavoritesActive ? 'active' : '' ?>">Favorit</a>
            <a href="/member/history" class="nav-pill <?= $isHistoryActive ? 'active' : '' ?>">Riwayat Membaca</a>
        </nav>

        <div class="nav-actions">
            <button type="button" class="quick-search-trigger" data-modal-open="quick-search-modal" title="Pencarian Cepat">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <span>Cari buku, penulis...</span>
                <span class="search-shortcut-badge">⌘K</span>
            </button>

            <button type="button" class="btn btn-ghost btn-icon" title="Notifikasi" style="border-radius:var(--radius-pill);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                </svg>
            </button>

            <?php if (!empty($isAuthenticated)): ?>
                <div class="dropdown">
                    <div class="dropdown-trigger">
                        <div class="avatar-circle">
                            <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div style="font-size:0.8125rem; font-weight:600; max-width:90px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </div>

                    <div class="dropdown-menu">
                        <div style="padding:0.5rem 0.75rem 0.5rem 0.75rem; border-bottom:1px solid var(--border-subtle); margin-bottom:0.25rem;">
                            <div style="font-size:0.75rem; color:var(--muted-foreground);">Masuk sebagai</div>
                            <div style="font-weight:600; font-size:0.8125rem;"><?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></div>
                        </div>

                        <a href="/member/dashboard" class="dropdown-item">Area Anggota</a>
                        <a href="/member/favorites" class="dropdown-item">Koleksi Favorit</a>
                        <a href="/member/history" class="dropdown-item">Riwayat Membaca</a>
                        <a href="/member/profile" class="dropdown-item">Profil & Pengaturan</a>

                        <?php if (in_array($currentUserRole, ['SUPER_ADMIN', 'LIBRARIAN', 'CATALOGER'], true)): ?>
                            <div class="dropdown-divider"></div>
                            <a href="/admin/dashboard" class="dropdown-item" style="color:var(--primary); font-weight:600;">Panel Petugas</a>
                        <?php endif; ?>

                        <div class="dropdown-divider"></div>
                        <form action="/logout" method="POST" style="margin:0;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="dropdown-item" style="color:var(--destructive); width:100%;">Keluar</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login" class="btn btn-ghost btn-sm">Masuk</a>
                <a href="/register" class="btn btn-primary btn-sm btn-pill">Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</header>
