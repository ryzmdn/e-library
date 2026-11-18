<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Cetak Dokumen', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <style>
        body {
            background-color: #f1f5f9;
            margin: 0;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <?= $content ?>
</body>
</html>
