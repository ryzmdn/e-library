<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <title><?= htmlspecialchars($pageTitle ?? 'Autentikasi Akun', ENT_QUOTES, 'UTF-8') ?> &mdash; <?= htmlspecialchars($appName ?? 'E-Library', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div style="text-align:center; margin-bottom:1.5rem;">
                <a href="/" style="font-size:1.5rem; font-weight:700; letter-spacing:-0.03em;">
                    <?= htmlspecialchars($appName ?? 'E-Library', ENT_QUOTES, 'UTF-8') ?>
                </a>
            </div>
            <?php require dirname(__DIR__) . '/components/alert.php'; ?>
            <?= $content ?>
        </div>
    </div>
    <script src="/assets/js/app.js"></script>
</body>
</html>
