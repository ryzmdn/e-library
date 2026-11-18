<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <title><?= htmlspecialchars($pageTitle ?? 'Pembaca Buku Digital', ENT_QUOTES, 'UTF-8') ?> &mdash; <?= htmlspecialchars($appName ?? 'E-Library', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
    <style>
        body {
            overflow: hidden;
            height: 100vh;
        }
    </style>
</head>
<body>
    <?= $content ?>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/reader.js"></script>
</body>
</html>
