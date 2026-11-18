<?php
declare(strict_types=1);
?>
<header class="site-header">
    <div class="container navbar">
        <div style="display:flex; align-items:center; gap:2rem;">
            <a href="/" class="nav-brand">
                <span><?= htmlspecialchars($appName ?? 'E-Library', ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            <nav>
                <ul class="nav-links">
                    <li><a href="/" class="nav-link">Beranda</a></li>
                    <li><a href="/catalog" class="nav-link">Katalog OPAC</a></li>
                    <li><a href="/rooms" class="nav-link">Ruang Baca</a></li>
                </ul>
            </nav>
        </div>

        <div class="nav-actions">
            <div style="display:flex; border:1px solid var(--border); border-radius:var(--radius-md); overflow:hidden;">
                <button type="button" class="btn btn-ghost btn-sm" data-theme-switcher="light" title="Mode Terang">L</button>
                <button type="button" class="btn btn-ghost btn-sm" data-theme-switcher="dark" title="Mode Gelap">D</button>
                <button type="button" class="btn btn-ghost btn-sm" data-theme-switcher="sepia" title="Mode Sepia">S</button>
            </div>

            <?php if (!empty($isAuthenticated)): ?>
                <?php if (in_array($currentUserRole, ['SUPER_ADMIN', 'LIBRARIAN', 'CATALOGER'], true)): ?>
                    <a href="/admin/dashboard" class="btn btn-outline btn-sm">Panel Petugas</a>
                <?php else: ?>
                    <a href="/member/dashboard" class="btn btn-outline btn-sm">Area Anggota</a>
                <?php endif; ?>
                <form action="/logout" method="POST" style="display:inline;">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn-ghost btn-sm">Keluar</button>
                </form>
            <?php else: ?>
                <a href="/login" class="btn btn-ghost btn-sm">Masuk</a>
                <a href="/register" class="btn btn-primary btn-sm">Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</header>
