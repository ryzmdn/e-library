<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <title><?= htmlspecialchars($pageTitle ?? 'Sistem Perpustakaan Terpadu', ENT_QUOTES, 'UTF-8') ?> &mdash; <?= htmlspecialchars($appName ?? 'E-Library', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/components/navbar.php'; ?>

    <main class="site-main">
        <div class="container">
            <?php require dirname(__DIR__) . '/components/alert.php'; ?>
            <?= $content ?>
        </div>
    </main>

    <?php require dirname(__DIR__) . '/components/footer.php'; ?>
    <?php require dirname(__DIR__) . '/components/quick-search-modal.php'; ?>

    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/catalog.js"></script>
</body>
</html>
