<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <title><?= htmlspecialchars($pageTitle ?? 'Panel Administrasi', ENT_QUOTES, 'UTF-8') ?> &mdash; <?= htmlspecialchars($appName ?? 'E-Library', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/components/navbar.php'; ?>

    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div style="padding:0.5rem 0.75rem 1rem 0.75rem; border-bottom:1px solid var(--border); margin-bottom:0.5rem;">
                <div style="font-weight:600; font-size:0.875rem;">Menu Staf Perpustakaan</div>
                <div style="font-size:0.75rem; color:var(--muted-foreground);"><?= htmlspecialchars($currentUserName ?? 'Petugas', ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <a href="/admin/dashboard" class="admin-sidebar-link">Ringkasan Sistem</a>
            <a href="/admin/circulation" class="admin-sidebar-link">Meja Sirkulasi</a>
            <a href="/admin/catalog" class="admin-sidebar-link">Katalog Bibliografi</a>
            <a href="/admin/copies" class="admin-sidebar-link">Eksemplar & Barcode</a>
            <a href="/admin/members" class="admin-sidebar-link">Manajemen Anggota</a>
            <a href="/admin/fines" class="admin-sidebar-link">Kasir & Buku Besar Denda</a>
            <a href="/admin/tasks" class="admin-sidebar-link">Tugas Terjadwal (Cron)</a>
        </aside>

        <section class="admin-content">
            <?php require dirname(__DIR__) . '/components/alert.php'; ?>
            <?= $content ?>
        </section>
    </div>

    <?php require dirname(__DIR__) . '/components/quick-search-modal.php'; ?>

    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/circulation.js"></script>
</body>
</html>
