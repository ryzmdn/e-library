<?php
declare(strict_types=1);

$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$baseUrl = $baseUrl ?? '';
?>
<?php if ($totalPages > 1): ?>
<nav class="pagination" aria-label="Navigasi Halaman">
    <?php if ($currentPage > 1): ?>
        <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>?page=<?= $currentPage - 1 ?>" class="btn btn-outline btn-sm">Sebelumnya</a>
    <?php else: ?>
        <button type="button" class="btn btn-outline btn-sm" disabled>Sebelumnya</button>
    <?php endif; ?>

    <span style="font-size:0.875rem; color:var(--muted-foreground); padding:0 0.5rem;">
        Halaman <?= $currentPage ?> dari <?= $totalPages ?>
    </span>

    <?php if ($currentPage < $totalPages): ?>
        <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>?page=<?= $currentPage + 1 ?>" class="btn btn-outline btn-sm">Berikutnya</a>
    <?php else: ?>
        <button type="button" class="btn btn-outline btn-sm" disabled>Berikutnya</button>
    <?php endif; ?>
</nav>
<?php endif; ?>
