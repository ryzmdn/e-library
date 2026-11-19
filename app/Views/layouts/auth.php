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
                <a href="/" style="display:inline-flex; align-items:center; gap:0.5rem; text-decoration:none; color:var(--foreground); font-weight:700; font-size:1.35rem; letter-spacing:-0.02em;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary);">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path>
                        <path d="M6 6h10"></path>
                        <path d="M6 10h10"></path>
                    </svg>
                    <span>RuangBaca</span>
                    <span class="brand-badge">E-Perpus</span>
                </a>
            </div>
            <?php require dirname(__DIR__) . '/components/alert.php'; ?>
            <?= $content ?>
        </div>
    </div>
    <script src="/assets/js/app.js"></script>
</body>
</html>
